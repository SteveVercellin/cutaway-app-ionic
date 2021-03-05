<?php
namespace BooklyPro\Backend\Modules\Dashboard\ProxyProviders;

use Bookly\Backend\Modules\Dashboard\Proxy;
use Bookly\Lib as BooklyLib;
use BooklyPro\Lib;

/**
 * Class Local
 * @package BooklyPro\Backend\Modules\Dashboard\ProxyProviders
 */
class Local extends Proxy\Pro
{
    /**
     * @inheritdoc
     */
    public static function renderAnalytics()
    {
        self::enqueueScripts( array(
            'bookly' => array(
                'backend/resources/js/datatables.min.js' => array( 'jquery' ),
                'backend/resources/js/dropdown.js' => array( 'jquery' ),
            ),
            'module' => array( 'js/analytics.js' => array( 'bookly-datatables.min.js', 'bookly-dropdown.js' ) )
        ) );

        wp_localize_script( 'bookly-analytics.js', 'BooklyAnalyticsL10n', array(
            'zeroRecords'   => __( 'No appointments for selected period.', 'bookly' ),
            'processing'    => __( 'Processing...', 'bookly' ),
        ) );

        $dropdown_data = array(
            'service' => BooklyLib\Utils\Common::getServiceDataForDropDown( 's.type = "simple"' ),
            'staff'   => Lib\ProxyProviders\Local::getStaffDataForDropDown()
        );

        self::renderTemplate( 'analytics', compact( 'dropdown_data' ) );
    }
}