<?php

namespace BooklyAdapter\Entities;

use \BooklyAdapter\Base;

class Appointment extends Base
{
    private static $_instance = null;

    private function __construct()
    {

    }

    public function getAppointmentDetail($appointment)
    {
        if (!$this->checkBooklyActivated() || empty($appointment)) {
            return array();
        }

        $query = \Bookly\Lib\Entities\Appointment::query('ba')
        ->select('st.full_name as barber_name, se.title as service_name, se.duration as service_time, se.price as service_price, ba.start_date, ba.end_date')
        ->where('ba.id', $appointment)
        ->innerJoin('Staff', 'st', 'ba.staff_id = st.id')
        ->innerJoin('Service', 'se', 'ba.service_id = se.id');

        $app = $query->fetchRow();

        return $app;
    }

    public function getAppointment($appointment)
    {
        if (!$this->checkBooklyActivated() || empty($appointment)) {
            return array();
        }

        $query = \Bookly\Lib\Entities\Appointment::query()
        ->select()
        ->where('id', $appointment);

        $app = $query->fetchRow();

        return $app;
    }

    public function createAppointment($appointmentData, $customers)
    {
        if (!$this->checkBooklyActivated()) {
            return 0;
        }

        $appointment = new \Bookly\Lib\Entities\Appointment();
        $booklyServiceAdapter = Service::getInstance();
        list($customers, $maxExtraDuration) = $this->_preProcessCustomers($customers);

        extract($appointmentData);

        $service = $booklyServiceAdapter->loadServiceBy(array(
            'id' => $service_id
        ));
        $end_date = $booklyServiceAdapter->calculateEndDate($service, $start_date);

        $appointment
            ->setLocationId($location_id)
            ->setStaffId($staff_id)
            ->setServiceId($service_id)
            ->setStartDate($start_date)
            ->setEndDate($end_date)
            ->setInternalNote($note)
            ->setExtrasDuration($maxExtraDuration)
        ;
        if ($appointment->save() !== false) {
            $ca = $appointment->saveCustomerAppointments($customers);
            // Google Calendar.
            if ( class_exists( '\Bookly\Lib\Proxy\Pro' ) ) {
                \Bookly\Lib\Proxy\Pro::syncGoogleCalendarEvent( $appointment );
            }
            // Outlook Calendar.
            if ( class_exists( '\Bookly\Lib\Proxy\OutlookCalendar' ) ) {
                \Bookly\Lib\Proxy\OutlookCalendar::syncEvent( $appointment );
            }

            $ca_list = $appointment->getCustomerAppointments( true );
            foreach ( $ca_list as $ca ) {
                \Bookly\Lib\Notifications\Booking\Sender::sendForCA( $ca, $appointment, array(), true );
            }

            return $appointment->getId();
        }

        return 0;
    }

    public function updateAppointment($appointmentData, $customers)
    {
        if (!$this->checkBooklyActivated()) {
            return 0;
        }

        $appointment = new \Bookly\Lib\Entities\Appointment();
        $appointment->load($appointmentData['id']);
        list($customers, $maxExtraDuration) = $this->_preProcessCustomers($customers);
        extract($appointmentData);

        if (!empty($location_id)) {
            $appointment->setLocationId($location_id);
        }
        if (!empty($staff_id)) {
            $appointment->setStaffId($staff_id);
        }
        if (!empty($service_id)) {
            $appointment->setServiceId($service_id);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $appointment->setStartDate($start_date);
            $appointment->setEndDate($end_date);
        }
        if (!empty($note)) {
            $appointment->setInternalNote($note);
        }
        $appointment->setExtrasDuration($maxExtraDuration);

        if ($appointment->save() !== false) {
            $ca = $appointment->saveCustomerAppointments($customers);
            // Google Calendar.
            if ( class_exists( '\Bookly\Lib\Proxy\Pro' ) ) {
                \Bookly\Lib\Proxy\Pro::syncGoogleCalendarEvent( $appointment );
            }
            // Outlook Calendar.
            if ( class_exists( '\Bookly\Lib\Proxy\OutlookCalendar' ) ) {
                \Bookly\Lib\Proxy\OutlookCalendar::syncEvent( $appointment );
            }

            $ca_list = $appointment->getCustomerAppointments( true );
            foreach ( $ca_list as $ca ) {
                \Bookly\Lib\Notifications\Booking\Sender::sendForCA( $ca, $appointment, array(), true );
            }

            return $appointment->getId();
        }

        return 0;
    }

    public function deleteAppointment($appointmentId)
    {
        if (!$this->checkBooklyActivated() || empty($appointmentId)) {
            return false;
        }

        $appointment = new \Bookly\Lib\Entities\Appointment();
        if (!$appointment->load($appointmentId)) {
            return false;
        }

        return $appointment->delete();
    }

    public function loadAppointmentReviewData($appointment)
    {
        if (!$this->checkBooklyActivated() || empty($appointment)) {
            return array();
        }

        $query = \Bookly\Lib\Entities\Appointment::query('ba')
        ->select('ba.service_id, se.title as service_name, se.price as service_price, se.duration as service_time, ca.token, ca.rating, ca.rating_comment')
        ->where('ba.id', $appointment)
        ->innerJoin('CustomerAppointment', 'ca', 'ca.appointment_id = ba.id')
        ->innerJoin('Service', 'se', 'ba.service_id = se.id');

        $app = $query->fetchRow();

        return $app;
    }

    private function _preProcessCustomers($customers)
    {
        $maxExtrasDuration = 0;
        foreach ( $customers as $i => $customer ) {
            if ( in_array( $customer['status'], \Bookly\Lib\Proxy\CustomStatuses::prepareBusyStatuses( array(
                \Bookly\Lib\Entities\CustomerAppointment::STATUS_PENDING,
                \Bookly\Lib\Entities\CustomerAppointment::STATUS_APPROVED
            ) ) ) ) {
                $extrasDuration = \Bookly\Lib\Proxy\ServiceExtras::getTotalDuration( $customer['extras'] );
                if ( $extrasDuration > $maxExtrasDuration ) {
                    $maxExtrasDuration = $extrasDuration;
                }
            }
            $customers[ $i ]['created_from'] = 'backend';
        }

        return array(
            $customers,
            $maxExtrasDuration
        );
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new Appointment();
        }

        return self::$_instance;
    }
}