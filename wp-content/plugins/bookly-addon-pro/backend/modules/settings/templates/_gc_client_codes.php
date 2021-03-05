<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Modules\Settings\Proxy;

$codes = array(
    array( 'code' => 'appointment_notes', 'description' => __( 'customer notes for appointment', 'bookly' ) ),
    array( 'code' => 'client_email',      'description' => __( 'email of client', 'bookly' ) ),
    array( 'code' => 'client_first_name', 'description' => __( 'first name of client', 'bookly' ) ),
    array( 'code' => 'client_last_name',  'description' => __( 'last name of client', 'bookly' ) ),
    array( 'code' => 'client_name',       'description' => __( 'full name of client', 'bookly' ) ),
    array( 'code' => 'client_phone',      'description' => __( 'phone of client', 'bookly' ) ),
    array( 'code' => 'payment_status',    'description' => __( 'status of payment', 'bookly' ) ),
    array( 'code' => 'payment_type',      'description' => __( 'payment type', 'bookly' ) ),
    array( 'code' => 'status',            'description' => __( 'status of appointment', 'bookly' ) ),
    array( 'code' => 'total_price',       'description' => __( 'total price of booking (sum of all cart items after applying coupon)', 'bookly' ) ),
);

$codes = Proxy\Shared::prepareCalendarAppointmentCodes( $codes, 'one' );

echo Bookly\Lib\Utils\Common::codes( $codes );