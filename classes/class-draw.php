<?php


$ek_az_draw = new ek_az_draw();

class ek_az_draw {


	static function drawAZ($atts)
	{
			
		$atts = shortcode_atts( 
			array(
				'parent'		=> '0',
				), 
			$atts
		);		
		
		$parentID = (int) $atts['parent'];

		$args = array(
			'hierarchical' => 0,
			'parent' => $parentID,
			'post_type' => 'page',
			'post_status' => 'publish'
		); 
		$pages = get_pages($args);
		
		// Create holding array for each of the letters
		$alphas = range('A', 'Z');
		$alphabetArray = array_fill_keys($alphas, array() );
		
		foreach ($pages as $pageInfo)
		{
			
			$pageID = $pageInfo-> ID;		
			$pageName = $pageInfo->post_title;
			$pageURL = get_page_link($pageID);
			
			$startLetter = strtoupper($pageName[0]);			
			
			$alphabetArray[$startLetter][$pageID] = array
			(
				"pageName"	=> $pageName,
				"pageURL"	=> $pageURL,
			);
			
			//echo $pageID.'<br/>';
			//echo $pageName.'<br/>';
		}
		
		// Also go through the custom AZ Links and get them added
		$args = array(
			
			
			'post_type' => 'az_link',
			'post_status' => 'publish'
		); 
		$az_links = get_pages($args); 	
		
		foreach ($az_links as $linkInfo)
		{
			
			$postID = $linkInfo-> ID;
			$linkName = $linkInfo->post_title;
			$azURL = get_post_meta($postID, 'azURL', true);
			$startLetter = strtoupper($linkName[0]);			
			
			$alphabetArray[$startLetter][$postID] = array
			(
				"pageName"	=> $linkName,
				"pageURL"	=> $azURL,
			);
		}
		
		// Also go through the custom AZ Links and get them added
		$args = array(
			'post_type' => 'page',
			'post_status' => 'publish',		
			'meta_key'     => 'include_az',
			'meta_value'   => 'on',
			'hierarchical' => 0,
			
		); 
		$additionalAZpages = get_pages($args); 	
		
		foreach ($additionalAZpages as $pageInfo)
		{
			
			$pageID = $pageInfo-> ID;		
			$pageName = $pageInfo->post_title;
			$pageURL = get_page_link($pageID);
			
			$startLetter = strtoupper($pageName[0]);			
			
			$alphabetArray[$startLetter][$pageID] = array
			(
				"pageName"	=> $pageName,
				"pageURL"	=> $pageURL,
			);
			

		}	
		

		$str='';
		$str.= '<div class="ek-az-jumpbox-wrap">';

		// Create the top quick links
		foreach($alphabetArray as $thisLetter => $letterPages)
		{
			
			$pageCount = count($letterPages);
			
			if($pageCount>=1)
			{
				$str.= '<a href="#letter-'.$thisLetter.'" class="';
				
				
				if($pageCount>=1)
				{
					$str.= 'az-active';
				}	
				else
				{
					$str.= 'az-inactive';
				}					
				
				
				$str.='">';
			}
			
			$str.= '<div>';

			
			$str.= $thisLetter;

			
			$str.= '</div>';
			if($pageCount>=1)
			{
				$str.= '</a>';
			}			
			
		}
		
		$str.= '</div>';
	
	
		// Create the Main list
		foreach($alphabetArray as $thisLetter => $letterPages)
		{				
				$pageCount = count($letterPages);
			
				if($pageCount>=1 )
				{
					$str.= '<div class="az-letter-section" id="letter-'.$thisLetter.'">';
					$str.= '<h1>'.$thisLetter.'</h1>';
					$str.= '<div class="az-pages-section">';
					$str.= '<ul>';
					foreach($letterPages as $pageID => $pageInfo)
					{
						$pageName = $pageInfo['pageName'];
						$pageURL = $pageInfo['pageURL'];
						
						$str.= '<li><a href="'.$pageURL.'">'.$pageName.'</a></li>';
					}
					$str.= '</ul>';
					$str.= '</div>';
					$str.= '</div>';
					
				}
			
		}
		
	

		
		return $str;

		
	}
	
}
?>