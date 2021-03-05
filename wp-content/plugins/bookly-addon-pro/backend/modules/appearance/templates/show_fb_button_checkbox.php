<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BooklyPro\Lib\Config;
?>
<div class="col-md-3">
    <div class="checkbox">
        <label>
            <input type="checkbox" id="bookly-show-facebook-login-button" <?php checked( Config::showFacebookLoginButton() ) ?> data-appid="<?php echo Config::getFacebookAppId() ?>"/>
            <?php esc_html_e( 'Show Facebook login button', 'bookly' ) ?>
        </label>
    </div>
</div>