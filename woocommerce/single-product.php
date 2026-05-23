<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * @package WooCommerce/Templates
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' );

/**
 * Hook for Page Start Wrapper
 * Hooked quanto_page_start_wrap_cb 10
 */
do_action( 'quanto_page_start_wrap' );

/**
 * Hook for Column Start Wrapper
 * Hooked quanto_page_col_start_wrap_cb 10
 */
do_action( 'quanto_page_col_start_wrap' );

while ( have_posts() ) :
	the_post();

	wc_get_template_part( 'content', 'single-product' );

endwhile; // end of the loop.

/**
 * Hook for Column End Wrapper
 * Hooked quanto_page_col_end_wrap_cb 10
 */
do_action( 'quanto_page_col_end_wrap' );

/**
 * Hook for Page Sidebar
 * Hooked quanto_page_sidebar_cb 10
 */
do_action( 'quanto_page_sidebar' );

/**
 * Hook for Page End Wrapper
 * Hooked quanto_page_end_wrap_cb 10
 */
do_action( 'quanto_page_end_wrap' );



// Render the last 3 sections from the homepage
if ( function_exists( 'quanto_render_homepage_tail_sections' ) ) {
    quanto_render_homepage_tail_sections( 3 );
}

// Trick Elementor into enqueueing and printing all the necessary global CSS and flexbox assets.
// We use the exact same template (9011) that was originally here, which perfectly triggered the CSS.
// We capture its output with ob_start() so the unwanted HTML (contact form) doesn't display, 
// but Elementor still prints the <link> tags directly to the page.
if ( class_exists( '\Elementor\Plugin' ) ) {
    ob_start();
    if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
        $css_file = new \Elementor\Core\Files\CSS\Post( 9011 );
        $css_file->enqueue();
        $css_file->print_css();
    }
    \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( 9011, true );
    $html = ob_get_clean();
    
    // The CSS is printed via wp_print_styles during the ob_start block, or prepended to $html.
    // We can extract any <link> or <style> tags from $html and print them, discarding the rest.
    if ( preg_match_all( '/<(link|style)[^>]*>.*?(?:<\/\1>)?/is', $html, $matches ) ) {
        foreach ( $matches[0] as $tag ) {
            echo $tag . "\n";
        }
    }
} else {
    // Fallback if Elementor is not active
    ob_start();
    do_shortcode( '[elementor-template id="9011"]' );
    ob_end_clean();
}

get_footer();
