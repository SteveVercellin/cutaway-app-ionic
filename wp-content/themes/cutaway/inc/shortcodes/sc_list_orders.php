<?php

add_shortcode('sc_list_orders', 'cutaway_sc_list_orders');
function cutaway_sc_list_orders( $atts ) {
	if (!is_user_logged_in()) {
		return;
	}

	global $post;

	$viewOrderSteps = ViewOrderSteps::getInstance();
	$viewOrderSteps->removePageDataSession();
	$viewOrderSteps->setPageDataSession($post->ID, array(
		'link' => get_permalink($post->ID)
	));

	$currencySymbol = cutawayGetBooklyCurrencySymbol();

	ob_start(); ?>
	<div class="top-content">
		<div class="wrap-page-title">
			<a href="#" class="top-left-icon show-menu-icon"><img src="<?php echo get_template_directory_uri();?>/images/registrati-icon.png"></a>
			<h1 class="page-title"><?php echo __('Elenca gli ordini', 'cutaway'); ?></h1>
		</div>
	</div>

	<?php
	if ( class_exists('REST_Shop_Controller') ) {
		$controller = new REST_Shop_Controller();
		$list_orders = $controller->processGetCustomerOrders();

        if (!is_wp_error($list_orders) && !empty($list_orders)) {
			if (count($list_orders['pending']) == 0 && count($list_orders['approved']) == 0) {
				echo __("Non hai nessun ordine.", 'cutaway');
			}
			else {
				foreach ($list_orders as $key => $orders) {
					$heading = '';
					if ($key == 'pending') {
						$heading = __('Ordini in attesa', 'cutaway');
					} elseif ($key == 'approved') {
						$heading = __('Ordini completati', 'cutaway');
					}

					echo $orders ? "<h3>$heading</h3>" : "";

					foreach ($orders as $value) {
						$group = $value['group'];
						$price = $value['services']['price'];
						$title = $value['barber']['full_name'];
						$time = $value['time'];
						$orderDetailLink = $viewOrderSteps->getNextPageLink(array(
							'group' => $group
						));
						$orderDetailLink = !empty($orderDetailLink) ? $orderDetailLink : '#';
						$date = $value['date']['year'] . '-' . $value['date']['month'] . '-' . $value['date']['day'];
						?>
						<a href="<?php echo $orderDetailLink; ?>">
							<div class="sub-block-content object-global-style">
								<div class="left"><?php echo __('Barbiere', 'cutaway'), ': ', $title; ?> <span class="time"><?php echo $date, ' ', $time; ?></span></div>
								<div class="right"><?php echo $currencySymbol; ?> <?php echo $price; ?></div>
							</div>
						</a> <?php
					}
				}
			}
        } else {
			echo __('Nessun dato', 'cutaway');
		}
	}

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}