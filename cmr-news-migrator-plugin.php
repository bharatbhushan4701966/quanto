<?php
/**
 * Plugin Name: CMR News Migrator
 * Description: Deletes old Media Release posts from cmr_news and re-migrates them from the regular Posts section.
 * Version: 1.0
 * Author: Antigravity
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add a menu page under Tools
add_action('admin_menu', 'cmr_news_migrator_menu');

function cmr_news_migrator_menu() {
    add_management_page(
        'CMR News Migrator',
        'CMR News Migrator',
        'manage_options',
        'cmr-news-migrator',
        'cmr_news_migrator_page'
    );
}

function cmr_news_migrator_page() {
    // Check if the user has permissions
    if (!current_user_can('manage_options')) {
        return;
    }

    echo '<div class="wrap">';
    echo '<h1>CMR News Migrator</h1>';

    // Handle form submission
    if (isset($_POST['run_migration']) && check_admin_referer('cmr_news_migrator_action', 'cmr_news_migrator_nonce')) {
        echo '<div class="notice notice-success is-dismissible"><p><strong>Migration Started!</strong></p></div>';
        
        // 1. Delete existing 'Media Release' posts in 'cmr_news'
        $terms_to_delete = array('Media Release', 'Media Releases');
        $deleted = 0;

        foreach ($terms_to_delete as $term_name) {
            $term = term_exists($term_name, 'cmr_news_category');
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
        }
        
        echo "<p>Deleted <strong>" . $deleted . "</strong> old Media Release posts from cmr_news.</p>";

        // 2. Migrate from regular posts to 'cmr_news'
        $args = array(
            'post_type' => 'post',
            'category_name' => 'press-releases,media-release,media-releases', // The slug for Press/Media Releases
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

        echo "<p>Migrated <strong>" . $migrated . "</strong> new Media Release posts.</p>";
        echo "<ul style='list-style-type:disc; padding-left: 20px;'>";
        foreach($log as $msg) {
            echo "<li>" . $msg . "</li>";
        }
        echo "</ul>";

    } else {
        echo '<p>Click the button below to delete all old Media Release posts from CMR News and re-migrate the updated posts from your blog.</p>';
        echo '<form method="post" action="">';
        wp_nonce_field('cmr_news_migrator_action', 'cmr_news_migrator_nonce');
        submit_button('Run Migration Now', 'primary', 'run_migration');
        echo '</form>';
    }

    echo '</div>';
}
