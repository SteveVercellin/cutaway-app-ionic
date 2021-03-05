<?php
namespace BooklyRatings\Lib\Utils;

use Bookly\Lib as BooklyLib;

/**
 * Class Common
 * @package BooklyRatings\Lib\Utils
 */
abstract class Common
{
    /**
     * @param      $staff_id
     * @param null $service_id
     *
     * @return float|null
     */
    public static function calculateStaffRating( $staff_id, $service_id = null )
    {
        $query = BooklyLib\Entities\CustomerAppointment::query( 'ca' )
            ->select( 'AVG (ca.rating) as rating' )
            ->leftJoin( 'Appointment', 'a', 'a.id = ca.appointment_id' )
            ->where( 'a.staff_id', $staff_id )
            ->whereGte( 'a.start_date', date_create( current_time( 'mysql' ) )->modify( sprintf( '- %s days', get_option( 'bookly_ratings_period', 365 ) ) )->format( 'Y-m-d H:i:s' ) )
            ->whereNot( 'ca.rating', null );
        if ( $service_id ) {
            $service = BooklyLib\Entities\Service::find( $service_id );
            if ( $service->withSubServices() ) {
                $sub_services = BooklyLib\Entities\Service::query( 's' )
                    ->innerJoin( 'SubService', 'ss', 'ss.sub_service_id = s.id' )
                    ->where( 'ss.service_id', $service_id )
                    ->where( 'ss.type', BooklyLib\Entities\SubService::TYPE_SERVICE )
                    ->fetchCol( 's.id' );
                $query->whereIn( 'a.service_id', $sub_services );
            } else {
                $query->where( 'a.service_id', $service_id );
            }
        }
        $rating = $query->fetchRow();

        return $rating['rating'] ? number_format( (float) $rating['rating'], 1, '.', '' ) : null;
    }

}