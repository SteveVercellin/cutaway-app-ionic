<?php
namespace BooklyLocations\Backend\Modules\Settings\ProxyProviders;

use Bookly\Backend\Modules\Settings\Proxy;
use Bookly\Backend\Components\Settings\Menu;

/**
 * Class Shared
 * @package BooklyLocations\Backend\Modules\Settings\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function renderMenuItem()
    {
        Menu::renderItem( esc_html__( 'Locations', 'bookly' ), 'locations' );
    }

    /**
     * @inheritdoc
     */
    public static function renderTab()
    {
        self::renderTemplate( 'settings_tab' );
    }

    /**
     * @inheritdoc
     */
    public static function prepareWooCommerceCodes( array $codes )
    {
        $codes[] = array( 'code' => 'location_info', 'description' => __( 'location info', 'bookly' ), );
        $codes[] = array( 'code' => 'location_name', 'description' => __( 'location name', 'bookly' ), );

        return $codes;
    }

    /**
     * @inheritdoc
     */
    public static function saveSettings( array $alert, $tab, array $params )
    {
        if ( $tab == 'locations' ) {
            $options = array( 'bookly_locations_allow_services_per_location' );
            foreach ( $options as $option_name ) {
                if ( array_key_exists( $option_name, $params ) ) {
                    update_option( $option_name, $params[ $option_name ] );
                }
            }
            $alert['success'][] = __( 'Settings saved.', 'bookly' );
        }

        return $alert;
    }

    /**
     * @inheritdoc
     */
    public static function prepareCalendarAppointmentCodes( array $codes, $participants )
    {
        $codes[] = array( 'code' => 'location_info', 'description' => __( 'location info', 'bookly' ), );
        $codes[] = array( 'code' => 'location_name', 'description' => __( 'location name', 'bookly' ), );

        return $codes;
    }
}