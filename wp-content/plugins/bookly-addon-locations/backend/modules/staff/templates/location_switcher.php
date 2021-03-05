<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label for="staff_location_id"><?php esc_html_e( 'Select location', 'bookly' ) ?></label>
            <p class="help-block"><?php esc_html_e( 'Set Default values that will be used in all locations where Use default settings is selected. To use custom settings in a location, select Use custom settings and enter custom values.', 'bookly' ) ?></p>
            <select class="form-control" name="location_id" id="staff_location_id">
                <option value=""><?php esc_html_e( 'Default settings', 'bookly' ) ?></option>
                <?php foreach ( $staff_locations as $location ) : ?>
                    <option value="<?php echo $location['id'] ?>" <?php selected( $location['id'], $location_id ) ?>><?php echo $location['name'] ?></option>
                <?php endforeach ?>
            </select>
        </div>
    </div>
</div>
<?php if ( $location_id ) : ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="custom_location_settings"><?php esc_html_e( 'Settings', 'bookly' ) ?></label>
                <select class="form-control" name="custom_location_settings" id="custom_location_settings">
                    <option value="0"><?php esc_html_e( 'Use default settings', 'bookly' ) ?></option>
                    <option value="1"<?php selected( $custom_settings ) ?>><?php esc_html_e( 'Use custom settings', 'bookly' ) ?></option>
                </select>
            </div>
        </div>
    </div>
<?php endif ?>