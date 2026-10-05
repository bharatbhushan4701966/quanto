<?php
/**
 * Shortcode for CMR Connect Section
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Shortcode to display the CMR Connect section by rendering the quanto_tab_build post
function cmr_connect_shortcode( $atts = array() ) {
    ob_start();
    
    // Find the post by slug
    $posts = get_posts(array(
        'name'           => 'cmr-connect',
        'post_type'      => 'quanto_tab_build',
        'posts_per_page' => 1,
        'post_status'    => 'publish'
    ));
    
    $post_id = ( $posts && !empty($posts[0]) ) ? $posts[0]->ID : 0;
    $output  = '';
    
    if ( $post_id ) {
        // Print CSS link inline
        if ( function_exists('cmr_print_elementor_css') ) {
            cmr_print_elementor_css($post_id);
        }
        
        // Render via Elementor frontend
        if ( class_exists( '\\Elementor\\Plugin' ) && isset(\Elementor\Plugin::$instance) && isset(\Elementor\Plugin::$instance->frontend) ) {
            $builder_content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $post_id, true );
            if ( ! empty( $builder_content ) ) {
                $output = $builder_content;
            }
        }
    }
    
    // Fallback: If builder content is empty, fetch and cache rendered output
    if ( empty( $output ) && $post_id ) {
        $transient_key = 'cmr_connect_cache';
        $cached_html   = get_transient( $transient_key );
        
        if ( false === $cached_html || ( is_user_logged_in() && isset($_GET['refresh_connect']) ) ) {
            $url      = home_url( '/?quanto_tab_build=cmr-connect' );
            $response = wp_remote_get( $url, array( 'timeout' => 5 ) );
            if ( ! is_wp_error( $response ) ) {
                $body = wp_remote_retrieve_body( $response );
                // Extract inner elementor content based on post ID
                if ( preg_match( '/<div[^>]*class="[^"]*elementor-(?:' . $post_id . ')[^"]*"[^>]*>.*?<\/body>/is', $body, $matches ) ) {
                    if ( preg_match( '/<div[^>]*class="[^"]*elementor-(?:' . $post_id . ')[^"]*"[^>]*>.*<\/div>\s*<\/div>\s*<\/div>/is', $body, $inner_matches ) ) {
                        $cached_html = $inner_matches[0];
                        set_transient( $transient_key, $cached_html, WEEK_IN_SECONDS );
                    }
                }
            }
        }
        
        if ( ! empty( $cached_html ) ) {
            $output = $cached_html;
        }
    }
    
    if ( ! empty( $output ) ) {
        echo '<div id="cmr-connect-section">';
        echo $output;
        echo '</div>';
    }
    
    return ob_get_clean();
}
add_shortcode('cmr_connect', 'cmr_connect_shortcode');
add_shortcode('cmr-connect', 'cmr_connect_shortcode');
