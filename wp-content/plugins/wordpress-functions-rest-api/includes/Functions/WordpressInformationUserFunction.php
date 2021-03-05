<?php

namespace Includes\Functions;

use \Includes\Interfaces\WordpressRestFunction;
use \Includes\Classes\WordpressBaseRestFunctions;

use \WP_REST_Server as WP_REST_Server;
use \WP_Error as WP_Error;

class WordpressInformationUserFunction extends WordpressBaseRestFunctions implements WordpressRestFunction
{
    protected $debugOptionName = '_wordpress_information_user_rest_function';

    public function __construct()
    {

    }

    public function registerRestApis()
    {
        add_action('rest_api_init', array($this, 'registerRestFunctions'));
        add_action('rest_api_init', array($this, 'registerTestRestFunctions'));
        add_filter('jwt_auth_token_before_dispatch', array($this, 'addUserTypeToAuthenticatedToken'), 99, 2);
    }

    public function registerRestFunctions()
    {
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/get-user-information/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'getUserInformation'),
            'permission_callback' => array( $this, 'get_items_permissions_check' )
        ));
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/update-user-information/', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'updateUserInformation'),
            'permission_callback' => array( $this, 'get_items_permissions_check' )
        ));
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/change-user-password/', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'changeUserPassword'),
            'permission_callback' => array( $this, 'get_items_permissions_check' )
        ));
    }

    public function registerTestRestFunctions()
    {
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/get-user-information/test/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'testGetUserInformation')
        ));
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/update-user-information/test/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'testUpdateUserInformation')
        ));
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/change-user-password/test/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'testChangeUserPassword')
        ));
    }

    public function testGetUserInformation($request)
    {
        return rest_ensure_response(array(
            'status' => 'enabled'
        ));
    }
    public function testUpdateUserInformation($request)
    {
        return rest_ensure_response(array(
            'status' => 'enabled'
        ));
    }
    public function testChangeUserPassword($request)
    {
        return rest_ensure_response(array(
            'status' => 'enabled'
        ));
    }

    public function getUserInformation($request)
    {
        if ($this->needValidateNonce && !$this->validateRequest($request, 'get-user-information')) {
            $this->logDebug('validate request fail');
            /*return new WP_Error('validate_fail', 'validate_fail',
            array(
                'status' => 403
            ));*/
            return wordpress_rest_format_response_fail( 'validate_fail' );
        }

        $userInformation = $this->loadCurrentUserInformation();

        //return rest_ensure_response($userInformation);
        return wordpress_rest_format_response_success( $userInformation );
    }

    public function updateUserInformation($request)
    {
        if ($this->needValidateNonce && !$this->validateRequest($request, 'update-user-information')) {
            $this->logDebug('validate request fail');
            /*return new WP_Error('validate_fail', 'validate_fail',
            array(
                'status' => 403
            ));*/
            return wordpress_rest_format_response_fail( 'validate_fail' );
        }

        $currentUser = wp_get_current_user();

        $newUserInformation = array(
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email']
        );
        $newUserInformation = array_filter($newUserInformation);

        if (count($newUserInformation) != 3) {
            $debug = array(
                'info' => 'new user information missing',
                'data' => $newUserInformation
            );
            $this->logDebug($debug);
            /*return new WP_Error('data_missing', 'data_missing',
            array(
                'status' => 403
            ));*/
            return wordpress_rest_format_response_fail( 'data_missing' );
        }

        if (!is_email($newUserInformation['email'])) {
            $debug = array(
                'info' => 'new user information email invalid',
                'data' => $newUserInformation
            );
            $this->logDebug($debug);
            /*return new WP_Error('email_invalid', 'email_invalid',
            array(
                'status' => 403
            ));*/
            return wordpress_rest_format_response_fail( 'email_invalid' );
        }

        if (email_exists($newUserInformation['email'])) {
            if ($newUserInformation['email'] != $currentUser->user_email) {
                $debug = array(
                    'info' => 'new user information email existed',
                    'data' => $newUserInformation
                );
                $this->logDebug($debug);
                /*return new WP_Error('email_existed', 'email_existed',
                array(
                    'status' => 403
                ));*/
                return wordpress_rest_format_response_fail( 'email_existed' );
            }
        }

        $newUserInformation = array_merge($newUserInformation, array(
            'phone_number' => $request['phone_number'],
            'city' => $request['city']
        ));

        $result = $this->updateCurrentUserInformation($newUserInformation);
        if (is_wp_error($result)) {
            $debug = array(
                'info' => 'update user fail',
                'data' => $newUserInformation,
                'error' => $result->get_error_code(),
                'message' => $result->get_error_message()
            );
            $this->logDebug($debug);
            /*return new WP_Error('update_fail', 'update_fail',
            array(
                'status' => 403
            ));*/
            return wordpress_rest_format_response_fail( 'update_fail' );
        }

        $newUserInformation = array(
            'user_email' => $newUserInformation['email'],
            'user_nicename' => '',
            'user_display_name' => $newUserInformation['first_name'] . ' ' . $newUserInformation['last_name']
        );

        //return rest_ensure_response($newUserInformation);
        return wordpress_rest_format_response_success( $newUserInformation );
    }

    public function changeUserPassword($request)
    {
        if ($this->needValidateNonce && !$this->validateRequest($request, 'change-user-password')) {
            $this->logDebug('validate request fail');
            /*return new WP_Error('validate_fail', 'validate_fail',
            array(
                'status' => 403
            ));*/
            return wordpress_rest_format_response_fail( 'validate_fail' );
        }

        $userPassInfomation = array(
            'old_password' => $request['old_password'],
            'new_password' => $request['new_password']
        );
        $userPassInfomation = array_filter($userPassInfomation);

        if (count($userPassInfomation) != 2) {
            $debug = array(
                'info' => 'user password information missing',
                'data' => $userPassInfomation
            );
            $this->logDebug($debug);
            /*return new WP_Error('data_missing', 'data_missing',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'data_missing' );
        }

        $result = $this->changeCurrentUserPassword($userPassInfomation);
        if (is_wp_error($result)) {
            $debug = array(
                'info' => 'change user password fail',
                'data' => $userPassInfomation,
                'error' => $result->get_error_code(),
                'message' => $result->get_error_message()
            );
            $this->logDebug($debug);
            if ($debug['error'] == 'old_password_not_match') {
                /*return new WP_Error('old_pass_not_match', 'old_pass_not_match',
                array(
                    'status' => 403
                ));*/
                return wordpress_rest_format_response_fail( 'old_pass_not_match' );
            } else {
                /*return new WP_Error('change_fail', 'change_fail',
                array(
                    'status' => 403
                ));*/
                return wordpress_rest_format_response_fail( 'change_fail' );
            }
        }

        /*return rest_ensure_response(array(
            'status' => 'ok'
        ));*/
        return wordpress_rest_format_response_success();
    }

    public function loadCurrentUserInformation()
    {
        $currentUser = wp_get_current_user();
        if (empty($currentUser->ID)) {
            return array();
        }

        $userInformation = array(
            'email' => $currentUser->user_email,
            'first_name' => $currentUser->user_firstname,
            'last_name' => $currentUser->user_lastname,
            'phone' => '',
            'city' => ''
        );
        $bookingCustomer = $this->loadBookingCustomer($currentUser->ID);
        if (!empty($bookingCustomer)) {
            unset($bookingCustomer['id']);
        }
        $userInformation = array_merge($userInformation, $bookingCustomer);

        $userInformation['is_social_user'] = $this->detechUserIsSocialUser($currentUser->ID);

        $userInformation['type'] = $this->determineUserType();

        return $userInformation;
    }

    public function updateCurrentUserInformation($data)
    {
        $currentUser = wp_get_current_user();
        if (empty($currentUser->ID)) {
            return new WP_Error('current_user_empty', __('Utente non trovato', 'cutaway'));
        }

        if (empty($data)) {
            return new WP_Error('data_empty', __('Dati mancanti', 'cutaway'));
        }

        $fullName = $data['first_name'] . ' ' . $data['last_name'];
        $oldFullName = $currentUser->user_firstname . ' ' . $currentUser->user_lastname;

        $newWordpressUserData = array(
            'ID' => $currentUser->ID,
            'user_email' => $data['email'],
            'display_name' => $fullName
        );
        $oldWordpressUserData = array(
            'ID' => $currentUser->ID,
            'user_email' => $currentUser->user_email,
            'display_name' => $oldFullName
        );
        $user = wp_update_user($newWordpressUserData);
        if (is_wp_error($user)) {
            return $user;
        }

        update_user_meta($user, 'first_name', $data['first_name']);
        update_user_meta($user, 'last_name', $data['last_name']);

        $dataBooklyCustomer = array(
            'wp_user_id' => $user,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'full_name' => $fullName,
            'phone' => $data['phone_number'],
            'email' => $data['email'],
            'city' => $data['city']
        );
        $bookingCustomer = $this->loadBookingCustomer($user);
        $dataBooklyCustomer['id'] = empty($bookingCustomer) ? false : $bookingCustomer['id'];
        if (!$this->updateBookingCustomer($dataBooklyCustomer)) {
            wp_update_user($oldWordpressUserData);
            update_user_meta($currentUser->ID, 'first_name', $currentUser->user_firstname);
            update_user_meta($currentUser->ID, 'last_name', $currentUser->user_lastname);

            return new WP_Error('update_bookly_fail', __('Aggiornamento dell\'account non riuscito.', 'cutaway'));
        }

        return true;
    }

    public function changeCurrentUserPassword($data)
    {
        $currentUser = wp_get_current_user();
        if (empty($currentUser->ID)) {
            return new WP_Error('current_user_empty', __('Utente non trovato', 'cutaway'));
        }

        if (empty($data)) {
            return new WP_Error('data_empty', __('Dati mancanti', 'cutaway'));
        }

        if (!wp_check_password( $data['old_password'], $currentUser->user_pass, $currentUser->ID)) {
            return new WP_Error('old_password_not_match', __('La vecchia password non corrisponde.', 'cutaway'));
        }

        $newWordpressUserData = array(
            'ID' => $currentUser->ID,
            'user_pass' => $data['new_password']
        );

        $user = wp_update_user($newWordpressUserData);
        if (is_wp_error($user)) {
            return $user;
        }

        return true;
    }

    public function addUserTypeToAuthenticatedToken($data, $user)
    {
        if (!empty($user->data->ID)) {
            $data = array_merge( $data, $this->determineUserType($user->data->ID) );
        }

        return $data;
    }

    private function determineUserType($user = false)
    {
        $userType = array(
            'type' => '',
            'type_id' => 0
        );

        if (empty($user)) {
            $user = wp_get_current_user();
            $user = !empty($user->ID) ? $user->ID : false;
        }

        if (!empty($user) && $this->checkBooklyAdapterActivated()) {
            $booklyCustomerAdapter = \BooklyAdapter\Entities\Customer::getInstance();
            $customerId = $booklyCustomerAdapter->getCustomerIdFromUser($user);

            $booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
            $staffId = $booklyStaffAdapter->getStaffIdFromUser($user);

            if (!empty($customerId)) {
                $userType['type'] = 'customer';
				$userType['type_id'] = $customerId;
            } elseif (!empty($staffId)) {
                $userType['type'] = 'staff';
                $userType['type_id'] = $staffId;
            }
        }

        return $userType;
    }

    private function loadBookingCustomer($wpUserId)
    {
        $data = array();

        if (!$this->checkBooklyAdapterActivated()) {
            return $data;
        }

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

    private function updateBookingCustomer($data)
    {
        if (!$this->checkBooklyAdapterActivated()) {
            return true;
        }

        $booklyCustomerAdapter = \BooklyAdapter\Entities\Customer::getInstance();

        if ($booklyCustomerAdapter->checkBooklyActivated()) {
            return $booklyCustomerAdapter->updateCustomer($data);
        }

        return true;
    }
}