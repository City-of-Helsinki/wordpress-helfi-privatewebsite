<?php

/**
  * Plugin Name: Helsinki Private Website
  * Description: Hides the website content behind a login.
  * Version: 1.0.0
  * License: MIT
  * Requires at least: 5.7
  * Requires PHP:      7.1
  * Author: Broomu Digitals
  * Author URI: https://www.broomudigitals.fi
  * Text Domain: helsinki-privatewebsite
  * Domain Path: /languages
  */

namespace CityOfHelsinki\WordPress\PrivateWebsite;

/**
 * Constants
*/
define( __NAMESPACE__ . '\\PLUGIN_VERSION', '1.0.0' );
define( __NAMESPACE__ . '\\PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( __NAMESPACE__ . '\\PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( __NAMESPACE__ . '\\PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
  

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\\plugin_activate' );
function plugin_activate() {
	/**
	 * User Role
	 */
	require_once 'userrole/privateuser.php';
	if (!privatewebsite_check_for_userrole()) {
		privatewebsite_create_userrole();
	}
}

register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\plugin_deactivate' );
function plugin_deactivate() {
	/**
	 * Media Access
	 */
	require_once 'media-access/manage.php';
	privatewebsite_remove_media_restriction_file();
}


add_action( 'plugins_loaded', __NAMESPACE__ . '\\init', 100 );
function init() {


	/**
	  * Plugin parts
	  */
	require_once 'functions.php';
	require_once 'userrole/privateuser.php';
	require_once 'settings/settings.php';
	require_once 'media-access/manage.php';
	require_once 'login/login.php';

	/**
	 * Assets
	 */
	require_once 'class/assets.php';

	//spl_autoload_register( __NAMESPACE__ . '\\autoloader' );

	/**
	  * Actions & filters
	  */
	add_action( 'init', __NAMESPACE__ . '\\textdomain' );

	/**
	  * Plugin ready
	  */
	do_action( 'helsinki_privatewebsite_init' );
}
