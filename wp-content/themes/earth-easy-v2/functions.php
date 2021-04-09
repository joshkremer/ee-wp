<?php

@ini_set( 'upload_max_size' , '20M' );
@ini_set( 'post_max_size', '20M');
@ini_set( 'max_execution_time', '300' );


// remove junk from head
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);

include_once('bc_functions.php');

// Add default posts and comments RSS feed links to head
add_theme_support( 'automatic-feed-links' );

if (!class_exists('Timber')){
		add_action( 'admin_notices', function(){
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . admin_url('plugins.php#timber') . '">' . admin_url('plugins.php') . '</a></p></div>';
		});
		return;
	}

	class StarterSite extends TimberSite {

		function __construct(){
			add_theme_support('post-formats');
			add_theme_support('post-thumbnails');
			add_theme_support('menus');
			add_filter('timber_context', array($this, 'add_to_context'));
			add_filter('get_twig', array($this, 'add_to_twig'));
			add_action('init', array($this, 'register_post_types'));
			add_action('init', array($this, 'register_taxonomies'));
			parent::__construct();
		}

		function register_post_types(){
			//this is where you can register custom post types
		}

		function register_taxonomies(){
			//this is where you can register custom taxonomies
		}

		function add_to_context($context){
			$context['menu_main'] = new TimberMenu('main');
			$context['menu_shop'] = new TimberMenu('shop');
			$context['menu_learn'] = new TimberMenu('learn');
			$context['menu_connect'] = new TimberMenu('connect');
			$context['menu_customer'] = new TimberMenu('customer');
			$context['menu_social'] = new TimberMenu('social');
			$context['site'] = $this;
			return $context;
		}

		function add_to_twig($twig){
			/* this is where you can add your own fuctions to twig */
			$twig->addExtension(new Twig_Extension_StringLoader());
			$twig->addFilter('convertNumberToWord', new Twig_Filter_Function('convertNumberToWord'));
			return $twig;
		}

	}

	new StarterSite();


	// Options Page
	if( function_exists('acf_add_options_sub_page') )
	{

		acf_add_options_page();
		acf_add_options_sub_page(array(
			'page_title' => 'Site Options'
		));
	}



add_filter( 'timber_context', 'mytheme_timber_context'  );
function mytheme_timber_context( $context ) {
    $context['options'] = get_fields('option');
    return $context;
}


function convertNumberToWord($num = false)
{
    $num = str_replace(array(',', ' '), '' , trim($num));
    if(! $num) {
        return false;
    }
    $num = (int) $num;
    $words = array();
    $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
        'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
    );
    $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
    $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
        'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
        'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
    );
    $num_length = strlen($num);
    $levels = (int) (($num_length + 2) / 3);
    $max_length = $levels * 3;
    $num = substr('00' . $num, -$max_length);
    $num_levels = str_split($num, 3);
    for ($i = 0; $i < count($num_levels); $i++) {
        $levels--;
        $hundreds = (int) ($num_levels[$i] / 100);
        $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ( $hundreds == 1 ? '' : 's' ) . ' ' : '');
        $tens = (int) ($num_levels[$i] % 100);
        $singles = '';
        if ( $tens < 20 ) {
            $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
        } else {
            $tens = (int)($tens / 10);
            $tens = ' ' . $list2[$tens] . ' ';
            $singles = (int) ($num_levels[$i] % 10);
            $singles = ' ' . $list1[$singles] . ' ';
        }
        $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
    } //end for loop
    $commas = count($words);
    if ($commas > 1) {
        $commas = $commas - 1;
    }
    return implode(' ', $words);
}


// EDITOR STYLES
add_editor_style();

// ENQUEUE STYLESHEET
function mytheme_enqueue_style() {
   // wp_enqueue_style( 'sudo-styles', get_stylesheet_uri(),'', filemtime( get_stylesheet_directory() . '/style.css') ); 
}
//add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_style' );

