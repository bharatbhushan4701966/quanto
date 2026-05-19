<?php
/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */

// Block direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}   
    // Header
    get_header();

    /**
    * 
    * Hook for Blog Section Title
    *
    * Hook quanto_blog_section_title
    *
    * @Hooked quanto_blog_section_title_cb 10
    *  
    */
    do_action( 'quanto_blog_section_title' );

    /**
    * 
    * Hook for Blog Start Wrapper
    *
    * Hook quanto_blog_start_wrap
    *
    * @Hooked quanto_blog_start_wrap_cb 10
    *  
    */
    do_action( 'quanto_blog_start_wrap' );

    /**
    * 
    * Hook for Blog Column Start Wrapper
    *
    * Hook quanto_blog_col_start_wrap
    *
    * @Hooked quanto_blog_col_start_wrap_cb 10
    *  
    */
    do_action( 'quanto_blog_col_start_wrap' );

    /**
    * 
    * Hook for Blog Content
    *
    * Hook quanto_blog_content
    *
    * @Hooked quanto_blog_content_cb 10
    *  
    */
    do_action( 'quanto_blog_content' );

    /**
    * 
    * Hook for Blog Pagination
    *
    * Hook quanto_blog_pagination
    *
    * @Hooked quanto_blog_pagination_cb 10
    *  
    */
    do_action( 'quanto_blog_pagination' ); 

    /**
    * 
    * Hook for Blog Column End Wrapper
    *
    * Hook quanto_blog_col_end_wrap
    *
    * @Hooked quanto_blog_col_end_wrap_cb 10
    *  
    */
    do_action( 'quanto_blog_col_end_wrap' ); 

    /**
    * 
    * Hook for Blog Sidebar
    *
    * Hook quanto_blog_sidebar
    *
    * @Hooked quanto_blog_sidebar_cb 10
    *  
    */
    do_action( 'quanto_blog_sidebar' );     
        
    /**
    * 
    * Hook for Blog End Wrapper
    *
    * Hook quanto_blog_end_wrap
    *
    * @Hooked quanto_blog_end_wrap_cb 10
    *  
    */
    do_action( 'quanto_blog_end_wrap' );

    //footer
    get_footer();