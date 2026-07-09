<?php
require_once('../../../wp-load.php');
global $wpdb;

// Count all posts by status
$statuses = $wpdb->get_results("SELECT post_status, COUNT(*) as c FROM $wpdb->posts WHERE post_type='post' GROUP BY post_status");
$output = "Post Counts by Status:<br>";
foreach ($statuses as $status) {
    $output .= "{$status->post_status}: {$status->c}<br>";
}
echo $output;
?>
