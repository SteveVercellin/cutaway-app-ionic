<?php
namespace BooklyLocations\Backend\Components\Appearance\ProxyProviders;

use Bookly\Backend\Components\Appearance\Proxy;

/**
 * Class Shared
 * @package BooklyLocations\Backend\Modules\Appearance\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareCodes( array $codes )
    {
        return array_merge( $codes, array(
            array( 'code' => 'location_info', 'description' => __( 'location info', 'bookly' ) ),
            array( 'code' => 'location_name', 'description' => __( 'location name', 'bookly' ) ),
        ) );
    }

}