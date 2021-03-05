<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Components\Settings\Selects;
?>
<div class="tab-pane" id="bookly_settings_locations">
    <form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'locations' ) ) ?>">
        <?php Selects::renderSingle( 'bookly_locations_allow_services_per_location', __( 'Custom settings for location', 'bookly' ), __( 'Enable this setting to be able to set custom settings for staff members for different locations.', 'bookly' ) ) ?>
        <div class="panel-footer">
            <?php Inputs::renderCsrf() ?>
            <?php Buttons::renderSubmit() ?>
            <?php Buttons::renderReset() ?>
        </div>
    </form>
</div>