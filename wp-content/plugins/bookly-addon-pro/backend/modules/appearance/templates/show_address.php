<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib as BooklyLib;
?>
<div class="col-md-3">
    <div class="checkbox">
        <label<?php if ( BooklyLib\Config::invoicesActive() ) : ?> class="bookly-js-simple-popover" data-html="true" data-toggle="popover" data-trigger="hover" data-placement="auto" data-content="<?php echo esc_attr( 'Address information is needed for Invoices add-on. To disable, deactivate Invoices add-on first.', 'bookly' ) ?>"<?php endif ?>>
            <input type="checkbox" id="bookly-show-address"
                <?php echo BooklyLib\Config::invoicesActive()
                    ? 'checked="checked" disabled="disabled"'
                    : checked( get_option( 'bookly_app_show_address' ), true, false ) ?> >
            <?php _e( 'Show address fields', 'bookly' ) ?>
        </label>
    </div>
</div>