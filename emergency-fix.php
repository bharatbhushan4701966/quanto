<?php
// Load WordPress environment
require_once('../../../wp-load.php');

if (!current_user_can('administrator')) {
    die('You must be logged in as an administrator to run this script.');
}

global $wpdb;

echo "<h2>Starting Emergency Database Fix</h2>";

$fixes = array(
    // 1. Revert the "report/" mistake back to the original to restore string lengths and un-corrupt serialized data
    'qai8358l95-staging.onrocket.site/report/' => 'qai8358l95-staging.onrocket.site/wp-content/uploads/YOUR_FOLDER/',
    '\"/report/' => '\"/wp-content/uploads/YOUR_FOLDER/',
    
    // 2. Revert the "wp-content/uploads/report/" mistake (if applied)
    'qai8358l95-staging.onrocket.site/wp-content/uploads/report/' => 'qai8358l95-staging.onrocket.site/wp-content/uploads/YOUR_FOLDER/',
    '\"/wp-content/uploads/report/' => '\"/wp-content/uploads/YOUR_FOLDER/'
);

foreach ($fixes as $bad => $good) {
    echo "Reverting: <strong>" . esc_html($bad) . "</strong> <br>To: <strong>" . esc_html($good) . "</strong><br>";
    
    $posts_updated = $wpdb->query( $wpdb->prepare(
        "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)",
        $bad, $good
    ) );
    
    $meta_updated = $wpdb->query( $wpdb->prepare(
        "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s",
        $bad, $good, '%' . $wpdb->esc_like($bad) . '%'
    ) );
    
    echo "- Updated $posts_updated posts and $meta_updated meta rows.<br><br>";
}

// NOW, properly apply the change they originally wanted safely using PHP serialization-aware methods.
echo "<h2>Applying correct 'report' folder replacement</h2>";
$original = 'wp-content/uploads/YOUR_FOLDER';
$correct = 'wp-content/uploads/report';

// Fix post contents (not serialized, safe to replace directly)
$posts_updated = $wpdb->query( $wpdb->prepare(
    "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)",
    $original, $correct
) );
echo "Updated $posts_updated posts with correct URL.<br>";

// Fix downloadable files meta (Serialization-aware)
$downloadable_files = $wpdb->get_results("SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_downloadable_files' AND meta_value LIKE '%$original%'");
$count = 0;
foreach ($downloadable_files as $row) {
    $data = maybe_unserialize($row->meta_value);
    if (is_array($data)) {
        $updated = false;
        foreach ($data as $hash => $file) {
            if (strpos($file['file'], $original) !== false) {
                $data[$hash]['file'] = str_replace($original, $correct, $file['file']);
                $updated = true;
            }
        }
        if ($updated) {
            update_post_meta($row->post_id, '_downloadable_files', $data);
            $count++;
        }
    }
}
echo "Correctly updated $count WooCommerce downloadable files using safe serialization.<br>";

echo "<br><strong>Done!</strong> Please refresh your WooCommerce product page and check the files.";
?>
