<?php
namespace BooklyPro\Backend\Components\Dialogs\Payment;

use Bookly\Lib as BooklyLib;
use Bookly\Lib\Entities\Payment;

/**
 * Class Ajax
 * @package BooklyPro\Backend\Modules\Payments
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * @inheritdoc
     */
    protected static function permissions()
    {
        return array(
            'addPaymentAdjustment' => 'user',
        );
    }

    /**
     * Adjust payment.
     */
    public static function addPaymentAdjustment()
    {
        $payment_id = self::parameter( 'payment_id' );
        $reason     = self::parameter( 'reason' );
        $tax        = self::parameter( 'tax', 0 );
        $amount     = self::parameter( 'amount' );

        $payment = new Payment();
        $payment->load( $payment_id );

        if ( $payment && is_numeric( $amount ) ) {
            $details = json_decode( $payment->getDetails(), true );

            $details['adjustments'][] = compact( 'reason', 'amount', 'tax' );
            $payment
                ->setDetails( json_encode( $details ) )
                ->setTotal( $payment->getTotal() + $amount )
                ->setTax( $payment->getTax() + $tax )
                ->save();
        }

        wp_send_json_success();
    }

    /**
     * Extend parent method to control access on staff member level.
     *
     * @param string $action
     * @return bool
     */
    protected static function hasAccess( $action )
    {
        if ( parent::hasAccess( $action ) ) {
            if ( ! BooklyLib\Utils\Common::isCurrentUserAdmin() ) {
                $staff = new BooklyLib\Entities\Staff();

                return $staff->loadBy( array( 'wp_user_id' => get_current_user_id() ) );
            }

            return true;
        }

        return false;
    }
}