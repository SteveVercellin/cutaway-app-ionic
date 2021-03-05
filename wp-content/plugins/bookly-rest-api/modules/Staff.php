<?php
class REST_Staff_Controller extends BaseRestModule {


    // Here initialize our namespace and resource name.
    public function __construct() {
        $this->namespace     = '/cutaway/v1';
        $this->resource_name = 'staffs';
    }

    // Register our routes.
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getStaffDashboardData', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => WP_REST_Server::READABLE,
                'callback'  => array( $this, 'getStaffDashboardData' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            )
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getStaffDashboardData/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => WP_REST_Server::READABLE,
                'callback'  => array( $this, 'getStaffDashboardData' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            )
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/updateStaffBookAvailable', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => WP_REST_Server::CREATABLE,
                'callback'  => array( $this, 'updateStaffBookAvailable' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            )
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/updateStaffBookAvailable/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => WP_REST_Server::CREATABLE,
                'callback'  => array( $this, 'updateStaffBookAvailable' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            )
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getStaffOrders', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => WP_REST_Server::READABLE,
                'callback'  => array( $this, 'getStaffOrders' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            )
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getStaffOrders/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => WP_REST_Server::READABLE,
                'callback'  => array( $this, 'getStaffOrders' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            )
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getStaffOrder', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => WP_REST_Server::READABLE,
                'callback'  => array( $this, 'getStaffOrder' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            )
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getStaffOrder/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => WP_REST_Server::READABLE,
                'callback'  => array( $this, 'getStaffOrder' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            )
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getStaffOrdersRating', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => WP_REST_Server::READABLE,
                'callback'  => array( $this, 'getStaffOrdersRating' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            )
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getStaffOrdersRating/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => WP_REST_Server::READABLE,
                'callback'  => array( $this, 'getStaffOrdersRating' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            )
        ) );
    }

    public function getStaffDashboardData($request){
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        $data = $this->processGetStaffDashboardData();
        if (is_wp_error($data)) {
            //return $data;
            $error = $data->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }

        if (!empty($data['report_price'])) {
            $currencySymbol = $this->getBooklyConfigCurrencySymbol();
            $dayIncome = 1;
            if (function_exists('cutawayGetThemeOption')) {
                $dayIncome = cutawayGetThemeOption('barber_dashboard_income_day', 1);
            }
            $dayIncome = is_numeric($dayIncome) && $dayIncome > 0 ? $dayIncome : 1;
            $dayIncome = $dayIncome < 10 ? '0' . $dayIncome : $dayIncome;

            $reportPrices = array();
            foreach ($data['report_price'] as $year => $months) {
                foreach ($months as $month => $totalPrice) {
                    $formattedPrice = number_format($totalPrice, 0);
                    $month = $month < 10 ? '0' . $month : $month;

                    $reportPrices[] = sprintf('%s/%s/%s: %s %s', $dayIncome, $month, $year, $formattedPrice, $currencySymbol);
                }
            }
            $data['report_price'] = $reportPrices;
        }

        //$response['success'] = true;
        //$response['message'] = null;
        $response['data'] = $data;

        return wordpress_rest_format_response_success( $response );
    }

    public function processGetStaffDashboardData()
    {
        $default = array(
            'report_price' => array(),
            'day_to_pay' => '',
            'rating' => array(
                'percent' => 0,
                'review' => 0
            ),
            'book_available' => true
        );

        if (!$this->checkBooklyAdapterActivated()) {
            return $default;
        }

        $booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
        $data = $booklyStaffAdapter->getStaffDashboardDataFromUserLogged();

        $data = array_merge($default, $data);

        return $data;
    }

    public function updateStaffBookAvailable($request){
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        $state = $request['state'];

        $result = $this->processUpdateStaffBookAvailable($state);
        if (is_wp_error($result)) {
            //return $result;
            $error = $result->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }


        //$response['success'] = true;
        //$response['message'] = null;
        //$response['status'] = 'ok';

        return wordpress_rest_format_response_success();
    }

    public function processUpdateStaffBookAvailable($state)
    {
        if (!$this->checkBooklyAdapterActivated()) {
            return new WP_Error(
                'update_fail',
                __('Mancato.', 'cutaway'),
                array(
                    'status' => 403
                )
            );
        }

        $booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
        $currentBarberId = $booklyStaffAdapter->getStaffIdFromUserLogged();
        $state = is_numeric($state) ? $state : 1;
        $state = $state ? 'public' : 'private';

        $result = $booklyStaffAdapter->updateStaff(array(
            'id' => $currentBarberId,
            'visibility' => $state
        ));

        if (!$result) {
            return new WP_Error(
                'update_fail',
                __('Mancato.', 'cutaway'),
                array(
                    'status' => 403
                )
            );
        }

        return true;
    }

    public function getStaffOrders($request){
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        $booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
        $staff = $booklyStaffAdapter->getStaffIdFromUserLogged();
        if ( empty( $staff ) ) {
            return $this->generateAuthenticateFailError();
        }

        $dataPaginate = parseDataPaginationFromRequest($request);

        $response = $this->processGetStaffOrders( $staff, $dataPaginate );
        if (is_wp_error($response)) {
            //return $result;
            $error = $response->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }


        //$response['success'] = true;
        //$response['message'] = null;
        //$response['orders'] = $orders;

        return wordpress_rest_format_response_success( $response );
    }

    public function processGetStaffOrders( $staff, $dataPaginate )
    {
        if (!$this->checkBooklyAdapterActivated() || empty( $staff )) {
            return new WP_Error(
                'fail',
                'fail',
                array(
                    'status' => 403
                )
            );
        }

        $default = array(
            'page' => 1,
            'limit' => $this->restLimitRecords
        );
        $dataPaginate = array_merge($default, $dataPaginate);
        extract($dataPaginate);
        $offset = ($page - 1) * $limit;

        $booklyAppGroupAdapter = \BooklyAdapter\Classes\AppointmentGroup::getInstance();
        $staffOrders = $booklyAppGroupAdapter->getStaffAppointments( $staff, $offset, $limit );

        $checkHasNextOrders = $booklyAppGroupAdapter->getStaffAppointments( $staff, $offset + $limit, 1 );

        return array(
            'orders' => $staffOrders,
            'end' => empty( $checkHasNextOrders )
        );
    }

    public function getStaffOrder($request){
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        $booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
        $staff = $booklyStaffAdapter->getStaffIdFromUserLogged();
        $id = !empty( $request['id'] ) ? $request['id'] : 0;
        if ( empty( $staff ) || empty( $id ) ) {
            return $this->generateAuthenticateFailError();
        }

        $order = $this->processGetStaffOrder( $id );
        if (is_wp_error($order)) {
            //return $result;
            $error = $order->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }


        //$response['success'] = true;
        //$response['message'] = null;
        $response['order'] = $order;

        return wordpress_rest_format_response_success( $response );
    }

    public function processGetStaffOrder( $id )
    {
        if ( !$this->checkBooklyAdapterActivated() || empty( $id ) ) {
            return new WP_Error(
                'fail',
                'fail',
                array(
                    'status' => 403
                )
            );
        }

        $booklyAppGroupAdapter = \BooklyAdapter\Classes\AppointmentGroup::getInstance();
        $staffOrder = $booklyAppGroupAdapter->getStaffAppointment( $id );
        if ( !empty( $staffOrder ) ) {
            if( empty( $staffOrder['customer']['logo'] ) ){
                $staffOrder['customer']['logo'] = $this->default_staff_img;
            } else {
                $staffOrder['customer']['logo'] = (string) wp_get_attachment_url( $staffOrder['customer']['logo'] );
            }
            $staffOrder['date'] = parseMysqlDateTime( $staffOrder['date'] );
            $staffOrder['time_selected'] = $staffOrder['date']['time'];
        }

        return $staffOrder;
    }

    public function getStaffOrdersRating($request){
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        $booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
        $staff = $booklyStaffAdapter->getStaffIdFromUserLogged();
        if ( empty( $staff ) ) {
            return $this->generateAuthenticateFailError();
        }

        $dataPaginate = parseDataPaginationFromRequest($request);

        $response = $this->processGetStaffOrdersRating( $staff, $dataPaginate );
        if (is_wp_error($response)) {
            //return $result;
            $error = $response->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }


        //$response['success'] = true;
        //$response['message'] = null;
        //$response['orders'] = $orders;

        return wordpress_rest_format_response_success( $response );
    }

    public function processGetStaffOrdersRating( $staff, $dataPaginate )
    {
        if (!$this->checkBooklyAdapterActivated() || empty( $staff )) {
            return new WP_Error(
                'fail',
                'fail',
                array(
                    'status' => 403
                )
            );
        }

        $default = array(
            'page' => 1,
            'limit' => $this->restLimitRecords
        );
        $dataPaginate = array_merge($default, $dataPaginate);
        extract($dataPaginate);
        $offset = ($page - 1) * $limit;

        $booklyAppGroupAdapter = \BooklyAdapter\Classes\AppointmentGroup::getInstance();
        $staffOrders = $booklyAppGroupAdapter->getStaffOrdersReview( $staff, $offset, $limit );

        $checkHasNextOrders = $booklyAppGroupAdapter->getStaffOrdersReview( $staff, $offset + $limit, 1 );

        return array(
            'orders' => $staffOrders,
            'end' => empty( $checkHasNextOrders )
        );
    }
}

// Function to register our new routes from the controller.
function prefix_register_staffs_rest_routes() {
    $controller = new REST_Staff_Controller();
    $controller->register_routes();
}

add_action( 'rest_api_init', 'prefix_register_staffs_rest_routes' );