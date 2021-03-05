<?php

/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 *
 * @wordpress-plugin
 * Plugin Name:       CAM wordpress email queue and cron solutions
 * Description:       CAM wordpress email queue and cron solutions
 * Version:           1.0.0
 * Author:            congnguyentan (CAM)
 * Text Domain:       cam-wordpress-email-queue
 */

require_once('includes/constants.php');
require_once('includes/wordpress_clone_functions.php');

function camWordpressEmailQueueInitTable()
{
    global $wpdb;

    $wpTable = $wpdb->prefix . CAM_WP_EMAIL_QUEUE_TABLE;

    $sqlCheckTableExists = $wpdb->prepare('SHOW TABLES LIKE %s', $wpTable);
    $tableExists = $wpdb->get_var($sqlCheckTableExists);

    if (empty($tableExists)) {
        $sql = "CREATE TABLE IF NOT EXISTS $wpTable(
            `id` INT NOT NULL AUTO_INCREMENT,
            `to` TEXT NOT NULL,
            `subject` VARCHAR(255) NOT NULL,
            `message` TEXT NOT NULL,
            `headers` TEXT NOT NULL,
            `attachments` TEXT NOT NULL,
            `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `processed` TINYINT NOT NULL DEFAULT 0,
            `error` TEXT,
            PRIMARY KEY (`id`)
        ) ENGINE = InnoDB";

        $wpdb->query($sql);
    }
}

function camWordpressEmailQueueHandleWpMail($data)
{
    global $wpdb;

    camWordpressEmailQueueInitTable();

    $wpTable = $wpdb->prefix . CAM_WP_EMAIL_QUEUE_TABLE;

    $default = array(
        'to' => '',
        'subject' => '',
        'message' => '',
        'headers' => '',
        'attachments' => ''
    );
    $dataEmail = array_merge($default, $data);

    if (!empty($dataEmail['to']) && !empty($dataEmail['subject'])) {
        $wpdb->insert(
            $wpTable,
            array(
                'to' => maybe_serialize($dataEmail['to']),
                'subject' => $dataEmail['subject'],
                'message' => $dataEmail['message'],
                'headers' => maybe_serialize($dataEmail['headers']),
                'attachments' => maybe_serialize($dataEmail['attachments'])
            ),
            array('%s', '%s', '%s', '%s', '%s')
        );
    }

    return $data;
}
add_filter('wp_mail', 'camWordpressEmailQueueHandleWpMail', 9999, 1);

function camWordpressEmailQueueCheckShouldStopWpMail($data)
{
    return '';
}
add_filter('wp_mail_from', 'camWordpressEmailQueueCheckShouldStopWpMail', 9999, 1);
add_filter('wp_mail_from_name', 'camWordpressEmailQueueCheckShouldStopWpMail', 9999, 1);

function camWordpressEmailQueueCronSendMail($debug = false, $env = 'wp')
{
    global $wpdb;

    camWordpressEmailQueueInitTable();

    if ($env == 'wp' && CAM_WP_EMAIL_QUEUE_STOP_WP_CRON) {
        return;
    }

    $wpTable = $wpdb->prefix . CAM_WP_EMAIL_QUEUE_TABLE;

    $checkCronProcessing = get_option(CAM_WP_EMAIL_QUEUE_OPTION_CRON_PROCESSING, 0);
    if ($checkCronProcessing) {
        return;
    }

    update_option(CAM_WP_EMAIL_QUEUE_OPTION_CRON_PROCESSING, 1);

    if ($debug) {
        echo '<pre>';
    }

    $sql = $wpdb->prepare("SELECT * FROM {$wpTable} WHERE processed = %d ORDER BY created LIMIT 0, %d", 0, CAM_WP_EMAIL_QUEUE_CRON_LIMIT_RECORDS_PROCESS);
    $records = $wpdb->get_results($sql, ARRAY_A);

    if (!empty($records)) {
        foreach ($records as $item) {
            if ($debug) {
                var_dump($item);
            }

            $to = $item['to'];
            $to = maybe_unserialize($to);
            $subject = $item['subject'];
            $message = $item['message'];
            $headers = $item['headers'];
            $headers = maybe_unserialize($headers);
            $attachments = $item['attachments'];
            $attachments = maybe_unserialize($attachments);

            if ($debug) {
                var_dump(compact( 'to', 'subject', 'message', 'headers', 'attachments' ));
            }

            $result = cam_wordpress_email_queue_wp_mail($to, $subject, $message, $headers, $attachments);
            if ($debug) {
                var_dump($result);
            }

            if ($result['status'] == 'ok') {
                $wpdb->delete($wpTable, array('id' => $item['id']), array('%d'));
            } else {
                $updated = $wpdb->update($wpTable, array(
                    'processed' => 1,
                    'error' => $result['error']
                ), array('id' => $item['id']), array('%d', '%s'), array('%d'));
                if (!$updated) {
                    $wpdb->delete($wpTable, array('id' => $item['id']), array('%d'));
                }
            }
        }
    }

    if ($debug) {
        echo '</pre>';
    }

    update_option(CAM_WP_EMAIL_QUEUE_OPTION_CRON_LAST_PROCESS_TIME, time());
    update_option(CAM_WP_EMAIL_QUEUE_OPTION_CRON_PROCESSING, 0);
}
add_action("action_cam_wordpress_email_queue_process", "camWordpressEmailQueueCronSendMail");

function camWordpressEmailQueueHandleHttpCron()
{
    $httpCron = !empty($_REQUEST['_cam_wp_email_queue_http_cron']) ? $_REQUEST['_cam_wp_email_queue_http_cron'] : '';
    if (!empty($httpCron)) {
        $httpCron = ucfirst(str_replace('_', ' ', $httpCron));
        $httpCron = str_replace(' ', '', $httpCron);
        $funcCall = 'camWordpressEmailQueueHttpCron' . $httpCron;

        if (function_exists($funcCall)) {
            $funcCall();
        }
        exit;
    }
}
add_action('init', 'camWordpressEmailQueueHandleHttpCron', 99);

function camWordpressEmailQueueHttpCronTest()
{
    if (!CAM_WP_EMAIL_QUEUE_STOP_HTTP_CRON) {
        camWordpressEmailQueueCronSendMail(true, 'http');
    }
}
function camWordpressEmailQueueHttpCronProcess()
{
    if (!CAM_WP_EMAIL_QUEUE_STOP_HTTP_CRON) {
        camWordpressEmailQueueCronSendMail(false, 'http');
    }
}

function camWordpressEmailQueueDefineWpScheduleInterval($schedules){
	$schedules['every_' . CAM_WP_EMAIL_QUEUE_CRON_TIME_SCHEDULE_PERIOD . '_minutes'] = array(
        'interval'  => CAM_WP_EMAIL_QUEUE_CRON_TIME_SCHEDULE_PERIOD * 60,
        'display'   => __( 'Every ' . CAM_WP_EMAIL_QUEUE_CRON_TIME_SCHEDULE_PERIOD . ' Minutes', 'cam-wordpress-email-queue' )
    );

    return $schedules;
}
add_filter( 'cron_schedules', 'camWordpressEmailQueueDefineWpScheduleInterval' );

function camWordpressEmailQueueCreateWpSchedule() {
	if ( ! wp_next_scheduled( 'action_cam_wordpress_email_queue_process' ) ) {
        wp_schedule_event( time(), 'every_' . CAM_WP_EMAIL_QUEUE_CRON_TIME_SCHEDULE_PERIOD . '_minutes', 'action_cam_wordpress_email_queue_process' );
    }
}
add_action('init', 'camWordpressEmailQueueCreateWpSchedule');

/**
 * Replace wp_mail function of wordpress
 */
if (!function_exists('wp_mail'))
{
    function wp_mail($to, $subject, $message, $headers = '', $attachments = array())
    {
        /**
    	 * Filters the wp_mail() arguments.
    	 *
    	 * @since 2.2.0
    	 *
    	 * @param array $args A compacted array of wp_mail() arguments, including the "to" email,
    	 *                    subject, message, headers, and attachments values.
    	 */
    	$atts = apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );
    	
    	return true;
    }
}