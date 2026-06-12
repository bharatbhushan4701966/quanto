<?php
require_once( explode( "wp-content" , __FILE__ )[0] . "wp-load.php" );
$args = array(
    'post_type' => 'cmr_news',
    'posts_per_page' => -1,
);
$q = new WP_Query($args);
echo "Total cmr_news posts: " . $q->found_posts . "<br/>";
while($q->have_posts()){
    $q->the_post();
    $terms = wp_get_post_terms(get_the_ID(), 'cmr_news_category');
    $term_names = array();
    foreach($terms as $t){ $term_names[] = $t->name; }
    echo get_the_title() . " - " . implode(", ", $term_names) . "<br/>";
}
wp_reset_postdata();

echo "<hr/>";

$args2 = array(
    'post_type' => 'post',
    'posts_per_page' => -1,
    'category_name' => 'press-releases'
);
$q2 = new WP_Query($args2);
echo "Total post -> press-releases: " . $q2->found_posts . "<br/>";

$args3 = array(
    'post_type' => 'post',
    'posts_per_page' => -1,
    'category_name' => 'media-release'
);
$q3 = new WP_Query($args3);
echo "Total post -> media-release: " . $q3->found_posts . "<br/>";
