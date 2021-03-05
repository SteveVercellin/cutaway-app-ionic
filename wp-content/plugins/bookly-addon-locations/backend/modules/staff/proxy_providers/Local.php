<?php
namespace BooklyLocations\Backend\Modules\Staff\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Backend\Modules\Staff\Proxy;
use BooklyLocations\Lib;

/**
 * Class Local
 * @package BooklyLocations\Backend\Modules\Staff\ProxyProviders
 */
class Local extends Proxy\Locations
{
    /**
     * @inheritdoc
     */
    public static function renderLocationSwitcher( $staff_id, $location_id, $type = 'custom_services' )
    {
        if ( get_option( 'bookly_locations_allow_services_per_location' ) ) {
            $staff_locations = BooklyLib\Entities\Staff::query( 's' )
                ->select( 'l.id, l.name' )
                ->leftJoin( 'StaffLocation', 'sl', 'sl.staff_id = s.id', '\BooklyLocations\Lib\Entities' )
                ->leftJoin( 'Location', 'l', 'sl.location_id = l.id', '\BooklyLocations\Lib\Entities' )
                ->where( 'id', $staff_id )
                ->fetchArray();
            if ( count( $staff_locations ) && $staff_locations[0]['id'] ) {
                $row = Lib\Entities\StaffLocation::query()
                    ->select( $type )
                    ->where( 'staff_id', $staff_id )
                    ->where( 'location_id', $location_id )
                    ->fetchRow();

                $custom_settings = $row[ $type ];

                self::renderTemplate( 'location_switcher', compact( 'staff_locations', 'location_id', 'custom_settings' ) );
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function getStaffSchedule( $staff_id, $location_id )
    {
        $schedule = array();

        if ( $location_id !== null ) {
            $working_schedule = Lib\Entities\StaffLocation::query( 'sl' )
                ->select( 'ssi.day_index, ssi.start_time, ssi.end_time' )
                ->leftJoin( 'StaffScheduleItem', 'ssi', 'ssi.staff_id=sl.staff_id AND IF(sl.custom_schedule =1, ssi.location_id = sl.location_id, ssi.location_id IS NULL)', '\Bookly\Lib\Entities' )
                ->where( 'sl.staff_id', $staff_id )
                ->whereNot( 'sl.location_id', $location_id )
                ->whereNot( 'ssi.start_time', null )
                ->fetchArray();
        } else {
            $working_schedule = Lib\Entities\StaffLocation::query( 'sl' )
                ->select( 'ssi.day_index, ssi.start_time, ssi.end_time' )
                ->leftJoin( 'StaffScheduleItem', 'ssi', 'ssi.staff_id=sl.staff_id AND ssi.location_id = sl.location_id', '\Bookly\Lib\Entities' )
                ->where( 'sl.staff_id', $staff_id )
                ->where( 'sl.custom_schedule', 1 )
                ->whereNot( 'ssi.start_time', null )
                ->fetchArray();
        }

        foreach ( $working_schedule as $day ) {
            $schedule[ $day['day_index'] ][] = array( $day['start_time'], $day['end_time'] );
        }

        return $schedule;
    }
}