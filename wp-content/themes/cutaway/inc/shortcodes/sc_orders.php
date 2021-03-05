<?php

add_shortcode('sc_orders', 'cutaway_sc_orders');
function cutaway_sc_orders( $atts ) {
	
	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="#" class="top-left-icon back-to-previou-page"><img src="<?php echo get_template_directory_uri();?>/images/back-icon.png"></a>
				<h1 class="page-title">Riepilogo</h1>
			</div>
		</div>
		
		<?php
		if ( isset($_POST['form_barber_detail_submit']) ) {
			$staff = $_POST['staff'];
			$date = $_POST['date'];
			$services = $_POST['services'];
			$time = $_POST['time'];

			$_SESSION['order_time'] = $time;

			// services, location, staff, date, time

			$date_fomarted = cutaway_formatDateTime($date);
			
			$request = new WP_REST_Request( 'GET', 'cutaway/v1/shop/getStaffBookSummary' );
			$request->set_query_params(array(
				'staff' => $staff,
				'service' => $services,
				'work_time' => $date,
				'time' => $time
			));

			if ( class_exists('REST_Shop_Controller') ) {
				$controller = new REST_Shop_Controller();
				$response = $controller->getStaffBookSummary($request);
				// var_dump($response);
				
				if ( $response->data['success'] && count($response->data['staffBookSummary']) > 0 ) {
					$barber = $response->data['staffBookSummary']['barber'];
					$services_arr = $response->data['staffBookSummary']['services'];
					$price_total = 0; ?>

					<form name="form_summary" id="form_summary" class="cutaway-form summary_form" action="/payment" method="post">
						<label>Servizio Selezionato</label> <?php
						foreach ($services_arr as $value) {
						$price_total += $value['price']; ?>
							<div class="sub-block-content object-global-style">
								<div class="left"><?php echo $value['title']; ?> <span class="time"><?php echo $value['duration']; ?> minuti</span></div>
								<div class="right">&euro;<?php echo $value['price']; ?></div>
							</div> <?php
						} ?>
						<label>Barbiere Selezionato</label>
						<div class="sub-block-content object-global-style">
							<div class="left"><img src="<?php echo $barber['avatar']; ?>"> <span class="name"><?php echo $barber['full_name']; ?></span></div>
						</div>
						<label>Data e orario</label>
						<div class="date-and-time object-global-style">
							<div><?php echo $date_fomarted[1] . ' ' . $date_fomarted[0]; ?> <span class="day"><?php echo $date_fomarted[2]; ?></span> <span class="time"><?php echo $time; ?></span></div>
						</div>
						<div class="cutaway-line blue"></div>
						<p><input type="text" name="full_name" class="input" placeholder="Inserisci il cognome / name riportato sul tuo citofono"></p>			
						<p><input type="text" name="adress" class="input" placeholder="Inserisci il piano / interno / scala a cui abiti"></p>
						<input type="hidden" name="price" value="<?php echo $price_total; ?>">
						<div class="form-submit">
							<div class="price">&euro; <?php echo $price_total; ?></div>
							<input type="submit" name="form_summary_submit" value="Completa l'acquisto">
							<div class="cutaway-line"></div>
						</div>
					</form> <?php
				}
				else {
					echo "No result";
				}
			}
			else {
				echo "No result";
			}
		}
		else {
			echo "No result";
		}

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}