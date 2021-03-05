<?php
namespace BooklyRatings\Backend\Modules\Settings\ProxyProviders;

use Bookly\Backend\Modules\Settings\Proxy;
use Bookly\Backend\Components\Settings\Menu;
use Bookly\Backend\Components\Settings\Inputs;

/**
 * Class Shared
 * @package BooklyRatings\Backend\Modules\Settings\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function renderMenuItem()
    {
        Menu::renderItem( esc_html__( 'Ratings', 'bookly' ), 'ratings' );
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
    public static function renderUrlSettings()
    {
        Inputs::renderText( 'bookly_ratings_page_url', __( 'Rating page URL', 'bookly' ), __( 'Set the URL of a page with a rating and comment form.', 'bookly' ) );
    }

    /**
     * @inheritdoc
     */
    public static function saveSettings( array $alert, $tab, array $params )
    {
        if ( $tab == 'ratings' ) {
            $options = array(
                'bookly_ratings_timeout',
                'bookly_ratings_period',
                'bookly_ratings_show_at_backend',
            );
            foreach ( $options as $option_name ) {
                if ( array_key_exists( $option_name, $params ) ) {
                    update_option( $option_name, $params[ $option_name ] );
                }
            }
            $alert['success'][] = __( 'Settings saved.', 'bookly' );
        } else if ( $tab == 'url' ) {
            update_option( 'bookly_ratings_page_url', $params['bookly_ratings_page_url'] );
        }

        return $alert;
    }
}