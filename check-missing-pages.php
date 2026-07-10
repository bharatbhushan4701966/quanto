<?php
require_once('../../../wp-load.php');

if (!current_user_can('manage_options') && !isset($_GET['force'])) {
    die('Not allowed');
}

$api_base = 'https://cmrindia.com/wp-json/wp/v2';

echo "<h1>Checking for missing pages...</h1>";
echo "<ul>";

$missing_count = 0;
$page = 1;

while (true) {
    $response = wp_remote_get($api_base . "/pages?per_page=100&page=$page", array('timeout' => 60));
    if (is_wp_error($response)) break;
    
    $posts = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($posts) || isset($posts['code'])) break;
    
    global $wpdb;
    
    foreach ($posts as $post) {
        $slug = $post['slug'];
        $existing_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = 'post' LIMIT 1", $slug));
        
        if (!$existing_id) {
            $missing_count++;
            echo "<li>Missing: <a href='{$post['link']}' target='_blank'>{$post['title']['rendered']}</a> (Slug: $slug)</li>";
        }
    }
    
    $page++;
}

echo "</ul>";
echo "<p>Total missing: $missing_count</p>";
