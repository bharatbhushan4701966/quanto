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
    <div class="cmr-mc-wrapper">
        <div class="cmr-mc-top-banner">
            <div class="cmr-mc-top-banner-title">CMR in News</div>
            <div class="cmr-mc-top-banner-links">
                <a href="#">Featured</a>
                <a href="#">Latest Updates</a>
                <a href="#">Press Release</a>
                <a href="#">CMR Live</a>
                <a href="#">Reports</a>
                <a href="#">Media Contacts</a>
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
                // Show up to 4 publishers, hide others in "More"
                $count = 0;
                $hidden_publishers = array();
                foreach ( $publishers as $pub ) {
                    if ( $count < 4 ) {
                        echo '<button class="cmr-mc-pill" data-publisher="' . esc_attr( $pub ) . '">' . esc_html( $pub ) . '</button>';
                    } else {
                        $hidden_publishers[] = $pub;
                    }
                    $count++;
                }
                ?>
                <?php if ( ! empty( $hidden_publishers ) ) : ?>
                <div class="cmr-mc-pill-dropdown">
                    <button class="cmr-mc-pill cmr-mc-more-btn">More <span class="cmr-mc-chevron">⌄</span></button>
                    <div class="cmr-mc-dropdown-content">
                        <?php foreach ( $hidden_publishers as $hpub ) : ?>
                            <button class="cmr-mc-dropdown-item" data-publisher="<?php echo esc_attr( $hpub ); ?>"><?php echo esc_html( $hpub ); ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="cmr-mc-search">
                <input type="text" id="cmr-mc-search-input" placeholder="Search by name">
                <div class="cmr-mc-search-icon">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                'terms'    => array('media-releases', 'media-release', 'media_releases', 'media_release'),
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
