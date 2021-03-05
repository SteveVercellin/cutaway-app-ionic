<?php

namespace BooklyAdapter\Classes;

class AppointmentGroupDetail
{
    private $table = 'bookly_appointment_group_detail';

    private $appGroupId;

    public function __construct($appGroupId = '')
    {
        global $wpdb;

        $this->table = $wpdb->prefix . $this->table;
        $this->appGroupId = $appGroupId;

        $this->initTable();
    }

    public function insertAppointmentGroupDetail( $appIds )
    {
        global $wpdb;

        if ( !$this->checkCanProcess( $appIds ) ) {
            return false;
        }

        $wpTable = $this->table;

        foreach ( $appIds as $appId ) {
            $result = $wpdb->insert(
                $wpTable,
                array(
                    'appointment_group_id' => $this->appGroupId,
                    'appointment_id' => $appId,
                ),
                array( '%d', '%d' )
            );

            if ( $result === false ) {
                return false;
            }
        }

        return true;
    }

    public function setAppGroupId( $appGroupId )
    {
        $this->appGroupId = $appGroupId;
    }

    public function getTableName()
    {
        return $this->table;
    }

    private function checkCanProcess( $appIds )
    {
        return !empty( $this->appGroupId ) && !empty( $appIds );
    }

    private function initTable()
    {
        global $wpdb;

        $wpTable = $this->table;

        $sqlCheckTableExists = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpTable );
        $tableExists = $wpdb->get_var( $sqlCheckTableExists );

        if ( empty( $tableExists ) ) {
            $sql = "CREATE TABLE IF NOT EXISTS $wpTable(
                `id` INT NOT NULL AUTO_INCREMENT,
                `appointment_group_id` INT(11) NOT NULL,
                `appointment_id` INT(10) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE (`appointment_group_id`, `appointment_id`),
                INDEX (`appointment_group_id`),
                UNIQUE (`appointment_id`)
            ) ENGINE = InnoDB";

            $wpdb->query( $sql );
        }
    }
}