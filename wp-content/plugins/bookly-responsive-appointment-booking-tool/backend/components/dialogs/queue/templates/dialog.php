<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
?>
<form id="bookly-queue-modal" class="modal fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                <div class="modal-title h2"><?php esc_html_e( 'Send notifications', 'bookly' ) ?></div>
            </div>
            <div class="modal-body">
                <div id="bookly-queue"></div>
            </div>
            <div class="modal-footer">
                <?php Inputs::renderCsrf() ?>
                <?php Buttons::renderCustom( null, 'bookly-js-send btn-lg btn-success', esc_html__( 'Send', 'bookly' ) ) ?>
                <?php Buttons::renderCustom( null, 'bookly-js-cancel btn-lg btn-default', esc_html__( 'Close', 'bookly' ), array( 'data-dismiss' => 'modal' ) ) ?>
            </div>
        </div>
    </div>
</form>
<div id="bookly-notification-template" class="collapse">
    <div class="bookly-js-notification-queue checkbox bookly-margin-bottom-lg bookly-margin-top-remove">
        <label>
            <input type="checkbox" data-index="{{index}}" checked/> <i class="fa fa-fw {{icon}}"></i> <b>{{recipient}}</b> ({{address}})<br/>
            {{description}}
        </label>
    </div>
</div>