<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://codeandmore.com
 * @since             1.0.0
 * @package           Bookly_Extras
 *
 * @wordpress-plugin
 * Plugin Name:       Bookly Extras
 * Plugin URI:        https://codeandmore.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            CodeAndMore
 * Author URI:        https://codeandmore.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bookly-extras
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

require_once __DIR__ . '/bootstrap.php';
require_once plugin_dir_path(__FILE__) . 'lib/post-types.php';
require_once plugin_dir_path(__FILE__) . 'lib/functions.php';
require_once(plugin_dir_path(__FILE__) . 'lib/api.php');

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PLUGIN_NAME_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bookly-extras-activator.php
 */
function activate_bookly_extras() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-bookly-extras-activator.php';
	Bookly_Extras_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bookly-extras-deactivator.php
 */
function deactivate_bookly_extras() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-bookly-extras-deactivator.php';
	Bookly_Extras_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_bookly_extras');
register_deactivation_hook(__FILE__, 'deactivate_bookly_extras');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-bookly-extras.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bookly_extras() {

	$plugin = new Bookly_Extras();
	$plugin->run();

}

run_bookly_extras();
