<?php
// Load WordPress environment
require_once('../../../wp-load.php');

if (!current_user_can('administrator')) {
    die('You must be logged in as an administrator to run this script.');
}

global $wpdb;

echo "<h2>Starting Domain Replacement for Products</h2>";

$old_url = 'https://cmrindia.com';
$old_url2 = 'http://cmrindia.com';
$new_url = 'https://qai8358l95-staging.onrocket.site';
$new_url_noslash = rtrim($new_url, '/'); // Ensure no trailing slash issues

echo "<p>Replacing: <strong>$old_url</strong> and <strong>$old_url2</strong><br>";
echo "With: <strong>$new_url_noslash</strong></p>";

// 1. Update post_content for Products only
$posts_updated = $wpdb->query( $wpdb->prepare(
    "UPDATE {$wpdb->posts} SET post_content = REPLACE(REPLACE(post_content, %s, %s), %s, %s) WHERE post_type = 'product'",
    $old_url, $new_url_noslash,
    $old_url2, $new_url_noslash
) );
echo "Updated $posts_updated WooCommerce product descriptions.<br>";

// 2. Safely Update WooCommerce Downloadable Files (Serialization-aware)
// Get all products that have downloadable files
$downloadable_files = $wpdb->get_results("
    SELECT pm.post_id, pm.meta_value 
    FROM {$wpdb->postmeta} pm
    JOIN {$wpdb->posts} p ON p.ID = pm.post_id
    WHERE p.post_type = 'product' 
    AND pm.meta_key = '_downloadable_files'
");

$count = 0;
foreach ($downloadable_files as $row) {
    // Safely unserialize the WooCommerce file data
    $data = maybe_unserialize($row->meta_value);
    
    if (is_array($data)) {
        $updated = false;
        foreach ($data as $hash => $file) {
            if (strpos($file['file'], $old_url) !== false) {
                $data[$hash]['file'] = str_replace($old_url, $new_url_noslash, $file['file']);
                $updated = true;
            } elseif (strpos($file['file'], $old_url2) !== false) {
                $data[$hash]['file'] = str_replace($old_url2, $new_url_noslash, $file['file']);
                $updated = true;
            }
        }
        
        // If we found the old URL and changed it, save the new correctly serialized array back to the database!
        if ($updated) {
            update_post_meta($row->post_id, '_downloadable_files', $data);
            $count++;
        }
    }
}
echo "Safely updated URLs in $count WooCommerce downloadable files without breaking serialization!<br>";

echo "<br><strong>Done!</strong> Check your products to verify.";
?>
