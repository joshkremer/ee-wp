<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */


// Content
$context = Timber::get_context();
$post = Timber::query_post();
$context['post'] = $post;

include_once('bc_functions.php');
// $context['post'] = new TimberPost(); 

// Vars
$current_category = '';
if(isset($post->categories[0])){
$current_category = $post->categories[0];	
}
$current_post = $post->ID;
$current_author = $post->post_author;
// Categories
include_once('partials/categories.php');

echo '<!--';
print_r( get_field( 'button_link_url' ) );
print_r( get_field( 'button_label' ) );
echo "\n"; 
echo '-->';

// Get latest articles and trending articles
if( $post->post_type == 'ee_articles') {

	// Get latest articles
	$latest_articles_args = array(
		'post_type' => 'ee_articles',
		'post__not_in' => array($current_post),
		'posts_per_page' => 5
	);
	$context['latest_articles'] = Timber::get_posts($latest_articles_args);



	// Get trending articles
	$trending_articles_args = array(
		'post_type' => 'ee_articles',
		'post__not_in' => array($current_post),
		'posts_per_page' => 3,
		'meta_query' => array(
			array(
				'key'     => 'trending',
				'value'   => '1',
				'compare' => '=',
			),
		)
	);
	$context['trending_articles'] = Timber::get_posts($trending_articles_args);

}

		$newargs = array(
            'post_type' => array(
                'ee_articles',
                'ee_guides'
            ),
            'posts_per_page' => 2,
            'orderby' => array(
                'date' => 'DESC'
            )
        );    

	$context['newpost'] = Timber::get_posts( $newargs );


$type = $_SERVER['REQUEST_URI'];

if(strpos($type, 'articles') !== false) {
	$cat_type['post_type'] = 'ee_articles';
	$taxonomy	= 'articles-categories';
}
else if(strpos($type, 'guides') !== false) {
	$cat_type['post_type'] = 'ee_guides';
	$taxonomy	= 'guides-categories';
}
$categories = get_the_terms( $post->ID, $taxonomy );
foreach( $categories as $category ) {
   // echo $category->term_id . ', ' . $category->slug . ', ' . $category->name . '<br />';
}





$all_related = $post->related_articles_guides;
$context['all_related'] = $post->related_articles_guides;

if( $all_related ){
	$related_posts_args = array(
		'post_type' => array( 'ee_articles', 'ee_guides' ),
		'post__in' => $all_related,
		'orderby' => 'post__in',
	);
}else{
	// Get related Guides & Articles 
	$related_posts_args = array(
		'posts_per_page' => 6,
		'post_type' => $cat_type,
		'post__not_in' => array($current_post),
		'tax_query' => array(
	        array(
	            'taxonomy' => $taxonomy,
	            'terms' => $category->term_id
	        )
	    )
	);
}
$context['related_posts'] = Timber::get_posts($related_posts_args);	



/*


Begin functions to grab products for display in guides


*/





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



//  Look for any products inserted throughout the guide as a layout.

$context['guide_layout_products'] = array();

if( have_rows('guide') ):

    while( have_rows('guide') ) : the_row();
		
		$layout = get_row_layout();
		
		if( $layout === 'products' ) {
			$products_intro = get_sub_field('products_intro_text');
			$products = get_sub_field('products_list');
			if(!empty($products)) {
				foreach($products as $product_id) {
					$this_product = get_bigcommerce_product($product_id);
					// var_dump($this_product);
					array_push($context['guide_layout_products'], array(
						'id' => $product_id,
						'name' => $this_product['name'],
						'link' => $this_product['custom_url'],
						// 'image_url' => $this_product['primary_image']['standard_url'],
						'image_url' => $this_product['image_url'],
					));
				}
			}
		}
    endwhile;
endif;




/*  

Begin function to grab products for the bottom of each article.

*/



//  Get the products for the bottom section (articles)

