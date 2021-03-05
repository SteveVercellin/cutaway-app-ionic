<?php

add_shortcode('sc_leave_a_review', 'cutaway_sc_leave_a_review');
function cutaway_sc_leave_a_review( $atts ) {
	if (!is_user_logged_in()) {
		return;
	}

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="#" class="top-left-icon back-to-previou-page"><img src="<?php echo get_template_directory_uri();?>/images/back-icon.png"></a>
				<h1 class="page-title"><?php esc_html_e('Lascia una recensione', 'cutaway'); ?></h1>
			</div>
		</div>

		<form class="leave-a-review-from">
			<p class="small-label"><?php esc_html_e('Riepilogo servizio selezionato', 'cutaway'); ?></p>
			
			<?php
				$left_content1 = '<img src="' . get_stylesheet_directory_uri() . '/images/barber-1.png"> <span class="name">Nome barbiere</span>';
				$params1 = [
					'url' => '',
					'class' => '',
					'left_content' => $left_content1,
					'left' => true,
					'right' => false,
					'right_content' => ''
				];

				$left_content2 = 'Taglio capelli corti <span class="time">45 minuti</span>';
				$right_content2 = '$ 10';
				$params2 = [
					'url' => '',
					'class' => '',
					'left' => true,
					'right' => true,
					'left_content' => $left_content2,
					'right_content' => $right_content2
				];
				echo cutaway_subBlockContent($params1);
				echo cutaway_subBlockContent($params2);
			?>

			<p class="small-label"><?php esc_html_e('Data selezionata', 'cutaway'); ?></p>
			<label class="input-text your-address calendar">
				<input type="text" name="date" id="home-datepicker" placeholder="2018-11-20">
			</label>

			<p class="small-label"><?php esc_html_e('Orario selezionato', 'cutaway'); ?></p>
			<div class="slots-time">
				<div class="time">9:00 am</div>
				<div class="time selected">11:00 am</div>
				<div class="time">12:00 pm</div>
				<div class="time">15:00 pm</div>
			</div>

			<p class="small-label"><?php esc_html_e('Scrivi la tua recensione', 'cutaway'); ?></p>
			<textarea class="object-global-style"></textarea>

			<p class="small-label"><?php esc_html_e('Esprimi il tuo giudizio', 'cutaway'); ?></p>
			<div class="rating star-4">
				<span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span>
			</div>
			<div class="wrap-submit-button center-content">
				<a href="#" class="submit-button object-global-style">Invia</a>
			</div>
		</form>

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

	<?php
	
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}