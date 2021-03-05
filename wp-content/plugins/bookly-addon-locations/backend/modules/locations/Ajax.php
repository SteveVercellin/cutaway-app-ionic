<?php
namespace BooklyLocations\Backend\Modules\Locations;

use BooklyLocations\Lib;
use Bookly\Lib as BooklyLib;

/**
 * Class Controller
 * @package BooklyLocations\Backend\Modules\Locations
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * Get list of locations.
     */
    public static function getLocations()
    {
        $query = Lib\Entities\Location::query( 'l' );
        $rows = $query->select( 'l.id, l.name, l.info, l.position,
                GROUP_CONCAT(DISTINCT s.id) AS staff_ids, l.lat, l.lng' )
            ->leftJoin( 'StaffLocation', 'sl', 'sl.location_id = l.id' )
            ->leftJoin( 'Staff', 's', 's.id = sl.staff_id', '\Bookly\Lib\Entities' )
            ->groupBy( 'l.id' )
            ->sortBy( 'position' )
            ->fetchArray();

        foreach( $rows as &$row ) {
            $row['staff_ids'] = $row['staff_ids'] ? explode( ',', $row['staff_ids'] ) : array();
        }

        wp_send_json_success( $rows );
    }

    /**
     * Update locations position.
     */
    public static function updateLocationsPosition()
    {
        $locations_sort = (array) self::parameter( 'positions' );
        foreach ( $locations_sort as $position => $location_id ) {
            $locations_sort = new Lib\Entities\Location();
            $locations_sort->load( $location_id );
            $locations_sort->setPosition( $position );
            $locations_sort->save();
        }
        wp_send_json_success();
    }

    /**
     * Remove location(s).
     */
    public static function deleteLocations()
    {
        $location_ids = array_map( 'intval', self::parameter( 'locations', array() ) );
        Lib\Entities\Location::query()->delete()->whereIn( 'id', $location_ids )->execute();
        wp_send_json_success();
    }

    /**
     * Add new location.
     */
    public static function saveLocation()
    {
        $form = new Forms\Location();
        $form->bind( self::postParameters() );
        $location = $form->save();

        $staff_ids = self::parameter( 'staff_ids', array() );
        if ( empty ( $staff_ids ) ) {
            Lib\Entities\StaffLocation::query()
                ->delete()
                ->where( 'location_id', $location->getId() )
                ->execute();
        } else {
            Lib\Entities\StaffLocation::query()
                ->delete()
                ->where( 'location_id', $location->getId() )
                ->whereNotIn( 'staff_id', $staff_ids )
                ->execute();
            $existing_staff_ids = Lib\Entities\StaffLocation::query()
                ->select( 'staff_id' )
                ->where( 'location_id', $location->getId() )
                ->indexBy( 'staff_id' )
                ->fetchArray();
            foreach ( $staff_ids as $staff_id ) {
                if ( ! isset ( $existing_staff_ids[ $staff_id ] ) ) {
                    $staff_location = new Lib\Entities\StaffLocation();
                    $staff_location->setStaffId( $staff_id )
                        ->setLocationId( $location->getId() )
                        ->save();
                }
            }
        }
        $row = Lib\Entities\Location::query( 'l' )
            ->select( 'l.id, l.name, l.info, l.position,
                GROUP_CONCAT(DISTINCT s.id) AS staff_ids, l.lat, l.lng' )
            ->leftJoin( 'StaffLocation', 'sl', 'sl.location_id = l.id' )
            ->leftJoin( 'Staff', 's', 's.id = sl.staff_id', '\Bookly\Lib\Entities' )
            ->groupBy( 'l.id' )
            ->where( 'l.id', $location->getId() )
            ->fetchRow();

        $row['staff_ids'] = $row['staff_ids'] ? explode( ',', $row['staff_ids'] ) : array();

        wp_send_json_success( $row );
    }

}