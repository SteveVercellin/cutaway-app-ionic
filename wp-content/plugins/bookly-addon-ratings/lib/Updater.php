<?php
namespace BooklyRatings\Lib;

use Bookly\Lib;

/**
 * Class Updates
 * @package BooklyRatings\Lib
 */
class Updater extends Lib\Base\Updater
{
    function update_1_1()
    {
        add_option( 'bookly_ratings_app_show_on_frontend', '0' );
    }
}