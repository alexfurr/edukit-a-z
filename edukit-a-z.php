<?php
/*
Plugin Name: Edukit A - Z
Description: Simple A - Z
Version: 0.1.1
Author: Alex Furr
GitHub Plugin URI: https://github.com/ImperialCollegeLondon/edukit-a-z
*/

$edukit_az = dirname(__FILE__);


define( 'EK_AZ_PLUGIN_URL', plugins_url('edukit-a-z' , dirname( __FILE__ )) );
define( 'EK_AZ_PATH', plugin_dir_path(__FILE__) );



include_once( $edukit_az . '/functions.php');
include_once( $edukit_az . '/classes/class-draw.php');


?>
