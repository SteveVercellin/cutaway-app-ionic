<?php

// Services post type -----------------------------------------------------

// add_action('init', 'cutaway_service_custom_init');
function cutaway_service_custom_init() {
	$labels = array(
		'name'               => _x('Services', 'post type general name', 'cutaway'),
		'singular_name'      => _x('Service', 'post type singular name', 'cutaway'),
		'add_new'            => _x('Add New', 'service', 'cutaway'),
		'add_new_item'       => __('Add New Service', 'cutaway'),
		'edit_item'          => __('Edit Service', 'cutaway'),
		'new_item'           => __('New Service', 'cutaway'),
		'view_item'          => __('View Service', 'cutaway'),
		'search_items'       => __('Search Services', 'cutaway'),
		'not_found'          => __('No services found', 'cutaway'),
		'not_found_in_trash' => __('No services found in Trash', 'cutaway'),
		'parent_item_colon'  => '',
		'menu_name'          => 'Services'
	);

	$args = array(
		'labels'        => $labels,
		'public'        => true,
		'show_ui'       => true,
		'query_var'     => true,
		'rewrite'       => array("slug" => "service", 'with_front' => false),
		'menu_position' => 5,
		'supports'      => array('editor','title', 'thumbnail'),
		'menu_icon'     => 'dashicons-admin-post',
	);
	register_post_type('service', $args);
}

// Services Taxonomy
// add_action('init', 'cutaway_service_taxonomies_init');
function cutaway_service_taxonomies_init() {
	$labels = array(
		'name'              => __('Categories', 'cutaway'),
		'singular_name'     => __('Categories', 'cutaway'),
		'search_items'      => __('Search Categories', 'cutaway'),
		'all_items'         => __('All Categories', 'cutaway'),
		'parent_item'       => __('Parent Categories', 'cutaway'),
		'parent_item_colon' => __('Parent Categories:', 'cutaway'),
		'edit_item'         => __('Edit Category', 'cutaway'),
		'update_item'       => __('Update Category', 'cutaway'),
		'add_new_item'      => __('Add New Category', 'cutaway'),
		'new_item_name'     => __('New Category Name', 'cutaway'),
		'menu_name'         => __('Categories', 'cutaway'),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => ['hierarchical' => true]
	);

	register_taxonomy('service-category', array('service'), $args);
}

// Barber post type -----------------------------------------------------

//add_action('init', 'cutaway_barber_custom_init');
function cutaway_barber_custom_init() {
	$labels = array(
		'name'               => _x('Barbers', 'post type general name', 'cutaway'),
		'singular_name'      => _x('Barber', 'post type singular name', 'cutaway'),
		'add_new'            => _x('Add New', 'barber', 'cutaway'),
		'add_new_item'       => __('Add New Barber', 'cutaway'),
		'edit_item'          => __('Edit Barber', 'cutaway'),
		'new_item'           => __('New Barber', 'cutaway'),
		'view_item'          => __('View Barber', 'cutaway'),
		'search_items'       => __('Search Barber', 'cutaway'),
		'not_found'          => __('No barbers found', 'cutaway'),
		'not_found_in_trash' => __('No barbers found in Trash', 'cutaway'),
		'parent_item_colon'  => '',
		'menu_name'          => 'Barbers'
	);

	$args = array(
		'labels'        => $labels,
		'public'        => true,
		'show_ui'       => true,
		'query_var'     => true,
		'rewrite'       => array("slug" => "barber", 'with_front' => false),
		'menu_position' => 5,
		'supports'      => array('editor','title', 'thumbnail'),
		'menu_icon'     => 'dashicons-admin-post',
	);
	register_post_type('barber', $args);
}

// Barber Taxonomy
//add_action('init', 'cutaway_barber_taxonomies_init');
function cutaway_barber_taxonomies_init() {
	$labels = array(
		'name'              => __('Categories', 'cutaway'),
		'singular_name'     => __('Categories', 'cutaway'),
		'search_items'      => __('Search Categories', 'cutaway'),
		'all_items'         => __('All Categories', 'cutaway'),
		'parent_item'       => __('Parent Categories', 'cutaway'),
		'parent_item_colon' => __('Parent Categories:', 'cutaway'),
		'edit_item'         => __('Edit Category', 'cutaway'),
		'update_item'       => __('Update Category', 'cutaway'),
		'add_new_item'      => __('Add New Category', 'cutaway'),
		'new_item_name'     => __('New Category Name', 'cutaway'),
		'menu_name'         => __('Categories', 'cutaway'),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => ['hierarchical' => true]
	);

	register_taxonomy('barber-category', array('barber'), $args);
}
