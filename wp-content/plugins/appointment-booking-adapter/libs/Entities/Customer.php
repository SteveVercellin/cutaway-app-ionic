<?php

namespace BooklyAdapter\Entities;

use \BooklyAdapter\Base;

class Customer extends Base
{
    private static $_instance = null;

    private function __construct()
    {

    }

    public function getCustomerIdFromUserLogged()
    {
        $currentUser = wp_get_current_user();
        if (empty($currentUser->ID)) {
            return 0;
        }

        return $this->getCustomerIdFromUser($currentUser->ID);
    }

    public function getCustomerIdFromUser($userId)
    {
        if (empty($userId)) {
            return 0;
        }

        $customer = $this->loadCustomerBy(array(
            'wp_user_id' => $userId
        ));
        $customerId = !empty($customer) ? $customer->getId() : 0;

        return $customerId;
    }

    public function updateCustomer($data)
    {
        if (!$this->checkBooklyActivated()) {
            return false;
        }

        $booklyCustomer = new \Bookly\Lib\Entities\Customer();
        $booklyCustomer->setFields($data);

        return $booklyCustomer->save();
    }

    public function loadCustomerBy($conditions)
    {
        if (!$this->checkBooklyActivated()) {
            return false;
        }

        $booklyCustomer = new \Bookly\Lib\Entities\Customer();
        $loadBooklyCustomer = $booklyCustomer->loadBy($conditions);
        if ($loadBooklyCustomer) {
            return $booklyCustomer;
        }

        return false;
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new Customer();
        }

        return self::$_instance;
    }
}