<?php

namespace BooklyAdapter\Entities;

use \BooklyAdapter\Base;

use \BooklyAdapter\Classes\AppointmentGroup;
use \BooklyAdapter\Classes\StaffMeta;

class Staff extends Base
{
    private static $_instance = null;

    private function __construct()
    {

    }

    public function loadStaffBy($conditions)
    {
        if (!$this->checkBooklyActivated()) {
            return false;
        }

        $booklyStaff = new \Bookly\Lib\Entities\Staff();
        $loadBooklyStaff = $booklyStaff->loadBy($conditions);
        if ($loadBooklyStaff) {
            return $booklyStaff;
        }

        return false;
    }

    public function getStaffFromUser($userId)
    {
        if (empty($userId)) {
            return false;
        }

        $staff = $this->loadStaffBy(array(
            'wp_user_id' => $userId
        ));

        return $staff;
    }

    public function getStaffFromUserLogged()
    {
        $currentUser = wp_get_current_user();
        if (empty($currentUser->ID)) {
            return false;
        }

        return $this->getStaffFromUser($currentUser->ID);
    }

    public function getStaffIdFromUser($userId)
    {
        if (empty($userId)) {
            return 0;
        }

        $staff = $this->getStaffFromUser($userId);
        $staffId = !empty($staff) ? $staff->getId() : 0;

        return $staffId;
    }

    public function getStaffIdFromUserLogged()
    {
        $staff = $this->getStaffFromUserLogged();
        $staffId = !empty($staff) ? $staff->getId() : 0;

        return $staffId;
    }

    public function getTimeAvailableOfStaff($staff, $date)
    {
        if (!$this->checkBooklyRestActivated() || !function_exists('getStaffAvailableTimes')) {
            return array();
        }

        $timeAvailable = getStaffAvailableTimes($staff, $date);

        return $timeAvailable;
    }

    public function restoreTimeAvailableOfStaff($staff, $date, $timesAvailable)
    {
        if (!$this->checkBooklyRestActivated() || !function_exists('updateStaffAvailableTimes')) {
            return array();
        }

        return updateStaffAvailableTimes($staff, $date, $timesAvailable);
    }

