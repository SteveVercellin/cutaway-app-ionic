<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Dialogs\Appointment\AttachPayment\Proxy as AttachPaymentProxy;
$taxes_included = get_option( 'bookly_taxes_in_price' ) == 'included';
?>
<div id="bookly-payment-attach-modal" class="modal fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="modal-title h2"><?php _e( 'Attach payment', 'bookly' ) ?></div>
            </div>
            <div class="modal-body">
                <div class="radio">
                    <label>
                        <input type="radio" name="attach_method" value="create" checked ng-model="form.attach.payment_method">
                        <?php _e( 'Create payment', 'bookly' ) ?>
                    </label>
                </div>
                <div class="form-group bookly-margin-left-xlg"<?php if ( $taxes_included ) : ?> ng-class="{'has-error': (form.attach.payment_price * 1) < (form.attach.payment_tax * 1)}"<?php endif ?>>
                    <label for="bookly-attach-payment-price"><?php _e( 'Total price', 'bookly' ) ?></label>
                    <input id="bookly-attach-payment-price" class="form-control bookly-js-attach-payment-price" type="text" ng-model="form.attach.payment_price"/>
                </div>
                <?php AttachPaymentProxy\Taxes::renderAttachPayment() ?>
                <div class="radio">
                    <label>
                        <input type="radio" name="attach_method" value="search" ng-model="form.attach.payment_method">
                        <?php _e( 'Search payment', 'bookly' ) ?>
                    </label>
                </div>
                <div class="form-group bookly-margin-left-xlg">
                    <label for="bookly-attach-payment-id"><?php _e( 'Payment ID', 'bookly' ) ?></label>
                    <input id="bookly-attach-payment-id" class="form-control bookly-js-attach-payment-id" type="text" ng-model="form.attach.payment_id"/>
                </div>
            </div>
            <div class="modal-footer">
                <div ng-hide=loading>
                    <?php $disabled = $taxes_included ? '(form.attach.payment_price * 1) < (form.attach.payment_tax * 1) || ' : '' ?>
                    <?php Buttons::renderSubmit( 'bookly-attach-payment-apply', null, __( 'Apply', 'bookly' ), array( 'data-dismiss' => 'modal', 'ng-disabled' => $disabled . '(form.attach.payment_method==\'create\' && !form.attach.payment_price) || (form.attach.payment_method==\'search\' && !form.attach.payment_id)',  'ng-click' => 'attachPayment( form.attach.payment_method, form.attach.payment_price, form.attach.payment_tax, form.attach.payment_id, form.attach.customer_id, form.attach.customer_index )' ) ) ?>
                    <?php Buttons::renderCustom( null, 'btn-lg btn-default', __( 'Cancel', 'bookly' ), array( 'data-dismiss' => 'modal' ) ) ?>
                </div>
            </div>
        </div>
    </div>
</div>