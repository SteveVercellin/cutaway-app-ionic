<?php

namespace Includes\Classes;

use \Includes\Classes\BaseRestPayments;
use \Includes\Interfaces\RestPayments;

use \Exception;
use \WP_REST_Server;
use \WP_REST_Response;
use \WP_Error;

class PaypalPayment extends BaseRestPayments implements RestPayments
{
    protected $debugOptionName = '_paypal_payment_debug';

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerRestPayments'));
        add_action('rest_api_init', array($this, 'registerTestRestPayments'));
    }

    public function registerRestPayments()
    {
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/paypal-payment/', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'paypalPayment'),
            'permission_callback' => array( $this, 'get_items_permissions_check' )
        ));
    }

    public function registerTestRestPayments()
    {
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/paypal-payment/test/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'testPaypalPayment')
        ));
    }

    public function paypalPayment($request)
    {
        if ($this->needValidateNonce && !$this->validateRequest($request, 'paypal')) {
            $this->logDebug('validate request fail');
            /*return new WP_Error('validate_fail', 'validate_fail',
            array(
                'status' => 403
            ));*/
            return wordpress_rest_format_response_fail( 'validate_fail' );
        }

        $dataPay = array(
            'pay_id' => $request['pay_id'],
            'pay_state' => $request['pay_state']
        );
        $dataPay = array_filter($dataPay);

        $dataFinishBook = $this->getDataFinishBook($request);

        if (count($dataPay) != 2 || count($dataFinishBook) != 1) {
            $debug = array(
                'info' => 'data missing',
                'data' => $dataPay
            );
            $this->logDebug($debug);
            /*return new WP_Error('data_missing', 'data_missing',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'data_missing' );
        }
        extract($dataPay);

        if ($pay_state != 'approved') {
            $debug = array(
                'info' => 'pay not approved',
                'data' => $dataPay
            );
            $this->logDebug($debug);
            /*return new WP_Error('pay_not_approved', 'pay_not_approved',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'pay_not_approved' );
        }

        if (!$this->completeBooking($dataFinishBook)) {
            /*return new WP_Error('complete_book_fail', 'complete_book_fail',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'complete_book_fail' );
        }

        /*return rest_ensure_response(array(
            'status' => 'ok'
        ));*/
        return wordpress_rest_format_response_success( array( 'group' => $dataFinishBook['group'] ) );
    }

    public function testPaypalPayment($request)
    {
        return rest_ensure_response(array(
            'status' => 'enabled'
        ));
    }
}