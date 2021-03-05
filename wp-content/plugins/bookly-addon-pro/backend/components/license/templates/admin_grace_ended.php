<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib as BooklyLib;
?>
<div>
    <p><?php esc_html_e( 'Access to your bookings has been disabled.', 'bookly' ) ?></p>
    <p><?php esc_html_e( 'To enable access to your bookings, please verify your license by providing a valid purchase code.', 'bookly' ) ?></p>
</div>
<div class="btn-group-vertical align-left" role="group">
    <button type="button" class="btn btn-link" data-trigger="request_code"><span class="text-success"><i class="glyphicon glyphicon-ok-sign"></i> <?php esc_html_e( 'I have already made the purchase', 'bookly' ) ?></span></button>
    <a type="button" class="btn btn-link" href="<?php echo BooklyLib\Utils\Common::prepareUrlReferrers( 'https://codecanyon.net/user/ladela/portfolio?ref=ladela', 'grace_ended' ) ?>" target="_blank"><i class="glyphicon glyphicon-shopping-cart"></i> <?php esc_html_e( 'I want to make a purchase now', 'bookly' ) ?></a>
    <button type="button" class="btn ladda-button btn-link bookly-js-deactivate-pro" data-spinner-size="24" data-spinner-color="#333" data-style="zoom-in" data-redirect="1"><i class="glyphicon glyphicon-remove-sign"></i> <?php esc_html_e( 'Deactivate Bookly Pro', 'bookly' ) ?></button>
</div>