<?php
// Load WordPress environment
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('administrator')) {
    die('You must be logged in as an administrator to run this script.');
}

global $wpdb;

$old_string = 'wp-content/uploads/YOUR_FOLDER';
$new_string = 'wp-content/uploads/report';

// Also handle relative URLs if they existed
$old_string_relative = '"/wp-content/uploads/YOUR_FOLDER';
$new_string_relative = '"/wp-content/uploads/report';

echo "<h2>Starting Database Search & Replace</h2>";
echo "Replacing: <strong>" . esc_html($old_string) . "</strong> <br>With: <strong>" . esc_html($new_string) . "</strong><br><br>";

// 1. Replace in post_content (Absolute URLs)
$posts_updated = $wpdb->query( $wpdb->prepare(
    "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)",
    $old_string, $new_string
) );
// 1b. Replace in post_content (Relative URLs)
$posts_updated += $wpdb->query( $wpdb->prepare(
    "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)",
    $old_string_relative, $new_string_relative
) );
echo "Updated $posts_updated rows in post_content.<br>";

// 2. Replace in postmeta (Absolute URLs)
$meta_updated = $wpdb->query( $wpdb->prepare(
    "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s",
    $old_string, $new_string, '%' . $wpdb->esc_like($old_string) . '%'
) );
// 2b. Replace in postmeta (Relative URLs)
$meta_updated += $wpdb->query( $wpdb->prepare(
    "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s",
    $old_string_relative, $new_string_relative, '%' . $wpdb->esc_like($old_string_relative) . '%'
) );
echo "Updated $meta_updated rows in postmeta.<br>";

echo "<br><strong>Done!</strong> Please clear your Elementor cache and check the URLs.";
?>
