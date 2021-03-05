<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div id="bookly-tbs" class="wrap">
    <div id="bookly-js-powered-by" class="alert alert-info bookly-tbs-body bookly-flexbox" data-action="bookly_dismiss_powered_by_notice">
        <div class="bookly-flex-row">
            <div class="bookly-flex-cell" style="width:39px"><i class="alert-icon"></i></div>
            <div class="bookly-flex-cell">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php esc_html_e( 'Allow the plugin to set a Powered by Bookly notice on the booking widget to spread information about the plugin. This will allow the team to improve the product and enhance its functionality.', 'bookly' ) ?>
                <div class="bookly-margin-top-md">
                    <button type="button" class="btn btn-default" data-dismiss="alert"><?php esc_html_e( 'Disagree', 'bookly' ) ?></button>
                    <button type="button" class="btn btn-success" id="bookly-js-show-powered-by"><?php esc_html_e( 'Agree', 'bookly' ) ?></button>
                </div>
            </div>
        </div>
    </div>
</div>