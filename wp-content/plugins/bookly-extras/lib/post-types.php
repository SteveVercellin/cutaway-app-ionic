<?php

// Register post type
add_action('init', 'cutaway_register_cpt');

function cutaway_register_cpt() {

	$labels = array(
		'name'               => __('Galleries', 'cutaway'),
		'singular_name'      => __('Gallery', 'cutaway'),
		'add_new'            => __('Add New', 'cutaway'),
		'add_new_item'       => __('Add New Gallery', 'cutaway'),
		'edit_item'          => __('Edit Gallery', 'cutaway'),
		'new_item'           => __('New Gallery', 'cutaway'),
		'view_item'          => __('View Gallery', 'cutaway'),
		'search_items'       => __('Search Galleries', 'cutaway'),
		'not_found'          => __('No shift8 portfolios found', 'cutaway'),
		'not_found_in_trash' => __('No shift8 portfolios found in Trash', 'cutaway'),
		'parent_item_colon'  => __('Parent Gallery:', 'cutaway'),
		'menu_name'          => __('Galleries', 'cutaway'),
	);

	$args = array(
		'labels'              => $labels,
		'hierarchical'        => false,
		'description'         => '',
		'supports'            => array('title'),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-grid-view',
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => array(
			'slug'       => 'gallery',
			'with_front' => true,
			'feeds'      => true,
			'pages'      => true
		),
		'capability_type'     => 'post'
	);

	register_post_type('gallery', $args);
}
