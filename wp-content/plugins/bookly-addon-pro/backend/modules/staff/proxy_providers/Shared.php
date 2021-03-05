<?php
namespace BooklyPro\Backend\Modules\Staff\ProxyProviders;

use Bookly\Backend\Modules\Staff\Proxy;
use Bookly\Lib as BooklyLib;
use Bookly\Lib\Entities\Staff;
use BooklyPro\Lib\Config;
use BooklyPro\Lib\Google;

/**
 * Class Shared
 * @package BooklyPro\Backend\Modules\Staff\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function editStaff( array $data, Staff $staff )
    {
        if ( $gc_errors = BooklyLib\Session::get( 'staff_google_auth_error' ) ) {
            foreach ( (array) json_decode( $gc_errors, true ) as $error ) {
                $data['alert']['error'][] = $error;
            }
            BooklyLib\Session::destroy( 'staff_google_auth_error' );
        }

        $auth_url           = null;
        $google_calendars   = array();
        $google_calendar_id = null;
        if ( $staff->getGoogleData() == '' ) {
            if ( Config::getGoogleCalendarSyncMode() !== null ) {
                $google  = new Google\Client();
                $auth_url = $google->createAuthUrl( $staff->getId() );
            } else {
                $auth_url = false;
            }
        } else {
            $google = new Google\Client();
            if ( $google->auth( $staff, true ) && ( $list = $google->getCalendarList() ) !== false ) {
                $google_calendars   = $list;
                $google_calendar_id = $google->data()->calendar->id;
            } else {
                foreach ( $google->getErrors() as $error ) {
                    $data['alert']['error'][] = $error;
                }
            }
        }

        $data['tpl']['gc'] = compact( 'staff', 'auth_url', 'google_calendars', 'google_calendar_id' );

        return $data;
    }

    /**
     * @inheritdoc
     */
    public static function renderStaffPage( $params )
    {
        // Check if this request is the request after google auth, set the token-data to the staff.
        if ( isset ( $params['code'] ) ) {
            $google = new Google\Client();
            $token  = $google->exchangeCodeForAccessToken( $params[ 'code'] );

            if ( $token ) {
                $staff_id = (int) base64_decode( strtr( $params['state'], '-_,', '+/=' ) );
                $staff = new Staff();
                $staff->load( $staff_id );
                $staff
                    ->setGoogleData( json_encode( array(
                        'token'    => $token,
                        'calendar' => array( 'id' => null, 'sync_token' => null ),
                        'channel'  => array( 'id' => null, 'resource_id' => null, 'expiration' => null ),
                    ) ) )
                    ->save()
                ;

                exit ( sprintf( '<script>location.href="%s";</script>', admin_url( 'admin.php?page=' . self::pageSlug() . '&staff_id=' . $staff_id ) ) );
            } else {
                BooklyLib\Session::set( 'staff_google_auth_error', json_encode( $google->getErrors() ) );
            }
        }

        return $params;
    }

    /**
     * @inheritdoc
     */
    public static function preUpdateStaff( Staff $staff, array $params )
    {
        if ( array_key_exists( 'google_disconnect', $params ) && $params['google_disconnect'] == '1' ) {
            $google = new Google\Client();
            if ( $google->auth( $staff ) ) {
                if ( BooklyLib\Config::advancedGoogleCalendarActive() ) {
                    $google->calendar()->stopWatching( false );
                }
                $google->revokeToken();
            }
            $staff->setGoogleData( null );
        } elseif ( isset ( $params['google_calendar_id'] ) ) {
            $calendar_id = $params['google_calendar_id'];
            $google      = new Google\Client();
            $update_google_data = false;
            if ( $google->auth( $staff, true ) ) {
                if ( ( $staff->getVisibility() === 'archive' )
                    && ( $params['visibility'] !== 'archive' )
                    && $google->calendar()->clearSyncToken()->sync()
                ) {
                    // Change visibility from archive
                    if ( BooklyLib\Config::advancedGoogleCalendarActive() ) {
                        $google->calendar()->watch();
                        $update_google_data = true;
                    }
                } elseif ( ( $staff->getVisibility() !== 'archive' )
                    && ( $params['visibility'] === 'archive' )
                ) {
                    // Change visibility to archive
                    if ( BooklyLib\Config::advancedGoogleCalendarActive() ) {
                        $google->calendar()->clearSyncToken();
                        $update_google_data = true;
                    }
                } elseif ( $calendar_id !== $google->data()->calendar->id ) {
                    // Calendar changed
                    if ( $staff->getVisibility() === 'archive' ) {
                        wp_send_json_error( array( 'error' => __( 'Can\'t change calendar for archived staff', 'bookly' ) ) );
                    } elseif ( $calendar_id != '' ) {
                        if ( ! $google->validateCalendarId( $calendar_id ) ) {
                            wp_send_json_error( array( 'error' => implode( '<br>', $google->getErrors() ) ) );
                        }
                    } else {
                        $calendar_id = null;
                    }
                    if ( BooklyLib\Config::advancedGoogleCalendarActive() ) {
                        $google->calendar()->clearSyncToken()->stopWatching( false );
                    }
                    $google->data()->calendar->id = $calendar_id;
                    $update_google_data = true;
                }
                if ( $update_google_data ) {
                    $google_data = $google->data();
                    $staff->setGoogleData( $google_data->toJson() );
                }
            }
        }
    }
}