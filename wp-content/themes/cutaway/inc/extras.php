<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package cutaway
 */

add_filter('body_class', 'cutaway_body_classes');

if (!function_exists('cutaway_body_classes')) {
    /**
     * Adds custom classes to the array of body classes.
     *
     * @param array $classes Classes for the body element.
     *
     * @return array
     */
    function cutaway_body_classes($classes) {
        if ( !is_admin() ) {
            $classes[] = 'not-admin-page';
        }
        return $classes;
    }
}

function add_slug_body_class( $classes ) {
    global $post;
    if ( isset( $post ) ) {
        $classes[] = $post->post_type . '-' . $post->post_name;
    }
    return $classes;
}
add_filter( 'body_class', 'add_slug_body_class' );

add_action('wp_head', 'cutaway_remove_margin_html_tag', 99, 0);
function cutaway_remove_margin_html_tag() {
    if ( !is_admin() ) {
        ?>
        <style type="text/css" media="screen">
            html { margin-top: 0 !important; }
            * html body { margin-top: 0 !important; }
            @media screen and ( max-width: 782px ) {
                html { margin-top: 0 !important; }
                * html body { margin-top: 0 !important; }
            }
        </style>
        <?php
    }
}

// Redirect to homepage if user don't login
add_action('template_redirect', 'cutaway_redirect_to_home');
function cutaway_redirect_to_home() {
    $request_uri = $_SERVER['REQUEST_URI'];

    if ( !is_admin() ) {
        if ( !is_user_logged_in() ) {
            if ( $request_uri != "/" && $request_uri != "/?login=failed" && strpos($request_uri, 'sign-in') === false && strpos($request_uri, 'recovery-password') === false ) {
                wp_redirect( home_url() );
                exit();
            }
        }
    }
}

/*
 * Register User
 */
add_action('init', 'cutaway_register_new_user');
function cutaway_register_new_user() {

    if ( isset($_POST['form_sign_in_submit']) ) {
        global $result_content;
        $result_content = [
            'type' => 'info',
            'content' => __('Registrati con successo.', 'cutaway')
        ];

        $first_name = $_POST['user_firstname'];
        $last_name = $_POST['user_lastname'];
        $email = $_POST['user_email'];
        $phone_number = $_POST['user_phone'];
        $city = $_POST['user_city'];
        $password = $_POST['user_password'];
        $confirm_pass = $_POST['user_password_confirm'];
        $userData = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => $password,
            'phone_number' => $phone_number,
            'city' => $city
        );
        if ( !is_email($userData['email']) ) {
            $result_content['type'] = 'danger';
            $result_content['content'] = __('Registrati utente fallito. Email non valida.', 'cutaway');
        }
        else if ( email_exists($userData['email']) ) {
            $result_content['type'] = 'danger';
            $result_content['content'] = __('Registrati utente fallito. E-mail esistita.', 'cutaway');
        }
        else if ( $password != $confirm_pass ) {
            $result_content['type'] = 'danger';
            $result_content['content'] = __('Registrati utente fallito. Conferma password non vera.', 'cutaway');
        }
        else {
            $fullName = $userData['first_name'] . ' ' . $userData['last_name'];
            $user = wp_insert_user(array(
                'user_login' => $userData['email'],
                'user_email' => $userData['email'],
                'display_name' => $fullName,
                'user_pass' => $userData['password'],
                'role' => 'subscriber'
            ));
            if ( is_wp_error($user) ) {
                $result_content['type'] = 'danger';
                $result_content['content'] = __('Registrati utente fallito.', 'cutaway');
            }
            else {
                update_user_meta($user, 'first_name', $userData['first_name']);
                update_user_meta($user, 'last_name', $userData['last_name']);
                $dataBooklyCustomer = array(
                    'id' => false,
                    'wp_user_id' => $user,
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'full_name' => $fullName,
                    'phone' => $userData['phone_number'],
                    'email' => $userData['email'],
                    'city' => $userData['city']
                );
                if ( !cutawayRegisterBookingCustomer($dataBooklyCustomer) ) {
                    $result_content['type'] = 'danger';
                    $result_content['content'] = __('Registrati utente fallito.', 'cutaway');

                    wp_delete_user($user);
                }
                else {
                    wp_redirect( home_url() );
                    exit;
                }
            }
        }
    }
}

