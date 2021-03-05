<?php
namespace BooklyRatings\Backend\Modules\Appearance\ProxyProviders;

use Bookly\Backend\Modules\Appearance\Proxy;

/**
 * Class Shared
 * @package BooklyRatings\Backend\Modules\Appearance\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function renderServiceStepSettings()
    {
        self::renderTemplate( 'appearance_settings' );
    }

    /**
     * @inheritdoc
     */
    public static function prepareOptions( array $options_to_save, array $options )
    {
        $options_to_save = array_merge( $options_to_save, array_intersect_key( $options, array_flip( array(
            'bookly_ratings_app_show_on_frontend',
        ) ) ) );

        return $options_to_save;
    }
}