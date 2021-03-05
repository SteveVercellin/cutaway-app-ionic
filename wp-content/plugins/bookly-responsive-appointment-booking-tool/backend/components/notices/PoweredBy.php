<?php
namespace Bookly\Backend\Components\Notices;

use Bookly\Lib;

/**
 * Class PoweredBy
 * @package Bookly\Backend\Components\Notices
 */
class PoweredBy extends Lib\Base\Component
{
    /**
     * Render show Powered by Bookly notice.
     */
    public static function render()
    {
        if ( Lib\Utils\Common::isCurrentUserAdmin()
            && ! get_option( 'bookly_app_show_powered_by' )
            && ! get_user_meta( get_current_user_id(), 'bookly_dismiss_powered_by_notice' )
        ) {
            self::enqueueStyles( array(
                'frontend' => array( 'css/ladda.min.css', ),
            ) );
            self::enqueueScripts( array(
                'frontend' => array(
                    'js/spin.min.js'  => array( 'jquery' ),
                    'js/ladda.min.js' => array( 'jquery' ),
                ),
                'module'   => array( 'js/powered-by.js' => array( 'bookly-ladda.min.js', ), ),
            ) );

            self::renderTemplate( 'powered_by' );
        }
    }
}