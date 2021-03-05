<?php

add_shortcode('sc_support', 'cutaway_sc_support');
function cutaway_sc_support( $atts ) {
	if (!is_user_logged_in()) {
		return;
	}

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="#" class="top-left-icon show-menu-icon"><img src="<?php echo get_template_directory_uri();?>/images/registrati-icon.png"></a>
				<h1 class="page-title"><?php esc_html_e('Supporto', 'cutaway'); ?></h1>
			</div>
		</div>

		<div class="support-page">
			<p class="small-label"><?php esc_html_e('Domande e rispote frequenti', 'cutaway'); ?></p>

			<?php
				$class = '';
				$accordions = [];

				$test_content = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';

				for ($i=1; $i < 6; $i++) { 
					$accordions[] = [ 'header' => 'Domanda' . $i, 'content' => $test_content ];
				}

				$params = [
                    'class' => $class,
                    'accordions' => $accordions,
                ];
                echo cutaway_accordion($params);
			?>
			
			<div class="wrap-submit-button">
				<a href="#" class="submit-button object-global-style"><?php esc_html_e('Apri un ticket', 'cutaway'); ?></a>
			</div>
			<div class="wrap-submit-button">
				<a href="#" class="submit-button object-global-style"><?php esc_html_e('Chiama un operatore', 'cutaway'); ?></a>
			</div>
			<p class="small-label limit-width"><?php esc_html_e('I nostri operatori sono attivi dal lun al ven dalle 10.00 alle 16.00', 'cutaway'); ?></p>
		</div>

	<?php
	
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}