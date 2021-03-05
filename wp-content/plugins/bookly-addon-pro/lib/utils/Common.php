<?php
namespace BooklyPro\Lib\Utils;

use Bookly\Lib as BooklyLib;

/**
 * Class Common
 * @package BooklyPro\Lib\Utils
 */
abstract class Common
{
    /**
     * WPML translation
     *
     * @param array $appointments
     * @return array
     */
    public static function translateAppointments( array $appointments )
    {
        $postfix_any = sprintf( ' (%s)', BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_option_employee' ) );
        foreach ( $appointments as &$appointment ) {
            $category                = new BooklyLib\Entities\Category( array( 'id' => $appointment['category_id'], 'name' => $appointment['category'] ) );
            $service                 = new BooklyLib\Entities\Service( array( 'id' => $appointment['service_id'], 'title' => $appointment['service'] ) );
            $staff                   = new BooklyLib\Entities\Staff( array( 'id' => $appointment['staff_id'], 'full_name' => $appointment['staff'] ) );
            $appointment['category'] = $category->getTranslatedName();
            $appointment['service']  = $service->getTranslatedTitle();
            $appointment['staff']    = $staff->getTranslatedName() . ( $appointment['staff_any'] ? $postfix_any : '' );
            // Prepare extras.
            $appointment['extras'] = (array) BooklyLib\Proxy\ServiceExtras::getCAInfo( json_decode( $appointment['ca_id'], true ), true );
        }

        return $appointments;
    }

    /**
     * @return array
     */
    public static function getDisplayedAddressFields()
    {
        $address_show_fields = (array) get_option( 'bookly_cst_address_show_fields', array() );

        return array_filter( $address_show_fields, function( $field ) {
            return ! ( is_array( $field ) && array_key_exists( 'show', $field ) && ! $field['show'] );
        } );
    }

    /**
     * @param array $data
     * @return string
     */
    public static function getFullAddressByCustomerData( array $data )
    {
        $address_show_fields = self::getDisplayedAddressFields();
        $address_fields      = array();

        foreach ( $address_show_fields as $field_name => $field ) {

            if ( ! array_key_exists( $field_name, $data ) ) {
                continue;
            }

            $address_fields[] = $data[ $field_name ];
        }

        return implode( ', ', array_filter( $address_fields ) );
    }

    /**
     * Create day options.
     *
     * @return array
     */
    public static function dayOptions()
    {
        return array_combine( range( 1, 31 ), range( 1, 31 ) );
    }

    /**
     * Create month options.
     *
     * @return array
     */
    public static function monthOptions()
    {
        global $wp_locale;

        return array_combine( range( 1, 12 ), $wp_locale->month );
    }

    /**
     * Create year options.
     *
     * @param int $delta_from
     * @param int $delta_to
     *
     * @return array
     */
    public static function yearOptions( $delta_from = 0, $delta_to = -100 )
    {
        $year  = (int) BooklyLib\Slots\DatePoint::now()->format( 'Y' );
        $range = range( $year + $delta_from, $year + $delta_to );

        return array_combine( $range, $range );
    }

}