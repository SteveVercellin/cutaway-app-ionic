<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="col-md-3">
    <div class="checkbox">
        <label data-toggle="popover" data-trigger="hover" data-placement="auto"<?php if ( get_option( 'bookly_locations_allow_services_per_location' ) ) : ?> class="bookly-js-simple-popover" data-content="<?php echo esc_attr( 'Custom settings for location enabled', 'bookly' ) ?>"<?php else : ?>  data-content="<?php echo esc_attr( 'Show location required', 'bookly' ) ?>"<?php endif ?>>
            <input type="checkbox" id="bookly-required-location" <?php checked( get_option( 'bookly_app_required_location' ) || get_option( 'bookly_locations_allow_services_per_location' ) ); disabled( get_option( 'bookly_locations_allow_services_per_location' ) ) ?>>
            <?php _e( 'Make selecting location required', 'bookly' ) ?>
        </label>
    </div>
</div>