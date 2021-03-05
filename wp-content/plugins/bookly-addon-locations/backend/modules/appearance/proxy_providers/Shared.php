<?php
namespace BooklyLocations\Backend\Modules\Appearance\ProxyProviders;

use BooklyLocations\Lib;
use Bookly\Backend\Modules\Appearance\Proxy;

/**
 * Class Shared
 * @package BooklyLocations\Backend\Modules\Appearance\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function renderServiceStepSettings( $col )
    {
        self::renderTemplate( 'appearance_settings' );
    }

    /**
     * @inheritdoc
     */
    public static function prepareOptions( array $options_to_save, array $options )
    {
        $options_to_save = array_merge( $options_to_save, array_intersect_key( $options, array_flip( array (
            'bookly_app_required_location',
            'bookly_l10n_label_location',
            'bookly_l10n_option_location',
            'bookly_l10n_required_location',
        ) ) ) );

        return $options_to_save;
    }

}