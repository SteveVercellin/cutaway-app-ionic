<?php

namespace BooklyPro\Backend\Components\Dialogs\Staff\Categories\ProxyProviders;

use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Dialogs\Staff\Categories\Proxy;
use Bookly\Lib as BooklyLib;

/**
 * Class Local
 * @package BooklyPro\Backend\Components\Dialogs\Appointment\AttachPayment\ProxyProviders
 */
class Local extends Proxy\Pro
{
    /**
     * @inheritdoc
     */
    public static function renderDialog()
    {
        self::enqueueStyles( array(
            'frontend' => array( 'css/ladda.min.css', ),
            'backend'  => array( 'css/fontawesome-all.min.css', 'css/select2.min.css' ),
        ) );

        self::enqueueScripts( array(
            'frontend' => array(
                'js/spin.min.js'  => array( 'jquery', ),
                'js/ladda.min.js' => array( 'jquery', ),
            ),
            'backend'  => array(
                'js/select2.full.min.js' => array( 'jquery' ),
            ),
            'module'   => array( 'js/staff-categories-dialog.js' => array( 'jquery', ) ),
        ) );

        wp_localize_script( 'bookly-staff-categories-dialog.js', 'BooklyStaffCategoriesL10n', array(
            'csrfToken' => BooklyLib\Utils\Common::getCsrfToken(),
        ) );

        self::renderTemplate( 'dialog' );
    }

    /**
     * @inheritdoc
     */
    public static function renderAdd()
    {
        Buttons::renderCustom( 'bookly-js-categories', 'btn-default', esc_html__( 'Categories...', 'bookly' ), array( 'data-toggle' => 'modal', 'data-target' => '#bookly-staff-categories-modal' ) );
    }
}