function cutawayRegisterBookingCustomer($data)
{
    if (!class_exists('\\BooklyAdapter\\Entities\\Customer')) {
        return true;
    }

    $booklyCustomerAdapter = \BooklyAdapter\Entities\Customer::getInstance();

    if ($booklyCustomerAdapter->checkBooklyActivated()) {
        return $booklyCustomerAdapter->updateCustomer($data);
    }

    return true;
}
/*
 * End - Register User
 */

/*
 * Change Password
 */
add_action('init', 'cutaway_change_password_user');
function cutaway_change_password_user() {
    if (is_user_logged_in() && isset($_POST['form_change_password_submit'])) {
        global $result_content;
        $result_content = [
            'type' => 'info',
            'content' => __('Cambia password correttamente.', 'cutaway')
        ];

        $old_password = !empty($_POST['old_password']) ? $_POST['old_password'] : '';
        $new_password = !empty($_POST['new_password']) ? $_POST['new_password'] : '';

        if (empty($old_password) || empty($new_password)) {
            $result_content['type'] = 'danger';
            $result_content['content'] = __('Cambia password utente fallita. Dati mancanti.', 'cutaway');
        }

        $newUserData = array(
            'old_password' => $old_password,
            'new_password' => $new_password
        );

        if (class_exists('\\Includes\\Functions\\WordpressInformationUserFunction')) {
            $obj = new \Includes\Functions\WordpressInformationUserFunction();
            $result = $obj->changeCurrentUserPassword($newUserData);

            if (is_wp_error($result)) {
                $code = $result->get_error_code();

                $result_content['type'] = 'danger';
                if ($code == 'old_password_not_match') {
                    $result_content['content'] = __('Cambia password utente fallita. La vecchia password non corrisponde.', 'cutaway');
                } else {
                    $result_content['content'] = __('Cambia password utente fallita.', 'cutaway');
                }
            }
        } else {
            $result_content['type'] = 'danger';
            $result_content['content'] = __('Cambia password utente fallita.', 'cutaway');
        }
    }
}
/*
 * End - Change Password
 */

/*
 * Update account
 */
add_action('init', 'cutaway_update_account', 10, 0);
function cutaway_update_account() {
    if (is_user_logged_in() && isset($_POST['form_update_account_submit'])) {
        global $result_content;
        $result_content = [
            'type' => 'info',
            'content' => __('Aggiorna account con successo.', 'cutaway')
        ];

        $currentUser = wp_get_current_user();

        $first_name = !empty($_POST['user_firstname']) ? $_POST['user_firstname'] : '';
        $last_name = !empty($_POST['user_lastname']) ? $_POST['user_lastname'] : '';
        $email = !empty($_POST['user_email']) ? $_POST['user_email'] : '';
        $phone_number = !empty($_POST['user_phone']) ? $_POST['user_phone'] : '';
        $city = !empty($_POST['user_city']) ? $_POST['user_city'] : '';

        if (empty($first_name) || empty($last_name) || empty($email)) {
            $result_content['type'] = 'danger';
            $result_content['content'] = __('Aggiornamento dell\'account non riuscito. Dati mancanti.', 'cutaway');
        } else {
            $newUserData = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone_number' => $phone_number,
                'city' => $city
            );

            if ( !is_email($newUserData['email']) ) {
                $result_content['type'] = 'danger';
                $result_content['content'] = __('Aggiornamento dell\'account non riuscito. Email non valida.', 'cutaway');
            }
            else if ( email_exists($newUserData['email']) && $newUserData['email'] != $currentUser->user_email ) {
                $result_content['type'] = 'danger';
                $result_content['content'] = __('Aggiornamento dell\'account non riuscito. E-mail esistita.', 'cutaway');
            }
            else {
                if (class_exists('\\Includes\\Functions\\WordpressInformationUserFunction')) {
                    $obj = new \Includes\Functions\WordpressInformationUserFunction();
                    $result = $obj->updateCurrentUserInformation($newUserData);

                    if (is_wp_error($result)) {
                        $result_content['type'] = 'danger';
                        $result_content['content'] = __('Aggiornamento dell\'account non riuscito.', 'cutaway');
                    }
                } else {
                    $result_content['type'] = 'danger';
                    $result_content['content'] = __('Aggiornamento dell\'account non riuscito.', 'cutaway');
                }
            }
        }
    }
}
/*
 * End - Update account
 */

/*
 * Get information of user and add to global variable
 */
