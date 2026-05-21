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


echo do_shortcode( '[elementor-template id="9011"]' );

get_footer();
