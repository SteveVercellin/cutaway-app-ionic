<?php

add_shortcode('sc_barber_manage_reviews', 'cutaway_sc_barber_manage_reviews');
function cutaway_sc_barber_manage_reviews( $atts ) {
	global $user_info;

	if (!is_user_logged_in() || empty($user_info['type']) || $user_info['type'] != 'staff') {
		return;
	}

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
	            <a href="#" class="top-left-icon show-menu-icon"><img src="<?php echo get_template_directory_uri();?>/images/registrati-icon.png"></a>
				<h1 class="page-title"><?php echo __('Recensioni del barbiere', 'cutaway') ?></h1>
			</div>
		</div>

	<?php
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}