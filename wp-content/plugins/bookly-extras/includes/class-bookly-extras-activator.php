<?php

/**
 * Fired during plugin activation
 *
 * @link       https://codeandmore.com
 * @since      1.0.0
 *
 * @package    Bookly_Extras
 * @subpackage Bookly_Extras/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Bookly_Extras
 * @subpackage Bookly_Extras/includes
 * @author     CodeAndMore <vuong.dinhngoc@codeandmore.com>
 */
class Bookly_Extras_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		$table_nam = $wpdb->prefix . 'bookly_favorites';
		$sql = "CREATE TABLE $table_nam (
			id int(10) NOT NULL AUTO_INCREMENT,
			customer_id int(10),
			staff_id int(10),
			PRIMARY KEY  (id)
		);";

		$wpdb->query($sql);
	}

}
