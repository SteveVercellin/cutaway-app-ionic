<?php
namespace BooklyPro\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Frontend\Modules\Booking\Proxy;
use Bookly\Lib\Config;
use Bookly\Lib\Entities\Payment;
use BooklyPro\Lib;

/**
 * Class Shared
 * @package BooklyPro\Frontend\Modules\Booking\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function enqueueBookingScripts( array $depends )
    {
        if ( Lib\Config::showFacebookLoginButton() && ! get_current_user_id() ) {
            wp_enqueue_script( 'bookly-facebook-sdk', sprintf( 'https://connect.facebook.net/%s/sdk.js', BooklyLib\Config::getLocale() ) );

            $depends[] = 'bookly-facebook-sdk';
        }

        return $depends;
    }

    /**
     * @inheritdoc
     */
    public static function booklyFormOptions( array $bookly_options )
    {
        $bookly_options['facebook'] = array(
            'enabled' => (int) ( Lib\Config::showFacebookLoginButton() && ! get_current_user_id() ),
            'appId'   => Lib\Config::getFacebookAppId(),
        );

        return $bookly_options;
    }

    /**
     * @inheritdoc
     */
    public static function preparePaymentOptions( $options, $form_id, $show_price, BooklyLib\CartInfo $cart_info, $payment_status )
    {
        if ( Config::paypalEnabled() ) {
            $cart_info->setGateway( Payment::TYPE_PAYPAL );

            $options['paypal'] = array(
                'html' => self::renderTemplate(
                    'paypal_payment_option',
                    compact( 'form_id', 'show_price', 'cart_info', 'payment_status' ),
                    false
                ),
                'pay'  => $cart_info->getPayNow(),
            );
        }

        return $options;
    }

    /**
     * @inheritdoc
     */
    public static function renderPaymentForms( $form_id, $page_url )
    {
        if ( Config::paypalEnabled() ) {
            $type = get_option( 'bookly_paypal_enabled' );
            self::renderTemplate(
                'paypal_payment_form',
                compact( 'type', 'form_id', 'page_url' )
            );
        }
    }
}