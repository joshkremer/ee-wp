<?php
$wp_content_dir = ABSPATH;
$wp_load_filename = $wp_content_dir . "wp-load.php";

require_once($wp_load_filename);

//Add settings link to plugin
function settings_link($links) {
    $settings_link = '<a href="tools.php?page=klaviyo-wp-data-feed">Settings</a>';

    array_unshift($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'settings_link' );

function klaviyo_wp_data_feed_register_settings() {
    add_option( 'klaviyo_wp_data_feed_option_name', 'This is my option value.');
    register_setting( 'klaviyo_wp_data_feed_options_group', 'klaviyo_wp_data_feed_option_name', 'klaviyo_wp_data_feed_callback' );
}
add_action( 'admin_init', 'klaviyo_wp_data_feed_register_settings' );

function klaviyo_wp_data_feed_register_options_page() {
    add_options_page('Page Title', 'Plugin Menu', 'manage_options', 'klaviyo_wp_data_feed', 'klaviyo_wp_data_feed_options_page');
}

add_action('admin_menu', 'klaviyo_wp_data_feed_register_options_page');


//Create entry in Tools menu
add_action('admin_menu', 'add_submenu_item');
function add_submenu_item(){
    add_submenu_page( 'tools.php', 'Klaviyo + WP Data Feed', 'Kaviyo + WP Data Feed', 'manage_options', 'klaviyo-wp-data-feed', 'klaviyo_wp_data_feed');
}

// add to WP Cron
function kdf4wp_custom_cron_schedule( $schedules ) {
    $schedules['every_six_hours'] = array(
        'interval' => 21600,
        'display'  => __( 'Every 6 hours' ),
    );
    return $schedules;
}

add_filter( 'cron_schedules', 'kdf4wp_custom_cron_schedule' );

if ( ! wp_next_scheduled( 'kdf4wp_cron_hook' ) ) {
    wp_schedule_event( time(), 'every_six_hours', 'kdf4wp_cron_hook' );
}

add_action( 'kdf4wp_cron_hook', 'kdf4wp_cron_function' );

function kdf4wp_cron_function() {
    build_master_json_file();
}

function add_css_and_js_files(){
    $js_filename = plugin_dir_url( __FILE__ ) . "/js/kdf4wp.js";
    wp_enqueue_script('kdf4wp', $js_filename, array('jquery'), '1.2.3', true);

    $js_filename = plugin_dir_url( __FILE__ ) . "/js/jquery-ui.js";
    wp_enqueue_script('jquery_ui_js', $js_filename, array('jquery'), '1.2.3', true);

    $css_filename = plugin_dir_url( __FILE__ ) . "/css/jquery-ui.css";
    wp_enqueue_style( 'jquery_ui_css', $css_filename, false, '1.0.0', 'all');

    $css_filename = plugin_dir_url( __FILE__ ) . "/css/style.css";
    wp_enqueue_style( 'style_css', $css_filename, false, '1.0.0', 'all');
}

add_action('admin_enqueue_scripts', "add_css_and_js_files");

// add tags taxonomy
function add_tags_to_articles_and_guides(){

    $labels = array(
    'name' => _x( 'Tags', 'taxonomy general name' ),
    'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Tags' ),
    'popular_items' => __( 'Popular Tags' ),
    'all_items' => __( 'All Tags' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit Tag' ),
    'update_item' => __( 'Update Tag' ),
    'add_new_item' => __( 'Add New Tag' ),
    'new_item_name' => __( 'New Tag Name' ),
    'separate_items_with_commas' => __( 'Separate tags with commas' ),
    'add_or_remove_items' => __( 'Add or remove tags' ),
    'choose_from_most_used' => __( 'Choose from the most used tags' ),
    'menu_name' => __( 'Tags' ),
    );

    $args = array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'tag' ),
    );

    register_taxonomy( 'tag', array( 'ee_articles', 'ee_guides' ), $args );
}

add_action('init', 'add_tags_to_articles_and_guides');

function load_json_from_file($json_filename){
    $current_dir = __DIR__;
    $json_filename = $current_dir . "/json/" . $json_filename . ".json";
    $json_data = file_get_contents($json_filename);
    $json_data = json_decode($json_data, JSON_UNESCAPED_SLASHES);
    return $json_data;
}

function create_json_file($data, $filename){

    $current_dir = __DIR__;
    $json_filename = $current_dir . "/json/" . $filename . ".json";

    if (file_exists($json_filename)){

        $date_time = new DateTime('2011-01-01T15:03:01.012345Z');
        $date_time = $date_time->format('Y-m-dTH_i_s.u');

        $json_backup_filename = $current_dir . "/json/backups/" . $filename . "_" . $date_time . ".json.backup";

        copy ($json_filename, $json_backup_filename);
    };

    $json = json_encode($data, JSON_UNESCAPED_SLASHES);
    $bytes = file_put_contents($json_filename, $json);
}


