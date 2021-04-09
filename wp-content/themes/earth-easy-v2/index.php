<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package 	WordPress
 * @subpackage 	Timber
 * @since 		Timber 0.1
 */

	if (!class_exists('Timber')){
		echo 'Timber not activated. Make sure you activate the plugin in <a href="/wp-admin/plugins.php#timber">/wp-admin/plugins.php</a>';
		return;
	}
	$context = Timber::get_context();
	$context['posts'] = Timber::get_posts();
	$context['foo'] = 'bar';
	$templates = array('index.twig');


	if(isset($_GET['search']))
	{
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

		$context['newpost'] = Timber::query_posts( $args );
			$context['search']	= $_GET['search'];
		$args = array(
		    's' =>  $context['search'],
		    'posts_per_page' => '-1'
		);  
		$context['category_posts'] = Timber::query_posts($args);
	}
	if (is_home()){
		array_unshift($templates, 'home.twig');
	}
	// Tags
include_once('partials/tags.php');

// Tags
include_once('partials/categories.php');

	Timber::render($templates, $context);