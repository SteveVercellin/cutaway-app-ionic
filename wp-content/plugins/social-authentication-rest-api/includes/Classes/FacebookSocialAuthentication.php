<?php

namespace Includes\Classes;

use \Includes\Classes\SocialAuthentication;
use \Includes\Interfaces\SocialAuthenticationInterface;

use \Facebook\Facebook;

use \WP_Error as WP_Error;

class FacebookSocialAuthentication extends SocialAuthentication implements SocialAuthenticationInterface
{
    private $fbAppId = '364472907552247';
    private $fbAppSecret = '76bea6dc2ca226334081e651f945a6a4';

    public function __construct()
    {
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
        if ($this->needValidateNonce && !$this->validateRequest($request, 'facebook', 'authenticate')) {
            $this->logDebug('validate request fail');
            return new WP_Error('authenticate_fail', __('Request failed.', 'social-auth'));
        }

        $fbUser = array(
            'email' => $request->get_param('email'),
            'name' => $request->get_param('name'),
            'id' => $request->get_param('id'),
            'access_token' => $request->get_param('access_token')
        );
        $fbUser = array_filter($fbUser);

        if (count($fbUser) != 4) {
            $debug = array(
                'info' => 'validate request fail',
                'data' => $fbUser
            );
            $this->logDebug($debug);
            return new WP_Error('authenticate_fail', __('Data request failed.', 'social-auth'));
        }

        $facebook = new Facebook(array(
            'app_id'  => $this->fbAppId,
            'app_secret' => $this->fbAppSecret,
            'default_graph_version' => 'v2.10'
        ));

        try {
            // Get the \Facebook\GraphNodes\GraphUser object for the current user.
            $response = $facebook->get('/me', $fbUser['access_token']);
            $me = $response->getGraphUser();
            $meId = $me->getId();

            if ($meId != $fbUser['id']) {
                $debug = array(
                    'info' => 'validate fb user id fail',
                    'data' => array(
                        'request user id' => $fbUser['id'],
                        'request access token' => $fbUser['access_token'],
                        'get user id' => $meId
                    )
                );
                $this->logDebug($debug);
                return new WP_Error('authenticate_fail', __('Authenticate failed.', 'social-auth'));
            }
        } catch(\Exception $e) {
            // When Graph returns an error
            $debug = array(
                'info' => 'get fb user id fail',
                'data' => array(
                    'request user id' => $fbUser['id'],
                    'request access token' => $fbUser['access_token'],
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
        $fbUserId = $request->get_param('id');
        $fbUserIdMeta = get_user_meta($user->ID, '_fb_user_id', true);

        if ($fbUserId != $fbUserIdMeta) {
            $debug = array(
                'info' => 'verify fb user id fail',
                'data' => array(
                    'wp user id' => $user->ID,
                    'wp user fb id' => $fbUserIdMeta,
                    'request user id' => $fbUserId
                )
            );
            $this->logDebug($debug);
            return new WP_Error('authenticate_fail', __('Authenticate failed.', 'social-auth'));
        }

        return $user;
    }

    public function addMetaForNewUser($request, $user)
    {
        $fbUserId = $request->get_param('id');

        update_user_meta($user, '_fb_user_id', $fbUserId);
    }
}