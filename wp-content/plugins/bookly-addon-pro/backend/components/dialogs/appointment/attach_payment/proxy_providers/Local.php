<?php
namespace BooklyPro\Backend\Components\Dialogs\Appointment\AttachPayment\ProxyProviders;

use Bookly\Backend\Components\Dialogs\Appointment\AttachPayment\Proxy;

/**
 * Class Local
 * @package BooklyPro\Backend\Components\Dialogs\Appointment\AttachPayment\ProxyProviders
 */
class Local extends Proxy\Pro
{
    /**
     * @inheritdoc
     */
    public static function renderAttachPaymentDialog()
    {
        self::renderTemplate( 'attach_payment' );
    }

}