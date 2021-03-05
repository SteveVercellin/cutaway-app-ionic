<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
?>
<style>
    .pac-container {
        z-index: 1100 !important;
    }
</style>
<div class="modal fade" id="bookly-location-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="bookly-new-locations-title"><?php _e( 'New Location', 'bookly' ) ?></h4>
                    <h4 class="modal-title" id="bookly-edit-locations-title"><?php _e( 'Edit Location', 'bookly' ) ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class=form-group>
                                <label for="bookly-location-name"><?php _e( 'Name', 'bookly' ) ?></label>
                                <input type="text" id="bookly-location-name" class="form-control google-map-enter-address" />
                                <div id="google-map-wrap" class="form-control" style="width: 100%; height: 250px;"></div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class=form-group>
                                <label for="bookly-location-info"><?php _e( 'Info', 'bookly' ) ?></label>
                                <p class="help-block">
                                    <?php printf( __( 'This text can be inserted into notifications with %s code.', 'bookly' ), '{location_info}' ) ?>
                                </p>
                                <textarea id="bookly-location-info" class="form-control" name="info"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12" >
                            <ul id="bookly-js-staff"
                                data-txt-select-all="<?php esc_attr_e( 'All staff', 'bookly' ) ?>"
                                data-txt-all-selected="<?php esc_attr_e( 'All staff', 'bookly' ) ?>"
                                data-txt-nothing-selected="<?php esc_attr_e( 'No staff selected', 'bookly' ) ?>"
                            >
                                <?php foreach ( $staff_dropdown_data as $category_id => $category ): ?>
                                    <li<?php if ( ! $category_id ) : ?> data-flatten-if-single<?php endif ?>><?php echo esc_html( $category['name'] ) ?>
                                        <ul>
                                            <?php foreach ( $category['items'] as $staff ) : ?>
                                                <li data-input-name="staff_ids[]" data-value="<?php echo $staff['id'] ?>">
                                                    <?php echo esc_html( $staff['full_name'] ) ?>
                                                </li>
                                            <?php endforeach ?>
                                        </ul>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php Inputs::renderCsrf() ?>
                    <?php Buttons::renderSubmit( 'bookly-location-save' ) ?>
                    <button class="btn btn-lg btn-default" data-dismiss="modal">
                        <?php _e( 'Cancel', 'bookly' ) ?>
                    </button>
                </div>
                <input id="bookly-location-address" name='name' type='hidden' value='' />
                <input id="bookly-location-lat" name='lat' type='hidden' value='' />
                <input id="bookly-location-long" name='lng' type='hidden' value='' />
            </form>
        </div>
    </div>
</div>