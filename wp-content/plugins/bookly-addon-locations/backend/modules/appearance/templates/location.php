<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Appearance\Editable;
use Bookly\Lib as BooklyLib;
?>
<div class="bookly-form-group bookly-js-location">
    <?php
    Editable::renderLabel( array(
        'bookly_l10n_label_location',
        'bookly_l10n_option_location',
        'bookly_l10n_required_location',
    ) )
    ?>
    <div class="bookly-form-field">
        <select class="bookly-form-element bookly-select-mobile bookly-js-select-location bookly-animate" style="width: 100%">
            <option value="" class="bookly-js-option bookly_l10n_option_location"><?php echo esc_html( get_option( 'bookly_l10n_option_location' ) ) ?></option>
            <option value="1">New York</option>
            <option value="2">Los Angeles</option>
            <option value="3">Chicago</option>
            <option value="3">Dallas</option>
        </select>
    </div>
</div>