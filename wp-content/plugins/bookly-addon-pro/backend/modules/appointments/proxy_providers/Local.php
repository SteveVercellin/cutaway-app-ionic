<?php
namespace BooklyPro\Backend\Modules\Appointments\ProxyProviders;

use Bookly\Backend\Modules\Appointments\Proxy;

/**
 * Class Local
 * @package BooklyPro\Backend\Modules\Appointments\ProxyProviders
 */
class Local extends Proxy\Pro
{
    /**
     * @inheritdoc
     */
    public static function renderExportButton()
    {
        self::renderTemplate( 'export_button' );
    }

    /**
     * @inheritdoc
     */
    public static function renderExportDialog( $custom_fields )
    {
        self::renderTemplate( 'export_dialog', compact( 'custom_fields' ) );
    }

    /**
     * @inheritdoc
     */
    public static function renderPrintButton()
    {
        self::renderTemplate( 'print_button' );
    }

    /**
     * @inheritdoc
     */
    public static function renderPrintDialog( $custom_fields )
    {
        self::renderTemplate( 'print_dialog', compact( 'custom_fields' ) );
    }
}