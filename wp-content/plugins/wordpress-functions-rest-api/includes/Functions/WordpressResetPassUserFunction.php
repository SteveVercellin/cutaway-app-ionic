<?php

namespace Includes\Functions;

use \Includes\Interfaces\WordpressRestFunction;
use \Includes\Classes\WordpressBaseRestFunctions;

use \WP_REST_Server as WP_REST_Server;
use \WP_Error as WP_Error;

class WordpressResetPassUserFunction extends WordpressBaseRestFunctions implements WordpressRestFunction
{
    protected $debugOptionName = '_wordpress_reset_pass_user_rest_function';

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
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/send-reset-pass/', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'sendResetPassLink')
        ));
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/reset-pass/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'resetPass')
        ));
    }

    public function registerTestRestFunctions()
    {
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/send-reset-pass/test/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'testSendResetPassLink')
        ));
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/reset-pass/test/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'testResetPass')
        ));
    }

    public function testSendResetPassLink($request)
    {
        return rest_ensure_response(array(
            'status' => 'enabled'
        ));
    }

    public function testResetPass($request)
    {
        return rest_ensure_response(array(
            'status' => 'enabled'
        ));
    }

    public function sendResetPassLink($request)
    {
        if ($this->needValidateNonce && !$this->validateRequest($request, 'send-reset-pass-link')) {
            $this->logDebug('validate request fail');
            /*return new WP_Error('validate_fail', 'validate_fail',
            array(
                'status' => 403
            ));*/
            return wordpress_rest_format_response_fail( 'validate_fail' );
        }

        $userData = array(
            'email' => $request['email']
        );
        $userData = array_filter($userData);

        if (count($userData) != 1) {
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

        extract($userData);

        if (!is_email($email)) {
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

        if (!email_exists($email)) {
            $debug = array(
                'info' => 'email not exists',
                'data' => $userData
            );
            $this->logDebug($debug);
            /*return new WP_Error('email_not_exists', 'email_not_exists',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'email_not_exists' );
        }

        $user = get_user_by('email', $email);
        if (empty($user)) {
            $debug = array(
                'info' => 'email not exists',
                'data' => $userData
            );
            $this->logDebug($debug);
            /*return new WP_Error('email_not_exists', 'email_not_exists',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'email_not_exists' );
        }

        $errors = new WP_Error();

        /**
         * Fires before errors are returned from a password reset request.
         *
         * @since 2.1.0
         * @since 4.4.0 Added the `$errors` parameter.
         *
         * @param WP_Error $errors A WP_Error object containing any errors generated
         *                         by using invalid credentials.
         */
        do_action( 'lostpassword_post', $errors );
        if ( $errors->get_error_code() ) {
            $debug = array(
                'info' => 'send reset pass fail',
                'data' => $userData,
                'error' => $errors->get_error_message()
            );
            $this->logDebug($debug);
            /*return new WP_Error('send_reset_pass_fail', 'send_reset_pass_fail',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'send_reset_pass_fail' );
        }

        $userLogin = $user->user_login;
        $key = get_password_reset_key($user);
        if (is_wp_error($key)) {
            $debug = array(
                'info' => 'send reset pass fail',
                'data' => $userData,
                'error' => $key->get_error_message()
            );
            $this->logDebug($debug);
            /*return new WP_Error('send_reset_pass_fail', 'send_reset_pass_fail',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'send_reset_pass_fail' );
        }

        $resetPasswordLink = add_query_arg(array(
            'key' => $key,
            'login' => rawurlencode($userLogin)
        ), get_rest_url(1, "{$this->prefix}/{$this->restVersion}/reset-pass/"));

        $message = __( 'Someone has requested a password reset for the following account:', $this->textDomain ) . "\r\n\r\n";
        /* translators: %s: user login */
        $message .= sprintf( __( 'Username: %s', $this->textDomain), $userLogin ) . "\r\n\r\n";
        $message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', $this->textDomain ) . "\r\n\r\n";
        $message .= __( 'To reset your password, visit the following address:', $this->textDomain ) . "\r\n\r\n";
        $message .= '' . $resetPasswordLink . '';

        /* translators: Password reset email subject. %s: Site name */
        $title = sprintf( __( '[%s] Password Reset', $this->textDomain ), "Cutaway" );

        /**
         * Filters the subject of the password reset email.
         *
         * @since 2.8.0
         * @since 4.4.0 Added the `$user_login` and `$user_data` parameters.
         *
         * @param string  $title      Default email title.
         * @param string  $user_login The username for the user.
         * @param WP_User $user_data  WP_User object.
         */
        $title = apply_filters( 'retrieve_password_title', $title, $userLogin, $user );

        /**
         * Filters the message body of the password reset mail.
         *
         * If the filtered message is empty, the password reset email will not be sent.
         *
         * @since 2.8.0
         * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
         *
         * @param string  $message    Default mail message.
         * @param string  $key        The activation key.
         * @param string  $user_login The username for the user.
         * @param WP_User $user_data  WP_User object.
         */
        $message = apply_filters( 'retrieve_password_message', $message, $key, $userLogin, $user );

        if (empty($message)) {
            $debug = array(
                'info' => 'send reset pass fail',
                'data' => $userData,
                'error' => 'message empty'
            );
            $this->logDebug($debug);
            /*return new WP_Error('send_reset_pass_fail', 'send_reset_pass_fail',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'send_reset_pass_fail' );
        }

        if (!wp_mail($email, wp_specialchars_decode( $title ), $message)) {
            $debug = array(
                'info' => 'send reset pass fail',
                'data' => $userData,
                'error' => 'send mail fail'
            );
            $this->logDebug($debug);
            /*return new WP_Error('send_reset_pass_fail', 'send_reset_pass_fail',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'send_reset_pass_fail' );
        }

		/*return rest_ensure_response(array(
            'status' => 'ok'
        ));*/
        return wordpress_rest_format_response_success();
    }

    function resetPass($request)
    {
        header("Content-Type: text/plain");

        $userData = array(
            'key' => $request['key'],
            'login' => $request['login']
        );
        $userData = array_filter($userData);

        if (count($userData) != 2) {
            echo __('Reset pass failed. Data missing.', $this->textDomain);
            exit;
        }

        extract($userData);

        $user = check_password_reset_key( $key, $login );
        if (empty($user) || is_wp_error($user)) {
            $error = "";

            if (empty($user)) {
                $error = __('User does not exists.', $this->textDomain);
            } else {
                if ($user->get_error_code() === 'expired_key') {
                    $error = __('Reset password key expired.', $this->textDomain);
                } else {
                    $error = __('Reset password key does not exists.', $this->textDomain);
                }
            }

            echo __('Reset pass failed. ', $this->textDomain) . $error;
            exit;
        }

        $errors = new WP_Error();
        /**
         * Fires before the password reset procedure is validated.
         *
         * @since 3.5.0
         *
         * @param object           $errors WP Error object.
         * @param WP_User|WP_Error $user   WP_User object if the login and reset key match. WP_Error object otherwise.
         */
        do_action( 'validate_password_reset', $errors, $user );
        if ($errors->get_error_code()) {
            $debug = array(
                'info' => 'reset password user failed',
                'data' => $userData,
                'error' => $errors->get_error_message()
            );
            $this->logDebug($debug);

            echo __('Reset pass failed.', $this->textDomain);
            exit;
        }

        $pass = rand(1000000, 9999999);
        reset_password($user, $pass);

        $userLogin = $user->user_login;
        $email = $user->user_email;
        $message = __( 'Password changed for the following account:', $this->textDomain ) . "\r\n\r\n";
        /* translators: %s: user login */
        $message .= sprintf( __( 'Username: %s', $this->textDomain), $userLogin ) . "\r\n\r\n";
        $message .= sprintf( __( 'Password: %s', $this->textDomain), $pass );

        /* translators: Password reset email subject. %s: Site name */
        $title = sprintf( __( '[%s] Password Changed', $this->textDomain ), "Cutaway" );

        if (!wp_mail($email, wp_specialchars_decode( $title ), $message)) {
            echo __('Send reset pass link failed.', $this->textDomain);
            exit;
        }

        echo __('Changed password. Check email to get new password.', $this->textDomain);
        exit;
    }
}

/*add_filter( 'wp_mail_from', function () {
	return 'tancongnguyen83@gmail.com';
}, 999 );
add_filter( 'wp_mail_from_name', function () {
	return 'Test';
}, 999 );*/