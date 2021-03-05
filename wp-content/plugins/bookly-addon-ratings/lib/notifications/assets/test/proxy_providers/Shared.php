<?php
namespace BooklyRatings\Lib\Notifications\Assets\Test\ProxyProviders;

use Bookly\Lib\Notifications\Assets\Test\Codes;
use Bookly\Lib\Notifications\Assets\Test\Proxy;

/**
 * Class Shared
 * @package BooklyRatings\Lib\Notifications\Assets\Test\ProxyProviders
 */
abstract class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareCodes( Codes $codes )
    {
        $codes->staff_rating_url = home_url();
    }
}