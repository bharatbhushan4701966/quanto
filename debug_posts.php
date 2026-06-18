<?php
require_once('wp-load.php');
$query_args = array(
    'post_type'      => array( 'post', 'cmr_news' ),
    'posts_per_page' => 15,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'tax_query'      => array(
        array(
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => 'industry-connect',
        ),
    ),
);
$q = new WP_Query($query_args);
foreach($q->posts as $p) {
    echo $p->ID . " | " . $p->post_type . " | " . $p->post_title . "\n";
}
?>
