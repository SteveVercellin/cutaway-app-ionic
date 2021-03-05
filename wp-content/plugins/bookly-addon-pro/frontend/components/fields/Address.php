<?php
namespace BooklyPro\Frontend\Components\Fields;

use Bookly\Lib as BooklyLib;
use BooklyPro\Lib;

/**
 * Class Address
 * @package BooklyPro\Frontend\Components\Fields
 */
class Address extends BooklyLib\Base\Component
{
    /**
     * Render inputs for address fields on the frontend.
     *
     * @param BooklyLib\UserBookingData $user_data
     */
    public static function render( BooklyLib\UserBookingData $user_data )
    {
        foreach ( Lib\Utils\Common::getDisplayedAddressFields() as $field_name => $field ) {
            $field_value = $user_data->getAddressField( $field_name );
            self::renderTemplate( 'address',
                compact( 'field_name', 'field_value' )
            );
        }
    }
}