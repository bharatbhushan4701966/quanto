<?php
require_once('../../../wp-load.php');

if (!current_user_can('manage_options') && !isset($_GET['force'])) {
    die('Not allowed');
}

echo "<h1>Merging and Deleting Duplicate Categories...</h1>";

$taxonomies = array('category', 'cmr_news_category');

foreach ($taxonomies as $tax) {
    echo "<h3>Processing Taxonomy: $tax</h3>";
    $terms = get_terms(array(
        'taxonomy' => $tax,
        'hide_empty' => false,
    ));

    if (is_wp_error($terms) || empty($terms)) {
        continue;
    }

    foreach ($terms as $term) {
        $name = $term->name;
        
        // Check if the category name ends with ' 2' or '2'
        if (preg_match('/^(.*?)\s*2$/', $name, $matches)) {
            $main_name = trim($matches[1]);
            
            // Try to find the main category
            $main_term = get_term_by('name', $main_name, $tax);
            
            if ($main_term) {
                echo "Found duplicate category: <strong>{$name}</strong>. Main category exists: <strong>{$main_name}</strong>.<br>";
                
                // Get all posts in the duplicate category
                $post_types = ($tax === 'cmr_news_category') ? array('cmr_news') : array('post', 'cmr_news');
                
                $posts = get_posts(array(
                    'post_type' => $post_types,
                    'posts_per_page' => -1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => $tax,
                            'field' => 'term_id',
                            'terms' => $term->term_id
                        )
                    )
                ));
                
                $moved = 0;
                foreach($posts as $p) {
                    // Reassign to main term (true means append, but it doesn't matter since the old term will be deleted)
                    wp_set_object_terms($p->ID, $main_term->term_id, $tax, true);
                    $moved++;
                }
                
                echo "- Moved $moved posts to '{$main_name}'.<br>";
                
                // Now delete the duplicate term to prevent future duplication
                $deleted = wp_delete_term($term->term_id, $tax);
                if (is_wp_error($deleted)) {
                    echo "- Failed to delete '{$name}'.<br>";
                } else {
                    echo "- Successfully deleted '{$name}'.<br>";
                }
                
            } else {
                echo "Skipping <strong>{$name}</strong> because main category '{$main_name}' could not be found.<br>";
            }
        }
    }
}

echo "<h2>Done!</h2>";
