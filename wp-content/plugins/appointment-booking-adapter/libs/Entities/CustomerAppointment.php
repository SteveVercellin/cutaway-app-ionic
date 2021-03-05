<?php

namespace BooklyAdapter\Entities;

use \BooklyAdapter\Base;

class CustomerAppointment extends Base
{
    private static $_instance = null;

    private function __construct()
    {

    }

    public function loadCustomerAppointmentData(
        $customer,
        $status = 'pending',
        $note = '',
        $appointmentId = 0
    )
    {
        if (!$this->checkBooklyActivated()) {
            return array();
        }

        $data = array(
            'id' => $customer,
            'ca_id' => null,
            'custom_fields' => array(),
            'extras' => array(),
            'number_of_persons' => 1,
            'notes' => $note,
            'status' => $status,
            'payment_id' => null,
            'extras_consider_duration' => 1,
        );

        if (!empty($appointmentId)) {
            $booklyCustomerAppointment = $this->loadCustomerAppointmentBy(array(
                'customer_id' => $customer,
                'appointment_id' => $appointmentId
            ));

            if ($booklyCustomerAppointment) {
                $data['ca_id'] = $booklyCustomerAppointment->getId();
            }
        }

        return $data;
    }

    public function saveCustomerReviewData(
        $token,
        $rating,
        $comment
    )
    {
        if (!$this->checkBooklyActivated()) {
            return false;
        }

        if ( empty( $token ) || empty( $rating ) ) {
            return false;
        }

        $booklyCustomerAdapter = Customer::getInstance();
        $customerId = $booklyCustomerAdapter->getCustomerIdFromUserLogged();

        $ca = new \Bookly\Lib\Entities\CustomerAppointment();
        $ca->loadBy( array( 'token' => $token ) );
        $appointment = \Bookly\Lib\Entities\Appointment::find( $ca->getAppointmentId() );
        if (
            strtotime( $appointment->getEndDate() ) > current_time( 'timestamp' ) - (int) get_option( 'bookly_ratings_timeout' ) * DAY_IN_SECONDS &&
            $customerId == $ca->getCustomerId()
        ) {
            $ca->setRating( $rating )
                ->setRatingComment( $comment ?: null )
                ->save();

            return true;
        }

        return false;
    }

    public function deleteCustomerReviewData( $token )
    {
        if (!$this->checkBooklyActivated()) {
            return false;
        }

        if ( empty( $token ) ) {
            return false;
        }

        $booklyCustomerAdapter = Customer::getInstance();
        $customerId = $booklyCustomerAdapter->getCustomerIdFromUserLogged();

        $ca = new \Bookly\Lib\Entities\CustomerAppointment();
        $ca->loadBy( array( 'token' => $token ) );
        $appointment = \Bookly\Lib\Entities\Appointment::find( $ca->getAppointmentId() );
        if (
            strtotime( $appointment->getEndDate() ) > current_time( 'timestamp' ) - (int) get_option( 'bookly_ratings_timeout' ) * DAY_IN_SECONDS &&
            $customerId == $ca->getCustomerId()
        ) {
            $ca->setRating( null )
                ->setRatingComment( null )
                ->save();

            return true;
        }

        return false;
    }

    public function loadReviewsFromStaffAndServices(
        $staff,
        $services
    )
    {
        $reviews = array();

        if (!$this->checkBooklyActivated()) {
            return $reviews;
        }

        if ( empty( $staff ) || empty( $services ) ) {
            return $reviews;
        }

        if ( !is_array( $services ) ) {
            $services = array( $services );
        }

        $query = \Bookly\Lib\Entities\Appointment::query('ba')
        ->select('cus.full_name AS name, cus.wp_user_id, ca.rating AS rating_count, ca.rating_comment AS review')
        ->where('ba.staff_id', $staff)
        ->whereIn('ba.service_id', $services)
        ->whereRaw('ca.rating IS NOT NULL', array())
        ->innerJoin('CustomerAppointment', 'ca', 'ca.appointment_id = ba.id')
        ->innerJoin('Customer', 'cus', 'ca.customer_id = cus.id');

        $reviews = $query->fetchArray();

        foreach ( $reviews as $index => $review ) {
            if ( !empty( $review['wp_user_id'] ) ) {
                $userAvatarId = get_user_meta( $review['wp_user_id'], 'cutaway_user_avatar', true );
                if ( !empty( $userAvatarId ) ) {
                    $userAvatar = wp_get_attachment_image_src( $userAvatarId );
                    if ( !empty( $userAvatar[0] ) ) {
                        $reviews[$index]['avatar'] = $userAvatar[0];
                    }
                }
            }

            $reviews[$index]['review'] = "<p>{$reviews[$index]['review']}</p>";
            $reviews[$index]['rating_percent'] = round( ( $reviews[$index]['rating_count'] / 5 ) * 100 , 0 );
            unset( $reviews[$index]['wp_user_id'] );
        }

        return $reviews;
    }

    public function loadCustomerAppointmentBy($conditions)
    {
        if (!$this->checkBooklyActivated()) {
            return false;
        }

        $booklyCustomerAppointment = new \Bookly\Lib\Entities\CustomerAppointment();
        $loadBooklyCustomerAppoinment = $booklyCustomerAppointment->loadBy($conditions);
        if ($loadBooklyCustomerAppoinment) {
            return $booklyCustomerAppointment;
        }

        return false;
    }

    public function refundCustomerAppointment( $appId )
    {
        $result = false;

        if (!$this->checkBooklyActivated()) {
            return $result;
        }

        $booklyCusApp = $this->loadCustomerAppointmentBy( array(
            'appointment_id' => $appId,
        ) );
        if ( empty( $booklyCusApp ) ) {
            return $result;
        }

        $booklyCusApp->cancel();

        return true;
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new CustomerAppointment();
        }

        return self::$_instance;
    }
}