// ENQUEUE JS STUFF
function theme_scripts() {

	wp_deregister_script('jquery');
	//wp_enqueue_script('jquery');

	//wp_enqueue_script('scripts', get_template_directory_uri() . '/js/main.min.js' , array( 'jquery' ) , filemtime( get_template_directory() . '/js/main.min.js'), true);
	//  Localize ajaxurl for front end
	wp_localize_script( 'scripts', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php')));
}

add_action( 'wp_enqueue_scripts', 'theme_scripts' );

// POST TYPES
require_once('inc/guides-post-type.php');
require_once('inc/articles-post-type.php');


/*  Add AJAX function to grab a preview for quick look  */
add_action("wp_ajax_get_quick_look", "get_quick_look");
add_action("wp_ajax_nopriv_get_quick_look", "get_quick_look");

function get_quick_look() {

	$post_id = $_POST['post_id'];
	$ret = get_post($post_id);
	$content_blocks = get_field('article', $post_id);
	$excerpt = '';

	while(have_rows('article', $post_id)) :
		the_row();
		if(get_row_layout() === 'introduction') {
			$excerpt = get_sub_field('introduction');
			break;
		}
	endwhile;

	$atts = array(
		'title' => $ret->post_title,
		'excerpt' => $excerpt,
		'link' => get_permalink($post_id),
		'imgsrc' => get_field('featured_image', $post_id)['url']
	);

	$ret_html = <<<EOT
	<div class='quick-look-overlay'>
		<div class='quick-look-container'>
			<div style="background-image: url('{$atts['imgsrc']}'); width: 50%; background-size: cover;"></div>
			<div class="quick-look-meta">
				<h3>{$atts['title']}</h3>
				<p>{$atts['excerpt']}</p>
				<a href='{$atts['link']}'>READ NOW</a>
				<span>Close</span>
			</div>
		</div>
	</div>
EOT;

	echo $ret_html;
}


/*  Add in function to grab categories from BigCommerce  */

/*function acf_load_bc_categories_field_choices( $field ) {

	$url = 'https://store-j602wc6a.mybigcommerce.com/api/v2/categories.json';

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

	//  Parse all the category names from the returned result
	$bc_categories = array();

	foreach($result_array as $result) {
		array_push($bc_categories, array('name' => $result['name'], 'id' => $result['id']));
	}
    
    // reset choices
    $field['choices'] = array();

    // loop through array and add to field 'choices'

    foreach( $bc_categories as $choice ) {
        
        $field['choices'][$choice['id']] = $choice['name'];
        
    }

    // return the field
    return $field;
    
}*/


/*  Add in function to grab products from BigCommerce  */

/*function acf_load_bc_products_field_choices( $field ) {

	$url = 'https://store-j602wc6a.mybigcommerce.com/api/v2/products.json?limit=250';

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

	//  Parse all the products names from the returned result
	$bc_products = array();

	foreach($result_array as $result) {

		if( $result['is_visible'] == 1 && $result['inventory_level'] > 0 ){
			array_push($bc_products, array(
				'name' => $result['name'], 
				'id' => $result['id']
			));
		}

	}
    
    // reset choices
    $field['choices'] = array();

    // loop through array and add to field 'choices'

    foreach( $bc_products as $choice ) {
        
        $field['choices'][$choice['id']] = $choice['name'];
        
    }

    // return the field
    return $field;
    
}*/




/*function products_txt(){

	$continue = 1;
	$page = 1;
	$all_products = array();
	while( $continue == 1 ){

		$url = 'https://store-j602wc6a.mybigcommerce.com/api/v2/products.json?limit=250&page=' . $page;

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


		if( empty( $result ) ){
			$continue = 0;
			break;
		}

		// $result_array = json_decode($result, true);
		// $all_products = array_merge( $all_products, json_decode($result, true) );
		$all_products = array_merge( $all_products, json_decode($result, true) );

		
		$page++;
	}

	if( ! empty( $all_products ) ){
		// print_r( $all_products );
		$bc_products = array();

		foreach( $all_products as $result ){
			//if( $result['availability'] == 'available' || $result['is_visible'] == true ){
			if( $result['is_visible'] == true ){
				$bc_products[] = array( 'name' => '['.$result['id'].'] ' . $result['name'], 'id' => $result['id'] );
			}

		}

		$products_txt = get_stylesheet_directory() . '/cache/products.txt'; 
		unlink($products_txt);
		$fp = fopen( $products_txt, 'w' );
		foreach( $bc_products as $product ){
			fwrite($fp, $product['id'] . " : " . $product['name'] . "\n" );
		}
		fclose($fp);		
	}
	
}*/

/*function products_from_txt( $field ) {
    
    // reset choices
    $field['choices'] = array();

	$products_txt = get_stylesheet_directory() . '/cache/products.txt'; 
	$txt_file = fopen($products_txt, "r");
	// print_r( $txt_file );
	while( $line = fgets($txt_file) ) {
		$line_obj = explode( ' : ', str_replace(array("\r", "\n"), '', $line) );
		$field['choices'][$line_obj[0]] = $line_obj[1];
	}
	fclose($txt_file);    
    // return the field
    return $field;
}*/

add_filter('acf/load_field/name=categories', 'acf_load_bc_categories_field_choices');
add_filter('acf/load_field/name=products_list', 'acf_load_bc_products_field_choices');
add_filter('acf/load_field/name=article_products', 'acf_load_bc_products_field_choices');

// add_filter('acf/load_field/name=products_list', 'products_from_txt');
// add_filter('acf/load_field/name=article_products', 'products_from_txt');
add_filter('acf/load_field/name=products_list', 'get_bc_backend_products');
add_filter('acf/load_field/name=article_products', 'get_bc_backend_products');




if ( ! wp_next_scheduled( 'refresh_product_txt' ) ) {
	wp_schedule_event( time(), 'daily', 'refresh_product_txt' );
}

//add_action( 'refresh_product_txt', 'products_txt' );






	add_filter('acf/settings/save_json', 'custom_acf_json_save_point');
	function custom_acf_json_save_point( $path ) {
		$path = get_template_directory() . '/acf-json';
		return $path;
	}	

	add_filter('acf/settings/load_json', 'custom_acf_json_load_point');
	function custom_acf_json_load_point( $paths ) {
		unset($paths[0]);
		$paths[] = get_template_directory() . '/acf-json';
		return $paths;
		
	}




function get_post_acc_category_gd() {
  global $wpdb; 
   $post_id = intval( $_POST['id'] );
   $post_type =  $_POST['type'];
  global $post;

$args = [
	'post_status' => 'publish',
	'post_type' => array(
        'ee_articles',
        'ee_guides'
    ),
    'tax_query' => [
        [
            'taxonomy' =>  $post_type,
            'terms' =>$post_id,
            'include_children' => true // Remove if you need posts from term 7 child terms
        ],
    ],
];

$myposts = get_posts( $args );
  echo json_encode(array('postdata' => $myposts ));
  $html='';
  	foreach ($myposts as $k => $v) {
  		
  	}
}

add_action('wp_ajax_get_post_gd', 'get_post_gd');
add_action('wp_ajax_nopriv_get_post_gd', 'get_post_gd' );

function get_post_gd() {
	$term_id = $_POST['term_id'];
	$taxonomy = $_POST['taxonomy'];
	$ret = array();
	if(strpos($taxonomy, 'articles-categories') !== false) {
		$type = 'ee_articles';
	}
	else if(strpos($taxonomy, 'guides-categories') !== false) {
		$type = 'ee_guides';
	}
	$args = array(
		'post_status' => 'publish',
		'term_id' => $term_id,
		'orderby' => array(
			'date' => 'DESC'
		)
	);

		$categories = get_categories( $args );
		foreach ( $categories as $cat ) {

		// here's my code for getting the posts for custom post type

		$posts_array = query_posts(
		    array(
		    	'posts_per_page' => '12',
		        'post_type' => $type,
		        'tax_query' => array(
		            array(
		                'taxonomy' => $taxonomy,
		                'terms' => $term_id
		            )
		        )
		    )
		);
		//print_r( $posts_array ); 
		while(have_posts()) : the_post();

	$col_post_title = get_the_title();
	$col_post_id = get_the_ID();
	$col_post_link = get_the_permalink();
	$col_post_image_url = get_field('featured_image')['url'];
	$col_post_site_url = site_url();
	$col_post_tags = get_tags();
	$col_post_cat = get_the_terms( $post->$col_post_id,$taxonomy);
	$col_post_tags_html = '';
	$col_post_this_type = explode('_', get_post_type())[1];
	//echo $url  = wp_get_attachment_url($col_post_cat[0]->term_id);
	//echo $taxonomy;
	//echo $col_post_cat[0]->term_id;
	$value = get_field('category_icon',$taxonomy . '_' . $col_post_cat[0]->term_id);
	$slug =  $col_post_cat[0]->slug;
	$url = $_SERVER['HTTP_REFERER'];
	//print_r($col_post_cat);
	//echo $value['url'];
	//echo $value = get_field( 'image', $col_post_cat[0]->term_id );
	//var_dump($col_post_cat);
		foreach($col_tags as $col_tag) {
			$col_tags_html .= <<<EOT
			<li><a href="#"">{$col_tag->name}</a></li>
EOT;
		}
	
		//TODO: Adjust for proper lazy loading on mobile
		$col_this_post_tile = <<<EOT
			<div class="grid-item col-sm-4">
				<article class="post-link" id='{$col_post_id}'>
				<a class="post-link__hover lazyload" href="{$link}" data-bg="$image_url">
						<div class="post-link__content">
							<h3 class="post-link__title">{$col_post_title}</h3>
						</div>
					</a>
					<footer class="post-link__meta">
						<a class="post-link__type" href="{$site_url}/{$col_post_this_type}/">{$col_post_this_type}</a>
						<div class="tags tags--toggle">
							 
						</div>
						<ul class="selector__categories">
							<li>
								<a target="_self" href="../../{$taxonomy}/{$slug}/">
									<label>
										<img src="{$value['url']}" />
										<span>{$col_post_cat[0]->name}</span>
									</label>
								</a>
							</li>
						</ul>
					</footer>
				</article>
			</div>
EOT;
	
		$rets[]= $col_this_post_tile;
 

	endwhile;

	echo json_encode($rets);

	wp_die();

		}

 }


/*

Add AJAX function to lazy load posts

*/

/*  Add AJAX function to grab a preview for quick look  */
add_action("wp_ajax_get_lazy_load_posts", "get_lazy_load_posts");
add_action("wp_ajax_nopriv_get_lazy_load_posts", "get_lazy_load_posts");

function get_lazy_load_posts() {

	$offset = $_POST['offset'];
	$type = $_POST['type'];
	$page_type = $_POST['page_type'];
	$stopat = $_POST['firstpost'];
	$term_id = $_POST['term_id'];
	$texo_name = $_POST['taxonomy'];
	$category_name = $_POST['cat_name'];
	$custom_term_id = get_term_by('slug',$category_name , $texo_name); 
	$custom_tid = $custom_term_id->term_id; 
	$article_single_cat = $_POST['custom_cat_name'];
	$ret = array();
	$ids = $_POST['sum'];
	//print_r($ids);
	parse_str($_POST['cat']);
	parse_str($_POST['tag']);
	if(strpos($type, 'articles') !== false) {
		$type = 'ee_articles';
	}
	else if(strpos($type, 'guides') !== false) {
		$type = 'ee_guides';
	}
	else if(strpos($type, 'learn') !== false) {
		$type = array('ee_articles', 'ee_guides');
	}

	$taxonomies = get_object_taxonomies( $type);
	$taxonomy = $taxonomies[0];
	$tag_id = get_term_by('slug',$category_name , $texo_name); 
	$cat_tag_id = $tag_id->term_id; 
	if(isset($article_single_cat)){
		if($texo_name == 'guides'){
			$texo_name = 'guides-categories';
		}else{
			$texo_name = 'articles-categories';
		}
		$a_tem_i = get_term_by('slug',$article_single_cat , $texo_name); 
		$cat_tag_id = $a_tem_i->term_id;
		
	}

	// $posts_per_page = 6;
	$posts_per_page = 12;
	
	
	if( $page_type == 'show-articles' )
	{
		$args = array(
			'post_status' => 'publish',
			'offset' => $offset,
			'posts_per_page' => $posts_per_page,
			'post_type' => array( 'ee_articles' ),
			'tax_query' => array(
	            array(
	                'taxonomy' => 'articles-categories',
	                'field'    => 'slug',
	                'terms' => $category_name,
	            )
	        )		
		);	

		$grid_classes = 'col-xs-6 col-md-4 grid-item';

	}elseif( $page_type == 'show-guides' )
	{
		$args = array(
			'post_status' => 'publish',
			'offset' => $offset,
			'posts_per_page' => $posts_per_page,
			'post_type' => array( 'ee_guides' ),
			'tax_query' => array(
	            array(
	                'taxonomy' => 'guides-categories',
	                'field'    => 'slug',
	                'terms' => $category_name,
	            )
	        )		
		);	

		$grid_classes = 'col-xs-6 col-md-4 grid-item';

	}elseif( $page_type == 'all-articles-guides' )
	{
		$args = array(
			'post_status' => 'publish',
			'offset' => $offset,
			'posts_per_page' => $posts_per_page,
			'post_type' => array( 'ee_articles', 'ee_guides' ),
		    'orderby' => array(
		        'date' => 'DESC'
		    )			
		);	

		$grid_classes = 'col-xs-6 col-md-4 grid-item';

	}elseif( $page_type == 'all-articles' )
	{
		$args = array(
			'post_status' => 'publish',
			'offset' => $offset,
			'posts_per_page' => $posts_per_page,
			'post_type' => 'ee_articles'
		);	

		$grid_classes = 'col-xs-6 col-md-4 grid-item';

	}elseif( $page_type == 'all-guides' )
	{
		$args = array(
			'post_status' => 'publish',
			'offset' => $offset,
			'posts_per_page' => $posts_per_page,
			'post_type' => 'ee_guides'
		);	

		$grid_classes = 'col-xs-6 col-md-4 grid-item';

		// $single_term_obj = get_term_by( 'slug', $article_single_cat, 'articles-categories' );

	}
	elseif( $page_type == 'single-article' )
	{
		$article_obj = get_page_by_path( $category_name, 'OBJECT', 'ee_articles' );
		$args = array(
			'post_status' => 'publish',
			'offset' => $offset,
			'posts_per_page' => $posts_per_page,
			'post_type' => 'ee_articles',
			'post__not_in' => array( $article_obj->ID ),
			'tax_query' => array(
	            array(
	                'taxonomy' => 'articles-categories',
	                'field'    => 'slug',
	                'terms' => $article_single_cat,
	            )
	        )
		);	

		$grid_classes = 'col-xs-6 col-md-4 grid-item';

		$single_term_obj = get_term_by( 'slug', $article_single_cat, 'articles-categories' );

	}
	elseif( $page_type == 'single-guide' )
	{
		$guide_obj = get_page_by_path( $category_name, 'OBJECT', 'ee_guides' );
		$args = array(
			'post_status' => 'publish',
			'offset' => $offset,
			'posts_per_page' => $posts_per_page,
			'post_type' => 'ee_guides',
			'post__not_in' => array( $guide_obj->ID ),
			'tax_query' => array(
	            array(
	                'taxonomy' => 'guides-categories',
	                'field'    => 'slug',
	                'terms' => $article_single_cat,
	            )
	        )
		);	

		$grid_classes = 'col-xs-6 col-md-4 grid-item';

		$single_term_obj = get_term_by( 'slug', $article_single_cat, 'guides-categories' );

	}
	elseif( $page_type == 'taxonomy-guides' )
	{
		$args = array(
			'post_status' => 'publish',
			'offset' => $offset,
			'posts_per_page' => $posts_per_page,
			'post_type' => 'ee_guides',
			// 'post__not_in' => array( $guide_obj->ID ),
			'tax_query' => array(
	            array(
	                'taxonomy' => 'guides-categories',
	                'field'    => 'slug',
	                'terms' => $category_name,
	            )
	        )
		);	

		$grid_classes = 'col-xs-6 col-md-4 grid-item';
		$single_term_obj = get_term_by( 'slug', $_POST['cat_name'], 'guides-categories' );
	}
	elseif( $page_type == 'taxonomy-articles' )
	{
		$args = array(
			'post_status' => 'publish',
			'offset' => $offset,
			'posts_per_page' => $posts_per_page,
			'post_type' => 'ee_articles',
			// 'post__not_in' => array( $guide_obj->ID ),
			'tax_query' => array(
	            array(
	                'taxonomy' => 'articles-categories',
	                'field'    => 'slug',
	                'terms' => $category_name,
	            )
	        )
		);	

		$grid_classes = 'col-xs-6 col-md-4 grid-item';
		$single_term_obj = get_term_by( 'slug', $_POST['cat_name'], 'articles-categories' );
	}
	elseif(isset($cat_tag_id))
	{
		$args = array(
			'post_status' => 'publish',
			'offset' => $offset,
			'posts_per_page' => $posts_per_page,
			'post_type' => $type,
			'tax_query' => array(
	            array(
	                'taxonomy' => $taxonomy,
	                'terms' => $term_id,
	            )
	        )
		);

		$grid_classes = 'grid-item col-sm-4';
	}
	elseif(isset($cat_tag_id))
	{
		$args = array(
			'post_status' => 'publish',
			'post__not_in' => $ids,
			'offset' => $offset,
			'posts_per_page' => $posts_per_page,
			'post_type' => $type,
			'tax_query' => array(
	            array(
	                'taxonomy' => $texo_name,
	                'terms' => $cat_tag_id,
	            )
	        )
		);

		$grid_classes = 'grid-item col-sm-4';
	}
	else{
		$args = array(
			'post_status' => 'publish',
			'offset' => $offset,
			'posts_per_page' => $posts_per_page,
			'post_type' => $type,
			'orderby' => array(
				'date' => 'DESC'
			)
		);

		$grid_classes = 'grid-item col-sm-4';
	}

	if(term_exists($cat, 'category') !== NULL) {
		$args['category_name'] = $cat;
	} 

	if(term_exists($tag, 'post_tag') !== NULL) {
		$args['tag'] = $tag;
	} 

	query_posts($args);

	while(have_posts()) : the_post();

		global $post;
	$title = get_the_title();
	$id = get_the_ID();
	$link = get_the_permalink();
	$image_url = get_field('featured_image')['sizes']['thumb-related'];
	$site_url = site_url();
	$tags = get_tags();
	$tags_html = '';
	$this_type = explode('_', get_post_type())[1];

	//print_r($taxonomies);
	if( $page_type == 'all-articles-guides' ){
		if( $post->post_type == 'ee_articles' ){
			$art_terms = get_the_terms( $id, 'articles-categories' );
			$value = get_field('category_icon','articles-categories_' . $art_terms[0]->term_id);
		}else{
			$art_terms = get_the_terms( $id, 'guides-categories' );
			$value = get_field('category_icon','guides-categories_' . $art_terms[0]->term_id);
		}
		$select_cat_icon = $value['url'];
		$select_cat_name = $art_terms[0]->name;
	}elseif( $page_type == 'all-guides' || $page_type == 'show-guides' ){
		$art_terms = get_the_terms( $id, 'guides-categories' );		
		$value = get_field('category_icon','guides-categories_' . $art_terms[0]->term_id);

		
		$select_cat_icon = $value['url'];
		$select_cat_name = $art_terms[0]->name;
	}elseif( $page_type == 'all-articles' || $page_type == 'show-articles' ){
		$art_terms = get_the_terms( $id, 'articles-categories' );		
		$value = get_field('category_icon','articles-categories_' . $art_terms[0]->term_id);

		
		$select_cat_icon = $value['url'];
		$select_cat_name = $art_terms[0]->name;
	}elseif( ! empty( $single_term_obj ) ){
		$value = get_field('category_icon',$single_term_obj->taxonomy. '_' . $single_term_obj->term_id);

		$select_cat_icon = $value['url'];
		$select_cat_name = $single_term_obj->name;
	}else{
		$col_post_cat = get_the_terms( $post->$col_post_id,$taxonomy);
		$value = get_field('category_icon',$taxonomy . '_' . $col_post_cat[0]->term_id);
		
		$select_cat_icon = $value['url'];
		$select_cat_name = $col_post_cat[0]->name;
	}

	// echo '<!-- ';
	// print_r( $single_term_obj );
	// echo "\n";
	// echo '-->';

	
	$url = '';

	if(isset($art_terms[0]->slug) && isset($art_terms[0]->taxonomy)){
		$slug = $art_terms[0]->slug;
		$category_type = $art_terms[0]->taxonomy;
		$url = $site_url.'/'.$category_type.'/'.$slug;
	}

	if($id == $stopat) {
		the_post();
	}

	else {
		foreach($tags as $tag) {
			$tags_html .= <<<EOT
			<li><a href="#"">{$tag->name}</a></li>
EOT;
		}
	
		//TODO: Adjust for proper lazy load of images on mobile.
		$this_post_tile = <<<EOT
			<div class="{$grid_classes} {$taxonomy}">
				<article class="post-link" id='{$id}'>
				<a class="post-link__hover lazyload" href="{$link}" data-bg="$image_url">
						<div class="post-link__content">
							<h3 class="post-link__title">{$title}</h3>
						</div>
					</a>
					<footer class="post-link__meta">
						<a class="post-link__type" href="{$site_url}/{$this_type}/">{$this_type}</a>
						<div class="tags tags--toggle">
							 
						</div>
						<ul class="selector__categories">
							<li>
								<a target="_self" href="{$url}">
									<label>
										<img src="{$select_cat_icon}" />
										<span>{$select_cat_name}</span>
									</label>
								</a>
							</li>
						</ul>
					</footer>
<!-- tags menu ->
				</article>
			</div>
EOT;
	
		array_push($ret, $this_post_tile);
	}

	endwhile;

	echo json_encode($ret);

	wp_die();
}

/*
can be replace to <!-- tags menu ->
					<div class="tags__menu">
						<div class="tags__menu--inner">
							<p>Tagged With</p>
							<ul class="tags__list">
								{$tags_html}
							</ul>
							<button class="tags__close" title="Close">
								<svg viewBox="0 0 27 27" width="27" height="27" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/	xlink">
								    <use xlink:href="#icon-close-circle"></use>
								</svg>
							</button>
						</div>
					</div>
*/

add_image_size( 'thumb-related', 615, 300, true );
add_image_size( 'thumb-pinterest', 735, 9999, false );
add_image_size( 'feeds_image_size', 1440, 900, true );


/*

//add support for post thumbnails
add_theme_support('post-thumbnails');
if ( function_exists('add_theme_support') ) {
	add_theme_support('post-thumbnails');
}


//setup other image sizes
add_image_size( 'slider', 1296, 200, true );


// This theme uses wp_nav_menu() in one location.
register_nav_menus( array(
	'main' => __( 'Main Navigation', 'csam' ),
	'footer' => __( 'Footer Links', 'csam' ),
) );



//fire up a sidebar if there is one
if ( function_exists('register_sidebar') )
	register_sidebars(2,array(
		'name' => 'Sidebar %d',
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h4 class="sidebar-heading">',
		'after_title' => '</h4><hr class="inset" />',
));


//REPORTS
function reports_post_type() {
  $labels = array(
	'name' => _x('Reports', 'post type general name'),
	'singular_name' => _x('Report', 'post type singular name'),
	'add_new' => _x('Add New', 'report'),
	'add_new_item' => __('Add New Report'),
	'edit_item' => __('Edit Report'),
	'new_item' => __('New Report'),
	'all_items' => __('All Reports'),
	'view_item' => __('View Report'),
	'search_items' => __('Search Reports'),
	'not_found' =>  __('No reports found'),
	'not_found_in_trash' => __('No reports found in Trash'), 
	'parent_item_colon' => '',
	'menu_name' => 'Reports'

  );
  $args = array(
	'labels' => $labels,
	'public' => true,
	'publicly_queryable' => true,
	'show_ui' => true, 
	'show_in_menu' => true, 
	'query_var' => true,
	'rewrite' => false,
	'capability_type' => 'post',
	'has_archive' => true, 
	'hierarchical' => false,
	'menu_position' => null,
	'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
  ); 
  register_post_type('pas_reports',$args);
}
add_action( 'init', 'reports_post_type' );



// Reports Years
add_action( 'init', 'create_book_taxonomies', 0 );

function create_book_taxonomies() 
{
  $labels = array(
	'name' => _x( 'Year', 'taxonomy general name' ),
	'singular_name' => _x( 'Year', 'taxonomy singular name' ),
	'search_items' =>  __( 'Search Years' ),
	'all_items' => __( 'All Years' ),
	'parent_item' => __( 'Parent Years' ),
	'parent_item_colon' => __( 'Parent Year:' ),
	'edit_item' => __( 'Edit Years' ), 
	'update_item' => __( 'Update Year' ),
	'add_new_item' => __( 'Add New Year' ),
	'new_item_name' => __( 'New Year Name' ),
	'menu_name' => __( 'Years' ),
  ); 	

  register_taxonomy('reports_years',array('pas_reports'), array(
	'hierarchical' => true,
	'labels' => $labels,
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => false
  ));
}


// Change Post label to News
function change_post_menu_label() {
	global $menu;
	global $submenu;
	$menu[5][0] = 'News';
	$submenu['edit.php'][5][0] = 'News';
	$submenu['edit.php'][10][0] = 'Add News';
	$submenu['edit.php'][16][0] = 'News Tags';
	echo '';
}
function change_post_object_label() {
	global $wp_post_types;
	$labels = &$wp_post_types['post']->labels;
	$labels->name = 'News';
	$labels->singular_name = 'News';
	$labels->add_new = 'Add News';
	$labels->add_new_item = 'Add News';
	$labels->edit_item = 'Edit News';
	$labels->new_item = 'News';
	$labels->view_item = 'View News';
	$labels->search_items = 'Search News';
	$labels->not_found = 'No News found';
	$labels->not_found_in_trash = 'No News found in Trash';
}
add_action( 'init', 'change_post_object_label' );
add_action( 'admin_menu', 'change_post_menu_label' );


// Reorder Admin Menu
function custom_menu_order($menu_ord) {
	if (!$menu_ord) return true;
	return array(
		'index.php', // Dashboard
		'edit.php', // News
		//'edit.php?post_type=pas_media',
		'edit.php?post_type=page', // Pages
		'separator1',
		'upload.php', // Media
		'link-manager.php', // Links
		'edit-comments.php', // Comments
		'themes.php', // Appearance
		'plugins.php', // Plugins
		'users.php', // Users
		'tools.php', // Tools
		'options-general.php', // Settings
		'separator2'

	);
}
add_filter('custom_menu_order', 'custom_menu_order');
add_filter('menu_order', 'custom_menu_order');



//Direct Focus in the Admin Footer
function modify_footer_admin () {
  echo 'Created by <a href="http://www.directfocus.com" target="_blank">Direct Focus</a>. Powered by <a href="http://www.wordpress.org" target="_blank">WordPress</a>';
}
add_filter('admin_footer_text', 'modify_footer_admin');


// Options Page
if( function_exists('acf_add_options_sub_page') )
{
	acf_add_options_sub_page(array(
		'title' => 'Footer',
		'capability' => 'manage_options'
	));
}



// Add styles dropdown to TinyMCE for custom styles
add_filter( 'mce_buttons_2', 'tuts_mce_editor_buttons' );
 
function tuts_mce_editor_buttons( $buttons ) {
	 array_unshift( $buttons, 'styleselect' );
	 return $buttons;
}

add_filter( 'tiny_mce_before_init', 'tuts_mce_before_init' );
 
function tuts_mce_before_init( $settings ) {

	$style_formats = array(
		array(
			'title' => 'Button',
			'selector' => 'a',
			'classes' => 'button'
		)
	);

	$settings['style_formats'] = json_encode( $style_formats );

	return $settings;
}




// Delete News Transients on Post Save
function delete_news_trasient() {
	global $post;
	if( $post->post_type == 'post' ) {
		delete_transient( 'news_result' );
	}
}
add_action( 'save_post', 'delete_news_trasient' );




// Wrap video embeds with div for responsiveness
add_filter('embed_oembed_html', 'my_embed_oembed_html', 99, 4);
function my_embed_oembed_html($html, $url, $attr, $post_id) {
	return '<div class="video">' . $html . '</div>';
}



// Remove Gallery Inline Styles
add_filter( 'use_default_gallery_style', '__return_false' );



require_once('inc/shortcode.collapse.php');
require_once('inc/shortcode.course-registration.php');
*/

 add_filter("timber_context", "add_to_context");

    function add_to_context($data) {
        $data['menu'] = new TimberMenu();
        return $data;
    }

function feedPreFilter($query)
{
	// Check if we are requesting a feed.
	if ($query->is_feed)
	{
		// If a Feed do we have the post_type URL parameter (query_string)?
		if (isset($_GET['post_type']))
		{
			$post_types = load_post_types_qs();
			if ($post_types)
			{
				// If the query_string contans the 'post_type' parameter then 
				//set the query post_type to that array 
				$query->set('post_type',$post_types);				
			}
		}
	}	
	return $query;
}
add_filter('pre_get_posts','feedPreFilter');

function load_post_types_qs()
{
	$post_types = explode('-', $_GET['post_type']);
	if (($post_types) || (count($post_types)))
	{
		// If we have the package we want to remove leading and trailing spaces. 
		//Then re-assign back to the array
		foreach($post_types as $idx => $post_type){
			$post_types[$idx] = trim($post_type);
		}
		
		// returning the array to caller
		return $post_types;
	}
}

function dn_add_rss_image() {
	global $post;
	
	$thumbnail_ID = get_post_thumbnail_feature_id( $post );
	$thumbnail    = wp_get_attachment_image_src( $thumbnail_ID, 'full' );
	$content = '<image>
					<url>'.$thumbnail[0].'</url>					
					<width>'.$thumbnail[1].'</width>
					<height>'.$thumbnail[2].'</height>
				</image>';
	
	echo $content;
}


function get_post_thumbnail_feature_id( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	return get_post_meta( $post->ID, 'featured_image', true );
}
add_filter('the_excerpt_rss', 'dn_add_rss_image');


function getDisqusCount($link_article,$link_guid) {
	$shortname = 'eartheasy';
	$APIKey = 'J8pV3VsnoZ5pmTN2qTLgPeTEX4svxM2SkajYhRPVJ4O08zoSsX367vYVxRI0oUQs';

	// $shortname = 'staging-learn-eartheasy-com';
	// $APIKey = 'RLEtdkbwjrbHa68ybGqosHwYZWLAzufK3XyiySKFNKrDGZhec3LXxK66mPBLSVyb';
	
	$count = 0;

	$link_article_feed_url = "https://disqus.com/api/3.0/threads/details.json?forum=" . $shortname . "&api_key=" . $APIKey . "&thread:link=" . $link_article;
	$link_article_source = wp_remote_get( $link_article_feed_url );

	if( $link_article_source['response']['code'] == 200 ){

		$link_article_result = json_decode( $link_article_source['body'] );
		$count = $link_article_result->response->posts;

	}elseif( $link_article_source['response']['code'] == 400 ){

		$link_guid_feed_url = "https://disqus.com/api/3.0/threads/details.json?forum=" . $shortname . "&api_key=" . $APIKey . "&thread:link=" . urlencode( $link_guid );
		$link_guid_source = wp_remote_get( $link_guid_feed_url );

		$link_guid_result = json_decode( $link_guid_source['body'] );
		$count = $link_guid_result->response->posts;
	}
    return $count;
}

// $disqus_feed = getDisqusCount('https://learn.eartheasy.com/?post_type=ee_articles&p=14498');
// print_r( $disqus_feed );
?>
