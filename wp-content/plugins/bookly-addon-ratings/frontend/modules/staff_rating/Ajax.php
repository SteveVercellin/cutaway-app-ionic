<?php
namespace BooklyRatings\Frontend\Modules\StaffRating;

use Bookly\Lib as BooklyLib;

/**
 * Class Ajax
 * @package BooklyRatings\Frontend\Modules\StaffRating
 */
class Ajax extends BooklyLib\Base\Ajax
{
    protected static function permissions()
    {
        return array( '_default' => 'anonymous' );
    }

    /**
     * Set customer appointment rating.
     */
    public static function setRating()
    {
        $token   = self::parameter( 'token' );
        $rating  = self::parameter( 'rating' );
        $comment = self::parameter( 'comment' );

        $ca = new BooklyLib\Entities\CustomerAppointment();
        $ca->loadBy( array( 'token' => $token ) );
        $appointment = BooklyLib\Entities\Appointment::find( $ca->getAppointmentId() );
        if ( strtotime( $appointment->getEndDate() ) > current_time( 'timestamp' ) - (int) get_option( 'bookly_ratings_timeout' ) * DAY_IN_SECONDS ) {
            if ( $ca->getCompoundServiceId() !== null ) {
                BooklyLib\Entities\CustomerAppointment::query()
                    ->update()
                    ->set( 'rating', $rating )
                    ->set( 'rating_comment', $comment ?: null )
                    ->where( 'compound_token', $ca->getCompoundToken() )
                    ->execute();
            } else {
                $ca
                    ->setRating( $rating )
                    ->setRatingComment( $comment ?: null )
                    ->save();
            }
        }

        wp_send_json_success();
    }
}