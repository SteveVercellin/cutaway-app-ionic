<?php
namespace BooklyLocations\Backend\Components\TinyMce\ProxyProviders;

use Bookly\Backend\Components\TinyMce\Proxy;

/**
 * Class Shared
 * @package BooklyLocations\Backend\Components\TinyMce
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function renderBooklyFormHead()
    {
        self::renderTemplate( 'bookly_form_head' );
    }
}