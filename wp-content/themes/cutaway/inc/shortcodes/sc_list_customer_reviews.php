<?php

add_shortcode('sc_list_customer_reviews', 'cutaway_sc_list_customer_reviews');
function cutaway_sc_list_customer_reviews( $atts ) {
	if (!is_user_logged_in()) {
		return;
	}

	$currencySymbol = cutawayGetBooklyCurrencySymbol();

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="#" class="top-left-icon show-menu-icon"><img src="<?php echo get_template_directory_uri();?>/images/registrati-icon.png"></a>
				<h1 class="page-title"><?php esc_html_e('Recensioni', 'cutaway'); ?></h1>
			</div>
		</div>

		<div class="your-reviews">
			<p class="small-label"><?php esc_html_e('Le tue recensioni', 'cutaway'); ?></p>
			<?php
				$left_content = '<img src="' . get_stylesheet_directory_uri() . '/images/barber-1.png"> <span class="name">Recensione 1</span>';
				$params = [
					'url' => home_url('/review'),
					'class' => '',
					'left' => true,
					'right' => false,
					'left_content' => $left_content,
					'right_content' => '',
				];

				for ($i=0; $i < 3 ; $i++) {
					echo cutaway_subBlockContent($params);
				}
			?>
		</div>
		<div class="you-still-have-to-review">
			<p class="small-label"><?php esc_html_e('Devi ancora recensire', 'cutaway'); ?></p>
			<?php
				$left_content = '<img src="' . get_stylesheet_directory_uri() . '/images/barber-1.png"> <span class="name">Recensione 1</span>';
				$params = [
					'url' => home_url('/leave-a-review'),
					'class' => '',
					'left' => true,
					'right' => false,
					'left_content' => $left_content,
					'right_content' => '',
				];

				for ($i=0; $i < 3 ; $i++) {
					echo cutaway_subBlockContent($params);
				}
			?>
		</div>

	<?php

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}