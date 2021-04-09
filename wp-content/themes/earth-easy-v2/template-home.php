<?php
/*
Template Name: Home
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

$context['category_posts'] = Timber::get_posts($filterargs);

    $select_type        = get_field( 'select_type' );
    $article_products   = get_field( 'article_products' );
    $product_category   = get_field( 'product_category' );
    $products_by_id     = get_field( 'products_by_id' );

    $context['select_type'] = $select_type;
    $context['product_category'] = $article_products;

    $default_products   = get_field( 'default_product_ids', 'option' ); 

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
                //"image_url" => $prod['primary_image']['standard_url'],
                "image_url" => $prod['image_url'],
                "name" => $prod['name'],
                "link" => $prod['custom_url']
            ));
        }
    }else{
        $context['archive_products'] = get_bigcommerce_related_products_by_id( $default_products );
    }*/

// slider
$slides_args = array(
    'post__in' => $post->home_slides,
    'orderby' => 'post__in',
    'posts_per_page' => 1,
    'post_type' => array(
        'ee_guides',
        'ee_articles'
    )
);
$context['slider_posts'] = Timber::get_posts($slides_args);

//  From the BigCommerce products returned, build an array for each to pass to context
// $context['archive_products'] = get_bigcommerce_product();

// Categories
include_once('partials/categories.php');

// Tags
include_once('partials/tags.php');

// Render Template
Timber::render(array('template-' . $post->post_name . '.twig', 'template-home.twig', 'base.twig'), $context);