add_action('init', 'cutaway_get_user_infomation', 99, 0);
function cutaway_get_user_infomation() {
    if ( is_user_logged_in() ) {
        global $user_info;
        $user_info = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
            'city' => ''
        ];

        if (class_exists('\\Includes\\Functions\\WordpressInformationUserFunction')) {
            $obj = new \Includes\Functions\WordpressInformationUserFunction();
            $currentUserInfo = $obj->loadCurrentUserInformation();
            $user_info = array_merge($user_info, $currentUserInfo);
        } else {
            global $current_user;
            get_currentuserinfo();

            $user_info['first_name'] = $current_user->user_firstname;
            $user_info['last_name'] = $current_user->user_lastname;
            $user_info['email'] = $current_user->user_email;

            $customer_info = cutaway_loadBookingCustomer($current_user->ID);
            if (!empty($customer_info)) {
                $user_info['phone'] = $customer_info['phone'];
                $user_info['city'] = $customer_info['city'];
            }
        }
    }
}

function cutaway_loadBookingCustomer($wpUserId)
{
    if (!class_exists('\\BooklyAdapter\\Entities\\Customer')) {
        return array();
    }

    $data = array();
    $booklyCustomerAdapter = \BooklyAdapter\Entities\Customer::getInstance();

    if ($booklyCustomerAdapter->checkBooklyActivated()) {
        $booklyCustomer = $booklyCustomerAdapter->loadCustomerBy(array(
            'wp_user_id' => $wpUserId
        ));
        if ($booklyCustomer) {
            $data = array(
                'id' => $booklyCustomer->getId(),
                'phone' => $booklyCustomer->getPhone(),
                'city' => $booklyCustomer->getCity()
            );

            return $data;
        }
    }

    return $data;
}
/*
 * End - Get information of user and add to global variable
 */

// Redirect to FrontEnd login page when login failed
add_action( 'wp_login_failed', 'cutaway_front_end_login_fail' );  // hook failed login
function cutaway_front_end_login_fail( $username ) {
    $request = $_SERVER['REQUEST_URI'];
    // if is not processing REST request
    if (empty($request) || strpos($request, '/wp-json/') === false) {
        $referrer = home_url("/");  // where did the post submission come from?
        // if there's a valid referrer, and it's not the default log-in screen
        if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
            wp_redirect( $referrer . '?login=failed' );  // let's append some information (login=failed) to the URL for the theme to use
            exit;
        }
    }
}

add_action( 'after_setup_theme', 'cutaway_theme_setup' );
function cutaway_theme_setup() {
    add_theme_support( 'post-thumbnails');
}

// Ajax - Result Find barber
add_action('wp_ajax_find_barber_filter', 'find_barber_filter');
add_action('wp_ajax_nopriv_find_barber_filter', 'find_barber_filter');

function find_barber_filter() {
    $location = $_GET['location'];
    $date_new = $_GET['date'];
    $services_str = $_GET['services'];
    $sort_type = $_GET['sort_type'];
    $order = $_GET['order'];

    $request = new WP_REST_Request( 'GET', 'cutaway/v1/shop/searchStaffMember' );

    if ( $sort_type == "alphabet" ) {
        $request->set_query_params(array(
            'location' => $location,
            'service' => $services_str,
            'work_time' => $date_new,
            'orderby' => $sort_type,
            'order' => $order,
        ));
    }
    else {
        $request->set_query_params(array(
            'location' => $location,
            'service' => $services_str,
            'work_time' => $date_new,
        ));
    }

    ob_start();

    if ( class_exists('REST_Shop_Controller') ) {
        $controller = new REST_Shop_Controller();
        $response = $controller->searchShop($request);
        // var_dump($response);

        if ( $response->data['success'] && count($response->data['StaffMembers']) > 0 ) {
            $barber_list = $response->data['StaffMembers'];
            foreach ($barber_list as $barber) {

                // include(get_template_directory() . '/loop-templates/find_barber_result.php');

                $params = [
                    'url' => esc_url_raw(add_query_arg(array('staff' => $barber['id']), home_url() . '/barber-detail' )),
                    'avatar' => $barber['avatar'],
                    'full_name' => $barber['full_name'],
                    'location' => $barber['location'],
                    'price' => __("Capelli | Barba", "cutaway") . " | &euro;&euro;&euro;",
                    'starNumber' => 4,
                    'ratings' => 13
                ];
                echo cutaway_barber_detail_box($params);
            }
        }
        else {
            echo "No result";
        }
    }
    else {
        echo "No result";
    }

    $output = ob_get_contents();
    ob_end_clean();

    die(json_encode(array(
        'data' => $output
    )));

}

