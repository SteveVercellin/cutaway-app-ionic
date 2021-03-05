<?php
namespace BooklyPro\Backend\Modules\Customers\ProxyProviders;

use Bookly\Backend\Modules\Customers\Proxy;
use Bookly\Lib as BooklyLib;

/**
 * Class Local
 * @package BooklyPro\Backend\Modules\Customers\ProxyProviders
 */
class Local extends Proxy\Pro
{
    /**
     * @inheritdoc
     */
    public static function importCustomers()
    {
        @ini_set( 'auto_detect_line_endings', true );
        $fields = array();
        foreach ( array( 'full_name', 'first_name', 'last_name', 'phone', 'email', 'birthday' ) as $field ) {
            if ( self::parameter( $field ) ) {
                $fields[] = $field;
            }
        }
        if ( file_exists( $_FILES['import_customers_file']['tmp_name'] ) ) {
            $file = fopen( $_FILES['import_customers_file']['tmp_name'], 'r' );
            if ( $file ) {
                while ( $line = fgetcsv( $file, null, self::parameter( 'import_customers_delimiter' ) ) ) {
                    if ( $line[0] != '' ) {
                        $customer = new BooklyLib\Entities\Customer();
                        foreach ( $line as $number => $value ) {
                            if ( $number < count( $fields ) ) {
                                if ( $fields[ $number ] == 'birthday' ) {
                                    $dob = date_create( $value );
                                    if ( $dob !== false ) {
                                        $customer->setBirthday( $dob->format( 'Y-m-d' ) );
                                    }
                                } else {
                                    $method = 'set' . implode( '', array_map( 'ucfirst', explode( '_', $fields[ $number ] ) ) );
                                    $customer->$method( $value );
                                }
                            }
                        }
                        $customer->save();
                    }
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function renderCustomerAddressTableHeader()
    {
        self::renderTemplate( 'customer_address' );
    }

    /**
     * @inheritdoc
     */
    public static function renderImportButton()
    {
        self::renderTemplate( 'import_button' );
    }

    /**
     * @inheritdoc
     */
    public static function renderExportButton()
    {
        self::renderTemplate( 'export_button' );
    }

    /**
     * @inheritdoc
     */
    public static function renderImportDialog()
    {
        self::renderTemplate( 'import_dialog' );
    }

    /**
     * @inheritdoc
     */
    public static function renderExportDialog( array $info_fields )
    {
        self::renderTemplate( 'export_dialog', compact( 'info_fields' ) );
    }
}