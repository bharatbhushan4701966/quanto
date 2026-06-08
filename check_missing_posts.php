<?php
require 'c:/Users/HI/Downloads/wordpress/wp-load.php';

echo "Fetching total posts from live...\n";
$response = wp_remote_head( 'https://cmrindia.com/wp-json/wp/v2/posts' );
$total_posts = wp_remote_retrieve_header( $response, 'X-WP-Total' );
$total_pages = wp_remote_retrieve_header( $response, 'X-WP-TotalPages' );

echo "Live site has $total_posts posts across $total_pages pages.\n";

$live_slugs = [];
for ( $page = 1; $page <= $total_pages; $page++ ) {
    echo "Fetching page $page...\n";
    $res = wp_remote_get( "https://cmrindia.com/wp-json/wp/v2/posts?_fields=slug&per_page=100&page=$page", array('timeout' => 60) );
    if ( is_wp_error( $res ) ) {
        echo "Error on page $page: " . $res->get_error_message() . "\n";
        continue;
    }
    $body = wp_remote_retrieve_body( $res );
    $posts = json_decode( $body );
    if ( ! empty( $posts ) ) {
        foreach ( $posts as $p ) {
            $live_slugs[] = $p->slug;
        }
    }
}

echo "Fetched " . count($live_slugs) . " slugs from live.\n";

global $wpdb;
$local_slugs = $wpdb->get_col( "SELECT post_name FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish'" );
echo "Local site has " . count($local_slugs) . " published posts.\n";

$missing_slugs = array_diff( $live_slugs, $local_slugs );
echo "Missing " . count($missing_slugs) . " posts.\n";

if ( !empty($missing_slugs) ) {
    file_put_contents('missing_slugs.json', json_encode(array_values($missing_slugs)));
    echo "Saved missing slugs to missing_slugs.json\n";
    foreach(array_slice($missing_slugs, 0, 20) as $slug) {
        echo "- $slug\n";
    }
}
