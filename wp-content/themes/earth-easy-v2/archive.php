<?php

/*  Template Name:  Archive Page  */

// Content
$context = Timber::get_context();
$post = new TimberPost();
$term = new \Timber\Term( get_queried_object() );

// file for BC functions.
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

$context['newpost'] = Timber::query_posts( $args );

$type = $_SERVER['REQUEST_URI'];
$arr    =    explode('/',$type);
$count    =    sizeof($arr);
if($count == 4){
	$term = get_term_by('slug', $arr[2] , $arr[1]);
	$term_id = $term->term_id;
	//  Get guides or articles by cat
	$filterargs = array(
		'post_status' => 'publish',
		'posts_per_page' => 7,
		'orderby' => array(
	        'date' => 'DESC'
		),
		'post_type' => array(
			'ee_guides',
			'ee_articles'
		),
		'tax_query' => array(
	        array(
	            'taxonomy' => $arr[1],
	            'terms' => $term_id
	        )
	    )
	);
}else{
	$filterargs = array(
		'post_status' => 'publish',
		'posts_per_page' => 7,
		'orderby' => array(
	        'date' => 'DESC'
		),
		'post_type' => array(
			'ee_guides',
			'ee_articles'
		)
	);
}
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

$category_posts = Timber::query_posts($filterargs);
$context['category_posts'] = $category_posts;
$context['posts_count'] = count($category_posts);



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

/*function get_bigcommerce_product_by_id($id) {

	return;

	$url = 'https://store-j602wc6a.mybigcommerce.com/api/v2/products/' . $id;

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
// $context['archive_products'] = get_bigcommerce_product();


/*function get_bigcommerce_related_products($category) {

	return;

	$url = 'https://store-j602wc6a.mybigcommerce.com/api/v2/products.json?category=' . $category;
	// $url = 'https://store-j602wc6a.mybigcommerce.com/api/v2/products?category=' . $category;

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

// related products by IDs
/*function get_bigcommerce_related_products_by_id( $product_id_array ){
	$result_array = array();
	// $context['article_products_by_id'] = array();
	if( isset( $product_id_array ) ){
		$product_ids = explode( ',', $product_id_array );
		foreach( $product_ids as $product_id ) {
			$prod = get_bigcommerce_product_by_id($product_id);
			// array_push($context['article_products_by_id'], $prod);
			$result_array[] = $prod;
		}
	}
	return $result_array;
}*/

