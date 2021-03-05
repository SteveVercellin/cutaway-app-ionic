<?php
namespace Bookly\Backend\Modules\Staff;

use Bookly\Lib;
use Bookly\Backend\Modules\Staff\Forms\Widgets\TimeChoice;

/**
 * Class Page
 * @package Bookly\Backend\Modules\Staff
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritdoc
     */
    protected static function permissions()
    {
        $permissions = get_option( 'bookly_gen_allow_staff_edit_profile' ) ? array( '_default' => 'user' ) : array();
        if ( Lib\Config::staffCabinetActive() ) {
            $permissions = array( '_default' => 'user' );
        }

        return $permissions;
    }

    /**
     * Staff list
     */
    public static function getStaffList()
    {
        global $wpdb;

        $query = Lib\Entities\Staff::query( 's' );
        $total = $query->count();
        $query
            ->select( 's.id, s.category_id, s.full_name, s.visibility, s.position, email, phone, wpu.display_name AS wp_user' )
            ->tableJoin( $wpdb->users, 'wpu', 'wpu.ID = s.wp_user_id' )
            ->sortBy( 'position' );
        if ( ! Lib\Utils\Common::isCurrentUserAdmin() ) {
            $query->where( 's.wp_user_id', get_current_user_id() );
        }
        $filter = self::parameter( 'filter' );
        if ( $filter['archived'] ) {
            if ( $filter['visibility'] != '' ) {
                $query->whereRaw( 's.visibility = %s OR s.visibility = %s', array( $filter['visibility'], 'archive' ) );
            }
        } elseif ( $filter['visibility'] != '' ) {
            $query->where( 's.visibility', $filter['visibility'] );
        } else {
            $query->whereNot( 's.visibility', 'archive' );
        }
        $list = $query->fetchArray();

        update_user_meta( get_current_user_id(), 'bookly_filter_staff_list', $filter );

        wp_send_json_success( compact( 'list', 'total' ) );
    }

    /**
     * Update staff position.
     */
    public static function updateStaffPosition()
    {
        $staff_sorts = self::parameter( 'positions' );
        foreach ( $staff_sorts as $position => $id ) {
            $staff = new Lib\Entities\Staff();
            $staff->load( $id );
            $staff->setPosition( $position );
            $staff->save();
        }

        wp_send_json_success();
    }

    /**
     * 'Safely' remove staff (report if there are future appointments)
     */
    public static function removeStaff()
    {
        if ( Lib\Utils\Common::isCurrentUserAdmin() ) {
            $staff_ids = self::parameter( 'staff_ids', array() );
            if ( self::parameter( 'force_delete', false ) ) {
                foreach ( $staff_ids as $staff_id ) {
                    if ( $staff = Lib\Entities\Staff::find( $staff_id ) ) {
                        $staff->delete();
                    }
                }
                $total = Lib\Entities\Staff::query()->count();

                wp_send_json_success( compact( 'total' ) );
            } else {
                $appointment = Lib\Entities\Appointment::query( 'a' )
                    ->select( 'a.staff_id, MAX(a.start_date) AS start_date' )
                    ->leftJoin( 'CustomerAppointment', 'ca', 'ca.appointment_id = a.id' )
                    ->whereIn( 'a.staff_id', $staff_ids )
                    ->whereGt( 'a.start_date', current_time( 'mysql' ) )
                    ->whereIn( 'ca.status', Lib\Proxy\CustomStatuses::prepareBusyStatuses( array(
                        Lib\Entities\CustomerAppointment::STATUS_PENDING,
                        Lib\Entities\CustomerAppointment::STATUS_APPROVED,
                    ) ) )
                    ->limit( 1 )
                    ->fetchRow();

                $filter_url  = '';
                if ( $appointment['start_date'] ) {
                    $last_month = date_create( $appointment['start_date'] )->modify( 'last day of' )->format( 'Y-m-d' );
                    $action     = 'show_modal';
                    $filter_url = sprintf( '%s#staff=%d&appointment-date=%s-%s',
                        Lib\Utils\Common::escAdminUrl( \Bookly\Backend\Modules\Appointments\Ajax::pageSlug() ),
                        $appointment['staff_id'],
                        date_create( current_time( 'mysql' ) )->format( 'Y-m-d' ),
                        $last_month );
                    wp_send_json_error( compact( 'action', 'filter_url' ) );
                }
                $filter_url = Proxy\Shared::getAffectedAppointmentsFilter( $filter_url, $staff_ids );
                if ( $filter_url ) {
                    $action = 'show_modal';
                    wp_send_json_error( compact( 'action', 'filter_url' ) );
                } else {
                    $action = 'confirm';
                    wp_send_json_error( compact( 'action' ) );
                }
            }
        }

        wp_send_json_success();
    }

    /**
     * Update staff schedule.
     */
    public static function staffScheduleUpdate()
    {
        $form = new Forms\StaffSchedule();
        $form->bind( self::postParameters() );
        $form->save();

        Proxy\Shared::updateStaffSchedule( self::postParameters() );

        wp_send_json_success();
    }

     /**
     * Extend parent method to control access on staff member level.
     *
     * @param string $action
     * @return bool
     */
    protected static function hasAccess( $action )
    {
        if ( parent::hasAccess( $action ) ) {
            if ( ! Lib\Utils\Common::isCurrentUserAdmin() ) {
                $staff = new Lib\Entities\Staff();

                switch ( $action ) {
                    case 'getStaffList':
                        return $staff->loadBy( array( 'wp_user_id' => get_current_user_id() ) );
                    case 'staffScheduleUpdate':
                        if ( self::hasParameter( 'days' ) ) {
                            foreach ( self::parameter( 'days' ) as $id => $day_index ) {
                                $res_schedule = new Lib\Entities\StaffScheduleItem();
                                $res_schedule->load( $id );
                                $staff = new Lib\Entities\Staff();
                                $staff->load( $res_schedule->getStaffId() );
                                if ( $staff->getWpUserId() != get_current_user_id() ) {
                                    return false;
                                }
                            }
                        }
                        break;
                    case 'resetBreaks':
                        $parameter = self::parameter( 'breaks' );
                        if ( $parameter && isset( $parameter['staff_id'] ) ) {
                            $staff->load( $parameter['staff_id'] );
                        }
                        break;
                    default:
                        return false;
                }

                return $staff->getWpUserId() == get_current_user_id();
            }

            return true;
        }

        return false;
    }
}