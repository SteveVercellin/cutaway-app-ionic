<?php
namespace BooklyPro\Lib\Notifications\NewWpUser;

use Bookly\Lib\Entities\Customer;
use Bookly\Lib\Notifications\Base;
use BooklyPro\Lib\Notifications\Assets\NewWpUser\Codes;

/**
 * Class NewUser
 * @package BooklyPro\Lib\Notifications\NewWpUser
 */
abstract class Sender extends Base\Sender
{
    /**
     * Send email/sms with username and password for newly created WP user.
     *
     * @param Customer $customer
     * @param string $username
     * @param string $password
     */
    public static function send( Customer $customer, $username, $password )
    {
        $codes = new Codes( $customer, $username, $password );
        $notifications = static::getNotifications( 'customer_new_wp_user' );

        // Notify client.
        foreach ( $notifications['client'] as $notification ) {
            static::sendToClient( $customer, $notification, $codes );
        }
    }
}