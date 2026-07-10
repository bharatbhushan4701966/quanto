<?php
/**
 * CMR Press Release Sync
 * Synchronizes standard posts in the "Press Releases" category to the cmr_news custom post type.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action('save_post', 'cmr_sync_press_release_to_cmr_news', 10, 3);

function cmr_sync_press_release_to_cmr_news($post_id, $post, $update) {
    // Prevent infinite loop if this function triggers save_post again
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;
    
    // Only act on standard 'post' type
    if ($post->post_type !== 'post') return;

    // Check if post belongs to Press Releases category
    $categories = wp_get_post_categories($post_id, array('fields' => 'all'));
    $is_press_release = false;
    foreach ($categories as $cat) {
        if (in_array($cat->slug, array('media-release', 'media-releases', 'press-release', 'press-releases', 'pressreleases'))) {
            $is_press_release = true;
            break;
        }
    }

    if (!$is_press_release) return;

    // Remove the hook to prevent infinite loops during wp_insert_post/wp_update_post
    remove_action('save_post', 'cmr_sync_press_release_to_cmr_news', 10, 3);

    // Check if a clone already exists
    $clone_id = get_post_meta($post_id, '_cmr_news_clone_id', true);

    $clone_data = array(
        'post_title'   => $post->post_title,
        'post_content' => $post->post_content,
        'post_excerpt' => $post->post_excerpt,
        'post_status'  => $post->post_status,
        'post_author'  => $post->post_author,
        'post_type'    => 'cmr_news',
        'post_date'    => $post->post_date,
        'post_name'    => $post->post_name,
    );

    if ($clone_id && get_post_type($clone_id) === 'cmr_news') {
        // Update existing clone
        $clone_data['ID'] = $clone_id;
        wp_update_post($clone_data);
    } else {
        // Create new clone
        $clone_id = wp_insert_post($clone_data);
        if (!is_wp_error($clone_id)) {
            update_post_meta($post_id, '_cmr_news_clone_id', $clone_id);
            // Optional: link back from clone to original
            update_post_meta($clone_id, '_original_post_id', $post_id);
        }
    }

    if (!is_wp_error($clone_id)) {
        // Sync thumbnail
        $thumbnail_id = get_post_thumbnail_id($post_id);
        if ($thumbnail_id) {
            set_post_thumbnail($clone_id, $thumbnail_id);
        } else {
            delete_post_thumbnail($clone_id);
        }

        // Sync custom taxonomy (cmr_news_category)
        wp_set_object_terms($clone_id, 'press-releases', 'cmr_news_category');
    }

    // Re-add hook
    add_action('save_post', 'cmr_sync_press_release_to_cmr_news', 10, 3);
}
