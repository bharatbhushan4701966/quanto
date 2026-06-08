<?php
/**
 * CMR Post Sync API
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'cmr/v1', '/sync-posts', array(
        'methods'             => 'POST',
        'callback'            => 'cmr_sync_posts_callback',
        'permission_callback' => '__return_true', // Open for ad-hoc syncing
    ) );
} );

// Helper to require media functions for sideloading images
function cmr_require_media_functions() {
    if ( ! function_exists( 'media_sideload_image' ) ) {
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }
}

function cmr_sync_posts_callback( WP_REST_Request $request ) {
    cmr_require_media_functions();

    $page = $request->get_param('page') ? intval($request->get_param('page')) : 1;
    $per_page = 5; // Small batch to prevent timeouts when downloading large images

    $target_url = "https://cmrindia.com/wp-json/wp/v2/posts?_embed=true&page={$page}&per_page={$per_page}";
    $response = wp_remote_get( $target_url, array( 'timeout' => 120 ) );

    if ( is_wp_error( $response ) ) {
        return new WP_REST_Response( array( 'success' => false, 'message' => $response->get_error_message() ), 500 );
    }

    $response_code = wp_remote_retrieve_response_code( $response );
    if ( $response_code == 400 ) {
        return new WP_REST_Response( array( 'success' => true, 'message' => 'No more posts found. Sync complete.' ), 200 );
    }

    $body = wp_remote_retrieve_body( $response );
    $posts = json_decode( $body );

    if ( empty( $posts ) || ! is_array( $posts ) ) {
        return new WP_REST_Response( array( 'success' => false, 'message' => 'No posts found or invalid response.' ), 400 );
    }

    $created = 0;
    $skipped = 0;
    $log = array();

    foreach ( $posts as $post ) {
        if ( empty( $post->slug ) || empty( $post->title->rendered ) ) {
            continue;
        }

        // Check if post exists locally
        global $wpdb;
        $post_id_exists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = 'post'", $post->slug ) );
        
        if ( $post_id_exists ) {
            $skipped++;
            $log[] = "Skipped existing post: {$post->slug}";
            continue;
        }

        // 1. Process Categories
        $category_ids = array();
        if ( isset( $post->_embedded->{'wp:term'} ) && is_array( $post->_embedded->{'wp:term'} ) ) {
            // The first array in 'wp:term' usually holds categories
            $terms = $post->_embedded->{'wp:term'}[0]; 
            foreach ( $terms as $term ) {
                if ( $term->taxonomy === 'category' ) {
                    $local_term = get_term_by( 'slug', $term->slug, 'category' );
                    if ( $local_term ) {
                        $category_ids[] = $local_term->term_id;
                    } else {
                        $new_term = wp_insert_term( $term->name, 'category', array( 'slug' => $term->slug ) );
                        if ( ! is_wp_error( $new_term ) ) {
                            $category_ids[] = $new_term['term_id'];
                        }
                    }
                }
            }
        }

        // 2. Process Author
        $author_id = 1; // Fallback to admin if author not found
        if ( isset( $post->_embedded->author[0] ) ) {
            $author_data = $post->_embedded->author[0];
            $local_user = get_user_by( 'login', $author_data->slug );
            if ( $local_user ) {
                $author_id = $local_user->ID;
            } else {
                // Automatically create author if missing
                $user_id = wp_insert_user( array(
                    'user_login'   => sanitize_user( $author_data->slug, true ),
                    'user_pass'    => wp_generate_password( 24, true, true ),
                    'display_name' => sanitize_text_field( $author_data->name ),
                    'role'         => 'author',
                ) );
                if ( ! is_wp_error( $user_id ) ) {
                    $author_id = $user_id;
                }
            }
        }

        // 3. Create Post
        $post_data = array(
            'post_name'    => $post->slug,
            'post_title'   => wp_kses_post( $post->title->rendered ),
            'post_content' => wp_kses_post( $post->content->rendered ),
            'post_excerpt' => wp_kses_post( $post->excerpt->rendered ),
            'post_status'  => 'publish',
            'post_type'    => 'post',
            'post_author'  => $author_id,
            'post_category'=> !empty($category_ids) ? $category_ids : array(),
            'post_date'    => gmdate( 'Y-m-d H:i:s', strtotime( $post->date ) ),
        );

        $post_id = wp_insert_post( $post_data );

        if ( is_wp_error( $post_id ) ) {
            $log[] = "Failed to create post: {$post->slug} - " . $post_id->get_error_message();
            continue;
        }

        // 4. Process Featured Image
        if ( isset( $post->_embedded->{'wp:featuredmedia'}[0] ) ) {
            $media = $post->_embedded->{'wp:featuredmedia'}[0];
            if ( isset( $media->source_url ) ) {
                $image_url = $media->source_url;
                $image_id = media_sideload_image( $image_url, $post_id, null, 'id' );
                if ( ! is_wp_error( $image_id ) ) {
                    set_post_thumbnail( $post_id, $image_id );
                } else {
                    $log[] = "Failed to download image for post {$post_id}: " . $image_id->get_error_message();
                }
            }
        }

        $created++;
        $log[] = "Created new post: {$post->slug} (ID: {$post_id})";
    }

    return new WP_REST_Response( array(
        'success'   => true,
        'message'   => "Batch {$page} completed.",
        'next_page' => $page + 1,
        'stats'     => array(
            'fetched' => count( $posts ),
            'created' => $created,
            'skipped' => $skipped,
        ),
        'log'       => $log,
    ), 200 );
}
