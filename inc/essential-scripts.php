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
    wp_enqueue_style( 'cmr-industry-intelligence', get_theme_file_uri('/assets/css/cmr-industry-intelligence.css'), array(), time() );
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
        // Replace "CMR Pulse" with "CMR GTM" in modal (text and checkbox value)
        function updateCMRModalTexts() {
            var modalContainers = document.querySelectorAll(
                '.elementor-popup-modal, ' +
                '.elementor-7832, .elementor-7849, ' +
                '[data-elementor-id="7832"], [data-elementor-id="7849"], ' +
                '.dialog-widget-content, ' +
                '.dialog-lightbox-widget-content, ' +
                '.custom-subscribe-form, ' +
                '.dialog-widget, ' +
                '[data-elementor-type="popup"]'
            );

            modalContainers.forEach(function(modal) {
                try {
                    var walker = document.createTreeWalker(modal, NodeFilter.SHOW_TEXT, null, false);
                    var node;
                    while (node = walker.nextNode()) {
                        if (node.nodeValue && node.nodeValue.indexOf('CMR Pulse') !== -1) {
                            node.nodeValue = node.nodeValue.replace(/CMR Pulse/g, 'CMR GTM');
                        }
                    }
                } catch(e) {}

                modal.querySelectorAll('input[type="checkbox"]').forEach(function(input) {
                    if (input.value === 'CMR Pulse') {
                        input.value = 'CMR GTM';
                    }
                });

                // Restore missing icons in modal list items if empty
                modal.querySelectorAll('.elementor-icon-list-item').forEach(function(item) {
                    var iconSpan = item.querySelector('.elementor-icon-list-icon');
                    var textEl = item.querySelector('.elementor-icon-list-text');
                    if (!iconSpan || !textEl) return;
                    
                    var text = textEl.textContent.trim().toLowerCase();
                    if (!iconSpan.querySelector('svg') && !iconSpan.querySelector('i')) {
                        if (text.indexOf('key market') !== -1 || text.indexOf('trends') !== -1) {
                            iconSpan.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="12" viewBox="0 0 20 12" fill="none"><path d="M1.4 12L0 10.6L7.4 3.15L11.4 7.15L16.6 2H14V0H20V6H18V3.4L11.4 10L7.4 6L1.4 12Z" fill="white"></path></svg>';
                        } else if (text.indexOf('actionable') !== -1 || text.indexOf('insights') !== -1) {
                            iconSpan.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="20" viewBox="0 0 15 20" fill="none"><path d="M7.5 20C6.95 20 6.47917 19.8042 6.0875 19.4125C5.69583 19.0208 5.5 18.55 5.5 18H9.5C9.5 18.55 9.30417 19.0208 8.9125 19.4125C8.52083 19.8042 8.05 20 7.5 20ZM3.5 17V15H11.5V17H3.5ZM3.75 14C2.6 13.3167 1.6875 12.4 1.0125 11.25C0.3375 10.1 0 8.85 0 7.5C0 5.41667 0.729167 3.64583 2.1875 2.1875C3.64583 0.729167 5.41667 0 7.5 0C9.58333 0 11.3542 0.729167 12.8125 2.1875C14.2708 3.64583 15 5.41667 15 7.5C15 8.85 14.6625 10.1 13.9875 11.25C13.3125 12.4 12.4 13.3167 11.25 14H3.75ZM4.35 12H10.65C11.4 11.4667 11.9792 10.8083 12.3875 10.025C12.7958 9.24167 13 8.4 13 7.5C13 5.96667 12.4667 4.66667 11.4 3.6C10.3333 2.53333 9.03333 2 7.5 2C5.96667 2 4.66667 2.53333 3.6 3.6C2.53333 4.66667 2 5.96667 2 7.5C2 8.4 2.20417 9.24167 2.6125 10.025C3.02083 10.8083 3.6 11.4667 4.35 12Z" fill="white"></path></svg>';
                        } else if (text.indexOf('industry') !== -1 || text.indexOf('analysis') !== -1) {
                            iconSpan.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="18" viewBox="0 0 20 18" fill="none"><path d="M0 18V0H10V4H20V18H0ZM2 16H4V14H2V16ZM2 12H4V10H2V12ZM2 8H4V6H2V8ZM2 4H4V2H2V4ZM6 16H8V14H6V16ZM6 12H8V10H6V12ZM6 8H8V6H6V8ZM6 4H8V2H6V4ZM10 16H18V6H10V8H12V10H10V12H12V14H10V16ZM14 10V8H16V10H14ZM14 14V12H16V14H14Z" fill="white"></path></svg>';
                        }
                    }
                });
            });
        }

        var updateCMRModalDebounce = null;
        function scheduleCMRModalUpdate() {
            if (updateCMRModalDebounce) return;
            updateCMRModalDebounce = requestAnimationFrame(function() {
                updateCMRModalDebounce = null;
                updateCMRModalTexts();
            });
        }

        // Universal modal scroll lock checker (Event-driven, 0 CPU overhead)
        function checkActiveModals() {
            updateCMRModalTexts();
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

        // Helper to extract popup ID from Elementor action URLs
        function extractPopupId(href) {
            if (!href) return null;
            try {
                var decoded = decodeURIComponent(href);
                var match = decoded.match(/settings=([A-Za-z0-9+/=]+)/);
                if (match && match[1]) {
                    var jsonStr = atob(match[1]);
                    var parsed = JSON.parse(jsonStr);
                    if (parsed && parsed.id) return parseInt(parsed.id);
                }
                var idMatch = decoded.match(/id["':=\s]+(\d+)/);
                if (idMatch && idMatch[1]) return parseInt(idMatch[1]);
            } catch(e) {}
            return null;
        }

        document.addEventListener('DOMContentLoaded', function() {
            fixProcessDescriptionHTML();

            // Function to close all modals cleanly
            function closeAllModals(e) {
                document.documentElement.classList.remove('cmr-modal-open');
                document.body.classList.remove('cmr-modal-open');
                
                if (typeof elementorProFrontend !== 'undefined' && elementorProFrontend.modules && elementorProFrontend.modules.popup) {
                    try {
                        elementorProFrontend.modules.popup.closePopup({}, e);
                    } catch(err) {}
                }

                document.querySelectorAll('.dialog-widget.dialog-type-lightbox, .elementor-popup-modal').forEach(function(el) {
                    el.style.display = 'none';
                    el.style.opacity = '0';
                });

                var crOverlay = document.getElementById('cmr-review-modal-overlay');
                if (crOverlay) crOverlay.classList.remove('cmr-open');
                var crModal = document.getElementById('cmr-review-modal');
                document.querySelectorAll('.cmr-keyboard-active').forEach(function(el) {
                    el.classList.remove('cmr-keyboard-active');
                });
                document.body.classList.remove('cmr-keyboard-active');

                setTimeout(checkActiveModals, 30);
            }

            // Function to open popup by ID cleanly and reliably
            function openPopup(id, triggerEl) {
                id = parseInt(id);
                if (!id) return;

                // Remove previous hide overrides on target popup
                var targets = document.querySelectorAll(
                    '.elementor-popup-modal[data-elementor-id="' + id + '"], ' +
                    '.dialog-widget[data-elementor-id="' + id + '"], ' +
                    '#elementor-popup-modal-' + id + ', ' +
                    '.elementor-' + id
                );
                targets.forEach(function(el) {
                    el.style.removeProperty('display');
                    el.style.removeProperty('opacity');
                    el.style.removeProperty('visibility');
                    el.scrollTop = 0;
                    el.classList.remove('cmr-keyboard-active');
                });
                document.body.classList.remove('cmr-keyboard-active');

                var opened = false;
                if (typeof elementorProFrontend !== 'undefined' && elementorProFrontend.modules) {
                    // Primary: popup.showPopup is the most reliable method and supports re-opening
                    if (elementorProFrontend.modules.popup) {
                        try {
                            elementorProFrontend.modules.popup.showPopup({ id: id, toggle: false });
                            opened = true;
                        } catch(err) {
                            console.error(err);
                        }
                    }
                    // Fallback: actionHash (can silently fail on re-open)
                    if (!opened && elementorProFrontend.modules.actionHash) {
                        try {
                            var actionUrl = 'elementor-action:action=popup:open&settings=' + btoa(JSON.stringify({ id: id.toString(), toggle: false }));
                            elementorProFrontend.modules.actionHash.runAction(actionUrl);
                            opened = true;
                        } catch(err) {}
                    }
                }

                if (window.jQuery) {
                    try {
                        window.jQuery(document).trigger('elementor/popup/show', [{ id: id }]);
                    } catch(e) {}
                }

                var modal = document.querySelector('#elementor-popup-modal-' + id) || document.querySelector('.elementor-' + id + '.elementor-location-popup');
                if (modal) {
                    var dialogWidget = modal.closest('.dialog-widget') || modal;
                    dialogWidget.style.setProperty('display', 'flex', 'important');
                    dialogWidget.style.setProperty('opacity', '1', 'important');
                    dialogWidget.style.setProperty('visibility', 'visible', 'important');
                    dialogWidget.style.setProperty('z-index', '999999', 'important');
                }

                document.documentElement.classList.add('cmr-modal-open');
                document.body.classList.add('cmr-modal-open');
                setTimeout(checkActiveModals, 50);
            }

            // Expose globally so onclick handlers can call it directly
            window.cmrOpenPopup = openPopup;
            window.cmrCloseAllModals = closeAllModals;

            // Helper: Setup Hero CTAs ("Explore Insights" scroll & "Connect with us" popup)
            function initHeroCTAButtons() {
                var talkButtons = document.querySelectorAll(
                    '.talk-btn .elementor-button-text, ' +
                    '.elementor-element-d2bb779 .elementor-button-text, ' +
                    '.elementor-element-ecd03c0 .elementor-button-text, ' +
                    '#smooth-content .talk-btn .elementor-button-text, ' +
                    '.hero .btn-outline'
                );
                
                talkButtons.forEach(function(btnText) {
                    var txt = btnText.textContent.trim().toLowerCase();
                    if (txt.includes('talk to') || txt.includes('analyst') || txt.includes('download latest report')) {
                        // Check if it's not the main header button
                        var inHeader = btnText.closest('header, [data-elementor-type="header"], #quanto-header-desktop');
                        if (!inHeader) {
                            btnText.textContent = 'Connect with us';
                        }
                    }
                });

                document.querySelectorAll('.elementor-button-text').forEach(function(btnText) {
                    var inHeader = btnText.closest('header, [data-elementor-type="header"], #quanto-header-desktop');
                    if (inHeader) return;
                    var txt = btnText.textContent.trim().toLowerCase();
                    if (txt === 'talk to our analyst' || txt === 'talk to analyst') {
                        btnText.textContent = 'Connect with us';
                    }
                });
            }

            initHeroCTAButtons();
            setTimeout(initHeroCTAButtons, 400);
            setTimeout(initHeroCTAButtons, 1200);

            // Unified click handler for all popups & hero CTAs
            document.body.addEventListener('click', function(e) {
                // 1. Check for "Connect to Analyst" / "Connect with us" / Talk to Analyst CTA (open popup 7637)
                var connectTrigger = e.target.closest('.talk-btn, .elementor-element-d2bb779, .elementor-element-ecd03c0, .custom-talk-analyst-btn, [data-popup-id="7637"], .open-popup');
                if (!connectTrigger) {
                    var btnEl = e.target.closest('.elementor-button, button, a');
                    if (btnEl) {
                        var bTxt = (btnEl.textContent || '').trim().toLowerCase();
                        if (bTxt.includes('connect to analyst') || bTxt.includes('talk to analyst') || bTxt.includes('connect with us') || bTxt.includes('talk to our analyst')) {
                            connectTrigger = btnEl;
                        }
                    }
                }

                if (connectTrigger) {
                    var inHeader = connectTrigger.closest('header, [data-elementor-type="header"], #quanto-header-desktop');
                    if (!inHeader) {
                        e.preventDefault();
                        e.stopPropagation();
                        openPopup(7637, connectTrigger);
                        return;
                    }
                }

                // 2. Check for "Explore Insights" CTA (smooth scroll to Latest Insights)
                var exploreTrigger = e.target.closest('.elementor-element-37736ef, .elementor-element-b44d429 .download-btn, .elementor-element-758e182 .download-btn, .hero .download-btn');
                if (!exploreTrigger) {
                    var btnEl2 = e.target.closest('.elementor-button, button, a');
                    if (btnEl2 && !btnEl2.classList.contains('open-popup') && !btnEl2.hasAttribute('data-popup-id') && !btnEl2.classList.contains('cmr-cta-btn')) {
                        var bTxt2 = (btnEl2.textContent || '').trim().toLowerCase();
                        if (bTxt2.includes('explore insights') || bTxt2.includes('explore all insights')) {
                            exploreTrigger = btnEl2;
                        }
                    }
                }

                if (exploreTrigger && !exploreTrigger.classList.contains('open-popup') && !exploreTrigger.hasAttribute('data-popup-id')) {
                    var inHeader2 = exploreTrigger.closest('header, [data-elementor-type="header"], #quanto-header-desktop');
                    if (!inHeader2) {
                        e.preventDefault();
                        e.stopPropagation();

                        var targetSection = document.getElementById('overview') ||
                                            document.getElementById('insights') ||
                                            document.getElementById('latest-insights') ||
                                            document.querySelector('.cmr-latest-section') ||
                                            document.querySelector('.cmr-industry-intel-section') ||
                                            document.querySelector('.cmr-vpi-section') ||
                                            document.querySelector('.cmr-mui-section');

                        if (!targetSection) {
                            var allHeadings = Array.from(document.querySelectorAll('h1, h2, h3, h4, .elementor-heading-title'));
                            var matchHeading = allHeadings.find(function(h) {
                                var t = h.textContent.toLowerCase();
                                return t.includes('latest insights') || t.includes('expert viewpoints') || t.includes('latest industry');
                            });
                            if (matchHeading) {
                                targetSection = matchHeading.closest('.elementor-section, .elementor-element, section, .e-con') || matchHeading;
                            }
                        }

                        if (targetSection) {
                            var headerOffset = 80;
                            var wpAdminBar = document.getElementById('wpadminbar');
                            if (wpAdminBar) headerOffset += wpAdminBar.offsetHeight;
                            var elPosition = targetSection.getBoundingClientRect().top + window.pageYOffset;
                            var offsetPosition = elPosition - headerOffset;

                            window.scrollTo({
                                top: offsetPosition,
                                behavior: 'smooth'
                            });
                        }
                        return;
                    }
                }

                // 3. Check if clicked element or parent is a popup trigger
                var trigger = e.target.closest(
                    'a[href*="popup:open"], a[href*="elementor-action"], ' +
                    '.open-report-popup, .slide-cta-button, [href="#open-report-popup"], ' +
                    '.open-popup, .custom-talk-analyst-btn, [href="#open-popup"], ' +
                    '[data-elementor-open-popup], [data-popup-id]'
                );

                if (trigger) {
                    var href = trigger.getAttribute('href') || '';
                    var popupId = null;

                    if (trigger.classList.contains('open-report-popup') || trigger.classList.contains('slide-cta-button') || href === '#open-report-popup') {
                        popupId = 7758;
                    } else if (trigger.classList.contains('open-popup') || trigger.classList.contains('custom-talk-analyst-btn') || href === '#open-popup') {
                        popupId = 7637;
                    } else if (trigger.getAttribute('data-popup-id')) {
                        popupId = parseInt(trigger.getAttribute('data-popup-id'));
                    } else if (href.indexOf('7832') !== -1) {
                        popupId = 7832;
                    } else if (href.indexOf('7849') !== -1) {
                        popupId = 7849;
                    } else {
                        popupId = extractPopupId(href);
                    }

                    if (popupId) {
                        e.preventDefault();
                        e.stopPropagation();
                        openPopup(popupId, trigger);
                        return;
                    }
                }

                // 4. Handle close button click
                var closeBtn = e.target.closest('.dialog-close-button, .dialog-lightbox-close-button, .btn-close, .mfp-close, #cmr-close-review-modal, #cmr-review-modal-close');
                if (closeBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeAllModals(e);
                    return;
                }

                // 5. Close on clicking backdrop (outside dialog content)
                if (e.target.classList.contains('dialog-type-lightbox') || e.target.classList.contains('dialog-widget') || e.target.classList.contains('dialog-backdrop') || e.target.id === 'cmr-review-modal-overlay') {
                    e.preventDefault();
                    closeAllModals(e);
                }
            });

            // Instant ESC key close
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    closeAllModals(e);
                }
            });

            // Event hooks for Elementor & Bootstrap
            if (window.jQuery) {
                jQuery(document).on('elementor/popup/show', checkActiveModals);
                jQuery(document).on('elementor/popup/hide', function() {
                    checkActiveModals();
                });
                jQuery(document).on('shown.bs.modal show.bs.modal', checkActiveModals);
                jQuery(document).on('hidden.bs.modal hide.bs.modal', function() {
                    checkActiveModals();
                });
            }

            var keyboardBlurTimeout = null;

            // Auto scroll focused input into viewport above mobile virtual keyboard
            document.addEventListener('focusin', function(e) {
                if (window.innerWidth <= 768) {
                    var target = e.target;
                    if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.tagName === 'SELECT')) {
                        var modal = target.closest('.dialog-widget, .elementor-popup-modal, .modal, #cmr-review-modal, #cmr-review-modal-overlay');
                        if (modal) {
                            clearTimeout(keyboardBlurTimeout);
                            modal.classList.add('cmr-keyboard-active');
                            document.body.classList.add('cmr-keyboard-active');
                            setTimeout(function() {
                                try {
                                    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                } catch(err) {
                                    target.scrollIntoView(false);
                                }
                            }, 280);
                        }
                    }
                }
            }, true);

            // Restore Center Alignment when input loses focus (smooth recenter)
            document.addEventListener('focusout', function(e) {
                if (window.innerWidth <= 768) {
                    var target = e.target;
                    if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.tagName === 'SELECT')) {
                        keyboardBlurTimeout = setTimeout(function() {
                            var activeEl = document.activeElement;
                            var stillInInput = activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA' || activeEl.tagName === 'SELECT');
                            if (!stillInInput) {
                                document.querySelectorAll('.cmr-keyboard-active').forEach(function(el) {
                                    el.classList.remove('cmr-keyboard-active');
                                    try {
                                        el.scrollTo({ top: 0, behavior: 'smooth' });
                                    } catch(err) {
                                        el.scrollTop = 0;
                                    }
                                });
                                document.body.classList.remove('cmr-keyboard-active');
                            }
                        }, 200);
                    }
                }
            }, true);

            if (window.visualViewport) {
                window.visualViewport.addEventListener('resize', function() {
                    if (window.innerWidth <= 768) {
                        var activeEl = document.activeElement;
                        if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA' || activeEl.tagName === 'SELECT')) {
                            var modal = activeEl.closest('.dialog-widget, .elementor-popup-modal, .modal, #cmr-review-modal, #cmr-review-modal-overlay');
                            if (modal) {
                                modal.classList.add('cmr-keyboard-active');
                                document.body.classList.add('cmr-keyboard-active');
                                setTimeout(function() {
                                    try {
                                        activeEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    } catch(err) {}
                                }, 100);
                            }
                        }
                    }
                });
            }

            checkActiveModals();
            updateCMRModalTexts();

            if (window.MutationObserver) {
                var modalObserver = new MutationObserver(scheduleCMRModalUpdate);
                modalObserver.observe(document.body, { childList: true, subtree: true });
            }
        });
    })();
    </script>
    <?php
}, 999 );
