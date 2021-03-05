<?php

add_shortcode('sc_services', 'cutaway_sc_services');
function cutaway_sc_services( $atts ) {
	global $post;

	if (cutawayGetCurrentUserBooklyType() != 'customer') {
		return;
	}

	$bookingSteps = BookingSteps::getInstance();

	$address = '';
	$date = '';

	if (!empty($_POST)) {
		$address = !empty($_POST['address']) ? $_POST['address'] : '';
		$date = !empty($_POST['date']) ? $_POST['date'] : '';
	} else {
		$sessionPageData = $bookingSteps->getCurrentPageFromSession();
		if (!empty($sessionPageData['post_data'])) {
			$postData = $sessionPageData['post_data'];

			$address = !empty($postData['address']) ? $postData['address'] : '';
			$date = !empty($postData['date']) ? $postData['date'] : '';
		}
	}

	if (empty($address) || empty($date)) {
		return;
	}

	if ( !class_exists('REST_Services_Controller') ) {
		return;
	}

	$bookingSteps->setPageDataSession($post->ID, array(
		'link' => get_permalink($post->ID),
		'post_data' => compact('address', 'date')
	));

	$prevPageData = $bookingSteps->getPreviousPageFromSession();
	$prevLink = !empty($prevPageData['link']) ? $prevPageData['link'] : '#';

	$nextStepLink = $bookingSteps->getNextPageLink();
	$nextStepLink = !empty($nextStepLink) ? $nextStepLink : '#';

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title wrap-page-services-title">
				<a href="<?php echo $prevLink; ?>" class="top-left-icon back-to-previou-page"><img src="<?php echo get_template_directory_uri();?>/images/back-icon.png"></a>
				<h1 class="page-title"><?php esc_html_e('Scegli il servizio', 'cutaway'); ?></h1>
			</div>
		</div>

		<?php
		$controller = new REST_Services_Controller();
		$services = $controller->getListServices();
		// var_dump($response);

		if (!empty($services)) {
			?>
			<div class="services"> <?php
				$i = 0;
				foreach ($services as $value) { ?>
					<div class="service-item <?php echo $i%2 == 0 ? 'even' : 'odd'; ?> service-item-<?php echo $value['id']; ?>">
						<div class="service-item-thumb" style="background: url(<?php echo $value['image']; ?>) no-repeat"></div>
						<h3 class="service-title"><?php echo $value['title']; ?></h3>
						<div class="service-content"><?php echo $value['info']; ?></div>
						<a href="#" class="service-book service-book-submit"><?php echo __('Prenota', 'cutaway'); ?></a>
						<form style='display: none' action='<?php echo $nextStepLink; ?>' method='post'>
							<input type='hidden' name='address' value='<?php echo $address; ?>' />
							<input type='hidden' name='date' value='<?php echo $date; ?>' />
							<input type='hidden' name='services' value='<?php echo $value['id']; ?>' />
						</form>
					</div> <?php
					$i++;
				}
			?>
			</div> <?php
		}

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}