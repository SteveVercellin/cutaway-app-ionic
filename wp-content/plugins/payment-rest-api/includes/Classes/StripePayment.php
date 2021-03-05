<?php

namespace Includes\Classes;

use \Includes\Classes\BaseRestPayments;
use \Includes\Interfaces\RestPayments;

use \Stripe\Stripe;
use \Stripe\Customer;
use \Stripe\Charge;
use \Stripe\Refund;

use \Exception;
use \WP_REST_Server;
use \WP_REST_Response;
use \WP_Error;

class StripePayment extends BaseRestPayments implements RestPayments
{
    protected $debugOptionName = '_stripe_payment_debug';

    private $key = array(
        'sandbox' => array(
            'published' => 'pk_test_ympBvvTQTTwwaNWtJzSlBtay',
            'secret' => 'sk_test_wA0ctJRg00rQfnG5ufiG3tcF'
        ),
        'production' => array(
            'published' => 'pk_live_5URarp9bQIKkmPC54SLsQ55T',
            'secret' => 'sk_live_wzE9YmnVJCXQvHe3dGt03kdl'
        )
    );
    private $mode = 'sandbox';

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerRestPayments'));
        add_action('rest_api_init', array($this, 'registerTestRestPayments'));
    }

    public function registerRestPayments()
    {
        $restService = $this->getRestServicePayment();
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/stripe-payment/', array(
            'methods' => $restService['method'],
            'callback' => array($this, 'stripePayment'),
            'permission_callback' => array( $this, 'get_items_permissions_check' )
        ));
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/refund-stripe-payment/', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'refundStripePayment'),
            'permission_callback' => array( $this, 'get_items_permissions_check' )
        ));
    }

    public function registerTestRestPayments()
    {
        register_rest_route("{$this->prefix}/{$this->restVersion}", '/stripe-payment/test/', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'testStripePayment')
        ));
    }

    public function getRestServicePayment()
    {
        return array(
            'method' => WP_REST_Server::CREATABLE,
            'url' => "{$this->prefix}/{$this->restVersion}/stripe-payment/"
        );
    }

    public function stripePayment($request)
    {
        if ($this->needValidateNonce && !$this->validateRequest($request, 'stripe')) {
            $this->logDebug('validate request fail');
            /*return new WP_Error('validate_fail', 'validate_fail',
            array(
                'status' => 403
            ));*/
            return wordpress_rest_format_response_fail( 'validate_fail' );
        }

        $currentUser = wp_get_current_user();

        $dataPay = array(
            'token' => $request['token'],
            'amount' => $request['amount'],
            'currency' => $request['currency'],
            'description' => $request['description'],
            'sku' => $request['sku']
        );
        $dataPay = array_filter($dataPay);

        $dataFinishBook = $this->getDataFinishBook($request);

        if (count($dataPay) != 5 || count($dataFinishBook) != 1) {
            $debug = array(
                'info' => 'data missing',
                'data_pay' => $dataPay,
                'data_finish' => $dataFinishBook
            );
            $this->logDebug($debug);
            /*return new WP_Error('data_missing', 'data_missing',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'data_missing' );
        }

        extract($dataPay);

        $mode = !empty($this->key[$this->mode]) ? $this->mode : 'sandbox';
        $stripeKey = $this->key[$mode]['secret'];
        $cusEmail = $currentUser->user_email;
        $amount *= 100;
        $txnId = '';

        Stripe::setApiKey($stripeKey);
        $dataCustomer = array(
            "source" => $token,
            "email" => $cusEmail
        );
        $dataCharge = array(
            "amount" => $amount,
            "currency" => $currency,
            "description" => $description,
            "metadata" => array("ID/SKU" => $sku),
            "customer" => $customer->id,
        );

        try {
            $customer = Customer::create($dataCustomer);
            $dataCharge['customer'] = $customer->id;
            $charge = Charge::create($dataCharge);
            $txnId = $charge->id;
        } catch (Exception $e) {
            $body = $e->getJsonBody();
            $err = $body['error'];
            $reason = $err['message'];

            $debug = array(
                'info' => 'stripe charge fail',
                'customer' => $dataCustomer,
                'charge' => $dataCharge,
                'error' => $reason
            );
            $this->logDebug($debug);
        }

        if (empty($txnId)) {
            /*return new WP_Error('pay_fail', 'pay_fail',
            array(
                'status' => 400
            ));*/
            return wordpress_rest_format_response_fail( 'pay_fail' );
        }

        $dataFinishBook['stripe_charge_id'] = $txnId;

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

    public function refundStripePayment($request)
    {
        global $wpdb;

        if ($this->needValidateNonce && !$this->validateRequest($request, 'stripe')) {
            $this->logDebug('validate request fail');
            return wordpress_rest_format_response_fail( 'validate_fail' );
        }

        $currentUser = wp_get_current_user();

        $group = !empty( $request['group'] ) ? $request['group'] : 0;
        if ( empty( $group ) ) {
            $this->logDebug('empty customer appointment group');
            return wordpress_rest_format_response_fail( 'fail' );
        }

        $booklyCustomerAdapter = \BooklyAdapter\Entities\Customer::getInstance();
        $booklyAppointmentGroup = \BooklyAdapter\Classes\AppointmentGroup::getInstance();

        $customerId = $booklyCustomerAdapter->getCustomerIdFromUserLogged();
        if ( empty( $customerId ) ) {
            $this->logDebug( sprintf( 'empty customer id from wp user id: %d', $currentUser->ID ) );
            return wordpress_rest_format_response_fail( 'fail' );
        }

        $cusAppGroup = $booklyAppointmentGroup->onlyLoadCustomerAppointment( $customerId, $group );
        if ( empty( $cusAppGroup ) ) {
            $this->logDebug( sprintf( 'empty appointment group with customer %d and group %d', $customerId, $group ) );
            return wordpress_rest_format_response_fail( 'fail' );
        }

        $booklyAppointmentGroupMeta = new \BooklyAdapter\Classes\AppointmentGroupMeta( $cusAppGroup->id );
        $stripeChargeId = $booklyAppointmentGroupMeta->getAppointmentGroupMeta( '_stripe_charge_id' );
        if ( $cusAppGroup->status_string == 'approved' && empty( $stripeChargeId ) ) {
            $this->logDebug( sprintf( 'empty stripe charge id from app group %d', $cusAppGroup->id ) );
            return wordpress_rest_format_response_fail( 'fail' );
        }

        $resultRefund = false;

        $wpdb->query('START TRANSACTION');

        $resultRefund = $booklyAppointmentGroup->refundAppointment( $cusAppGroup->id );
        if ( $resultRefund ) {

            if ( $cusAppGroup->status_string == 'approved' ) {
                $mode = !empty($this->key[$this->mode]) ? $this->mode : 'sandbox';
                $stripeKey = $this->key[$mode]['secret'];

                Stripe::setApiKey($stripeKey);
                $debug = array(
                    'info' => 'empty result refund',
                );

                try {
                    $resultRefund = Refund::create( array(
                        "charge" => $stripeChargeId
                    ) );
                } catch (Exception $e) {
                    $body = $e->getJsonBody();
                    $err = $body['error'];
                    $reason = $err['message'];

                    $debug = array(
                        'info' => 'stripe refund fail',
                        'charge_id' => $stripeChargeId,
                        'error' => $reason
                    );
                }

                if ( empty( $resultRefund->id ) ) {
                    $wpdb->query('ROLLBACK');
                    $this->logDebug($debug);
                    return wordpress_rest_format_response_fail( 'fail' );
                }

                $resultRefund = array(
                    'id' => $resultRefund->id,
                    'amount' => $resultRefund->amount,
                    'currency' => $resultRefund->currency,
                    'created' => $resultRefund->created,
                    'status' => $resultRefund->status,
                );

                $booklyAppointmentGroupMeta->updateAppointmentGroupMeta(
                    '_stripe_refund_result',
                    $resultRefund
                );
                $wpdb->query('COMMIT');
            } else {
                $wpdb->query('COMMIT');
            }

            return wordpress_rest_format_response_success();

        } else {
            $wpdb->query('ROLLBACK');
            $this->logDebug( sprintf( 'app group %d refund fail', $cusAppGroup->id ) );
            return wordpress_rest_format_response_fail( 'fail' );
        }
    }

    public function testStripePayment($request)
    {
        return rest_ensure_response(array(
            'status' => 'enabled'
        ));
    }
}