function cutaway_formatDateTime($datetime) {
    $date = explode('/', $datetime);
    $months = [
        '01' => 'Gennaio',
        '02' => 'Febbraio',
        '03' => 'Marzo',
        '04' => 'Aprile',
        '05' => 'Maggio',
        '06' => 'Giugno',
        '07' => 'Luglio',
        '08' => 'Agosto',
        '09' => 'Settembre',
        '10' => 'Ottobre',
        '11' => 'Novembre',
        '12' => 'Dicembre',
    ];
    $date[1] = $months[$date[1]];
    return $date;
}

// start session if not already started
function cutaway_start_session() {
    if(!session_id()) {
        session_start();
    }
}
add_action('init', 'cutaway_start_session', 1);

add_action('wp_ajax_pay_with_stripe', 'cutawayPayWithStripe');
function cutawayPayWithStripe() {
    $token = !empty($_POST['token']) ? $_POST['token'] : '';
    $group = !empty($_POST['group']) ? $_POST['group'] : '';
    $result = array(
        'status' => 'fail'
    );

    if (
        class_exists('\\Includes\\Classes\\StripePayment') &&
        class_exists('\\BooklyAdapter\\Classes\\AppointmentGroup') &&
        class_exists('\\BooklyAdapter\\Entities\\Customer') &&
        !empty($group) &&
        !empty($token)
    ) {
        $customerAppointmentGroupAdapter = \BooklyAdapter\Classes\AppointmentGroup::getInstance();
        $customerBooklyAdapter = \BooklyAdapter\Entities\Customer::getInstance();

        $customerId = $customerBooklyAdapter->getCustomerIdFromUserLogged();
        if (!empty($customerId)) {
            $customerBooking = $customerAppointmentGroupAdapter->getCustomerAppointment($customerId, $group);
            if (!empty($customerBooking) && $customerBooking['status'] == 'pending') {
                $dataBook = cutawayGenerateDataBookFromAppointmentGroup($customerBooking);
                if (!empty($dataBook)) {
                    $controller = new \Includes\Classes\StripePayment();
                    $restService = $controller->getRestServicePayment();
                    $request = new WP_REST_Request($restService['method'], $restService['url']);

                    $description = cutawayGenerateDescriptionPayment($dataBook);
                    $price = $dataBook['price'];

                    if ($price && !empty($description)) {
                        $datas = array(
                            'token' => $token,
                            'amount' => $price,
                            'currency' => cutawayGetBooklyCurrencySetting(),
                            'description' => $description,
                            'sku' => 'booking',
                            'group' => $group
                        );

                        foreach ($datas as $key => $value) {
                            $request->set_param($key, $value);
                        }

                        $response = $controller->stripePayment($request);
                        if (!is_wp_error($response)) {
                            $result['status'] = $response->data['status'] == 'ok' ? 'ok' : 'fail';
                        }
                    }
                }
            }
        }
    }

    die(json_encode($result));
}

add_action('wp_ajax_create_pending_booking', 'cutawayCreatePendingBooking');
function cutawayCreatePendingBooking() {
    $dataBook = cutawayGetCustomerDataActiveBook();
    $result = array(
        'status' => 'fail'
    );
    $encryptObj = new CutawayEncryptDecrypt();

    if (
        class_exists('\\Includes\\Classes\\BaseRestPayments') &&
        class_exists('\\BooklyAdapter\\Entities\\Customer') &&
        !empty($dataBook) &&
        cutawayGetCurrentUserBooklyType() == 'customer'
    ) {
        $customerBooklyAdapter = \BooklyAdapter\Entities\Customer::getInstance();
        $customerId = $customerBooklyAdapter->getCustomerIdFromUserLogged();
        extract($dataBook);

        if (!empty($customerId) && !empty($price)) {
            $controller = new \Includes\Classes\BaseRestPayments();
            $result = $controller->processCreatePendingBooking(array(
                'location' => $location,
                'services' => $services,
                'staff' => $staff,
                'date' => $date,
                'time' => $time
            ));
            if (is_wp_error($result)) {
                $result = array(
                    'status' => 'fail',
                    'group' => 0
                );
            } elseif ($result['status'] == 'ok') {
                cutawayRemoveCustomerDataActiveBook();
                unset($_SESSION['list_barbers_found']);

                $tokenSendPaypal = $customerId . ';' . $result['group'];
                $tokenSendPaypal = $encryptObj->encrypt($tokenSendPaypal);
                $result['paypal_notify_link'] = cutaway_generate_http_cron_link('handle_paypal_ipn', array(
                    '_encrypt_token' => urlencode($tokenSendPaypal)
                ));
            }
        }
    }

    die(json_encode($result));
}

