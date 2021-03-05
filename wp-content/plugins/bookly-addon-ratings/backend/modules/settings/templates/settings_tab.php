<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Components\Settings\Selects;
use Bookly\Lib\Utils\DateTime;
?>
<div class="tab-pane" id="bookly_settings_ratings">
    <form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'ratings' ) ) ?>">
        <?php Selects::renderSingle( 'bookly_ratings_show_at_backend', __( 'Displaying appointments rating in the backend', 'bookly' ), __( 'Enable this setting to display ratings in the back-end.', 'bookly' ) ) ?>
        <?php
        $values = array();
        foreach ( array( 1, 2, 3, 4, 5, 6, 7, 14, 21, 30 ) as $days ) {
            $values[] = array( $days, DateTime::secondsToInterval( $days * DAY_IN_SECONDS ) );
        }
        Selects::renderSingle( 'bookly_ratings_timeout', __( 'Timeout for rating appointment', 'bookly' ), __( 'Set a period of time after appointment when customer can rate and leave feedback for your services.', 'bookly' ), $values );
        $values = array();
        foreach ( array( 7, 14, 21, 30, 60, 90, 120, 180, 270, 365, 545, 730 ) as $days ) {
            $values[] = array( $days, DateTime::secondsToInterval( $days * DAY_IN_SECONDS ) );
        }
        Selects::renderSingle( 'bookly_ratings_period', __( 'Period for calculating rating average', 'bookly' ), __( 'Set a period of time during which the rating average is calculated.', 'bookly' ), $values );
        ?>
        <div class="panel-footer">
            <?php Inputs::renderCsrf() ?>
            <?php Buttons::renderSubmit() ?>
            <?php Buttons::renderReset() ?>
        </div>
    </form>
</div>