<?php
/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */

// Block direct access
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'quanto_setup' ) ){
    function quanto_setup() {

        // content width
        $GLOBALS['content_width'] = apply_filters( 'quanto_content_width', 751 );

        // language file
		load_theme_textdomain( 'quanto', get_template_directory() . '/languages' );	
		

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// title tag
		add_theme_support( 'title-tag' );

		// post thumbnails
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'quanto-more-detail', 472, 362, true );

		// This theme uses wp_nav_menu() in three locations.
        register_nav_menus( array(
            'primary-menu'      => esc_html__( 'Primary Menu', 'quanto' ),
            'mobile-menu'       => esc_html__( 'Mobile Menu', 'quanto' ),
        ) );

		//support html5
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

        // support post format
        add_theme_support( 'post-formats', array( 'audio', 'video', 'gallery' ) );

		// Custom logo
		add_theme_support( 'custom-logo' );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Enqueue editor styles.
		add_editor_style( 'assets/css/style-editor.css' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		// WooCommerce Support
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

	}
}
add_action( 'after_setup_theme', 'quanto_setup' );

/**
 * Display empty/placeholder stars for products with 0 reviews.
 */
function quanto_show_empty_stars_when_no_reviews( $html, $rating, $count ) {
	if ( 0 == $rating ) {
		$html = '<div class="star-rating" role="img" aria-label="' . esc_attr__( 'No reviews yet', 'quanto' ) . '">';
		$html .= '<span style="width:0%">Rated <strong class="rating">0</strong> out of 5</span>';
		$html .= '</div>';
	}
	return $html;
}
add_filter( 'woocommerce_product_get_rating_html', 'quanto_show_empty_stars_when_no_reviews', 10, 3 );

/**
 * Change WooCommerce Add to Cart button text to "Download" for single product and "Download Report" for loops
 */
add_filter( 'woocommerce_product_single_add_to_cart_text', 'quanto_custom_single_add_to_cart_text' );
function quanto_custom_single_add_to_cart_text() {
	return __( 'Download', 'quanto' );
}

add_filter( 'woocommerce_product_add_to_cart_text', 'quanto_custom_loop_add_to_cart_text' );
function quanto_custom_loop_add_to_cart_text() {
	return __( 'Download Report', 'quanto' );
}

/**
 * Change WooCommerce Loop Product Title from h2 to h5
 */
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', 'quanto_woocommerce_template_loop_product_title', 10 );
function quanto_woocommerce_template_loop_product_title() {
	echo '<h5 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</h5>';
}

/**
 * Display category breadcrumb above product title
 */
add_action( 'woocommerce_shop_loop_item_title', 'quanto_woocommerce_template_loop_product_category', 5 );
function quanto_woocommerce_template_loop_product_category() {
    global $product;
    $categories = wc_get_product_category_list( $product->get_id(), ', ' );
    if ( $categories ) {
        echo '<div class="product-category-breadcrumb"><span class="breadcrumb-dash">&mdash;</span> ' . wp_strip_all_tags( $categories ) . '</div>';
    }
}