function create_custom_article_endpoints($json_filename){

    $json_data = load_json_from_file($json_filename);

    function get_endpoint_string($post_category){
        $endpoint_string = str_replace("&", "-", $post_category);
        $endpoint_string= str_replace("_amp", "-", $endpoint_string);
        $endpoint_string= str_replace(";", "", $endpoint_string);
        $endpoint_string= str_replace(" ", "", $endpoint_string);
        $endpoint_string= str_replace("amp", "", $endpoint_string);
        $endpoint_string = strtolower($endpoint_string);
        $ac = $endpoint_string;
        return $endpoint_string;
    }

    function assign_posts_to_categories($json_data){

        $post_categories = [];

        foreach($json_data as $post){


            if (in_array($post["post_category"], $post_categories)){
            }

            else {
                array_push($post_categories, $post["post_category"]);
            }
        }

        $categories = [];

        foreach ($post_categories as $pc){
            $category = array(
                "post_category" => $pc,
                "category_slug" => get_endpoint_string($pc),
                "category_members" => []
            );
            array_push($categories, $category);
        }

        $complete_categories = [];

        foreach ($categories as $cat){
            foreach($json_data as $post){
                if ($post["post_category"] == $cat["post_category"]){
                    array_push($cat["category_members"], $post);
                }
            }
            array_push($complete_categories, $cat);
        }
        return $complete_categories;
    };

    $complete_categories = assign_posts_to_categories($json_data);

    foreach ($complete_categories as $cc){
        create_json_file($cc["category_members"], $cc["category_slug"]);
    }

    return $complete_categories;
}

function list_posts(WP_REST_Request $request){
        $result['code'] = 200;
        $result['message'] = "Horrayyy!!!!!!!";
        $result['data'] = $request->get_params();
        $selected_file = $result['data']['category'];
        $json_data = load_json_from_file($selected_file);
        return $json_data;
};

function build_api(){
    $namespace = 'wp/v2';
    $endpoint_url = 'ee-articles-and-guides-klaviyo';

    register_rest_route($namespace, '/' . $endpoint_url, array(
        'methods' => 'GET',
        'callback' => 'list_posts',
        'permission_callback' => '__return_true'
    ));
}

add_action('rest_api_init', 'build_api');

add_action('wp_ajax_build_master_json_file', 'build_master_json_file');


function build_master_json_file(){

    $message_id = $_REQUEST['message_id'];

    function get_post_by_type($post_type){
        $posts = get_posts([
            'post_type' => $post_type,
            'post_status' => 'publish',
            'numberposts' => -1
            // 'order'    => 'ASC'
        ]);
        return $posts;
    };


    function get_all_custom_posts($post_type, $taxonomy){

        $terms = get_terms('taxonomy=' . $taxonomy . '&post_type=' . $post_type);

        $posts_and_categories = [];

        foreach($terms as $cat){

            $category_member_posts = get_posts(
                array(
                    'posts_per_page' => -1,
                    'post_type' => $post_type,
                    'tax_query' => array(
                        array(
                            'taxonomy' => $taxonomy,
                            'field' => 'term_id',
                            'terms' => $cat->term_id,
                        )
                    )
                )
            );

            $category = array(
                "name" => $cat->name,
                "category_members" => $category_member_posts
            );

            array_push($posts_and_categories, $category);
        }
        return $posts_and_categories;
    }

    $all_articles = get_all_custom_posts("ee_articles", "articles-categories" );
    $all_articles_by_tag = get_all_custom_posts("ee_articles", "tag" );
    $all_guides = get_all_custom_posts("ee_guides", "guides-categories" );
    $all_guides_by_tag = get_all_custom_posts("ee_guides", "tag" );

    function build_posts_containers($posts, $post_type){
        $post_pack = [];
        foreach ($posts as $post_category){
            $category_members = $post_category["category_members"];

            foreach ($category_members as $cm){
                $images = get_attached_media('image', $cm->ID);
                $post_id = $cm->ID;
                $post_images = [];

                foreach ($images as $image){
                    array_push($post_images, wp_get_attachment_image_url($image->ID, array( 1440, 900 )));
                }

                $post = array(
                    "title" => $cm->post_title,
                    "public_url" => get_permalink($cm->ID),
                    "featured_image" => $post_images[0],
                    "additional_images" => $post_images,
                    "post_date" => $cm->post_date_gmt,
                    "post_category" => $post_category["name"],
                    "post_type" => $post_type,
//	                "post_id" => $cm->ID,
//	                "post_meta" => get_post_meta($cm->ID),
	                "post_description" => get_post_meta($cm->ID)["article_1_introduction"][0]
                );
                array_push($post_pack, $post);
            }
        }
        return $post_pack;
    }
    $articles_pack = build_posts_containers($all_articles, "ee_articles");
    $articles_by_tag_pack = build_posts_containers($all_articles_by_tag, "ee_articles");

    $guides_pack = build_posts_containers($all_guides, "ee_guides");
    $guides_by_tag_pack = build_posts_containers($all_guides_by_tag, "ee_guides");
    $final_posts_pack = array_merge($articles_pack, $guides_pack, $articles_by_tag_pack, $guides_by_tag_pack);

    $json_filename = "ee_articles_and_guides";

    create_json_file($final_posts_pack, $json_filename);

    function display_success_message($data){
        var_dump($data);
    }

    wp_send_json_success(["file creation complete", $final_posts_pack]);
    wp_send_json_error(["could not create file"]);
}

$complete_categories = create_custom_article_endpoints("ee_articles_and_guides");
