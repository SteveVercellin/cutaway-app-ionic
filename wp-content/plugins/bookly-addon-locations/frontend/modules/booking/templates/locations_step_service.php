<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="bookly-form-group bookly-location">
    <label><?php echo \Bookly\Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_location' ) ?></label>
    <div>
        <select class="bookly-select-mobile bookly-js-select-location">
            <option value=""><?php echo \Bookly\Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_option_location' ) ?></option>
        </select>
    </div>
    <div class="bookly-js-select-location-error bookly-label-error" style="display: none">
        <?php echo esc_html( \Bookly\Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_required_location' ) ) ?>
    </div>
</div>