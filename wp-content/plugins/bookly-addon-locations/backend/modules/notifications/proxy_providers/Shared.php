<?php
namespace BooklyLocations\Backend\Modules\Notifications\ProxyProviders;

use Bookly\Backend\Modules\Notifications\Proxy;

/**
 * Class Shared
 * @package BooklyLocations\Backend\Modules\Notifications\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareNotificationCodes( array $codes, $type )
    {
        $codes['appointment']['location_info'] = __( 'location info', 'bookly' );
        $codes['appointment']['location_name'] = __( 'location name', 'bookly' );

        $codes['location']['location_info'] = __( 'location info', 'bookly' );
        $codes['location']['location_name'] = __( 'location name', 'bookly' );

        return $codes;
    }
}