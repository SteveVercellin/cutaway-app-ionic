<?php
namespace BooklyRatings\Backend\Modules\Appointments\ProxyProviders;

use Bookly\Backend\Modules\Appointments\Proxy\Ratings as RatingsProxy;

/**
 * Class Local
 * @package BooklyRatings\Backend\Modules\Appointments\ProxyProviders
 */
class Local extends RatingsProxy
{
    /**
     * @inheritdoc
     */
    public static function renderTableHeader()
    {
        printf( '<th>%s</th>', __( 'Rating', 'bookly' ) );
    }

    /**
     * @inheritdoc
     */
    public static function renderExport()
    {
        printf( '<div class="checkbox"><label><input checked name="exp[rating]" type="checkbox"/>%s</label></div>', __( 'Rating', 'bookly' ) );
    }

    /**
     * @inheritdoc
     */
    public static function prepareExportTitles( $titles )
    {
        $titles['rating'] = __( 'Rating', 'bookly' );

        return $titles;
    }
}