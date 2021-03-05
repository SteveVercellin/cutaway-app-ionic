<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Appearance\Editable;
?>
<div class="bookly-box bookly-list">
    <label>
        <input type="radio" name="payment" />
        <?php Editable::renderString( array( 'bookly_l10n_label_pay_paypal', ) ) ?>
        <img src="<?php echo plugins_url( 'frontend/resources/images/paypal.png', BooklyPro\Lib\Plugin::getMainFile() ) ?>" alt="paypal" />
    </label>
</div>