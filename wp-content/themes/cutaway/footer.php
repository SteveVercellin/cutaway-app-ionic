<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package understrap
 */

$the_theme = wp_get_theme();
$container = get_theme_mod('understrap_container_type');

if ( is_user_logged_in() ) :
	global $user_info;
	$avatar_url = get_avatar_url( $user->ID ) ? esc_url( get_avatar_url( $user->ID ) ) : get_template_directory_uri() . '/images/avatar-default.png';
	?>
	<div class="main-menu" show-menu="hide">
		<div class="user-info">
			<div class="bg-avatar">
				<img src="<?php echo get_template_directory_uri();?>/images/bg-avatar.jpg">
			</div>
			<div class="overlay"></div>
			<div class="user-info-inner">
				<img src="<?php echo $avatar_url;?>">
				<div class="name"><?php echo $user_info['first_name'] . " " . $user_info['last_name']; ?></div>
				<div class="email"><?php echo $user_info['email']; ?></div>
			</div>
		</div>
		<div class="main-navigation">
			<ul>
				<?php
				if (!empty($user_info['type'])) {
					if ($user_info['type'] == 'customer') :
				?>
				<li><a href="<?php echo home_url(); ?>"><span><img src="<?php echo get_template_directory_uri();?>/images/home.png"></span> <?php esc_html_e('Home', 'cutaway'); ?></a></li>
				<li><a href="<?php echo home_url('/account'); ?>"><span><img src="<?php echo get_template_directory_uri();?>/images/risorsa-1.png"></span> <?php esc_html_e('Account', 'cutaway'); ?></a></li>
				<li><a href="<?php echo home_url('/orders'); ?>"><span><img src="<?php echo get_template_directory_uri();?>/images/risorsa-2.png"></span> <?php esc_html_e('Ordini', 'cutaway'); ?></a></li>
				<li><a href="<?php echo home_url('/chronology'); ?>"><span><img src="<?php echo get_template_directory_uri();?>/images/risorsa-3.png"></span> <?php esc_html_e('Cronologia', 'cutaway'); ?></a></li>
				<li><a href="<?php echo home_url('/favorites'); ?>"><span><img src="<?php echo get_template_directory_uri();?>/images/risorsa-4.png"></span> <?php esc_html_e('Preferiti', 'cutaway'); ?></a></li>
				<li><a href="<?php echo home_url('/list-customer-reviews'); ?>"><span><img src="<?php echo get_template_directory_uri();?>/images/risorsa-5.png"></span> <?php esc_html_e('Lascia una Recensione', 'cutaway'); ?></a></li>
				<li><a href="<?php echo home_url('/support'); ?>"><span><img src="<?php echo get_template_directory_uri();?>/images/risorsa-6.png"></span> <?php esc_html_e('Supporto', 'cutaway'); ?></a></li>
				<?php
					elseif ($user_info['type'] == 'staff') :
						wp_nav_menu(array(
							'menu' => 'Barber left menu',
							'container' => '',
							'items_wrap' => '%3$s',
							'walker' => new CutawaySwiperLeftMenu()
						));
					endif;
				}
    		    ?>
				<li><a href="<?php echo wp_logout_url(home_url('/')); ?>"><span><img src="<?php echo get_template_directory_uri();?>/images/risorsa-7.png"></span> <?php esc_html_e('Log Out', 'cutaway'); ?></a></li>
			</ul>
		</div>
	</div> <?php
endif;

?>

</div><!-- #page we need this extra closing tag here -->

<?php wp_footer(); ?>

</body>

</html>