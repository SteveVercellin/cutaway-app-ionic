<?php
namespace BooklyPro\Lib\Notifications\Cart;

use Bookly\Lib\DataHolders\Booking\Order;
use Bookly\Lib\Notifications\Assets\Item\Attachments;
use Bookly\Lib\Notifications\Base;
use BooklyPro\Lib\Notifications\Assets\Combined\Codes;

/**
 * Class Sender
 * @package BooklyPro\Lib\Notifications\Cart
 */
abstract class Sender extends Base\Sender
{
    /**
     * Send combined notifications to client.
     *
     * @param Order $order
     * @param array|bool $queue
     */
    public static function sendCombined( Order $order, &$queue = false )
    {
        $item = current( $order->getItems() );

        $justCreated = $item->getCA()->isJustCreated()
            // Maybe this is IPN request and combined notification should be sent for pending payment appointments created
            ?: strtotime( $item->getCA()->getCreated() ) - current_time( 'timestamp' ) >= - 120; // 2 minutes

        if ( $justCreated ) {
            $codes         = new Codes( $order );
            $notifications = static::getNotifications( 'new_booking_combined' );

            $attachments = new Attachments( $codes );
            // Notify client.
            foreach ( $notifications['client'] as $notification ) {
                static::sendToClient( $order->getCustomer(), $notification, $codes, $attachments, $queue );
            }
            $attachments->clear();
        }
    }
}