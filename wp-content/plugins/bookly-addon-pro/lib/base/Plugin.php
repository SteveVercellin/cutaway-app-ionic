<?php
namespace BooklyPro\Lib\Base;

use Bookly\Lib as BooklyLib;
use BooklyPro\Lib;

/**
 * Class Plugin
 * @package BooklyPro\Lib\Base
 */
abstract class Plugin
{
    /**
     * Register hooks.
     *
     * @param string $plugin_class
     */
    public static function registerHooks( $plugin_class )
    {
        /** @var BooklyLib\Base\Plugin $plugin_class */

        if ( is_admin() ) {
            if ( $plugin_class::getSlug() != BooklyLib\Plugin::getSlug() && ! $plugin_class::embedded() ) {
                add_filter( 'bookly_save_purchase_codes', function ( $errors, $purchase_codes, $blog_id ) use ( $plugin_class ) {
                    $option = $plugin_class::getPurchaseCodeOption();
                    if ( array_key_exists( $option, (array) $purchase_codes ) ) {
                        $purchase_code = preg_replace( '/[ \t\x00-\x1F\x7F-\xFF]/', '', $purchase_codes[ $option ] );
                        if ( $purchase_code == '' ) {
                            $plugin_class::updatePurchaseCode( '', $blog_id );
                        } else {
                            $result = Lib\API::verifyPurchaseCode( $purchase_code, $plugin_class, $blog_id );
                            if ( $result['valid'] ) {
                                $plugin_class::updatePurchaseCode( $purchase_code, $blog_id );
                                $grace_notifications = get_option( 'bookly_grace_notifications' );
                                $grace_notifications['add-ons'] = '0';
                                if ( $blog_id ) {
                                    update_blog_option( $blog_id, 'bookly_grace_notifications', $grace_notifications );
                                } else {
                                    update_option( 'bookly_grace_notifications', $grace_notifications );
                                }
                            } else {
                                if ( $purchase_code == $plugin_class::getPurchaseCode( $blog_id ) ) {
                                    $plugin_class::updatePurchaseCode( '', $blog_id );
                                }
                                $errors[] = $result['error'];
                            }
                        }
                    }

                    return $errors;
                }, 10, 3 );

                add_action( 'after_plugin_row_' . $plugin_class::getBasename(), function ( $plugin_file, $plugin_data, $status ) use ( $plugin_class ) {
                    $slug = $plugin_class::getSlug();
                    $bookly_update_plugins = get_site_transient( 'bookly_update_plugins' );
                    if ( isset( $bookly_update_plugins[ $slug ]['new_version'] ) ) {
                        $data = $bookly_update_plugins[ $slug ];
                        if ( version_compare( $data['new_version'], $plugin_data['Version'], '>' ) ) {
                            $details_url = self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $slug . '&section=changelog&TB_iframe=true&width=600&height=800' );

                            $new_version_info = sprintf( __( 'There is a new version of %s available. <a href="%s" %s>View version %s details</a>.', 'bookly' ),
                                $plugin_data['Name'],
                                esc_url( $details_url ),
                                sprintf( 'class="thickbox open-plugin-details-modal" aria-label="%s"',
                                    esc_attr( sprintf( __( 'View %s version %s details', 'bookly' ), $plugin_data['Name'], $data['new_version'] ) )
                                ),
                                $data['new_version']
                            );
                            $support_required = sprintf( esc_html__( 'Automatic update is available during active support period, %1$s renew now %3$s or %2$s I\'ve already renewed support. %3$s', 'bookly' ),
                                '<a href="' . esc_url( array_key_exists( 'renew_support', $data ) ? $data['renew_support'] : 'https://codecanyon.net/user/ladela' ) . '" target="_blank">',
                                '<a href="#" data-bookly-plugin="' . $plugin_class::getRootNamespace() . '" data-csrf="' . BooklyLib\Utils\Common::getCsrfToken() . '">',
                                '</a>'
                            );

                            $update_manualy = sprintf( esc_html__( 'If you want to update the plugin manually, visit this %s page. %s', 'bookly' ),
                                '<a href="https://support.booking-wp-plugin.com/hc/en-us/articles/360010638333-How-to-update-Bookly-using-FTP" target="_blank">', '</a>'
                            );

                            echo '<tr class="plugin-update-tr active bookly-js-plugin">
                                <td colspan="3" class="plugin-update colspanchange">
                                    <div class="update-message notice inline notice-warning notice-alt">' . $new_version_info . '<p>' . $support_required . ' <span class="spinner" style="float: none; margin: -2px 0 0 2px"></span></p></div>
                                    <div class="update-message notice inline notice-error notice-alt"><p>' . $update_manualy . '</p></div>
                                </td>
                            </tr>';
                        } else {
                            unset( $bookly_update_plugins[ $slug ] );
                            set_site_transient( 'bookly_update_plugins', $bookly_update_plugins );
                        }
                    }
                }, 10, 3 );

                if ( $plugin_class::getSlug() === 'bookly-addon-pro' ) {
                    add_action( 'pre_current_active_plugins', function () {
                        $version   = Lib\Plugin::getVersion();
                        $resources = plugins_url( 'backend/components/license/resources', Lib\Plugin::getMainFile() );
                        wp_enqueue_script( 'bookly-plugins', $resources . '/js/plugins.js', array( 'jquery' ), $version );
                    } );
                }
            }
        }
    }

    /**
     * Init plugin update checker.
     *
     * @param BooklyLib\Base\Plugin $plugin_class
     */
    public static function initPluginUpdateChecker( $plugin_class )
    {
        include_once Lib\Plugin::getDirectory() . '/lib/utils/plugin-update-checker.php';

        $purchase_code = $plugin_class::getPurchaseCode();
        add_filter( 'puc_manual_check_link-' . $plugin_class::getSlug(), function () use ( $purchase_code ) {
            return $purchase_code != '' ? __( 'Check for updates', 'bookly' ) : '';
        }, 10, 1 );

        add_filter( 'puc_manual_check_message-' . $plugin_class::getSlug(), function ( $message, $status ) {
            switch ( $status ) {
                case 'no_update':        return __( 'This plugin is up to date.', 'bookly' );
                case 'update_available': return __( 'A new version of this plugin is available.', 'bookly' );
                default:                 return sprintf( __( 'Unknown update checker status "%s"', 'bookly' ), htmlentities( $status ) );
            }
        }, 10, 2 );

        add_filter( 'puc_request_info_result-' . $plugin_class::getSlug(), function ( $pluginInfo, $result ) use ( $plugin_class ) {
            if ( is_wp_error( $result ) ) {
                if ( get_option( 'bookly_api_server_error_time' ) == '0' ) {
                    update_option( 'bookly_api_server_error_time', current_time( 'timestamp' ) );
                }
            } elseif ( isset( $result['body'] ) ) {
                $response = json_decode( $result['body'], true );
                if ( isset( $response['options'] ) ) {
                    foreach ( $response['options'] as $option => $value ) {
                        $value !== null ? update_option( $option, $value ) : delete_option( $option );
                    }
                }
                update_option( 'bookly_api_server_error_time', '0' );
                if ( isset( $response['licensed'] ) && ! $response['licensed'] ) {
                    update_option( $plugin_class::getPurchaseCodeOption(), '' );
                }
                if ( isset( $response['limited'] ) ) {
                    $bookly_update_plugins = (array) get_site_transient( 'bookly_update_plugins' );
                    $bookly_update_plugins[ $plugin_class::getSlug() ] = $response['limited'];
                    set_site_transient( 'bookly_update_plugins', $bookly_update_plugins );
                }
            }

            return $pluginInfo;
        }, 10, 2 );

        add_filter( 'puc_request_info_query_args-' . $plugin_class::getSlug(), function( $queryArgs ) use ( $purchase_code ) {
            $queryArgs['site_url']      = site_url();
            $queryArgs['purchase_code'] = $purchase_code;
            $queryArgs['bookly']        = BooklyLib\Plugin::getVersion();
            $queryArgs['bookly-addon-pro'] = Lib\Plugin::getVersion();
            unset ( $queryArgs['checking_for_updates'] );

            return $queryArgs;
        }, 10, 1 );

        \PucFactory::buildUpdateChecker(
            BooklyLib\API::API_URL . '/1.1/plugins/' . $plugin_class::getSlug() . '/update',
            $plugin_class::getMainFile(),
            $plugin_class::getSlug(),
            24
        );

        if ( $purchase_code == '' ) {
            $plugin_basename = $plugin_class::getBasename();
            add_filter( 'plugin_row_meta', function ( $links, $plugin ) use ( $plugin_basename ) {
                if ( $plugin == $plugin_basename ) {
                    return array_merge(
                        $links,
                        array(
                            0 => '<span class="dashicons dashicons-info"></span> ' .
                                sprintf(
                                    __( 'To update - enter the <a href="%s">Purchase Code</a>', 'bookly' ),
                                    BooklyLib\Utils\Common::escAdminUrl( \Bookly\Backend\Modules\Settings\Page::pageSlug(), array( 'tab' => 'purchase_code' ) )
                                ),
                        )
                    );
                }

                return $links;
            }, 10, 2 );
        }
    }
}