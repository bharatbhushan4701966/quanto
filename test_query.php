require 'wp-load.php';
$args = array(
    'post_type' => 'cmr_news',
    'posts_per_page' => 5,
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'cmr_news_category',
            'field' => 'slug',
            'terms' => 'cmr-in-news'
        )
    )
);
$q = new WP_Query($args);
foreach($q->posts as $p) {
    echo $p->post_title . \"\n\";
    $terms = wp_get_post_terms($p->ID, 'cmr_news_category');
    foreach($terms as $t) {
        echo \" - \" . $t->name . \" (\" . $t->slug . \")\n\";
    }
}
