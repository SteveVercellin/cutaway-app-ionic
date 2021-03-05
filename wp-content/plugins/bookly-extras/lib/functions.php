<?php

use App\Models\Service;
use App\Models\Staff;

function get_service_galleries() {
	$args = [
		'post_type'      => 'gallery',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'asc',
	];

	$results = [];
	$query = new WP_Query($args);

	if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
		$results[get_the_ID()] = get_the_title();
	endwhile; endif;
	wp_reset_query();

	return $results;
}

function acf_load_service_field_choices($field) {

	// reset choices
	$field['choices'] = [];

	$services = Service::orderBy('title', 'asc')->pluck('title', 'id');

	foreach ($services as $service_id => $service_title) {
		$field['choices'][$service_id] = $service_title;
	}

	return $field;
}

add_filter('acf/load_field/name=service', 'acf_load_service_field_choices');

function acf_load_staff_field_choices($field) {

	// reset choices
	$field['choices'] = [];

	$staffs = Staff::orderBy('full_name', 'asc')->pluck('full_name', 'id');

	foreach ($staffs as $staff_id => $staff_title) {
		$field['choices'][$staff_id] = $staff_title;
	}

	return $field;
}

add_filter('acf/load_field/name=staff', 'acf_load_staff_field_choices');

function get_service_gallery($service_id) {
	$args = [
		'post_type'      => 'gallery',
		'posts_per_page' => 1,
		'orderby'        => 'title',
		'order'          => 'asc',
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => 'type',
				'value'   => 'service',
			),
			array(
				'key'     => 'service',
				'value'   => $service_id,
			),
		),
	];

	$result = 0;
	$query = new WP_Query($args);

	if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
		$result = get_the_ID();
	endwhile; endif;
	wp_reset_query();

	return $result;
}

function get_staff_gallery($staff_id) {
	$args = [
		'post_type'      => 'gallery',
		'posts_per_page' => 1,
		'orderby'        => 'title',
		'order'          => 'asc',
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => 'type',
				'value'   => 'staff',
			),
			array(
				'key'     => 'staff',
				'value'   => $staff_id,
			),
		),
	];

	$result = 0;
	$query = new WP_Query($args);

	if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
		$result = get_the_ID();
	endwhile; endif;
	wp_reset_query();

	return $result;
}
