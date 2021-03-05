<?php
namespace BooklyRatings\Frontend\Modules\StaffRating;

use Bookly\Lib as BooklyLib;
use BooklyRatings\Lib;

/**
 * Class ShortCode
 * @package BooklyRatings\Frontend\Modules\StaffRating
 */
class ShortCode extends BooklyLib\Base\Component
{
    /**
     * Init component.
     */
    public static function init()
    {
        // Register short code.
        add_shortcode( 'bookly-staff-rating', array( __CLASS__, 'render' ) );

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
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-rating' )
        ) {
            $bookly_ver = BooklyLib\Plugin::getVersion();
            $bookly_url = plugins_url( '', BooklyLib\Plugin::getMainFile() );
            $ratings    = plugins_url( '', Lib\Plugin::getMainFile() );

            wp_enqueue_style( 'bookly-ladda-min', $bookly_url . '/frontend/resources/css/ladda.min.css', array(), $bookly_ver );
            wp_enqueue_style( 'bookly-bootstrap-theme', $bookly_url . '/backend/resources/bootstrap/css/bootstrap-theme.min.css', array( 'dashicons' ), $bookly_ver );
            wp_enqueue_style( 'bookly-barrating', $bookly_url . '/backend/components/notices/resources/css/bootstrap-stars.css', array( 'bookly-bootstrap-theme' ), $bookly_ver );
            wp_enqueue_style( 'bookly-staff-rating', $ratings . '/frontend/modules/staff_rating/resources/css/staff_rating.css', array( 'bookly-barrating' ), Lib\Plugin::getVersion() );
        }
    }

    /**
     * Link scripts.
     */
    public static function linkScripts()
    {
        if (
            get_option( 'bookly_gen_link_assets_method' ) == 'enqueue' ||
            BooklyLib\Utils\Common::postsHaveShortCode( 'bookly-staff-rating' )
        ) {
            $bookly_ver = BooklyLib\Plugin::getVersion();
            $bookly_url = plugins_url( '', BooklyLib\Plugin::getMainFile() );

            wp_enqueue_script( 'bookly-barrating', $bookly_url . '/backend/components/notices/resources/js/jquery.barrating.min.js', array( 'jquery' ), $bookly_ver );

            wp_localize_script( 'bookly-barrating', 'BooklyStaffRatingL10n', array(
                'csrf_token' => BooklyLib\Utils\Common::getCsrfToken(),
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
        // Disable caching.
        BooklyLib\Utils\Common::noCache();

        $token   = self::parameter( 'bookly-rating-token' );
        $ca      = BooklyLib\Entities\CustomerAppointment::query( 'ca' )
            ->select( 'ca.rating, ca.rating_comment, a.end_date, a.start_date, a.service_id, a.staff_id' )
            ->leftJoin( 'Appointment', 'a', 'a.id = ca.appointment_id' )
            ->where( 'ca.token', $token )
            ->fetchRow();
        $expired = $ca ? strtotime( $ca['end_date'] ) < current_time( 'timestamp' ) - (int) get_option( 'bookly_ratings_timeout' ) * DAY_IN_SECONDS : false;
        $not_started = $ca ? strtotime( $ca['start_date'] ) > current_time( 'timestamp' ) : false;
        if ( $ca && ! $expired && ! $not_started) {
            $service = new BooklyLib\Entities\Service();
            $service->load( $ca['service_id'] );
            $ca['service_title'] = $service->getTranslatedTitle();

            $staff = new BooklyLib\Entities\Staff();
            $staff->load( $ca['staff_id'] );
            $ca['staff_name'] = $staff->getTranslatedName();

            $ca['date'] = BooklyLib\Utils\DateTime::formatDate( $ca['start_date'] );
            $ca['time'] = BooklyLib\Utils\DateTime::formatTime( $ca['start_date'] );
        }

        // Prepare URL for AJAX requests.
        $ajax_url = admin_url( 'admin-ajax.php' );

        $form_id = uniqid();

        return self::renderTemplate( 'short_code', compact( 'ajax_url', 'form_id', 'token', 'ca', 'expired', 'not_started', 'attributes' ), false );
    }
}