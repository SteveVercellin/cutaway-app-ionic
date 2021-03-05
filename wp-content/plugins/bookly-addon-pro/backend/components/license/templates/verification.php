<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib as BooklyLib;
use BooklyPro\Lib\Config;
?>
<div>
    <p><?php printf( __( 'Cannot find your purchase code? See this <a href="%s" target="_blank">page</a>.', 'bookly' ), 'https://help.market.envato.com/hc/en-us/articles/202822600-Where-can-I-find-my-Purchase-Code' ) ?></p>
    <?php
    $addons = apply_filters( 'bookly_plugins', array() );
    unset ( $addons[ BooklyLib\Plugin::getSlug() ] );
    /** @var \Bookly\Lib\Base\Plugin $plugin_class */
    foreach ( $addons as $plugin_class ) :
        if ( $plugin_class::getPurchaseCode() == '' && ! $plugin_class::embedded() ) :
            printf(
                '<div class="form-group %4$s has-feedback">
                    <label for="%2$s">%1$s:</label>
                    <input id="%2$s" class="purchase-code form-control bookly-margin-bottom-xs" type="text" value="%3$s" />
                    <span class="alert-icon form-control-feedback" aria-hidden="true"></span>
                    </div>',
                $plugin_class::getTitle() . ' ' . __( 'Purchase Code', 'bookly' ),
                $plugin_class::getRootNamespace(),
                $plugin_class::getPurchaseCode(),
                $plugin_class::getPurchaseCode() == '' ? 'has-warning' : 'has-success'
            );
        endif;
    endforeach ?>
</div>
<?php if ( ! Config::graceExpired() ) : ?>
    <div class="btn-group-vertical align-left bookly-verified" role="group" style="display: none">
        <a href="" class="btn btn-link" data-trigger="temporary-hide"><span class="text-warning"><i class="glyphicon glyphicon glyphicon-time"></i> <?php esc_html_e( 'Proceed without license verification', 'bookly' ) ?></span></a>
    </div>
<?php endif ?>