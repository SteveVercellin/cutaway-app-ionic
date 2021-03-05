<?php
namespace BooklyPro\Lib\Notifications\Assets\Item\ProxyProviders;

use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Assets\Item\Proxy;

/**
 * Class Shared
 * @package BooklyPro\Lib\Notifications\Assets\Item\ProxyProviders
 */
abstract class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareCodes( Codes $codes )
    {
        $codes->status = $codes->getItem()->getCA()->getStatus();
    }

    /**
     * @inheritdoc
     */
    public static function prepareReplaceCodes( array $replace_codes, Codes $codes, $format )
    {
        $replace_codes['{status}'] = CustomerAppointment::statusToString( $codes->status );

        return $replace_codes;
    }
}