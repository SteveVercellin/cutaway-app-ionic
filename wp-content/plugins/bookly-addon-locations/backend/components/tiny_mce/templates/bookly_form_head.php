<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<tr>
    <td>
        <label for="bookly-select-location"><?php _e( 'Default value for location select', 'bookly' ) ?></label>
    </td>
    <td>
        <select class="form-control locations-list" id="bookly-select-location">
            <option value=""><?php echo \Bookly\Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_option_location' ) ?></option>
        </select>
        <div class="checkbox">
            <label>
                <input type="checkbox" id="bookly-hide-locations">
                <?php _e( 'Hide this field', 'bookly' ) ?>
            </label>
        </div>
    </td>
</tr>