<?php
/**
 * Theme basic setup.
 *
 * @package cutaway
 */


// Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

add_action( 'after_setup_theme', 'cutaway_setup' );

if ( ! function_exists ( 'cutaway_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function cutaway_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on cutaway, use a find and replace
		 * to change 'cutaway' to the name of your theme in all the template files
		 */
		load_theme_textdomain( 'cutaway', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		// This theme uses wp_nav_menu() in two locations.
		register_nav_menus( array(
			'user-left-menu' => __( 'User left menu',      'cutaway' ),
			'barber-left-menu'  => __( 'Barber left menu', 'cutaway' ),
		) );

		cutaway_setup_theme_default_settings();
	}
}

if (!class_exists('CutawaySwiperLeftMenu')) {
	class CutawaySwiperLeftMenu extends Walker_Nav_Menu
	{
		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			global $wp_query;
			$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent

			$linkBefore = $args->link_before;
			if ($item->object == 'page' && function_exists('get_field')) {
				$menuPageIcon = get_field('menu_page_icon', $item->object_id);
				if (empty($menuPageIcon)) {
					$menuPageIcon = get_template_directory_uri() . "/images/risorsa-1.png";
				}

				$linkBefore = "<span><img src='{$menuPageIcon}' /></span>";
			}

			// Depth-dependent classes.
			$depth_classes = array(
				( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
				( $depth >=2 ? 'sub-sub-menu-item' : '' ),
				( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
				'menu-item-depth-' . $depth
			);
			$depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

			// Passed classes.
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

			// Build HTML.
			$output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';

			// Link attributes.
			$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
			$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
			$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
			$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
			$attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . '"';

			// Build HTML output and pass through the proper filter.
			$item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
				$args->before,
				$attributes,
				$linkBefore,
				apply_filters( 'the_title', $item->title, $item->ID ),
				$args->link_after,
				$args->after
			);
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
}

add_action("init", "cutaway_handle_http_cron", 99);
if (!function_exists("cutaway_handle_http_cron")) {
	function cutaway_handle_http_cron()
	{
		$cron = !empty($_GET['_cutaway_cron']) ? $_GET['_cutaway_cron'] : "";

		if (!empty($cron)) {
			$funcCall = "cutaway_http_cron_" . $cron;
			if (function_exists($funcCall)) {
				$funcCall();
			}

			exit;
		}
	}
}

if ( ! function_exists( 'cutaway_generate_http_cron_link' ) ) {
	function cutaway_generate_http_cron_link($task, $args = array()) {
		$args = array_merge($args, array('_cutaway_cron' => $task));
		return add_query_arg($args, home_url("/"));
	}
}

if (!function_exists('cutaway_http_cron_handle_paypal_ipn')) {
	function cutaway_http_cron_handle_paypal_ipn()
	{
		$token = !empty($_GET['_encrypt_token']) ? $_GET['_encrypt_token'] : '';
		$dataIpn = !empty($_POST) ? $_POST : array();
		$paypalConfig = cutawayGetThemeOption('paypal');

		update_option('_debug_paypal_handle_ipn_token', $token);
		update_option('_debug_paypal_handle_ipn_data', $dataIpn);

		if (
			empty($token) ||
			empty($dataIpn) ||
			!class_exists('CutawayEncryptDecrypt') ||
			!class_exists('\\BooklyAdapter\\Classes\\AppointmentGroup') ||
			!class_exists('\\Includes\\Classes\\PaypalPayment') ||
			empty($paypalConfig)
		) {
			return;
		}

		$encryptObj = new CutawayEncryptDecrypt();
		$data = $encryptObj->decrypt($token);
		if (empty($data)) {
			return;
		}

		$data = explode(';', $data);
		update_option('_debug_paypal_handle_ipn_token_decrypt', $data);
		if (count($data) != 2) {
			return;
		}

		$customerId = $data[0];
		$group = $data[1];

		$paypal_adr = "https://www.paypal.com/cgi-bin/webscr";
		$paypalEmail = '';
		if ($paypalConfig['payment_mode'] == 'sandbox') {
			$paypal_adr = "https://www.sandbox.paypal.com/cgi-bin/webscr";
			$paypalEmail = $paypalConfig['test_merchant_email'];
		} else {
			$paypalEmail = $paypalConfig['live_merchant_email'];
		}
		$validate_ipn = array('cmd' => '_notify-validate');
        $validate_ipn += stripslashes_deep($dataIpn);
        // Send back post vars to paypal
        $params = array(
            'body' => $validate_ipn,
            'sslverify' => false,
            'timeout' => 60,
            'httpversion' => '1.1',
            'compress' => false,
            'decompress' => false,
            'user-agent' => 'WP PayPal/' . WP_PAYPAL_VERSION
		);
		$response = wp_remote_post($paypal_adr, $params);
		update_option('_debug_paypal_handle_ipn_validate_response', $response);
		if (
			is_wp_error($response) ||
			$response['response']['code'] < 200 ||
			$response['response']['code'] >= 300 ||
			!strstr($response['body'], 'VERIFIED')
		) {
			return;
		}

		$paymentStatus = '';
		if (isset($dataIpn['payment_status'])) {
            $paymentStatus = sanitize_text_field($dataIpn['payment_status']);
		}
		if (!empty($paymentStatus)) {
			$customerAppointmentGroupAdapter = \BooklyAdapter\Classes\AppointmentGroup::getInstance();
			$customerAppointmentGroupAdapter->editGroupCustomerAppointments($group, $customerId, array(
				'paypal_status' => $paymentStatus
			));
		}
		if ($paymentStatus != 'Completed') {
			return;
		}

		$txnId = '';
        if (isset($dataIpn['txn_id'])) {
            $txnId = sanitize_text_field($dataIpn['txn_id']);
		}
		if (empty($txnId)) {
			return;
		}

		$receiveEmail = '';
		if (isset($dataIpn['receiver_email'])) {
			$receiveEmail = sanitize_text_field($dataIpn['receiver_email']);
			if ($receiveEmail != $paypalEmail) {
				return;
			}
		}

		$paypalPayment = new \Includes\Classes\PaypalPayment();
		return $paypalPayment->completeBooking(array(
			'group' => $group
		), $customerId);
	}
}

if (!function_exists('cutaway_http_cron_test')) {
	function cutaway_http_cron_test()
	{
		echo "<pre>";

		$currentUser = wp_get_current_user();
		var_dump($currentUser->user_pass);

		echo "</pre>";
	}
}

if (!function_exists('cutaway_http_cron_process_list_expired_pending_booking')) {
	function cutaway_http_cron_process_list_expired_pending_booking()
	{
		global $wpdb;

		if (
			class_exists('\\BooklyAdapter\\Classes\\AppointmentGroup') ||
			class_exists('\\BooklyAdapter\\Entities\\Staff') ||
			class_exists('\\BooklyAdapter\\Entities\\Appointment')
		) {
			$cronRunningOptionName = '_cron_process_list_expired_pending_booking_running';

			$cronRunning = get_option($cronRunningOptionName, 0);
			$cronRunning = intval($cronRunning);
			if ($cronRunning) {
				echo '<p>Cron is running</p>';
				return;
			}

			update_option($cronRunningOptionName, 1);

			$appGroup = \BooklyAdapter\Classes\AppointmentGroup::getInstance();
			$booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
			$booklyAppointmentAdapter = \BooklyAdapter\Entities\Appointment::getInstance();
			$listExpiredPendingBooking = $appGroup->getListPendingAppointmentsToDelete();

			if (!empty($listExpiredPendingBooking)) {
				foreach ($listExpiredPendingBooking as $staff => $bookings) {
					$level = 1;
					printParagraphFollowLevel("Processing staff id {$staff}", $level);

					if (!empty($bookings)) {
						foreach ($bookings as $date => $items) {
							printParagraphFollowLevel("Processing date {$date}", $level + 1);

							if (!empty($items)) {
								$wpdb->query('START TRANSACTION');
								$deletedAllItemsInDate = true;

								foreach ($items as $item) {
									extract($item);
									printParagraphFollowLevel("Processing appointments {$appointments} and start date {$start_date}", $level + 2);

									$appointments = maybe_unserialize($appointments);
									if (empty($appointments)) {
										$deletedAllItemsInDate = false;
									} else {
										foreach ($appointments as $appointment) {
											printParagraphFollowLevel("Deleting appointment {$appointment}", $level + 3);
											if ($booklyAppointmentAdapter->deleteAppointment($appointment)) {
												printParagraphFollowLevel('Done', $level + 4);
											} else {
												printParagraphFollowLevel('Failed', $level + 4);
												$deletedAllItemsInDate = false;
											}
										}

										printParagraphFollowLevel('Deleting appointments group', $level + 3);
										$deleted = $appGroup->deleteAppoinmentGroupByConditions(array(
											'id' => $id
										));

										if ($deleted) {
											printParagraphFollowLevel('Done', $level + 4);
										} else {
											printParagraphFollowLevel('Failed', $level + 4);
											$deletedAllItemsInDate = false;
										}
									}
								}

								if ($deletedAllItemsInDate) {
									printParagraphFollowLevel('Calculating staff available times', $level + 2);
									$reCalculateStaffAvailableTimes = $booklyStaffAdapter->updateTimeAvailableOfStaff($staff, $date);
									if ($reCalculateStaffAvailableTimes) {
										printParagraphFollowLevel('Done', $level + 3);
										$wpdb->query('COMMIT');
										printParagraphFollowLevel('Committed', $level);
									} else {
										printParagraphFollowLevel('Failed', $level + 3);
										$wpdb->query('ROLLBACK');
										printParagraphFollowLevel('Rolledback', $level);
									}
								} else {
									printParagraphFollowLevel('Delete all bookings of staff on date failed. Rolledback.', $level + 2);
									$wpdb->query('ROLLBACK');
								}
							} else {
								printParagraphFollowLevel('Empty expired pending booking', $level + 2);
							}
						}
					} else {
						printParagraphFollowLevel('Empty expired pending booking', $level + 1);
					}
				}
			}

			update_option($cronRunningOptionName, 0);

			echo '<p>Done</p>';
		} else {
			echo '<p>Class AppointmentGroup did not exists</p>';
		}
	}
}

if (!function_exists('cutaway_http_cron_update_staff_report_price')) {
	function cutaway_http_cron_update_staff_report_price()
	{
		if (
			class_exists('\\Bookly\\Lib\\Entities\\Appointment') ||
			class_exists('\\BooklyAdapter\\Classes\\StaffMeta')
		) {
			$staffAppointmentQuery = \Bookly\Lib\Entities\Appointment::query('a')
				->select('a.staff_id, MONTH(a.start_date) AS start_date_month, YEAR(a.start_date) AS start_date_year, s.price')
				->innerJoin('CustomerAppointment', 'ca', 'ca.appointment_id = a.id')
				->innerJoin('Service', 's', 'a.service_id = s.id')
				->where('ca.status', 'approved')
				->sortBy('a.start_date')
				->order('asc');

			$staffAppointments = $staffAppointmentQuery->fetchArray();

			echo '<pre>';

			$staffsReport = array();

			foreach ($staffAppointments as $staffAppointment) {
				extract($staffAppointment);

				$staffsReport[$staff_id][$start_date_year][$start_date_month] += $price;
			}

			var_dump($staffsReport);

			foreach($staffsReport as $staff => $report) {
				$staffMeta = new \BooklyAdapter\Classes\StaffMeta($staff);
				$staffMeta->updateStaffMeta('_report_booking_price', $report);
			}

			echo 'Done';

			echo '</pre>';
		}
	}
}

if (!function_exists('cutaway_check_request_page_step_valid')) {
	function cutaway_check_request_page_step_valid()
	{
		global $post;

		if (is_page()) {
			$loginSteps = LoginSteps::getInstance();
			$loginSteps->setCurrentPage($post->ID);

			$editAccSteps = ChangeAccountInformationSteps::getInstance();
			$editAccSteps->setCurrentPage($post->ID);

			$viewOrderSteps = ViewOrderSteps::getInstance();
			$viewOrderSteps->setCurrentPage($post->ID);

			$bookingSteps = BookingSteps::getInstance();
			$bookingSteps->setCurrentPage($post->ID);
			if (!$bookingSteps->checkRequestToPageIsValid($post->ID)) {
				$redirect = $bookingSteps->getFirstPageLink();
				wp_redirect($redirect);
        		die;
			}
		}
	}
}
add_action( 'template_redirect', 'cutaway_check_request_page_step_valid' );

add_action('admin_menu', 'cutawayHideAdminMenu');
if (!function_exists('cutawayHideAdminMenu')) {
	function cutawayHideAdminMenu()
	{
		$currentUser = wp_get_current_user();

		if ($currentUser->user_login != 'codeandmore') {
			remove_menu_page('themes.php');
			remove_menu_page('customize.php');
			remove_menu_page('nav-menus.php');
			remove_menu_page('theme-editor.php');
		}
	}
}

function printParagraphFollowLevel($content, $level = 1)
{
	$marginFollowLevel = ($level - 1) * 20;
	$css = "margin-left: {$marginFollowLevel}px";

	echo "<p style='{$css}'>{$content}</p>";
}