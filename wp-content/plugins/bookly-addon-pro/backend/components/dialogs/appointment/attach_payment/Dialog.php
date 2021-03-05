<?php
namespace BooklyPro\Backend\Components\Dialogs\Appointment\AttachPayment;

use Bookly\Lib as BooklyLib;

/**
 * Class Dialog
 * @package BooklyPro\Backend\Components\Dialogs\Appointment\AttachPayment
 */
class Dialog extends BooklyLib\Base\Component
{
    public static function render()
    {
        static::renderTemplate( 'attach_payment' );
    }
}