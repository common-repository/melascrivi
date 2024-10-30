<?php

/*
 * @package melascrivi_plugin
 * @version 1.5
 *
Plugin Name: melascrivi-plugin
Plugin URI: http://app.melascrivi.com
Description: A brief description of the Plugin.
Version: 1.5
Author: AdIntend
Author URI: http://app.melascrivi.com
License: GPL2
*/
if ( is_admin() ){

	$path=dirname( __FILE__ ) ;
	require_once $path. '/admin.php';
}
?>