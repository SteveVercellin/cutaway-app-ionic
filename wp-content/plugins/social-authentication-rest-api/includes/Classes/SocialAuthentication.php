<?php

namespace Includes\Classes;

use \Firebase\JWT\JWT as SocialJWT;

use \WP_REST_Server as WP_REST_Server;
use \WP_REST_Response as WP_REST_Response;
use \WP_Error as WP_Error;

use \BooklyAdapter\Entities\Customer;

class SocialAuthentication
{
    private $prefix = 'social-authenticate-rest';
    private $restVersion = 'v1';
    private static $instance = null;
    private $debugOptionName = '_social_authenticate_rest_debug';
    private $errorAuthenticateOptionName = '_social_authenticate_rest_error';

    protected $defaultNewUserPass = 'ZyWnXG5p*+MXjgs*';
    protected $prefixWordpressAction = 'social_authenticate_rest_action';
    protected $prefixWordpressFilter = 'social_authenticate_rest_filter';

    protected $needValidateNonce = false;

    private function __construct()
    {
        if ($this->checkConditionToPluginCanWork()) {
            add_action('rest_api_init', array($this, 'registerRestApi'));
        }
    }

    public function registerRestApi()
    {
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/get-nonce-action/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'getNonceAction')
        ));
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/authenticate/', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'authenticateFromSocial')
        ));
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/log-authenticate-fail/', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'logAuthenticateFromSocial')
        ));
    }

    public function getNonceAction($request)
    {
        $social = $request->get_param('social');
        $action = $request->get_param('action');
        $nonceAction = $this->generateNonceAction($social, $action);

        if (is_wp_error($nonceAction)) {
            /*$nonceAction->add_data(array(
                'status' => 403
            ), 'get_nonce_fail');

            return $nonceAction;*/
            return wordpress_rest_format_response_fail( 'get_nonce_fail' );
        }

        /*return new WP_REST_Response(array(
            'nonce' => $nonceAction
        ), 200);*/

        return wordpress_rest_format_response_success( $nonceAction );
    }

    public function authenticateFromSocial($request)
    {
        $social = $request->get_param('social');
        if (empty($social)) {
            /*return new WP_Error(
                'authenticate_fail',
                __('Missing social param.', 'social-auth'),
                array(
                    'status' => 403
                )
            );*/
            return wordpress_rest_format_response_fail( 'authenticate_fail' );
        }

        $social = ucwords(preg_replace('~[\s|_]+~', ' ', $social));
        $social = str_replace(' ', '', $social);
        $social = "{$social}SocialAuthentication";
        $socialClass = "\\Includes\\Classes\\{$social}";
        if (!class_exists($socialClass)) {
            /*return new WP_Error(
                'authenticate_fail',
                __('Social not found.', 'social-auth'),
                array(
                    'status' => 403
                )
            );*/
            return wordpress_rest_format_response_fail( 'authenticate_fail' );
        }

        $socialObj = new $socialClass();
        $authenticateResult = $socialObj->authenticateUser($request);
        $dataAuthenticated = array();

        if (is_wp_error($authenticateResult)) {
            /*$authenticateResult->add_data(array(
                'status' => 403
            ), 'authenticate_fail');

            return $authenticateResult;*/
            return wordpress_rest_format_response_fail( 'authenticate_fail' );
        } else {
            $socialUser = array(
                'name' => $request->get_param('name'),
                'email' => $request->get_param('email')
            );

            $wpUser = get_user_by('email', $socialUser['email']);
            if (!empty($wpUser)) {
                $wpUser = apply_filters("{$this->prefixWordpressFilter}_recheck_authenticated_user", $request, $wpUser);
                if (is_wp_error($wpUser)) {
                    /*$wpUser->add_data(array(
                        'status' => 403
                    ), 'authenticate_fail');

                    return $wpUser;*/
                    return wordpress_rest_format_response_fail( 'authenticate_fail' );
                }

                $dataAuthenticated = $this->generateAuthenticationToken($wpUser);
            } else {
                $createUserResult = $this->registerWpUserFromSocial($request, $socialUser);
                if ($createUserResult['status'] != 'ok') {
                    $debug = array(
                        'info' => 'register new user fail',
                        'data' => array(
                            'error' => $createUserResult['error']
                        )
                    );
                    $this->logDebug($debug);
                    //return new WP_Error('authenticate_fail', __('Authenticate failed.', 'social-auth'), array('status' => 403));
                    return wordpress_rest_format_response_fail( 'authenticate_fail' );
                }

                $dataAuthenticated = $this->generateAuthenticationToken($createUserResult['user']);
            }
        }

        return wordpress_rest_format_response_success( $dataAuthenticated );
    }

    public function logAuthenticateFromSocial($request)
    {
        $social = $request->get_param('social');
        $error = $request->get_param('error');
        if ( empty( $social ) || empty( $error ) ) {
            /*return new WP_Error(
                'log_authenticate_fail',
                __('Missing param.', 'social-auth'),
                array(
                    'status' => 403
                )
            );*/
            return wordpress_rest_format_response_fail( 'log_authenticate_fail' );
        }

        $email = $request->get_param('email');
        $email = !empty( $email ) ? $email : 'unknow';

        $logErrors = get_option( $this->errorAuthenticateOptionName, array() );
        $logErrors[$email] = !empty( $logErrors[$email] ) ? $logErrors[$email] : array();
        $logErrors[$email][$social] = !empty( $logErrors[$email][$social] ) ? $logErrors[$email][$social] : array();
        // only store 200 newest errors
        if ( count( $logErrors[$email][$social] ) > 200 ) {
            $logErrors[$email][$social] = array();
        }
        $logErrors[$email][$social][] = $error;

        update_option( $this->errorAuthenticateOptionName, $logErrors );

        //return new WP_REST_Response( array( 'status' => 'ok' ), 200 );
        return wordpress_rest_format_response_success();
    }

    protected function validateRequest($request, $social, $action)
    {
        $requestNonce = $request->get_param('nonce');

        if (empty($requestNonce) || empty($social) || empty($action)) {
            return false;
        }

        $action =  "{$this->prefix}/{$social}/{$action}";

        return wp_verify_nonce($requestNonce, $action);
    }

    protected function logDebug($debug)
    {
        update_option($this->debugOptionName, $debug);
    }

    private function generateNonceAction($social, $action)
    {
        if (empty($social) || empty($action)) {
            return new WP_Error('get_nonce_fail', __('Get nonce request need two params: social and action.', 'social-auth'));
        }

        $action =  "{$this->prefix}/{$social}/{$action}";

        return wp_create_nonce($action);
    }

    private function registerWpUserFromSocial($request, $socialUser)
    {
        $socialUser = wp_parse_args($socialUser, array(
            'name' => '',
            'email' => ''
        ));

        $result = array(
            'status' => 'fail',
            'user' => '',
            'error' => ''
        );

        if (empty($socialUser['email']) || !is_email($socialUser['email'])) {
            $result['error'] = __('Email invalid', 'social-auth');
            return $result;
        }

        $newUser = array(
            'user_login' => $socialUser['email'],
            'user_email' => $socialUser['email'],
            'display_name' => $socialUser['name'],
            'user_pass' => $this->defaultNewUserPass,
            'role' => 'subscriber'
        );

        $user = wp_insert_user($newUser);

        if (is_wp_error($user)) {
            $result['error'] = $user->get_error_message();
        } else {
            $dataBooklyCustomer = array(
                'id' => false,
                'wp_user_id' => $user,
                'first_name' => '',
                'last_name' => '',
                'full_name' => $socialUser['name'],
                'phone' => '',
                'email' => $socialUser['email'],
                'city' => ''
            );
            if (!$this->registerBookingCustomer($dataBooklyCustomer)) {
                wp_delete_user($user);
                $result['error'] = __('Bookly create customer fail', 'social-auth');
            } else {
                do_action("{$this->prefixWordpressAction}_registered_new_user", $request, $user);

                $result['status'] = 'ok';
                $result['user'] = get_user_by('ID', (int) $user);
            }
        }

        return $result;
    }

    private function generateAuthenticationToken($user)
    {
        $secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;

        /** Valid credentials, the user exists create the according Token */
        $issuedAt = time();
        $notBefore = apply_filters('jwt_auth_not_before', $issuedAt, $issuedAt);
        $expire = apply_filters('jwt_auth_expire', $issuedAt + (DAY_IN_SECONDS * 7), $issuedAt);

        $token = array(
            'iss' => get_bloginfo('url'),
            'iat' => $issuedAt,
            'nbf' => $notBefore,
            'exp' => $expire,
            'data' => array(
                'user' => array(
                    'id' => $user->data->ID,
                ),
            ),
        );

        /** Let the user modify the token data before the sign. */
        $token = SocialJWT::encode(apply_filters('jwt_auth_token_before_sign', $token, $user), $secret_key);

        /** The token is signed, now create the object with no sensible user data to the client*/
        $data = array(
            'token' => $token,
            'user_email' => $user->data->user_email,
            'user_nicename' => $user->data->user_nicename,
            'user_display_name' => $user->data->display_name,
        );

        /** Let the user modify the data before send it back */
        $data = apply_filters('jwt_auth_token_before_dispatch', $data, $user);
        $data = array_filter($data);

        return $data;
    }

    private function checkConditionToPluginCanWork()
    {
        if (!function_exists('is_plugin_active')) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
        $secret_key = defined('JWT_AUTH_SECRET_KEY') ? true : false;
        $jwtPluginEnabled = is_plugin_active('jwt-authentication-for-wp-rest-api/jwt-auth.php');

        return $secret_key && $jwtPluginEnabled;
    }

    private function registerBookingCustomer($data)
    {
        $booklyCustomerAdapter = Customer::getInstance();

        if ($booklyCustomerAdapter->checkBooklyActivated()) {
            return $booklyCustomerAdapter->updateCustomer($data);
        }

        return true;
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new SocialAuthentication();
        }

        return self::$instance;
    }
}