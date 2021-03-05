<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib\Config;
use Bookly\Lib\Entities\Service;
use Bookly\Backend\Modules\Services\Proxy;
?>
<div class="form-group"><label><?php esc_html_e( 'Visibility', 'bookly' ) ?></label>
    <p class="help-block"><?php esc_html_e( 'To make service invisible to your customers set the visibility to "Private".', 'bookly' ) ?></p>
    <div class="radio"><label><input type="radio" name="visibility" value="public" <?php checked( $service['visibility'], Service::VISIBILITY_PUBLIC ) ?>><?php esc_html_e( 'Public', 'bookly' ) ?></label></div>
    <div class="radio"><label><input type="radio" name="visibility" value="private" <?php checked( $service['visibility'] == Service::VISIBILITY_PRIVATE || ( $service['visibility'] == Service::VISIBILITY_GROUP_BASED && ! Config::customerGroupsActive() ) ) ?>><?php esc_html_e( 'Private', 'bookly' ) ?></label></div>
    <?php Proxy\CustomerGroups::renderVisibilityOption( $service ) ?>
</div>