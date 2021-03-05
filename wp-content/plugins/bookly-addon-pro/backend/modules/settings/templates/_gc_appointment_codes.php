<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Modules\Settings\Proxy;

$codes = array(
    array( 'code' => 'appointment_date', 'description' => __( 'date of appointment', 'bookly' ) ),
    array( 'code' => 'appointment_time', 'description' => __( 'time of appointment', 'bookly' ) ),
    array( 'code' => 'booking_number',   'description' => __( 'booking number', 'bookly' ) ),
    array( 'code' => 'category_name',    'description' => __( 'name of category', 'bookly' ) ),
    array( 'code' => 'company_address',  'description' => __( 'address of company', 'bookly' ) ),
    array( 'code' => 'company_name',     'description' => __( 'name of company', 'bookly' ) ),
    array( 'code' => 'company_phone',    'description' => __( 'company phone', 'bookly' ) ),
    array( 'code' => 'company_website',  'description' => __( 'company web-site address', 'bookly' ) ),
    array( 'code' => 'service_info',     'description' => __( 'info of service', 'bookly' ) ),
    array( 'code' => 'service_name',     'description' => __( 'name of service', 'bookly' ) ),
    array( 'code' => 'service_price',    'description' => __( 'price of service', 'bookly' ) ),
    array( 'code' => 'staff_email',      'description' => __( 'email of staff', 'bookly' ) ),
    array( 'code' => 'staff_info',       'description' => __( 'info of staff', 'bookly' ) ),
    array( 'code' => 'staff_name',       'description' => __( 'name of staff', 'bookly' ) ),
    array( 'code' => 'staff_phone',      'description' => __( 'phone of staff', 'bookly' ) ),
);

$codes = Proxy\Shared::prepareCalendarAppointmentCodes( $codes, 'many' );

echo Bookly\Lib\Utils\Common::codes( $codes );