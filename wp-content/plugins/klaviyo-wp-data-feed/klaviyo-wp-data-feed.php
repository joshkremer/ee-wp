<?php
/**
* Plugin Name: Klaviyo + WP Data Feed
* Plugin URI: https://joshkremer.com/
* Description: Create a json data feed from Wordpress for inclusion in Klaviyo templates and campaigns.
* Version: 0.8
* Author: Josh Kremer
* Author URI: https://joshkremer.com/
**/

require( "data.php" );

function klaviyo_wp_data_feed(){
    $current_dir = __DIR__;
    $json_filename = $current_dir . "/json/ee_articles_and_guides.json";
    $data_php_path = __DIR__ . "/data.php";
    $loading_gif_image_path =  get_home_url() . "/wp-content/plugins/klaviyo-wp-data-feed/spinner.gif";

        if (file_exists($json_filename)) {
        }
        else {
            var_dump(file_exists($json_filename));
            build_master_json_file();
        }

    $json_data = load_json_from_file("ee_articles_and_guides");

    function input_form($json_data, $data_php_path, $loading_gif_image_path){

        echo '<div>';
        echo '<h2>Klaviyo + WP Data Feed</h2>';
        echo '<h4>Feeds automatically rebuilt every 6 hours</h4>';
        echo '<table>';
        echo '<tr>';
        echo '<button id="rebuild_json_file" data_php_path="' . $data_php_path. '">Rebuild Feeds</button>';
        echo '</tr>';
        echo '<tr>';
        echo '<div id="message"></div>';
        echo '</tr>';
        echo '<tr>';
        echo '<div id="loading"></div>';
        echo '</tr>';
        echo '</table>';
        echo '&nbsp';

        $url_endpoints = [];
        foreach ($json_data as $post_category){
            $post_category = $post_category["post_category"];
            $endpoint_string = str_replace("&", "-", $post_category);
            $endpoint_string= str_replace("_amp", "-", $endpoint_string);
            $endpoint_string= str_replace(";", "", $endpoint_string);
            $endpoint_string= str_replace(" ", "", $endpoint_string);
            $endpoint_string= str_replace("amp", "", $endpoint_string);
            $endpoint_string = strtolower($endpoint_string);
            array_push($url_endpoints, $endpoint_string);
        };
        $url_endpoints = array_unique($url_endpoints);

        foreach ($url_endpoints as $ue){
            $home_url = get_home_url();
            $url_prefix = '<a href="';
            $url = $home_url . '/wp-json/wp/v2/ee-articles-and-guides-klaviyo?category=' . $ue;
            $url_suffix = '"> ' . $url . '</a>';
            $url = $url_prefix . $url . $url_suffix;
            echo '<br>';
            echo $url;
            echo '<br>';
        }
        echo '</table>';
        echo '<br>';
        echo '</div>';

        $article_type = get_option("klaviyo_wp_data_feed_option_name");
        return $article_type;
    }

    input_form($json_data, $data_php_path, $loading_gif_image_path);
}