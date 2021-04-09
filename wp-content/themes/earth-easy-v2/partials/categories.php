<?php
// Categories
$category_args = array(
	'orderby' => 'term_order',
	'hide_empty' => 0
);
$categories = get_terms('category', $category_args);
$context['categories'] = $categories;

$cat_array = array();
foreach($categories as $cat) {
	$cat_array[] = get_field('category_icon', 'category_' . $cat->term_id); 
}
$context["category_icons"] = $cat_array;


$ee_articles_args = array(
	'orderby' => 'term_order',
	'hide_empty' => 0,
	'taxonomy' => 'articles-categories',
	// 'post_type' => array('ee_articles'),
	// 'taxonomies' => array('articles-categories')
);
$ee_articles_categories = get_terms( $ee_articles_args );
$context['articles_categories'] = $ee_articles_categories;


$ee_articles_array = array();
foreach($ee_articles_categories as $cat) {
	$ee_articles_array[] = get_field('category_icon', 'category_' . $cat->term_id); 
	$ee_articles_link[] = get_category_link( $cat->term_id );
}
$context["articles_category_icons"] = $ee_articles_array;
$context["articles_category_link"] = $ee_articles_link;

$ee_guides_args = array(
	'orderby' => 'term_order',
	'hide_empty' => 0,
	'taxonomy' => 'guides-categories'
	// 'post_type' => array('ee_guides'),
	// 'taxonomies' => array('guides-categories')
);
$ee_guides_categories = get_terms( $ee_guides_args );
$context['guides_categories'] = $ee_guides_categories;


$ee_guides_array = array();
foreach($ee_guides_categories as $cat) {
	$ee_guides_array[] = get_field('category_icon', 'category_' . $cat->term_id); 
	$ee_guides_link[] = get_category_link( $cat->term_id );
}
$context["guides_category_icons"] = $ee_guides_array;
$context["guides_category_link"] = $ee_guides_link;



$article_terms = array();
$article_cats = get_terms( array( 'taxonomy' => 'articles-categories', 'hide_empty' => true ) );
foreach( $article_cats as $acat ){
	$article_terms[ $acat->slug ] = get_posts( array( 'post_type' => 'ee_articles', 'posts_per_page' => 10, 
		'tax_query' => array(
			array(
				'taxonomy' => 'articles-categories',
				'field'    => 'slug',
				'terms'    => $acat->slug
			),
		),		
	) );
}
$context["article_terms"] = $article_terms;

$guide_terms = array();
$guide_cats = get_terms( array( 'taxonomy' => 'guides-categories', 'hide_empty' => true ) );
foreach( $guide_cats as $gcat ){
	$guide_terms[ $gcat->slug ] = get_posts( array( 'post_type' => 'ee_guides', 'posts_per_page' => 10, 
		'tax_query' => array(
			array(
				'taxonomy' => 'guides-categories',
				'field'    => 'slug',
				'terms'    => $gcat->slug
			),
		),		
	) );
}
$context["guide_terms"] = $guide_terms;