    public function updateTimeAvailableOfStaff($staff, $date)
    {
        if (!$this->checkBooklyRestActivated()) {
            return false;
        }

        try {
            $booklyAppointmentGroup = AppointmentGroup::getInstance();
            $dateParse = explode('/', $date);

            $staffAppointments = $booklyAppointmentGroup->getStaffAppointmentsOnDate($staff, array(
                'day' => $dateParse[2],
                'month' => $dateParse[1],
                'year' => $dateParse[0]
            ));
            if ($staffAppointments !== false) {
                $staffSchedule = $this->getStaffSchedule($staff, $date);
                if (empty($staffSchedule)) {
                    return false;
                }

                $staffStartTime = $staffSchedule['start_time'];
                $staffEndTime = $staffSchedule['end_time'];

                if (!initStaffAvailableTimes($staff, $date, $staffStartTime, $staffEndTime)) {
                    return false;
                }

                if (!empty($staffAppointments)) {
                    $availableTimes = getStaffAvailableTimes($staff, $date);
                    foreach ($staffAppointments as $item) {
                        $availableTimes = calculateStaffAvailableTimes($availableTimes, $item['start_date'], $item['end_date']);
                    }
                    if (!updateStaffAvailableTimes($staff, $date, $availableTimes)) {
                        return false;
                    }
                }

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateStaff($dataUpdate)
    {
        if (!$this->checkBooklyActivated() || empty($dataUpdate)) {
            return false;
        }

        try {
            $booklyStaff = new \Bookly\Lib\Entities\Staff();
            if (!empty($dataUpdate['id'])) {
                $booklyStaff->load($dataUpdate['id']);
            }
            foreach ($dataUpdate as $key => $value) {
                $key = str_replace('_', ' ', $key);
                $key = ucfirst($key);
                $key = str_replace(' ', '', $key);
                $method = "set{$key}";

                if (method_exists($booklyStaff, $method)) {
                    $booklyStaff->$method($value);
                }
            }

            if ($booklyStaff->save() !== false) {
                return $booklyStaff->getId();
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getStaffsDetail($staffs)
    {
        if (!$this->checkBooklyActivated()) {
            return array();
        }

        $staffs = !is_array($staffs) ? array($staffs) : $staffs;

        $staff_query = \Bookly\Lib\Entities\Staff::query()
            ->select()
            ->sortBy('full_name');
        foreach($staffs as $s){
            $staff_query->where('id', $s, 'OR');
        }

        $detail = $staff_query->fetchArray();
        $detail = !empty($detail) ? $detail : array();

        return $detail;
    }

    public function getStaffSchedule($staff, $date)
    {
        if (!$this->checkBooklyActivated()) {
            return array();
        }

        $day_index = $this->getDayIndex($date);

        $staff_query = \Bookly\Lib\Entities\StaffScheduleItem::query()
            ->select('start_time as start_time, end_time as end_time, TIME_TO_SEC(TIMEDIFF(end_time, start_time))/60 as total_time')
            ->where('staff_id', $staff)
            ->where('day_index', $day_index);

        $staffSchedule = $staff_query->fetchRow();

        return !empty($staffSchedule) ? $staffSchedule : array();
    }

    public function checkStaffWorkTimeValid($staff, $date, $startTime, $endTime)
    {
        $staffSchedule = $this->getStaffSchedule($staff, $date);
        if (empty($staffSchedule)) {
            return false;
        }

        $staffStartTime = $staffSchedule['start_time'];
        $staffEndTime = $staffSchedule['end_time'];

        if (strlen($startTime) <= 8) {
            $startTime = strtotime($date . ' ' . $startTime);
        }
        if (strlen($endTime) <= 8) {
            $endTime = strtotime($date . ' ' . $endTime);
        }

        $startTimeInt = strtotime($startTime);
        $endTimeInt = strtotime($endTime);
        $staffStartTimeInt = strtotime($date . ' ' . $staffStartTime);
        $staffEndTimeInt = strtotime($date . ' ' . $staffEndTime);

        return $staffStartTimeInt <= $startTimeInt && $endTimeInt <= $staffEndTimeInt;
    }

    public function checkStaffWorkTimeAvailable($staff, $date, $startTime, $endTime)
    {
        if (!$this->checkBooklyRestActivated() || !function_exists('checkStaffCanBookOnTimeRange')) {
            return false;
        }

        return checkStaffCanBookOnTimeRange($staff, $date, $startTime, $endTime);
    }

    public function getStaffDashboardDataFromUserLogged()
    {
        $currentUser = wp_get_current_user();
        if (empty($currentUser->ID)) {
            return array();
        }

        return $this->getStaffDashboardDataFromUser($currentUser->ID);
    }

    public function getStaffDashboardDataFromUser($userId)
    {
        if (empty($userId)) {
            return array();
        }

        $staff = $this->getStaffFromUser($userId);
        if (empty($staff)) {
            return array();
        }

        $data = array(
            'report_price' => array(),
            'day_to_pay' => date('t') - date('j') . ' ' . __('giorni rimanenti', 'cutaway'),
            'book_available' => true
        );

        $staffMeta = new StaffMeta($staff->getId());
        $reportPrices = $staffMeta->getStaffMeta('_report_booking_price');
        $data['report_price'] = !empty($reportPrices) ? $reportPrices : array();

        $barberAvailableBook = $staff->getVisibility();
        $data['book_available'] = !empty($barberAvailableBook) && $barberAvailableBook == 'public' ? true : false;

        $staffRating = $this->getStaffRatingAvg( null, $staff->getId() );
        $data = array_merge( $data, array(
            'rating' => array(
                'star' => $staffRating['rating_count'],
                'percent' => $staffRating['rating_percent'],
            )
        ) );
        $data['rating']['review'] = $this->countStaffComment( null, $staff->getId() );

        return $data;
    }

    public function getStaffRatingAvg( $service = null, $staffId = 0 )
    {
        $default = array(
            'rating_count' => 0,
            'rating_percent' => 0,
        );

        if ( !$this->checkBooklyRatingAddonActivated() ) {
            return $default;
        }

        if ( empty( $staffId ) ) {
            $currentUser = wp_get_current_user();
            if ( !empty( $currentUser->ID ) ) {
                $staff = $this->getStaffFromUser( $currentUser->ID );
                $staffId = !empty($staff) ? $staff->getId() : 0;
            }
        }

        if ( empty( $staffId ) ) {
            return $default;
        }

        $rating = \BooklyRatings\Lib\Utils\Common::calculateStaffRating( $staffId, $service );
        if ( is_wp_error( $rating ) ) {
            return $default;
        }

        $rating_count = ( int ) $rating;
        $rating_percent = round( ( $rating_count / 5 ) * 100, 0 );

        return compact( 'rating_count', 'rating_percent' );
    }

    public function countStaffComment( $serviceId = null, $staffId = 0 )
    {
        $count = 0;

        if (!$this->checkBooklyActivated()) {
            return $count;
        }

        if ( empty( $staffId ) ) {
            $currentUser = wp_get_current_user();
            if ( !empty( $currentUser->ID ) ) {
                $staff = $this->getStaffFromUser( $currentUser->ID );
                $staffId = !empty($staff) ? $staff->getId() : 0;
            }
        }

        if ( empty( $staffId ) ) {
            return $count;
        }

        $query = \Bookly\Lib\Entities\CustomerAppointment::query( 'ca' )
            ->select( 'COUNT(ca.rating_comment) as comments' )
            ->leftJoin( 'Appointment', 'a', 'a.id = ca.appointment_id' )
            ->where( 'a.staff_id', $staffId )
            ->whereGte( 'a.start_date', date_create( current_time( 'mysql' ) )->modify( sprintf( '- %s days', get_option( 'bookly_ratings_period', 365 ) ) )->format( 'Y-m-d H:i:s' ) )
            ->whereNot( 'ca.rating_comment', null );
        if ( $serviceId ) {
            $service = \Bookly\Lib\Entities\Service::find( $serviceId );
            if ( $service->withSubServices() ) {
                $sub_services = \Bookly\Lib\Entities\Service::query( 's' )
                    ->innerJoin( 'SubService', 'ss', 'ss.sub_service_id = s.id' )
                    ->where( 'ss.service_id', $serviceId )
                    ->where( 'ss.type', \Bookly\Lib\Entities\SubService::TYPE_SERVICE )
                    ->fetchCol( 's.id' );
                $query->whereIn( 'a.service_id', $sub_services );
            } else {
                $query->where( 'a.service_id', $serviceId );
            }
        }

		$row = $query->fetchRow();

        return !empty( $row['comments'] ) ? $row['comments'] : $count;
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new Staff();
        }

        return self::$_instance;
    }

    private function debugAvailableTimes($availableTimes)
    {
        foreach ($availableTimes as $begin => $end) {
            var_dump(date("Y-m-d h:i a", $begin));
            var_dump(date("Y-m-d h:i a", $end));
        }
    }
}