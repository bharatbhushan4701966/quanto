<?php
require_once('../../../wp-load.php');

if (!current_user_can('manage_options') && !isset($_GET['force'])) {
    die('Not allowed');
}

echo "<h1>Merging Categories...</h1>";

// 1. Merge standard 'category' taxonomy: 'Press Releases 2' -> 'Press Releases'
$term_2 = get_term_by('name', 'Press Releases 2', 'category');
if (!$term_2) $term_2 = get_term_by('name', 'Press Release 2', 'category');

$term_main = get_term_by('name', 'Press Releases', 'category');
if (!$term_main) $term_main = get_term_by('name', 'Press Release', 'category');

if ($term_2 && $term_main) {
    $posts = get_posts(array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $term_2->term_id
            )
        )
    ));
    
    echo "Found " . count($posts) . " standard posts in Press Releases 2.<br>";
    foreach($posts as $p) {
        wp_set_object_terms($p->ID, $term_main->term_id, 'category', true);
        echo "Merged post: " . $p->post_title . "<br>";
    }
} else {
    echo "Could not find standard category terms.<br>";
}

// 2. Merge 'cmr_news_category' taxonomy: 'press-releases-2' -> 'press-releases'
$news_term_2 = get_term_by('slug', 'press-releases-2', 'cmr_news_category');
if (!$news_term_2) $news_term_2 = get_term_by('slug', 'press-release-2', 'cmr_news_category');

$news_term_main = get_term_by('slug', 'press-releases', 'cmr_news_category');
if (!$news_term_main) $news_term_main = get_term_by('slug', 'press-release', 'cmr_news_category');

if ($news_term_2 && $news_term_main) {
    $news_posts = get_posts(array(
        'post_type' => 'cmr_news',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'cmr_news_category',
                'field' => 'term_id',
                'terms' => $news_term_2->term_id
            )
        )
    ));
    
    echo "Found " . count($news_posts) . " cmr_news posts in press-releases-2.<br>";
    foreach($news_posts as $p) {
        wp_set_object_terms($p->ID, $news_term_main->term_id, 'cmr_news_category', true);
        echo "Merged cmr_news post: " . $p->post_title . "<br>";
    }
} else {
    echo "Could not find cmr_news_category terms.<br>";
}

echo "<h2>Done!</h2>";
