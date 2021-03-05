<?php
namespace BooklyLocations\Lib\Notifications\Assets\Item\ProxyProviders;

use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Assets\Item\Proxy;
use BooklyLocations\Lib;

/**
 * Class Shared
 * @package BooklyLocations\Lib\Notifications\Assets\Item\ProxyProviders
 */
abstract class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareCodes( Codes $codes )
    {
        $location = Lib\Entities\Location::find( $codes->getItem()->getAppointment()->getLocationId() );
        $codes->location_info = $location ? $location->getTranslatedInfo() : '';
        $codes->location_name = $location ? $location->getTranslatedName() : '';
    }

    /**
     * @inheritdoc
     */
    public static function prepareReplaceCodes( array $replace_codes, Codes $codes, $format )
    {
        $replace_codes['{location_name}'] = $codes->location_name;
        $replace_codes['{location_info}'] = $codes->location_info;

        return $replace_codes;
    }
}