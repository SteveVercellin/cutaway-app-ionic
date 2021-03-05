<?php

namespace Includes;

use \Includes\Classes\WordpressBaseRestFunctions;

class WordpressLoadRestFunctions {
    private static $instance = null;
    private $restFunctions;

    private function __construct()
    {
        $this->_initRestFunctions();
        $this->_loadRestFunctions();
    }

    private function _initRestFunctions()
    {
        $this->restFunctions = array(
            'register' => array(
                'enable' => true,
                'class' => '\\Includes\\Functions\\WordpressRegisterUserFunction'
            ),
            'reset_pass' => array(
                'enable' => true,
                'class' => '\\Includes\\Functions\\WordpressResetPassUserFunction'
            ),
            'user_information' => array(
                'enable' => true,
                'class' => '\\Includes\\Functions\\WordpressInformationUserFunction'
            )
        );
    }

    private function _loadRestFunctions()
    {
        new WordpressBaseRestFunctions();
        foreach ($this->restFunctions as $function) {
            if ($function['enable'] && class_exists($function['class'])) {
                $obj = new $function['class']();
                $obj->registerRestApis();
            }
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new WordpressLoadRestFunctions();
        }

        return self::$instance;
    }
}