<?php

namespace Includes;

use \Includes\Classes\BaseRestPayments;

class LoadRestPayments {
    private static $instance = null;
    private $restPayments;

    private function __construct()
    {
        $this->_initRestPayments();
        $this->_loadRestPayments();
    }

    private function _initRestPayments()
    {
        $this->restPayments = array(
            'stripe' => array(
                'enable' => true,
                'class' => '\\Includes\\Classes\\StripePayment'
            ),
            'paypal' => array(
                'enable' => true,
                'class' => '\\Includes\\Classes\\PaypalPayment'
            )
        );
    }

    private function _loadRestPayments()
    {
        new BaseRestPayments();
        foreach ($this->restPayments as $method) {
            if ($method['enable'] && class_exists($method['class'])) {
                new $method['class']();
            }
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new LoadRestPayments();
        }

        return self::$instance;
    }
}