<?php
/**
 * CMR Media Coverage Page Template Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Register Shortcode
add_shortcode( 'cmr_media_coverage', 'cmr_render_media_coverage_shortcode' );

function cmr_render_media_coverage_shortcode( $atts ) {
    // Enqueue scripts and styles
    wp_enqueue_style( 'cmr-media-coverage-style', get_template_directory_uri() . '/assets/css/cmr-media-coverage.css', array(), time() );
    wp_enqueue_script( 'cmr-media-coverage-script', get_template_directory_uri() . '/assets/js/cmr-media-coverage.js', array('jquery'), time(), true );
    
    // Pass ajax_url to JS
    wp_localize_script( 'cmr-media-coverage-script', 'cmr_mc_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' )
    ) );

    // Fetch distinct publishers
    global $wpdb;
    $publishers = $wpdb->get_col("
        SELECT DISTINCT meta_value 
        FROM {$wpdb->postmeta} 
        WHERE meta_key = '_cmr_news_publisher_name' 
        AND meta_value != ''
        ORDER BY meta_value ASC
    ");

    ob_start();
    ?>
    <style>
    /* Sticky Intel Nav Bar for Media Coverage - Desktop */
    @media (min-width: 769px) {
        .cmr-mc-wrapper .intel-nav-bar {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
            min-height: 52px !important;
            background: #ffffff !important;
            border-bottom: 1px solid #eeeeee !important;
            margin-bottom: 35px !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        }

        .cmr-mc-wrapper .intel-nav-title {
            font-size: 16px !important;
            font-weight: 600 !important;
            color: #111111 !important;
        }

        .cmr-mc-wrapper .intel-nav-links {
            display: flex !important;
            gap: 25px !important;
            align-items: center !important;
        }

        .cmr-mc-wrapper .intel-nav-links a {
            text-decoration: none !important;
            color: #333333 !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            transition: color 0.2s ease !important;
        }

        .cmr-mc-wrapper .intel-nav-links a:hover,
        .cmr-mc-wrapper .intel-nav-links a.active {
            color: #5842c3 !important;
        }
    }

    /* Filters Row & Scrollable Chips */
    .cmr-mc-filters-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 35px;
        gap: 20px;
        width: 100%;
        box-sizing: border-box;
    }

    .cmr-mc-pills {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .cmr-mc-pill {
        background: transparent;
        border: 1px solid #E2E8F0;
        border-radius: 40px;
        padding: 8px 24px;
        font-size: 14px;
        font-weight: 500;
        color: #111111;
        cursor: pointer;
        transition: all 0.25s ease;
        outline: none;
    }

    .cmr-mc-pill:hover {
        border-color: #5842c3;
        color: #5842c3;
    }

    .cmr-mc-pill.active {
        background: #5842c3 !important;
        border-color: #5842c3 !important;
        color: #ffffff !important;
    }

    /* Search Bar */
    .cmr-mc-search {
        position: relative;
        width: 320px;
        flex-shrink: 0;
    }

    .cmr-mc-search input {
        width: 100%;
        height: 48px;
        border-radius: 40px;
        border: 1px solid #E2E8F0;
        padding: 10px 50px 10px 22px;
        font-size: 14.5px;
        color: #111111;
        background: #ffffff;
        box-sizing: border-box;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .cmr-mc-search input:focus {
        border-color: #5842c3;
    }

    .cmr-mc-search-icon {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #5842c3;
        display: flex;
        justify-content: center;
        align-items: center;
        pointer-events: none;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .cmr-mc-wrapper {
            padding-left: 16px !important;
            padding-right: 16px !important;
            box-sizing: border-box !important;
        }

        .cmr-mc-wrapper .intel-nav-bar:not(.intel-nav-fixed-js) {
            margin-left: -16px !important;
            margin-right: -16px !important;
            width: calc(100% + 32px) !important;
            padding: 0 16px !important;
            margin-bottom: 25px !important;
        }

        .cmr-mc-header {
            text-align: center !important;
            margin-bottom: 25px !important;
        }

        .cmr-mc-title {
            font-size: 28px !important;
            line-height: 1.25 !important;
            letter-spacing: -0.5px !important;
            margin-bottom: 10px !important;
            text-align: center !important;
        }

        .cmr-mc-subtitle {
            font-size: 14px !important;
            line-height: 1.5 !important;
            color: #475569 !important;
            text-align: center !important;
            margin: 0 auto !important;
        }

        .cmr-mc-filters-row {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 16px !important;
            margin-bottom: 25px !important;
            width: 100% !important;
        }

        /* Horizontal Scrollable Chips on Mobile */
        .cmr-mc-pills {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: none !important;
            gap: 10px !important;
            width: 100% !important;
            padding-bottom: 4px !important;
            margin: 0 !important;
        }

        .cmr-mc-pills::-webkit-scrollbar {
            display: none !important;
        }

        .cmr-mc-pill {
            flex-shrink: 0 !important;
            white-space: nowrap !important;
            padding: 8px 22px !important;
            font-size: 14px !important;
        }

        /* Full Width Search Bar on Mobile */
        .cmr-mc-search {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
        }

        .cmr-mc-search input {
            width: 100% !important;
            height: 48px !important;
            border-radius: 40px !important;
            padding: 10px 50px 10px 22px !important;
        }

        /* Grid & Cards on Mobile */
        .cmr-mc-wrapper .cmr-mc-grid,
        .cmr-media-coverage-wrapper .cmr-mc-grid,
        .cmr-media-coverage-wrapper .cmr-mc-grid-inner {
            display: flex !important;
            flex-direction: column !important;
            gap: 24px !important;
            width: 100% !important;
        }

        /* Featured Card Mobile */
        .cmr-mc-wrapper .cmr-mc-featured,
        .cmr-media-coverage-wrapper .cmr-mc-featured {
            grid-column: span 12 !important;
            width: 100% !important;
            margin-bottom: 0 !important;
        }

        .cmr-mc-wrapper .cmr-mc-featured .cmr-mc-link-wrapper,
        .cmr-media-coverage-wrapper .cmr-mc-featured-inner {
            display: flex !important;
            flex-direction: column !important;
            gap: 16px !important;
            width: 100% !important;
        }

        .cmr-mc-wrapper .cmr-mc-featured .cmr-mc-image-wrap,
        .cmr-media-coverage-wrapper .cmr-mc-featured-image {
            flex: none !important;
            width: 100% !important;
            max-width: 100% !important;
            height: 220px !important;
            min-height: 0 !important;
            border-radius: 12px !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .cmr-mc-wrapper .cmr-mc-featured .cmr-mc-image-wrap .cmr-mc-bg,
        .cmr-media-coverage-wrapper .cmr-mc-featured-image .cmr-mc-bg {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        .cmr-mc-wrapper .cmr-mc-featured .cmr-mc-content,
        .cmr-media-coverage-wrapper .cmr-mc-featured-content {
            flex: none !important;
            width: 100% !important;
            padding: 0 !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .cmr-mc-wrapper .cmr-mc-featured .cmr-mc-meta,
        .cmr-media-coverage-wrapper .cmr-mc-featured .cmr-mc-meta {
            font-size: 12px !important;
            color: #64748b !important;
            margin-bottom: 8px !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        .cmr-mc-wrapper .cmr-mc-featured .cmr-mc-title,
        .cmr-media-coverage-wrapper .cmr-mc-featured .cmr-mc-title {
            font-size: 18px !important;
            font-weight: 600 !important;
            line-height: 1.35 !important;
            color: #111111 !important;
            margin: 0 0 10px 0 !important;
        }

        .cmr-mc-wrapper .cmr-mc-featured .cmr-mc-excerpt,
        .cmr-media-coverage-wrapper .cmr-mc-featured .cmr-mc-excerpt {
            font-size: 13px !important;
            line-height: 1.5 !important;
            color: #475569 !important;
            margin: 0 0 14px 0 !important;
        }

        .cmr-mc-wrapper .cmr-mc-featured .cmr-mc-read-coverage,
        .cmr-media-coverage-wrapper .cmr-mc-featured .cmr-mc-view-btn {
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #111111 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            text-decoration: none !important;
            border-bottom: 1px solid #111111 !important;
            padding-bottom: 2px !important;
            align-self: flex-start !important;
            margin-top: 4px !important;
        }

        /* Standard Cards Mobile */
        .cmr-mc-wrapper .cmr-mc-standard,
        .cmr-media-coverage-wrapper .cmr-mc-card {
            grid-column: span 12 !important;
            width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .cmr-mc-wrapper .cmr-mc-standard .cmr-mc-link-wrapper,
        .cmr-media-coverage-wrapper .cmr-mc-card-inner {
            display: flex !important;
            flex-direction: column !important;
            gap: 14px !important;
            width: 100% !important;
        }

        .cmr-mc-wrapper .cmr-mc-standard .cmr-mc-image-wrap,
        .cmr-media-coverage-wrapper .cmr-mc-card-image {
            width: 100% !important;
            height: 200px !important;
            aspect-ratio: auto !important;
            border-radius: 12px !important;
            position: relative !important;
            overflow: hidden !important;
            margin-bottom: 0 !important;
        }

        .cmr-mc-wrapper .cmr-mc-standard .cmr-mc-image-wrap .cmr-mc-bg,
        .cmr-media-coverage-wrapper .cmr-mc-card-image .cmr-mc-bg {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        .cmr-mc-wrapper .cmr-mc-standard .cmr-mc-content,
        .cmr-media-coverage-wrapper .cmr-mc-card-content {
            width: 100% !important;
            padding: 0 !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .cmr-mc-wrapper .cmr-mc-standard .cmr-mc-meta,
        .cmr-media-coverage-wrapper .cmr-mc-card .cmr-mc-meta-row {
            font-size: 11.5px !important;
            color: #64748b !important;
            margin-bottom: 8px !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }

        .cmr-mc-wrapper .cmr-mc-standard .cmr-mc-title,
        .cmr-media-coverage-wrapper .cmr-mc-card .cmr-mc-title {
            font-size: 15.5px !important;
            font-weight: 600 !important;
            line-height: 1.4 !important;
            color: #111111 !important;
            margin: 0 0 14px 0 !important;
        }

        .cmr-mc-wrapper .cmr-mc-standard .cmr-mc-read-coverage,
        .cmr-media-coverage-wrapper .cmr-mc-card .cmr-mc-view-btn {
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #111111 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            text-decoration: none !important;
            border-bottom: 1px solid #111111 !important;
            padding-bottom: 2px !important;
            align-self: flex-start !important;
            margin-top: auto !important;
        }
    }
    </style>

    <div class="cmr-mc-wrapper" id="cmr-in-news">
        <div class="intel-nav-bar cmr-mc-top-nav">
            <div class="intel-nav-title">
                CMR in News
            </div>
            <div class="intel-nav-links">
                <a href="#featured">Featured</a>
                <a href="#latest-updates">Latest Updates</a>
                <a href="#press-release">Press Release</a>
                <a href="#cmr-live">CMR Live</a>
                <a href="#reports">Reports</a>
                <a href="#media-contacts">Media Contacts</a>

                <a href="#cmr-footer-card-section" class="cmr-nav-btn-subscribe" style="display: none; align-items: center; justify-content: center; background: #fff; color: #111; font-weight: 600; font-size: 14px; padding: 8px 16px; border-radius: 40px; text-decoration: none; border: 1px solid #111; margin-left: 15px; line-height: 1; transition: all 0.3s ease;">
                    Subscribe now
                    <svg style="margin-left: 6px;" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
        </div>

        <div class="cmr-mc-header">
            <h1 class="cmr-mc-title">CMR Media Coverage</h1>
            <p class="cmr-mc-subtitle">Track emerging shifts, growth signals, and market movements in real time.</p>
        </div>

        <div class="cmr-mc-filters-row">
            <div class="cmr-mc-pills">
                <button class="cmr-mc-pill active" data-publisher="">All</button>
                <?php 
                foreach ( $publishers as $pub ) {
                    echo '<button class="cmr-mc-pill" data-publisher="' . esc_attr( $pub ) . '">' . esc_html( $pub ) . '</button>';
                }
                ?>
            </div>
            <div class="cmr-mc-search">
                <input type="text" id="cmr-mc-search-input" placeholder="Search by name">
                <div class="cmr-mc-search-icon">
                    <svg width="15" height="15" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="7" cy="7" r="5" stroke="white" stroke-width="2"/>
                        <path d="M11 11L14 14" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="cmr-mc-grid" id="cmr-mc-grid-container">
            <!-- Initial posts will be loaded here via AJAX -->
            <div class="cmr-mc-loading">Loading...</div>
        </div>

        <div class="cmr-mc-footer">
            <button id="cmr-mc-load-more" class="cmr-mc-load-more" style="display:none;">Load More</button>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// 2. AJAX Handler for Fetching Posts
add_action( 'wp_ajax_cmr_filter_media_coverage', 'cmr_ajax_filter_media_coverage' );
add_action( 'wp_ajax_nopriv_cmr_filter_media_coverage', 'cmr_ajax_filter_media_coverage' );

function cmr_ajax_filter_media_coverage() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $publisher = isset( $_POST['publisher'] ) ? sanitize_text_field( $_POST['publisher'] ) : '';
    $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';

    $args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 7, // 1 featured + 6 standard (2 rows) per page
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
        'tax_query'      => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'cmr_news_category',
                'field'    => 'slug',
                'terms'    => array('media-releases', 'media-release', 'media_releases', 'media_release', 'press-releases', 'press-release', 'pressreleases', 'press-releases-2', 'press-release-2'),
                'operator' => 'NOT IN'
            ),
        ),
    );

    // Apply publisher filter
    if ( ! empty( $publisher ) ) {
        $args['meta_query'] = array(
            array(
                'key'     => '_cmr_news_publisher_name',
                'value'   => $publisher,
                'compare' => '='
            )
        );
    }

    // Apply search
    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }

    $query = new WP_Query( $args );
    
    ob_start();

    if ( $query->have_posts() ) {
        $count = 0;
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $bg_image = get_the_post_thumbnail_url( $post_id, 'large' );
            $logo_id = get_post_meta( $post_id, '_cmr_news_source_logo_id', true );
            $logo_url = $logo_id ? wp_get_attachment_url( $logo_id ) : '';
            $document_id = get_post_meta( $post_id, '_cmr_news_document_id', true );
            $ext_url = get_post_meta( $post_id, '_cmr_news_external_link', true );
            if ( $document_id ) {
                $link = wp_get_attachment_url( $document_id );
                $target = '_blank';
            } elseif ( $ext_url ) {
                $link = $ext_url;
                $target = '_blank';
            } else {
                $link = get_permalink( $post_id );
                $target = '_self';
            }
            $reading_time = get_post_meta( $post_id, '_cmr_news_reading_time', true );
            $publisher_name = get_post_meta( $post_id, '_cmr_news_publisher_name', true );
            $date = get_the_date( 'M j, Y' );

            // If it's page 1 and the very first item, render the featured layout
            if ( $paged === 1 && $count === 0 ) {
                ?>
                <div class="cmr-mc-card cmr-mc-featured">
                    <a href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="cmr-mc-link-wrapper">
                        <div class="cmr-mc-image-wrap">
                            <?php if ( $bg_image ) : ?>
                                <img src="<?php echo esc_url( $bg_image ); ?>" class="cmr-mc-bg" alt="<?php the_title_attribute(); ?>">
                            <?php endif; ?>
                            <?php if ( $logo_url ) : ?>
                                <img src="<?php echo esc_url( $logo_url ); ?>" class="cmr-mc-logo" alt="Source Logo">
                            <?php endif; ?>
                            <span class="cmr-mc-trending-tag">✦ TRENDING</span>
                        </div>
                        <div class="cmr-mc-content">
                            <div class="cmr-mc-meta">
                                <?php if ( $publisher_name ) : ?>
                                    <span class="cmr-mc-publisher"><?php echo esc_html( $publisher_name ); ?></span> <span class="cmr-mc-separator">|</span> 
                                <?php endif; ?>
                                <span class="cmr-mc-date">Published <?php echo esc_html( $date ); ?></span>
                            </div>
                            <h2 class="cmr-mc-title"><?php the_title(); ?></h2>
                            <?php if ( has_excerpt() ) : ?>
                                <p class="cmr-mc-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 25 ); ?></p>
                            <?php endif; ?>
                            <span class="cmr-mc-read-coverage">View Coverage <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Symbol-1.svg" class="cmr-mc-arrow" alt=""></span>
                        </div>
                    </a>
                </div>
                <?php
            } else {
                // Render standard card
                ?>
                <div class="cmr-mc-card cmr-mc-standard">
                    <a href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="cmr-mc-link-wrapper">
                        <div class="cmr-mc-image-wrap">
                            <?php if ( $bg_image ) : ?>
                                <img src="<?php echo esc_url( $bg_image ); ?>" class="cmr-mc-bg" alt="<?php the_title_attribute(); ?>">
                            <?php endif; ?>
                            <?php if ( $logo_url ) : ?>
                                <img src="<?php echo esc_url( $logo_url ); ?>" class="cmr-mc-logo" alt="Source Logo">
                            <?php endif; ?>
                        </div>
                        <div class="cmr-mc-content">
                            <div class="cmr-mc-meta">
                                <div class="cmr-mc-meta-left">
                                    <?php if ( $publisher_name ) : ?>
                                        <span class="cmr-mc-publisher"><?php echo esc_html( $publisher_name ); ?></span> <span class="cmr-mc-separator">|</span> 
                                    <?php endif; ?>
                                    <span class="cmr-mc-date">Published <?php echo esc_html( $date ); ?></span>
                                </div>
                                <?php if ( $reading_time ) : ?>
                                    <div class="cmr-mc-meta-right">
                                        <span class="cmr-mc-read-time"><?php echo esc_html( $reading_time ); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h3 class="cmr-mc-title"><?php the_title(); ?></h3>
                            <span class="cmr-mc-read-coverage">View Coverage <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Symbol-1.svg" class="cmr-mc-arrow" alt=""></span>
                        </div>
                    </a>
                </div>
                <?php
            }
            $count++;
        }
    } else {
        if ( $paged === 1 ) {
            echo '<p class="cmr-mc-no-results">No articles found matching your criteria.</p>';
        }
    }
    
    $html = ob_get_clean();

    $has_more = ( $query->max_num_pages > $paged );

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}
