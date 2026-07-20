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
 * Include File
 *
 */

// Constants
require_once get_parent_theme_file_path() . '/inc/quanto-constants.php';

//theme setup
require_once QUANTO_DIR_PATH_INC . 'theme-setup.php';

//essential scripts
require_once QUANTO_DIR_PATH_INC . 'essential-scripts.php';

//template helper
require_once QUANTO_DIR_PATH_INC . 'template-helper.php';

// plugin activation
require_once QUANTO_DIR_PATH_INC . 'Quanto-framework/plugins-activation/quanto-active-plugins.php';

// meta options
require_once QUANTO_DIR_PATH_INC . 'Quanto-framework/quanto-meta/quanto-config.php';

// page breadcrumbs
require_once QUANTO_DIR_PATH_INC . 'quanto-breadcrumbs.php';

// sidebar register
require_once QUANTO_DIR_PATH_INC . 'quanto-widgets-reg.php';

//essential functions
require_once QUANTO_DIR_PATH_INC . 'quanto-functions.php';

// theme dynamic css
require_once QUANTO_DIR_PATH_INC . 'quanto-commoncss.php';

// helper function
require_once QUANTO_DIR_PATH_INC . 'wp-html-helper.php';

// pagination
require_once QUANTO_DIR_PATH_INC . 'wp_bootstrap_pagination.php';

// quanto options
function quanto_setup_ab() { 
    require_once QUANTO_DIR_PATH_INC . 'Quanto-framework/quanto-options/quanto-options.php';
}
add_action( 'after_setup_theme', 'quanto_setup_ab', 20 );

// hooks
require_once QUANTO_DIR_PATH_HOOKS . 'hooks.php';

// hooks funtion
require_once QUANTO_DIR_PATH_HOOKS . 'hooks-functions.php';

// Enqueue footer CSS early
add_action( 'wp_enqueue_scripts', 'quanto_enqueue_footer_css_early', 10 );

// Force enable WooCommerce product reviews and ratings
add_action( 'init', 'quanto_force_enable_woocommerce_reviews' );
function quanto_force_enable_woocommerce_reviews() {
    if ( class_exists( 'WooCommerce' ) ) {
        if ( get_option( 'woocommerce_enable_reviews' ) !== 'yes' ) {
            update_option( 'woocommerce_enable_reviews', 'yes' );
        }
        if ( get_option( 'woocommerce_enable_review_rating' ) !== 'yes' ) {
            update_option( 'woocommerce_enable_review_rating', 'yes' );
        }
    }
}

