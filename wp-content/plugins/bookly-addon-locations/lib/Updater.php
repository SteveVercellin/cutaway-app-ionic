<?php
namespace BooklyLocations\Lib;

/**
 * Class Updates
 * @package BooklyLocations\Lib
 */
class Updater extends \Bookly\Lib\Base\Updater
{
    public function update_2_5()
    {
        $this->upgradeCharsetCollate( array(
            'bookly_locations',
            'bookly_staff_locations',
        ) );
    }

    function update_2_4()
    {
        delete_option( 'bookly_locations_enabled' );
    }

    function update_2_2()
    {
        global $wpdb;

        $this->alterTables( array(
            'bookly_staff_locations' => array(
                'ALTER TABLE `%s` ADD COLUMN `custom_schedule` TINYINT(1) NOT NULL DEFAULT 0',
            ),
        ) );

        $wpdb->query(
            'ALTER TABLE `' . $this->getTableName( 'bookly_staff_schedule_items' ) . '` 
             ADD CONSTRAINT 
                FOREIGN KEY (location_id)
                REFERENCES ' . $this->getTableName( 'bookly_locations' ) . '(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE'
        );
    }

    function update_2_1()
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        // Rename tables.
        $tables = array(
            'locations',
            'staff_locations',
        );
        $query = 'RENAME TABLE ';
        foreach ( $tables as $table ) {
            $query .= sprintf( '`%s` TO `%s`, ', $this->getTableName( 'ab_' . $table ), $this->getTableName( 'bookly_' . $table ) );
        }
        $query = substr( $query, 0, -2 );
        $wpdb->query( $query );
    }

    function update_1_13()
    {
        global $wpdb;

        $this->alterTables( array(
            'ab_staff_locations' => array(
                'ALTER TABLE `%s` ADD COLUMN `custom_services` TINYINT(1) NOT NULL DEFAULT 0',
            ),
        ) );

        $wpdb->query(
            'ALTER TABLE `' . $this->getTableName( 'ab_staff_services' ) . '` 
             ADD CONSTRAINT 
                FOREIGN KEY (location_id)
                REFERENCES ' . $this->getTableName( 'ab_locations' ) . '(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE'
        );

        add_option( 'bookly_locations_allow_services_per_location', '0' );
    }

    function update_1_6()
    {
        add_option( 'bookly_locations_enabled', '1' );
    }

    function update_1_5()
    {
        $options = array(
            'ab_appearance_required_location'      => 'bookly_app_required_location',
            'ab_appearance_text_label_location'    => 'bookly_l10n_label_location',
            'ab_appearance_text_option_location'   => 'bookly_l10n_option_location',
            'ab_appearance_text_required_location' => 'bookly_l10n_required_location',
        );
        $this->renameL10nStrings( $options );
    }

    function update_1_3()
    {
        add_option( 'bookly_app_required_location', 0 );
        $this->addL10nOptions( array( 'bookly_l10n_required_location' => __( 'Please select a location', 'bookly' ) ) );
    }

    function update_1_1()
    {
        /** @var \wpdb $wpdb */
        global $wpdb;

        $this->alterTables( array(
            'ab_locations' => array(
                'ALTER TABLE `%s` CHANGE COLUMN `title` `name` VARCHAR(255) DEFAULT ""',
                'ALTER TABLE `%s` ADD COLUMN `info` TEXT AFTER `name`',
            ),
        ) );

        $locations_table = $this->getTableName( 'ab_locations' );
        $rows = (array) $wpdb->get_results( sprintf( 'SELECT id FROM `%s` ORDER BY `position`', $locations_table ), ARRAY_A );
        $position = 1;
        foreach ( $rows as $row ) {
            $wpdb->update( $locations_table, array( 'position' => $position ++ ), array( 'id' => $row['id'] ) );
        }
        $wpdb->query( 'ALTER TABLE `' . $this->getTableName( 'ab_customer_appointments' ) . '`
              ADD CONSTRAINT
                FOREIGN KEY (`location_id`)
                REFERENCES `' . $locations_table . '`(`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE' );
    }

}