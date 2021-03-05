<?php

add_shortcode('sc_favorites', 'cutaway_sc_favorites');
function cutaway_sc_favorites( $atts ) {
	if (!is_user_logged_in()) {
		return;
	}

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="#" class="top-left-icon show-menu-icon"><img src="<?php echo get_template_directory_uri();?>/images/registrati-icon.png"></a>
				<h1 class="page-title"><?php esc_html_e('Preferiti', 'cutaway'); ?></h1>
			</div>
		</div>

		<div class="cronologia preferiti">
			<?php
				if ( class_exists('REST_Shop_Controller') ) {
					$controller = new REST_Shop_Controller();
					$staffs = $controller->processGetListStaffsCustomerBooked();

        			if (!is_wp_error($staffs) && !empty($staffs)) {
						foreach ($staffs as $staff) {
							$pointerCss = 'cursor: pointer;';
							$cssFavIcon = $staff['is_favorite'] ? '' : 'display: none;';
							$cssUnfavIcon = $staff['is_favorite'] ? 'display: none;' : '';
							$left_content = '<img src="' . $staff['avatar'] . '"> <span class="name">' . $staff['full_name'] . '</span>';
							$right_content = '<img class="favourite-staff-icon" data-id="' . $staff['id'] . '" src="' . get_stylesheet_directory_uri() . '/images/blue-heart-icon.png" style="' . $pointerCss . $cssFavIcon . '" />';
							$right_content .= '<img class="unfavourite-staff-icon" data-id="' . $staff['id'] . '" src="' . get_stylesheet_directory_uri() . '/images/no-bg-heart-icon.png" style="' . $pointerCss . $cssUnfavIcon . '" />';

							$params = [
								'url' => '',
								'class' => '',
								'left' => true,
								'right' => true,
								'left_content' => $left_content,
								'right_content' => $right_content,
							];

							echo cutaway_subBlockContent($params);
						}
					} else {
						echo __('Nessun dato', 'cutaway');
					}
				}
			?>
		</div>

	<?php

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}