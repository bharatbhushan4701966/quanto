<?php
// Load WordPress environment
require_once('../../../wp-load.php');

if (!current_user_can('administrator')) {
    die('You must be logged in as an administrator to run this script.');
}

global $wpdb;

echo "<h2>Starting Final URL Structure Fix</h2>";
echo "<p>Target structure: <strong>https://qai8358l95-staging.onrocket.site/report/filename.pdf</strong></p>";

$targets = array(
    'wp-content/uploads/report',      // If emergency-fix was run
    'wp-content/uploads/YOUR_FOLDER'  // Original just in case
);
$correct = 'report';

foreach ($targets as $original) {
    echo "<h3>Replacing '$original' with '$correct'</h3>";
    
    // 1. Fix standard post content (safe for raw SQL replacement)
    $posts_updated = $wpdb->query( $wpdb->prepare(
        "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)",
        $original, $correct
    ) );
    echo "Updated $posts_updated standard posts.<br>";

    // 2. Fix WooCommerce downloadable files (Must use serialization-aware method!)
    $downloadable_files = $wpdb->get_results("SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_downloadable_files' AND meta_value LIKE '%$original%'");
    $count = 0;
    foreach ($downloadable_files as $row) {
        // Unserialize the corrupted or uncorrupted data
        $data = maybe_unserialize($row->meta_value);
        
        // If it's corrupted (unserialize failed), we must repair it manually first
        if (!is_array($data) && strpos($row->meta_value, 'a:') === 0) {
            // Repair the string length corruption for this specific meta row before proceeding
            $repaired_string = str_replace($correct, $original, $row->meta_value); // Revert the raw text
            $data = maybe_unserialize($repaired_string); // Now it should unserialize!
        }

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
}

echo "<br><strong>Done!</strong> Please refresh your WooCommerce product page and check the files.";
?>
