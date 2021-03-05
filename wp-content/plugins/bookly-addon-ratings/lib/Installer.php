<?php
namespace BooklyRatings\Lib;

use Bookly\Lib as BooklyLib;

/**
 * Class Installer
 * @package BooklyRatings\Lib
 */
class Installer extends Base\Installer
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->options = array(
            'bookly_ratings_app_show_on_frontend' => '0',
            'bookly_ratings_timeout'              => '7',
            'bookly_ratings_period'               => '365',
            'bookly_ratings_show_at_backend'      => '1',
            'bookly_ratings_page_url'             => home_url(),
        );
    }
}