<?php
namespace BooklyPro\Backend\Modules\Calendar\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Backend\Modules\Calendar\Proxy;
use BooklyPro\Lib;

/**
 * Class Shared
 * @package BooklyPro\Backend\Modules\Calendar\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareAppointmentCodesData( array $codes, $appointment_data, $participants )
    {
        if ( $participants == 'one' ) {
            $codes['{client_address}'] = Lib\Utils\Common::getFullAddressByCustomerData( $appointment_data );
        }

        return $codes;
    }
}