add_action('wp_ajax_mark_staff_favourite', 'cutawayMarkStaffFavourite');
function cutawayMarkStaffFavourite() {
    $staff = !empty($_POST['staff']) ? $_POST['staff'] : '';

    $result = array(
        'status' => 'fail'
    );

    if (
        class_exists('Booky_Extras_REST_Posts_Controller') &&
        !empty($staff)
    ) {
        $controller = new Booky_Extras_REST_Posts_Controller();
        $restService = $controller->getRestServiceStaffFavorite();
        $request = new WP_REST_Request($restService['method'], $restService['url']);
        $request->set_param('staff_id', $staff);

        $response = $controller->favorite($request);
        $result['status'] = !empty($response->data['success']) && $response->data['success'] ? 'ok' : 'fail';
    }

    die(json_encode($result));
}

add_action('wp_ajax_mark_staff_unfavourite', 'cutawayMarkStaffUnfavourite');
function cutawayMarkStaffUnfavourite() {
    $staff = !empty($_POST['staff']) ? $_POST['staff'] : '';

    $result = array(
        'status' => 'fail'
    );

    if (
        class_exists('Booky_Extras_REST_Posts_Controller') &&
        !empty($staff)
    ) {
        $controller = new Booky_Extras_REST_Posts_Controller();
        $restService = $controller->getRestServiceStaffUnfavorite();
        $request = new WP_REST_Request($restService['method'], $restService['url']);
        $request->set_param('staff_id', $staff);

        $response = $controller->un_favorite($request);
        $result['status'] = !empty($response->data['success']) && $response->data['success'] ? 'ok' : 'fail';
    }

    die(json_encode($result));
}

add_action('wp_ajax_generate_list_services_html', 'cutawayGenerateListServicesHtml');
function cutawayGenerateListServicesHtml() {
    $order = !empty($_GET['order']) ? $_GET['order'] : '';

    if (
        class_exists('REST_Services_Controller')
    ) {
        $controller = new REST_Services_Controller();
        $services = $controller->getListServices(array(
            'order' => $order
        ));

        if (!empty($services)) {
            ?>
            <div class="services"> <?php
                $i = 0;
                foreach ($services as $value) { ?>
                    <div class="service-item <?php echo $i%2 == 0 ? 'even' : 'odd'; ?> service-item-<?php echo $value['id']; ?>">
                        <div class="service-item-thumb" style="background: url(<?php echo $value['image']; ?>) no-repeat"></div>
                        <h3 class="service-title"><?php echo $value['title']; ?></h3>
                        <div class="service-content"><?php echo $value['info']; ?></div>
                        <a href="#" class="service-book" service-title="<?php echo $value['title']; ?>" service-id="<?php echo $value['id']; ?>"><?php echo __('Prenota', 'cutaway'); ?></a>
                    </div> <?php
                    $i++;
                }
            ?>
            </div>
            <?php
        }
    }

    exit;
}

add_action('wp_ajax_change_barber_available_booking', 'cutawayChangeBarberAvailableBooking');
function cutawayChangeBarberAvailableBooking() {
    $state = strlen($_POST['state']) ? $_POST['state'] : '';
    $result = array(
        'status' => 'fail'
    );

    if (
        class_exists('\\BooklyAdapter\\Entities\\Staff') &&
        strlen($state)
    ) {
        $booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
		$currentBarberId = $booklyStaffAdapter->getStaffIdFromUserLogged();
        $state = is_numeric($state) ? $state : 1;
        $state = $state ? 'public' : 'private';

        if ($booklyStaffAdapter->updateStaff(array(
            'id' => $currentBarberId,
            'visibility' => $state
        ))) {
            $result['status'] = 'ok';
        }
    }

    die(json_encode($result));
}

