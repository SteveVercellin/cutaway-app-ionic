<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Settings\Selects;
use BooklyPro\Backend\Components\Settings\Address;
use Bookly\Lib as BooklyLib;

Selects::renderSingle(
    'bookly_cst_required_address',
    __( 'Make address mandatory', 'bookly' ),
    __( BooklyLib\Config::invoicesActive() ? 'Customers are required to enter address to proceed with a booking. To disable, deactivate Invoices add-on first.' : 'If enabled, a customer will be required to enter address to proceed with a booking.', 'bookly' ),
    array(
        array( 0, __( 'Disabled', 'bookly' ), BooklyLib\Config::invoicesActive() ),
        array( 1, __( 'Enabled', 'bookly' ) ),
    )
) ?>

<div class="form-group">
    <label for="bookly_cst_address_show_fields"><?php echo __( 'Customer\'s address fields', 'bookly' ) ?></label>
    <p class="help-block"><?php echo __( 'Choose address fields you want to request from the client.', 'bookly' ) ?></p>
    <div class="bookly-flags" id="bookly_cst_address_show_fields">
        <?php Address::render() ?>
    </div>
</div>