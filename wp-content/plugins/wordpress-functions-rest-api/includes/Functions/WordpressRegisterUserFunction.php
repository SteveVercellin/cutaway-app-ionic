<?php

namespace Includes\Functions;

use \Includes\Interfaces\WordpressRestFunction;
use \Includes\Classes\WordpressBaseRestFunctions;

use \WP_REST_Server as WP_REST_Server;
use \WP_Error as WP_Error;

use \BooklyAdapter\Entities\Customer;

class WordpressRegisterUserFunction extends WordpressBaseRestFunctions implements WordpressRestFunction
{
    protected $debugOptionName = '_wordpress_register_user_rest_function';

    public function __construct()
    {

    }
    public function registerRestApis()
    {
        add_action('rest_api_init', array($this, 'registerRestFunctions'));
        add_action('rest_api_init', array($this, 'registerTestRestFunctions'));
    }

    public function registerRestFunctions()
    {
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/register/', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'registerNewUser')
        ));
    }

    public function registerTestRestFunctions()
    {
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/register/test/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'testRegisterNewUser')
        ));
    }

    public function testRegisterNewUser($request)
    {
        return rest_ensure_response(array(
            'status' => 'enabled'
        ));
    }

    public function registerNewUser($request)
    {
        if ($this->needValidateNonce && !$this->validateRequest($request, 'register')) {
            $this->logDebug('validate request fail');
            /*return new WP_Error('validate_fail', 'validate_fail',
            array(
                'status' => 403
            ));*/
            return wordpress_rest_format_response_fail( 'validate_fail' );
        }

        $userData = array(
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'password' => $request['password']
        );
        $userData = array_filter($userData);

        if (count($userData) != 4) {
            $debug = array(
                'info' => 'validate request fail',
                'data' => $userData
            );
            $this->logDebug($debug);
            /*return new WP_Error('data_missing', 'data_missing',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'data_missing' );
        }

        $userData = array_merge($userData, array(
            'phone_number' => !empty( $request['phone_number'] ) ? $request['phone_number'] : '',
            'city' => $request['city']
        ));

        if (!is_email($userData['email'])) {
            $debug = array(
                'info' => 'email invalid',
                'data' => $userData
            );
            $this->logDebug($debug);
            /*return new WP_Error('email_invalid', 'email_invalid',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'email_invalid' );
        }

        if (email_exists($userData['email'])) {
            $debug = array(
                'info' => 'email existed',
                'data' => $userData
            );
            $this->logDebug($debug);
            /*return new WP_Error('email_existed', 'email_existed',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'email_existed' );
        }

        $fullName = $userData['first_name'] . ' ' . $userData['last_name'];

        $user = wp_insert_user(array(
            'user_login' => $userData['email'],
            'user_email' => $userData['email'],
            'display_name' => $fullName,
            'user_pass' => $userData['password'],
            'role' => 'subscriber'
        ));

        if (is_wp_error($user)) {
            $debug = array(
                'info' => 'wordpress create user fail',
                'data' => $userData,
                'error' => $user->get_error_message()
            );
            $this->logDebug($debug);
            /*return new WP_Error('register_failed', 'register_failed',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'register_failed' );
        }

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
        if (!$this->registerBookingCustomer($dataBooklyCustomer)) {
            $debug = array(
                'info' => 'booky create customer fail',
                'data' => $dataBooklyCustomer,
                'error' => $user->get_error_message()
            );
            $this->logDebug($debug);

            wp_delete_user($user);

            /*return new WP_Error('register_failed', 'register_failed',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'register_failed' );
        }

        $user = get_user_by('ID', (int) $user);
        $dataAuthenticated = $this->generateAuthenticationToken($user);

        //return rest_ensure_response($dataAuthenticated);
        return wordpress_rest_format_response_success( $dataAuthenticated );
    }

    private function registerBookingCustomer($data)
    {
        $booklyCustomerAdapter = Customer::getInstance();

        if ($booklyCustomerAdapter->checkBooklyActivated()) {
            return $booklyCustomerAdapter->updateCustomer($data);
        }

        return true;
    }
}