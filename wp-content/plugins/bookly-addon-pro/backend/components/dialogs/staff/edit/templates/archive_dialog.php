<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<form id="bookly-archiving-confirmation" class="modal fade" tabindex=-1>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close bookly-js-close" data-dismiss="modal"><span>&times;</span></button>
                <div class="modal-title h2"><?php esc_html_e( 'Archiving Staff', 'bookly' ) ?></div>
            </div>
            <div class="modal-body">
                <p>
                    <?php esc_html_e( 'You are going to archive item which is involved in upcoming appointments. Please double check and edit appointments before this item archive if needed.', 'bookly' ) ?>
                </p>
            </div>
            <div class="modal-footer">
                <?php Buttons::renderCustom( null, 'btn-lg btn-success', esc_html__( 'Ok, continue editing', 'bookly' ), array( 'style' => 'display:none;', 'data-dismiss' => 'modal' ) ) ?>
                <?php Buttons::renderCustom( null, 'btn-lg btn-danger bookly-js-staff-archive', esc_html__( 'Archive', 'bookly' ), array() ) ?>
                <?php Buttons::renderCustom( null, 'btn-lg btn-success bookly-js-edit', __( 'Edit appointments', 'bookly' ) ) ?>
                <?php Buttons::renderCustom( null, 'btn-lg btn-default bookly-js-close', esc_html__( 'Cancel', 'bookly' ), array( 'data-dismiss' => 'modal' ) ) ?>
            </div>
        </div>
    </div>
</form>