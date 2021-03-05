<?php
/*
Plugin Name: Bookly Rest Api
Plugin URI: http://codeandmore.com
Description: Rest api for bookly plugin
Author: Code And More
Version: 1.0
Author URI: http://codeandmore.com
*/
require 'utils.php';
require 'includes/libs.php';
require 'modules/BaseRestModule.php';
require 'modules/Location.php';
require 'modules/Shop.php';
require 'modules/Service.php';
require 'modules/Staff.php';

function plugin_init() {
 	$plugin_dir = basename(dirname(__FILE__));
 	load_plugin_textdomain( 'cutaway', false, $plugin_dir );
}
add_action('plugins_loaded', 'plugin_init');

