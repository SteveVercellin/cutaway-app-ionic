<?php

namespace BooklyPro\Backend\Components\Dialogs\Staff\Edit\ProxyProviders;

use Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;
use Bookly\Lib as BooklyLib;

/**
 * Class Local
 * @package BooklyPro\Backend\Components\Dialogs\Staff\Edit\ProxyProviders
 */
class Local extends Proxy\Pro
{
    /**
     * @inheritdoc
     */
    public static function renderArchivingComponents()
    {
        self::enqueueScripts( array(
            'module'   => array( 'js/archive.js' => array( 'jquery' ), ),
            'frontend' => array(
                'js/spin.min.js'  => array( 'jquery' ),
                'js/ladda.min.js' => array( 'jquery' ),
                'js/datatables.min.js' => array( 'jquery' ),
            ),
            'bookly'   => array(
                'backend/resources/js/alert.js' => array( 'bookly-archive.js' ),
            )
        ) );

        wp_localize_script( 'bookly-archive.js', 'BooklyL10nStaffArchive', array(
            'csrfToken'  => BooklyLib\Utils\Common::getCsrfToken(),
            'areYouSure' => __( 'Are you sure?', 'bookly' ),
            'saved'      => __( 'Settings saved.', 'bookly' ),
        ) );

        self::renderTemplate( 'archive_dialog' );
    }

}