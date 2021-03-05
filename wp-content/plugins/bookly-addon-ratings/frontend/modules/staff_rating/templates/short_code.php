<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<div id="bookly-staff-rating" class="bookly-js-staff-rating-<?php echo $form_id ?> ">
    <div id="bookly-tbs">
    <?php if ( $ca ) : ?>
        <?php if ( $expired ) : ?>
            <p><?php _e( 'The feedback period has expired.', 'bookly' ) ?></p>
        <?php elseif( $not_started ) : ?>
            <p><?php _e( 'You cannot rate this service before appointment.', 'bookly' ) ?></p>
        <?php else : ?>
            <div class="bookly-js-rating-wrap">
                <div class="form-group">
                    <?php printf( __( 'Rate the quality of the %s provided to you on %s at %s by %s', 'bookly' ), $ca['service_title'], $ca['date'], $ca['time'], $ca['staff_name'] ) ?>
                </div>
                <div class="form-group">
                    <select class="bookly-js-rating">
                        <option value=""></option>
                        <?php for ( $i = 1; $i <= 5; ++ $i ): ?>
                            <option value="<?php echo $i ?>" <?php selected( $i, $ca['rating'] ) ?>><?php echo $i ?></option>
                        <?php endfor ?>
                    </select>
                </div>
                <?php if ( ! isset( $attributes['hide'] ) || $attributes['hide'] != 'comment' ) : ?>
                    <div class="form-group">
                        <textarea class="form-control bookly-js-rating-comment" placeholder="<?php esc_attr_e( 'Leave your comment', 'bookly' ) ?>" style="margin-top: 20px;"><?php echo esc_textarea( $ca['rating_comment'] ) ?></textarea>
                    </div>
                <?php endif ?>
                <div class="form-group">
                    <?php Buttons::renderSubmit() ?>
                </div>
            </div>
            <p class="bookly-js-rating-success" style="display: none;">
                <?php _e( 'Your rating has been saved. We appreciate your feedback.', 'bookly' ) ?>
            </p>
        <?php endif ?>
    <?php endif ?>
    </div>
</div>

<script type="text/javascript">
    (function (win, fn) {
        var done = false, top = true,
            doc = win.document,
            root = doc.documentElement,
            modern = doc.addEventListener,
            add = modern ? 'addEventListener' : 'attachEvent',
            rem = modern ? 'removeEventListener' : 'detachEvent',
            pre = modern ? '' : 'on',
            init = function(e) {
                if (e.type == 'readystatechange') if (doc.readyState != 'complete') return;
                (e.type == 'load' ? win : doc)[rem](pre + e.type, init, false);
                if (!done) { done = true; fn.call(win, e.type || e); }
            },
            poll = function() {
                try { root.doScroll('left'); } catch(e) { setTimeout(poll, 50); return; }
                init('poll');
            };
        if (doc.readyState == 'complete') fn.call(win, 'lazy');
        else {
            if (!modern) if (root.doScroll) {
                try { top = !win.frameElement; } catch(e) { }
                if (top) poll();
            }
            doc[add](pre + 'DOMContentLoaded', init, false);
            doc[add](pre + 'readystatechange', init, false);
            win[add](pre + 'load', init, false);
        }
    })(window, function() {
        if (jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> .bookly-js-rating').val() == '') {
            jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> #bookly-save').prop('disabled', true);
        }
        jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> .bookly-js-rating').barrating({
            theme     : 'bootstrap-stars',
            allowEmpty: false,
            onSelect  : function (value, text, event) {
                jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> #bookly-save').prop('disabled', false);
            }
        });
        jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> button').on('click', function () {
            if (jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> .bookly-js-rating').val()) {
                var ladda = Ladda.create(this);
                ladda.start();
                jQuery.post({
                    url        : <?php echo json_encode( $ajax_url ) ?>,
                    data       : {
                        action    : 'bookly_ratings_set_rating',
                        csrf_token: BooklyStaffRatingL10n.csrf_token,
                        token     : <?php echo json_encode( $token ) ?>,
                        rating    : jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> .bookly-js-rating').val(),
                        comment   : jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> .bookly-js-rating-comment').val()
                    },
                    dataType   : 'json',
                    xhrFields  : {withCredentials: true},
                    crossDomain: 'withCredentials' in new XMLHttpRequest(),
                    success    : function (response) {
                        ladda.stop();
                        jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> .bookly-js-rating-wrap').hide();
                        jQuery('.bookly-js-staff-rating-<?php echo $form_id ?> .bookly-js-rating-success').show();
                    }
                });
            }
        });
    });
</script>