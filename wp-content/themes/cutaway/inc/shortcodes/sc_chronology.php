<?php

add_shortcode('sc_chronology', 'cutaway_sc_chronology');
function cutaway_sc_chronology( $atts ) {
	if (!is_user_logged_in()) {
		return;
	}

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="#" class="top-left-icon show-menu-icon"><img src="<?php echo get_template_directory_uri();?>/images/registrati-icon.png"></a>
				<h1 class="page-title"><?php esc_html_e('Cronologia', 'cutaway'); ?></h1>
			</div>
		</div>

		<div class="cronologia">
			<?php
				for ($i=0; $i < 7 ; $i++) { 
					$left_content = '<img src="' . get_stylesheet_directory_uri() . '/images/barber-1.png"> <span class="name">Nome barbiere</span>';
					$right_content = '1 Settembre 2018';
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
			?>
		</div>

	<?php
	
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}