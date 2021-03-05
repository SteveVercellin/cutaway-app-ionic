<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package understrap
 */

$container = get_theme_mod( 'understrap_container_type' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="<?php bloginfo( 'name' ); ?> - <?php bloginfo( 'description' ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<meta name="format-detection" content="telephone=no">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div class="hfeed site" id="page">
	<!-- ******************* The Navbar Area ******************* -->
	<div id="wrapper-header" class="l-header">
		<?php 
		if ( !is_front_page() && !is_home() ) : ?>
			<div class="background-top">
				<img src="<?php echo get_template_directory_uri(); ?>/images/background-top.png">
			</div> <?php
		endif;
		if ( is_front_page() && is_user_logged_in() ) : ?>
			<div class="background-top">
				<img src="<?php echo get_template_directory_uri(); ?>/images/background-top.png">
			</div> <?php
		endif; ?>
	</div><!-- #wrapper-header end -->
