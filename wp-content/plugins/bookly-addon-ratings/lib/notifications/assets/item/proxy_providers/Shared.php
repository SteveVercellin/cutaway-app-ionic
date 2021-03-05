<?php
namespace BooklyRatings\Lib\Notifications\Assets\Item\ProxyProviders;

use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Assets\Item\Proxy;

/**
 * Class Shared
 * @package BooklyRatings\Lib\Notifications\Assets\Item\ProxyProviders
 */
abstract class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareCodes( Codes $codes )
    {
        $bookly_ratings_page_url = get_option( 'bookly_ratings_page_url', false );
        $codes->staff_rating_url = $codes->appointment_token && $bookly_ratings_page_url
            ? add_query_arg( 'bookly-rating-token', $codes->appointment_token, $bookly_ratings_page_url )
            : '';
    }

    /**
     * @inheritdoc
     */
    public static function prepareReplaceCodes( array $replace_codes, Codes $codes, $format )
    {
        $replace_codes['{staff_rating_url}'] = $codes->staff_rating_url;

        return $replace_codes;
    }
}