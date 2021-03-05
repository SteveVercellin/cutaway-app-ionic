<?php
namespace BooklyPro\Frontend\Modules\CustomerProfile;

use Bookly\Lib as BooklyLib;
use BooklyPro\Lib;

/**
 * Class Ajax
 * @package BooklyPro\Frontend\Modules\CustomerProfile
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * @inheritdoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'user' );
    }

    /**
     * Get past appointments.
     */
    public static function getPastAppointments()
    {
        $customer = new BooklyLib\Entities\Customer();
        $customer->loadBy( array( 'wp_user_id' => get_current_user_id() ) );
        $past = $customer->getPastAppointments( self::parameter( 'page' ), 30 );
        $appointments  = Lib\Utils\Common::translateAppointments( $past['appointments'] );
        $custom_fields = self::parameter( 'custom_fields' ) ? explode( ',', self::parameter( 'custom_fields' ) ) : array();
        $allow_cancel  = current_time( 'timestamp' ) + Lib\Config::getMinimumTimePriorCancel();
        $columns       = (array) self::parameter( 'columns' );
        $with_cancel   = in_array( 'cancel', $columns );
        $html = self::renderTemplate( '_rows', compact( 'appointments', 'columns', 'allow_cancel', 'custom_fields', 'with_cancel' ), false );
        wp_send_json_success( array( 'html' => $html, 'more' => $past['more'] ) );
    }
}