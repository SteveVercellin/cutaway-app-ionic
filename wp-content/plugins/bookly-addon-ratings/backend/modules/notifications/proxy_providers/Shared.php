<?php
namespace BooklyRatings\Backend\Modules\Notifications\ProxyProviders;

use Bookly\Backend\Modules\Notifications\Proxy;

/**
 * Class Shared
 * @package BooklyRatings\Backend\Modules\Notifications\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * Add codes for displaying in notification templates.
     *
     * @param array  $codes
     * @param string $type
     *
     * @return array
     */
    public static function prepareNotificationCodes( array $codes, $type )
    {
        $codes['rating'] = array(
            'staff_rating_url' => __( 'URL of the page for staff rating', 'bookly' ),
        );

        return $codes;
    }
}