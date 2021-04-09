<?php
// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_book_taxonomies', 0 );

// create two taxonomies, genres and writers for the post type "book"
function create_book_taxonomies() {
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name'              => _x( 'Articles Categories', 'taxonomy general name', 'textdomain' ),
    'singular_name'     => _x( 'Articles Categories', 'taxonomy singular name', 'textdomain' ),
    'search_items'      => __( 'Search Articles Categories', 'textdomain' ),
    'all_items'         => __( 'All Articles Categories', 'textdomain' ),
    'parent_item'       => __( 'Parent Articles Categories', 'textdomain' ),
    'parent_item_colon' => __( 'Parent Articles Categories:', 'textdomain' ),
    'edit_item'         => __( 'Edit Articles Categories', 'textdomain' ),
    'update_item'       => __( 'Update Articles Categories', 'textdomain' ),
    'add_new_item'      => __( 'Add New Articles Categories', 'textdomain' ),
    'new_item_name'     => __( 'New Articles Categories Name', 'textdomain' ),
    'menu_name'         => __( 'Articles Categories', 'textdomain' ),
  );

  $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
    'rewrite'           => array( 'slug' => 'articles-categories' ),
  );

  register_taxonomy( 'articles-categories', array( 'book' ), $args );
}

function articles_post_type() {
  $labels = array(
  'name' => _x('Articles', 'post type general name'),
  'singular_name' => _x('Article', 'post type singular name'),
  'add_new' => _x('Add New', 'Article'),
  'add_new_item' => __('Add New Article'),
  'edit_item' => __('Edit Article'),
  'new_item' => __('New Article'),
  'all_items' => __('All Articles'),
  'view_item' => __('View Article'),
  'search_items' => __('Search Articles'),
  'not_found' =>  __('No Articles Found'),
  'not_found_in_trash' => __('No Articles Found in Trash'), 
  'parent_item_colon' => '',
  'menu_name' => 'Articles'

  );
  $args = array(
  'labels' => $labels,
  'public' => true,
  'publicly_queryable' => true,
  'show_ui' => true, 
  'show_in_menu' => true, 
  'show_in_rest' => true,
  'query_var' => true,
  'rewrite' => array( 'slug' => 'articles' ),
  'capability_type' => 'post',
  'has_archive' => true, 
  'hierarchical' => false,
  'menu_position' => null,
  'taxonomies' => array('articles-categories'),
  'supports' => array( 'title', 'editor', 'author', 'comments', 'revisions' )
  ); 
  register_post_type('ee_articles',$args);
}
add_action( 'init', 'articles_post_type' );
