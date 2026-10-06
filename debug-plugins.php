<?php
require_once('../../../wp-load.php');

echo "<pre>";
$log_file = WP_CONTENT_DIR . '/debug.log';
if (file_exists($log_file)) {
    $lines = file($log_file);
    echo "=== LAST 50 LINES OF DEBUG.LOG ===\n";
    echo implode("", array_slice($lines, -50));
} else {
    echo "debug.log does not exist at " . $log_file . "\n";
}

echo "\n=== POST 52631 INFO ===\n";
$post = get_post(52631);
if ($post) {
    echo "Title: " . $post->post_title . "\n";
    echo "Type: " . $post->post_type . "\n";
    echo "Status: " . $post->post_status . "\n";
} else {
    echo "Post 52631 not found\n";
}

echo "</pre>";
