<?php

class REST_Shop_Controller extends BaseRestModule {

    // Here initialize our namespace and resource name.
    public function __construct() {
        $this->namespace     = '/cutaway/v1';
        $this->resource_name = 'shop';
        $this->default_staff_img = plugins_url( "bookly-rest-api/assets/images/default_avatar.png");
    }

    // Register our routes.
    public function register_routes() {
        $restService = $this->getRestServiceGetStaffDetail();
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getStaffDetail', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'getStaffDetail' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            ),
        ) );

        $restService = $this->getRestServiceSearchStaffMember();
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/searchStaffMember', array(
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'searchShop' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' ),
            ),
            // Register our schema callback.
            //'schema' => array( $this, 'get_item_schema' ),
        ) );

        $restService = $this->getRestServiceGetStaffBookSummary();
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getStaffBookSummary', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'getStaffBookSummary' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            ),
        ) );

        $restService = $this->getRestServiceGetCustomerOrders();
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getCustomerOrders', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'getCustomerOrders' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            ),
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getCustomerOrders/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'getCustomerOrders' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            ),
        ) );

        $restService = $this->getRestServiceGetCustomerOrder();
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getCustomerOrder', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'getCustomerOrder' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            ),
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getCustomerOrder/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'getCustomerOrder' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            ),
        ) );

		$restService = $this->getRestServiceGetCustomerHistories();
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getCustomerHistories', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'getCustomerHistoryOrders' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            ),
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getCustomerHistories/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'getCustomerHistoryOrders' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            ),
        ) );

        $restService = $this->getRestServiceGetListStaffsCustomerBooked();
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getListStaffsCustomerBooked', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'getListStaffsCustomerBooked' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            ),
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getListStaffsCustomerBooked/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'getListStaffsCustomerBooked' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getStaffConfigAvailableTimeSlots', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => WP_REST_Server::READABLE,
                'callback'  => array( $this, 'getStaffConfigAvailableTimeSlots' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            ),
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getStaffConfigAvailableTimeSlots/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => WP_REST_Server::READABLE,
                'callback'  => array( $this, 'getStaffConfigAvailableTimeSlots' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->resource_name . '/setStaffConfigAvailableTimeSlots', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => WP_REST_Server::CREATABLE,
                'callback'  => array( $this, 'setStaffConfigAvailableTimeSlots' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            ),
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/setStaffConfigAvailableTimeSlots/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => WP_REST_Server::CREATABLE,
                'callback'  => array( $this, 'setStaffConfigAvailableTimeSlots' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            ),
        ) );

        $restService = $this->getRestServiceGetCustomerOrdersReview();
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/' . $restService['url'], array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'getCustomerOrdersReview' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            ),
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/' . $restService['url'] . '/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'getCustomerOrdersReview' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            ),
        ) );

        $restService = $this->getRestServiceUpdateCustomerOrderReview();
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/' . $restService['url'], array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'setCustomerOrdersReview' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            ),
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/' . $restService['url'] . '/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'setCustomerOrdersReview' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            ),
        ) );

        $restService = $this->getRestServiceDeleteCustomerOrderReview();
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/' . $restService['url'], array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'deleteCustomerOrdersReview' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            ),
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/' . $restService['url'] . '/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'deleteCustomerOrdersReview' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            ),
        ) );

        $restService = $this->getRestServiceGetOrdersReview();
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/' . $restService['url'], array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'getOrdersReview' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            ),
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/' . $restService['url'] . '/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $restService['method'],
                'callback'  => array( $this, 'getOrdersReview' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            ),
        ) );
    }

    public function getRestServiceSearchStaffMember()
    {
        return array(
            'method' => WP_REST_Server::READABLE,
            'url' => $this->namespace . '/' . $this->resource_name . '/searchStaffMember'
        );
    }

    public function getRestServiceGetStaffDetail()
    {
        return array(
            'method' => WP_REST_Server::READABLE,
            'url' => $this->namespace . '/' . $this->resource_name . '/getStaffDetail'
        );
    }

    public function getRestServiceGetStaffBookSummary()
    {
        return array(
            'method' => WP_REST_Server::READABLE,
            'url' => $this->namespace . '/' . $this->resource_name . '/getStaffBookSummary'
        );
    }

    public function getRestServiceGetCustomerOrders()
    {
        return array(
            'method' => WP_REST_Server::READABLE,
            'url' => $this->namespace . '/' . $this->resource_name . '/getCustomerOrders'
        );
    }

    public function getRestServiceGetCustomerOrder()
    {
        return array(
            'method' => WP_REST_Server::READABLE,
            'url' => $this->namespace . '/' . $this->resource_name . '/getCustomerOrder'
        );
    }

	public function getRestServiceGetCustomerHistories()
    {
        return array(
            'method' => WP_REST_Server::READABLE,
            'url' => $this->namespace . '/' . $this->resource_name . '/getCustomerHistories'
        );
    }

    public function getRestServiceGetListStaffsCustomerBooked()
    {
        return array(
            'method' => WP_REST_Server::READABLE,
            'url' => $this->namespace . '/' . $this->resource_name . '/getListStaffsCustomerBooked'
        );
    }

    public function getRestServiceGetCustomerOrdersReview()
    {
        return array(
            'method' => WP_REST_Server::READABLE,
            'url' => 'getCustomerOrdersReview'
        );
    }

    public function getRestServiceUpdateCustomerOrderReview()
    {
        return array(
            'method' => WP_REST_Server::CREATABLE,
            'url' => 'updateCustomerOrderReview'
        );
    }

    public function getRestServiceDeleteCustomerOrderReview()
    {
        return array(
            'method' => WP_REST_Server::CREATABLE,
            'url' => 'deleteCustomerOrderReview'
        );
    }

    public function getRestServiceGetOrdersReview()
    {
        return array(
            'method' => WP_REST_Server::READABLE,
            'url' => 'getOrdersReview'
        );
    }

    public function searchShop($request){
        $location =  $request['location'];
        $work_time = $request['work_time'];
        $service = $request['service'];
        $orderby = $request['orderby'];
        $order = $request['order'];

        $dataPaginate = parseDataPaginationFromRequest($request);
        extract($dataPaginate);

        $data = compact('location', 'work_time', 'service', 'orderby', 'order', 'page', 'limit');
        $staffs = $this->processSearchShop($data);
        if (is_wp_error($staffs)) {
            //return $staffs;
            $error = $staffs->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }

        $data = array();
        //$data['success'] = true;
        //$data['message'] = null;
        $data['StaffMembers'] = $staffs;

        return wordpress_rest_format_response_success( $data );
    }

    public function processSearchShop($data)
    {
        $default = array(
            'location' => '',
            'work_time' => '',
            'service' => '',
            'orderby' => '',
            'order'=> '',
            'page' => 1,
            'limit' => $this->restLimitRecords
        );

        $data = array_merge($default, $data);
        extract($data);

        if(empty($location) || empty($work_time) || empty($service)){
            return new WP_Error(
                'data_missing',
                __('Mancato. Dati mancanti.', 'cutaway'),
                array(
                    'status' => 403
                )
            );
        }

        if (!class_exists('\\Bookly\\Lib\\Entities\\Staff')) {
            return array();
        }

		$booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();

        $page = is_int($page) && $page > 0 ? $page : 1;
        $limit = is_int($limit) && $limit > 0 ? $limit : $this->restLimitRecords;
        $offset = ($page - 1) * $limit;

        switch($orderby){
            case 'alphabet':
                $orderby = "s.full_name";
            break;
            case 'rating':
            break;
            default:
                $orderby = "ssi.start_time";
            break;
        }

        if(empty($order)){
            $order = 'desc';
        }

        $service = explode(',', $service);
        $takeCareAll = !empty($service) && count($service) > 1 ? true : false;
        $serviceStaffIds = $this->getListStaffIdsTakeCareTheServices($service, $takeCareAll);

        if (empty($serviceStaffIds)) {
            return array();
        }

        $day_index = $this->getDayIndex($work_time);

        $staff_query = \Bookly\Lib\Entities\Staff::query('s')
        ->select('s.id, s.full_name, s.phone, s.email, s.info, s.attachment_id, lo.name as location, lo.id as location_id')
        ->innerJoin('StaffLocation', 'sl', 'sl.staff_id = s.id', 'BooklyLocations\Lib\Entities')
        ->innerJoin('Location', 'lo', 'lo.id = sl.location_id','BooklyLocations\Lib\Entities')
        ->innerJoin('StaffScheduleItem', 'ssi', 'ssi.staff_id = s.id')
        ->whereIn('s.id', $serviceStaffIds)
        ->whereLike('lo.name', '%' . $location . '%')
        ->where('s.visibility', 'public')
        ->where('ssi.day_index', $day_index)
        ->whereNot('ssi.start_time', '')
        ->groupBy('s.id')
        ->sortBy($orderby)
        ->order($order)
        ->limit($limit)
        ->offset($offset);

        $staff_array = $staff_query->fetchArray();
        $staff_data = array();

        if(!empty($staff_array)){
            foreach($staff_array as $staff){
                if(!$staff['attachment_id']){
                    $staff['avatar'] = $this->default_staff_img;
                } else {
                    $staff['avatar'] = (string) wp_get_attachment_url($staff['attachment_id']);
                }
                unset($staff['attachment_id']);

                $staff = array_merge( $staff, $booklyStaffAdapter->getStaffRatingAvg( $service[0], $staff['id'] ) );
                $staff_data[] = $staff;
            }
        }

        return $staff_data;
    }

    public function getDayIndex($date){
        $index = date('w', strtotime($date));
        return $index + 1;
    }

    public function getStaffDetail($request){
        $staff = $request['staff'];
        $work_time = $request['work_time'];
        $service = $request['service'];

        $staff = $this->processGetStaffDetail(compact('staff', 'work_time', 'service'));
        if (is_wp_error($staff)) {
            //return $staff;
            $error = $staffs->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }

        $data = array();
        //$data['success'] = true;
        //$data['message'] = null;
        $data['staffDetail'] = $staff;

        return wordpress_rest_format_response_success( $data );
    }

    public function processGetStaffDetail($data)
    {
        $default = array(
            'staff' => '',
            'work_time' => '',
            'service' => ''
        );

        $data = array_merge($default, $data);
        extract($data);

        if(empty($staff) || empty($work_time) || empty($service)){
            return new WP_Error(
                'data_missing',
                __('Mancato. Dati mancanti.', 'cutaway'),
                array(
                    'status' => 403
                )
            );
        }

        if (
            !class_exists('\\Bookly\\Lib\\Entities\\Staff') ||
            !class_exists('\\Bookly\\Lib\\Entities\\StaffService') ||
            !class_exists('\\Bookly\\Lib\\Config')
        ) {
            return array();
        }

		$booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();

        $service = explode(',', $service);
        $day_index = $this->getDayIndex($work_time);

        $staff_query = Bookly\Lib\Entities\Staff::query('s')
            ->select('s.id, s.full_name, s.attachment_id, s.info, ssi.start_time as start_time, ssi.end_time as end_time, TIME_TO_SEC(TIMEDIFF(ssi.end_time, ssi.start_time))/60 as total_time, COUNT(ss.id) AS service_count')
            ->innerJoin('StaffService', 'ss', 'ss.staff_id = s.id')
            ->innerJoin('StaffScheduleItem', 'ssi', 'ssi.staff_id = s.id')
            ->where('s.id', $staff)
            ->where('ssi.day_index', $day_index)
            ->groupBy('s.id');

        $staffId = $staff;
        $staff = $staff_query->fetchRow();

        if(!empty($staff)){
            if(!$staff['attachment_id']){
                $staff['avatar'] = $this->default_staff_img;
            } else {
                $staff['avatar'] = (string) wp_get_attachment_url($staff['attachment_id']);
            }
            unset($staff['attachment_id']);

            unset($staff['id']);
            //Get Time Service
            $serviceTimeAndPrice = $this->getServicesTimeAndPrice($service);
            $service_price = $serviceTimeAndPrice['price'];
            $time_service = $serviceTimeAndPrice['time'];
            $staff['service_price'] = $service_price;
            $timeSlot = (int) Bookly\Lib\Config::getTimeSlotLength() / 60;
            $total_time = explode(':', $staff['total_time']);
            $total_time = (int) $total_time[0];
            $block_times = $this->buildBlockTime($staffId, $service, $work_time, $staff['start_time'], $staff['end_time'], $staff['total_time'], $timeSlot, $time_service);
            $staff['block_times'] = $block_times;
            unset($staff['total_time']);
            $staff['gallery'] = $this->getStaffGallery($staffId);
            $staff['comment'] = null;

            $staff['is_favorite'] = false;
            if (class_exists('\\BooklyAdapter\\Entities\\Customer')) {
                $booklyCustomerAdapter = \BooklyAdapter\Entities\Customer::getInstance();
                $customerId = $booklyCustomerAdapter->getCustomerIdFromUserLogged();
                if (function_exists('hasFavorite') && !empty($customerId)) {
                    $staff['is_favorite'] = hasFavorite($customerId, $staffId);
                }
            }

			$staff['reviews'] = $this->processGetOrdersReview( $staffId, $service );
            $staff = array_merge( $staff, $booklyStaffAdapter->getStaffRatingAvg( $service[0], $staffId ) );
        }

        return $staff;
    }

    public function getStaffBookSummary($request)
    {
        $staff = $request['staff'];
        $date = $request['work_time'];
        $service = $request['service'];
        $time = $request['time'];

        $staffBookSummary = $this->processGetStaffBookSummary(compact(
            'staff',
            'date',
            'service',
            'time'
        ));
        if (is_wp_error($staffBookSummary)) {
            //return $staffBookSummary;
            $error = $staffBookSummary->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }


        //$data['success'] = true;
        //$data['message'] = null;
        $data['staffBookSummary'] = $staffBookSummary;

        return wordpress_rest_format_response_success( $data );
    }

    public function processGetStaffBookSummary($data)
    {
        $default = array(
            'staff' => '',
            'date' => '',
            'service' => '',
            'time' => ''
        );

        $data = array_merge($default, $data);
        extract($data);

        if(empty($staff) || empty($date) || empty($service) || empty($time)){
            return new WP_Error(
                'data_missing',
                __('Mancato. Dati mancanti.', 'cutaway'),
                array(
                    'status' => 403
                )
            );
        }

        $service = explode(",", $service);

        if (!$this->checkStaffCanBookServicesInTime($staff, $service, $date, $time)) {
            return new WP_Error(
                'time_not_available',
                __('Il tempo non è disponibile.', 'cutaway'),
                array(
                    'status' => 403
                )
            );
        }

        $staffDetail = $this->getStaffsDetail($staff);
        $staffDetail = !empty($staffDetail) ? $staffDetail[0] : array();
        $servicesDetail = $this->getServicesDetail($service);

        if (empty($staffDetail)) {
            return new WP_Error(
                'barber_not_found',
                __('Barbiere non trovato.', 'cutaway'),
                array(
                    'status' => 403
                )
            );
        }
        if (empty($servicesDetail) || count($servicesDetail) != count($service)) {
            return new WP_Error(
                'service_not_found',
                __('Servizi non trovati.', 'cutaway'),
                array(
                    'status' => 403
                )
            );
        }

        foreach ($servicesDetail as $key => &$service) {
            $service['duration'] = (int) $service['duration'] / 60;
            $service['price'] = round($service['price'], 0);
        }

        return array(
            'barber' => $staffDetail,
            'services' => $servicesDetail
        );
    }

    public function getCustomerOrders($request)
    {
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        $dataPaginate = parseDataPaginationFromRequest($request);

        $orders = $this->processGetCustomerOrders($dataPaginate);
        if (is_wp_error($orders)) {
            //return $orders;
            $error = $orders->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }

        //$data['success'] = true;
        //$data['message'] = null;
        $data['orders'] = $orders;

        $end = empty($orders['approved']);
        $data['end'] = $end;

        return wordpress_rest_format_response_success( $data );
    }

    public function processGetCustomerOrders($data = array())
    {
        $orders = array(
            'pending' => array(),
            'approved' => array()
        );

        if (!$this->checkBooklyAdapterActivated()) {
            return $orders;
        }

        $booklyCustomerAdapter = BooklyAdapter\Entities\Customer::getInstance();
        $booklyCustomerAppGroup = BooklyAdapter\Classes\AppointmentGroup::getInstance();
        $customerId = $booklyCustomerAdapter->getCustomerIdFromUserLogged();

        if (empty($customerId)) {
            return $this->generateWpErrorAuthenticateFailError();
        }

        $default = array(
            'page' => 1,
            'limit' => $this->restLimitRecords
        );
        $data = array_merge($default, $data);
        extract($data);

        $offset = ($page - 1) * $limit;

        foreach (array('pending', 'approved') as $status) {
            if ($status == 'pending') {
                if (!empty($offset)) {
                    continue;
                }
                $items = $booklyCustomerAppGroup->getCustomerAppointments($customerId, $status, 0, -1);
            } else {
                $items = $booklyCustomerAppGroup->getCustomerAppointments($customerId, $status, $offset, $limit);
            }

            if (!empty($items)) {
                foreach ($items as $order) {
                    $date = explode('-', $order['date']);
                    $order['date'] = array(
                        'day' => $date[2],
                        'month' => $date[1],
                        'year' => $date[0]
                    );

                    $time = array_map('intval', explode(':', $order['time']));
                    $order['time'] = formatTime($time);

					unset($order['barber']['attachment_id']);

                    $orders[$status][] = $order;
                }
            }
        }

        return $orders;
    }

	public function getCustomerHistoryOrders($request)
    {
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        $dataPaginate = parseDataPaginationFromRequest($request);

        $histories = $this->processGetCustomerHistoryOrders($dataPaginate);
        if (is_wp_error($histories)) {
            //return $orders;
            $error = $histories->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }

        //$data['success'] = true;
        //$data['message'] = null;
        $data['histories'] = $histories;

        $end = empty($histories);
        $data['end'] = $end;

        return wordpress_rest_format_response_success( $data );
    }

    public function processGetCustomerHistoryOrders($data = array())
    {
        $histories = array();

        if (!$this->checkBooklyAdapterActivated()) {
            return $histories;
        }

        $booklyCustomerAdapter = BooklyAdapter\Entities\Customer::getInstance();
        $booklyCustomerAppGroup = BooklyAdapter\Classes\AppointmentGroup::getInstance();
        $customerId = $booklyCustomerAdapter->getCustomerIdFromUserLogged();

        if (empty($customerId)) {
            return $this->generateWpErrorAuthenticateFailError();
        }

        $default = array(
            'page' => 1,
            'limit' => $this->restLimitRecords
        );
        $data = array_merge($default, $data);
        extract($data);

        $offset = ($page - 1) * $limit;

        $items = $booklyCustomerAppGroup->getCustomerAppointments($customerId, '', $offset, $limit);

		if (!empty($items)) {
			foreach ($items as $history) {
				$date = explode('-', $history['date']);
				$history['date'] = array(
					'day' => $date[2],
					'month' => $date[1],
					'year' => $date[0]
				);

				$time = array_map('intval', explode(':', $history['time']));
				$history['time'] = formatTime($time);

				if(!$history['barber']['attachment_id']){
                    $history['barber']['avatar'] = $this->default_staff_img;
                } else {
                    $history['barber']['avatar'] = (string) wp_get_attachment_url($history['barber']['attachment_id']);
                }
                unset($history['barber']['attachment_id']);

				$histories[] = $history;
			}
		}

        return $histories;
    }

    public function getCustomerOrder($request)
    {
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        $group = $request['group'];
        $orderReturn = $this->processGetCustomerOrder(array(
            'group' => $group
        ));
        if (is_wp_error($orderReturn)) {
            //return $orderReturn;
            $error = $orderReturn->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }

        //$data['success'] = true;
        //$data['message'] = null;
        $data['order'] = $orderReturn;

        return wordpress_rest_format_response_success( $data );
    }

    public function processGetCustomerOrder($data = array())
    {
        $default = array(
            'group' => ''
        );

        if (!$this->checkBooklyAdapterActivated()) {
            return array();
        }

        $data = array_merge($default, $data);
        extract($data);

        if(empty($group)){
            return new WP_Error(
                'data_missing',
                __('Mancato.', 'cutaway'),
                array(
                    'status' => 403
                )
            );
        }

        $booklyCustomerAdapter = BooklyAdapter\Entities\Customer::getInstance();
        $booklyCustomerAppGroup = BooklyAdapter\Classes\AppointmentGroup::getInstance();

        $customerId = $booklyCustomerAdapter->getCustomerIdFromUserLogged();
        if (empty($customerId)) {
            return $this->generateWpErrorAuthenticateFailError();
        }

        $order = $booklyCustomerAppGroup->getCustomerAppointment($customerId, $group);
        if (empty($order)) {
            return new WP_Error(
                'order_not_found',
                __('Mancato.', 'cutaway'),
                array(
                    'status' => 403
                )
            );
        }

        if(!$order['barber']['attachment_id']){
            $order['barber']['avatar'] = $this->default_staff_img;
        } else {
            $order['barber']['avatar'] = (string) wp_get_attachment_url($order['barber']['attachment_id']);
        }

        $order['date'] = explode('-', $order['date']);

        $time = array_map('intval', explode(':', $order['time']));
        $order['time'] = formatTime($time);

        $orderReturn = array(
            'barber' => array(
                'full_name' => $order['barber']['full_name'],
                'logo' => $order['barber']['avatar']
            ),
            'services' => array(),
            'notes' => $order['notes'],
            'date' => array(
                'day' => $order['date'][2],
                'month' => $order['date'][1],
                'year' => $order['date'][0]
            ),
            'time' => $order['time'],
            'group_booking' => $order['group'],
            'status' => $order['status'],
            'number_people' => $order['number_people']
        );
        foreach ($order['services'] as $service) {
            $orderReturn['services'][] = array(
                'title' => $service['title'],
                'time_process' => (int) $service['duration'] / 60,
                'price' => round($service['price'], 0)
            );
        }

        return $orderReturn;
    }

    public function getListStaffsCustomerBooked($request)
    {
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        $staffsData = $this->processGetListStaffsCustomerBooked();
        if (is_wp_error($staffsData)) {
            //return $staffsData;
            $error = $staffsData->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }

        //$data['success'] = true;
        //$data['message'] = null;
        $data['staffs'] = $staffsData;

        return wordpress_rest_format_response_success( $data );
    }

    public function processGetListStaffsCustomerBooked($data = array())
    {
        $staffsData = array();

        if (!$this->checkBooklyAdapterActivated()) {
            return $staffsData;
        }

        $booklyStaffAdapter = BooklyAdapter\Entities\Staff::getInstance();
        $booklyCustomerAppGroup = BooklyAdapter\Classes\AppointmentGroup::getInstance();
        $booklyCustomerAdapter = BooklyAdapter\Entities\Customer::getInstance();

        $customerId = $booklyCustomerAdapter->getCustomerIdFromUserLogged();
        if (empty($customerId)) {
            return $this->generateWpErrorAuthenticateFailError();
        }

        $staffs = $booklyCustomerAppGroup->getListStaffIdsCustomerBooked($customerId);

        if (!empty($staffs)) {
            $staffs = $booklyStaffAdapter->getStaffsDetail($staffs);
            if (!empty($staffs)) {
                foreach ($staffs as $staff) {
                    $staffData = array(
                        'id' => $staff['id'],
                        'full_name' => $staff['full_name']
                    );

                    if(!$staff['attachment_id']){
                        $staffData['avatar'] = $this->default_staff_img;
                    } else {
                        $staffData['avatar'] = (string) wp_get_attachment_url($staff['attachment_id']);
                    }

                    $staffData['is_favorite'] = false;
                    if (function_exists('hasFavorite')) {
                        $staffData['is_favorite'] = hasFavorite($customerId, $staff['id']);
                    }

                    $staffsData[] = $staffData;
                }
            }
        }

        return $staffsData;
    }

    public function getStaffConfigAvailableTimeSlots($request)
    {
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        if (!$this->checkBooklyAdapterActivated()) {
            /*return new WP_Error(
                'get_fail',
                __('Mancato.', 'cutaway'),
                array(
                    'status' => 403
                )
            );*/
            return wordpress_rest_format_response_fail( 'get_fail' );
        }

        $booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
        $staff = $booklyStaffAdapter->getStaffIdFromUserLogged();
        $services = $request['services'];
        $date = $request['date'];

        $blockTimes = $this->processGetStaffConfigAvailableTimeSlots($staff, $services, $date);

        //$data['success'] = true;
        //$data['message'] = null;
        $data['block_times'] = $blockTimes;

        return wordpress_rest_format_response_success( $data );
    }

    public function processGetStaffConfigAvailableTimeSlots($staff, $services, $date){
        if(
            empty($staff) ||
            empty($services) ||
            empty($date) ||
            !class_exists('\\BooklyAdapter\\Classes\\StaffMeta')
        ){
            return array();
        }

        $services = explode(',', $services);
        $day_index = $this->getDayIndex($date);

        $staff_query = Bookly\Lib\Entities\Staff::query('s')
            ->select('ssi.start_time as start_time, ssi.end_time as end_time, TIME_TO_SEC(TIMEDIFF(ssi.end_time, ssi.start_time))/60 as total_time')
            ->innerJoin('StaffScheduleItem', 'ssi', 'ssi.staff_id = s.id')
            ->where('s.id', $staff)
            ->where('ssi.day_index', $day_index)
            ->groupBy('s.id');

        $staffId = $staff;
        $staff = $staff_query->fetchRow();
        $block_times = array();

        if(!empty($staff)){
            //Get Time Service
            $serviceTimeAndPrice = $this->getServicesTimeAndPrice($services);
            $time_service = $serviceTimeAndPrice['time'];
            $timeSlot = (int) Bookly\Lib\Config::getTimeSlotLength() / 60;
            $total_time = explode(':', $staff['total_time']);
            $total_time = (int) $total_time[0];
            $block_times = $this->buildBlockTimeForStaffConfigAvailableTimeSlots($staffId, $work_time, $staff['start_time'], $staff['end_time'], $staff['total_time'], $timeSlot, $time_service);

            if (!empty($block_times)) {
                $configUnAvaiTimeSlots = $this->getStaffMetaUnAvailableTimeSlots($staffId, $services, $date);
                if (!empty($configUnAvaiTimeSlots)) {
                    foreach ($block_times as $key => $time) {
                        if (in_array($time['start_time'], $configUnAvaiTimeSlots)) {
                            $block_times[$key]['selected'] = true;
                        }
                    }
                }
            }
        }

        return $block_times;
    }

    public function setStaffConfigAvailableTimeSlots($request)
    {
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        if (!$this->checkBooklyAdapterActivated()) {
            /*return new WP_Error(
                'get_fail',
                __('Mancato.', 'cutaway'),
                array(
                    'status' => 403
                )
            );*/
            return wordpress_rest_format_response_fail( 'get_fail' );
        }

        $booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
        $staff = $booklyStaffAdapter->getStaffIdFromUserLogged();
        $services = $request['services'];
        $date = $request['date'];
        $unAvailableTimeSlots = $request['un_available_time_slots'];
        $unAvailableTimeSlots = trim($unAvailableTimeSlots, '; ');
        $unAvailableTimeSlots = explode(';', $unAvailableTimeSlots);

        $result = $this->processSetStaffConfigAvailableTimeSlots($staff, $services, $date, $unAvailableTimeSlots);
        if (is_wp_error($result)) {
            //return $result;
            $error = $result->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }

        //$data['success'] = true;
        //$data['message'] = null;
        //$data['status'] = $result ? 'ok' : 'fail';
        if ( $result ) {
            return wordpress_rest_format_response_success();
        } else {
            return wordpress_rest_format_response_fail( 'fail' );
        }
    }

    public function processSetStaffConfigAvailableTimeSlots($staff, $services, $date, $unAvailableTimeSlots){
        if(
            empty($staff) ||
            empty($services) ||
            empty($date) ||
            !class_exists('\\BooklyAdapter\\Classes\\StaffMeta')
        ){
            return false;
        }

        $services = $this->standardServiceIdsToKey($services);
        $unAvailableTimeSlots = !empty($unAvailableTimeSlots) ? $unAvailableTimeSlots : array();
        $unAvailableTimeSlots = is_array($unAvailableTimeSlots) ? $unAvailableTimeSlots : array($unAvailableTimeSlots);
        $staffMeta = new \BooklyAdapter\Classes\StaffMeta($staff);
        $configUnAvaiTimeSlots = $staffMeta->getStaffMeta('_config_unavailable_time_slots');
        $configUnAvaiTimeSlots = !empty($configUnAvaiTimeSlots) ? $configUnAvaiTimeSlots : array();
        $configUnAvaiTimeSlots[$services][$date] = $unAvailableTimeSlots;

        return $staffMeta->updateStaffMeta('_config_unavailable_time_slots', $configUnAvaiTimeSlots);
    }

    public function getCustomerOrdersReview($request)
    {
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        $dataPaginate = parseDataPaginationFromRequest($request);

        $ordersReview = $this->processGetCustomerOrdersReview($dataPaginate);
        if (is_wp_error($ordersReview)) {
            //return $orders;
            $error = $ordersReview->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }

        //$data['success'] = true;
        //$data['message'] = null;
        $data['orders'] = $ordersReview;

        $end = empty($ordersReview['reviewed']);
        $data['end'] = $end;

        return wordpress_rest_format_response_success( $data );
    }

    public function processGetCustomerOrdersReview($data = array())
    {
        $orders = array(
            'need_review' => array(),
            'reviewed' => array(),
        );

        if (!$this->checkBooklyAdapterActivated()) {
            return $orders;
        }

        $booklyCustomerAdapter = BooklyAdapter\Entities\Customer::getInstance();
        $booklyCustomerAppGroup = BooklyAdapter\Classes\AppointmentGroup::getInstance();
        $customerId = $booklyCustomerAdapter->getCustomerIdFromUserLogged();

        if (empty($customerId)) {
            return $this->generateWpErrorAuthenticateFailError();
        }

        $default = array(
            'page' => 1,
            'limit' => $this->restLimitRecords
        );
        $data = array_merge($default, $data);
        extract($data);

        $offset = ($page - 1) * $limit;

        $orders = $booklyCustomerAppGroup->getCustomerOrdersReview( $customerId, $offset, $limit );
        if ( !empty( $orders['need_review'] ) ) {
            foreach ( $orders['need_review'] as &$order ) {
                if( empty( $order['barber']['logo'] ) ){
                    $order['barber']['logo'] = $this->default_staff_img;
                } else {
                    $order['barber']['logo'] = (string) wp_get_attachment_url( $order['barber']['logo'] );
                }
                $order['date'] = parseMysqlDateTime( $order['date'] );
                $order['time_selected'] = $order['date']['time'];
            }
        }
        if ( !empty( $orders['reviewed'] ) ) {
            foreach ( $orders['reviewed'] as &$order ) {
                if( empty( $order['barber']['logo'] ) ){
                    $order['barber']['logo'] = $this->default_staff_img;
                } else {
                    $order['barber']['logo'] = (string) wp_get_attachment_url( $order['barber']['logo'] );
                }
                $order['date'] = parseMysqlDateTime( $order['date'] );
                $order['time_selected'] = $order['date']['time'];
            }
        }

        return $orders;
    }

    public function setCustomerOrdersReview($request)
    {
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        $booklyCustomerAdapter = BooklyAdapter\Entities\Customer::getInstance();
        $customerId = $booklyCustomerAdapter->getCustomerIdFromUserLogged();
        if (empty($customerId)) {
            return $this->generateAuthenticateFailError();
        }

        $token = !empty( $request['token'] ) ? $request['token'] : '';
        $rating = !empty( $request['rating'] ) ? $request['rating'] : '';
        $comment = !empty( $request['comment'] ) ? trim( $request['comment'] ) : '';
        $comment = !empty( $comment ) ? $comment : false;

        $booklyCustomerAppAdapter = BooklyAdapter\Entities\CustomerAppointment::getInstance();
        if ( $booklyCustomerAppAdapter->saveCustomerReviewData(
            $token,
            $rating,
            $comment
        ) ) {
            return wordpress_rest_format_response_success();
        } else {
            return wordpress_rest_format_response_fail( 'fail' );
        }
    }

    public function deleteCustomerOrdersReview($request)
    {
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        $booklyCustomerAdapter = BooklyAdapter\Entities\Customer::getInstance();
        $customerId = $booklyCustomerAdapter->getCustomerIdFromUserLogged();
        if (empty($customerId)) {
            return $this->generateAuthenticateFailError();
        }

        $token = !empty( $request['token'] ) ? $request['token'] : '';

        $booklyCustomerAppAdapter = BooklyAdapter\Entities\CustomerAppointment::getInstance();
        if ( $booklyCustomerAppAdapter->deleteCustomerReviewData( $token ) ) {
            return wordpress_rest_format_response_success();
        } else {
            return wordpress_rest_format_response_fail( 'fail' );
        }
    }

    public function getOrdersReview($request)
    {
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        $staff = !empty( $request['staff'] ) ? trim( $request['staff'] ) : 0;
        $services = !empty( $request['services'] ) ? trim( $request['services'] ) : '';
        $services = explode( ',', $services );

        $ordersReview = $this->processGetOrdersReview( $staff, $services );
        if (is_wp_error($ordersReview)) {
            //return $orders;
            $error = $ordersReview->get_error_code();
            return wordpress_rest_format_response_fail( $error );
        }

        //$data['success'] = true;
        //$data['message'] = null;
        $data['reviews'] = $ordersReview;

        return wordpress_rest_format_response_success( $data );
    }

    public function processGetOrdersReview($staff, $services)
    {
        if (!$this->checkBooklyAdapterActivated()) {
            return array();
        }

        if ( empty( $staff ) || empty( $services ) ) {
            return array();
        }

        $booklyCustomerAppAdapter = BooklyAdapter\Entities\CustomerAppointment::getInstance();
        $booklyCustomerAdapter = BooklyAdapter\Entities\Customer::getInstance();
        $customerId = $booklyCustomerAdapter->getCustomerIdFromUserLogged();

        if (empty($customerId)) {
            return $this->generateWpErrorAuthenticateFailError();
        }

        $reviews = $booklyCustomerAppAdapter->loadReviewsFromStaffAndServices( $staff, $services );

        return $reviews;
    }

    public function buildBlockTime($staff, $services, $date, $start_time, $end_time, $total_time, $slot_length, $service_time, $ignoreCheckAvailable = false){
        $block_times = array();
        $i = 0;
        $start_time = array_map('intval', explode(':', $start_time));

        $configUnAvaiTimeSlots = $this->getStaffMetaUnAvailableTimeSlots($staff, $services, $date);

        while($service_time < $total_time){
            $startTimeFormatted = formatTime($start_time);
            $lost_time = addMinute($start_time, (int) $service_time);
            $endTimeFormatted = formatTime($lost_time);
            $start_time = addMinute($lost_time, (int) $slot_length);
            $total_time -= $service_time + $slot_length;

            if ($ignoreCheckAvailable || empty($configUnAvaiTimeSlots) || !in_array($startTimeFormatted, $configUnAvaiTimeSlots)) {
                if ($ignoreCheckAvailable || checkStaffCanBookOnTimeRange($staff, $date, $startTimeFormatted, $endTimeFormatted)) {
                    $block_times[$i]['start_time'] = $startTimeFormatted;
                    $i++;
                }
            }
        }
        return $block_times;
    }

    public function buildBlockTimeForStaffConfigAvailableTimeSlots($staff, $date, $start_time, $end_time, $total_time, $slot_length, $service_time){
        $block_times = array();
        $i = 0;
        $start_time = array_map('intval', explode(':', $start_time));
        while($service_time < $total_time){
            $startTimeFormatted = formatTime($start_time);
            $lost_time = addMinute($start_time, (int) $service_time);
            $endTimeFormatted = formatTime($lost_time);
            $start_time = addMinute($lost_time, (int) $slot_length);
            $total_time -= $service_time + $slot_length;

            $block_times[$i] = array(
                'start_time' => $startTimeFormatted,
                'selected' => false
            );
            $i++;
        }
        return $block_times;
    }

    public function getStaffMetaUnAvailableTimeSlots($staff, $services, $date)
    {
        $configUnAvaiTimeSlots = array();

        if (class_exists('\\BooklyAdapter\\Classes\\StaffMeta')) {
            $services = $this->standardServiceIdsToKey($services);
            $date = str_replace('/', '-', $date);
            $staffMeta = new \BooklyAdapter\Classes\StaffMeta($staff);
            $configUnAvaiTimeSlots = $staffMeta->getStaffMeta('_config_unavailable_time_slots');
            $configUnAvaiTimeSlots = !empty($configUnAvaiTimeSlots) ? $configUnAvaiTimeSlots : array();
            if (!empty($configUnAvaiTimeSlots) && !empty($configUnAvaiTimeSlots[$services]) && !empty($configUnAvaiTimeSlots[$services][$date])) {
                $configUnAvaiTimeSlots = $configUnAvaiTimeSlots[$services][$date];
            } else {
                $configUnAvaiTimeSlots = array();
            }
        }

        return $configUnAvaiTimeSlots;
    }

    private function getListStaffIdsTakeCareTheServices($services, $takeCareAll = true)
    {
        if (empty($services) || !class_exists('\\Bookly\\Lib\\Entities\\StaffService')) {
            return array();
        }

        if (!is_array($services)) {
            $services = array($services);
        }
        $totalServices = count($services);

        $service_query = Bookly\Lib\Entities\StaffService::query();
        if ($takeCareAll) {
            $service_query->select('staff_id, COUNT(id) AS total_services');
            $service_query->groupBy('staff_id');
            $service_query->havingRaw('total_services >= %d', array($totalServices));
        } else {
            $service_query->select('DISTINCT staff_id');
        }

        foreach ($services as $service) {
            $service_query->where('service_id', $service, 'OR');
        }

        $staffsService = $service_query->fetchArray();
        $result = array();
        foreach($staffsService as $item){
            $result[] = $item['staff_id'];
        }

        return $result;
    }

    private function standardServiceIdsToKey($services)
    {
        if (empty($services)) {
            return $services;
        }

        if (!is_array($services)) {
            $services = explode(',', $services);
        }

        sort($services, SORT_NUMERIC);

        return implode(',', $services);
    }

    private function getStaffGallery($staff)
    {
        $gallery = array();

        if (function_exists('get_staff_gallery')) {
            $galleryPost = get_staff_gallery($staff);
            if (!empty($galleryPost) && function_exists('get_field')) {
                $galImages = get_field('gallery', $galleryPost);
                if (!empty($galImages)) {
                    foreach ($galImages as $image) {
                        $gallery[] = $image['sizes']['thumbnail'];
                    }
                }
            }
        }

        return $gallery;
    }

    private function getServicesTimeAndPrice($services)
    {
        $booklyServiceAdapter = BooklyAdapter\Entities\Service::getInstance();
        return $booklyServiceAdapter->getServicesTimeAndPrice($services);
    }

    private function getStaffsDetail($staffs)
    {
        $booklyStaffAdapter = BooklyAdapter\Entities\Staff::getInstance();
        $detail = $booklyStaffAdapter->getStaffsDetail($staffs);

        foreach ($detail as $key => $item) {
            if(!$item['attachment_id']){
                $item['avatar'] = $this->default_staff_img;
            } else {
                $item['avatar'] = (string) wp_get_attachment_url($item['attachment_id']);
            }

            $detail[$key] = $item;
        }

        return $detail;
    }

    private function getServicesDetail($services)
    {
        $booklyServiceAdapter = BooklyAdapter\Entities\Service::getInstance();
        $detail = $booklyServiceAdapter->getServicesDetail($services);

        return $detail;
    }

    private function checkStaffCanBookServicesInTime($staff, $services, $date, $startTime)
    {
        $servicesInfo = $this->getServicesTimeAndPrice($services);
        $startTime = convertTimeFrom12To24($startTime);
        $startTime = array_map('intval', explode(':', $startTime));
        $lost_time = addMinute($startTime, $servicesInfo['time']);

        $startTimeFormatted = formatTime($startTime);
        $endTimeFormatted = formatTime($lost_time);

        return checkStaffCanBookOnTimeRange($staff, $date, $startTimeFormatted, $endTimeFormatted);
    }
}

// Function to register our new routes from the controller.
function prefix_register_shop_rest_routes() {
    $controller = new REST_Shop_Controller();
    $controller->register_routes();
}

add_action( 'rest_api_init', 'prefix_register_shop_rest_routes' );