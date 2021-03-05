<?php
namespace BooklyLocations\Lib;

use BooklyLocations\Backend\Modules as Backend;
use BooklyLocations\Frontend\Modules as Frontend;
use BooklyLocations\Backend\Components;

/**
 * Class Plugin
 * @package BooklyLocations\Lib
 */
abstract class Plugin extends \Bookly\Lib\Base\Plugin
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
        Backend\Locations\Ajax::init();

        // Init proxy.
        Backend\Appearance\ProxyProviders\Local::init();
        Backend\Appearance\ProxyProviders\Shared::init();
        Backend\Calendar\ProxyProviders\Local::init();
        Backend\Calendar\ProxyProviders\Shared::init();
        Backend\Notifications\ProxyProviders\Shared::init();
        Backend\Settings\ProxyProviders\Shared::init();
        Backend\Staff\ProxyProviders\Local::init();
        Backend\Staff\ProxyProviders\Shared::init();
        Components\Appearance\ProxyProviders\Shared::init();
        Components\TinyMce\ProxyProviders\Shared::init();

        Frontend\Booking\ProxyProviders\Shared::init();

        Notifications\Assets\Item\ProxyProviders\Shared::init();
        ProxyProviders\Local::init();
        ProxyProviders\Shared::init();
    }
}