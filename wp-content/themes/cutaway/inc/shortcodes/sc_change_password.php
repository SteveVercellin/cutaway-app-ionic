<?php

add_shortcode('sc_change_password', 'cutaway_sc_change_password');
function cutaway_sc_change_password( $atts ) {
	global $post;

	$editAccSteps = ChangeAccountInformationSteps::getInstance();
	$editAccSteps->setPageDataSession($post->ID, array(
		'link' => get_permalink($post->ID)
	));
	$prevPageData = $editAccSteps->getPreviousPageFromSession();
	$prevLink = !empty($prevPageData['link']) ? $prevPageData['link'] : '#';

	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="<?php echo $prevLink; ?>" class="top-left-icon back-to-previou-page"><img src="<?php echo get_template_directory_uri();?>/images/back-icon.png"></a>
				<h1 class="page-title"><?php echo __('Cambia password', 'cutaway'); ?></h1>
			</div>
		</div>

		<form name="form_change_password" id="form_change_password" class="cutaway-form sign_in_form change_password_form" method="post">
			<?php
			global $result_content;
			if ( $result_content ) : ?>
				<p class="alert alert-<?php echo $result_content['type']; ?>"><?php echo $result_content['content'] ?></p> <?php
			endif; ?>
			<p>
				<label><?php echo __('Vecchia password', 'cutaway'); ?></label>
				<input type="password" name="old_password" id="old_password" class="input" value="" placeholder="" required="" />
			</p>
			<p>
				<label><?php echo __('Nuova password', 'cutaway'); ?></label>
				<input type="password" name="new_password" id="new_password" class="input" value="" placeholder="" required="" />
			</p>
			<p class="wrap-submit"><input type="submit" name="form_change_password_submit" id="wp-submit" class="button button-primary button-large" value="<?php echo __('Cambia password', 'cutaway'); ?>" /></p>
		</form>

	<?php
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}