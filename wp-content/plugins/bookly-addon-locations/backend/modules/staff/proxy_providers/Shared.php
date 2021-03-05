<?php
namespace BooklyLocations\Backend\Modules\Staff\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Backend\Modules\Staff\Proxy;
use BooklyLocations\Lib;

/**
 * Class Shared
 * @package BooklyLocations\Backend\Modules\Staff\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function renderStaffForm( BooklyLib\Entities\Staff $staff )
    {
        $staff_locations = BooklyLib\Entities\Staff::query( 's' )
            ->select( 'sl.staff_id, GROUP_CONCAT(DISTINCT l.id) AS location_ids' )
            ->leftJoin( 'StaffLocation', 'sl', 'sl.staff_id = s.id', '\BooklyLocations\Lib\Entities' )
            ->leftJoin( 'Location', 'l', 'sl.location_id = l.id', '\BooklyLocations\Lib\Entities' )
            ->where( 'id', $staff->getId() )
            ->fetchRow();
        $locations       = Lib\Entities\Location::query( 'l' )
            ->select( 'l.*, COUNT(sl.id) AS total_locations, GROUP_CONCAT(DISTINCT sl.id) AS location_ids' )
            ->leftJoin( 'StaffLocation', 'sl', 'sl.location_id = l.id' )
            ->groupBy( 'l.id' )
            ->indexBy( 'id' )
            ->sortBy( 'l.position' )
            ->fetchArray();

        self::renderTemplate( 'staff_form', compact( 'locations', 'staff_locations' ) );
    }

    /**
     * @inheritdoc
     */
    public static function updateStaff( BooklyLib\Entities\Staff $staff, array $params )
    {
        $location_ids = isset ( $params['location_ids'] ) ? $params['location_ids'] : array();
        $staff_id     = $staff->getId();
        if ( empty ( $location_ids ) ) {
            Lib\Entities\StaffLocation::query()->delete()->where( 'staff_id', $staff_id )->execute();
        } else {
            Lib\Entities\StaffLocation::query()->delete()->where( 'staff_id', $staff_id )->whereNotIn( 'location_id', $location_ids )->execute();
            $staff_locations_exists = Lib\Entities\StaffLocation::query()->select( 'location_id' )->where( 'staff_id', $staff_id )->fetchArray();
            $staff_locations        = array();
            foreach ( $staff_locations_exists as $location ) {
                $staff_locations[] = $location['location_id'];
            }
            foreach ( $location_ids as $location_id ) {
                if ( ! in_array( $location_id, $staff_locations ) ) {
                    $staff_location = new Lib\Entities\StaffLocation();
                    $staff_location->setStaffId( $staff_id )
                        ->setLocationId( $location_id )
                        ->save();
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function updateStaffServices( array $_post )
    {
        $location_id = array_key_exists( 'location_id', $_post ) ? $_post['location_id'] : null;
        $staff_id    = $_post['staff_id'];
        $custom_settings = $_post['custom_location_settings'];
        if ( $location_id && $staff_id ) {
            Lib\Entities\StaffLocation::query()
                ->update()
                ->set( 'custom_services', $custom_settings )
                ->where( 'staff_id', $staff_id )
                ->where( 'location_id', $location_id )
                ->execute();
        }
    }

    /**
     * @inheritdoc
     */
    public static function updateStaffSchedule( array $_post )
    {
        $location_id     = $_post['location_id'];
        $staff_id        = $_post['staff_id'];
        $custom_settings = $_post['custom_location_settings'];
        if ( $location_id && $staff_id ) {
            Lib\Entities\StaffLocation::query()
                ->update()
                ->set( 'custom_schedule', $custom_settings )
                ->where( 'staff_id', $staff_id )
                ->where( 'location_id', $location_id )
                ->execute();
        }
    }
}