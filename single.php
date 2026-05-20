<?php
/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */
 
    // Block direct access
    if( ! defined( 'ABSPATH' ) ){
        exit();
    }

    //header
    get_header();

    /**
    * 
    * Hook for Blog Details Wrapper
    *
    * Hook quanto_blog_details_wrapper_start
    *
    * @Hooked quanto_blog_details_wrapper_start_cb 10
    *  
    */
    do_action( 'quanto_blog_details_wrapper_start' );
    
    /**
    * 
    * Hook for Blog Details Column Start
    *
    * Hook quanto_blog_details_col_start
    *
    * @Hooked quanto_blog_details_col_start_cb 10
    *  
    */
    do_action( 'quanto_blog_details_col_start' );

    while( have_posts( ) ) :
        the_post();
        
        get_template_part( 'templates/content-single');
        
    endwhile;
    /**
    * 
    * Hook for Blog Details Column End
    *
    * Hook quanto_blog_details_col_end
    *
    * @Hooked quanto_blog_details_col_end_cb 10
    *  
    */
    do_action( 'quanto_blog_details_col_end' );

    /**
    * 
    * Hook for Blog Details Sidebar
    *
    * Hook quanto_blog_details_sidebar
    *
    * @Hooked quanto_blog_details_sidebar_cb 10
    *  
    */
    do_action( 'quanto_blog_details_sidebar' );
    
    /**
    * 
    * Hook for Blog Details Wrapper End
    *
    * Hook quanto_blog_details_wrapper_end
    *
    * @Hooked quanto_blog_details_wrapper_end_cb 10
    *  
    */
    do_action( 'quanto_blog_details_wrapper_end' );
    
    /**
    *
    * Hook for Blog Details Related Post
    *
    * Hook quanto_blog_details_related_post
    *
    * @Hooked quanto_blog_details_related_post_cb 10
    *
    */
    do_action( 'quanto_blog_details_related_post' );

    // WHO WE SERVE section from homepage
    if ( function_exists( 'quanto_render_homepage_who_we_serve_section' ) ) {
        quanto_render_homepage_who_we_serve_section();
    }

    // CLIENT TESTIMONIALS section from homepage
    if ( function_exists( 'quanto_render_homepage_client_testimonials_section' ) ) {
        quanto_render_homepage_client_testimonials_section();
    }

    // WE WORKED WITH LARGEST GLOBAL BRANDS section from homepage
    if ( function_exists( 'quanto_render_homepage_global_brands_section' ) ) {
        quanto_render_homepage_global_brands_section();
    }

    // CHALLENGE & RESEARCH section from homepage
    if ( function_exists( 'quanto_render_homepage_challenge_research_section' ) ) {
        quanto_render_homepage_challenge_research_section();
    }

    // CONNECT & TRENDS section from homepage
    if ( function_exists( 'quanto_render_homepage_connect_trends_section' ) ) {
        quanto_render_homepage_connect_trends_section();
    }

    // Verbose Debug:
    echo '<!-- VERBOSE DEBUG:';
    echo ' Elementor: ' . (class_exists( '\\Elementor\\Plugin' ) ? 'YES' : 'NO');
    $homepage_id = get_option( 'page_on_front' );
    echo ' | Homepage ID: ' . $homepage_id;
    $meta = get_post_meta( $homepage_id, '_elementor_data', true );
    echo ' | Meta exists: ' . ($meta ? 'YES (' . strlen($meta) . ' bytes)' : 'NO');
    if ($meta) {
        $data = json_decode( $meta, true );
        echo ' | Data is array: ' . (is_array($data) ? 'YES' : 'NO');
        if (is_array($data)) {
            // Helper function to find element recursively
            $find_element = null;
            $find_element = function( $elements, $id ) use ( &$find_element ) {
                foreach ( $elements as $element ) {
                    if ( isset( $element['id'] ) && $element['id'] === $id ) {
                        return $element;
                    }
                    if ( isset( $element['elements'] ) && ! empty( $element['elements'] ) ) {
                        $found = $find_element( $element['elements'], $id );
                        if ( $found ) {
                            return $found;
                        }
                    }
                }
                return null;
            };
            $element_data = $find_element( $data, '7c312a9' );
            echo ' | Element 7c312a9 exists: ' . ($element_data ? 'YES' : 'NO');
            if ($element_data) {
                $element_instance = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data );
                echo ' | Instance exists: ' . ($element_instance ? 'YES' : 'NO');
            }
        }
    }
    echo ' -->';

    //footer
    get_footer();
