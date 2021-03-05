<?php

define('CAM_WP_EMAIL_QUEUE_TABLE', 'cam_wp_email_queue');
define('CAM_WP_EMAIL_QUEUE_CRON_LIMIT_RECORDS_PROCESS', 5);
define('CAM_WP_EMAIL_QUEUE_CRON_TIME_SCHEDULE_PERIOD', 2);
define('CAM_WP_EMAIL_QUEUE_OPTION_CRON_PROCESSING', '_cam_wp_email_queue_cron_processing');
define('CAM_WP_EMAIL_QUEUE_OPTION_CRON_LAST_PROCESS_TIME', '_cam_wp_email_queue_cron_last_process_time');
define('CAM_WP_EMAIL_QUEUE_STOP_WP_CRON', true);
define('CAM_WP_EMAIL_QUEUE_STOP_HTTP_CRON', false);