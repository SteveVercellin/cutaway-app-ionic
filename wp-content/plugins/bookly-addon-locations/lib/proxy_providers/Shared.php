<?php
namespace BooklyLocations\Lib\ProxyProviders;

use BooklyLocations\Lib;
use Bookly\Lib as BooklyLib;

/**
 * Class Shared
 * @package BooklyLocations\Lib\ProxyProviders
 */
class Shared extends BooklyLib\Proxy\Shared
{
    /**
     * @param BooklyLib\Query $query
     *
     * @return BooklyLib\Query
     */
    public static function prepareCaSeStQuery( BooklyLib\Query $query )
    {
        if ( get_option( 'bookly_locations_allow_services_per_location' ) ) {
            $query
                ->addSelect( 'sl.location_id' )
                ->leftJoin( 'StaffLocation', 'sl', 'sl.staff_id = ss.staff_id', '\BooklyLocations\Lib\Entities' )
                ->whereRaw( 'ss.location_id IS NULL AND sl.custom_services = 0 OR ss.location_id IS NOT NULL AND sl.custom_services = 1 AND sl.location_id = ss.location_id', array() );
        }

        return $query;
    }

    /**
     * Add locations array to CaSeSt
     *
     * @param array $result
     * @return array
     */
    public static function prepareCaSeSt( $result )
    {
        $simple_services = BooklyLib\Entities\Service::query()
            ->select( 'id' )
            ->where( 'type', BooklyLib\Entities\Service::TYPE_SIMPLE )
            ->where( 'visibility', 'public' )
            ->sortBy( null );

        $type = array();
        if ( BooklyLib\Config::collaborativeServicesActive() ) {
            $type[] = BooklyLib\Entities\Service::TYPE_COLLABORATIVE;
        }
        if ( BooklyLib\Config::compoundServicesActive() ) {
            $type[] = BooklyLib\Entities\Service::TYPE_COMPOUND;
        }
        if ( $type ) {
            $primary_sub_services = BooklyLib\Entities\SubService::query( 'ss' )
                ->select( 'ss.sub_service_id, ss.position' )
                ->leftJoin( 'Service', 's', 's.id = ss.service_id AND s.visibility = \'public\'', '\Bookly\Lib\Entities' )
                ->whereIn( 's.type', $type )
                ->groupBy( 'ss.service_id' )
                ->havingRaw( 'ss.position = MIN(ss.position)', array() )
                ->sortBy( null );
            $sub_query = sprintf( '%s UNION SELECT DISTINCT sub_service_id FROM ( %s ) AS pub_ss', $simple_services, $primary_sub_services );
        } else {
            $sub_query = $simple_services;
        }

        $rows = Lib\Entities\Location::query( 'l' )
            ->select( 'l.id, l.name, l.position, sl.staff_id, COUNT(ss.service_id) AS qnt' )
            ->leftJoin( 'StaffLocation', 'sl', 'sl.location_id = l.id' )
            ->leftJoin( 'StaffService', 'ss', 'ss.staff_id = sl.staff_id', '\Bookly\Lib\Entities' )
            ->innerJoin( 'Staff', 'st', 'st.id = ss.staff_id AND st.visibility = \'public\'', '\Bookly\Lib\Entities' )
            // ids of all public simple services and primary (first) sub_service for all compound and collaborative public services
            ->joinRaw( $sub_query, 's', 's.id = ss.service_id' )
            ->groupBy( 'l.id' )->groupBy( 'sl.staff_id' )
            ->fetchArray();
        foreach ( $rows as $row ) {
            if ( ! isset ( $result['locations'][ $row['id'] ] ) ) {
                $result['locations'][ $row['id'] ] = array(
                    'id'    => (int) $row['id'],
                    'name'  => empty( $row['name'] )
                        ? __( 'Untitled', 'bookly' )
                        : BooklyLib\Utils\Common::getTranslatedString( 'location_' . $row['id'], $row['name'] ),
                    'staff' => array(),
                    'pos'   => (int) $row['position'],
                );
            }
            if ( $row['staff_id'] != null && $row['qnt'] > 0 ) {
                $result['locations'][ $row['id'] ]['staff'][ $row['staff_id'] ] = array();
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public static function prepareStaffServiceQuery( BooklyLib\Query $query )
    {
        if ( Local::servicesPerLocationAllowed() ) {
            $query
                ->addSelect( 'sl.location_id' )
                ->leftJoin( 'StaffLocation', 'sl', 'sl.staff_id = ss.staff_id', '\BooklyLocations\Lib\Entities' )
                ->whereRaw( '( ss.location_id IS NULL AND sl.custom_services = 0 ) OR ( ss.location_id IS NOT NULL AND sl.custom_services = 1 AND sl.location_id = ss.location_id )', array() );
        }

        return $query;
    }
}