<?php
/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */

	// Block direct access
	if( !defined( 'ABSPATH' ) ){
		exit();
	}

	/**
	* Hook for cursor
	*/
	add_action( 'quanto_cursor_wrap', 'quanto_cursor_wrap_cb', 10 );

	/**
	* Hook for preloader
	*/
	add_action( 'quanto_preloader_wrap', 'quanto_preloader_wrap_cb', 10 );

	/**
	* Hook for offcanvas cart
	*/
	add_action( 'quanto_main_wrapper_start', 'quanto_main_wrapper_start_cb', 10 );

	/**
	* Hook for Header
	*/
	add_action( 'quanto_header', 'quanto_header_cb', 10 );

	/**
	* Hook for Breadcrumb
	*/
	add_action( 'quanto_breadcrumb', 'quanto_breadcrumb_cb', 10 );

	/**
	* Hook for Blog Start Wrapper
	*/
	add_action( 'quanto_blog_section_title', 'quanto_blog_section_title_cb', 10 );

	/**
	* Hook for Blog Start Wrapper
	*/
	add_action( 'quanto_blog_start_wrap', 'quanto_blog_start_wrap_cb', 10 );

	/**
	* Hook for Blog Column Start Wrapper
	*/
    add_action( 'quanto_blog_col_start_wrap', 'quanto_blog_col_start_wrap_cb', 10 );

	/**
	* Hook for Service Column Start Wrapper
	*/
    add_action( 'quanto_service_col_start_wrap', 'quanto_service_col_start_wrap_cb', 10 );

	/**
	* Hook for Blog Column End Wrapper
	*/
    add_action( 'quanto_blog_col_end_wrap', 'quanto_blog_col_end_wrap_cb', 10 );

	/**
	* Hook for Blog Column End Wrapper
	*/
    add_action( 'quanto_blog_end_wrap', 'quanto_blog_end_wrap_cb', 10 );

	/**
	* Hook for Blog Pagination
	*/
    add_action( 'quanto_blog_pagination', 'quanto_blog_pagination_cb', 10 );

    /**
	* Hook for Blog Content
	*/
	add_action( 'quanto_blog_content', 'quanto_blog_content_cb', 10 );

    /**
	* Hook for Blog Sidebar
	*/
	add_action( 'quanto_blog_sidebar', 'quanto_blog_sidebar_cb', 10 );


    /**
	* Hook for Service Sidebar
	*/
	add_action( 'quanto_service_sidebar', 'quanto_service_sidebar_cb', 10 );

    /**
	* Hook for Blog Details Sidebar
	*/
	add_action( 'quanto_blog_details_sidebar', 'quanto_blog_details_sidebar_cb', 10 );

	/**
	* Hook for Blog Details Wrapper Start
	*/
	add_action( 'quanto_blog_details_wrapper_start', 'quanto_blog_details_wrapper_start_cb', 10 );

	/**
	* Hook for Blog Post Meta
	*/
	add_action( 'quanto_blog_post_meta', 'quanto_blog_post_meta_cb', 10 );


	/**
	* Hook for Blog Details Post Meta
	*/
	add_action( 'quanto_blog_details_post_meta', 'quanto_blog_details_post_meta_cb', 10 );

	/**
	* Hook for Blog Details Post Share Options
	*/
	add_action( 'quanto_blog_details_share_options', 'quanto_blog_details_share_options_cb', 10 );

	/**
	* Hook for Blog Details Tags and Categories
	*/
	add_action( 'quanto_blog_details_tags_and_categories', 'quanto_blog_details_tags_and_categories_cb', 10 );

	/**
	* Hook for Blog Deatils Related Post
	*/
	add_action( 'quanto_blog_details_related_post', 'quanto_blog_details_related_post_cb', 10 );

	/**
	* Hook for Blog Deatils Comments
	*/
	add_action( 'quanto_blog_details_comments', 'quanto_blog_details_comments_cb', 10 );

	/**
	* Hook for Blog Deatils Column Start
	*/
	add_action('quanto_blog_details_col_start','quanto_blog_details_col_start_cb');

	/**
	* Hook for Blog Deatils Column End
	*/
	add_action('quanto_blog_details_col_end','quanto_blog_details_col_end_cb');

	/**
	* Hook for Blog Deatils Wrapper End
	*/
	add_action('quanto_blog_details_wrapper_end','quanto_blog_details_wrapper_end_cb');

	/**
	* Hook for Blog Post Thumbnail
	*/
	add_action('quanto_blog_post_thumb','quanto_blog_post_thumb_cb');

	/**
	* Hook for Blog Post Content
	*/
	add_action('quanto_blog_post_content','quanto_blog_post_content_cb');

	/**
	* Hook for Blog Post Excerpt And Read More Button
	*/
	add_action('quanto_blog_postexcerpt_read_content','quanto_blog_postexcerpt_read_content_cb');

	/**
	* Hook for footer content (Disabled to prevent duplicate footers, as user manually added them to Elementor pages)
	*/
	// add_action( 'quanto_footer_content', 'quanto_footer_content_cb', 10 );

	/**
	* Enqueue Elementor footer CSS early (in <head>) so styles are present when footer renders
	*/
	add_action( 'wp_enqueue_scripts', 'quanto_enqueue_footer_css_early', 99 );

	/**
	* Hook for main wrapper end
	*/
	add_action( 'quanto_main_wrapper_end', 'quanto_main_wrapper_end_cb', 10 );

	/**
	* Hook for Page Start Wrapper
	*/
	add_action( 'quanto_page_start_wrap', 'quanto_page_start_wrap_cb', 10 );

	/**
	* Hook for Page End Wrapper
	*/
	add_action( 'quanto_page_end_wrap', 'quanto_page_end_wrap_cb', 10 );

	/**
	* Hook for Page Column Start Wrapper
	*/
	add_action( 'quanto_page_col_start_wrap', 'quanto_page_col_start_wrap_cb', 10 );

	/**
	* Hook for Page Column End Wrapper
	*/
	add_action( 'quanto_page_col_end_wrap', 'quanto_page_col_end_wrap_cb', 10 );

	/**
	* Hook for Page Column End Wrapper
	*/
	add_action( 'quanto_page_sidebar', 'quanto_page_sidebar_cb', 10 );

	/**
	* Hook for Page Content
	*/
	add_action( 'quanto_page_content', 'quanto_page_content_cb', 10 );