add_action('wp_ajax_get_staff_config_available_time_slots', 'cutawayGetStaffConfigAvailableTimeSlots');
function cutawayGetStaffConfigAvailableTimeSlots() {
    $date = !empty($_GET['date']) ? $_GET['date'] : '';
    $services = !empty($_GET['services']) ? $_GET['services'] : '';

    $result = array(
        'status' => 'fail',
        'time' => ''
    );

    if (
        class_exists('REST_Shop_Controller') &&
        class_exists('\\BooklyAdapter\\Entities\\Staff') &&
        !empty($date) &&
        !empty($services)
    ) {
        $booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
        $staff = $booklyStaffAdapter->getStaffIdFromUserLogged();

        if (!empty($staff)) {
            $controller = new REST_Shop_Controller();
            $timeSlots = $controller->processGetStaffConfigAvailableTimeSlots($staff, $services, $date);
            $result['status'] = 'ok';

            if (!empty($timeSlots)) {
                $count = count($timeSlots);
                $i = 1;

                $timeSlotsHtml = '<div class="wrap-slots"><div class="slots-time">';
                foreach ($timeSlots as $time) {
                    $cls = $time['selected'] ? ' selected' : '';
                    $timeSlotsHtml .= '<div class="time' . $cls . '">' . $time['start_time'] . '</div>';
                    if ( $i%4 == 0 && $i != $count ) {
                        $timeSlotsHtml .= '</div><div class="slots-time">';
                    }
                    $i++;
                }
                $timeSlotsHtml .= '</div></div>';

                $result['time'] = $timeSlotsHtml;
            }
        }
    }

    die(json_encode($result));
}

add_action('wp_ajax_set_staff_config_available_time_slots', 'cutawaySetStaffConfigAvailableTimeSlots');
function cutawaySetStaffConfigAvailableTimeSlots() {
    $date = !empty($_POST['date']) ? $_POST['date'] : '';
    $services = !empty($_POST['services']) ? $_POST['services'] : '';
    $unAvaiTimeSlots = !empty($_POST['un_available_time']) ? $_POST['un_available_time'] : '';

    $result = array(
        'status' => 'fail'
    );

    if (
        class_exists('REST_Shop_Controller') &&
        class_exists('\\BooklyAdapter\\Entities\\Staff') &&
        !empty($date) &&
        !empty($services)
    ) {
        $booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
        $staff = $booklyStaffAdapter->getStaffIdFromUserLogged();

        if (!empty($staff)) {
            $controller = new REST_Shop_Controller();
            $unAvaiTimeSlots = !empty($unAvaiTimeSlots) ? explode(',', $unAvaiTimeSlots) : array();
            if ($controller->processSetStaffConfigAvailableTimeSlots($staff, $services, $date, $unAvaiTimeSlots)) {
                $result = array(
                    'status' => 'ok'
                );
            }
        }
    }

    die(json_encode($result));
}

if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title'  => __('Theme Options', 'cutaway'),
        'menu_title'  => __('Theme Options', 'cutaway'),
        'menu_slug'   => 'theme-options',
        'capability'  => 'edit_posts',
        'parent_slug' => '',
        'position'    => false,
        'icon_url'    => false,
        'redirect'    => false
    ));
}

if (!class_exists('CutawayEncryptDecrypt')) {
    class CutawayEncryptDecrypt
    {
        protected $method = 'AES-128-CTR'; // default cipher method if none supplied
        private $key;

        protected function iv_bytes()
        {
            return openssl_cipher_iv_length($this->method);
        }

        public function __construct($key = false)
        {
            if(!$key) {
                $key = php_uname(); // default encryption key if none supplied
            }
            if(ctype_print($key)) {
                // convert ASCII keys to binary format
                $this->key = openssl_digest($key, 'SHA256', true);
            } else {
                $this->key = $key;
            }
        }

        public function encrypt($data)
        {
            $iv = openssl_random_pseudo_bytes($this->iv_bytes());
            return bin2hex($iv) . openssl_encrypt($data, $this->method, $this->key, 0, $iv);
        }

        // decrypt encrypted string
        public function decrypt($data)
        {
            $iv_strlen = 2  * $this->iv_bytes();

            if(preg_match("/^(.{" . $iv_strlen . "})(.+)$/", $data, $regs)) {
                list(, $iv, $crypted_string) = $regs;
                if(ctype_xdigit($iv) && strlen($iv) % 2 == 0) {
                    return openssl_decrypt($crypted_string, $this->method, $this->key, 0, hex2bin($iv));
                }
            }
            return false; // failed to decrypt
        }
    }
}
add_action('admin_menu', 'remove_admin_menu_links');
function remove_admin_menu_links(){
    $user = wp_get_current_user();
    if( $user && in_array( 'admin_no_bookly', (array) $user->roles )) {
        remove_menu_page('bookly-menu');

    }
}