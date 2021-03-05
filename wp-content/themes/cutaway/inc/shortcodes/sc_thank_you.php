<?php

add_shortcode('sc_thank_you', 'cutaway_sc_thank_you');
function cutaway_sc_thank_you( $atts ) {

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title grazie">
				<a href="#" class="top-left-icon show-menu-icon"><img src="<?php echo get_template_directory_uri();?>/images/registrati-icon.png"></a>
				<h3 class="page-title">&nbsp;</h3>
				<a href="#" class="top-right-icon">Aiuto</a>
			</div>
		</div>

		<div class="logo-bianco grazie">
			<img src="<?php echo get_template_directory_uri(); ?>/images/tick-inside-circle.png">
		</div>
		<h1 class="page-title grazie"><?php echo __('Grazie per il tuo acquisto', 'cutaway'); ?></h1>
		<div class="grazie-page-content">
			<p><?php echo __('Ti abbiamo appena inviato un\'email di conferma con i dati relativi all\' ordine.', 'cutaway'); ?></p>
			<p><?php echo __('Riceverai una notifica come promemoria, un ora prima del tuo appuntamento.', 'cutaway'); ?></p>
			<a href="#" class="your-order"><?php echo __('Guarda il tuo ordine', 'cutaway'); ?></a>
			<a href="#" class="need-help"><?php echo __('Hai bisogno di aiuto?', 'cutaway'); ?></a>
		</div> <?php

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}