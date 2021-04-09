<?php
/*
Template Name: Articles
*/


// Content
$context = Timber::get_context();
$post = new TimberPost();

$context['post'] = $post;

// Latest post for mega menu
$args = array(
    'post_type' => array(
        'ee_articles',
        'ee_guides'
    ),
    'posts_per_page' => 2,
    'orderby' => array(
        'date' => 'DESC'
    )
);    

$context['newpost'] = Timber::get_posts( $args );


//  Post ID list to display most recent, max 6
$args = array(
    'post_type' => array(
        'ee_articles',
        'ee_guides'
    ),
    'posts_per_page' => 6,
    'orderby' => array(
        'date' => 'DESC'
    )
);    


$context['post_id_list'] = Timber::get_posts($args);


// Categories
include_once('partials/categories.php');



// Tags
include_once('partials/tags.php');



// Render Template
Timber::render(array('template-' . $post->post_name . '.twig', 'template-articles.twig', 'base.twig'), $context);