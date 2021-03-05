<?php
namespace BooklyPro\Frontend\Modules\CustomerProfile;

use Bookly\Lib as BooklyLib;
use Bookly\Lib\Entities\Stat;
use BooklyPro\Lib;

/**
 * Class ShortCode
 * @package BooklyPro\Frontend\Modules\CustomerProfile
 */
class ShortCode extends BooklyLib\Base\Component
{
    /**
     * Init component.
     */
    public static function init()
    {
        // Register short code.
        add_shortcode( 'bookly-appointments-list', array( __CLASS__, 'render' ) );

        // Assets.
        if ( get_option( 'bookly_gen_link_assets_method' ) == 'enqueue' ) {
            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'linkStyles' ) );
            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'linkScripts' ) );
        } else {
            add_action( 'wp_print_styles', array( __CLASS__, 'linkStyles' ) );
            add_action( 'wp_print_scripts', array( __CLASS__, 'linkScripts' ) );
        }
    }

    /**
     * Link styles.
     */
    public static function linkStyles()
    {
        if (
            get_option( 'bookly_gen_link_assets_method' ) == 'enqueue' ||
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-appointments-list' )
        ) {
            $plugin_ver = Lib\Plugin::getVersion();
            $pro        = plugins_url( '', Lib\Plugin::getMainFile() );

            $bookly_ver = BooklyLib\Plugin::getVersion();
            $bookly     = plugins_url( '', BooklyLib\Plugin::getMainFile() );

            wp_enqueue_style( 'bookly-ladda-min', $bookly . '/frontend/resources/css/ladda.min.css', array(), $bookly_ver );
            wp_enqueue_style( 'bookly-customer-profile', $pro . '/frontend/modules/customer_profile/resources/css/customer_profile.css', array(), $plugin_ver );
        }
    }

    /**
     * Link scripts.
     */
    public static function linkScripts()
    {
        if (
            get_option( 'bookly_gen_link_assets_method' ) == 'enqueue' ||
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-appointments-list' )
        ) {
            $plugin_ver = Lib\Plugin::getVersion();
            $pro        = plugins_url( '', Lib\Plugin::getMainFile() );

            $bookly_ver = BooklyLib\Plugin::getVersion();
            $bookly     = plugins_url( '', BooklyLib\Plugin::getMainFile() );

            wp_enqueue_script( 'bookly-spin', $bookly . '/frontend/resources/js/spin.min.js', array(), $bookly_ver );
            wp_enqueue_script( 'bookly-ladda', $bookly . '/frontend/resources/js/ladda.min.js', array( 'bookly-spin' ), $bookly_ver );
            wp_enqueue_script( 'bookly-customer-profile', $pro . '/frontend/modules/customer_profile/resources/js/customer_profile.js', array( 'jquery' ), $plugin_ver );

            wp_localize_script( 'bookly-customer-profile', 'BooklyCustomerProfileL10n', array(
                'csrf_token' => BooklyLib\Utils\Common::getCsrfToken(),
                'show_more'  => __( 'Show more', 'bookly' ),
            ) );
        }
    }

    /**
     * Render shortcode.
     *
     * @param array $attributes
     * @return string
     */
    public static function render( $attributes )
    {
        global $sitepress;

        // Disable caching.
        BooklyLib\Utils\Common::noCache();

        $customer = new BooklyLib\Entities\Customer();
        $customer->loadBy( array( 'wp_user_id' => get_current_user_id() ) );
        if ( $customer->isLoaded() ) {
            $appointments = Lib\Utils\Common::translateAppointments( $customer->getUpcomingAppointments() );
            $expired      = $customer->getPastAppointments( 1, 1 );
            $more   = ! empty ( $expired['appointments'] );
        } else {
            $appointments = array();
            $more   = false;
        }
        $allow_cancel = current_time( 'timestamp' ) + Lib\Config::getMinimumTimePriorCancel();

        // Prepare URL for AJAX requests.
        $ajax_url = admin_url( 'admin-ajax.php' );

        // Support WPML.
        if ( $sitepress instanceof \SitePress ) {
            $ajax_url = add_query_arg( array( 'lang' => $sitepress->get_current_language() ) , $ajax_url );
        }

        $titles = array();
        if ( @$attributes['show_column_titles'] ) {
            $titles = array(
                'category' => BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_category' ),
                'service'  => BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_service' ),
                'staff'    => BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_employee' ),
                'date'     => __( 'Date',   'bookly' ),
                'time'     => __( 'Time',   'bookly' ),
                'price'    => __( 'Price',  'bookly' ),
                'cancel'   => __( 'Cancel', 'bookly' ),
                'status'   => __( 'Status', 'bookly' ),
            );
            if ( BooklyLib\Config::customFieldsActive() && get_option( 'bookly_custom_fields_enabled' ) ) {
                foreach ( (array) BooklyLib\Proxy\CustomFields::getTranslated() as $field ) {
                    if ( ! in_array( $field->type, array( 'captcha', 'text-content', 'file' ) ) ) {
                        $titles[ $field->id ] = $field->label;
                    }
                }
            }
        }

        $url_cancel = add_query_arg( array( 'action' => 'bookly_cancel_appointment', 'csrf_token' => BooklyLib\Utils\Common::getCsrfToken() ) , $ajax_url );
        if ( is_user_logged_in() ) {
            Stat::record( 'view_customer_profile', 1 );
        }

        return self::renderTemplate( 'short_code', compact( 'ajax_url', 'appointments', 'attributes', 'url_cancel', 'titles', 'more', 'allow_cancel' ), false );
    }
}