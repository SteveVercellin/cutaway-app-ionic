<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div id="bookly-tbs" class="wrap">
    <div class="alert alert-info bookly-tbs-body bookly-flexbox">
        <div class="bookly-flex-row">
            <div class="bookly-flex-cell" style="width:39px"><i class="alert-icon"></i></div>
            <div class="bookly-flex-cell">
                <button type="button" class="close" data-dismiss="alert" data-trigger="temporary-hide">&times;</button>
                <span class="h4"><?php esc_html_e( 'Bookly Pro - License verification required', 'bookly' ) ?></span>
                <p></p>
                <p><?php esc_html_e( 'Access to your bookings has been disabled.', 'bookly' ) ?></p>
                <p><?php esc_html_e( 'To enable access to your bookings, please contact your website administrator to verify your license by providing a valid purchase code.', 'bookly' ) ?></p>
            </div>
        </div>
    </div>
</div>