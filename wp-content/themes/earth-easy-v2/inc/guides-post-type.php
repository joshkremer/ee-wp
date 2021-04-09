<?php
// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_guide_taxonomies', 0 );

// create two taxonomies, genres and writers for the post type "book"
function create_guide_taxonomies() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Guides Categories', 'taxonomy general name', 'textdomain' ),
		'singular_name'     => _x( 'Guides Categories', 'taxonomy singular name', 'textdomain' ),
		'search_items'      => __( 'Search Guides Categories', 'textdomain' ),
		'all_items'         => __( 'All Guides Categories', 'textdomain' ),
		'parent_item'       => __( 'Parent Guides Categories', 'textdomain' ),
		'parent_item_colon' => __( 'Parent Guides Categories:', 'textdomain' ),
		'edit_item'         => __( 'Edit Guides Categories', 'textdomain' ),
		'update_item'       => __( 'Update Guides Categories', 'textdomain' ),
		'add_new_item'      => __( 'Add New Guides Categories', 'textdomain' ),
		'new_item_name'     => __( 'New Guides Categories Name', 'textdomain' ),
		'menu_name'         => __( 'Guides Categories', 'textdomain' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'guides-categories' ),
	);

	register_taxonomy( 'guides-categories', array( 'guide' ), $args );
}


function guides_post_type() {
  $labels = array(
	'name' => _x('Guides', 'post type general name'),
	'singular_name' => _x('Guide', 'post type singular name'),
	'add_new' => _x('Add New', 'Guide'),
	'add_new_item' => __('Add New Guide'),
	'edit_item' => __('Edit Guide'),
	'new_item' => __('New Guide'),
	'all_items' => __('All Guides'),
	'view_item' => __('View Guide'),
	'search_items' => __('Search Guides'),
	'not_found' =>  __('No Guides Found'),
	'not_found_in_trash' => __('No Guides Found in Trash'), 
	'parent_item_colon' => '',
	'menu_name' => 'Guides'

  );
  $args = array(
	'labels' => $labels,
	'public' => true,
	'publicly_queryable' => true,
	'show_ui' => true, 
	'show_in_menu' => true, 
	'query_var' => true,
	'rewrite' => array( 'slug' => 'guides' ),
	'capability_type' => 'post',
	'has_archive' => true, 
	'hierarchical' => false,
	'menu_position' => null,
	'taxonomies' => array('guides-categories'),
	'supports' => array( 'title', 'editor', 'author', 'revisions' )
  ); 
  register_post_type('ee_guides',$args);
}
add_action( 'init', 'guides_post_type' );