// Custom Breadcrumb Shortcode
add_shortcode('cmr_breadcrumb', 'cmr_breadcrumb_shortcode');
function cmr_breadcrumb_shortcode() {
    $current_title = '';
    if ( is_archive() ) {
        $current_title = wp_strip_all_tags( get_the_archive_title() );
    } elseif ( is_search() ) {
        $current_title = 'Search Results';
    } else {
        $current_title = get_the_title();
    }
    
    ob_start();
    ?>
    <div class="breadcumb-menu-wrap" style="margin-bottom: 20px; padding: 0;">
        <div class="breadcumb-menu">
            <ul class="justify-content-center" style="margin:0; padding:0; display:flex; align-items:center; list-style:none; justify-content: center;">
                <li><a href="<?php echo esc_url( home_url('/') ); ?>" title="Home" style="color: #666; font-weight: 500; font-size: 12px; text-decoration:none;">Home</a></li>
                <span class="arrow" style="margin: 0 10px; color: #666; font-size:12px;"><i class="fa-solid fa-angle-right"></i></span>
                <li class="active" title="<?php echo esc_attr( $current_title ); ?>" style="color: #111; font-weight: 500; font-size: 12px;"><?php echo esc_html( $current_title ); ?></li>
            </ul>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// CMR News & Media Releases Feature
require_once QUANTO_DIR_PATH_INC . 'cmr-news.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-news-automotive.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-media-coverage.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-news-carousel.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-press-releases.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-spotlight.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-media-contacts.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-press-releases.php';
require_once QUANTO_DIR_PATH_INC . 'author-meta.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-latest-insights.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-latest-insights-tech.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-latest-insights-consumer.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-latest-insights-supply.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-industry-intelligence.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-marketing-services.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-consulting-advisory.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-explore-sectors.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-stay-updated.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-featured-insight.php';
require_once get_theme_file_path( 'inc/cmr-what-we-think.php' );
require_once get_theme_file_path( 'inc/cmr-slide-of-the-day.php' );
require_once get_theme_file_path( 'inc/cmr-team-scroll.php' );
require_once get_theme_file_path( 'inc/cmr-media-cpt.php' );
function cmr_get_unique_smb_post_ids() {
    global $wpdb;
    $results = $wpdb->get_results("
        SELECT p.ID, p.post_title 
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        WHERE p.post_type IN ('post', 'cmr_news', 'cmr_media') 
          AND p.post_status = 'publish' 
          AND (t.slug IN ('smb-connect', 'smb-connect-industry-connect', 'smb', 'smb_connect', 'smb-connects') OR t.name LIKE '%SMB Connect%' OR t.name LIKE '%SMB%' OR t.name LIKE '%Industry Connect%')
        ORDER BY p.post_date DESC
        LIMIT 500
    ");

    $unique_ids = array();
    $seen_titles = array();
    if ( $results ) {
        foreach ( $results as $row ) {
            $title = trim( $row->post_title );
            if ( ! isset( $seen_titles[ $title ] ) ) {
                $seen_titles[ $title ] = true;
                $unique_ids[] = $row->ID;
            }
        }
    }

    // Fallback to recent articles if fewer than 12 SMB posts found so grid/pagination always works
    if ( count( $unique_ids ) < 12 ) {
        $fallback = $wpdb->get_results("
            SELECT ID, post_title FROM {$wpdb->posts}
            WHERE post_type IN ('post', 'cmr_news', 'cmr_media') AND post_status = 'publish'
            ORDER BY post_date DESC
            LIMIT 30
        ");
        if ( $fallback ) {
            foreach ( $fallback as $row ) {
                $title = trim( $row->post_title );
                if ( ! isset( $seen_titles[ $title ] ) && ! in_array( $row->ID, $unique_ids ) ) {
                    $seen_titles[ $title ] = true;
                    $unique_ids[] = $row->ID;
                }
            }
        }
    }

    return $unique_ids;
}

function cmr_get_unique_enterprise_post_ids() {
    global $wpdb;
    $results = $wpdb->get_results("
        SELECT p.ID, p.post_title 
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        WHERE p.post_type IN ('post', 'cmr_news', 'cmr_media') 
          AND p.post_status = 'publish' 
          AND (t.slug IN ('enterprise-connect', 'enterprise', 'enterprise_connect') OR t.name LIKE '%Enterprise Connect%' OR t.name LIKE '%Enterprise%')
        ORDER BY p.post_date DESC
        LIMIT 500
    ");

    $unique_ids = array();
    $seen_titles = array();
    if ( $results ) {
        foreach ( $results as $row ) {
            $title = trim( $row->post_title );
            if ( ! isset( $seen_titles[ $title ] ) ) {
                $seen_titles[ $title ] = true;
                $unique_ids[] = $row->ID;
            }
        }
    }

    if ( count( $unique_ids ) < 12 ) {
        $fallback = $wpdb->get_results("
            SELECT ID, post_title FROM {$wpdb->posts}
            WHERE post_type IN ('post', 'cmr_news', 'cmr_media') AND post_status = 'publish'
            ORDER BY post_date DESC
            LIMIT 30
        ");
        if ( $fallback ) {
            foreach ( $fallback as $row ) {
                $title = trim( $row->post_title );
                if ( ! isset( $seen_titles[ $title ] ) && ! in_array( $row->ID, $unique_ids ) ) {
                    $seen_titles[ $title ] = true;
                    $unique_ids[] = $row->ID;
                }
            }
        }
    }

    return $unique_ids;
}

function cmr_get_unique_channel_post_ids() {
    global $wpdb;
    $results = $wpdb->get_results("
        SELECT p.ID, p.post_title 
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        WHERE p.post_type IN ('post', 'cmr_news', 'cmr_media') 
          AND p.post_status = 'publish' 
          AND (t.slug IN ('channel-connect', 'channel', 'channel_connect') OR t.name LIKE '%Channel Connect%' OR t.name LIKE '%Channel%')
        ORDER BY p.post_date DESC
        LIMIT 500
    ");

    $unique_ids = array();
    $seen_titles = array();
    if ( $results ) {
        foreach ( $results as $row ) {
            $title = trim( $row->post_title );
            if ( ! isset( $seen_titles[ $title ] ) ) {
                $seen_titles[ $title ] = true;
                $unique_ids[] = $row->ID;
            }
        }
    }

    if ( count( $unique_ids ) < 12 ) {
        $fallback = $wpdb->get_results("
            SELECT ID, post_title FROM {$wpdb->posts}
            WHERE post_type IN ('post', 'cmr_news', 'cmr_media') AND post_status = 'publish'
            ORDER BY post_date DESC
            LIMIT 30
        ");
        if ( $fallback ) {
            foreach ( $fallback as $row ) {
                $title = trim( $row->post_title );
                if ( ! isset( $seen_titles[ $title ] ) && ! in_array( $row->ID, $unique_ids ) ) {
                    $seen_titles[ $title ] = true;
                    $unique_ids[] = $row->ID;
                }
            }
        }
    }

    return $unique_ids;
}

require_once QUANTO_DIR_PATH_INC . 'cmr-media-releases-general.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-channel-connect-general.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-channel-connect-grid.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-enterprise-connect-general.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-enterprise-connect-grid.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-smb-connect-general.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-smb-connect-grid.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-smb-tabs.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-media-releases-grid.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-featured-intelligence-carousel.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-industry-intel-list.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-industry-intelligence-trends.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-industry-stack.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-market-updates-hero.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-viewpoints-hero.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-market-updates-insights.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-viewpoints-insights.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-insights-ajax.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-live.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-breadcrumbs.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-sticky-nav-script.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-ajax-handlers.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-dark-media-coverage.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-author-sync.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-post-sync.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-product-sync.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-research-reports-hero.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-featured-reports.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-trending-now.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-custom-report-cta.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-latest-reports.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-custom-cart.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-press-release-sync.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-fix-downloads.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-checkout-ui.php';

require_once QUANTO_DIR_PATH_INC . 'cmr-mega-menu-who-we-are.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-mega-menu-who-we-serve.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-mega-menu-what-we-do.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-mega-menu-what-we-think.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-mega-menu-newsroom.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-mega-menu-connect.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-mobile-mega-menu.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-foundation-scroll.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-location-accordion.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-job-application-form.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-job-application-backend.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-smtp-config.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-team-grid.php';

require_once QUANTO_DIR_PATH_INC . 'cmr-quarterly-results.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-investor-banner.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-performance-results.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-hero-banner.php';
// Removed require for cmr-intro-text.php

// Save rating meta for cmr_news comments
add_action('comment_post', 'cmr_save_comment_rating', 10, 2);
function cmr_save_comment_rating( $comment_id, $comment_approved ) {
    if ( isset( $_POST['rating'] ) && isset( $_POST['comment_post_ID'] ) ) {
        $post_id = intval( $_POST['comment_post_ID'] );
        if ( get_post_type( $post_id ) === 'cmr_news' ) {
            $rating = intval( $_POST['rating'] );
            if ( $rating >= 1 && $rating <= 5 ) {
                add_comment_meta( $comment_id, 'rating', $rating, true );
            }
        }
    }
}

// CMR Intro Text Shortcode directly in functions.php
add_shortcode('cmr_intro', 'cmr_intro_text_shortcode_inline');
function cmr_intro_text_shortcode_inline() {
    ob_start(); ?>
    <style>
        .cmr-intro-text-section {
            font-family: 'Instrument Sans', sans-serif !important;
            font-weight: 400 !important;
            font-style: normal !important;
            font-size: 16px !important;
            line-height: 1.6 !important;
            letter-spacing: 0 !important;
            vertical-align: middle !important;
            background: #ffffff !important;
            color: #000000 !important;
            padding: 60px 40px !important;
            max-width: 1200px !important;
            margin: 0 auto !important;
        }

        .cmr-intro-text-section p {
            font-family: inherit !important;
            font-size: inherit !important;
            color: inherit !important;
            line-height: inherit !important;
            font-weight: inherit !important;
            margin-bottom: 35px;
            margin-top: 0;
        }

        .cmr-intro-text-section p:last-of-type {
            margin-bottom: 0;
        }

        .cmr-intro-hidden-content {
            display: none;
            margin-top: 35px;
        }

        .cmr-intro-read-more {
            text-align: center;
            margin-top: 40px;
        }

        .cmr-read-more-btn {
            font-size: 16px;
            font-weight: 600;
            color: #8B5CF6;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s ease;
            cursor: pointer;
        }

        .cmr-read-more-btn:hover {
            color: #a78bfa;
        }

        .cmr-read-more-btn svg {
            margin-top: 2px;
            transition: transform 0.3s ease;
        }

        .cmr-read-more-btn.active svg {
            transform: rotate(180deg);
        }
        
        @media (max-width: 768px) {
            .cmr-intro-text-section {
                font-size: 16px !important;
                line-height: 1.6 !important;
                padding: 40px 20px !important;
            }
        }
    </style>
    <div class="cmr-intro-text-section">
        <p>The automotive industry isn't just evolving, it's being remade from the ground up. Electrification, connectivity, autonomy, shared mobility and sustainability (the 5 RACES) are no longer emerging concepts. They are here, accelerating simultaneously and demanding strategic clarity.</p>
        <p>Companies that treat these shifts as isolated product decisions will fall behind. Those that recognise them as organisation-wide transformation imperatives — reorienting around customer needs, deploying AI with purpose and rethinking everything from the C-suite to the factory floor will lead.</p>
        <p>The question is no longer whether to respond. It's where to differentiate and who to partner with.</p>
                <div class="cmr-intro-hidden-content">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        </div>

        <div class="cmr-intro-read-more">
            <a href="#" class="cmr-read-more-btn"><span>Read More</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var readMoreBtn = document.querySelector('.cmr-read-more-btn');
            var hiddenContent = document.querySelector('.cmr-intro-hidden-content');
            var btnText = readMoreBtn ? readMoreBtn.querySelector('span') : null;
            
            if(readMoreBtn && hiddenContent && btnText) {
                readMoreBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    if (hiddenContent.style.display === 'block') {
                        hiddenContent.style.display = 'none';
                        btnText.textContent = 'Read More';
                        readMoreBtn.classList.remove('active');
                    } else {
                        hiddenContent.style.display = 'block';
                        btnText.textContent = 'Read Less';
                        readMoreBtn.classList.add('active');
                    }
                });
            }
        });
    </script>
    <?php return ob_get_clean();
}

// CMR Intro Tech Shortcode directly in functions.php
add_shortcode('cmr_intro_tech', 'cmr_intro_tech_shortcode_inline');
function cmr_intro_tech_shortcode_inline() {
    ob_start(); ?>
    <style>
        .cmr-intro-tech-section {
            font-family: 'Instrument Sans', sans-serif !important;
            font-weight: 400 !important;
            font-style: normal !important;
            font-size: 16px !important;
            line-height: 1.6 !important;
            letter-spacing: 0 !important;
            vertical-align: middle !important;
            background: #ffffff !important;
            color: #000000 !important;
            padding: 60px 40px !important;
            max-width: 1200px !important;
            margin: 0 auto !important;
        }

        .cmr-intro-tech-section p {
            font-family: inherit !important;
            font-size: inherit !important;
            color: inherit !important;
            line-height: inherit !important;
            font-weight: inherit !important;
            margin-bottom: 35px;
            margin-top: 0;
        }

        .cmr-intro-tech-section p:last-of-type {
            margin-bottom: 0;
        }

        .cmr-intro-tech-hidden-content {
            display: none;
            margin-top: 35px;
        }

        .cmr-intro-tech-read-more {
            text-align: center;
            margin-top: 40px;
        }

        .cmr-read-more-btn-tech {
            font-size: 16px;
            font-weight: 600;
            color: #8B5CF6;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s ease;
            cursor: pointer;
        }

        .cmr-read-more-btn-tech:hover {
            color: #a78bfa;
        }

        .cmr-read-more-btn-tech svg {
            margin-top: 2px;
            transition: transform 0.3s ease;
        }

        .cmr-read-more-btn-tech.active svg {
            transform: rotate(180deg);
        }
        
        @media (max-width: 768px) {
            .cmr-intro-tech-section {
                font-size: 16px !important;
                line-height: 1.6 !important;
                padding: 40px 20px !important;
            }
        }
    </style>
    <div class="cmr-intro-tech-section">
        <h2 style="font-size: 32px; font-weight: 600; color: #111; margin-top: 0; margin-bottom: 25px; text-transform: uppercase;">CONSUMER TECH</h2>
        <p>Consumer technology is no longer defined by the device — it's defined by the experience surrounding it. AI integration, ecosystem lock-in and shifting ownership models are rewriting the rules of how people choose, use and stay loyal to technology brands.</p>
        <p>Companies still competing on specs alone are losing ground to those competing on context — understanding not just what consumers buy, but why, when and what they expect next.</p>
        <p>The winners won't just ship better products. They'll build smarter relationships with the people who use them.</p>
        
        <div class="cmr-intro-tech-hidden-content">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        </div>

        <div class="cmr-intro-tech-read-more">
            <a href="#" class="cmr-read-more-btn-tech"><span>Read More</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var readMoreBtnTech = document.querySelectorAll('.cmr-read-more-btn-tech');
            var hiddenContentTech = document.querySelectorAll('.cmr-intro-tech-hidden-content');
            
            readMoreBtnTech.forEach(function(btn, index) {
                var content = hiddenContentTech[index];
                var btnText = btn.querySelector('span');
                
                if(content && btnText) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        if (content.style.display === 'block') {
                            content.style.display = 'none';
                            btnText.textContent = 'Read More';
                            btn.classList.remove('active');
                        } else {
                            content.style.display = 'block';
                            btnText.textContent = 'Read Less';
                            btn.classList.add('active');
                        }
                    });
                }
            });
        });
    </script>
    <?php return ob_get_clean();
}

