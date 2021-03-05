<?php

namespace BooklyAdapter\Classes;

use \BooklyAdapter\Entities\Staff;

class StaffMeta
{
    private $table = 'bookly_staff_meta';

    private $staffId;

    public function __construct($staffId = '')
    {
        global $wpdb;

        if (empty($staffId)) {
            $booklyStaffAdapter = Staff::getInstance();
            $staffId = $booklyStaffAdapter->getStaffIdFromUserLogged();
        }

        $this->table = $wpdb->prefix . $this->table;
        $this->staffId = $staffId;

        $this->initTable();
    }

    public function getStaffMeta($metaKey)
    {
        global $wpdb;

        if (!$this->checkCanProcess($metaKey)) {
            return false;
        }

        $wpTable = $this->table;

        $sql = $wpdb->prepare("SELECT meta_value FROM $wpTable WHERE staff_id = %d AND meta_key LIKE %s", $this->staffId, $metaKey);
        $metaValue = $wpdb->get_var($sql);

        if (!empty($metaValue)) {
            return maybe_unserialize($metaValue);
        }

        return $metaValue;
    }

    public function updateStaffMeta($metaKey, $metaValue)
    {
        global $wpdb;

        if (!$this->checkCanProcess($metaKey)) {
            return false;
        }

        $wpTable = $this->table;

        $staffMetaId = $this->getStaffMetaId($metaKey);
        $metaValue = maybe_serialize($metaValue);

        if (!empty($staffMetaId)) {
            $result = $wpdb->update(
                $wpTable,
                array(
                    'meta_value' => $metaValue
                ),
                array(
                    'id' => $staffMetaId
                ),
                array('%s'),
                array('%d')
            );
        } else {
            $result = $wpdb->insert(
                $wpTable,
                array(
                    'staff_id' => $this->staffId,
                    'meta_key' => $metaKey,
                    'meta_value' => $metaValue
                ),
                array('%d', '%s', '%s')
            );
        }

        return $result !== false;
    }

    public function deleteStaffMeta($metaKey)
    {
        global $wpdb;

        if (!$this->checkCanProcess($metaKey)) {
            return false;
        }

        $wpTable = $this->table;

        $result = $wpdb->delete(
            $wpTable,
            array(
                'staff_id' => $this->staffId,
                'meta_key' => $metaKey
            ),
            array('%d', '%s')
        );

        return $result !== false;
    }

    private function getStaffMetaId($metaKey)
    {
        global $wpdb;

        if (!$this->checkCanProcess($metaKey)) {
            return false;
        }

        $wpTable = $this->table;

        $sql = $wpdb->prepare("SELECT id FROM $wpTable WHERE staff_id = %d AND meta_key LIKE %s", $this->staffId, $metaKey);
        $id = $wpdb->get_var($sql);

        return !empty($id) ? $id : 0;
    }

    private function checkCanProcess($metaKey)
    {
        return !empty($this->staffId) && !empty($metaKey);
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
                `staff_id` INT(10) NOT NULL,
                `meta_key` VARCHAR(255) NOT NULL,
                `meta_value` TEXT NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE (`staff_id`, `meta_key`),
                INDEX (`staff_id`),
                INDEX (`meta_key`)
            ) ENGINE = InnoDB";

            $wpdb->query($sql);
        }
    }
}