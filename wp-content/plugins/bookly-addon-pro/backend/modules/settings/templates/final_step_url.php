<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="form-group">
    <label for="bookly_settings_final_step_url_mode"><?php _e( 'Final step URL', 'bookly' ) ?></label>
    <p class="help-block"><?php _e( 'Set the URL of a page that the user will be forwarded to after successful booking. If disabled then the default Done step is displayed.', 'bookly' ) ?></p>
    <select class="form-control" id="bookly_settings_final_step_url_mode">
        <?php foreach ( array( __( 'Disabled', 'bookly' ) => 0, __( 'Enabled', 'bookly' ) => 1 ) as $text => $mode ) : ?>
            <option value="<?php echo esc_attr( $mode ) ?>" <?php selected( get_option( 'bookly_url_final_step_url' ), $mode ) ?> ><?php echo $text ?></option>
        <?php endforeach ?>
    </select>
    <input class="form-control"
           style="margin-top: 5px; <?php echo get_option( 'bookly_url_final_step_url' ) == '' ? 'display: none' : '' ?>"
           type="text" name="bookly_url_final_step_url"
           value="<?php form_option( 'bookly_url_final_step_url' ) ?>"
           placeholder="<?php esc_attr_e( 'Enter a URL', 'bookly' ) ?>"/>
</div>