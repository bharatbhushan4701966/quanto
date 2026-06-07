<?php
/**
 * Shortcode for Dark Media Coverage Section
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_dark_media_coverage_shortcode' ) ) {
    function cmr_dark_media_coverage_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'posts_per_page' => 4,
        ), $atts );

        $query_args = array(
            'post_type'      => 'cmr_news',
            'posts_per_page' => $atts['posts_per_page'],
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
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

        $media_query = new WP_Query( $query_args );

        // Extract distinct publishers for tabs if we want them dynamic, or just hardcode some for the layout
        global $wpdb;
        $publishers = $wpdb->get_col("
            SELECT DISTINCT meta_value 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = '_cmr_news_publisher_name' 
            AND meta_value != ''
            ORDER BY meta_value ASC
            LIMIT 4
        ");

        if ( empty($publishers) ) {
            $publishers = array('CNN', 'Times of India', 'BBC News', 'Your Story');
        }

        ob_start();
        ?>
        <style>
            .cmr-dmc-wrapper {
                font-family: 'Instrument Sans', sans-serif !important;
                background-color: #111;
                color: #fff;
                padding: 40px 20px;
                border-radius: 12px;
                max-width: 1280px;
                margin: 50px auto;
            }
            .cmr-dmc-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 30px;
                flex-wrap: wrap;
                gap: 20px;
            }
            .cmr-dmc-filters {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
            }
            .cmr-dmc-filter-btn {
                background: transparent;
                border: 1px solid #333;
                color: #ccc;
                padding: 8px 24px;
                border-radius: 30px;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            .cmr-dmc-filter-btn:hover, .cmr-dmc-filter-btn.active {
                border-color: #fff;
                color: #fff;
            }
            .cmr-dmc-explore-all {
                color: #fff;
                text-decoration: none;
                font-size: 15px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border-bottom: 1px solid #fff;
                padding-bottom: 2px;
                transition: opacity 0.3s;
            }
            .cmr-dmc-explore-all:hover {
                opacity: 0.8;
            }

            /* Featured Section */
            .cmr-dmc-featured {
                position: relative;
                width: 100%;
                height: 500px;
                border-radius: 8px;
                overflow: hidden;
                margin-bottom: 20px;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
            }
            .cmr-dmc-featured img.cmr-dmc-featured-bg {
                position: absolute;
                top: 0; left: 0; width: 100%; height: 100%;
                object-fit: cover;
                z-index: 1;
            }
            .cmr-dmc-featured-overlay {
                position: absolute;
                top: 0; left: 0; width: 100%; height: 100%;
                background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 40%, transparent 100%);
                z-index: 2;
            }
            .cmr-dmc-featured-logo {
                position: absolute;
                top: 30px;
                left: 30px;
                width: 50px;
                height: 50px;
                object-fit: contain;
                z-index: 3;
                border-radius: 8px;
            }
            .cmr-dmc-featured-content {
                position: relative;
                z-index: 3;
                padding: 40px;
                max-width: 800px;
            }
            .cmr-dmc-meta {
                font-size: 13px;
                color: #ccc;
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .cmr-dmc-featured-title {
                font-size: 32px;
                font-weight: 700;
                color: #fff;
                line-height: 1.3;
                margin: 0 0 20px 0;
            }
            .cmr-dmc-read-link {
                color: #fff;
                font-size: 14px;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 5px;
                border-bottom: 1.5px solid #fff;
                padding-bottom: 2px;
                transition: opacity 0.3s;
            }
            .cmr-dmc-read-link:hover {
                opacity: 0.8;
            }
            .cmr-dmc-read-link svg {
                width: 12px; height: 12px;
            }

            /* Grid Section */
            .cmr-dmc-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }
            @media (max-width: 992px) {
                .cmr-dmc-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
            @media (max-width: 768px) {
                .cmr-dmc-grid {
                    grid-template-columns: 1fr;
                }
                .cmr-dmc-featured {
                    height: 400px;
                }
                .cmr-dmc-featured-title {
                    font-size: 24px;
                }
                .cmr-dmc-featured-content {
                    padding: 20px;
                }
                .cmr-dmc-featured-logo {
                    top: 20px; left: 20px;
                }
            }
            .cmr-dmc-card {
                display: flex;
                flex-direction: column;
            }
            .cmr-dmc-card-img-wrap {
                position: relative;
                width: 100%;
                aspect-ratio: 16 / 11;
                border-radius: 8px;
                overflow: hidden;
                margin-bottom: 15px;
            }
            .cmr-dmc-card-img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.3s ease;
            }
            .cmr-dmc-card-img-wrap:hover .cmr-dmc-card-img {
                transform: scale(1.05);
            }
            .cmr-dmc-card-logo {
                position: absolute;
                bottom: 15px;
                left: 15px;
                width: 45px;
                height: 45px;
                object-fit: contain;
                z-index: 2;
                border-radius: 6px;
            }
            .cmr-dmc-card-meta {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 13px;
                color: #aaa;
                margin-bottom: 10px;
            }
            .cmr-dmc-card-title {
                font-size: 18px;
                font-weight: 600;
                color: #fff;
                line-height: 1.4;
                margin: 0;
            }
            .cmr-dmc-card-title a {
                color: #fff;
                text-decoration: none;
                transition: opacity 0.3s;
            }
            .cmr-dmc-card-title a:hover {
                opacity: 0.8;
            }
        </style>

        <div class="cmr-dmc-wrapper">
            <div class="cmr-dmc-header">
                <div class="cmr-dmc-filters">
                    <button class="cmr-dmc-filter-btn active">All</button>
                    <?php foreach($publishers as $pub): ?>
                        <button class="cmr-dmc-filter-btn"><?php echo esc_html($pub); ?></button>
                    <?php endforeach; ?>
                </div>
                <a href="/media-coverage" class="cmr-dmc-explore-all">
                    Explore All 
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7 7 17 7 17 17"></polyline>
                    </svg>
                </a>
            </div>

            <?php if ( $media_query->have_posts() ) : ?>
                <?php 
                $count = 0;
                while ( $media_query->have_posts() ) : $media_query->the_post();
                    $post_id = get_the_ID();
                    $bg_image = get_the_post_thumbnail_url( $post_id, 'full' );
                    if (!$bg_image) {
                        $bg_image = 'https://via.placeholder.com/800x500?text=News';
                    }
                    $logo_id = get_post_meta( $post_id, '_cmr_news_source_logo_id', true );
                    $logo_url = $logo_id ? wp_get_attachment_url( $logo_id ) : '';
                    
                    $document_id = get_post_meta( $post_id, '_cmr_news_document_id', true );
                    $ext_url = get_post_meta( $post_id, '_cmr_news_external_link', true );
                    if ( $document_id ) {
                        $link = wp_get_attachment_url( $document_id );
                    } elseif ( $ext_url ) {
                        $link = $ext_url;
                    } else {
                        $link = get_permalink();
                    }

                    $reading_time = get_post_meta( $post_id, '_cmr_news_reading_time', true );
                    if (!$reading_time) $reading_time = '5 mins';
                    $publisher_name = get_post_meta( $post_id, '_cmr_news_publisher_name', true );
                    if (!$publisher_name) $publisher_name = 'CMR';
                    $date = get_the_date( 'M j, Y' );

                    if ($count === 0) {
                        // Render Featured
                        ?>
                        <div class="cmr-dmc-featured">
                            <img src="<?php echo esc_url($bg_image); ?>" class="cmr-dmc-featured-bg" alt="">
                            <div class="cmr-dmc-featured-overlay"></div>
                            
                            <?php if ($logo_url): ?>
                                <img src="<?php echo esc_url($logo_url); ?>" class="cmr-dmc-featured-logo" alt="">
                            <?php endif; ?>
                            
                            <div class="cmr-dmc-featured-content">
                                <div class="cmr-dmc-meta">
                                    <span class="cmr-dmc-publisher"><?php echo esc_html($publisher_name); ?></span> | 
                                    <span class="cmr-dmc-date">Published <?php echo esc_html($date); ?></span>
                                </div>
                                <h2 class="cmr-dmc-featured-title"><?php echo esc_html(get_the_title()); ?></h2>
                                <a href="<?php echo esc_url($link); ?>" class="cmr-dmc-read-link" target="_blank">
                                    Read Coverage 
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="7" y1="17" x2="17" y2="7"></line>
                                        <polyline points="7 7 17 7 17 17"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="cmr-dmc-grid">
                        <?php
                    } else {
                        // Render Grid items
                        ?>
                        <div class="cmr-dmc-card">
                            <a href="<?php echo esc_url($link); ?>" target="_blank">
                                <div class="cmr-dmc-card-img-wrap">
                                    <img src="<?php echo esc_url($bg_image); ?>" class="cmr-dmc-card-img" alt="">
                                    <?php if ($logo_url): ?>
                                        <img src="<?php echo esc_url($logo_url); ?>" class="cmr-dmc-card-logo" alt="">
                                    <?php endif; ?>
                                </div>
                            </a>
                            <div class="cmr-dmc-card-meta">
                                <span><?php echo esc_html($publisher_name); ?> | Published <?php echo esc_html($date); ?></span>
                                <span><?php echo esc_html($reading_time); ?></span>
                            </div>
                            <h3 class="cmr-dmc-card-title">
                                <a href="<?php echo esc_url($link); ?>" target="_blank"><?php echo esc_html(get_the_title()); ?></a>
                            </h3>
                        </div>
                        <?php
                    }

                    $count++;
                endwhile;
                
                if ($count > 1) {
                    echo '</div>'; // close grid
                }
            endif; wp_reset_postdata(); ?>
            
        </div>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_dark_media_coverage', 'cmr_dark_media_coverage_shortcode' );
