<?php
require_once('../../../wp-load.php');

if (!current_user_can('manage_options') && !isset($_GET['force'])) {
    die('Not allowed');
}

$paged = isset($_GET['paged']) ? (int)$_GET['paged'] : 1;
$posts_per_page = 50;

// Find posts that belong to the "Press Releases" category
$args = array(
    'post_type'      => 'post',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'tax_query'      => array(
        array(
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => array('media-release', 'media-releases', 'press-release', 'press-releases', 'pressreleases'),
        ),
    ),
);
$posts = get_posts($args);

if (empty($posts)) {
    echo "<h1>Done! All Press Releases converted to CMR News.</h1>";
    exit;
}

echo "<h1>Converting Page $paged...</h1>";

$updated_count = 0;
foreach ($posts as $p) {
    // 1. Change post type to cmr_news
    wp_update_post(array(
        'ID'        => $p->ID,
        'post_type' => 'cmr_news',
    ));
    
    // 2. Set cmr_news_category to "media-releases" or "press-releases"
    wp_set_object_terms($p->ID, 'press-releases', 'cmr_news_category');
    
    // 3. Remove standard categories (optional, but good for cleanup)
    wp_set_post_categories($p->ID, array());

    $updated_count++;
    echo "Converted: " . $p->post_title . "<br>";
}

echo "<br>Finished page $paged. Converted $updated_count posts.<br>";
$next_page = $paged + 1;
echo "<script>setTimeout(function() { window.location.href = '?force=1&paged=$next_page'; }, 1000);</script>";
