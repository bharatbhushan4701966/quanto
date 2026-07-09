<?php
require_once('../../../wp-load.php');
global $wpdb;
$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type='post'");
echo "Total posts in DB (any status): $count <br>";
$statuses = $wpdb->get_results("SELECT post_status, COUNT(*) as c FROM $wpdb->posts WHERE post_type='post' GROUP BY post_status");
print_r($statuses);
?>
