<?php

add_shortcode('sc_barber_detail', 'cutaway_sc_barber_detail');
function cutaway_sc_barber_detail( $atts ) {
	global $post;

	$dataBook = cutawayGetCustomerDataActiveBook();
	$listBarbersFound = $_SESSION['list_barbers_found'];
	if (cutawayGetCurrentUserBooklyType() != 'customer' || empty($dataBook) || empty($listBarbersFound) || empty($_GET['staff'])) {
		return;
	}

	$staff_id = $_GET['staff'];
	if (empty($staff_id) || empty($listBarbersFound[$staff_id])) {
		return;
	}

	if ( !class_exists('REST_Shop_Controller') ) {
		return;
	}

	$bookingSteps = BookingSteps::getInstance();
	$bookingSteps->setPageDataSession($post->ID, array(
		'link' => add_query_arg(array('staff' => $staff_id), get_permalink($post->ID))
	));

	$prevPageData = $bookingSteps->getPreviousPageFromSession();
	$prevLink = !empty($prevPageData['link']) ? $prevPageData['link'] : '#';

	$nextPageLink = $bookingSteps->getNextPageLink();
	$nextPageLink = !empty($nextPageLink) ? $nextPageLink : '#';

	$currencySymbol = cutawayGetBooklyCurrencySymbol();

	$work_time = $dataBook['date'];
	$service = $dataBook['services'];

	$dataBook['staff'] = $staff_id;
	$dataBook['location'] = $listBarbersFound[$staff_id];

	$controller = new REST_Shop_Controller();
	$staffDetail = $controller->processGetStaffDetail(array(
		'staff' => $staff_id,
		'service' => $service,
		'work_time' => $work_time
	));

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="<?php echo $prevLink; ?>" class="top-left-icon back-to-previou-page"><img src="<?php echo get_template_directory_uri();?>/images/back-icon.png"></a>
				<h1 class="page-title">&nbsp;</h1>
				<?php if (!empty($staffDetail)) { ?>
					<?php if ($staffDetail['is_favorite']) : ?>
						<a href="#" class="top-right-icon"><img src="<?php echo get_template_directory_uri();?>/images/blue-heart-icon.png"></a>
					<?php else: ?>
						<a href="#" class="top-right-icon"><img src="<?php echo get_template_directory_uri();?>/images/heart-icon.png"></a>
					<?php endif; ?>
				<?php } ?>
			</div>
		</div>

		<?php
			if (!is_wp_error($staffDetail) && !empty($staffDetail)) {
				$block_times = $staffDetail['block_times'];
				$gallery = $staffDetail['gallery']; ?>

				<div class="wrap-staff-detail">
					<div class="avatar"><img src="<?php echo $staffDetail['avatar'];?>"></div>
					<h3 class="name"><?php echo $staffDetail['full_name'];?></h3>
					<div class="star-service-price">
						<div>
							<div class="icon star" sort-type="rating" order="desc"><img src="<?php echo get_template_directory_uri();?>/images/big-star.png"></div>
							<div class="number"><?php echo $staffDetail['rating_count'];?>/10</div>
						</div>
						<div>
							<div class="icon star" sort-type="rating" order="desc"><img src="<?php echo get_template_directory_uri();?>/images/big-scissors.png"></div>
							<div class="number"><?php echo $staffDetail['service_count'];?> <?php echo __('servizi', 'cutaway'); ?></div>
						</div>
						<div>
							<div class="icon star" sort-type="rating" order="desc"><img src="<?php echo get_template_directory_uri();?>/images/big-euro.png"></div>
							<div class="number">&euro;&euro;</div>
						</div>
					</div>
					<?php if (!empty($staffDetail['info'])) : ?>
					<div class="info"><?php echo $staffDetail['info']; ?></div>
					<?php endif; ?>
					<div class="service-price"><?php esc_html_e('Prezzo servizio:', 'cutaway'); ?> <span><?php echo $currencySymbol; ?> <?php echo $staffDetail['service_price'];?></span></div> <?php
					if ( count($block_times) > 0 ) :
						$count = count($block_times);
						$i = 1; ?>
						<form id="available-slots-form" class="cutaway-form available-slots-form" action="<?php echo $nextPageLink; ?>" method="POST">
							<p><?php esc_html_e('Seleziona uno degli slot disponibilli', 'cutaway'); ?></p>
							<div class="wrap-slots">
								<div class="slots-time"> <?php
									foreach ($block_times as $value) { ?>
										<div class="time"><?php echo $value['start_time']; ?></div> <?php
										if ( $i%4 == 0 && $i != $count ) {
											echo '</div><div class="slots-time">';
										}
										$i++;
									} ?>
								</div>
								<input type="hidden" name="time" value="" class="input_time" />
							</div>
							<p class="wrap-submit">

							</p>
							<div class="wrap-submit-button center-content">
								<button type="submit" name="form_barber_detail_submit" id="wp-submit" class="submit-button object-global-style"><?php esc_html_e('Prenota', 'cutaway'); ?></button>
							</div>
						</form> <?php
					else : ?>
					<p><?php echo __('Non c\'è tempo per selezionare. Si prega di scegliere altro barbiere di altra data.', 'cutaway') ?></p>
					<?php endif; ?>
					<div class="staff-gallery"> <?php
						foreach ($gallery as $value) { ?>
							<div>
								<img src="<?php echo $value; ?>">
								<div class="overlay"></div>
							</div> <?php
						} ?>
					</div>
					<div class="wrap-reviews">
						<div class="reviews-count"><?php esc_html_e('Recensioni', 'cutaway'); ?> (43)</div>
						<div class="review-list">
							<div class="review">
								<div class="left">
									<img src="<?php echo get_template_directory_uri();?>/images/gallery-1.jpg">
									<p>Nome Cognome</p>
								</div>
								<div class="right">
									<div class="rating star-4">
										<span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span>
										<em> (13 ratings)</em>
									</div>
									<div class="quality">Ottimo servizio!!!</div>
									<div class="review-content">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</div>
								</div>
							</div>
							<div class="review">
								<div class="left">
									<img src="<?php echo get_template_directory_uri();?>/images/gallery-1.jpg">
									<p>Nome Cognome</p>
								</div>
								<div class="right">
									<div class="rating star-4">
										<span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span>
										<em> (13 ratings)</em>
									</div>
									<div class="quality">Ottimo servizio!!!</div>
									<div class="review-content">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</div>
								</div>
							</div>
							<div class="review">
								<div class="left">
									<img src="<?php echo get_template_directory_uri();?>/images/gallery-1.jpg">
									<p>Nome Cognome</p>
								</div>
								<div class="right">
									<div class="rating star-4">
										<span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span>
										<em> (13 ratings)</em>
									</div>
									<div class="quality">Ottimo servizio!!!</div>
									<div class="review-content">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</div>
								</div>
							</div>
						</div>
						<a href="#" class="reviews-count show-all-review"><?php esc_html_e('Visualizza tutte', 'cutaway'); ?></a>
					</div>
				</div>
			<?php
			}
			else {
				if (is_wp_error($staffDetail)) {
					echo $staffDetail->get_error_message();
				} else {
					echo __('Nessun barbiere', 'cutaway');
				}
			}

			cutawaySetCustomerDataActiveBook($dataBook);
		?>


	<?php
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}