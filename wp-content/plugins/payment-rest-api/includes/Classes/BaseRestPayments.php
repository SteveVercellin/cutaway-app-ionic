<?php

namespace Includes\Classes;

use \WP_Error;
use \WP_REST_Server;
use \Exception;

class BaseRestPayments
{
    protected $prefix = 'rest-payments';
    protected $restVersion = 'v1';
    protected $textDomain = 'payment-functions-rest';

    protected $needValidateNonce = false;
    protected $debugOptionName = '_base_payment_debug';

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerBaseRestFunctions'));
    }

    public function registerBaseRestFunctions()
    {
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/get-nonce-action/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'getNonce'),
            'permission_callback' => array( $this, 'get_items_permissions_check' )
        ));

        $restService = $this->getRestServiceCreatePendingBooking();
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/create-pending-booking/', array(
            'methods' => $restService['method'],
            'callback' => array($this, 'createPendingBooking'),
            'permission_callback' => array( $this, 'get_items_permissions_check' )
        ));
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/test-create-pending-booking/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'testCreatePendingBooking')
        ));
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/test-complete-booking/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'testCompleteBooking')
        ));
    }

    public function getRestServiceCreatePendingBooking()
    {
        return array(
            'method' => WP_REST_Server::CREATABLE,
            'url' => "{$this->prefix}/{$this->restVersion}" . '/create-pending-booking/'
        );
    }

    public function testCreatePendingBooking($request)
    {
        header("Content-Type: text/plain");

        echo 'ok';
    }

    public function testCompleteBooking($request)
    {
        header("Content-Type: text/plain");

        echo 'ok';
    }

    public function getNonce($request)
    {
        $method = $request['method'];
        $nonce = $this->generateNonce($method);

        if (is_wp_error($nonce)) {
            /*$nonce->add_data(array(
                'status' => 403
            ), 'get_nonce_fail');

            return $nonce;*/
            return wordpress_rest_format_response_fail( 'get_nonce_fail' );
        }

        /*return rest_ensure_response(array(
            'nonce' => $nonce
        ));*/
        return wordpress_rest_format_response_success( $nonce );
    }

    protected function validateRequest($request, $method)
    {
        $requestNonce = $request->get_param('nonce');

        if (empty($requestNonce) || empty($method)) {
            return false;
        }

        $action =  "{$this->prefix}/{$method}";

        return wp_verify_nonce($requestNonce, $action);
    }

    public function createPendingBooking($request)
    {
        if ($this->needValidateNonce && !$this->validateRequest($request, 'create-pending-booking')) {
            $this->logDebug('validate request fail');
            /*return new WP_Error('validate_fail', 'validate_fail',
            array(
                'status' => 403
            ));*/
            return wordpress_rest_format_response_fail( 'validate_fail' );
        }

        $data = $this->getDataPendingBook($request);
        $dataReturn = $this->processCreatePendingBooking($data);
        if (is_wp_error($dataReturn)) {
            //return $dataReturn;
            $error = $dataReturn->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }

        //return rest_ensure_response($dataReturn);
        return wordpress_rest_format_response_success( $dataReturn );
    }

    public function processCreatePendingBooking($data)
    {
        global $wpdb;

        $default = array(
            'customer_address' => '',
            'location' => '',
            'services' => '',
            'staff' => '',
            'date' => '',
            'time' => '',
            'number_people' => 1,
            'block_times' => '',
        );

        $dataReturn = array();
        $dataReturn['status'] = 'fail';
        $dataReturn['group'] = 0;

        $checkUserIsLogging = $this->checkUserIsLogging();
        if (is_wp_error($checkUserIsLogging)) {
            $this->logDebug('current user empty');
            return $checkUserIsLogging;
        }

        $data = array_merge($default, $data);
        $data = array_filter($data);
        if (count($data) != 8) {
            $debug = array(
                'info' => 'data missing',
                'data' => $data
            );
            $this->logDebug($debug);
            return new WP_Error(
                'data_missing',
                __('Mancato.', 'cutaway'),
                array(
                    'status' => 403
                )
            );
        }

        if (!$this->checkBooklyAdapterActivated()) {
            return $dataReturn;
        }

        extract ($data);

        $services = explode(',', $services);
        $bookOkAll = true;
        $appointmentGroup = 0;
        $oldAvailableTimes = false;

        $wpdb->query('START TRANSACTION');

        try {

            $booklyAppointmentAdapter = \BooklyAdapter\Entities\Appointment::getInstance();
            $booklyCustomerAppointmentAdapter = \BooklyAdapter\Entities\CustomerAppointment::getInstance();
            $booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
            $booklyLocationAdapter = \BooklyAdapter\Entities\Location::getInstance();
            $booklyCustomerAdapter = \BooklyAdapter\Entities\Customer::getInstance();
            $booklyServiceAdapter = \BooklyAdapter\Entities\Service::getInstance();
            $booklyAppointmentGroup = \BooklyAdapter\Classes\AppointmentGroup::getInstance();

            $customerId = $booklyCustomerAdapter->getCustomerIdFromUserLogged();
            $locationLoaded = $booklyLocationAdapter->loadLocationBy(array(
                'id' => $location
            ));
            $staffLoaded = $booklyStaffAdapter->loadStaffBy(array(
                'id' => $staff
            ));

            if (!empty($customerId) && !empty($locationLoaded) && !empty($staffLoaded)) {
                $time24 = function_exists('convertTimeFrom12To24') ? convertTimeFrom12To24($time) : $time;
                $mysqlDate = str_replace('/', '-', $date);
                $mysqlDate = explode('-', $mysqlDate);
                $mysqlDate = array_map(function ($item) {
                    $item = intval($item);
                    $item = $item < 10 ? '0' . $item : $item;
                    return (string) $item;
                }, $mysqlDate);
                $mysqlDate = implode('-', $mysqlDate);
                $orderStartTime = $startTime = $mysqlDate . ' ' . $time24 . ':00';
                $orderEndTime = '';

                $oldAvailableTimes = $booklyStaffAdapter->getTimeAvailableOfStaff($staff, $date);

                $appointments = array();

                $staffConfigUnAvaiTimeSlots = array();
                if (class_exists('\\REST_Shop_Controller')) {
                    $controller = new \REST_Shop_Controller();
                    $staffConfigUnAvaiTimeSlots = $controller->getStaffMetaUnAvailableTimeSlots($staff, $services, $date);
                }

                foreach ($services as $service) {
                    $serviceLoaded = $booklyServiceAdapter->loadServiceBy(array(
                        'id' => $service
                    ));
                    if (empty($serviceLoaded)) {
                        $bookOkAll = false;
                        break;
                    }

                    $endTime = $booklyServiceAdapter->calculateEndDate($serviceLoaded, $startTime);
                    if (!$booklyStaffAdapter->checkStaffWorkTimeValid(
                        $staff, $date, $startTime, $endTime
                    ) || !$booklyStaffAdapter->checkStaffWorkTimeAvailable(
                        $staff, $date, $startTime, $endTime
                    ) || (
                        !empty($staffConfigUnAvaiTimeSlots) && in_array($startTime, $staffConfigUnAvaiTimeSlots)
                    )) {
                        $bookOkAll = false;
                        break;
                    }

                    $note = array();
                    if ( !empty( $customer_address ) ) {
                        $note[] = __('Indirizzo cliente', 'cutaway') . ": $customer_address";
                    }
                    if ( !empty( $number_people ) ) {
                        $note[] = __('Numero di persone', 'cutaway') . ": $number_people";
                    }
                    $note = implode("\n", $note);

                    $data = array(
                        'service_id' => $service,
                        'location_id' => $location,
                        'staff_id' => $staff,
                        'start_date' => $startTime,
                        'note' => $note
                    );

                    $appointmentData = $data;
                    $customers = array();
                    $customers[] = $booklyCustomerAppointmentAdapter->loadCustomerAppointmentData($customerId, 'pending');

                    $newAppointmentId = $booklyAppointmentAdapter->createAppointment($appointmentData, $customers);
                    if (!$newAppointmentId) {
                        $bookOkAll = false;
                        break;
                    }

                    $appointments[] = $newAppointmentId;
                    $startTime = $endTime;
                }

                $orderEndTime = $startTime;
            } else {
                $bookOkAll = false;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            $debug = array(
                'info' => 'exception error',
                'data' => $data,
                'error' => $error
            );
            $this->logDebug($debug);
            $bookOkAll = false;
        }

        if ($bookOkAll && !empty($appointments)) {
            $block_times = explode( ',', $block_times );
            $block_times = array_filter( $block_times );

            $appointmentGroup = $booklyAppointmentGroup->makeGroupCustomerAppointments($customerId, $staff, $appointments, $number_people, $orderStartTime, $orderEndTime);
            $bookOkAll = !empty($appointmentGroup['group']) && !empty($appointmentGroup['id']);
            if ( $bookOkAll ) {
                $booklyAppointmentGroupMeta = new \BooklyAdapter\Classes\AppointmentGroupMeta( $appointmentGroup['id'] );
                $booklyAppointmentGroupMeta->updateAppointmentGroupMeta( '_bookly_booked_block_times', $block_times );
            }
        }

        if ($bookOkAll && $this->updateStaffAvailableTimes($staff, $date)) {
            $wpdb->query('COMMIT');
        } else {
            $bookOkAll = false;
            $appointmentGroup = 0;

            $wpdb->query('ROLLBACK');

            if ($oldAvailableTimes !== false) {
                $booklyStaffAdapter->restoreTimeAvailableOfStaff($staff, $date, $oldAvailableTimes);
            }
        }

        $dataReturn['status'] = $bookOkAll ? 'ok' : 'fail';
        $dataReturn['group'] = $appointmentGroup['group'];

        return $dataReturn;
    }

    public function completeBooking($dataFinishBook, $customerId = 0)
    {
        global $wpdb;

        if (!$this->checkBooklyActivated() || !$this->checkBooklyAdapterActivated()) {
            return false;
        }

        $completed = true;
        extract($dataFinishBook);

        $wpdb->query('START TRANSACTION');

        try {

            $booklyAppointmentAdapter = \BooklyAdapter\Entities\Appointment::getInstance();
            $booklyCustomerAppointmentAdapter = \BooklyAdapter\Entities\CustomerAppointment::getInstance();
            $booklyCustomerAdapter = \BooklyAdapter\Entities\Customer::getInstance();
            $booklyAppointmentGroup = \BooklyAdapter\Classes\AppointmentGroup::getInstance();

            $customerId = !empty($customerId) ? $customerId : $booklyCustomerAdapter->getCustomerIdFromUserLogged();

            if (!empty($customerId) && !empty($group)) {
                if ($booklyAppointmentGroup->getCustomerAppointmentStatus($customerId, $group) == 'pending') {
                    $customerAppointments = $booklyAppointmentGroup->getCustomerAppointmentIds($customerId, $group);
                    $customerNumberPeople = $booklyAppointmentGroup->getCustomerNumberPeople($customerId, $group);
                    if (!empty($customerAppointments) && !empty($customerNumberPeople)) {

                        foreach ($customerAppointments as $app) {
                            $data = array(
                                'id' => $app
                            );

                            $appointmentData = $data;
                            $customers = array();
                            $customers[] = $booklyCustomerAppointmentAdapter->loadCustomerAppointmentData($customerId, 'approved');

                            $newAppointmentId = $booklyAppointmentAdapter->updateAppointment($appointmentData, $customers);
                            if (!$newAppointmentId || $newAppointmentId != $app) {
                                $completed = false;
                                break;
                            }
                        }

                        if ($completed) {
                            $staffAppointmentQuery = \Bookly\Lib\Entities\Appointment::query('a')
                                ->select('a.staff_id, MONTH(a.start_date) AS start_date_month, YEAR(a.start_date) AS start_date_year, s.price')
                                ->innerJoin('Service', 's', 'a.service_id = s.id');
                            foreach($customerAppointments as $s){
                                $staffAppointmentQuery->where('id', $s, 'OR');
                            }
                            $staffAppointmentQuery->sortBy('a.start_date')
                                ->order('asc');

                            $staffAppointments = $staffAppointmentQuery->fetchArray();
                            foreach ($staffAppointments as $staffAppointment) {
                                extract($staffAppointment);

                                $staffMeta = new \BooklyAdapter\Classes\StaffMeta($staff_id);
                                $reportPrices = $staffMeta->getStaffMeta('_report_booking_price');
                                $reportPrices = !empty($reportPrices) ? $reportPrices : array();

                                $reportPrices[$start_date_year][$start_date_month] += ($price * $customerNumberPeople);

                                $staffMeta->updateStaffMeta('_report_booking_price', $reportPrices);
                            }
                        }
                    } else {
                        throw new Exception('empty_appointments');
                    }
                } else {
                    throw new Exception('wrong_status');
                }
            } else {
                throw new Exception('missing_data');
            }
        } catch (Exception $e) {
            $completed = false;
        }

        if ($completed) {
            $completed = $booklyAppointmentGroup->editGroupCustomerAppointments(
                $group, $customerId, array(
                    'status' => 1
                )
            );
            if ( $completed ) {
                if ( !empty( $stripe_charge_id ) ) {
                    $customerAppGroup = $booklyAppointmentGroup->onlyLoadCustomerAppointment( $customerId, $group );
                    if ( !empty( $customerAppGroup->id ) ) {
                        $booklyAppointmentGroupMeta = new \BooklyAdapter\Classes\AppointmentGroupMeta( $customerAppGroup->id );
                        $booklyAppointmentGroupMeta->updateAppointmentGroupMeta( '_stripe_charge_id', $stripe_charge_id );
                    }
                }
            }
        }

        if ($completed) {
            $wpdb->query('COMMIT');
        } else {
            $wpdb->query('ROLLBACK');
        }

        return $completed;
    }

    /**
     * Check permissions for the posts.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_items_permissions_check( $request ) {
        if(!$this->isRunningDebugMode()){
            if ( ! is_user_logged_in() ) {
                return new WP_Error( 'authenticate_fail', 'authenticate_fail', array( 'status' => 403 ) );
            }
        }
        return true;
    }

    protected function getDataPendingBook($request)
    {
        $dataPendingBook = array(
            'customer_address' => $request['customer_address'],
            'location' => $request['location'],
            'services' => $request['services'],
            'staff' => $request['staff'],
            'date' => $request['date'],
            'time' => $request['time'],
            'number_people' => $request['number_people'],
            'block_times' => $request['block_times'],
        );
        $dataPendingBook = array_filter($dataPendingBook);

        return $dataPendingBook;
    }

    protected function getDataFinishBook($request)
    {
        $dataFinishBook = array(
            'group' => $request['group']
        );
        $dataFinishBook = array_filter($dataFinishBook);

        return $dataFinishBook;
    }

    protected function logDebug($debug)
    {
        update_option($this->debugOptionName, $debug);
    }

    protected function isRunningDebugMode()
    {
        return defined('REST_TEST_ENABLE') && REST_TEST_ENABLE;
    }

    protected function checkUserIsLogging()
    {
        $currentUser = wp_get_current_user();
        // REST debug mode -> allow determine user by request
        /*if (empty($currentUser->ID)) {
            $currentUser = $this->setWordpressCurrentUser($currentUser);
        }*/

        if (empty($currentUser->ID)) {
            return new WP_Error('authenticate_fail', 'authenticate_fail',
            array(
                'status' => 403
            ));
        }

        return true;
    }

    protected function checkBooklyActivated()
    {
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH.'wp-admin/includes/plugin.php');
        }

        return is_plugin_active('bookly-responsive-appointment-booking-tool/main.php');
    }

    protected function checkBooklyAdapterActivated()
    {
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH.'wp-admin/includes/plugin.php');
        }

        return is_plugin_active('appointment-booking-adapter/appointment-booking-adapter.php');
    }

    private function setWordpressCurrentUser($currentUser)
    {
        // REST debug mode -> allow determine user by request
        if ($this->isRunningDebugMode()) {
            $userRequest = !empty($_REQUEST['current_user']) ? $_REQUEST['current_user'] : '';
            if (!empty($userRequest)) {
                wp_set_current_user($userRequest);
                $currentUser = wp_get_current_user();
            }
        }

        return $currentUser;
    }

    private function updateStaffAvailableTimes($staff, $date)
    {
        $booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();

        return $booklyStaffAdapter->updateTimeAvailableOfStaff($staff, $date);
    }

    private function generateNonce($method)
    {
        if (empty($method)) {
            return new WP_Error('get_nonce_fail', __('Get nonce request need one param: method.', $this->textDomain));
        }

        $action =  "{$this->prefix}/{$method}";

        return wp_create_nonce($action);
    }
}