<?php

class BaseRestModule
{
    protected $restLimitRecords = 20;

    protected $restMode = '';

    /**
     * Check permissions for the rests.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_items_permissions_check( $request ) {
        if ( ! is_user_logged_in() ) {
            return new WP_Error( 'authenticate_fail', 'authenticate_fail', array( 'status' => 403 ) );
        }

        $this->restMode = 'live';

        return true;
    }

    /**
     * Check permissions for the rests test.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_items_permissions_check_test( $request ) {
        if (!$this->isRunningDebugMode()) {
            return new WP_Error( 'test_mode_disabled', 'test_mode_disabled', array( 'status' => 403 ) );
        }

        $this->restMode = 'test';

        return true;
    }

    protected function isRunningDebugMode()
    {
        return defined('REST_TEST_ENABLE') && REST_TEST_ENABLE;
    }

    protected function getCurrentUser()
    {
        $currentUser = wp_get_current_user();
        // REST debug mode -> allow determine user by request
        if (empty($currentUser->ID) && $this->isRunningDebugMode() && $this->restMode == 'test') {
            $currentUser = $this->setWordpressCurrentUser($currentUser);
        }

        return $currentUser;
    }

    protected function checkBooklyActivated()
    {
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH.'wp-admin/includes/plugin.php');
        }

        return is_plugin_active('bookly-responsive-appointment-booking-tool/main.php');
    }

    protected function checkBooklyAdapterActivated()
    {
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH.'wp-admin/includes/plugin.php');
        }

        return is_plugin_active('appointment-booking-adapter/appointment-booking-adapter.php');
    }

    protected function generateAuthenticateFailError()
    {
        return wordpress_rest_format_response_fail( 'authenticate_fail' );
    }

    protected function generateWpErrorAuthenticateFailError()
    {
        return new WP_Error(
            'authenticate_fail',
            'authenticate_fail',
            array(
                'status' => 403
            )
        );
    }

    protected function getBooklyConfigCurrencySymbol()
    {
        if (!$this->checkBooklyActivated()) {
            return '&euro;';
        }

        $currentBooklyCurrency = get_option('bookly_pmt_currency', 'EUR');
        $currencies = \Bookly\Lib\Utils\Price::getCurrencies();
        if (empty($currencies) || empty($currencies[$currentBooklyCurrency])) {
            return '&euro;';
        }

        return $currencies[$currentBooklyCurrency]['symbol'];
    }

    private function setWordpressCurrentUser($currentUser)
    {
        // REST debug mode -> allow determine user by request
        if ($this->isRunningDebugMode() && $this->restMode == 'test') {
            $userRequest = !empty($_REQUEST['current_user']) ? $_REQUEST['current_user'] : '';
            if (!empty($userRequest)) {
                wp_set_current_user($userRequest);
                $currentUser = wp_get_current_user();
            }
        }

        return $currentUser;
    }
}