<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * @var array $locations
 */
?>
<ul id="bookly-js-locations-filter"
    data-container-class="pull-right bookly-margin-top-xs bookly-margin-right-sm"
    data-icon-class="dashicons dashicons-location"
    data-txt-select-all="<?php esc_attr_e( 'All locations', 'bookly' ) ?>"
    data-txt-all-selected="<?php esc_attr_e( 'All locations', 'bookly' ) ?>"
    data-txt-nothing-selected="<?php esc_attr_e( 'No locations selected', 'bookly' ) ?>"
>
    <li data-value="no">
        <?php esc_html_e( 'W/o location', 'bookly' ) ?>
    </li>
    <?php foreach ( $locations as $location ): ?>
        <li data-value="<?php echo $location['id'] ?>">
            <?php echo esc_html( $location['name'] ) ?>
        </li>
    <?php endforeach ?>
</ul>