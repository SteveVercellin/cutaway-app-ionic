<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="col-md-3">
    <div class="checkbox">
        <label>
            <input type="checkbox" id=bookly-show-ratings <?php checked( get_option( 'bookly_ratings_app_show_on_frontend' ) ) ?>>
            <?php _e( 'Show staff member rating before employee name', 'bookly' ) ?>
        </label>
    </div>
</div>