<?php
/**
 * Single Template: quanto_tab_build
 * Renders Elementor tab builder templates directly for clean CSS pre-warming and preview.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        $post_id = get_the_ID();
        if ( function_exists( 'cmr_print_elementor_css' ) ) {
            cmr_print_elementor_css( $post_id );
        }
        if ( class_exists( '\Elementor\Plugin' ) ) {
            echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $post_id, true );
        } else {
            the_content();
        }
    }
}

get_footer();
