<?php

add_shortcode('sc_summary', 'cutaway_sc_summary');
function cutaway_sc_summary( $atts ) {
	global $post;

	$dataBook = cutawayGetCustomerDataActiveBook();
	if (cutawayGetCurrentUserBooklyType() != 'customer' || empty($dataBook)) {
		return;
	}

	$bookingSteps = BookingSteps::getInstance();

	$time = '';
	if (!empty($_POST['time'])) {
		$time = $_POST['time'];
	} else {
		$sessionPageData = $bookingSteps->getCurrentPageFromSession();
		if (!empty($sessionPageData['post_data'])) {
			$postData = $sessionPageData['post_data'];

			$time = !empty($postData['time']) ? $postData['time'] : '';
		}
	}

	if (empty($time)) {
		return;
	}

	$bookingSteps->setPageDataSession($post->ID, array(
		'link' => get_permalink($post->ID),
		'post_data' => compact('time')
	));

	$prevPageData = $bookingSteps->getPreviousPageFromSession();
	$prevLink = !empty($prevPageData['link']) ? $prevPageData['link'] : '#';

	$nextPageLink = $bookingSteps->getNextPageLink();
	$nextPageLink = !empty($nextPageLink) ? $nextPageLink : '#';

	$currencySymbol = cutawayGetBooklyCurrencySymbol();

	ob_start(); ?>
	<div class="top-content">
		<div class="wrap-page-title">
			<a href="<?php echo $prevLink; ?>" class="top-left-icon back-to-previou-page"><img src="<?php echo get_template_directory_uri();?>/images/back-icon.png"></a>
			<h1 class="page-title"><?php esc_html_e('Riepilogo', 'cutaway'); ?></h1>
		</div>
	</div>

	<?php
	$staff = $dataBook['staff'];
	$date = $dataBook['date'];
	$services = $dataBook['services'];

	$dataBook['time'] = $time;
	cutawaySetCustomerDataActiveBook($dataBook);

	// services, location, staff, date, time

	$date_fomarted = cutaway_formatDateTime($date);

	$staffBookSummary = cutawayGetCurrentBookSummary();

	if (!is_wp_error($staffBookSummary) && !empty($staffBookSummary)) {
		$barber = $staffBookSummary['barber'];
		$services_arr = $staffBookSummary['services']; ?>

		<form name="form_summary" id="form_summary" class="cutaway-form summary_form" action="<?php echo $nextPageLink; ?>" method="post">
			<label><?php esc_html_e('Servizio Selezionato', 'cutaway'); ?></label> <?php

			foreach ($services_arr as $value) {
				$price_total += $value['price'];

				$left_content = $value['title'] . ' <span class="time">' . $value['duration'] . ' ' . __('minuti', 'cutaway') . '</span>';
				$right_content = $currencySymbol . ' ' . $value['price'];
				$params = [
					'url' => '',
					'class' => '',
					'left' => true,
					'right' => true,
					'left_content' => $left_content,
					'right_content' => $right_content,
				];

				echo cutaway_subBlockContent($params);
			} ?>

			<label><?php esc_html_e('Barbiere Selezionato', 'cutaway'); ?></label>
			<?php
				$left_content = '<img src="' . $barber['avatar'] . '"> <span class="name">' . $barber['full_name'] . '</span>';

				$params = [
					'url' => '',
					'class' => '',
					'left' => true,
					'right' => false,
					'left_content' => $left_content,
					'right_content' => '',
				];

				echo cutaway_subBlockContent($params);
			?>

			<label><?php esc_html_e('Data e orario', 'cutaway'); ?></label>
			<div class="date-and-time object-global-style">
				<div><?php echo $date_fomarted[1] . ' ' . $date_fomarted[0]; ?> <span class="day"><?php echo $date_fomarted[2]; ?></span> <span class="time"><?php echo $time; ?></span></div>
			</div>
			<div class="cutaway-line blue"></div>
			<p><input type="text" name="full_name" class="input" placeholder="<?php esc_html_e('Inserisci il cognome / name riportato sul tuo citofono', 'cutaway'); ?>"></p>
			<p><input type="text" name="adress" class="input" placeholder="<?php esc_html_e('Inserisci il piano / interno / scala a cui abiti', 'cutaway'); ?>"></p>
			<input type="hidden" name="price" value="<?php echo $price_total; ?>">
			<div class="form-submit">
				<div class="price"><?php echo $currencySymbol; ?> <?php echo $price_total; ?></div>
				<input type="submit" name="form_summary_submit" value="<?php esc_html_e("Completa l'acquisto", 'cutaway'); ?>">
				<div class="cutaway-line"></div>
			</div>
		</form> <?php
	}
	else {
		if (is_wp_error($staffBookSummary)) {
			echo $staffBookSummary->get_error_message();
		} else {
			echo __('Nessun barbiere', 'cutaway');
		}
	}

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}