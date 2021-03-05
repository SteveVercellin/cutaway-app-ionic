<?php
namespace BooklyLocations\Backend\Modules\Appearance\ProxyProviders;

use BooklyLocations\Lib;
use Bookly\Backend\Modules\Appearance\Proxy;

/**
 * Class Local
 * @package BooklyLocations\Backend\Modules\Appearance\ProxyProviders
 */
class Local extends Proxy\Locations
{
    /**
     * @inheritdoc
     */
    public static function renderLocation()
    {
        self::renderTemplate( 'location' );
    }
}