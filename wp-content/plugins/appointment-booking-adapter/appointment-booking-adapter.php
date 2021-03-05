<?php

/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 *
 * @wordpress-plugin
 * Plugin Name:       Bookly Adapter
 * Description:       Bookly Adapter
 * Version:           1.0.0
 * Author:            congnguyentan (CAM)
 * Text Domain:       bookly-adapter
 */

require_once __DIR__ . '/libs/configuration/constants.php';

require_once __DIR__ . '/vendor/autoload.php';