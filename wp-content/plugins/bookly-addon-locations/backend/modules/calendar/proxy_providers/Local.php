<?php
namespace BooklyLocations\Backend\Modules\Calendar\ProxyProviders;

use Bookly\Backend\Modules\Calendar\Proxy;
use BooklyLocations\Lib\Entities\Location;

/**
 * Class Local
 * @package BooklyLocations\Backend\Modules\Calendar\ProxyProviders
 */
class Local extends Proxy\Locations
{
    /**
     * renderCalendarLocationFilter
     */
    public static function renderCalendarLocationFilter()
    {
        $locations = Location::query( 'l' )
            ->select( 'l.id, l.name' )
            ->sortBy( 'l.position' )
            ->fetchArray();

        self::renderTemplate( 'location_filter', array( 'locations' => $locations ) );
    }

}