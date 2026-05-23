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



    // Render specific homepage sections as requested
    if ( function_exists( 'quanto_render_homepage_client_testimonials_section' ) ) {
        quanto_render_homepage_client_testimonials_section();
    }

    if ( function_exists( 'quanto_render_homepage_who_we_serve_section' ) ) {
        quanto_render_homepage_who_we_serve_section();
    }

    // Render the last 4 sections from the homepage
    if ( function_exists( 'quanto_render_homepage_tail_sections' ) ) {
        quanto_render_homepage_tail_sections( 4 );
    }

    //footer
    get_footer();
