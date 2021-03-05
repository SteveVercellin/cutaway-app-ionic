<?php
	// $servicesIds = $controller->getServicesIds($barber['id']);
	// $servicesIds_str = implode(",",$servicesIds);
?>
<a href="<?php echo esc_url_raw(add_query_arg(array('staff' => $barber['id']), home_url() . '/barber-detail' )); ?>" >
	<div class="barber-detail">
		<div class="barber-thumbnail">
			<img src="<?php echo $barber['avatar']; ?>">
		</div>
		<div class="barber-info">
			<div class="name"><?php echo $barber['full_name']; ?></div>
			<div class="location"><?php echo $barber['location']; ?></div>
			<div class="price"><?php esc_html_e('Capelli', 'cutaway'); ?> | <?php esc_html_e('Barba', 'cutaway'); ?> | &euro;&euro;&euro;</div>
			<div class="rating star-4">
				<span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span>
				<em> (13 ratings)</em>
			</div>
		</div>
	</div>
</a>