// CMR Market Updates Shortcode
add_shortcode('cmr_market_updates', 'cmr_market_updates_shortcode');
function cmr_market_updates_shortcode($atts) {
    $atts = shortcode_atts( array(
        'category' => ''
    ), $atts );
    ob_start(); ?>
    <style>
        .cmr-market-updates-section {
            display: flex;
            flex-wrap: wrap;
            gap: 60px;
            font-family: 'Instrument Sans', sans-serif !important;
            max-width: 1280px;
            margin: 0 auto;
            padding: 80px 20px;
            color: #111;
        }

        .cmr-mu-left {
            flex: 1;
            min-width: 300px;
        }

        .cmr-mu-title {
            font-size: 42px;
            font-weight: 600;
            margin-bottom: 20px;
            margin-top: 0;
            line-height: 1.1;
            letter-spacing: -1.5px;
            color: #111;
        }

        .cmr-mu-desc {
            font-size: 18px;
            color: #555;
            margin-bottom: 40px;
            line-height: 1.5;
        }

        .cmr-mu-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            border: 1px solid #111;
            border-radius: 30px;
            color: #111;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .cmr-mu-btn:hover {
            background: #111;
            color: #fff;
        }

        .cmr-mu-right {
            flex: 2;
            min-width: 300px;
            display: flex;
            flex-direction: column;
        }

        .cmr-mu-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 0;
            border-bottom: 1px solid #eaeaea;
            text-decoration: none !important;
            color: inherit;
            transition: background 0.3s ease;
        }
        
        .cmr-mu-item:first-child {
            border-top: 1px solid #eaeaea;
            padding-top: 20px;
        }

        .cmr-mu-item-content {
            flex: 1;
            padding-right: 20px;
        }

        .cmr-mu-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .cmr-mu-date {
            color: #666;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cmr-mu-date::before {
            content: "";
            display: inline-block;
            width: 20px;
            height: 1px;
            background: #ccc;
        }

        .cmr-mu-category {
            font-weight: 500;
        }
        .cmr-mu-category.policy { color: #F5A623; }
        .cmr-mu-category.investment { color: #2ECC71; }
        .cmr-mu-category.supply { color: #E74C3C; }

        .cmr-mu-item-title {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: -0.5px;
            margin: 0;
            line-height: 1.4;
            color: #111;
        }

        .cmr-mu-arrow svg {
            transition: transform 0.3s ease;
            color: #111;
        }

        .cmr-mu-item:hover .cmr-mu-arrow svg {
            transform: translate(3px, -3px);
        }

        @media (max-width: 992px) {
            .cmr-market-updates-section {
                flex-direction: column;
                gap: 40px;
            }
        }
    </style>

    <div id="cmr-market-updates" class="cmr-market-updates-section">
        <div class="cmr-mu-left">
            <h2 class="cmr-mu-title">Market Updates</h2>
            <p class="cmr-mu-desc">Need real-time market updates for your business?</p>
            <div class="elementor-element elementor-element-c4dcb9f download-btn animejs-disable elementor-widget elementor-widget-button" data-id="c4dcb9f" data-element_type="widget" data-e-type="widget" data-settings="{&quot;mas-animation&quot;:&quot;none&quot;}" data-widget_type="button.default" style="align-self: flex-start; width: 53%;">
                <a class="elementor-button elementor-button-link elementor-size-sm insights-cta-button secondary" href="#" style="justify-content: center; width: 100%; background-color: transparent !important; border: 1px solid #111 !important; display: flex; align-items: center; border-radius: 40px; padding: 12px 24px;">
                    <span class="elementor-button-content-wrapper" style="width: 100%; display: flex; align-items: center; justify-content: center;">
                        <span class="elementor-button-text" style="margin-right: 6px; font-size: 14px; font-weight: 600 !important; color: #111 !important; line-height: 1; white-space: nowrap;">Talk to Analyst</span>
                        <span class="elementor-button-icon" style="display: flex; align-items: center;">
                            <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Symbol-1.svg" alt="Icon" width="16" height="14" style="object-fit: contain;">
                        </span>
                    </span>
                </a>
            </div>
        </div>
        
        <div class="cmr-mu-right">
            <?php
            $mu_args = array(
                'post_type' => 'post',
                'posts_per_page' => 4,
                'category_name' => 'market-updates', // Default category
            );
            
            // Allow category filtering if the user adds category="slug" to the shortcode
            if ( isset($atts['category']) && !empty($atts['category']) ) {
                $mu_args['category_name'] = $atts['category'];
            }
            
            $mu_posts = get_posts($mu_args);
            
            if (!empty($mu_posts)) :
                foreach ($mu_posts as $post_obj) :
                    $date = get_the_time('M d | h:i A', $post_obj);
                    $categories = get_the_category($post_obj->ID);
                    $cat_name = '';
                    $cat_class = 'policy'; // Consistent default color class
                    if ( ! empty( $categories ) ) {
                        $cat_name = esc_html( $categories[0]->name );
                        // We removed random color classes so the same category always looks consistent.
                    }
            ?>
            <a href="<?php echo esc_url(get_permalink($post_obj->ID)); ?>" class="cmr-mu-item">
                <div class="cmr-mu-item-content">
                    <div class="cmr-mu-meta">
                        <span class="cmr-mu-date"><?php echo $date; ?></span>
                        <?php if ( $cat_name ) : ?>
                            <span class="cmr-mu-category <?php echo $cat_class; ?>"><?php echo $cat_name; ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="cmr-mu-item-title"><?php echo get_the_title($post_obj); ?></h3>
                </div>
                <div class="cmr-mu-arrow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </div>
            </a>
            <?php 
                endforeach; 
            else : 
                echo '<p>No updates found.</p>';
            endif; 
            ?>
        </div>
    </div>
    <?php return ob_get_clean();
}

// Apply Instrument Sans globally
add_action('wp_head', 'cmr_global_font_style', 999);
function cmr_global_font_style() {
    echo '<style>
        body, p, h1, h2, h3, h4, h5, h6, a, button, input, select, textarea, .elementor-button, .elementor-button-text {
            font-family: "Instrument Sans", sans-serif !important;
        }
    </style>';
}

// Shortcode to display the Single Media CTA Banner section by rendering the quanto_tab_build post
add_shortcode('cmr_single_media_cta', function() {
    ob_start();
    
    // Find the post by slug
    $posts = get_posts(array(
        'name' => 'your-next-big-decision-deserves-better-intelligence',
        'post_type' => 'quanto_tab_build',
        'posts_per_page' => 1,
        'post_status' => 'publish'
    ));
    
    if ( $posts && !empty($posts[0]) ) {
        $post_id = $posts[0]->ID;
        
        // Print CSS link inline
        cmr_print_elementor_css($post_id);
        
        // Render it
        if ( class_exists( '\\Elementor\\Plugin' ) ) {
            echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $post_id, true );
        }
    }
    
    return ob_get_clean();
});

// Temporary endpoint to migrate Press Releases to CMR News
add_action( 'rest_api_init', function () {
    register_rest_route( 'cmr/v1', '/migrate-pr', array(
        'methods'             => 'GET',
        'callback'            => 'cmr_migrate_press_releases_callback',
        'permission_callback' => '__return_true',
    ) );
} );

function cmr_migrate_press_releases_callback() {
    $args = array(
        'post_type' => 'post',
        'category_name' => 'pressreleases', // The correct slug for Press Releases
        'posts_per_page' => -1,
    );
    $query = new WP_Query($args);
    
    $migrated = 0;
    $log = array();
    
    if ($query->have_posts()) {
        // Ensure Media Release category exists in cmr_news_category
        $term = term_exists('Media Release', 'cmr_news_category');
        if (!$term) {
            $term = wp_insert_term('Media Release', 'cmr_news_category');
        }
        $term_id = is_array($term) ? $term['term_id'] : $term;
        
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            
            // Check if it's already migrated
            $existing = get_posts(array(
                'post_type' => 'cmr_news',
                'title' => $title,
                'posts_per_page' => 1,
            ));
            
            if (!empty($existing)) {
                $log[] = "Skipped (Already migrated): " . $title;
                continue;
            }
            
            // Duplicate the post
            $new_post = array(
                'post_title' => $title,
                'post_content' => get_post_field('post_content', $post_id),
                'post_excerpt' => get_post_field('post_excerpt', $post_id),
                'post_status' => 'publish',
                'post_type' => 'cmr_news',
                'post_date' => get_post_field('post_date', $post_id),
                'post_author' => get_post_field('post_author', $post_id),
            );
            
            $new_post_id = wp_insert_post($new_post);
            
            if (!is_wp_error($new_post_id)) {
                // Assign taxonomy term
                wp_set_object_terms($new_post_id, (int)$term_id, 'cmr_news_category');
                
                // Copy featured image
                $thumb_id = get_post_thumbnail_id($post_id);
                if ($thumb_id) {
                    set_post_thumbnail($new_post_id, $thumb_id);
                }
                
                $migrated++;
                $log[] = "Migrated successfully: " . $title;
            } else {
                $log[] = "Error migrating: " . $title . " - " . $new_post_id->get_error_message();
            }
        }
                }
    
    return new WP_REST_Response(array(
        'success' => true,
        'migrated_count' => $migrated,
        'log' => $log
    ), 200);
}

