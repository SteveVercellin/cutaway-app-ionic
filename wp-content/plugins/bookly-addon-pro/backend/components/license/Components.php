<?php
namespace BooklyPro\Backend\Components\License;

use Bookly\Lib as BooklyLib;
use Bookly\Lib\Utils\Common;
use Bookly\Backend\Modules\Settings;
use BooklyPro\Lib;

/**
 * Class Components
 * @package BooklyPro\Backend\Components\License
 */
class Components extends BooklyLib\Base\Component
{
    /**
     * Render license required form.
     *
     * @param bool $bookly_page
     */
    public static function renderLicenseRequired( $bookly_page )
    {
        if ( $bookly_page && ( Lib\Config::graceExpired() || get_user_meta( get_current_user_id(), 'bookly_grace_hide_admin_notice_time', true ) < time() ) ) {
            $remaining_days = Lib\Config::graceRemainingDays();
            if ( $remaining_days !== false ) {
                $role = Common::isCurrentUserAdmin() ? 'admin' : 'staff';
                if ( $remaining_days > 0 ) {
                    // Grace has started.
                    self::_enqueueAssets();
                    $days_text = array( '{days}' => sprintf( _n( '%d day', '%d days', $remaining_days, 'bookly' ), $remaining_days ) );
                    self::renderTemplate( 'board', array(
                        'board_body' => self::renderTemplate( $role . '_grace', compact( 'days_text' ), false )
                    ) );
                } else {
                    // Grace expired.
                    self::_enqueueAssets();
                    self::renderTemplate( 'board', array(
                        'board_body' => self::renderTemplate( $role . '_grace_ended', array(), false )
                    ) );
                }
            }
        }
    }

    /**
     * Render license notice.
     *
     * @param bool $bookly_page
     */
    public static function renderLicenseNotice( $bookly_page )
    {
        // Checking if notice is 'rendered' in the current request
        if ( ! self::hasInCache( __FUNCTION__ ) ) {
            if ( ! $bookly_page && get_user_meta( get_current_user_id(), 'bookly_grace_hide_admin_notice_time', true ) < time() ) {
                $remaining_days = Lib\Config::graceRemainingDays();
                if ( $remaining_days !== false ) {
                    $role = Common::isCurrentUserAdmin() ? 'admin' : 'staff';
                    if ( $remaining_days > 0 ) {
                        self::_enqueueAssets();
                        $replace_data = array(
                            '{url}'  => Common::escAdminUrl( Settings\Page::pageSlug(), array( 'tab' => 'purchase_code' ) ),
                            '{days}' => sprintf( _n( '%d day', '%d days', $remaining_days, 'bookly' ), $remaining_days ),
                        );
                        self::renderTemplate( $role . '_notice_grace', compact( 'replace_data' ) );
                    } else {
                        self::_enqueueAssets();
                        $replace_data = array(
                            '{url}' => Common::escAdminUrl( Settings\Page::pageSlug(), array( 'tab' => 'purchase_code' ) ),
                        );
                        self::renderTemplate( $role . '_notice_grace_ended', compact( 'replace_data' ) );
                    }
                }
            }
        }
        self::putInCache( __FUNCTION__, 'rendered' );
    }

    /**
     * Render purchase reminder.
     *
     * @param bool $bookly_page
     */
    public static function renderPurchaseReminder( $bookly_page )
    {
        if ( $bookly_page && get_user_meta( get_current_user_id(), 'bookly_show_purchase_reminder' ) ) {
            self::renderTemplate( 'purchase_reminder' );
        }
    }

    /**
     * Enqueue assets.
     */
    private static function _enqueueAssets()
    {
        self::enqueueStyles( array(
            'bookly' => array(
                'backend/resources/bootstrap/css/bootstrap-theme.min.css',
                'frontend/resources/css/ladda.min.css',
            ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/license.js' => array( 'jquery' ), ),
            'bookly' => array(
                'backend/resources/js/alert.js' => array( 'jquery' ),
                'backend/resources/bootstrap/js/bootstrap.min.js' => array( 'jquery' ),
                'frontend/resources/js/spin.min.js' => array( 'jquery' ),
                'frontend/resources/js/ladda.min.js' => array( 'jquery' ),
            ),
        ) );

        wp_localize_script( 'bookly-license.js', 'LicenseL10n', array(
            'csrfToken' => Common::getCsrfToken(),
            'ajaxurl'   => admin_url( 'admin-ajax.php' ),
        ) );
    }
}