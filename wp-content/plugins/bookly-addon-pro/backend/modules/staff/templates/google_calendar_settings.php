<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Modules\Settings\Page as SettingsPage;
use Bookly\Lib\Utils\Common;
/** @var Bookly\Lib\Entities\Staff $staff */
?>
<div class="form-group bookly-js-google-calendar-row">
    <h3><?php esc_html_e( 'Google Calendar integration', 'bookly' ) ?></h3>
    <p class="help-block">
        <?php esc_html_e( 'Synchronize staff member appointments with Google Calendar.', 'bookly' ) ?>
    </p>
    <p>
        <?php if ( isset ( $auth_url ) ) : ?>
            <?php if ( $auth_url ) : ?>
                <a href="<?php echo esc_url( $auth_url ) ?>"><?php esc_html_e( 'Connect', 'bookly' ) ?></a>
            <?php else : ?>
                <?php printf( __( 'Please configure Google Calendar <a href="%s">settings</a> first', 'bookly' ), Common::escAdminUrl( SettingsPage::pageSlug(), array( 'tab' => 'google_calendar' ) ) ) ?>
            <?php endif ?>
        <?php else : ?>
            <?php esc_html_e( 'Connected', 'bookly' ) ?> (<span class="checkbox checkbox-inline" style="margin-top: -4px;padding-left: 1px">
                    <label>
                        <input type="checkbox" name="google_disconnect" value="1"> <?php esc_html_e( 'disconnect', 'bookly' ) ?>
                    </label>
                </span>)
        <?php endif ?>
    </p>
</div>
<?php if ( ! isset ( $auth_url ) ) : ?>
    <div class="form-group bookly-js-google-calendars-list">
        <label for="bookly-calendar-id"><?php esc_html_e( 'Calendar', 'bookly' ) ?></label>
        <p class="help-block">
            <?php esc_html_e( 'When you connect a calendar all future and past events will be synchronized according to the selected synchronization mode. This may take a few minutes. Please wait.', 'bookly' ) ?>
        </p>
        <select class="form-control" name="google_calendar_id" id="bookly-calendar-id">
            <option value=""><?php esc_html_e( '-- Select calendar --', 'bookly' ) ?></option>
            <?php foreach ( $google_calendars as $id => $calendar ) : ?>
                <option value="<?php echo esc_attr( $id ) ?>"<?php selected( $google_calendar_id == $id ) ?>>
                    <?php echo esc_html( $calendar['summary'] ) ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>
<?php endif ?>