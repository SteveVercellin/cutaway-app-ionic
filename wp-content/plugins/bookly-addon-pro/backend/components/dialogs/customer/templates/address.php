<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$address_show_fields = (array) get_option( 'bookly_cst_address_show_fields', array() );

foreach ( $address_show_fields as $field_name => $field ) : ?>
    <div class="form-group">
        <label for="<?php echo $field_name; ?>"><?php esc_html_e( get_option( 'bookly_l10n_label_' . $field_name ), 'bookly' ) ?></label>
        <input class="form-control" type="text" ng-model=form.<?php echo $field_name; ?> id="<?php echo $field_name; ?>"/>
    </div>
<?php endforeach ?>