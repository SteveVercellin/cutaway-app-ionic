<?php

add_shortcode('sc_list_barber', 'cutaway_sc_list_barber');
function cutaway_sc_list_barber( $atts ) {
	
	ob_start(); ?>
		<div class="top-content">
			<div class="wrap-page-title">
				<a href="#" class="top-left-icon back-to-previou-page"><img src="<?php echo get_template_directory_uri();?>/images/registrati-icon.png"></a>
				<h1 class="page-title">&nbsp;</h1>
				<a href="#" class="top-right-icon"><img src="<?php echo get_template_directory_uri();?>/images/resource-icon.png"></a>
			</div>
		</div>
		<?php
		$args1 = [
			'post_type' => 'barber',
			'posts_per_page' => -1,
			'orderby' => 'date',
			'order' => 'ASC',
			'tax_query' => [
				[
					'taxonomy' => 'barber-category',
					'field' => 'slug',
					'terms' => 'disponibile-nella-giornata-di-oggi'
				],
			],
		];

		$barber_available_today = new WP_Query($args1);
		
		?>
		<div class="barber-lists available-today">
			<h3>Disponibile nella giornata di oggi</h3>
			<?php
			if ( $barber_available_today->have_posts() ) :
				while ( $barber_available_today->have_posts() ) : $barber_available_today->the_post(); ?>

					<div class="barber-detail">
						<div class="barber-thumbnail">
							<img src="<?php echo the_post_thumbnail_url('full'); ?>">
						</div>
						<div class="barber-info">
							<div class="name"><?php the_title(); ?></div>
							<div class="address"><?php echo get_the_content(); ?></div>
							<div class="time">&euro;&euro;&euro; | <?php echo get_field('time_work', get_the_ID()); ?></div>
							<div class="rating"><img src="<?php echo get_template_directory_uri();?>/images/rating.png"> <span>(13 ratings)</span></div>
						</div>
					</div> <?php
				endwhile;
				wp_reset_postdata();
			endif; ?>
		</div>

		<div class="barber-lists available-following-days">
			<h3>Disponibili nei giomi successivi</h3>
			<?php

			$args2 = [
				'post_type' => 'barber',
				'posts_per_page' => -1,
				'orderby' => 'date',
				'order' => 'ASC',
				'tax_query' => [
					[
						'taxonomy' => 'barber-category',
						'field' => 'slug',
						'terms' => 'disponibili-nei-giomi-successivi'
					],
				],
			];

			$barber_available_following_days = new WP_Query($args2);
			
			if ( $barber_available_following_days->have_posts() ) :
				while ( $barber_available_following_days->have_posts() ) : $barber_available_following_days->the_post(); ?>

					<div class="barber-detail">
						<div class="barber-thumbnail">
							<img src="<?php echo the_post_thumbnail_url('full'); ?>">
						</div>
						<div class="barber-info">
							<div class="name"><?php the_title(); ?></div>
							<div class="address"><?php echo get_the_content(); ?></div>
							<div class="time">&euro;&euro;&euro; | <?php echo get_field('time_work', get_the_ID()); ?></div>
							<div class="rating"><img src="<?php echo get_template_directory_uri();?>/images/rating.png"> <span>(13 ratings)</span></div>
						</div>
					</div> <?php
				endwhile;
				wp_reset_postdata();
			endif; ?>
		</div>

	<?php
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}