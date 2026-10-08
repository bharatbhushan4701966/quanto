<?php
/**
 * One-time database updater: Rename msme-2 page to msme, rename attachment conflict, and update URLs.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', 'cmr_db_fix_msme_slug', 1 );
function cmr_db_fix_msme_slug() {
    global $wpdb;

    // Check if page 51541 or any page has slug msme already
    $current_msme_page = $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE post_name = 'msme' AND post_type = 'page' LIMIT 1" );

    // If not fixed yet or forced
    if ( ! $current_msme_page || isset( $_GET['force_fix_msme'] ) ) {

        // 1. Rename any non-page post/attachment with post_name = 'msme'
        $conflicts = $wpdb->get_results( "SELECT ID, post_name, post_type, post_status FROM {$wpdb->posts} WHERE post_name = 'msme' AND post_type != 'page'" );
        if ( ! empty( $conflicts ) ) {
            foreach ( $conflicts as $c ) {
                $new_slug = 'msme-' . $c->post_type . '-' . $c->ID;
                $wpdb->update(
                    $wpdb->posts,
                    array( 'post_name' => $new_slug ),
                    array( 'ID' => $c->ID ),
                    array( '%s' ),
                    array( '%d' )
                );
                clean_post_cache( $c->ID );
            }
        }

        // 2. Find the MSME page (ID 51541 or slug msme-2)
        $target_page_id = 51541;
        $page_check = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE ID = %d AND post_type = 'page'", $target_page_id ) );
        if ( ! $page_check ) {
            $target_page_id = $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE post_name = 'msme-2' AND post_type = 'page' LIMIT 1" );
        }

        if ( $target_page_id ) {
            // Also rename any trashed pages with slug 'msme'
            $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_name = CONCAT('msme-trashed-', ID) WHERE post_name = 'msme' AND ID != %d", $target_page_id ) );

            // Update target page slug to 'msme'
            $wpdb->update(
                $wpdb->posts,
                array( 'post_name' => 'msme' ),
                array( 'ID' => $target_page_id ),
                array( '%s' ),
                array( '%d' )
            );
            clean_post_cache( $target_page_id );
        }

        // 3. Update any menu items pointing to /msme-2/
        $wpdb->query( "
            UPDATE {$wpdb->postmeta}
            SET meta_value = REPLACE(meta_value, '/msme-2/', '/msme/')
            WHERE meta_key = '_menu_item_url' AND meta_value LIKE '%/msme-2/%'
        " );
        $wpdb->query( "
            UPDATE {$wpdb->postmeta}
            SET meta_value = REPLACE(meta_value, '/msme-2', '/msme')
            WHERE meta_key = '_menu_item_url' AND meta_value LIKE '%/msme-2'
        " );

        // 4. Update post_content and Elementor data
        $wpdb->query( "
            UPDATE {$wpdb->posts}
            SET post_content = REPLACE(post_content, '/msme-2/', '/msme/')
            WHERE post_content LIKE '%/msme-2/%'
        " );
        $wpdb->query( "
            UPDATE {$wpdb->postmeta}
            SET meta_value = REPLACE(meta_value, '/msme-2/', '/msme/')
            WHERE meta_key = '_elementor_data' AND meta_value LIKE '%/msme-2/%'
        " );

        // 5. Flush rewrite rules
        flush_rewrite_rules( false );

        update_option( 'cmr_msme_slug_fixed_v2', 'done' );
    }
}

// 301 Redirect from /msme-2/ to /msme/
add_action( 'template_redirect', 'cmr_redirect_msme_2_to_msme' );
function cmr_redirect_msme_2_to_msme() {
    if ( ! empty( $_SERVER['REQUEST_URI'] ) && preg_match( '#^/msme-2/?(\?.*)?$#i', $_SERVER['REQUEST_URI'] ) ) {
        wp_safe_redirect( home_url( '/msme/' ), 301 );
        exit;
    }
}
