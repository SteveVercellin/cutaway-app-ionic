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
 * Plugin Name:       Helper functions for WP-REST-API
 * Description:       Helper functions for WP-REST-API
 * Version:           1.0.0
 * Author:            congnguyentan (CAM)
 * Text Domain:       wordpress-rest-helpers
 */

function wordpress_rest_format_response_success( $data = array() )
{
    return rest_ensure_response( array(
        'status' => 'ok',
        'data' => $data,
    ) );
}

function wordpress_rest_format_response_fail( $error = '' )
{
    return rest_ensure_response( array(
        'status' => 'fail',
        'error' => !empty( $error ) ? $error : 'unknow',
    ) );
}