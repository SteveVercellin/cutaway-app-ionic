<?php
namespace BooklyPro\Backend\Modules\Staff\ProxyProviders;

use Bookly\Backend\Modules\Staff\Proxy;
use BooklyPro\Lib;

/**
 * Class Local
 * @package BooklyPro\Backend\Modules\Staff\ProxyProviders
 */
class Local extends Proxy\Pro
{
    /**
     * @inheritdoc
     */
    public static function getCategoriesList()
    {
        return Lib\Entities\StaffCategory::query()->sortBy( 'position' )->fetchArray();
    }

    /**
     * @inheritdoc
     */
    public static function renderGoogleCalendarSettings( array $tpl_data )
    {
        self::renderTemplate( 'google_calendar_settings', $tpl_data['gc'] );
    }

    /**
     * @inheritdoc
     */
    public static function updateCategoriesPositions( $categories )
    {
        foreach ( $categories as $position => $category_id ) {
            $category = Lib\Entities\StaffCategory::find( $category_id );
            $category->setPosition( $position )->save();
        }
    }

    /**
     * @inheritdoc
     */
    public static function renderStaffDetails( $staff )
    {
        $categories   = Lib\Entities\StaffCategory::query()->sortBy( 'position' )->fetchArray();
        $categories[] = array( 'id' => null, 'name' => __( 'Uncategorized', 'bookly' ) );

        self::renderTemplate( 'staff_details', compact( 'categories', 'staff' ) );
    }

}