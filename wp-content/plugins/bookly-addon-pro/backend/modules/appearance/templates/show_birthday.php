<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="col-md-3">
    <div class="checkbox">
        <label>
            <input type="checkbox" id="bookly-show-birthday" <?php checked( get_option( 'bookly_app_show_birthday' ) ) ?>>
            <?php _e( 'Show birthday field', 'bookly' ) ?>
        </label>
    </div>
</div>