/*function get_bigcommerce_product_art() {

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


$default_products   = get_field( 'default_product_ids', 'option' ); 

// GUIDE archive related products
if( is_post_type_archive( 'ee_guides' ) || is_post_type_archive( 'ee_articles' ) ){
	if( is_post_type_archive( 'ee_articles' ) ){
		$guide_archive_id = 12254; // 12254 page id of ARTICLES 
		$context['page_type'] = 'all-articles';
	}else{
		$guide_archive_id = 11888; // 11888 page id of GUIDES 
		$context['page_type'] = 'all-guides';
	}

	$select_type 		= get_field( 'select_type', $guide_archive_id );
	$article_products 	= get_field( 'article_products', $guide_archive_id );
	$product_category 	= get_field( 'product_category', $guide_archive_id );
	$products_by_id 	= get_field( 'products_by_id', $guide_archive_id );

	$context['select_type'] = $select_type;
	$context['product_category'] = $article_products;


	if( $select_type == 'category' && $product_category ){
		$context['archive_products'] = get_bigcommerce_related_products( urlencode( $product_category ) );
	}elseif( $select_type == 'product_ids' && $products_by_id ){
		$context['archive_products'] = get_bigcommerce_related_products_by_id( $products_by_id );
	}elseif( $select_type == 'product' ){
		$context['archive_products'] = array();
		foreach($article_products as $product_id) {
			$this_product = get_bigcommerce_product($product_id);
						// print_r($this_product);die;
			if(!empty($this_product)){
				array_push($context['archive_products'], array(
					'id' => $product_id,
					'name' => $this_product['name'],
					'custom_url' => $this_product['custom_url'],				
					'image_url' => $this_product['image_url'] )
		    	);
			}		
	    }
	}/*else{
		$context['archive_products'] = get_bigcommerce_related_products_by_id( $default_products );
	}*/
	if(empty($context['archive_products'])){	
    	$context['archive_products'] = get_bigcommerce_related_products_by_id( $default_products );
	}

}elseif( is_tax( 'guides-categories' ) ){
	$key = 'guides-categories_' . $term->term_id;
	$context['page_type'] = 'taxonomy-guides';

	$select_type 		= get_field( 'select_type', $key );
	$article_products 	= get_field( 'article_products', $key );
	$product_category 	= get_field( 'product_category', $key );
	$products_by_id 	= get_field( 'products_by_id', $key );

	$context['select_type'] = $select_type;
	$context['product_category'] = $article_products;

	if( $select_type == 'category' && $product_category ){
		$context['archive_products'] = get_bigcommerce_related_products( urlencode( $product_category ) );
	}elseif( $select_type == 'product_ids' && $products_by_id ){
		$context['archive_products'] = get_bigcommerce_related_products_by_id( $products_by_id );
	}elseif( $select_type == 'product' ){
		$context['archive_products'] = array();
		foreach($article_products as $product_id) {
			$this_product = get_bigcommerce_product($product_id);
						// print_r($this_product);die;
			if(!empty($this_product)){
				array_push($context['archive_products'], array(
					'id' => $product_id,
					'name' => $this_product['name'],
					'custom_url' => $this_product['custom_url'],				
					'image_url' => $this_product['image_url'] )
		    	);
			}		
	    }
	}/*else{
		$context['archive_products'] = get_bigcommerce_related_products_by_id( $default_products );
	}*/
	if(empty($context['archive_products'])){	
    	$context['archive_products'] = get_bigcommerce_related_products_by_id( $default_products );
	}	
}elseif( is_tax( 'articles-categories' ) ){
	$key = 'articles-categories_' . $term->term_id;
	$context['page_type'] = 'taxonomy-articles';

	$select_type 		= get_field( 'select_type', $key );
	$article_products 	= get_field( 'article_products', $key );
	$product_category 	= get_field( 'product_category', $key );
	$products_by_id 	= get_field( 'products_by_id', $key );

	$context['select_type'] = $select_type;
	$context['product_category'] = $article_products;

	if( $select_type == 'category' && $product_category ){
		$context['archive_products'] = get_bigcommerce_related_products( urlencode( $product_category ) );
	}elseif( $select_type == 'product_ids' && $products_by_id ){
		$context['archive_products'] = get_bigcommerce_related_products_by_id( $products_by_id );
		
	}elseif( $select_type == 'product' ){
		$context['archive_products'] = array();
		foreach($article_products as $product_id) {
			$this_product = get_bigcommerce_product($product_id);
						// print_r($this_product);die;
			if(!empty($this_product)){
				array_push($context['archive_products'], array(
					'id' => $product_id,
					'name' => $this_product['name'],
					'custom_url' => $this_product['custom_url'],				
					'image_url' => $this_product['image_url'] )
		    	);
			}		
	    }
	}/*else{
		$context['archive_products'] = get_bigcommerce_related_products_by_id( $default_products );
	}*/
	if(empty($context['archive_products'])){	
    	$context['archive_products'] = get_bigcommerce_related_products_by_id( $default_products );
	}
	/*elseif( $select_type == 'product' ){
		$context['archive_products'] = array();
		foreach( $article_products as $product_id) {
			$prod = get_bigcommerce_product($product_id);
			array_push($context['archive_products'], array(
				"id" => $product_id,
				// "image_url" => $prod['primary_image']['standard_url'],
				"image_url" => $prod['image_url'],
				"name" => $prod['name'],
				"link" => $prod['custom_url']
			));
		}
	}else{
		$context['archive_products'] = get_bigcommerce_related_products_by_id( $default_products );
	}*/	
}

// Tags
include_once('partials/tags.php');

// Tags
include_once('partials/categories.php');

// Render Template
Timber::render('archive.twig', $context);