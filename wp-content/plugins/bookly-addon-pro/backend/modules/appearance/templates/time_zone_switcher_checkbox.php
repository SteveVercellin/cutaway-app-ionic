<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="col-md-3">
    <div class="checkbox">
        <label>
            <input type="checkbox" id="bookly-show-time-zone-switcher" <?php checked( get_option( 'bookly_app_show_time_zone_switcher' ) ) ?>>
            <?php _e( 'Show time zone switcher', 'bookly' ) ?>
        </label>
    </div>
</div>