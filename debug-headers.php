<?php
require_once('../../../wp-load.php');
$headers = get_posts(['post_type' => 'quanto_header', 'posts_per_page' => -1]);
foreach($headers as $h) {
    echo "ID: " . $h->ID . " Slug: " . $h->post_name . " Title: " . $h->post_title . "\n";
}
