<?php

namespace Includes\Classes;

use \Includes\Classes\SocialAuthentication;
use \Includes\Interfaces\SocialAuthenticationInterface;

use \WP_Error as WP_Error;

use \Google_Client as Google_Client;
use \Google_Service_Plus as Google_Service_Plus;

class GoogleSocialAuthentication extends SocialAuthentication implements SocialAuthenticationInterface
{
    private $googleAppAuthenFile = 'google_app_authenticate.json';

    public function __construct()
    {
        $this->googleAppAuthenFile = plugin_dir_path(__FILE__) . $this->googleAppAuthenFile;

        add_action("{$this->prefixWordpressAction}_registered_new_user", array(
            $this,
            'addMetaForNewUser'
        ), 10, 2);

        add_filter("{$this->prefixWordpressFilter}_recheck_authenticated_user", array(
            $this,
            'reCheckExistsUser'
        ), 10, 2);
    }

    public function authenticateUser($request)
    {
        if ($this->needValidateNonce && !$this->validateRequest($request, 'google', 'authenticate')) {
            $this->logDebug('validate request fail');
            return new WP_Error('authenticate_fail', __('Request failed.', 'social-auth'));
        }

        $googleUser = array(
            'email' => $request->get_param('email'),
            'name' => $request->get_param('name'),
            'id' => $request->get_param('id'),
            'access_token' => $request->get_param('access_token'),
            //'id_token' => $request->get_param('id_token')
        );
        $googleUser = array_filter($googleUser);

        if (count($googleUser) != 4) {
            $debug = array(
                'info' => 'validate request fail',
                'data' => $googleUser
            );
            $this->logDebug($debug);
            return new WP_Error('authenticate_fail', __('Data request failed.', 'social-auth'));
        }

        try {
            $client = new Google_Client();
            $client->setAuthConfig($this->googleAppAuthenFile);
            //$client->setScopes(array(Google_Service_Plus::PLUS_ME));
            //$client->setAccessType("offline");
            $plus = new Google_Service_Plus($client);

            $client->setAccessToken($googleUser['access_token']);
            $userVerified = false;

            $me = $plus->people->get('me');
            if ($me) {
                $meId = $me->id;
                if ($meId == $googleUser['id']) {
                    $userVerified = true;
                }
            } else {
                $me = array();
                $meId = '';
            }

            if (!$userVerified) {
                $debug = array(
                    'info' => 'validate google user id fail',
                    'data' => array(
                        'request user id' => $googleUser['id'],
                        'profile user id' => $meId,
                        'user profile' => (array) $me
                    )
                );
                $this->logDebug($debug);
                return new WP_Error('authenticate_fail', __('Authenticate failed.', 'social-auth'));
            }
        } catch(\Exception $e) {
            // When Graph returns an error
            $debug = array(
                'info' => 'get google user id fail',
                'data' => array(
                    'request user id' => $googleUser['id'],
                    'error' => $e->getMessage()
                )
            );
            $this->logDebug($debug);
            return new WP_Error('authenticate_fail', __('Authenticate failed.', 'social-auth'));
        }

        return true;
    }

    public function reCheckExistsUser($request, $user)
    {
        $googleUserId = $request->get_param('id');
        $googleUserIdMeta = get_user_meta($user->ID, '_google_user_id', true);

        if ($googleUserId != $googleUserIdMeta) {
            $debug = array(
                'info' => 'verify google user id fail',
                'data' => array(
                    'wp user id' => $user->ID,
                    'wp user google id' => $googleUserIdMeta,
                    'request user id' => $googleUserId
                )
            );
            $this->logDebug($debug);
            return new WP_Error('authenticate_fail', __('Authenticate failed.', 'social-auth'));
        }

        return $user;
    }

    public function addMetaForNewUser($request, $user)
    {
        $googleUserId = $request->get_param('id');

        update_user_meta($user, '_google_user_id', $googleUserId);
    }
}