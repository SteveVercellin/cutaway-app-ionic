<?php
namespace BooklyLocations\Backend\Modules\Locations;

use Bookly\Lib as BooklyLib;

/**
 * Class Page
 * @package BooklyLocations\Backend\Modules\Locations
 */
class Page extends BooklyLib\Base\Component
{
    public static function render()
    {
        $googleMapApiKey = 'AIzaSyBLGYeNjCqoDHPmMW8Sv2NDaUQ9Jr_dH7Q';
        self::enqueueStyles( array(
            'bookly' => array(
                'backend/resources/bootstrap/css/bootstrap-theme.min.css',
                'frontend/resources/css/ladda.min.css',
            ),
        ) );

        wp_enqueue_script('google-map', "//maps.googleapis.com/maps/api/js?key={$googleMapApiKey}&libraries=places", array(), '1.0', true);
        self::enqueueScripts( array(
            'bookly' => array(
                'backend/resources/bootstrap/js/bootstrap.min.js' => array( 'jquery' ),
                'backend/resources/js/datatables.min.js'  => array( 'jquery' ),
                'backend/resources/js/dropdown.js' => array( 'jquery' ),
                'frontend/resources/js/spin.min.js' => array( 'jquery' ),
                'frontend/resources/js/ladda.min.js' => array( 'jquery' ),
            ),
            'module' => array( 'js/locations.js' => array( 'bookly-dropdown.js' ), ),
        ) );

        $staff_collection = BooklyLib\Entities\Staff::query()->select( 'id, full_name' )->indexBy( 'id' )->fetchArray();

        wp_localize_script( 'bookly-locations.js', 'BooklyL10n', array(
            'csrfToken'   => BooklyLib\Utils\Common::getCsrfToken(),
            'edit'        => __( 'Edit', 'bookly' ),
            'areYouSure'  => __( 'Are you sure?', 'bookly' ),
            'zeroRecords' => __( 'No locations found.', 'bookly' ),
            'processing'  => __( 'Processing...', 'bookly' ),
            'reorder'     => esc_attr( __( 'Reorder', 'bookly' ) ),
            'staff'       => array(
                'allSelected'     => __( 'All staff', 'bookly' ),
                'nothingSelected' => __( 'No staff selected', 'bookly' ),
                'collection'      => $staff_collection,
            ),
        ) );

        $staff_dropdown_data = BooklyLib\Proxy\Pro::getStaffDataForDropDown();

        self::renderTemplate( 'index', compact( 'staff_dropdown_data' ) );
    }

}