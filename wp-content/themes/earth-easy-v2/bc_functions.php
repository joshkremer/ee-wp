<?php 
$bc_products = wp_cache_get( 'bc_products' );
if ( empty($bc_products) ) {
	$query  = 'SELECT * FROM `P80_bc_products` WHERE is_visible = 1';
	$bc_products = $wpdb->get_results( $query );
	wp_cache_set( 'bc_products', $bc_products );
} 
// wp_cache_set( 'bc_products', '');


$bc_product_categories = wp_cache_get( 'bc_product_categories' );
if ( empty($bc_product_categories) ) {

	/*$query  = 'SELECT * FROM `P80_bc_products` p
		INNER JOIN P80_bc_product_categories pc 
		ON p.product_id = pc.product_id 
		WHERE p.is_visible = 1';*/
	$query = 'SELECT p.`product_id`,p.`product_name`,p.`primary_image`,p.`custom_url`,p.`is_visible`,p.`delete_flag`,
pc.`category_id`,ca.`category_name` 
        FROM `P80_bc_products` p
		INNER JOIN P80_bc_product_categories pc 
		ON p.product_id = pc.product_id
		LEFT JOIN P80_bc_categories ca 
		ON pc.category_id = ca.category_id 
		WHERE p.is_visible = 1';

	$bc_product_categories = $wpdb->get_results( $query );
	wp_cache_set( 'bc_product_categories', $bc_product_categories );
} 
// wp_cache_set( 'bc_product_categories', '');


$bc_categories = wp_cache_get( 'bc_categories' );
if ( empty($bc_categories) ) {

	$query  = 'SELECT * FROM `P80_bc_categories` WHERE is_visible = 1';
	$bc_categories = $wpdb->get_results( $query );
	wp_cache_set( 'bc_categories', $bc_categories );
} 
// wp_cache_set( 'bc_categories', '');die;


// related products by IDs
function get_bigcommerce_related_products_by_id( $product_id_array ){
	$result_array = array();	
	if( isset( $product_id_array ) && !empty($product_id_array)){
		$product_ids = explode( ',', $product_id_array );
		foreach( $product_ids as $product_id ) {
			$prod = get_bigcommerce_product($product_id);			
			$result_array[] = $prod;
		}
	}
	return $result_array;
}


function get_bigcommerce_related_products($category) {

	$category =  urldecode($category);
	
	$bc_product_categories = wp_cache_get( 'bc_product_categories' );

	$BcProducts = array();	
	
	if(!empty($category) && (!empty($bc_product_categories))){

		foreach ($bc_product_categories as $item) {	

			$prod = array();

			if(trim($item->category_name) == trim($category) || trim($item->category_id) == trim($category)){	

				$prod['id']        	   = $item->product_id;

				$prod['name']          = $item->product_name;

				// $prod['primary_image']['standard_url'] = $item->primary_image;
				$prod['image_url'] = $item->primary_image;				

				$prod['custom_url']    = $item->custom_url;

				$prod['category_id']   = $item->category_id;

				array_push($BcProducts, $prod);
			}

		}

	}	
	return $BcProducts;

}


function get_bc_guide_category_products($category) {	
	
	$category = explode(',',$category);
	
	$bc_product_categories = wp_cache_get( 'bc_product_categories' );

	$BcProducts = array();	
	
	if(!empty($category) && (!empty($bc_product_categories))){

		foreach ($bc_product_categories as $item) {	

			$prod = array();

			if( in_array($item->category_id, $category)){	

				$prod['id']        	   = $item->product_id;

				$prod['name']          = $item->product_name;

				// $prod['primary_image']['standard_url'] = $item->primary_image;
				$prod['image_url'] = $item->primary_image;

				$prod['custom_url']    = $item->custom_url;

				$prod['category_id']   = $item->category_id;

				array_push($BcProducts, $prod);
			}

		}

	}	
	return $BcProducts;

}


function get_bigcommerce_product($id) {


	$bc_products = wp_cache_get( 'bc_products' );	
	
	$prod = array();

	if(!empty($id) && (!empty($bc_products))){
		foreach ($bc_products as $item) {			
			if($item->product_id == $id){
				$prod['id']        	   = $item->product_id;
				$prod['name']          = $item->product_name;
				// $prod['primary_image']['standard_url'] = $item->primary_image;
				$prod['image_url'] = $item->primary_image;
				$prod['custom_url']    = $item->custom_url;	
				return $prod;			
			}			
		}
	}
	return;

}


function get_bigcommerce_limited_products($limit = 12) {


	$bc_products = wp_cache_get('bc_products');
	$BcProducts = array();
	$count = 0;

	
	if(!empty($limit) && (!empty($bc_products))){
		foreach ($bc_products as $item) {
			$prod = array();			
			$prod['id']        	   = $item->product_id;
			$prod['name']          = $item->product_name;
			// $prod['primary_image']['standard_url'] = $item->primary_image;
			$prod['image_url'] = $item->primary_image;
			$prod['custom_url']    = $item->custom_url;			
			array_push($BcProducts, $prod);		

			$count++;
			if($count == $limit){
				return $BcProducts;
			}	
		}
	}
	
	return $BcProducts;

}


function acf_load_bc_categories_field_choices( $field ) {

	$all_categories = wp_cache_get('bc_categories');	

	$field['choices'] = array();

	if(!empty($all_categories)){
		foreach( $all_categories as $choice ) {

			$field['choices'][$choice->category_id] = $choice->category_name;

		}
	}	

    // return the field
    return $field;

	
}

function acf_load_bc_products_field_choices( $field ) {

	$all_products = wp_cache_get('bc_products');	

	$field['choices'] = array();

	if(!empty($all_products)){
		foreach( $all_products as $choice ) {

			$field['choices'][$choice->product_id] = $choice->product_name;

		}
	}

    // return the field
    return $field;

	
}

function get_bc_backend_products( $field ) {

	$all_products = wp_cache_get('bc_products');	

	$field['choices'] = array();

	if(!empty($all_products)){
		foreach( $all_products as $choice ) {

			$field['choices'][$choice->product_id] = '['.$choice->product_id.']'.$choice->product_name;

		}
	}

    // return the field
    return $field;

	
}
/**
* Correct the array formating for the display ( no need )
**/
function correct_formating($archive_products = [] ){
    $response  = [];
	if (!empty($archive_products)){
		foreach ($archive_products as $prod) {
			$data = [];
			$data['id'] =  $prod['id'];			
			// $data['primary_image']['standard_url'] = $prod['primary_image'];
			$data['image_url'] = $prod['primary_image'];
			$data['custom_url'] =  $prod['custom_url'];
			$data['name'] =  $prod['name'];
			array_push($response, $data);
		}

	}
	return  $response;
} 