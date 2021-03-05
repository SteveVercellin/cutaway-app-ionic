<?php

add_shortcode('sc_payment', 'cutaway_sc_payment');
function cutaway_sc_payment( $atts ) {
	global $post;

	$dataBook = cutawayGetCustomerDataActiveBook();
	if (cutawayGetCurrentUserBooklyType() != 'customer' || empty($dataBook) || empty($_POST)) {
		return;
	}

	$bookingSteps = BookingSteps::getInstance();

	$fullName = '';
	$address = '';
	if (!empty($_POST)) {
		$fullName = !empty($_POST['full_name']) ? $_POST['full_name'] : '';
		$address = !empty($_POST['address']) ? $_POST['address'] : '';
	}

	$prevPageData = $bookingSteps->getPreviousPageFromSession();
	$prevLink = !empty($prevPageData['link']) ? $prevPageData['link'] : '#';

	$price = $dataBook['price'];
	$currencySymbol = cutawayGetBooklyCurrencySymbol();

	$dataBook['full_name'] = $full_name;
	$dataBook['address'] = $address;

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="<?php echo $prevLink; ?>" class="top-left-icon back-to-previou-page"><img src="<?php echo get_template_directory_uri();?>/images/back-icon.png"></a>
				<h1 class="page-title"><?php esc_html_e("Completa l'acquisto", 'cutaway'); ?></h1>
				<a href="#" class="top-right-icon"><?php esc_html_e('Aiuto', 'cutaway'); ?></a>
			</div>
		</div>

		<div class="wrap-payment">
			<div class="price_total"><?php esc_html_e('Totale:', 'cutaway'); ?> <?php echo $currencySymbol; ?> <?php echo isset($_POST['price']) ? $_POST['price'] : '0'; ?></div>
			<p><?php esc_html_e('Scegli un metodo di pagamento', 'cutaway'); ?></p>
			<a href="#" class="payment-method credit-card"><img src="<?php echo get_template_directory_uri();?>/images/credit-card.png""><?php esc_html_e('Paga con carta di credito', 'cutaway'); ?></a>
			<a href="#" class="payment-method paypal"><img src="<?php echo get_template_directory_uri();?>/images/paypal.png""><?php esc_html_e('Paga con Paypal', 'cutaway'); ?></a>
			<div style='display: none' class='paypal-form'>
				<?php echo cutawayGeneratePaypalForm(); ?>
			</div>
		</div>

	<?php
	cutawaySetCustomerDataActiveBook($dataBook);

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}