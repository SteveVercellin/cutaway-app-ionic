<?php
/*
 * Template name: Home
 */
global $post, $user_info;

$loginSteps = LoginSteps::getInstance();
$bookingSteps = BookingSteps::getInstance();

get_header(); ?>

<div  id="wrapper-main" class="l-main">

	<div id="content" class="content">

			<main class="site-main" id="main"> <?php
				if ( !is_user_logged_in() ):
					$loginSteps->removePageDataSession();
					$loginSteps->setPageDataSession($post->ID, array(
						'link' => get_permalink($post->ID)
					));

					$forgotPassPageLink = $loginSteps->getNextPageLink();
					$forgotPassPageLink = !empty($forgotPassPageLink) ? $forgotPassPageLink : '#';
				?>
					<div class="logo-bianco">
						<img src="<?php echo get_template_directory_uri(); ?>/images/logo-bianco.png">
					</div>
					<div class="login-equal-social">
						<a href="<?php echo home_url();?>/wp-login.php?loginSocial=facebook" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="facebook" data-popupwidth="475" data-popupheight="175" class="facebook"><?php esc_html_e('Use facebook', 'cutaway'); ?></a>
						<a href="<?php echo home_url();?>/wp-login.php?loginSocial=google" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="google" data-popupwidth="600" data-popupheight="600" class="google"><?php esc_html_e('Use Google', 'cutaway'); ?></a>
						<a href="/sign-in" class="signup-for-free"><?php esc_html_e('Iscriviti gratuitamente', 'cutaway'); ?></a>
					</div>
					<form class="form-login" method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) );?>">
						<p class="already-user"><?php esc_html_e('Sei già un utente?', 'cutaway'); ?></p>
						<?php
						if ( isset($_GET['login']) && $_GET['login'] == "failed" ) : ?>
							<p class="error_message"><?php esc_html_e('Email o password non corrette.', 'cutaway'); ?></p> <?php
						endif; ?>

						<label class="input-text email"><input type="email" name="log" placeholder="<?php esc_html_e('Email', 'cutaway'); ?>" required="" /></label>
						<label class="input-text password"><input type="password" name="pwd" placeholder="<?php esc_html_e('Password', 'cutaway'); ?>" required="" /></label>

						<div class="wrap-submit-button center-content">
							<button type="submit" class="submit-button object-global-style submit" name="wp-submit"><?php esc_html_e('Accedi', 'cutaway'); ?></button>
						</div>

						<?php
							$redirect = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
						?>
						<input type="hidden" name="redirect_to" value="<?php echo home_url(); ?>" />
						<!-- <a href="<?php echo esc_url( network_site_url( 'wp-login.php?action=lostpassword', 'login_post' ) ); ?>" class="forget-pass">Hai dimenticato la password?</a> -->
						<a href="<?php echo $forgotPassPageLink; ?>" class="forget-pass"><?php echo __('Hai dimenticato la password?', 'cutaway') ?></a>
					</form> <?php
				else :
					if (!empty($user_info['type'])) {
						$pageTitle = '&nbsp;';
						if ($user_info['type'] == 'staff') {
							$pageTitle = __('La Tua Dashboard', 'cutaway');
						}
					?>
					<div class="top-content">
					    <div class="wrap-page-title">
							<a href="#" class="top-left-icon show-menu-icon"><img src="<?php echo get_template_directory_uri();?>/images/registrati-icon.png"></a>
							<h1 class="page-title text-center"><?php echo $pageTitle; ?></h1>
						</div>
					</div>
					<?php
						if ($user_info['type'] == 'customer') {
							$bookingSteps->removePageDataSession();
							$bookingSteps->setPageDataSession($post->ID, array(
								'link' => get_permalink($post->ID)
							));

							$nextStepLink = $bookingSteps->getNextPageLink();
							$nextStepLink = !empty($nextStepLink) ? $nextStepLink : '#';
						?>
							<div class="logo-bianco logo">
								<img src="<?php echo get_template_directory_uri(); ?>/images/logo.png">
							</div>
							<h1 class="page-title"><?php esc_html_e('Benvenuto!', 'cutaway'); ?></h1>
							<div class="wrap-your-address">
								<form class="form-your-address" method="POST" action="<?php echo $nextStepLink; ?>">
									<p class="label"><?php esc_html_e('Inserisci tuo indirizzo', 'cutaway'); ?></p>
									<!-- <label class="input-text your-address address">
										<input type="text" name="address" placeholder="<?php esc_html_e('Moncalieri - Corso Roma 41', 'cutaway'); ?>">
									</label> -->
									<div class="wrap-select-address">
										<select class="select-address" name='address'>
											<option></option>
											<?php
												if (class_exists('\\BooklyAdapter\\Entities\\Location')) {
													$booklyLocationAdapter = \BooklyAdapter\Entities\Location::getInstance();
													$locations = $booklyLocationAdapter->getListLocations(array('order' => 'asc'));
													if (!empty($locations)) {
														foreach ($locations as $value) {
															echo '<option value="' . $value['name'] . '">' . $value['name'] . '</option>';
														}
													}
												}
											?>
										</select>
									</div>
									<p class="label"><?php esc_html_e('Seleziona la data', 'cutaway'); ?></p>
									<label class="input-text your-address calendar"><input type="hidden" name="date" id="home-datepicker" placeholder="2018-11-20"></label>
									<div id="div_datepicker"></div>

									<div class="wrap_services_selected">
										<h3 class="page-title"><?php echo __('Servizio Selezionato', 'cutaway'); ?></h3>
										<ul></ul>
									</div>

									<input type="hidden" name="submit_find_barber" value="1" />

									<div class="wrap-submit-button center-content">
										<button type="submit" class="submit-button object-global-style" name="submit_find_barber_btn"><?php esc_html_e('Find your Barber', 'cutaway'); ?></button>
									</div>
								</form>
							</div>
						<?php
						} elseif ($user_info['type'] == 'staff') {
							$data = array();
							if (class_exists('\\BooklyAdapter\\Entities\\Staff')) {
								$booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
								$data = $booklyStaffAdapter->getStaffDashboardDataFromUserLogged();
							}
						?>
						<div class="barber-dashboard-wrap">
							<h3><?php echo __('Reddito', 'cutaway') ?></h3>
							<?php
							if (!empty($data['report_price'])) {
								$currencySymbol = cutawayGetBooklyCurrencySymbol();
								$dayIncome = cutawayGetThemeOption('barber_dashboard_income_day', 1);
								$dayIncome = is_numeric($dayIncome) && $dayIncome > 0 ? $dayIncome : 1;
								$dayIncome = $dayIncome < 10 ? '0' . $dayIncome : $dayIncome;

								foreach ($data['report_price'] as $year => $months) {
									foreach ($months as $month => $totalPrice) {
										$formattedPrice = number_format($totalPrice, 0);
										$month = $month < 10 ? '0' . $month : $month;

										echo sprintf('<p>%s/%s/%s: %s %s</p>', $dayIncome, $month, $year, $formattedPrice, $currencySymbol);
									}
								}
							}
							?>
							<h3><?php echo __('Giorni al pagamento', 'cutaway') ?></h3>
							<?php if (!empty($data['day_to_pay'])) : ?>
							<p><?php echo $data['day_to_pay'] ?></p>
							<?php endif; ?>
							<h3><?php echo __('Stelle Valutazione', 'cutaway') ?></h3>
							<?php if (!empty($data['rating'])) : ?>
							<div class="rating star-<?php echo $data['rating']['star']; ?>">
								<span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span>
								<em> ( <?php echo $data['rating']['review']; ?> <?php echo __('giudizi', 'cutaway') ?>)</em>
							</div>
							<?php endif; ?>
							<h3><?php echo __('Cambia Disponibilità', 'cutaway') ?></h3>
							<p>
								<input type="checkbox" class="barber-available-book" value="1" <?php echo isset($data['book_available']) && $data['book_available'] ? 'checked="checked"' : '' ?> />
							</p>
						</div>
						<?php
						}
					}
                endif; ?>
			</main><!-- #main -->

	</div><!-- Content end -->

</div><!-- Wrapper main end -->

<?php
get_footer();
?>