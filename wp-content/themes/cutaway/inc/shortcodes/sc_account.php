<?php

add_shortcode('sc_account', 'cutaway_sc_account');
function cutaway_sc_account( $atts ) {
	global $user_info, $post;

	$editAccSteps = ChangeAccountInformationSteps::getInstance();
	$editAccSteps->removePageDataSession();
	$editAccSteps->setPageDataSession($post->ID, array(
		'link' => get_permalink($post->ID)
	));

	$changePassLink = $editAccSteps->getNextPageLink();
	$changePassLink = !empty($changePassLink) ? $changePassLink : '#';

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="#" class="top-left-icon show-menu-icon"><img src="<?php echo get_template_directory_uri();?>/images/registrati-icon.png"></a>
				<h1 class="page-title"><?php echo __('Modifica dati account', 'cutaway') ?></h1>
			</div>
		</div>

		<form name="form_update_account" id="form_update_account" class="cutaway-form update_account_form sign_in_form" method="post">
			<?php
			global $result_content;
			if ( $result_content ) : ?>
				<p class="alert alert-<?php echo $result_content['type']; ?>"><?php echo $result_content['content']; ?></p> <?php
			endif; ?>
			<p>
				<input type="text" name="user_firstname" id="user_firstname" class="input" value="<?php echo $user_info['first_name']; ?>" placeholder="<?php echo __('Inserisci il tuo nome', 'cutaway') ?>" required="" />
			</p>
			<p>
				<input type="text" name="user_lastname" id="user_lastname" class="input" value="<?php echo $user_info['last_name']; ?>" placeholder="<?php echo __('Inserisci il tuo cognome', 'cutaway'); ?>" required="" />
			</p>
			<p>
				<input type="email" name="user_email" id="user_email" class="input" value="<?php echo $user_info['email']; ?>" placeholder="<?php echo __('Inserisci la tua e-mail', 'cutaway'); ?>" required="" />
			</p>
			<p>
				<input type="text" name="user_phone" id="user_phone" class="input" value="<?php echo $user_info['phone']; ?>" placeholder="<?php echo __('Inserisci il tuo numero', 'cutaway'); ?>" required="" />
			</p>
			<p>
				<input type="text" name="user_city" id="user_city" class="input" value="<?php echo $user_info['city']; ?>" placeholder="<?php echo __('Inserisci la tua città', 'cutaway'); ?>" required="" />
			</p>
			<a href="<?php echo $changePassLink; ?>" class="link-to-page">Modifica password</a>
			<p class="wrap-submit"><input type="submit" name="form_update_account_submit" id="wp-submit" class="button button-primary button-large" value="<?php echo __('Salva', 'cutaway') ?>" /></p>
		</form>

	<?php
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}