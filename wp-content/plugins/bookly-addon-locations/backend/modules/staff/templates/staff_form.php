<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    $assigned_location_ids = $staff_locations['location_ids'] ? explode( ',', $staff_locations['location_ids'] ) : array();
?>
<div class="form-group locations-row">
    <label><?php form_option( 'bookly_l10n_label_location' ) ?></label>
    <p class="help-block">
        <?php _e( 'Select locations where the services are provided.', 'bookly' ) ?>
    </p>
    <ul id="bookly-js-locations"
        data-icon-class="dashicons dashicons-location"
        data-txt-select-all="<?php esc_attr_e( 'All locations', 'bookly' ) ?>"
        data-txt-all-selected="<?php esc_attr_e( 'All locations', 'bookly' ) ?>"
        data-txt-nothing-selected="<?php esc_attr_e( 'No locations selected', 'bookly' ) ?>"
    >
        <?php foreach ( $locations as $location ): ?>
            <li data-input-name="location_ids[]" data-value="<?php echo $location['id'] ?>" data-selected="<?php echo (int) in_array( $location['id'], $assigned_location_ids ) ?>">
                <?php echo esc_html( $location['name'] ) ?>
            </li>
        <?php endforeach ?>
    </ul>
</div>