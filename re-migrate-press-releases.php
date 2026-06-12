<?php
require_once( explode( "wp-content" , __FILE__ )[0] . "wp-load.php" );
// 1. Delete existing 'Media Release' posts in 'cmr_news'
$term = term_exists('Media Release', 'cmr_news_category');
$deleted = 0;

if ($term) {
    $term_id = is_array($term) ? $term['term_id'] : $term;
    $args_delete = array(
        'post_type' => 'cmr_news',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'cmr_news_category',
                'field'    => 'term_id',
                'terms'    => $term_id,
            ),
        ),
    );
    
    $query_delete = new WP_Query($args_delete);
    if ($query_delete->have_posts()) {
        while ($query_delete->have_posts()) {
            $query_delete->the_post();
            wp_delete_post(get_the_ID(), true); // true to bypass trash and force delete
            $deleted++;
        }
    }
    wp_reset_postdata();
}

echo "Deleted " . $deleted . " old Media Release posts from cmr_news.<br/>\n";

// 2. Migrate from regular posts to 'cmr_news'
$args = array(
    'post_type' => 'post',
    'category_name' => 'media-release', // The slug for Media Release
    'posts_per_page' => -1,
);
$query = new WP_Query($args);

$migrated = 0;
$log = array();

if ($query->have_posts()) {
    // Ensure Media Release category exists in cmr_news_category
    $term = term_exists('Media Release', 'cmr_news_category');
    if (!$term) {
        $term = wp_insert_term('Media Release', 'cmr_news_category');
    }
    $term_id = is_array($term) ? $term['term_id'] : $term;
    
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $title = get_the_title();
        
        // Duplicate the post
        $new_post = array(
            'post_title' => $title,
            'post_content' => get_post_field('post_content', $post_id),
            'post_excerpt' => get_post_field('post_excerpt', $post_id),
            'post_status' => 'publish',
            'post_type' => 'cmr_news',
            'post_date' => get_post_field('post_date', $post_id),
            'post_author' => get_post_field('post_author', $post_id),
        );
        
        $new_post_id = wp_insert_post($new_post);
        
        if (!is_wp_error($new_post_id)) {
            // Assign taxonomy term
            wp_set_object_terms($new_post_id, (int)$term_id, 'cmr_news_category');
            
            // Copy featured image
            $thumb_id = get_post_thumbnail_id($post_id);
            if ($thumb_id) {
                set_post_thumbnail($new_post_id, $thumb_id);
            }
            
            $migrated++;
            $log[] = "Migrated successfully: " . $title;
        } else {
            $log[] = "Error migrating: " . $title . " - " . $new_post_id->get_error_message();
        }
    }
    wp_reset_postdata();
}

echo "Migrated " . $migrated . " posts.<br/>\n";
foreach($log as $msg) {
    echo $msg . "<br/>\n";
}
