<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div id="bookly-tinymce-ratings-popup" style="display: none">
    <form id="bookly-ratings-shortcode-form">
        <table>
            <tr>
                <td class="bookly-title-col"><?php esc_html_e( 'Rating', 'bookly' ) ?></td>
                <td>
                    <label><input type="checkbox" data-rating-column="rating" checked disabled/></label>
                </td>
            </tr>
            <tr>
                <td class="bookly-title-col"><?php esc_html_e( 'Comment', 'bookly' ) ?></td>
                <td>
                    <label><input type="checkbox" data-rating-column="comment" checked/></label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button class="button button-primary bookly-js-insert-shortcode" type="button"><?php esc_attr_e( 'Insert', 'bookly' ) ?></button>
                </td>
            </tr>
        </table>
    </form>
</div>

<style type="text/css">
    #bookly-ratings-shortcode-form { margin-top: 15px; }
    #bookly-ratings-shortcode-form table td { padding: 5px; vertical-align: 0; }
    #bookly-ratings-shortcode-form table td.bookly-title-col { width: 80px; }
</style>

<script type="text/javascript">
    jQuery(function ($) {
        var $form    = $('#bookly-ratings-shortcode-form'),
            $insert  = $('button.bookly-js-insert-shortcode',$form),
            $add_staff_rating = $('#add-staff-rating'),
            $comment = $('[data-rating-column="comment"]',$form);

        $add_staff_rating.on('click', function () {
            window.parent.tb_show(<?php echo json_encode( __( 'Add staff rating form', 'bookly' ) ) ?>, this.href);
            window.setTimeout(function () {
                $('#TB_window').css({
                    'overflow-x': 'auto',
                    'overflow-y': 'hidden'
                });
            }, 100);
        });
        $insert.on('click', function (e) {
            e.preventDefault();
            var comment = $comment.is(':checked') ? '' : ' hide="comment"';
            window.send_to_editor('[bookly-staff-rating' + comment + ']');
            window.parent.tb_remove();
            return false;
        });
    });
</script>