<?php

add_shortcode('sc_recovery_password', 'cutaway_sc_recovery_password');
function cutaway_sc_recovery_password( $atts ) {
	global $post;

	$loginSteps = LoginSteps::getInstance();
	$loginSteps->setPageDataSession($post->ID, array(
		'link' => get_permalink($post->ID)
	));
	$prevPageData = $loginSteps->getPreviousPageFromSession();
	$prevLink = !empty($prevPageData['link']) ? $prevPageData['link'] : '#';

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="<?php echo $prevLink; ?>" class="top-left-icon back-to-previou-page"><img src="<?php echo get_template_directory_uri();?>/images/back-icon.png"></a>
				<h1 class="page-title"><?php echo __('Recupero password', 'cutaway'); ?></h1>
				<a href="#" class="top-right-icon"><?php echo __('Aiuto', 'cutaway'); ?></a>
			</div>
		</div>

		<form name="lostpasswordform" id="lostpasswordform" class="cutaway-form recupero_password_form" action="<?php echo esc_url( network_site_url( 'wp-login.php?action=lostpassword', 'login_post' ) ); ?>" method="post">
			<p>
				<input type="text" name="user_login" id="user_login" class="input" value="" placeholder="<?php echo __('Inserisci la tua e-mail', 'cutaway'); ?>" />
			</p>
			<?php
			do_action( 'lostpassword_form' ); ?>
			<input type="hidden" name="redirect_to" value="<?php echo home_url(); ?>" />
			<p><input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php echo __('Recupero password', 'cutaway'); ?>" /></p>
		</form>

	<?php
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}