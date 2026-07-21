<?php
/**
 * Script to import specific policy pages from cmrindia.com
 */

// Load WordPress environment
require_once('../../../wp-load.php');

// Simple security check (only allow if ?force=1 is passed or user is admin)
if (!current_user_can('manage_options') && !isset($_GET['force'])) {
    die('Not allowed. Please append ?force=1 to the URL to run this script.');
}

$source_domain = 'https://cmrindia.com';
$api_base = $source_domain . '/wp-json/wp/v2';

$pages_to_import = [
    'terms-of-use',
    'privacy-policy',
    'cookie-policy'
];

echo "<h2>Importing Policy Pages</h2>";

foreach ($pages_to_import as $slug) {
    echo "Processing slug: <strong>$slug</strong>...<br>";
    
    // Check if page already exists on this site
    $existing = get_page_by_path($slug, OBJECT, 'page');
    if ($existing) {
        echo "<span style='color:orange;'>Page '$slug' already exists. Skipping.</span><br><br>";
        continue;
    }
    
    // Fetch from CMR India API
    $api_url = $api_base . '/pages?slug=' . $slug;
    $response = wp_remote_get($api_url);
    
    if (is_wp_error($response)) {
        echo "<span style='color:red;'>Failed to fetch $slug: " . $response->get_error_message() . "</span><br><br>";
        continue;
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (empty($data) || !is_array($data) || !isset($data[0])) {
        echo "<span style='color:red;'>Page $slug not found on source site.</span><br><br>";
        continue;
    }
    
    $page_data = $data[0];
    
    $title = $page_data['title']['rendered'] ?? '';
    $content = $page_data['content']['rendered'] ?? '';
    
    // Basic cleanup of source URLs in content if needed
    $content = str_replace('https://cmrindia.com/', site_url('/'), $content);
    
    $new_page = array(
        'post_type'    => 'page',
        'post_title'   => wp_strip_all_tags($title),
        'post_content' => $content,
        'post_status'  => 'publish',
        'post_name'    => $slug,
        'post_author'  => 1
    );
    
    $inserted_id = wp_insert_post($new_page);
    
    if (is_wp_error($inserted_id)) {
        echo "<span style='color:red;'>Failed to insert $slug: " . $inserted_id->get_error_message() . "</span><br><br>";
    } else {
        echo "<span style='color:green;'>Successfully created page: $title (ID: $inserted_id)</span><br><br>";
    }
}

echo "<h3>Import Complete!</h3>";
?>
