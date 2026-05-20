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

    // Temp debug:
    $homepage_id = get_option( 'page_on_front' );
    if ( ! $homepage_id ) { $homepage_id = 14; }
    $meta = get_post_meta( $homepage_id, '_elementor_data', true );
    $data = json_decode( $meta, true );
    $list_elements = null;
    $list_elements = function( $elements ) use ( &$list_elements ) {
        $result = array();
        foreach ( $elements as $element ) {
            $info = array( 'id' => isset($element['id']) ? $element['id'] : '', 'elType' => isset($element['elType']) ? $element['elType'] : '' );
            if ( isset( $element['elements'] ) && ! empty( $element['elements'] ) ) {
                $info['children'] = $list_elements( $element['elements'] );
            }
            $result[] = $info;
        }
        return $result;
    };
    if ( $data ) {
        echo '<!-- ELEMENTOR DEBUG DUMP: ' . json_encode( $list_elements($data) ) . ' -->';
    }

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

    //footer
    get_footer();
