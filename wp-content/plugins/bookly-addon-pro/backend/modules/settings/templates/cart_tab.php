<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Lib\Config;
?>
<div class="tab-pane" id="bookly_settings_cart">
    <form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'cart' ) ) ?>">
        <div class="form-group">
            <label for="bookly_cart_show_columns"><?php esc_html_e( 'Columns', 'bookly' ) ?></label><br/>
            <p class="help-block">
                <?php if ( Config::cartActive() ) : ?>
                    <?php esc_html_e( 'Select columns that you want to display in a cart summary before the booking confirmation. Uncheck the box to hide the column. Drag the sandwich icon to change the order of fields.', 'bookly' ) ?>
                <?php else : ?>
                    <?php esc_html_e( 'If you use {cart_info} code in notifications, you can select the columns that you want to display and set the order of fields here. Uncheck the box to hide the column.', 'bookly' ) ?>
                <?php endif ?>
            </p>
            <div class="bookly-flags" id="bookly_cart_show_columns">
                <?php foreach ( (array) get_option( 'bookly_cart_show_columns' ) as $column => $attr ) : ?>
                    <div class="bookly-flexbox"<?php if ( ( $column == 'deposit' && ! Config::depositPaymentsActive() )
                        || ( $column == 'tax' && ! Config::taxesActive() ) ) : ?> style="display:none"<?php endif ?>>
                        <div class="bookly-flex-cell">
                            <i class="bookly-js-handle bookly-margin-right-sm bookly-icon bookly-icon-draghandle bookly-cursor-move" title="<?php esc_attr_e( 'Reorder', 'bookly' ) ?>"></i>
                        </div>
                        <div class="bookly-flex-cell" style="width: 100%">
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="bookly_cart_show_columns[<?php echo $column ?>][show]" value="0">
                                    <input type="checkbox"
                                           name="bookly_cart_show_columns[<?php echo $column ?>][show]"
                                           value="1" <?php checked( $attr['show'], true ) ?>>
                                    <?php echo isset( $cart_columns[ $column ] ) ? $cart_columns[ $column ] : '' ?>
                                </label>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
        <div class="panel-footer">
            <?php Inputs::renderCsrf() ?>
            <?php Buttons::renderSubmit() ?>
            <?php Buttons::renderReset() ?>
        </div>
    </form>
</div>