// Global Custom CSS for Menus
add_action('wp_head', function() {
    ?>
    <style>
        /* Make all top-level menu items purple on hover */
        .elementor-nav-menu--main .elementor-item:hover,
        .elementor-nav-menu--main .elementor-item.elementor-item-active,
        .elementor-nav-menu--main .elementor-item:focus,
        .menu-item > a:hover {
            color: #6A35FF !important;
        }

        /* Keep the text purple when the user is hovering inside the mega menu card itself */
        .cmr-has-mega-menu:hover > a,
        .cmr-has-mega-menu-do:hover > a,
        .cmr-has-mega-menu-serve:hover > a,
        .cmr-has-mega-menu-think:hover > a,
        .cmr-has-mega-menu-newsroom:hover > a,
        .cmr-has-mega-menu-connect:hover > a {
            color: #6A35FF !important;
        }
    </style>
    <?php
});

// Re-add Hover CSS for Team Box Image
add_action('wp_head', function() {
    ?>
    <style id="quanto-team-hover-css">
    html body .quanto-team-box .team-thumb::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.4);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
        z-index: 1;
    }
    html body .quanto-team-box:hover .team-thumb::after {
        opacity: 1;
    }
    html body .quanto-team-box .team-thumb::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        background-color: transparent;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' xmlns='http://www.w3.org/2000/svg'%3E%3Cline x1='7' y1='17' x2='17' y2='7'%3E%3C/line%3E%3Cpolyline points='7 7 17 7 17 17'%3E%3C/polyline%3E%3C/svg%3E");
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        border-radius: 0;
        opacity: 0;
        transition: all 0.3s ease;
        z-index: 2;
        pointer-events: none;
    }
    html body .quanto-team-box:hover .team-thumb::before {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.1);
    }
    </style>
    <?php
});

