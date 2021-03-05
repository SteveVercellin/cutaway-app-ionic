<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<button type="button" class="btn btn-sm btn-default bookly-margin-left-xs" ng-click="attachPaymentModal(customer, $index)" ng-show="! customer.payment_id && ! customer.payment_create" popover="<?php esc_attr_e( 'Attach payment', 'bookly' ) ?>">
    <span class="fa fa-fw fa-plus-square"></span>
</button>
