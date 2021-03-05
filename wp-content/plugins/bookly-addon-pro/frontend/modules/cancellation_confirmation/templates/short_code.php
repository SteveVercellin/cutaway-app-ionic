<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    $color = get_option( 'bookly_app_color', '#f4662f' );
?>
<style>
    .bookly-btn-default {
        padding: 9px 18px!important;
        border: 0!important;
        min-width: 118px;
        display: block;
        text-align: center;
        border-radius: 4px!important;
        background: #f4662f;
        cursor: pointer!important;
        height: auto!important;
        outline: none!important;
        text-decoration: none;
    }

    .bookly-btn-default, .bookly-btn-default > span {
        color: #fff!important;
        font-size: 18px!important;
        line-height: 17px!important;
        font-weight: bold!important;
        text-transform: uppercase!important;
    }
</style>
<div id="bookly-tbs" class="bookly-js-cancellation-confirmation">
    <div class="bookly-js-cancellation-confirmation-buttons">
        <a href="<?php echo admin_url( 'admin-ajax.php?action=bookly_cancel_appointment&token=' . $token ) ?>" class="bookly-btn-default" style="float: left; background: <?php echo $color ?>!important; width: auto; margin-right: 12px">
            <span><?php _e( 'Confirm', 'bookly' ) ?></span>
        </a>
        <a href="#" class="bookly-js-cancellation-confirmation-no bookly-btn-default" style="float: left; background: <?php echo $color ?>!important; width: auto">
            <span><?php _e( 'Cancel', 'bookly' ) ?></span>
        </a>
    </div>
    <div class="bookly-js-cancellation-confirmation-message bookly-row" style="display: none">
        <p class="bookly-bold">
            <?php _e( 'Thank you for being with us', 'bookly' ) ?>
        </p>
    </div>
</div>
<script type="text/javascript">
    var links = document.getElementsByClassName('bookly-js-cancellation-confirmation-no');
    for (var i = 0; i < links.length; i++) {
        if (links[i].onclick == undefined) {
            links[i].onclick = function (e) {
                e.preventDefault();
                var container = this.closest('.bookly-js-cancellation-confirmation'),
                    buttons = container.getElementsByClassName('bookly-js-cancellation-confirmation-buttons')[0],
                    message = container.getElementsByClassName('bookly-js-cancellation-confirmation-message')[0];
                buttons.style.display = 'none';
                message.style.display = 'inline';
            };
        }
    }
</script>