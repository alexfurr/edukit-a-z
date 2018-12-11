<?php
$ek_az = new ek_az();

class ek_az{

	
	//~~~~~
	function __construct ()
	{
		$this->addWPActions();
	}
	
/*	---------------------------
	PRIMARY HOOKS INTO WP 
	--------------------------- */	
	function addWPActions ()
	{
		
		// Register Shortcode
		add_shortcode( 'ek-a-z', array( 'ek_az_draw', 'drawAZ' ) );
		
		//Add Front End Jquery and CSS
		add_action( 'wp_footer', array( $this, 'frontendEnqueues' ) );		
		
		//Admin Menu
		add_action( 'init',  array( $this, 'create_CPTs' ) );		
		add_action( 'add_meta_boxes_az_link', array( $this, 'addAZ_LinkMetaBox' ));		
		add_action( 'add_meta_boxes_page', array( $this, 'addPageAZ_metabox' ));		


		
		// Save additional  meta for the custom post
		add_action( 'save_post', array($this, 'savePostMeta' ), 10 );		
		add_action( 'save_post', array($this, 'savePageMeta' ), 10 );
		
	}
	
	
	function frontendEnqueues ()
	{
		//Scripts
		wp_enqueue_script('jquery');

		// Custom Styles		
		wp_enqueue_style( 'ek-az-styles', EK_AZ_PLUGIN_URL . '/css/styles.css' );

	}	
	
	function create_CPTs ()
	{
	
		//Placements
		$labels = array(
			'name'               =>  'A-Z URLs',
			'singular_name'      =>  'A-Z URL',
			'menu_name'          =>  'A-Z URLs',
			'name_admin_bar'     =>  'Placements',
			'add_new'            =>  'Add New URL',
			'add_new_item'       =>  'Add New URL',
			'new_item'           =>  'New URL',
			'edit_item'          =>  'Edit URL',
			'view_item'          => 'View A-Z URLs',
			'all_items'          => 'All A-Z URLs',
			'search_items'       => 'Search URLs',
			'parent_item_colon'  => '',
			'not_found'          => 'No URL found.',
			'not_found_in_trash' => 'No URL found in Trash.'
		);
	
		$args = array(
			'menu_icon' => 'dashicons-admin-links',		
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_nav_menus'	 => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => false,
			'capability_type'    => 'page',
			'has_archive'        => true,
			'hierarchical'       => true,
			'menu_position'      => 65 ,
			'supports'           => array( 'title' )
			
		);
		
		register_post_type( 'az_link', $args );	
	}
	
	
	function  addAZ_LinkMetaBox()
	{
			
		//Project Settings Metabox
		$id 			= 'az_meta';
		$title 			= 'URL';
		$drawCallback 	= array( $this, 'drawAZLinkBox' );
		$screen 		= 'az_link';
		$context 		= 'normal';
		$priority 		= 'default';
		$callbackArgs 	= array();
		
		add_meta_box( 
			$id, 
			$title, 
			$drawCallback, 
			$screen, 
			$context,
			$priority, 
			$callbackArgs 
		);
		
		
	}
	
	function  addPageAZ_metabox()
	{
			
		//Main pages metabox
		$id 			= 'az_page_meta';
		$title 			= 'A-Z include';
		$drawCallback 	= array( $this, 'drawPageAZ_options' );
		$screen 		= 'page';
		$context 		= 'side';
		$priority 		= 'low';
		$callbackArgs 	= array();
		
		add_meta_box( 
			$id, 
			$title, 
			$drawCallback, 
			$screen, 
			$context,
			$priority, 
			$callbackArgs 
		);
		
		
	}	
	
	function drawPageAZ_options($post, $metabox)
	{
		
		$postID = $post->ID;
		$include_az = get_post_meta($postID, "include_az", true);
		
		
		wp_nonce_field( 'save_ek_az_metabox_nonce', 'ek_az_metabox_nonce' );		
		
		echo '<label for="include_az"><input type="checkbox" name="include_az" id="include_az" ';
		if($include_az=="on")
		{
			echo ' checked ';
		}
		
		echo '/> Include this page in the A-Z</label>';
	}
	
	
	
	
	
	function drawAZLinkBox($post, $metabox)
	{
		$azURL = get_post_meta($post->ID, 'azURL', true);
		
		// Add Nonce Field
		wp_nonce_field( 'save_ek_az_metabox_nonce', 'ek_az_metabox_nonce' );
		
		echo  '<label for="azURL">Link Address</label><br/>';
		echo '<input name="azURL" id="azURL" style="width:400px;" value="'.$azURL.'">';
		
		echo '<hr/>';
		if($azURL)
		{
			if (filter_var($azURL, FILTER_VALIDATE_URL) === FALSE) {
				echo '<span style="color:red">This is not a valid URL</span>';
			}
		}		
		
	}		
	
	
	// Save metabox data
	function savePostMeta ( $postID )
	{
		global $post_type;
		
		
		
		if($post_type=="az_link")
		{
			// Check if nonce is set.
			if ( ! isset( $_POST['ek_az_metabox_nonce'] ) ) {
				return;
			}
			
			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $_POST['ek_az_metabox_nonce'], 'save_ek_az_metabox_nonce' ) ) {
				return;
			}
			
			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
		
			// Check the user's permissions.
			if ( ! current_user_can( 'edit_post', $postID ) ) {
				return;
			}
			
			// check if there was a multisite switch before
			if ( is_multisite() && ms_is_switched() ) {
				return $postID;
			}	
			
			$azURL = $_POST['azURL'];

			update_post_meta( $postID, 'azURL', $azURL );
			

		}	

	
	}	

	// Save metabox data
	function savePageMeta ( $postID )
	{
		global $post_type;
		
		if($post_type=="page")
		{
			// Check if nonce is set.
			if ( ! isset( $_POST['ek_az_metabox_nonce'] ) ) {
				return;
			}	
			
			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $_POST['ek_az_metabox_nonce'], 'save_ek_az_metabox_nonce' ) ) {
				return;
			}
			
			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
		
			// Check the user's permissions.
			if ( ! current_user_can( 'edit_post', $postID ) ) {
				return;
			}
			
			// check if there was a multisite switch before
			if ( is_multisite() && ms_is_switched() ) {
				return $postID;
			}	
			
			$include_az = $_POST['include_az'];
			update_post_meta( $postID, 'include_az', $include_az );
			

		}		
	
	}		
	
	
	
	
	
	
	
}

?>