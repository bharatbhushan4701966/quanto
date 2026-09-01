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

	$quanto_style_path    = get_stylesheet_directory() . '/style.css';
	$quanto_style_version = file_exists( $quanto_style_path ) ? filemtime( $quanto_style_path ) : wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'quanto-style', get_stylesheet_uri(), array(), $quanto_style_version );
    wp_enqueue_style( 'cmr-mobile-menu-css', get_theme_file_uri( '/assets/css/cmr-mobile-menu.css' ), array(), time() );

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
    
    // CMR News Style
    wp_enqueue_style( 'cmr-news-style', get_theme_file_uri('/assets/css/cmr-news.css') ,array(), time() );
    
    // CMR Common Modal Styles
    wp_enqueue_style( 'cmr-modal-style', get_theme_file_uri('/assets/css/cmr-modal.css'), array(), time() );
    wp_enqueue_style( 'cmr-checkout-block', get_theme_file_uri('/assets/css/cmr-checkout-block.css') ,array(), time() );
    wp_register_style( 'cmr-latest-insights', get_theme_file_uri('/assets/css/cmr-latest-insights.css'), array(), time() );
    wp_register_style( 'cmr-industry-intelligence', get_theme_file_uri('/assets/css/cmr-industry-intelligence.css'), array(), time() );
    wp_register_style( 'cmr-explore-sectors', get_theme_file_uri('/assets/css/cmr-explore-sectors.css'), array(), time() );
    wp_register_style( 'cmr-stay-updated', get_theme_file_uri('/assets/css/cmr-stay-updated.css'), array(), time() );

    // Enqueue homepage Elementor CSS anywhere we inject homepage Elementor sections,
    // including WooCommerce product pages rendered via custom template helpers.
    if ( ( is_home() || is_archive() || is_singular( array( 'post', 'cmr_news', 'cmr_media_release', 'cmr_quarterly', 'product' ) ) ) && class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) ) {
        $homepage_id = get_option( 'page_on_front' );
        if ( ! $homepage_id ) {
            $homepage_id = 14; // Fallback
        }

        if ( function_exists( 'quanto_enqueue_elementor_post_assets' ) ) {
            quanto_enqueue_elementor_post_assets( $homepage_id );
            
            // Enqueue Similar Reports page assets so they are loaded in <head>
            $target_page = get_page_by_path( 'similar-reports-by-industry' );
            if ( ! $target_page ) {
                $target_page = get_page_by_path( 'test' );
            }
            if ( $target_page ) {
                quanto_enqueue_elementor_post_assets( $target_page->ID );
            }
        } else {
            $upload_dir = wp_upload_dir();
            if ( ! empty( $upload_dir['basedir'] ) ) {
                $css_path = trailingslashit( $upload_dir['basedir'] ) . 'elementor/css/';
                $css_url  = trailingslashit( $upload_dir['baseurl'] ) . 'elementor/css/';

                // 1. Enqueue active kit CSS
                $active_kit_id = get_option( 'elementor_active_kit' );
                if ( $active_kit_id ) {
                    $kit_file = 'post-' . $active_kit_id . '.css';
                    if ( file_exists( $css_path . $kit_file ) ) {
                        wp_enqueue_style( 'elementor-post-' . $active_kit_id, $css_url . $kit_file, array(), null );
                    }
                }

                // 2. Enqueue homepage post CSS
                $css_file = new \Elementor\Core\Files\CSS\Post( $homepage_id );
                $css_file->enqueue();

                // 3. Enqueue responsive/optimized styles if present
                $devices = array( 'desktop', 'laptop', 'tablet', 'mobile' );
                
                // base-*.css (responsive layout defaults)
                foreach ( $devices as $device ) {
                    $base_file = 'base-' . $device . '.css';
                    if ( file_exists( $css_path . $base_file ) ) {
                        wp_enqueue_style( 'base-' . $device, $css_url . $base_file, array(), null );
                    }
                }

                // local-[homepage_id]-frontend-*.css (homepage responsive overrides)
                foreach ( $devices as $device ) {
                    $local_file = 'local-' . $homepage_id . '-frontend-' . $device . '.css';
                    if ( file_exists( $css_path . $local_file ) ) {
                        wp_enqueue_style( 'local-' . $homepage_id . '-frontend-' . $device, $css_url . $local_file, array(), null );
                    }
                }
            }
        }
        
        // Aggressively force Elementor to load all core layout styles so the footer doesn't break
        if ( class_exists( '\Elementor\Plugin' ) ) {
            wp_enqueue_style( 'elementor-frontend' );
            wp_enqueue_style( 'elementor-icons' );
            wp_enqueue_style( 'e-flexbox' );
            wp_enqueue_style( 'e-container' );
            wp_enqueue_style( 'elementor-widget-heading' );
            wp_enqueue_style( 'elementor-widget-text-editor' );
            wp_enqueue_style( 'elementor-widget-icon-list' );
            wp_enqueue_style( 'elementor-widget-image' );
            wp_enqueue_style( 'elementor-widget-button' );
            wp_enqueue_style( 'elementor-widget-divider' );
            wp_enqueue_style( 'elementor-widget-spacer' );
        }
    }



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