/*function get_bigcommerce_product($id) {

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
$context['article_products'] = array();

if(!empty($post->article_products)) {
	foreach($post->article_products as $product_id) {
		$prod = get_bigcommerce_product($product_id);
		if(!empty($prod)){
			array_push($context['article_products'], array(
				"id" => $product_id,
				//"image_url" => $prod['primary_image'],
				"image_url" => $prod['image_url'],
				"name" => $prod['name'],
				"custom_url" => $prod['custom_url']
			));
		}		
	}
} 

/*  

Begin function to grab products for the bottom of each article.

*/
//  Get the products for the bottom section (articles)

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

//  From the BigCommerce products returned, build an array for each to pass to context
$context['select_type'] = $post->select_type;
$context['product_category'] = $post->product_category;

$default_products   = get_field( 'default_product_ids', 'option' ); 

$context['related_products_art'] = array();

if( $post->select_type == 'category' && $post->product_category ){

	$context['related_products_art'] = get_bigcommerce_related_products( urlencode( $post->product_category ) );

}elseif( $post->select_type == 'product_ids' && $post->products_by_id ){	

	$context['related_products_art'] = get_bigcommerce_related_products_by_id( $post->products_by_id );

}elseif( $post->select_type == 'product' && $post->article_products){	

	foreach($post->article_products as $product_id) {
		$this_product = get_bigcommerce_product($product_id);
					// print_r($this_product);die;
		if(!empty($this_product)){
			array_push($context['related_products_art'], array(
				'id' => $product_id,
				'name' => $this_product['name'],
				'custom_url' => $this_product['custom_url'],				
				'image_url' => $this_product['image_url'] )
	    	);
		}
		
	}
}

if(empty($context['related_products_art']) && empty($context['article_products'])){	
    $context['archive_products'] = get_bigcommerce_related_products_by_id( $default_products );
}

//  Get the related products from the categories selected for guides


// $related_products = get_bigcommerce_related_products($post->guide_categories);
$related_products = get_bc_guide_category_products($post->guide_categories);

//  Pass the products into the context
if(!empty($related_products)) {
	$context['related_products'] = array();
	for($i=0; $i<3; $i++) {
		array_push($context['related_products'], $related_products[$i]);
	}
}


// related products by IDs
/*function get_bigcommerce_related_products_by_id( $product_id_array ){
	$result_array = array();
	// $context['article_products_by_id'] = array();
	if( isset( $product_id_array ) ){
		$product_ids = explode( ',', $product_id_array );
		foreach( $product_ids as $product_id ) {
			$prod = get_bigcommerce_product($product_id);
			// array_push($context['article_products_by_id'], $prod);
			$result_array[] = $prod;
		}
	}
	return $result_array;
}*/



// pinterest image
if( get_field( 'pint_image', $post->ID ) ){
	$pinterest = get_field( 'pint_image', $post->ID );
}elseif( get_field( 'featured_image', $post->ID ) ){
	$pinterest = get_field( 'featured_image', $post->ID );
}
$context['pinterest'] = ( ! empty( $pinterest ) && isset($pinterest['sizes']['thumb-pinterest']) ) ? $pinterest['sizes']['thumb-pinterest'] : get_bloginfo( 'template_url' ) . '/img/no-pin-image.png';


// echo '------------- '. str_replace('&#038;', '&', get_the_guid());
$link_guid = str_replace('&#038;', '&', get_the_guid());
$link_article = get_permalink();

$feed_count = getDisqusCount($link_article,$link_guid);
$context['feed_count'] = $feed_count;



// Latest post for mega menu
// $context['csv_posts'] = Timber::query_posts( array( 'post_type' => 'ee_guides', 'posts_per_page' => '-1', 'post_status' => 'publish' ) );


// Render template
if (post_password_required($post->ID)){
	Timber::render('single-password.twig', $context);
} else {
	Timber::render(array('single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig'), $context);
}