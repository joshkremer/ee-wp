<?php

/*  Template Name:  Learn Page  */

// Content
$context = Timber::get_context();
$post = new TimberPost();

include_once('bc_functions.php');

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


//  Get guides or articles by cat
$filterargs = array(
	'post_status' => 'publish',
	'posts_per_page' => 34,
	'orderby' => array(
        'date' => 'DESC'
	),
	'post_type' => array(
		'ee_guides',
		'ee_articles'
	)
);

$type = $_SERVER['REQUEST_URI'];

if(strpos($type, 'articles') !== false) {
	$filterargs['post_type'] = 'ee_articles';
	$context['post_type'] = 'article';
}
else if(strpos($type, 'guides') !== false) {
	$filterargs['post_type'] = 'ee_guides';
	$context['post_type'] = 'guide';
}
else if(strpos($type, 'learn') !== false) {
	$context['post_type'] = 'learn';
}


if(isset($_GET['cat'])) {
	$cat = urldecode($_GET['cat']);

	if(term_exists($cat, 'category') !== NULL) {
		$filterargs['category'] = get_cat_ID($cat);
	}

	$context['thiscat'] = Timber::get_terms('category', array('slug' => strtolower($cat)));
}

if(isset($_GET['tag'])) {
	$tag = urldecode($_GET['tag']);
	$tag = str_replace(' ', '-', $tag);

	if(term_exists($tag, 'post_tag') !== NULL) {
		$filterargs['tag'] = array($tag);
	}
}
$context['category_posts'] = Timber::get_posts($filterargs);

echo '<!--';
var_dump($filterargs);
echo '-->';

/*  

Begin function to grab products for the bottom of each article.

*/
//  Get the products for the bottom section (articles)

/*function get_bigcommerce_product() {

	return;
	
	$url = 'https://store-j602wc6a.mybigcommerce.com/api/v2/products.json?limit=12';

	$cURL = curl_init();

	$username = 'eartheasy';
	$password = 'bd302a703fb9addd0f9b3482c30d9e18cb0d0af7';

	curl_setopt($cURL, CURLOPT_URL, $url);
	curl_setopt($cURL, CURLOPT_HTTPGET, true);
	curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($cURL, CURLOPT_USERPWD, "$username:$password");

	curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
    	'Content-Type: application/json',
    	'Accept: application/json'
	));

	$result = curl_exec($cURL);

	curl_close($cURL);

	$result_array = json_decode($result, true);

	return $result_array;
    
}*/

//  From the BigCommerce products returned, build an array for each to pass to context
$context['learn_products'] = get_bigcommerce_limited_products();

// Tags
include_once('partials/tags.php');

// Tags
include_once('partials/categories.php');


// Render Template
Timber::render('learn.twig', $context);


