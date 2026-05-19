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

/**
 * Enqueue scripts and styles.
 */
function quanto_essential_scripts() {

	wp_enqueue_style( 'quanto-style', get_stylesheet_uri() ,array(), wp_get_theme()->get( 'Version' ) );

    // google font
    wp_enqueue_style( 'quanto-fonts', quanto_google_fonts() ,array(), wp_get_theme()->get( 'Version' ) );

    // Bootstrap Style
    wp_enqueue_style( 'bootstrap-style', get_theme_file_uri( '/assets/css/bootstrap.min.css' ), array(), '5.3.3' );

    // Fontawesome Style
    wp_enqueue_style( 'fontawesome-style', get_theme_file_uri( '/assets/css/all.css' ), array(), '6.7.2' );

    // remixicon Style
    wp_enqueue_style( 'remixicon-style', get_theme_file_uri( '/assets/css/remixicon.css' ), array(), '2.0' );

    // Bootstrap Icons Style
    wp_enqueue_style( 'bootstrap-icons-style', get_theme_file_uri( '/assets/css/bootstrap-icons.min.css' ), array(), '1.11.3' );

    // magnific popup Style
    wp_enqueue_style( 'magnific-popup-style', get_theme_file_uri( '/assets/css/magnific-popup.css' ), array(), time() );

    // meanmenu min Style
    wp_enqueue_style( 'meanmenu-min-style', get_theme_file_uri( '/assets/css/meanmenu.min.css' ), array(), '2.0.7' );

    // odometer Style
    wp_enqueue_style( 'odometer-style', get_theme_file_uri( '/assets/css/odometer.css' ), array(), time() );

    // swiper bundle min Style
    wp_enqueue_style( 'swiper-bundle-min-style', get_theme_file_uri( '/assets/css/swiper-bundle.min.css' ), array(), '7.0.8' );

    // Core Style
    wp_enqueue_style( 'quanto-core-style', get_theme_file_uri( '/assets/css/core.css' ), array(), '1.0' );

    // quanto app style
    wp_enqueue_style( 'quanto-main-style', get_theme_file_uri('/assets/css/style.css') ,array(), time() );
    wp_enqueue_style( 'quanto-blog-style', get_theme_file_uri('/assets/css/blog-default.css') ,array(), time() );



    // Load Js
    
    // Bootstrap
    wp_enqueue_script( 'bootstrap-bundle', get_theme_file_uri( '/assets/js/bootstrap.bundle.min.js' ), array( 'jquery' ), '5.3.3', true );

    // jquery mixitup
    wp_enqueue_script( 'jquery-mixitup', get_theme_file_uri( '/assets/js/jquery.mixitup.min.js' ), array('jquery'), '2.1.11', true );

    // swiper bundle
    wp_enqueue_script( 'swiper-bundle', get_theme_file_uri( '/assets/js/swiper-bundle.min.js' ), array('jquery'), '7.0.8', true );

    // magnific popup
    wp_enqueue_script( 'magnific-popup', get_theme_file_uri( '/assets/js/jquery.magnific-popup.min.js' ), array('jquery'), '1.1.0', true );

    // Odometer JS
    wp_enqueue_script( 'odometer-min-script', get_theme_file_uri( '/assets/js/odometer.min.js' ), array( 'jquery' ), '0.4.8', true );
    wp_enqueue_script( 'viewport-jquery-script', get_theme_file_uri( '/assets/js/viewport.jquery.js' ), array('jquery'), time(), true );

    // Meanmenu JS
    wp_enqueue_script( 'jquery-meanmenu', get_theme_file_uri( '/assets/js/jquery.meanmenu.min.js' ), array('jquery'), time(), true );

    //  gsap JS 
    wp_enqueue_script( 'gsap-script', get_theme_file_uri( '/assets/js/gsap.js' ), array( 'jquery' ), '3.11.4', true );
    wp_enqueue_script( 'gsap-scroll-smoother-script', get_theme_file_uri( '/assets/js/gsap-scroll-smoother.js' ), array( 'jquery' ), '3.11.4', true );
    wp_enqueue_script( 'gsap-scroll-to-plugin-script', get_theme_file_uri( '/assets/js/gsap-scroll-to-plugin.js' ), array( 'jquery' ), '3.11.4', true );
    wp_enqueue_script( 'gsap-scroll-trigger-script', get_theme_file_uri( '/assets/js/gsap-scroll-trigger.js' ), array( 'jquery' ), '3.11.4', true );
    wp_enqueue_script( 'gsap-split-text-script', get_theme_file_uri( '/assets/js/gsap-split-text.js' ), array( 'jquery' ), '3.11.2', true );

    // main script
    wp_enqueue_script( 'quanto-main-script', get_theme_file_uri( '/assets/js/main.js' ), array('jquery'), time(), true );
    
    // comment reply
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'quanto_essential_scripts',99 );


function quanto_block_editor_assets( ) {
    // Add custom fonts.
	wp_enqueue_style( 'quanto-editor-fonts', quanto_google_fonts(), array(), null );
}

add_action( 'enqueue_block_editor_assets', 'quanto_block_editor_assets' );
 
function quanto_google_fonts() {
    $font_families = array(
        'Instrument Sans:400,500,600,700','800','900',
    );

    $familyArgs = array(
        'family' => urlencode( implode( '|', $font_families ) ),
        'subset' => urlencode( 'latin,latin-ext' ),
    );

    $fontUrl = add_query_arg( $familyArgs, '//fonts.googleapis.com/css' );

    return esc_url_raw( $fontUrl );
}