/**
 * Lightweight Modal Trigger & Scroll Lock Controller + HTML Entity Decoder
 */
add_action( 'wp_footer', function() {
    ?>
    <script id="cmr-modal-controller-js">
    (function() {
        // Universal modal scroll lock checker (Event-driven, 0 CPU overhead)
        function checkActiveModals() {
            var active = false;

            // Elementor Popups
            var elemModals = document.querySelectorAll('.dialog-widget.dialog-type-lightbox, .elementor-popup-modal');
            for (var i = 0; i < elemModals.length; i++) {
                var el = elemModals[i];
                if (el.style.display !== 'none' && !el.classList.contains('dialog-hidden') && window.getComputedStyle(el).display !== 'none') {
                    active = true;
                    break;
                }
            }

            // Bootstrap
            if (!active) {
                var bs = document.querySelectorAll('.modal.show');
                if (bs.length > 0) active = true;
            }

            // Magnific Popup
            if (!active) {
                var mfp = document.querySelectorAll('.mfp-wrap.mfp-ready');
                if (mfp.length > 0) active = true;
            }

            // CMR Review Modal
            if (!active) {
                var cr1 = document.getElementById('cmr-review-modal-overlay');
                if (cr1 && cr1.classList.contains('cmr-open')) active = true;
                var cr2 = document.getElementById('cmr-review-modal');
                if (cr2 && cr2.style.display === 'flex') active = true;
            }

            if (active) {
                document.documentElement.classList.add('cmr-modal-open');
                document.body.classList.add('cmr-modal-open');
            } else {
                document.documentElement.classList.remove('cmr-modal-open');
                document.body.classList.remove('cmr-modal-open');
            }
        }

        // Auto-decode any escaped HTML in process boxes (e.g. &lt;br&gt;&lt;a...&gt;)
        function fixProcessDescriptionHTML() {
            var descriptions = document.querySelectorAll('.process-description, .quanto-process-box p, .explore-sectors-track .sector-desc');
            descriptions.forEach(function(el) {
                var html = el.innerHTML;
                if (html.indexOf('&lt;') !== -1 || html.indexOf('&gt;') !== -1) {
                    var txt = document.createElement('textarea');
                    txt.innerHTML = html;
                    el.innerHTML = txt.value;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            fixProcessDescriptionHTML();

            // Delegate close on clicking outside dialog-widget-content
            document.body.addEventListener('click', function(e) {
                if (e.target.classList.contains('dialog-type-lightbox') || e.target.classList.contains('dialog-widget')) {
                    var closeBtn = e.target.querySelector('.dialog-close-button, .dialog-lightbox-close-button');
                    if (closeBtn) closeBtn.click();
                }
            });

            // Lightweight event hooks for Elementor & Bootstrap
            if (window.jQuery) {
                jQuery(document).on('elementor/popup/show', checkActiveModals);
                jQuery(document).on('elementor/popup/hide', function() {
                    setTimeout(checkActiveModals, 100);
                });
                jQuery(document).on('shown.bs.modal show.bs.modal', checkActiveModals);
                jQuery(document).on('hidden.bs.modal hide.bs.modal', function() {
                    setTimeout(checkActiveModals, 100);
                });
            }

            checkActiveModals();
        });
    })();
    </script>
    <?php
}, 999 );
