<?php
require_once 'wp-load.php';

$args = array(
    'post_type'      => 'post',
    'category_name'  => 'smb-connect',
    'posts_per_page' => 10,
    'orderby'        => 'date',
    'order'          => 'DESC'
);
$query = new WP_Query( $args );

foreach ( $query->posts as $post ) {
    echo $post->ID . " - " . $post->post_title . "\n";
}
