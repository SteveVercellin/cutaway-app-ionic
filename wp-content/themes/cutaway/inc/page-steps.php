<?php

class PageSteps
{
    protected $currentPage = '';

    public function getNextPageLink($args = array())
    {
        $nextPage = 0;

        if (!empty($this->configSteps)) {
            foreach ($this->configSteps as $key => $page) {
                if ($this->currentPage == $page && !empty($this->configSteps[$key + 1])) {
                    $nextPage = $this->configSteps[$key + 1];
                    break;
                }
            }
        }

        $link = '';
        if (!empty($nextPage)) {
            $args = $this->makeSureIsArray($args);

            $link = add_query_arg($args, get_permalink($nextPage));
        }

        return $link;
    }

    public function getPreviousPageFromSession()
    {
        $session = !empty($_SESSION[$this->sessionName]) ? $_SESSION[$this->sessionName] : array();
        if (empty($session)) {
            return array();
        }

        $prevPage = $this->getPreviousPage();
        if (empty($prevPage)) {
            return array();
        }

        return !empty($session[$prevPage]) ? $session[$prevPage] : array();
    }

    public function getCurrentPageFromSession()
    {
        $session = !empty($_SESSION[$this->sessionName]) ? $_SESSION[$this->sessionName] : array();
        if (empty($session)) {
            return array();
        }

        return !empty($session[$this->currentPage]) ? $session[$this->currentPage] : array();
    }

    public function getFirstPageLink()
    {
        $firstPage = 0;

        if (!empty($this->configSteps)) {
            $firstPage = $this->configSteps[0];
        }

        $link = '';
        if (!empty($firstPage)) {
            $link = get_permalink($firstPage);
        }

        return $link;
    }

    public function checkRequestToPageIsValid($page)
    {
        if (empty($this->configSteps) || $page == $this->configSteps[0]) {
            return true;
        }

        if (!$this->checkPageInListSteps($page)) {
            return true;
        }

        $referrer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if (empty($referrer)) {
            return false;
        }

        $referrer = $this->removeProtocolFromLink($referrer);
        $prevPage = 0;
        $nextPage = 0;

        foreach ($this->configSteps as $key => $page) {
            if ($this->currentPage == $page) {
                if (!empty($this->configSteps[$key - 1])) {
                    $prevPage = $this->configSteps[$key - 1];
                }
                if (!empty($this->configSteps[$key + 1])) {
                    $nextPage = $this->configSteps[$key + 1];
                }

                break;
            }
        }

        if (empty($prevPage) && empty($nextPage)) {
            return false;
        }

        $prevPage = get_permalink($prevPage);
        $prevPage = $this->removeProtocolFromLink($prevPage);
        $nextPage = get_permalink($nextPage);
        $nextPage = $this->removeProtocolFromLink($nextPage);

        return strpos($referrer, $prevPage) === 0 || strpos($referrer, $nextPage) === 0;
    }

    public function setCurrentPage($page)
    {
        $this->currentPage = $page;
    }

    public function setPageDataSession($page, $data)
    {
        $data = $this->makeSureIsArray($data);
        $session = !empty($_SESSION[$this->sessionName]) ? $_SESSION[$this->sessionName] : array();
        $session[$page] = $data;
        $_SESSION[$this->sessionName] = $session;
    }

    public function removePageDataSession()
    {
        unset($_SESSION[$this->sessionName]);
    }

    protected function makeSureIsArray($data)
    {
        $data = !empty($data) ? $data : array();
        $data = !is_array($data) ? (array) $data : $data;

        return $data;
    }

    protected function removeProtocolFromLink($link)
    {
        return str_replace(array('http://', 'https://'), '', $link);
    }

    protected function getPreviousPage()
    {
        $prevPage = 0;

        if (!empty($this->configSteps)) {
            foreach ($this->configSteps as $key => $page) {
                if ($this->currentPage == $page && !empty($this->configSteps[$key - 1])) {
                    $prevPage = $this->configSteps[$key - 1];
                    break;
                }
            }
        }

        return $prevPage;
    }

    protected function checkPageInListSteps($pageCheck)
    {
        if (!empty($this->configSteps)) {
            foreach ($this->configSteps as $key => $page) {
                if ($pageCheck == $page) {
                    return true;
                }
            }
        }

        return false;
    }
}

class LoginSteps extends PageSteps
{
    protected $configSteps = array();

    protected $sessionName = 'login_steps';

    private static $_instance = null;

    private function __construct()
    {
        $steps = cutawayGetThemeOption('login_steps');
        if (!empty($steps)) {
            $steps = array_map(function ($item) {
                return $item['step_page'];
            }, $steps);

            $this->configSteps = $steps;
        }
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new LoginSteps();
        }

        return self::$_instance;
    }
}

class ChangeAccountInformationSteps extends PageSteps
{
    protected $configSteps = array();

    protected $sessionName = 'change_account_information_steps';

    private static $_instance = null;

    private function __construct()
    {
        $steps = cutawayGetThemeOption('change_account_information_steps');
        if (!empty($steps)) {
            $steps = array_map(function ($item) {
                return $item['step_page'];
            }, $steps);

            $this->configSteps = $steps;
        }
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new ChangeAccountInformationSteps();
        }

        return self::$_instance;
    }
}

class ViewOrderSteps extends PageSteps
{
    protected $configSteps = array();

    protected $sessionName = 'view_order_steps';

    private static $_instance = null;

    private function __construct()
    {
        $steps = cutawayGetThemeOption('view_order_steps');
        if (!empty($steps)) {
            $steps = array_map(function ($item) {
                return $item['step_page'];
            }, $steps);

            $this->configSteps = $steps;
        }
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new ViewOrderSteps();
        }

        return self::$_instance;
    }
}

class BookingSteps extends PageSteps
{
    protected $configSteps = array();

    protected $sessionName = 'booking_steps';

    private static $_instance = null;

    private function __construct()
    {
        $steps = cutawayGetThemeOption('booking_steps');
        if (!empty($steps)) {
            $steps = array_map(function ($item) {
                return $item['step_page'];
            }, $steps);

            $this->configSteps = $steps;
        }
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new BookingSteps();
        }

        return self::$_instance;
    }
}