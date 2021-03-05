<?php

add_shortcode('sc_sign_in', 'cutaway_sc_sign_in');
function cutaway_sc_sign_in( $atts ) {

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="#" class="top-left-icon back-to-previou-page"><img src="<?php echo get_template_directory_uri();?>/images/back-icon.png"></a>
				<h1 class="page-title"><?php echo __('Registrati', 'cutaway'); ?></h1>
			</div>
		</div>

		<form name="form_sign_in" id="form_sign_in" class="cutaway-form sign_in_form" method="post">
			<?php
			global $result_content;
			if ( $result_content ) : ?>
				<p class="alert alert-<?php echo $result_content['type']; ?>"><?php echo $result_content['content']; ?></p> <?php
			endif; ?>
			<p>
				<input type="text" name="user_firstname" id="user_firstname" class="input" value="" placeholder="<?php echo __('Inserisci il tuo nome', 'cutaway') ?>" required="" />
			</p>
			<p>
				<input type="text" name="user_lastname" id="user_lastname" class="input" value="" placeholder="<?php echo __('Inserisci il tuo cognome', 'cutaway'); ?>" required="" />
			</p>
			<p>
				<input type="email" name="user_email" id="user_email" class="input" value="" placeholder="<?php echo __('Inserisci la tua e-mail', 'cutaway'); ?>" required="" />
			</p>
			<p>
				<input type="text" name="user_phone" id="user_phone" class="input" value="" placeholder="<?php echo __('Inserisci il tuo numero', 'cutaway'); ?>" required="" />
			</p>
			<p>
				<input type="text" name="user_city" id="user_city" class="input" value="" placeholder="<?php echo __('Inserisci la tua città', 'cutaway'); ?>" required="" />
			</p>
			<p>
				<input type="password" name="user_password" id="user_password" class="input" value="" placeholder="<?php echo __('Inserisci una password', 'cutaway'); ?>" required="" />
			</p>
			<p>
				<input type="password" name="user_password_confirm" id="user_password_confirm" class="input" value="" placeholder="<?php echo __('Conferma la password', 'cutaway'); ?>" required="" />
			</p>
			<input type="hidden" name="redirect_to" value="<?php echo home_url(); ?>" />
			<p class="wrap-submit"><input type="submit" name="form_sign_in_submit" id="wp-submit" class="button button-primary button-large" value="<?php echo __('Registrati', 'cutaway'); ?>" /></p>
		</form>

	<?php
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}