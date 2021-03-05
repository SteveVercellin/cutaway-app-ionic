<?php
namespace BooklyLocations\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;
use BooklyLocations\Lib;
use BooklyLocations\Backend\Modules\Locations\Page;

/**
 * Class Local
 * @package BooklyLocations\Lib\ProxyProviders
 */
abstract class Local extends BooklyLib\Proxy\Locations
{
    /**
     * @inheritdoc
     */
    public static function addBooklyMenuItem()
    {
        add_submenu_page(
            'bookly-menu',
            __( 'Locations', 'bookly' ),
            __( 'Locations', 'bookly' ),
            BooklyLib\Utils\Common::getRequiredCapability(),
            Page::pageSlug(),
            function () { Page::render(); }
        );
    }

    /**
     * @inheritdoc
     */
    public static function findById( $location_id )
    {
        return Lib\Entities\Location::find( $location_id );
    }

    /**
     * @inheritdoc
     */
    public static function findByStaffId( $staff_id )
    {
        return Lib\Entities\Location::query( 'l' )
            ->select( 'l.*' )
            ->leftJoin( 'StaffLocation', 'sl', 'sl.location_id = l.id' )
            ->where( 'sl.staff_id', $staff_id )
            ->find();
    }

    /**
     * @inheritdoc
     */
    public static function servicesPerLocationAllowed()
    {
        return get_option( 'bookly_locations_allow_services_per_location' );
    }

    /**
     * @inheritdoc
     */
    public static function prepareStaffLocationId( $location_id, $staff_id )
    {
        $custom_services = Lib\Entities\StaffLocation::query()
            ->select( 'custom_services' )
            ->where( 'staff_id', $staff_id )
            ->where( 'location_id', $location_id )
            ->limit( 1 )
            ->fetchRow();

        if ( $custom_services && $custom_services['custom_services'] ) {
            return $location_id;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public static function prepareStaffScheduleLocationId( $location_id, $staff_id )
    {
        $custom_services = Lib\Entities\StaffLocation::query()
            ->select( 'custom_schedule' )
            ->where( 'staff_id', $staff_id )
            ->where( 'location_id', $location_id )
            ->limit( 1 )
            ->fetchRow();

        if ( $custom_services && $custom_services['custom_schedule'] ) {
            return $location_id;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public static function prepareStaffScheduleQuery( $query, $location_id, $staff_id )
    {
        $query->where( 'location_id', $location_id );
        if ( ! $query->count() ) {
            foreach ( array( 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' ) as $day_index => $week_day ) {
                $item = new BooklyLib\Entities\StaffScheduleItem();
                $item
                    ->setStaffId( $staff_id )
                    ->setLocationId( $location_id )
                    ->setDayIndex( $day_index + 1 )
                    ->setStartTime( null )
                    ->setEndTime( null )
                    ->save();
            }
        }

        return $query;
    }

    /**
     * @inheritdoc
     */
    public static function prepareWorkingSchedule( $working_schedule, $staff_ids )
    {
        if ( self::servicesPerLocationAllowed() ) {
            $query = Lib\Entities\StaffLocation::query( 'sl' )
                ->select( 'ssi.id, ssi.staff_id, sl.location_id, ssi.day_index, ssi.start_time, ssi.end_time, break.start_time AS break_start, break.end_time AS break_end' )
                ->leftJoin( 'StaffScheduleItem', 'ssi', 'ssi.staff_id=sl.staff_id AND IF(sl.custom_schedule =1, ssi.location_id = sl.location_id, ssi.location_id IS NULL)', '\Bookly\Lib\Entities' )
                ->leftJoin( 'ScheduleItemBreak', 'break', 'break.staff_schedule_item_id = ssi.id', '\Bookly\Lib\Entities' )
                ->whereIn( 'staff_id', $staff_ids )
                ->whereNot( 'ssi.start_time', null );

            $working_schedule = $query->fetchArray();
        }

        return $working_schedule;
    }

    /**
     * @inheritdoc
     */
    public static function prepareLocationsForCombinedServices( $locations, $services )
    {
        if ( self::servicesPerLocationAllowed() ) {
            $query = BooklyLib\Entities\StaffService::query( 'ss' )
                ->leftJoin( 'Service', 's', 's.id = ss.service_id', '\Bookly\Lib\Entities' )
                ->whereIn( 'ss.service_id', $services )
                ->groupBy( 'ss.service_id, sl.location_id' );
            $query = BooklyLib\Proxy\Shared::prepareStaffServiceQuery( $query );
            foreach ( array_count_values( $query->fetchCol( 'sl.location_id' ) ) as $value => $count ) {
                if ( $count == count( $services ) ) {
                    $locations[] = $value;
                }
            }
        }

        return $locations;
    }
}