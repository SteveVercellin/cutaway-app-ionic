<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://codeandmore.com
 * @since      1.0.0
 *
 * @package    Bookly_Extras
 * @subpackage Bookly_Extras/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Bookly_Extras
 * @subpackage Bookly_Extras/includes
 * @author     CodeAndMore <vuong.dinhngoc@codeandmore.com>
 */
class Bookly_Extras_i18n
{


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'bookly-extras',
			false,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);

	}


}