// Move social icons from image thumb to below text using JS
add_action('wp_footer', function() {
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var teamBoxes = document.querySelectorAll(".quanto-team-box");
        teamBoxes.forEach(function(box) {
            var ul = box.querySelector(".custom-ul");
            var content = box.querySelector(".team-content");
            if (ul && content) {
                content.appendChild(ul);
            }
        });
    });
    </script>
    <style id="quanto-team-social-css">
    /* Styling for the relocated social icons */
    html body .quanto-team-box .team-content .custom-ul {
        display: flex !important;
        justify-content: flex-start !important;
        gap: 8px !important;
        margin-top: 15px !important;
        padding: 0 !important;
        list-style: none !important;
    }
    html body .quanto-team-box .team-content .custom-ul li {
        margin: 0 !important;
        padding: 0 !important;
    }
    html body .quanto-team-box .team-content .custom-ul li a {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        width: 24px !important;
        height: 24px !important;
        color: #111 !important;
        font-size: 18px !important;
        text-decoration: none !important;
        transition: all 0.3s !important;
        background: transparent !important;
    }
    html body .quanto-team-box .team-content .custom-ul li a:hover {
        color: #6A35FF !important;
    }
    html body .quanto-team-box .team-content .custom-ul li a svg {
        width: 18px !important;
        height: 18px !important;
        fill: currentColor !important;
    }
    </style>
    <?php
});

// Override the Elementor Services widget from the theme
add_action('elementor/widgets/register', function($widgets_manager) {
    // Unregister the plugin's original widget if it exists
    $widgets_manager->unregister('quanto_services');
    
    // Register our theme's overridden widget
    require_once get_template_directory() . '/inc/widgets/service.php';
    $widgets_manager->register(new \Quanto_Service_Theme());

    // Register new Logo Carousel widget
    require_once get_template_directory() . '/inc/widgets/logo-carousel.php';
    $widgets_manager->register(new \Quanto_Logo_Carousel_Widget());

    // Register Industry Intel List widget
    require_once get_template_directory() . '/inc/widgets/industry-intel-list.php';
    $widgets_manager->register(new \Quanto_Industry_Intel_List_Widget());
}, 20); // Priority 20 to run after the plugin registers its widgets
require_once get_template_directory() . '/inc/cmr-footer-css-fix.php';

// Shortcode to display the main Elementor footer by fetching the rendered URL
add_shortcode('cmr_footer', function() {
    $transient_key = 'cmr_footer_html_cache';
    $cached_footer = get_transient( $transient_key );
    
    // Check if user is logged in (to force refresh) or if cache is empty
    if ( false === $cached_footer || ( is_user_logged_in() && isset($_GET['refresh_footer']) ) ) {
        $url = home_url( '/?quanto_footer=main' );
        $response = wp_remote_get( $url, array('timeout' => 15) );
        
        if ( is_wp_error( $response ) ) {
            return $cached_footer ? $cached_footer : '<!-- Error fetching footer -->';
        }
        
        $body = wp_remote_retrieve_body( $response );
        
        // Extract everything from <footer class="footer"> to </footer>
        if ( preg_match( '/<footer class="footer".*?<\/footer>/is', $body, $matches ) ) {
            $cached_footer = $matches[0];
            
            // Instead of caching <link> tags (which can 404 when Elementor cache
            // is cleared), read the CSS directly from disk and embed it inline.
            // This makes the cached footer completely self-contained.
            $inline_css = '';
            
            // Extract post IDs from Elementor CSS link tags in the response
            if ( preg_match_all( '/elementor\/css\/post-(\d+)\.css/i', $body, $id_matches ) ) {
                $post_ids = array_unique( $id_matches[1] );
                foreach ( $post_ids as $pid ) {
                    if ( function_exists( 'cmr_get_elementor_css_inline' ) ) {
                        $css = cmr_get_elementor_css_inline( (int) $pid );
                        if ( ! empty( $css ) ) {
                            $inline_css .= '<style id="cmr-footer-cached-' . $pid . '-css">' . $css . '</style>' . "\n";
                        }
                    }
                }
            }
            
            // If we couldn't get inline CSS, fall back to link tags
            if ( empty( $inline_css ) ) {
                if ( preg_match_all( '/<link[^>]*href="[^"]*elementor\/css\/post-\d+\.css[^"]*"[^>]*>/is', $body, $css_matches ) ) {
                    $inline_css = implode("\n", $css_matches[0]) . "\n";
                }
            }
            
            $cached_footer = $inline_css . $cached_footer;
            
            set_transient( $transient_key, $cached_footer, 6 * HOUR_IN_SECONDS );
        } else {
            return '<!-- Footer tag not found in remote URL -->';
        }
    }
    
    return $cached_footer;
});

// Helper to force print Elementor CSS inline inside a shortcode
if ( ! function_exists('cmr_print_elementor_css') ) {
    function cmr_print_elementor_css($post_id) {
        if ( class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) ) {
            $css_file = new \Elementor\Core\Files\CSS\Post( $post_id );
            
            // Ensure the CSS file exists on disk
            if ( ! file_exists( $css_file->get_path() ) ) {
                // $css_file->update() generates empty CSS for custom post types.
                // We MUST use the full rendering pipeline to generate the CSS file.
                if ( class_exists( '\\Elementor\\Plugin' ) ) {
                    \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $post_id, true );
                }
            }
            
            // Read the CSS file content from disk and output inline.
            // This bypasses CDN/server caching issues where the external CSS file
            // URL returns a cached 404 even though the file now exists on disk.
            $css_path = $css_file->get_path();
            if ( file_exists( $css_path ) ) {
                $css_content = file_get_contents( $css_path );
                if ( ! empty( $css_content ) ) {
                    echo '<style id="elementor-post-' . $post_id . '-inline-css">' . $css_content . '</style>';
                    return;
                }
            }
            
            // Fallback: output link tag to external file
            $css_file->enqueue();
            $url = $css_file->get_url();
            if ($url) {
                echo '<link rel="stylesheet" id="elementor-post-'.$post_id.'-css" href="'.esc_url($url).'" type="text/css" media="all">';
            }
            $css_file->print_css();
        }
    }
}

// Shortcode to display the Challenge section by rendering the quanto_tab_build post
add_shortcode('cmr_challenge', function() {
    ob_start();
    
    // Find the post by slug
    $posts = get_posts(array(
        'name' => 'your-challenge-our-research-your-advantage',
        'post_type' => 'quanto_tab_build',
        'posts_per_page' => 1,
        'post_status' => 'publish'
    ));
    
    if ( $posts && !empty($posts[0]) ) {
        $post_id = $posts[0]->ID;
        
        // Print CSS link inline
        cmr_print_elementor_css($post_id);
        
        // Render it
        if ( class_exists( '\\Elementor\\Plugin' ) ) {
            echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $post_id, true );
        }
    }
    
    return ob_get_clean();
});

// Shortcode to display the Testimonials section by rendering the quanto_tab_build post
add_shortcode('cmr_testimonials', function() {
    ob_start();
    
    // Find the post by slug
    $posts = get_posts(array(
        'name' => 'testimonials',
        'post_type' => 'quanto_tab_build',
        'posts_per_page' => 1,
        'post_status' => 'publish'
    ));
    
    if ( $posts && !empty($posts[0]) ) {
        $post_id = $posts[0]->ID;
        
        // Print CSS link inline
        cmr_print_elementor_css($post_id);
        
        // Render it
        if ( class_exists( '\\Elementor\\Plugin' ) ) {
            echo '<div id="cmr-testimonials-section">';
            echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $post_id, true );
            echo '</div>';
        }
    }
    
    return ob_get_clean();
});// Shortcode to display the Footer Card section by rendering the quanto_tab_build post
add_shortcode('cmr_footer_card', function() {
    ob_start();
    
    // Find the post by slug
    $posts = get_posts(array(
        'name' => 'fotter-card',
        'post_type' => 'quanto_tab_build',
        'posts_per_page' => 1,
        'post_status' => 'publish'
    ));
    
    if ( $posts && !empty($posts[0]) ) {
        $post_id = $posts[0]->ID;
        
        // Print CSS link inline
        cmr_print_elementor_css($post_id);
        
        // Render it
        if ( class_exists( '\\Elementor\\Plugin' ) ) {
            echo '<div id="cmr-footer-card-section">';
            echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $post_id, true );
            echo '</div>';
        }
    }
    
    return ob_get_clean();
});

