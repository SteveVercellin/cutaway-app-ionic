<?php
namespace Bookly\Backend\Modules\Debug;

use Bookly\Lib;
use Bookly\Backend\Modules\Debug\Lib\Schema;
use Bookly\Backend\Modules\Debug\Lib\QueryBuilder;

/**
 * Class Page
 * @package Bookly\Backend\Modules\Debug
 */
class Page extends Lib\Base\Component
{
    const TABLE_STATUS_OK      = 1;
    const TABLE_STATUS_ERROR   = 0;
    const TABLE_STATUS_WARNING = 2;
    const TABLE_STATUS_INFO    = 3;

    /**
     * Render page.
     */
    public static function render()
    {
        /** @var \wpdb $wpdb*/
        global $wpdb;

        self::enqueueStyles( array(
            'frontend' => array( 'css/ladda.min.css', ),
            'backend'  => array( 'bootstrap/css/bootstrap-theme.min.css', ),
            'module'   => array( 'css/style.css' ),
        ) );

        self::enqueueScripts( array(
            'backend' => array(
                'bootstrap/js/bootstrap.min.js' => array( 'jquery' ),
                'js/alert.js' => array( 'jquery' ),
            ),
            'frontend' => array(
                'js/spin.min.js'  => array( 'jquery', ),
                'js/ladda.min.js' => array( 'jquery', ),
            ),
            'module'  => array( 'js/debug.js' => array( 'jquery' ) ),
        ) );

        $debug  = array();
        $schema = new Schema();
        /** @var Lib\Base\Plugin $plugin */
        foreach ( apply_filters( 'bookly_plugins', array() ) as $plugin ) {
            foreach ( $plugin::getEntityClasses() as $entity_class ) {
                $table_name = $entity_class::getTableName();
                $debug[ $table_name ] = array(
                    'fields'      => null,
                    'constraints' => null,
                    'status'      => null,
                );
                if ( $schema->existsTable( $table_name ) ) {
                    $table_structure    = $schema->getTableStructure( $table_name );
                    $table_constraints  = $schema->getTableConstraints( $table_name );
                    $entity_schema      = $entity_class::getSchema();
                    $entity_constraints = $entity_class::getConstraints();
                    $debug[ $table_name ]['status'] = self::TABLE_STATUS_OK;
                    $debug[ $table_name ]['fields'] = array();

                    // Comparing model schema with real DB schema
                    foreach ( $entity_schema as $field => $data ) {
                        if ( array_key_exists( $field, $table_structure ) ) {
                            $debug[ $table_name ]['fields'][ $field ] = 1;
                            $expect = QueryBuilder::getColumnData( $table_name, $field );
                            $actual = $table_structure[ $field ];
                            unset( $expect['key'], $actual['key'] );
                            $diff = array_diff_assoc( $actual, $expect );
                            if ( $expect && $diff ) {
                                $debug[ $table_name ]['status'] = self::TABLE_STATUS_INFO;
                                $debug[ $table_name ]['info'][ $field ] = array_keys( $diff );
                            }
                        } else {
                            $debug[ $table_name ]['fields'][ $field ] = 0;
                            $debug[ $table_name ]['status'] = self::TABLE_STATUS_WARNING;
                        }
                    }

                    // Comparing model constraints with real DB constraints
                    foreach ( $entity_constraints as $constraint ) {
                        $key = $constraint['column_name'] . $constraint['referenced_table_name'] . $constraint['referenced_column_name'];
                        $debug[ $table_name ]['constraints'][ $key ] = $constraint;
                        if ( array_key_exists ( $key, $table_constraints ) ) {
                            $debug[ $table_name ]['constraints'][ $key ]['status'] = 1;
                        } else {
                            $debug[ $table_name ]['constraints'][ $key ]['status'] = 0;
                            $debug[ $table_name ]['status'] = self::TABLE_STATUS_WARNING;
                        }
                    }
                    $debug[ $table_name ]['constraints_3d'] = array();
                    foreach ( $table_constraints as $constraint_name => $constraint ) {
                        $key = $constraint['column_name'] . $constraint['referenced_table_name'] . $constraint['referenced_column_name'];
                        if ( ! isset( $debug[ $table_name ]['constraints'][ $key ] ) ) {
                            $debug[ $table_name ]['constraints_3d'][ $key ] = $constraint;
                            $debug[ $table_name ]['constraints_3d'][ $key ]['status'] = 0;
                            $debug[ $table_name ]['status'] = self::TABLE_STATUS_WARNING;
                        }
                    }

                } else {
                    $debug[ $table_name ]['status'] = self::TABLE_STATUS_ERROR;
                }
            }
        }

        wp_localize_script( 'bookly-debug.js', 'BooklyL10n', array(
            'csrfToken'      => Lib\Utils\Common::getCsrfToken(),
            'charsetCollate' => $wpdb->has_cap( 'collation' )
                ? $wpdb->get_charset_collate()
                : 'DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci'
        ) );

        ksort( $debug );
        $import_status = self::parameter( 'status' );
        self::renderTemplate( 'index', compact( 'debug', 'import_status' ) );
    }
}