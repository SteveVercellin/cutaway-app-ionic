<?php
/**
 * cutaway functions and definitions
 *
 * @package cutaway
 */

/**
 * Helper functions
 */
require get_template_directory() . '/inc/helpers.php';

/**
 * Initialize theme default settings
 */
require get_template_directory() . '/inc/theme-settings.php';

/**
 * Theme setup and custom theme supports.
 */
require get_template_directory() . '/inc/setup.php';

/**
 * Enqueue scripts and styles.
 */
require get_template_directory() . '/inc/enqueue.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Shortcodes.
 */
require get_template_directory() . '/inc/shortcodes.php';

/**
 * Custom post type.
 */
require get_template_directory() . '/inc/post-types.php';

/**
 * Page steps.
 */
require get_template_directory() . '/inc/page-steps.php';

/**
 * ACF filters.
 */
require get_template_directory() . '/inc/acf/filters.php';

add_action( 'init', function () {
    if ( !empty( $_GET['_update_bookly_time_block_appointment'] ) ) {
        global $wpdb;
        $test = !empty( $_GET['_test'] );

        echo "<pre>";

        $shopController = new REST_Shop_Controller();
        $booklyAppAdapter = \BooklyAdapter\Entities\Appointment::getInstance();
        $booklyServiceAdapter = \BooklyAdapter\Entities\Service::getInstance();

        $appGroups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bookly_appointments_group");
        foreach ( $appGroups as $appGroup ) {
            $groupId = $appGroup->id;
            $staffId = $appGroup->staff_id;
            $work_time = $appGroup->start_date;
            $work_time = explode( ' ', $work_time );
            $work_time = str_replace( '-', '/', $work_time[0] );
            $day_index = $shopController->getDayIndex($work_time);
            $staff_query = \Bookly\Lib\Entities\Staff::query('s')
            ->select('s.id, s.full_name, s.attachment_id, s.info, ssi.start_time as start_time, ssi.end_time as end_time, TIME_TO_SEC(TIMEDIFF(ssi.end_time, ssi.start_time))/60 as total_time, COUNT(ss.id) AS service_count')
            ->innerJoin('StaffService', 'ss', 'ss.staff_id = s.id')
            ->innerJoin('StaffScheduleItem', 'ssi', 'ssi.staff_id = s.id')
            ->where('s.id', $staffId)
            ->where('ssi.day_index', $day_index)
            ->groupBy('s.id');
            $staff = $staff_query->fetchRow();
            $apps = $appGroup->appointments;
            $apps = maybe_unserialize( $apps );
            if ( !empty( $staff ) && count( $apps ) == 1 ) {
                $block_times_store = array();
                var_dump($groupId);

                foreach ( $apps as $app ) {
                    $appDetail = $booklyAppAdapter->getAppointment( $app );
                    if ( !empty( $appDetail ) ) {
                        var_dump($staffId);
                        var_dump($work_time);
                        var_dump($day_index);
                        var_dump($staff);
                        var_dump($app);
                        var_dump($appDetail);

                        $service = array( $appDetail['service_id'] );var_dump($service);
                        $serviceTimeAndPrice = $booklyServiceAdapter->getServicesTimeAndPrice($service);
                        var_dump($serviceTimeAndPrice);
                        $time_service = $serviceTimeAndPrice['time'];
                        $timeSlot = (int) \Bookly\Lib\Config::getTimeSlotLength() / 60;

                        $block_times = $shopController->buildBlockTime($staffId, $service, $work_time, $staff['start_time'], $staff['end_time'], $staff['total_time'], $timeSlot, $time_service, true);
                        var_dump($block_times);
                        foreach ( $block_times as $block_time ) {
                            $block_times_store[] = $block_time['start_time'];
                        }
                        var_dump($block_times_store);
                    }
                }

                if ( !$test ) {
                    $booklyAppointmentGroupMeta = new \BooklyAdapter\Classes\AppointmentGroupMeta( $groupId );
                    $booklyAppointmentGroupMeta->updateAppointmentGroupMeta( '_bookly_booked_block_times', $block_times_store );
                }
            }
        }

        echo "</pre>";

        exit;
    }
} );