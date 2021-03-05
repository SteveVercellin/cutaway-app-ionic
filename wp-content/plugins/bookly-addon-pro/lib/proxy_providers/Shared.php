<?php
namespace BooklyPro\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Lib\CartInfo;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Entities\Payment;
use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Utils\Common;
use BooklyPro\Backend\Components\License;
use BooklyPro\Frontend\Modules\Paypal;
use BooklyPro\Lib\Config;

/**
 * Class Shared
 * @package BooklyPro\Lib\ProxyProviders
 */
class Shared extends BooklyLib\Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function applyGateway( CartInfo $cart_info, $gateway )
    {
        if ( $gateway === Payment::TYPE_PAYPAL && BooklyLib\Config::paypalEnabled() ) {
            $cart_info->setGateway( $gateway );
        }

        return $cart_info;
    }

    /**
     * @inheritdoc
     */
    public static function doDailyRoutine()
    {
        // Grace routine.
        $remaining_days = Config::graceRemainingDays();
        if ( $remaining_days !== false ) {
            $today               = (int) ( current_time( 'timestamp' ) / DAY_IN_SECONDS );
            $grace_notifications = get_option( 'bookly_grace_notifications' );
            if ( $today != $grace_notifications['sent'] ) {
                $admin_emails = Common::getAdminEmails();
                if ( ! empty ( $admin_emails ) ) {
                    $grace_notifications['sent'] = $today;
                    if ( $remaining_days === 0 && ( $grace_notifications['bookly'] != 1 ) ) {
                        $subject = __( 'Please verify your Bookly Pro license', 'bookly' );
                        $message = __( 'Bookly Pro will need to verify your license to restore access to your bookings. Please enter the purchase code in the administrative panel.', 'bookly' );
                        if ( wp_mail( $admin_emails, $subject, $message ) ) {
                            $grace_notifications['bookly'] = 1;
                            update_option( 'bookly_grace_notifications', $grace_notifications );
                        }
                    } else if ( in_array( $remaining_days, array( 13, 7, 1 ) ) ) {
                        $days_text = sprintf( _n( '%d day', '%d days', $remaining_days, 'bookly' ), $remaining_days );
                        $replace   = array( '{days}' => $days_text );
                        $subject   = __( 'Please verify your Bookly Pro license', 'bookly' );
                        $message   = strtr( __( 'Please verify Bookly Pro license in the administrative panel. If you do not verify the license within {days}, access to your bookings will be disabled.', 'bookly' ), $replace );
                        if ( wp_mail( $admin_emails, $subject, $message ) ) {
                            update_option( 'bookly_grace_notifications', $grace_notifications );
                        }
                    }
                }
            }
        }

        if ( get_option( 'bookly_pr_show_time' ) < time() ) {
            update_option( 'bookly_pr_show_time', time() + 7776000 );
            if ( get_option( 'bookly_pro_envato_purchase_code' ) == '' ) {
                foreach ( get_users( array( 'role' => 'administrator' ) ) as $admin ) {
                    update_user_meta( $admin->ID, 'bookly_show_purchase_reminder', '1' );
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function doHourlyRoutine()
    {
        // Mark unpaid appointments as rejected.
        $payments = BooklyLib\Proxy\Shared::getOutdatedUnpaidPayments( array() );
        if ( $payments ) {
            $ca_ids = array();
            foreach ( $payments as $payment_details ) {
                $details = json_decode( $payment_details, true );
                if ( isset( $details['items'] ) && is_array( $details['items'] ) ) {
                    $ca_ids = array_merge( $ca_ids, array_map( function ( $item ) { return $item['ca_id']; }, $details['items'] ) );
                }
            }
            Payment::query()
                ->update()
                ->set( 'status', Payment::STATUS_REJECTED )
                ->whereIn( 'id', array_keys( $payments ) )
                ->execute();
            if ( ! empty ( $ca_ids ) ) {
                CustomerAppointment::query()
                    ->update()
                    ->set( 'status', CustomerAppointment::STATUS_REJECTED )
                    ->set( 'status_changed_at', current_time( 'mysql' ) )
                    ->whereIn( 'id', $ca_ids )
                    ->execute();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function handleRequestAction( $action )
    {
        switch ( $action ) {
            // PayPal Express Checkout.
            case 'paypal-ec-init':
                Paypal\Controller::ecInit();
                break;
            case 'paypal-ec-return':
                Paypal\Controller::ecReturn();
                break;
            case 'paypal-ec-cancel':
                Paypal\Controller::ecCancel();
                break;
            case 'paypal-ec-error':
                Paypal\Controller::ecError();
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public static function renderAdminNotices( $bookly_page )
    {
        License\Components::renderLicenseRequired( $bookly_page );
        License\Components::renderLicenseNotice( $bookly_page );
        License\Components::renderPurchaseReminder( $bookly_page );
    }

    /**
     * @inheritdoc
     */
    public static function showPaymentSpecificPrices( $show )
    {
        if ( ! $show && BooklyLib\Config::paypalEnabled() ) {
            return (float) get_option( 'bookly_paypal_increase' ) != 0 || (float) get_option( 'bookly_paypal_addition' ) != 0;
        }

        return $show;
    }

    /**
     * @inheritdoc
     */
    public static function prepareCaSeStQuery( BooklyLib\Query $query )
    {
        if ( ! BooklyLib\Config::customerGroupsActive() ) {
            $query->where( 's.visibility', BooklyLib\Entities\Service::VISIBILITY_PUBLIC );
        }

        return $query;
    }

    /**
     * @inheritdoc
     */
    public static function prepareStaffServiceQuery( BooklyLib\Query $query )
    {
        $query
            ->addSelect( 'spo.position' )
            ->leftJoin( 'StaffPreferenceOrder', 'spo', 'spo.service_id = ss.service_id AND spo.staff_id = ss.staff_id', '\BooklyPro\Lib\Entities' );

        return $query;
    }

    /**
     * @inheritdoc
     */
    public static function prepareStatement( $value, $statement, $table )
    {
        $tables = array( 'Service', 'Staff' );
        $key    = $table . '-' . $statement;
        if ( in_array( $table, $tables ) ) {
            if ( ! self::hasInCache( $key ) ) {
                preg_match( '/(?:(\w+)\()?\W*(?:(\w+)\.(\w+)|(\w+))/', $statement, $match );

                $count = count( $match );
                if ( $count == 4 ) {
                    $field = $match[3];
                } elseif ( $count == 5 ) {
                    $field = $match[4];
                }

                switch ( $field ) {
                    case 'category_id':
                    case 'padding_left':
                    case 'padding_right':
                    case 'staff_preference':
                    case 'staff_preference_settings':
                        self::putInCache( $key, $statement );
                        break;
                }
            }
        } else {
            self::putInCache( $key, $value );
        }

        return self::getFromCache( $key );
    }

    /**
     * @inheritdoc
     */
    public static function prepareNotificationTypes( array $types, $gateway )
    {
        if ( $gateway == 'email' ) {
            $types[] = Notification::TYPE_APPOINTMENT_REMINDER;
            $types[] = Notification::TYPE_LAST_CUSTOMER_APPOINTMENT;
            $types[] = Notification::TYPE_STAFF_DAY_AGENDA;
        }
        $types[] = Notification::TYPE_NEW_BOOKING_COMBINED;
        $types[] = Notification::TYPE_CUSTOMER_BIRTHDAY;
        $types[] = Notification::TYPE_CUSTOMER_NEW_WP_USER;

        return $types;
    }
}