<?php

add_shortcode('sc_find_barber_result', 'cutaway_sc_find_barber_result');
function cutaway_sc_find_barber_result( $atts ) {
	global $post;

	if (cutawayGetCurrentUserBooklyType() != 'customer') {
		return;
	}

	$bookingSteps = BookingSteps::getInstance();

	$address = '';
	$date = '';
	$services = '';

	if (!empty($_POST)) {
		$address = !empty($_POST['address']) ? $_POST['address'] : '';
		$date = !empty($_POST['date']) ? $_POST['date'] : '';
		$services = !empty($_POST['services']) ? $_POST['services'] : '';
	} else {
		$sessionPageData = $bookingSteps->getCurrentPageFromSession();
		if (!empty($sessionPageData['post_data'])) {
			$postData = $sessionPageData['post_data'];

			$address = !empty($postData['address']) ? $postData['address'] : '';
			$date = !empty($postData['date']) ? $postData['date'] : '';
			$services = !empty($postData['services']) ? $postData['services'] : '';
		}
	}

	if (empty($address) || empty($date) || empty($services)) {
		return;
	}

	if ( !class_exists('REST_Shop_Controller') ) {
		return;
	}

	$bookingSteps->setPageDataSession($post->ID, array(
		'link' => get_permalink($post->ID),
		'post_data' => compact('address', 'date', 'services')
	));

	$prevPageData = $bookingSteps->getPreviousPageFromSession();
	$prevLink = !empty($prevPageData['link']) ? $prevPageData['link'] : '#';

	ob_start(); ?>
	<div class="top-content">
		<div class="wrap-page-title">
			<a href="<?php echo $prevLink; ?>" class="top-left-icon back-to-previou-page"><img src="<?php echo get_template_directory_uri();?>/images/back-icon.png"></a>
			<h1 class="page-title">&nbsp;</h1>
			<a href="#" class="top-right-icon find_barber_sort_by"><img src="<?php echo get_template_directory_uri();?>/images/search-2-icon.png"></a>
		</div>
	</div>
	<div class="wrap-sort-by">
		<div class="title"><?php esc_html_e('Filtra per', 'cutaway'); ?></div>
		<div class="list-sort-by">
			<div class="s_alphabet" sort-type="alphabet" order="desc"><span><img src="<?php echo get_template_directory_uri();?>/images/s_alphabet.png"></span> <?php esc_html_e('Ordine alfabetico', 'cutaway'); ?></div>
			<div class="s_price" sort-type="price" order="desc"><span>&euro;</span> <?php esc_html_e('Prezzo', 'cutaway'); ?></div>
			<div class="s_rating" sort-type="rating" order="desc"><span>&#9734;</span> <?php esc_html_e('Valutazione', 'cutaway'); ?></div>
		</div>
	</div>
	<?php

	$dataBook = array();
	$listBarbersFound = array();

	$address_url = $address;
	$date_new = str_replace('-', '/', $date);
	$services_str = rtrim($services,",");
	$servicesArr = explode(',', $services_str);

	$controller = new REST_Shop_Controller();
	$dataSearch = array(
		'location' => $address_url,
		'service' => $services_str,
		'work_time' => $date_new
	);

	$dataBook['services'] = $services_str;
	$dataBook['date'] = $date_new;
	$dataBook['price'] = cutawayGetBooklyTotalPriceServices($servicesArr);

	?>

	<div class="find_barber_variable hidden" location="<?php echo $address_url;?>" date="<?php echo $date_new;?>" services="<?php echo $services_str;?>"></div>

	<?php
	$barber_list = $controller->processSearchShop($dataSearch);
	//var_dump($response);

	if (!is_wp_error($barber_list) && !empty($barber_list)) {
		?>
		<div class="barber-lists"> <?php
			foreach ($barber_list as $barber) {
				$listBarbersFound[$barber['id']] = $barber['location_id'];
				// include(get_template_directory() . '/loop-templates/find_barber_result.php');

				$params = [
					'url' => esc_url_raw($bookingSteps->getNextPageLink(array('staff' => $barber['id']))),
					'avatar' => $barber['avatar'],
					'full_name' => $barber['full_name'],
					'location' => $barber['location'],
					'price' => __("Capelli | Barba", "cutaway") . " | &euro;&euro;&euro;",
					'starNumber' => 4,
					'ratings' => 13
				];
				echo cutaway_barber_detail_box($params);
			}
		?>
			<div class="block_overlay"></div>
		</div> <?php
	}
	else {
		if (is_wp_error($barber_list)) {
			echo $barber_list->get_error_message();
		} else {
			echo __('Nessun barbiere da trovare.', 'cutaway');
		}
	}

	cutawaySetCustomerDataActiveBook($dataBook);
	$_SESSION['list_barbers_found'] = $listBarbersFound;

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}