<?php
namespace BooklyRatings\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;
use BooklyRatings\Lib;

/**
 * Class Local
 * @package BooklyRatings\Lib\ProxyProviders
 */
abstract class Local extends BooklyLib\Proxy\Ratings
{
    /**
     * @inheritdoc
     */
    public static function prepareCaSeSt( $result )
    {
        foreach ( $result['staff'] as $staff_pos => $staff ) {
            $result['staff'][ $staff_pos ]['rating'] = Lib\Utils\Common::calculateStaffRating( $staff['id'] );
            foreach ( $staff['services'] as $service_id => $service ) {
                $result['staff'][ $staff_pos ]['services'][ $service_id ]['rating'] = Lib\Utils\Common::calculateStaffRating( $staff['id'], $service_id );
            }
        }

        return $result;
    }
}