<?php
namespace BooklyLocations\Lib;

use Bookly\Lib as BooklyLib;

/**
 * Class Installer
 * @package BooklyLocations\Lib
 */
class Installer extends Base\Installer
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->options = array(
            'bookly_app_required_location'                 => '0',
            'bookly_locations_allow_services_per_location' => '0',
            'bookly_l10n_label_location'                   => __( 'Location', 'bookly' ),
            'bookly_l10n_option_location'                  => __( 'Select location', 'bookly' ),
            'bookly_l10n_required_location'                => __( 'Please select a location', 'bookly' ),
        );
    }

    /**
     * Create tables in database.
     */
    public function createTables()
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        $charset_collate = $wpdb->has_cap( 'collation' )
            ? $wpdb->get_charset_collate()
            : 'DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci';

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\Location::getTableName() . '` (
                `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name`        VARCHAR(255) DEFAULT "",
                `info`        TEXT,
				`lat`         VARCHAR(255) DEFAULT "",
                `lng`         VARCHAR(255) DEFAULT "",
                `position`    INT NOT NULL DEFAULT 9999
                ) ENGINE = INNODB
                ' . $charset_collate
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\StaffLocation::getTableName() . '` (
                `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `staff_id`    INT UNSIGNED NOT NULL,
                `location_id` INT UNSIGNED NOT NULL,
                `custom_services` TINYINT(1) NOT NULL DEFAULT 0,
                `custom_schedule` TINYINT(1) NOT NULL DEFAULT 0,
                UNIQUE KEY unique_ids_idx (staff_id, location_id),
                CONSTRAINT
                    FOREIGN KEY (staff_id)
                    REFERENCES ' . BooklyLib\Entities\Staff::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (location_id)
                    REFERENCES ' . Entities\Location::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE = INNODB
            ' . $charset_collate
        );

        $wpdb->query(
            'ALTER TABLE `' . BooklyLib\Entities\Appointment::getTableName() . '` 
             ADD CONSTRAINT 
                FOREIGN KEY (location_id)
                REFERENCES ' . Entities\Location::getTableName() . '(id)
                ON DELETE SET NULL
                ON UPDATE CASCADE'
        );

        $wpdb->query(
            'ALTER TABLE `' . BooklyLib\Entities\StaffService::getTableName() . '` 
             ADD CONSTRAINT 
                FOREIGN KEY (location_id)
                REFERENCES ' . Entities\Location::getTableName() . '(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE'
        );

        $wpdb->query(
            'ALTER TABLE `' . \Bookly\Lib\Entities\StaffScheduleItem::getTableName() . '` 
             ADD CONSTRAINT 
                FOREIGN KEY (location_id)
                REFERENCES ' . Entities\Location::getTableName() . '(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE'
        );
    }
}