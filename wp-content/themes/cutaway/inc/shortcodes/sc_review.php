<?php

add_shortcode('sc_review', 'cutaway_sc_review');
function cutaway_sc_review( $atts ) {
	if (!is_user_logged_in()) {
		return;
	}

	$currencySymbol = cutawayGetBooklyCurrencySymbol();

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="#" class="top-left-icon back-to-previou-page"><img src="<?php echo get_template_directory_uri();?>/images/back-icon.png"></a>
				<h1 class="page-title"><?php esc_html_e('Recensioni', 'cutaway'); ?></h1>
				<a href="#" class="top-right-icon"><?php esc_html_e('Aiuto', 'cutaway'); ?></a>
			</div>
		</div>

		<div class="wrap-review-content">
			<div class="review-name">Recensione 1</div>
			<div class="review-content object-global-style">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</div>
			<a href="#" class="delete-review object-global-style"><?php esc_html_e('Elimina recensione', 'cutaway'); ?></a>
		</div>

	<?php
	
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}