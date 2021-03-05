<?php
namespace BooklyRatings\Lib;

use Bookly\Lib;
use BooklyRatings\Backend;
use BooklyRatings\Frontend;

/**
 * Class Plugin
 * @package BooklyRatings\Lib
 */
abstract class Plugin extends Lib\Base\Plugin
{
    protected static $prefix;
    protected static $title;
    protected static $version;
    protected static $slug;
    protected static $directory;
    protected static $main_file;
    protected static $basename;
    protected static $text_domain;
    protected static $root_namespace;
    protected static $embedded;

    /**
     * @inheritdoc
     */
    protected static function init()
    {
        // Init ajax.
        Frontend\Modules\StaffRating\Ajax::init();

        Backend\Components\Gutenberg\StaffRatings\Block::init();

        // Init proxy.
        Backend\Components\TinyMce\ProxyProviders\Shared::init();
        Backend\Modules\Appearance\ProxyProviders\Shared::init();
        Backend\Modules\Appointments\ProxyProviders\Local::init();
        Backend\Modules\Notifications\ProxyProviders\Shared::init();
        Backend\Modules\Settings\ProxyProviders\Shared::init();
        Backend\Modules\Staff\ProxyProviders\Local::init();
        Notifications\Assets\Item\ProxyProviders\Shared::init();
        Notifications\Assets\Test\ProxyProviders\Shared::init();
        ProxyProviders\Local::init();

        if ( ! is_admin() ) {
            // Init short code.
            Frontend\Modules\StaffRating\ShortCode::init();
        }
    }
}