<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

$args = array(
    'post_type' => 'post',
    'category_name' => 'press-releases', // The slug for Press Releases
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
        
        // Check if it's already migrated
        $existing = get_posts(array(
            'post_type' => 'cmr_news',
            'title' => $title,
            'posts_per_page' => 1,
        ));
        
        if (!empty($existing)) {
            $log[] = "Skipped (Already migrated): " . $title;
            continue;
        }
        
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

echo "Migrated " . $migrated . " posts.<br/>";
foreach($log as $msg) {
    echo $msg . "<br/>";
}