// Shortcode to display the Global Brands section by rendering the quanto_tab_build post
add_shortcode('cmr_global_brands', function() {
    ob_start();
    
    // Find the post by slug
    $posts = get_posts(array(
        'name' => 'we-worked-with-largest-global-brands',
        'post_type' => 'quanto_tab_build',
        'posts_per_page' => 1,
        'post_status' => 'publish'
    ));
    
    if ( $posts && !empty($posts[0]) ) {
        $post_id = $posts[0]->ID;
        
        // Print CSS link inline
        cmr_print_elementor_css($post_id);
        
        // Render it
        if ( class_exists( '\\Elementor\\Plugin' ) ) {
            echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $post_id, true );
        }
    }
    
    return ob_get_clean();
});

// Helper function to get the post thumbnail, or fallback to scraping the og:image from the media URL, or a hardcoded fallback
if ( ! function_exists( 'cmr_get_thumbnail_with_fallback' ) ) {
    function cmr_get_thumbnail_with_fallback($post_id, $size = 'full') {
        // 1. Check for standard WordPress Featured Image
        $thumbnail_url = get_the_post_thumbnail_url($post_id, $size);
        if ($thumbnail_url) {
            return $thumbnail_url;
        }

        $fallback_img = 'https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/Why-Chipsets-are-the-New-Frontier-in-Smartphones1.jpg';

        // 2. Check if there's a custom media URL and try to fetch og:image
        $media_url = get_post_meta($post_id, '_cmr_media_url', true);
        if ($media_url && filter_var($media_url, FILTER_VALIDATE_URL)) {
            
            // Check transient cache to avoid slowing down page load (use v6 to bust cache)
            $cache_key = 'cmr_og_image_v6_' . md5($media_url);
            $cached_img = get_transient($cache_key);
            
            if ($cached_img) {
                if ($cached_img !== 'none') {
                    return $cached_img;
                }
            } else {
                // Fetch the URL
                $response = wp_remote_get($media_url, array('timeout' => 5));
                if (!is_wp_error($response)) {
                    $body = wp_remote_retrieve_body($response);
                    
                    $candidates = [];
                    
                    // Look for preload image first (best for getpodcast.com covers), then og:image, etc.
                    if (preg_match_all('/<link[^>]*rel="preload"[^>]*href="([^"]+)"[^>]*as="image"/i', $body, $m)) { $candidates = array_merge($candidates, $m[1]); }
                    if (preg_match_all('/<link[^>]*href="([^"]+)"[^>]*rel="preload"[^>]*as="image"/i', $body, $m)) { $candidates = array_merge($candidates, $m[1]); }
                    if (preg_match_all('/<meta property="og:image:secure_url" content="([^"]+)"/i', $body, $m)) { $candidates = array_merge($candidates, $m[1]); }
                    if (preg_match_all('/<meta property="og:image" content="([^"]+)"/i', $body, $m)) { $candidates = array_merge($candidates, $m[1]); }
                    if (preg_match_all('/<meta name="og:image" content="([^"]+)"/i', $body, $m)) { $candidates = array_merge($candidates, $m[1]); }
                    if (preg_match_all('/<meta property="twitter:image" content="([^"]+)"/i', $body, $m)) { $candidates = array_merge($candidates, $m[1]); }
                    
                    foreach ($candidates as $scraped_img) {
                        $scraped_img = trim($scraped_img);
                        
                        // Ignore tiny transparent square from getpodcast which breaks UI
                        if (strpos($scraped_img, 'square.png') !== false) {
                            continue;
                        }
                        
                        // Fix double scheme from some buggy websites (like getpodcast.com)
                        $scraped_img = str_replace('https://https://', 'https://', $scraped_img);
                        $scraped_img = str_replace('http://http://', 'http://', $scraped_img);
                        $scraped_img = str_replace('https://http://', 'https://', $scraped_img);
                        
                        // Handle relative URLs
                        if (strpos($scraped_img, 'http') !== 0) {
                            $parsed_url = parse_url($media_url);
                            $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] : 'https';
                            $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
                            if (strpos($scraped_img, '//') === 0) {
                                $scraped_img = $scheme . ':' . $scraped_img;
                            } else if (strpos($scraped_img, '/') === 0) {
                                $scraped_img = $scheme . '://' . $host . $scraped_img;
                            } else {
                                $scraped_img = $scheme . '://' . $host . '/' . $scraped_img;
                            }
                        }

                        if (filter_var($scraped_img, FILTER_VALIDATE_URL)) {
                            set_transient($cache_key, $scraped_img, 24 * HOUR_IN_SECONDS);
                            return $scraped_img;
                        }
                    }
                }
                // Cache the 'none' result so we don't fetch it every time
                set_transient($cache_key, 'none', 24 * HOUR_IN_SECONDS);
            }
        }

        // 3. Absolute fallback image
        return $fallback_img;
    }
}

// Bulk update WooCommerce downloadable files
add_action('init', 'cmr_bulk_update_woo_downloads');
function cmr_bulk_update_woo_downloads() {
    if (isset($_GET['cmr_update_reports']) && current_user_can('manage_options')) {
        if (!function_exists('wc_get_products')) { die('WooCommerce not active.'); }
        $products = wc_get_products(['limit' => -1]);
        $count = 0;
        $base_url = isset($_GET['base_url']) ? esc_url_raw($_GET['base_url']) : 'https://qai8358l95-staging.onrocket.site/report/';
        foreach ($products as $product) {
            $slug = $product->get_slug();
            // Assumes file is named as {product_slug}.pdf in the /report/ directory
            $file_url = rtrim($base_url, '/') . '/' . $slug . '.pdf';
            
            $download_id = md5($file_url);
            $file = new WC_Product_Download();
            $file->set_id($download_id);
            $file->set_name($product->get_title());
            $file->set_file($file_url);
            
            $downloads = []; 
            $downloads[$download_id] = $file;
            
            $product->set_downloads($downloads);
            $product->set_downloadable(true);
            $product->set_virtual(true);
            
            $product->save();
            $count++;
        }
        die("Successfully updated $count WooCommerce products. Used base URL: " . esc_html($base_url));
    }
}

// Bulk delete duplicate WooCommerce products
add_action('init', 'cmr_bulk_delete_duplicate_woo_products');
function cmr_bulk_delete_duplicate_woo_products() {
    if (isset($_GET['cmr_delete_duplicates']) && current_user_can('manage_options')) {
        if (!function_exists('wc_get_products')) { die('WooCommerce not active.'); }
        
        // Get all products from oldest to newest
        $products = wc_get_products([
            'limit' => -1,
            'status' => ['publish', 'draft', 'pending', 'private'],
            'orderby' => 'date',
            'order' => 'ASC'
        ]);
        
        $seen_titles = [];
        $deleted_count = 0;
        
        foreach ($products as $product) {
            // Normalize title for matching
            $title = trim(strtolower($product->get_name()));
            
            // Clean up trailing '- copy' or '- 1' sometimes added by duplicators
            $title = preg_replace('/ - copy( \d+)?$/i', '', $title);
            
            if (empty($title)) continue;
            
            if (isset($seen_titles[$title])) {
                // This is a duplicate, move it to trash
                wp_trash_post($product->get_id());
                $deleted_count++;
            } else {
                // First time seeing this title (the original/oldest)
                $seen_titles[$title] = true;
            }
        }
        
        die("Successfully moved $deleted_count duplicate WooCommerce products to the Trash (kept the oldest original for each title).");
    }
}

