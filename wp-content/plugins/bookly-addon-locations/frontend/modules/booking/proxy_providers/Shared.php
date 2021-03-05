<?php
namespace BooklyLocations\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Lib as BooklyLib;
use BooklyLocations\Lib\Entities;
use Bookly\Frontend\Modules\Booking\Proxy;

/**
 * Class Shared
 * @package BooklyLocations\Frontend\Modules\Booking\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * Add locations list to frontend.
     */
    public static function renderChainItemHead()
    {
        self::renderTemplate( 'locations_step_service' );
    }

    /**
     * @inheritdoc
     */
    public static function prepareChainItemInfoText( $data, BooklyLib\ChainItem $chain_item )
    {
        $location = Entities\Location::find( $chain_item->getLocationId() );
        $data['location_info'][]  = $location ? $location->getTranslatedInfo() : '';
        $data['location_names'][] = $location ? $location->getTranslatedName() : '';

        return $data;
    }

    /**
     * @inheritdoc
     */
    public static function prepareCartItemInfoText( $data, BooklyLib\CartItem $cart_item )
    {
        $location = Entities\Location::find( $cart_item->getLocationId() );
        $data['location_info'][]  = $location ? $location->getTranslatedInfo() : '';
        $data['location_names'][] = $location ? $location->getTranslatedName() : '';

        return $data;
    }

    /**
     * @inheritdoc
     */
    public static function prepareInfoTextCodes( array $info_text_codes, array $data )
    {
        $info_text_codes['{location_info}'] = '<b>' . implode( ', ', $data['location_info'] ) . '</b>';
        $info_text_codes['{location_name}'] = '<b>' . implode( ', ', $data['location_names'] ) . '</b>';

        return $info_text_codes;
    }
}