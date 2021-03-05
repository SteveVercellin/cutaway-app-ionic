<?php

namespace Includes\Classes;

use \Firebase\JWT\JWT as WordpressRestFunctionsJWT;

use \WP_REST_Server as WP_REST_Server;
use \WP_Error as WP_Error;

class WordpressBaseRestFunctions
{
    protected $prefix = 'wordpress-rest-functions';
    protected $restVersion = 'v1';
    protected $textDomain = 'wordpress-functions-rest';

    protected $needValidateNonce = false;

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerBaseRestFunctions'));
    }

    public function registerBaseRestFunctions()
    {
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/get-nonce-action/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'getNonce')
        ));
    }

    public function getNonce($request)
    {
        $function = $request['function'];
        $nonce = $this->generateNonce($function);

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

    /**
     * Check permissions for the posts.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_items_permissions_check( $request ) {
        if( ! REST_TEST_ENABLE ){
            if ( ! is_user_logged_in() ) {
                return new WP_Error( 'rest_forbidden', 'You cannot view the post resource.', array( 'status' => 403 ) );
            }
        }
        return true;
    }

    protected function validateRequest($request, $function)
    {
        $requestNonce = $request->get_param('nonce');

        if (empty($requestNonce) || empty($function)) {
            return false;
        }

        $action =  "{$this->prefix}/{$function}";

        return wp_verify_nonce($requestNonce, $action);
    }

    protected function logDebug($debug)
    {
        update_option($this->debugOptionName, $debug);
    }

    protected function generateAuthenticationToken($user)
    {
        $secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;

        if (!$secret_key) {
            return array();
        }

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
        $token = WordpressRestFunctionsJWT::encode(apply_filters('jwt_auth_token_before_sign', $token, $user), $secret_key);

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

    protected function detechUserIsSocialUser($user)
    {
        $googleUserId = get_user_meta($user, '_google_user_id', true);
        $fbUserId = get_user_meta($user, '_fb_user_id', true);

        return !empty($googleUserId) || !empty($fbUserId);
    }

    protected function checkBooklyAdapterActivated()
    {
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH.'wp-admin/includes/plugin.php');
        }

        return is_plugin_active('appointment-booking-adapter/appointment-booking-adapter.php');
    }

    private function generateNonce($function)
    {
        if (empty($function)) {
            return new WP_Error('get_nonce_fail', __('Get nonce request need one param: function.', $this->textDomain));
        }

        $action =  "{$this->prefix}/{$function}";

        return wp_create_nonce($action);
    }
}