// Bulk replace domain in WooCommerce downloadable files
add_action('init', 'cmr_bulk_replace_domain_woo_downloads');
function cmr_bulk_replace_domain_woo_downloads() {
    if (isset($_GET['cmr_fix_domains']) && current_user_can('manage_options')) {
        if (!function_exists('wc_get_products')) { die('WooCommerce not active.'); }
        
        $products = wc_get_products(['limit' => -1, 'status' => 'any']);
        $count = 0;
        
        foreach ($products as $product) {
            $downloads = $product->get_downloads();
            $updated = false;
            
            if ($downloads) {
                foreach ($downloads as $download_id => $file) {
                    $old_url = $file->get_file();
                    if (strpos($old_url, 'cmrindia.com') !== false) {
                        $new_url = str_replace(
                            ['https://cmrindia.com', 'http://cmrindia.com'], 
                            'https://qai8358l95-staging.onrocket.site', 
                            $old_url
                        );
                        $file->set_file($new_url);
                        $updated = true;
                    }
                }
                
                if ($updated) {
                    $product->set_downloads($downloads);
                    $product->save();
                    $count++;
                }
            }
        }
        
        die("Successfully updated domains from cmrindia.com to qai8358l95-staging in $count WooCommerce products.");
    }
}

// Custom WooCommerce Checkout Fields
add_filter('woocommerce_checkout_fields', 'cmr_custom_checkout_fields');
function cmr_custom_checkout_fields($fields) {
    // Modify Billing Fields
    $fields['billing']['billing_first_name']['placeholder'] = 'First';
    $fields['billing']['billing_first_name']['label'] = 'NAME';
    $fields['billing']['billing_first_name']['class'] = array('form-row-first');
    
    $fields['billing']['billing_last_name']['placeholder'] = 'Last';
    $fields['billing']['billing_last_name']['label'] = ''; // Hide label to align with First Name
    $fields['billing']['billing_last_name']['class'] = array('form-row-last');
    
    $fields['billing']['billing_address_1']['placeholder'] = 'Street address, P.O. box, company name';
    $fields['billing']['billing_address_1']['label'] = 'ADDRESS';
    $fields['billing']['billing_address_1']['class'] = array('form-row-wide');
    
    // Remove Address 2 and Company
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_company']);
    
    $fields['billing']['billing_city']['placeholder'] = 'City';
    $fields['billing']['billing_city']['label'] = 'LOCATION';
    $fields['billing']['billing_city']['class'] = array('form-row-first');
    
    $fields['billing']['billing_state']['placeholder'] = 'State';
    $fields['billing']['billing_state']['label'] = ''; // Hide label
    $fields['billing']['billing_state']['class'] = array('form-row-last');
    
    $fields['billing']['billing_country']['placeholder'] = 'Country';
    $fields['billing']['billing_country']['label'] = ''; // Hide label
    $fields['billing']['billing_country']['class'] = array('form-row-first');
    
    $fields['billing']['billing_postcode']['placeholder'] = 'Pincode';
    $fields['billing']['billing_postcode']['label'] = ''; // Hide label
    $fields['billing']['billing_postcode']['class'] = array('form-row-last');
    
    $fields['billing']['billing_email']['placeholder'] = 'alexander@botanical.com';
    $fields['billing']['billing_email']['label'] = 'EMAIL ADDRESS';
    $fields['billing']['billing_email']['class'] = array('form-row-wide');
    
    $fields['billing']['billing_phone']['placeholder'] = '00000-00000';
    $fields['billing']['billing_phone']['label'] = 'PHONE NUMBER';
    $fields['billing']['billing_phone']['class'] = array('form-row-wide');

    return $fields;
}

// Override locale JS rules so WooCommerce address-i18n script doesn't overwrite our labels or layout
add_filter('woocommerce_get_country_locale', 'cmr_override_country_locale');
function cmr_override_country_locale($locales) {
    foreach ($locales as $country => $fields) {
        if (isset($locales[$country]['address_1'])) {
            $locales[$country]['address_1']['label'] = 'ADDRESS';
            $locales[$country]['address_1']['priority'] = 30;
        }
        if (isset($locales[$country]['city'])) {
            $locales[$country]['city']['label'] = 'LOCATION';
            $locales[$country]['city']['priority'] = 40;
        }
        if (isset($locales[$country]['state'])) {
            $locales[$country]['state']['label'] = '';
            $locales[$country]['state']['priority'] = 50;
        }
        if (isset($locales[$country]['postcode'])) {
            $locales[$country]['postcode']['label'] = '';
            $locales[$country]['postcode']['priority'] = 70;
        }
    }
    return $locales;
}

// Change "Place Order" button text and HTML
add_filter('woocommerce_order_button_text', 'cmr_custom_order_button_text');
function cmr_custom_order_button_text() {
    return 'Proceed to Checkout'; 
}

add_filter('woocommerce_order_button_html', 'cmr_custom_order_button_html');
function cmr_custom_order_button_html($button) {
    return '<button type="submit" class="button alt cmr-place-order-btn" name="woocommerce_checkout_place_order" id="place_order" value="Proceed to Checkout" data-value="Proceed to Checkout"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px; vertical-align:middle;"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg> Proceed to Checkout</button>';
}

// Force shipping to billing address only (removes shipping fields)
add_filter('wc_ship_to_billing_address_only', '__return_true');

// Redirect to cart immediately after adding to cart
add_filter('woocommerce_add_to_cart_redirect', 'cmr_redirect_to_cart_after_add');
function cmr_redirect_to_cart_after_add() {
    return wc_get_cart_url();
}

// Enqueue custom checkout CSS with cache busting
add_action('wp_enqueue_scripts', 'cmr_enqueue_checkout_css', 99);
function cmr_enqueue_checkout_css() {
    if (is_checkout() || is_cart()) {
        wp_enqueue_style('cmr-custom-checkout', get_template_directory_uri() . '/assets/css/custom-checkout.css', array(), time());
    }
}

// Ensure cart fragment updates via AJAX
add_filter('woocommerce_add_to_cart_fragments', 'cmr_cart_count_fragments', 10, 1);
function cmr_cart_count_fragments($fragments) {
    ob_start();
    ?>
    <span class="cmr-cart-count" style="background:#4820B0; color:#fff; border-radius:50%; padding:2px 6px; font-size:11px; font-weight:700; line-height:1; min-width:18px; text-align:center;"><?php echo WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?></span>
    <?php
    $fragments['span.cmr-cart-count'] = ob_get_clean();
    return $fragments;
}

