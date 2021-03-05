<?php
namespace BooklyLocations\Backend\Modules\Calendar\ProxyProviders;

use Bookly\Backend\Modules\Calendar\Proxy;
use Bookly\Lib\Query;
use BooklyLocations\Lib;

/**
 * Class Shared
 * @package BooklyLocations\Backend\Modules\Calendar\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareAppointmentCodesData( array $codes, $appointment_data, $participants )
    {
        $location_id = $appointment_data['location_id'];
        if ( $location_id ) {
            $location = Lib\Entities\Location::find( $location_id );
            if ( $location ) {
                $codes['{location_name}'] = $location->getName();
                $codes['{location_info}'] = $location->getInfo();
            }
        }

        return $codes;
    }

    /**
     * @inheritdoc
     */
    public static function prepareAppointmentsQueryForFC( Query $query, $staff_id, \DateTime $start_date, \DateTime $end_date )
    {
        $location_ids = array_filter( explode( ',', self::parameter( 'location_ids' ) ) );

        if ( !empty( $location_ids ) && !in_array( 'all', $location_ids ) ) {

            $raw_where = array();
            if ( in_array( 'no', $location_ids ) ) {
                $raw_where[] = 'a.location_id IS NULL';
            }

            $location_ids = array_filter( $location_ids, 'is_numeric' );
            if ( !empty( $location_ids ) ) {
                $raw_where[] = 'a.location_id IN (' . implode( ',', $location_ids ) . ')';
            }

            if ( $raw_where ) {
                $query->whereRaw( implode( ' OR ', $raw_where ), array() );
            }
        }
    }

}