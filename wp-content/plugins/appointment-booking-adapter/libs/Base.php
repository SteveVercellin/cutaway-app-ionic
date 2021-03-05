<?php

namespace BooklyAdapter;

use \ServicesFileCacheData as ServicesFileCacheData;

class Base
{
    protected $cacheObj = null;

    protected $baseCache = 'bookly';

    protected $entityCache = '';

    protected function __construct()
    {
        if (class_exists('ServicesFileCacheData')) {
            $this->cacheObj = ServicesFileCacheData::getInstance();
        }
    }

    public function checkBooklyActivated()
    {
        return is_plugin_active('bookly-responsive-appointment-booking-tool/main.php');
    }

    public function checkBooklyLocationAddonActivated()
    {
        return is_plugin_active('bookly-addon-locations/main.php');
    }

    public function checkBooklyRatingAddonActivated()
    {
        return is_plugin_active('bookly-addon-ratings/main.php');
    }

    public function checkBooklyRestActivated()
    {
        return is_plugin_active('bookly-rest-api/bookly_rest_api.php');
    }

    protected function getDayIndex($date){
        $index = date('w', strtotime($date));
        return $index + 1;
    }

    protected function writeCache($identity, $content)
    {
        $pathToFile = $this->generatePathToFileCache($identity);

        if (is_null($this->cacheObj)) {
            return false;
        }

        return $this->cacheObj->writeCache($pathToFile, $content);
    }

    protected function readCache($identity, $default = null)
    {
        $pathToFile = $this->generatePathToFileCache($identity);

        if (is_null($this->cacheObj)) {
            return $default;
        }

        return $this->cacheObj->readCache($pathToFile, $default);
    }

    private function generatePathToFileCache($file)
    {
        $pathToFile = array($this->baseCache);
        if (!empty($this->entityCache)) {
            $pathToFile[] = $this->entityCache;
        }
        $pathToFile[] = $file;

        return implode('/', $pathToFile);
    }
}