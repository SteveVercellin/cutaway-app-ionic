<?php
namespace BooklyPro\Backend\Modules\Services\ProxyProviders;

use Bookly\Backend\Modules\Services\Proxy;
use BooklyPro\Lib;

/**
 * Class Shared
 * @package BooklyPro\Backend\Modules\Services\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareUpdateService( array $data )
    {
        // Saving staff preferences for service, when the form is submitted
        /** @var Lib\Entities\StaffPreferenceOrder[] $staff_preferences */
        $staff_preferences = Lib\Entities\StaffPreferenceOrder::query()
            ->where( 'service_id', $data['id'] )
            ->indexBy( 'staff_id' )
            ->find();
        if ( array_key_exists( 'positions', $data ) ) {
            foreach ( (array) $data['positions'] as $position => $staff_id ) {
                if ( array_key_exists( $staff_id, $staff_preferences ) ) {
                    $staff_preferences[ $staff_id ]->setPosition( $position )->save();
                } else {
                    $preference = new Lib\Entities\StaffPreferenceOrder();
                    $preference
                        ->setServiceId( $data['id'] )
                        ->setStaffId( $staff_id )
                        ->setPosition( $position )
                        ->save();
                }
            }
        }

        // Staff preference period.
        $data['staff_preference_settings'] = json_encode( array(
            'period' => array(
                'before' => isset( $data['staff_preferred_period_before'] ) ? max( 0, (int) $data['staff_preferred_period_before'] ) : 0,
                'after'  => isset( $data['staff_preferred_period_after'] ) ? max( 0, (int) $data['staff_preferred_period_after'] ) : 0,
            ),
        ) );

        return $data;
    }
}