<?php

add_shortcode('sc_order_detail', 'cutaway_sc_order_detail');
function cutaway_sc_order_detail( $atts ) {
	if ( !isset($_GET['group']) && !is_user_logged_in() ) {
		return;
	}

	global $post;

	$group = $_GET['group'];

	$viewOrderSteps = ViewOrderSteps::getInstance();

	$prevPageData = $viewOrderSteps->getPreviousPageFromSession();
	$prevLink = !empty($prevPageData['link']) ? $prevPageData['link'] : '#';

	$currencySymbol = cutawayGetBooklyCurrencySymbol();

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="<?php echo $prevLink; ?>" class="top-left-icon back-to-previou-page"><img src="<?php echo get_template_directory_uri();?>/images/back-icon.png"></a>
				<h1 class="page-title"><?php esc_html_e('Ordini', 'cutaway'); ?></h1>
			</div>
		</div>

		<?php
			if ( class_exists('REST_Shop_Controller') ) {
				$controller = new REST_Shop_Controller();
				$order = $controller->processGetCustomerOrder(array(
					'group' => $group
				));

				if (!is_wp_error($order) && !empty($order)) {
					$services = $order['services'];
					$barber = $order['barber'];
					$date = $order['date'];
					$time = $order['time'];
					$status = $order['status'];
					$paymentStatus = $order['paypal_status'];

					$date_str = $date['year'] . "/" . $date['month'] . "/" . $date['day'];

					$date_fomarted = cutaway_formatDateTime($date_str); ?>

					<div class="cutaway-form summary_form">
						<label><?php esc_html_e('Servizio Selezionato', 'cutaway'); ?></label> <?php
						foreach ($services as $value) { ?>
							<div class="sub-block-content object-global-style">
								<div class="left"><?php echo $value['title']; ?> <span class="time"><?php echo $value['time_process']; ?> minuti</span></div>
								<div class="right"><?php echo $currencySymbol . $value['price']; ?></div>
							</div> <?php
						} ?>
						<label><?php esc_html_e('Barbiere Selezionato', 'cutaway'); ?></label>
						<div class="sub-block-content object-global-style">
							<div class="left"><img src="<?php echo $barber['logo']; ?>"> <span class="name"><?php echo $barber['full_name']; ?></span></div>
						</div>
						<label><?php esc_html_e('Data e orario', 'cutaway'); ?></label>
						<div class="date-and-time object-global-style">
							<div><?php echo $date_fomarted[1] . ' ' . $date_fomarted[0]; ?> <span class="day"><?php echo $date_fomarted[2]; ?></span> <span class="time"><?php echo $time; ?></span></div>
						</div>
						<?php if ($status == 'approved') : ?>
						<h3 class="pending-confirmation"><?php esc_html_e('In attesa di conferma', 'cutaway'); ?></h3>
						<div class="cancel-order">
							<a href="#" class="cancel-button"><?php esc_html_e('Annulla ordine', 'cutaway'); ?></a>
							<p><?php esc_html_e("Puoi annullare il tuo ordine se non mancano meno di 6 ore dall'appuntamento", 'cutaway'); ?></p>
						</div>
						<?php elseif($status == 'pending') : ?>
							<?php if (empty($paymentStatus)) :

								$override = array();
								if (
									class_exists('\\BooklyAdapter\\Classes\\AppointmentGroup') &&
									class_exists('\\BooklyAdapter\\Entities\\Customer')
								) {
									$booklyCustomerAdapter = \BooklyAdapter\Entities\Customer::getInstance();
									$appointmentGroupAdapter = \BooklyAdapter\Classes\AppointmentGroup::getInstance();

									$customerId = $booklyCustomerAdapter->getCustomerIdFromUserLogged();
									$appGroup = $appointmentGroupAdapter->getCustomerAppointment($customerId, $group);
									$dataBook = cutawayGenerateDataBookFromAppointmentGroup($appGroup);
									if (!empty($dataBook)) {
										$override['data_book'] = $dataBook;
									}
								}

								$orderDetailPage = cutawayGetThemeOption('identify_order_detail_page');
								if (!empty($orderDetailPage)) {
									$link = add_query_arg(array(
										'group' => $group
									), get_permalink($orderDetailPage));

									$override['return'] = $link;
									$override['cancel_return'] = $link;
								}

								if (class_exists('CutawayEncryptDecrypt')) {
									$encryptObj = new CutawayEncryptDecrypt();
									$tokenSendPaypal = $customerId . ';' . $group;
									$tokenSendPaypal = $encryptObj->encrypt($tokenSendPaypal);
									$override['callback'] = cutaway_generate_http_cron_link('handle_paypal_ipn', array(
										'_encrypt_token' => urlencode($tokenSendPaypal)
									));
								}
							?>
						<div class="wrap-payment order-detail-page">
							<a href="#" class="payment-method credit-card"><img src="<?php echo get_template_directory_uri();?>/images/credit-card.png""><?php esc_html_e('Paga con carta di credito', 'cutaway'); ?></a>
							<a href="#" class="payment-method paypal"><img src="<?php echo get_template_directory_uri();?>/images/paypal.png""><?php esc_html_e('Paga con Paypal', 'cutaway'); ?></a>
							<div style='display: none' class='paypal-form'>
								<?php echo cutawayGeneratePaypalForm($override); ?>
							</div>
						</div>
							<?php elseif ($paymentStatus == 'Pending') : ?>
						<p><?php echo __('In attesa di PayPal per confermare', 'cutaway'); ?></p>
							<?php endif; ?>
						<?php endif; ?>
					</div> <?php
				}
				else {
					echo __('Nessun dato', 'cutaway');
				}
			}

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}