// Universal JS to place Cart Icon neatly inside the right-side header action container (.elementor-element-219e18d) and next to mobile toggle
add_action('wp_footer', 'cmr_header_cart_container_injection', 99);
function cmr_header_cart_container_injection() {
    $cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    $cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '/cart/';
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Remove any old li cart menu items if present
        document.querySelectorAll('.cmr-cart-menu-item').forEach(function(el) { el.remove(); });
        
        var cartHtml = '<a href="<?php echo esc_url($cart_url); ?>" class="cmr-cart-icon-link" style="display:inline-flex; align-items:center; gap:5px; text-decoration:none; color:#111; padding:7px 10px; border-radius:24px; background:#f3f4f6; font-weight:600; font-size:13px; transition:all 0.2s; border:1px solid #e5e7eb; height:40px; box-sizing:border-box;" aria-label="Cart">' +
            '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>' +
            '<span class="cmr-cart-count" style="background:#4820B0; color:#fff; border-radius:50%; padding:2px 6px; font-size:11px; font-weight:700; line-height:1; min-width:18px; text-align:center;"><?php echo esc_js($cart_count); ?></span>' +
        '</a>';

        // 1. Desktop: Place inside .elementor-element-219e18d or .elementor-element-200fa94 right before download-btn or at start of container
        var rightContainers = document.querySelectorAll('.elementor-element-219e18d, .elementor-element-200fa94, .elementor-element-c3cee6b, .quanto-header-right, .header-right-action');
        rightContainers.forEach(function(container) {
            if (!container.querySelector('.cmr-header-cart-widget')) {
                var widget = document.createElement('div');
                widget.className = 'cmr-header-cart-widget';
                widget.style.cssText = 'display:inline-flex; align-items:center; margin-right:12px; z-index:99;';
                widget.innerHTML = cartHtml;
                
                var downloadBtn = container.querySelector('.download-btn, .elementor-widget-button');
                if (downloadBtn) {
                    container.insertBefore(widget, downloadBtn);
                } else {
                    container.insertBefore(widget, container.firstChild);
                }
            }
        });

        // 2. Mobile/Tablet fallback: Place right before menuBar-toggle
        var mobileToggles = document.querySelectorAll('.menuBar-toggle, .quanto-menu-toggle');
        mobileToggles.forEach(function(toggle) {
            if (!toggle.parentNode.querySelector('.cmr-mobile-cart-widget')) {
                var widget = document.createElement('div');
                widget.className = 'cmr-mobile-cart-widget d-inline-block d-lg-none';
                widget.style.cssText = 'display:inline-flex; align-items:center; margin-right:10px; vertical-align:middle;';
                widget.innerHTML = '<a href="<?php echo esc_url($cart_url); ?>" class="cmr-cart-icon-link" style="display:flex; align-items:center; gap:4px; text-decoration:none; color:#111; padding:5px 10px; border-radius:20px; background:#f3f4f6; font-weight:600; font-size:12px; border:1px solid #e5e7eb; height:40px; box-sizing:border-box;">' +
                    '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>' +
                    '<span class="cmr-cart-count" style="background:#4820B0; color:#fff; border-radius:50%; padding:2px 6px; font-size:11px; font-weight:700; line-height:1; min-width:18px; text-align:center;"><?php echo esc_js($cart_count); ?></span>' +
                '</a>';
                toggle.parentNode.insertBefore(widget, toggle);
            }
        });
        
        // Target the Person / Account icon in the header and set its URL to the My Account page
        // so it works with the newly added login plugin.
        var accountUrl = '<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>';
        var iconLinks = document.querySelectorAll('.elementor-widget-icon a, .cmr-header-user-icon');
        iconLinks.forEach(function(link) {
            var html = link.innerHTML.toLowerCase();
            // Check if the icon contains 'user', 'person', or related paths
            if (html.includes('fa-user') || html.includes('person') || html.includes('user') || html.includes('cx="12" cy="7"')) {
                // cx="12" cy="7" is a common SVG coordinate for a user head circle
                link.setAttribute('href', accountUrl);
                // Also add a class in case the login plugin targets it
                link.classList.add('lrm-login', 'xoo-el-login-tgr'); 
            }
        });
    });
    </script>
    <?php
}

// Redirect shop page to research reports page
add_action( 'template_redirect', 'quanto_redirect_shop_to_research_reports' );
function quanto_redirect_shop_to_research_reports() {
    if ( function_exists('is_shop') && is_shop() && ! is_search() ) {
        wp_safe_redirect( home_url( '/research-reports/' ) );
        exit;
    }
}

// Force Elementor to regenerate the CSS files for our global shortcode tabs 
// whenever Elementor clears its cache, so they are never missing on the frontend.
// CRITICAL: Also delete the footer HTML transient so the footer shortcode
// re-fetches from ?quanto_footer=main on the next page load, which triggers
// Elementor to regenerate the CSS files as a side effect.
add_action( 'elementor/core/files/clear_cache', 'cmr_regenerate_tab_css_on_cache_clear' );
function cmr_regenerate_tab_css_on_cache_clear() {
    // Delete the cached footer HTML so it gets re-fetched with fresh CSS links
    delete_transient( 'cmr_footer_html_cache' );
    
    $tab_slugs = array(
        'your-challenge-our-research-your-advantage',
        'fotter-card',
        'testimonials',
        'we-worked-with-largest-global-brands',
        'your-next-big-decision-deserves-better-intelligence'
    );
    foreach ( $tab_slugs as $slug ) {
        $tab_posts = get_posts(array(
            'name'           => $slug,
            'post_type'      => 'quanto_tab_build',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
        ));
        if ( $tab_posts && !empty($tab_posts[0]) ) {
            $post_id = $tab_posts[0]->ID;
            if ( class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) && class_exists( '\\Elementor\\Plugin' ) ) {
                $document = \Elementor\Plugin::$instance->documents->get( $post_id );
                if ( $document ) {
                    $css_file = new \Elementor\Core\Files\CSS\Post( $post_id );
                    $css_file->update();
                }
            }
        }
    }
}

// Also delete the footer transient whenever post cache or object cache is flushed
add_action( 'wp_cache_flush', function() {
    delete_transient( 'cmr_footer_html_cache' );
});

// Also delete the footer transient when any quanto_footer post is saved
add_action( 'save_post_quanto_footer', function() {
    delete_transient( 'cmr_footer_html_cache' );
});

// Also delete the footer transient when any quanto_tab_build post is saved
add_action( 'save_post_quanto_tab_build', function() {
    delete_transient( 'cmr_footer_html_cache' );
});

/**
 * Pre-warm Elementor CSS caches by hitting all the builder URLs in the background.
 * Uses non-blocking HTTP requests so it doesn't slow down the user experience.
 */
function cmr_prewarm_elementor_caches() {
    $urls = array(
        home_url('/?quanto_tab_build=your-next-big-decision-deserves-better-intelligence'),
        home_url('/?quanto_tab_build=we-worked-with-largest-global-brands'),
        home_url('/?quanto_tab_build=testimonials'),
        home_url('/?quanto_tab_build=fotter-card'),
        home_url('/?quanto_tab_build=your-challenge-our-research-your-advantage'),
        home_url('/?quanto_tab_build=who-we-serve'),
        home_url('/?quanto_footer=main')
    );

    foreach ( $urls as $url ) {
        wp_remote_get( $url, array(
            'timeout'   => 0.01,
            'blocking'  => false,
            'sslverify' => false,
        ) );
    }
}

// Trigger pre-warming when Elementor cache is cleared
add_action( 'elementor/core/files/clear_cache', 'cmr_prewarm_elementor_caches' );

// Trigger pre-warming when a new user registers
add_action( 'user_register', 'cmr_prewarm_elementor_caches' );

// Trigger pre-warming when any WordPress cache is flushed
add_action( 'wp_cache_flush', 'cmr_prewarm_elementor_caches' );

/**
 * Remove <br> tags from WooCommerce My Account navigation link labels.
 */
add_filter( 'woocommerce_account_menu_items', function( $items ) {
    foreach ( $items as $endpoint => $label ) {
        // Strip out any <br>, <br/>, or <br /> tags from the label text
        $items[$endpoint] = str_ireplace( array( '<br>', '<br/>', '<br />', "\n", "\r", "\t" ), '', $label );
        // Also trim extra whitespace that might have been left
        $items[$endpoint] = trim( $items[$endpoint] );
    }
    return $items;
}, 999 );
