<?php
namespace BooklyPro\Backend\Modules\Settings\ProxyProviders;

use Bookly\Backend\Components\Settings\Menu;
use Bookly\Backend\Modules\Settings\Proxy;
use Bookly\Lib as BooklyLib;
use BooklyPro\Lib;

/**
 * Class Shared
 * @package BooklyPro\Backend\Modules\Settings\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareCalendarAppointmentCodes( array $codes, $participants )
    {
        if ( $participants == 'one' ) {
            $codes[] = array( 'code' => 'client_address', 'description' => __( 'address of client', 'bookly' ) );
        }

        return $codes;
    }

    /**
     * @inheritdoc
     */
    public static function preparePaymentGatewaySettings( $payment_data )
    {
        $payment_data['paypal'] = self::renderTemplate( 'paypal_settings', array(), false );

        return $payment_data;
    }

    /**
     * @inheritdoc
     */
    public static function renderMenuItem()
    {
        Menu::renderItem( 'WooCommerce', 'woo_commerce' );
        Menu::renderItem( 'Facebook', 'facebook' );
        if ( BooklyLib\Config::multipleServicesBookingEnabled() ) {
            Menu::renderItem( esc_html__( 'Cart', 'bookly' ), 'cart' );
        }
    }

    /**
     * @inheritdoc
     */
    public static function renderTab()
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        $goods    = array( array( 'id' => 0, 'name' => __( 'Select product', 'bookly' ) ) );
        $query    = 'SELECT ID, post_title FROM ' . $wpdb->posts . ' WHERE post_type = \'product\' AND post_status = \'publish\' ORDER BY post_title';
        $products = $wpdb->get_results( $query );

        foreach ( $products as $product ) {
            $goods[] = array( 'id' => $product->ID, 'name' => $product->post_title );
        }
        self::renderTemplate( 'wc_tab', compact( 'goods' ) );

        self::renderTemplate( 'fb_tab' );

        if ( BooklyLib\Config::multipleServicesBookingEnabled() ) {
            $cart_columns = array(
                'service'  => BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_service' ),
                'date'     => __( 'Date', 'bookly' ),
                'time'     => __( 'Time', 'bookly' ),
                'employee' => BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_employee' ),
                'price'    => __( 'Price', 'bookly' ),
                'deposit'  => __( 'Deposit', 'bookly' ),
                'tax'      => __( 'Tax', 'bookly' ),
            );
            self::renderTemplate( 'cart_tab', compact( 'cart_columns' ) );
        }
    }

    /**
     * @inheritdoc
     */
    public static function saveSettings( array $alert, $tab, array $params )
    {
        $options = array();
        switch ( $tab ) {
            case 'customers':
                $options = array(
                    'bookly_cst_address_show_fields',
                    'bookly_cst_cancel_action',
                    'bookly_cst_create_account',
                    'bookly_cst_new_account_role',
                    'bookly_cst_required_address',
                    'bookly_cst_required_birthday',
                );
                break;
            case 'facebook':
                $options = array( 'bookly_fb_app_id' );
                if ( $params['bookly_fb_app_id'] == '' ) {
                    update_option( 'bookly_app_show_facebook_login_button', 0 );
                }
                $alert['success'][] = __( 'Settings saved.', 'bookly' );
                break;
            case 'general':
                $options = array( 'bookly_gen_min_time_prior_booking', 'bookly_gen_min_time_prior_cancel' );
                break;
            case 'google_calendar':
                $alert = Proxy\AdvancedGoogleCalendar::preSaveSettings( $alert, $params );
                $options = array( 'bookly_gc_client_id', 'bookly_gc_client_secret', 'bookly_gc_sync_mode', 'bookly_gc_limit_events', 'bookly_gc_event_title', 'bookly_gc_event_client_info', 'bookly_gc_event_appointment_info' );
                $alert['success'][] = __( 'Settings saved.', 'bookly' );
                break;
            case 'payments':
                $options = array(
                    'bookly_paypal_enabled',
                    'bookly_paypal_api_username',
                    'bookly_paypal_api_password',
                    'bookly_paypal_api_signature',
                    'bookly_paypal_sandbox',
                    'bookly_paypal_increase',
                    'bookly_paypal_addition',
                    'bookly_paypal_send_tax',
                );
                break;
            case 'purchase_code':
                $grace_expired = Lib\Config::graceExpired();
                $errors = apply_filters( 'bookly_save_purchase_codes', array(), $params['purchase_code'], null );
                if ( empty ( $errors ) ) {
                    $alert['success'][] = __( 'Settings saved.', 'bookly' );
                    if ( $grace_expired && ! Lib\Config::graceExpired( false ) ) {
                        BooklyLib\Proxy\AdvancedGoogleCalendar::reSync();
                    }
                } else {
                    $alert['error'] = array_merge( $alert['error'], $errors );
                }
                break;
            case 'url':
                $options = array( 'bookly_url_final_step_url', 'bookly_url_cancel_confirm_page_url' );
                break;
            case 'woo_commerce':
                foreach ( array( 'bookly_l10n_wc_cart_info_name', 'bookly_l10n_wc_cart_info_value' ) as $option_name ) {
                    update_option( $option_name, $params[ $option_name ] );
                    do_action( 'wpml_register_single_string', 'bookly', $option_name, $params[ $option_name ] );
                }
                $options = array( 'bookly_wc_enabled', 'bookly_wc_product' );
                $alert['success'][] = __( 'Settings saved.', 'bookly' );
                break;
            case 'cart':
                $options = array( 'bookly_cart_show_columns' );
                $alert['success'][] = __( 'Settings saved.', 'bookly' );
                if ( get_option( 'bookly_wc_enabled' ) && $params['bookly_cart_enabled'] ) {
                    $alert['error'][] = sprintf(
                        __( 'To use the cart, disable integration with WooCommerce <a href="%s">here</a>.', 'bookly' ),
                        BooklyLib\Utils\Common::escAdminUrl( \Bookly\Backend\Modules\Settings\Page::pageSlug(), array( 'tab' => 'woo_commerce' ) )
                    );
                }
                break;
        }

        // Update options.
        foreach ( $options as $option_name ) {
            if ( array_key_exists( $option_name, $params ) ) {
                $value = $params[ $option_name ];
                update_option( $option_name, is_array( $value ) ? $value : trim( $value ) );
            }
        }

        return $alert;
    }
}