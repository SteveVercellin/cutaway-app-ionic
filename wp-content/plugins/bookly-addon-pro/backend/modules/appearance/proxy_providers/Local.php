<?php
namespace BooklyPro\Backend\Modules\Appearance\ProxyProviders;

use Bookly\Backend\Modules\Appearance\Proxy;

/**
 * Class Local
 * @package BooklyPro\Backend\Modules\Appointments\ProxyProviders
 */
class Local extends Proxy\Pro
{
    /**
     * @inheritdoc
     */
    public static function renderBookingStatesSelector()
    {
        self::renderTemplate( 'booking_states_selector' );
    }

    /**
     * @inheritdoc
     */
    public static function renderBookingStatesText()
    {
        self::renderTemplate( 'booking_states_text' );
    }

    /**
     * @inheritdoc
     */
    public static function renderPayPalPaymentOption()
    {
        self::renderTemplate( 'paypal_payment_option' );
    }

    /**
     * @inheritdoc
     */
    public static function renderShowAddress()
    {
        self::renderTemplate( 'show_address' );
    }

    /**
     * @inheritdoc
     */
    public static function renderShowBirthday()
    {
        self::renderTemplate( 'show_birthday' );
    }

    /**
     * @inheritdoc
     */
    public static function renderTimeZoneSwitcher()
    {
        $current_offset = get_option( 'gmt_offset' );
        $tz_string      = get_option( 'timezone_string' );
        if ( $tz_string == '' ) { // Create a UTC+- zone if no timezone string exists
            if ( $current_offset == 0 ) {
                $tz_string = 'UTC+0';
            } else if ( $current_offset < 0 ) {
                $tz_string = 'UTC' . $current_offset;
            } else {
                $tz_string = 'UTC+' . $current_offset;
            }
        }

        self::renderTemplate( 'time_zone_switcher', compact( 'tz_string' ) );
    }

    /**
     * @inheritdoc
     */
    public static function renderTimeZoneSwitcherCheckbox()
    {
        self::renderTemplate( 'time_zone_switcher_checkbox' );
    }

    /**
     * @inheritdoc
     */
    public static function renderFacebookButton()
    {
        self::renderTemplate( 'fb_button' );
    }

    /**
     * @inheritdoc
     */
    public static function renderShowFacebookButton()
    {
        self::renderTemplate( 'show_fb_button_checkbox' );
    }

}