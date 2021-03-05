<?php

namespace BooklyAdapter\Classes;

class AppointmentGroupMeta
{
    private $table = 'bookly_appointment_group_meta';

    private $appGroupId;

    public function __construct($appGroupId = '')
    {
        global $wpdb;

        $this->table = $wpdb->prefix . $this->table;
        $this->appGroupId = $appGroupId;

        $this->initTable();
    }

    public function getAppointmentGroupMeta($metaKey)
    {
        global $wpdb;

        if (!$this->checkCanProcess($metaKey)) {
            return false;
        }

        $wpTable = $this->table;

        $sql = $wpdb->prepare("SELECT meta_value FROM $wpTable WHERE appointment_group_id = %d AND meta_key LIKE %s", $this->appGroupId, $metaKey);
        $metaValue = $wpdb->get_var($sql);

        if (!empty($metaValue)) {
            return maybe_unserialize($metaValue);
        }

        return $metaValue;
    }

    public function updateAppointmentGroupMeta($metaKey, $metaValue)
    {
        global $wpdb;

        if (!$this->checkCanProcess($metaKey)) {
            return false;
        }

        $wpTable = $this->table;

        $appMetaId = $this->getAppointmentGroupMetaId($metaKey);
        $metaValue = maybe_serialize($metaValue);

        if (!empty($appMetaId)) {
            $result = $wpdb->update(
                $wpTable,
                array(
                    'meta_value' => $metaValue
                ),
                array(
                    'id' => $appMetaId
                ),
                array('%s'),
                array('%d')
            );
        } else {
            $result = $wpdb->insert(
                $wpTable,
                array(
                    'appointment_group_id' => $this->appGroupId,
                    'meta_key' => $metaKey,
                    'meta_value' => $metaValue
                ),
                array('%d', '%s', '%s')
            );
        }

        return $result !== false;
    }

    public function deleteAppointmentGroupMeta($metaKey)
    {
        global $wpdb;

        if (!$this->checkCanProcess($metaKey)) {
            return false;
        }

        $wpTable = $this->table;

        $result = $wpdb->delete(
            $wpTable,
            array(
                'appointment_group_id' => $this->appGroupId,
                'meta_key' => $metaKey
            ),
            array('%d', '%s')
        );

        return $result !== false;
    }

    private function getAppointmentGroupMetaId($metaKey)
    {
        global $wpdb;

        if (!$this->checkCanProcess($metaKey)) {
            return false;
        }

        $wpTable = $this->table;

        $sql = $wpdb->prepare("SELECT id FROM $wpTable WHERE appointment_group_id = %d AND meta_key LIKE %s", $this->appGroupId, $metaKey);
        $id = $wpdb->get_var($sql);

        return !empty($id) ? $id : 0;
    }

    private function checkCanProcess($metaKey)
    {
        return !empty($this->appGroupId) && !empty($metaKey);
    }

    private function initTable()
    {
        global $wpdb;

        $wpTable = $this->table;

        $sqlCheckTableExists = $wpdb->prepare('SHOW TABLES LIKE %s', $wpTable);
        $tableExists = $wpdb->get_var($sqlCheckTableExists);

        if (empty($tableExists)) {
            $sql = "CREATE TABLE IF NOT EXISTS $wpTable(
                `id` INT NOT NULL AUTO_INCREMENT,
                `appointment_group_id` INT(11) NOT NULL,
                `meta_key` VARCHAR(255) NOT NULL,
                `meta_value` TEXT NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE (`appointment_group_id`, `meta_key`),
                INDEX (`appointment_group_id`),
                INDEX (`meta_key`)
            ) ENGINE = InnoDB";

            $wpdb->query($sql);
        }
    }
}