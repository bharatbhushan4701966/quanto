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
    ob_start();
    ?>
    <div class="breadcumb-menu-wrap" style="margin-bottom: 20px; padding: 0;">
        <div class="breadcumb-menu">
            <ul class="justify-content-center" style="margin:0; padding:0; display:flex; align-items:center; list-style:none; justify-content: center;">
                <li><a href="<?php echo esc_url( home_url('/') ); ?>" title="Home" style="color: #666; font-weight: 500; font-size: 12px; text-decoration:none;">Home</a></li>
                <span class="arrow" style="margin: 0 10px; color: #666; font-size:12px;"><i class="fa-solid fa-angle-right"></i></span>
                <li class="active" title="Media Releases" style="color: #111; font-weight: 500; font-size: 12px;">Media Releases</li>
            </ul>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// CMR News & Media Releases Feature
require_once QUANTO_DIR_PATH_INC . 'cmr-news.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-media-coverage.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-news-carousel.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-press-releases.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-spotlight.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-media-contacts.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-press-releases.php';
require_once QUANTO_DIR_PATH_INC . 'author-meta.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-latest-insights.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-industry-intelligence.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-explore-sectors.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-stay-updated.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-featured-insight.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-featured-intelligence-carousel.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-industry-intel-list.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-industry-intelligence-trends.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-breadcrumbs.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-dark-media-coverage.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-author-sync.php';
require_once QUANTO_DIR_PATH_INC . 'cmr-post-sync.php';
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

    <div class="cmr-market-updates-section">
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
            
            $mu_query = new WP_Query($mu_args);
            
            if ($mu_query->have_posts()) :
                while ($mu_query->have_posts()) : $mu_query->the_post();
                    $date = get_the_time('M d | h:i A');
                    $categories = get_the_category();
                    $cat_name = '';
                    $cat_class = 'policy'; // Consistent default color class
                    if ( ! empty( $categories ) ) {
                        $cat_name = esc_html( $categories[0]->name );
                        // We removed random color classes so the same category always looks consistent.
                    }
            ?>
            <a href="<?php echo esc_url(get_permalink()); ?>" class="cmr-mu-item">
                <div class="cmr-mu-item-content">
                    <div class="cmr-mu-meta">
                        <span class="cmr-mu-date"><?php echo $date; ?></span>
                        <?php if ( $cat_name ) : ?>
                            <span class="cmr-mu-category <?php echo $cat_class; ?>"><?php echo $cat_name; ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="cmr-mu-item-title"><?php echo get_the_title(); ?></h3>
                </div>
                <div class="cmr-mu-arrow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </div>
            </a>
            <?php 
                endwhile; 
                wp_reset_postdata();
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
        wp_reset_postdata();
    }
    
    return new WP_REST_Response(array(
        'success' => true,
        'migrated_count' => $migrated,
        'log' => $log
    ), 200);
}
