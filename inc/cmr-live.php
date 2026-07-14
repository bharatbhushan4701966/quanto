<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! function_exists( 'cmr_live_shortcode' ) ) {
    function cmr_live_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'posts_per_page' => 10,
        ), $atts, 'cmr_live' );
        
        // Call the new shortcode function to return identical HTML
        return cmr_live_podcast_carousel_shortcode($atts);
    }
}
add_shortcode( 'cmr_live', 'cmr_live_shortcode' );

if ( ! function_exists( 'cmr_live_podcast_carousel_shortcode' ) ) {
    function cmr_live_podcast_carousel_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'posts_per_page' => 10,
        ), $atts, 'cmr_live_podcast_carousel' );

        $query_args = array(
            'post_type'      => 'cmr_media',
            'posts_per_page' => intval( $atts['posts_per_page'] ),
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        $live_query = new WP_Query( $query_args );
        $posts = $live_query->posts;

        ob_start();
        ?>
        <style>
            .cmr-podcast-carousel-section {
                background-color: #111;
                padding: 60px 0;
                color: #fff;
                font-family: inherit;
            }
            .cmr-podcast-carousel-inner {
                max-width: 1280px;
                margin: 0 auto;
                padding: 0 20px;
            }
            .cmr-podcast-breadcrumb {
                font-size: 12px;
                color: #888;
                margin-bottom: 20px;
            }
            .cmr-podcast-breadcrumb span {
                color: #fff;
            }
            .cmr-podcast-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-end;
                margin-bottom: 40px;
            }
            .cmr-podcast-title-area h2 {
                font-size: 42px;
                font-weight: 700;
                color: #fff;
                margin: 0 0 10px 0;
            }
            .cmr-podcast-title-area p {
                font-size: 16px;
                color: #aaa;
                margin: 0;
            }
            .cmr-podcast-pagination {
                display: flex;
                gap: 8px;
                justify-content: flex-end;
                position: relative;
                width: auto;
            }
            .cmr-podcast-pagination .swiper-pagination-bullet {
                width: 30px;
                height: 3px;
                background: #555;
                border-radius: 0;
                opacity: 1;
                margin: 0;
            }
            .cmr-podcast-pagination .swiper-pagination-bullet-active {
                background: #fff;
            }
            .cmr-podcast-card {
                display: flex;
                flex-direction: column;
                background: transparent;
                color: #fff;
                text-decoration: none;
            }
            .cmr-podcast-img-wrap {
                position: relative;
                width: 100%;
                aspect-ratio: 16/10;
                border-radius: 8px;
                overflow: hidden;
                margin-bottom: 20px;
                background: #222;
            }
            .cmr-podcast-img-wrap img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            .cmr-podcast-badge {
                position: absolute;
                top: 15px;
                left: 15px;
                background: #fff;
                color: #111;
                font-size: 11px;
                font-weight: 700;
                padding: 4px 8px;
                border-radius: 4px;
                z-index: 2;
            }
            .cmr-podcast-play-btn {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 50px;
                height: 50px;
                background: rgba(255, 255, 255, 0.3);
                backdrop-filter: blur(4px);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 2;
            }
            .cmr-podcast-play-btn svg {
                width: 20px;
                height: 20px;
                fill: #fff;
                margin-left: 3px;
            }
            .cmr-podcast-meta {
                font-size: 11px;
                font-weight: 700;
                color: #888;
                margin-bottom: 12px;
                letter-spacing: 0.5px;
                text-transform: uppercase;
            }
            .cmr-podcast-meta .type-podcast {
                color: #00d2ff;
            }
            .cmr-podcast-meta .type-topview {
                color: #2979ff;
            }
            .cmr-podcast-title {
                font-size: 20px;
                font-weight: 600;
                color: #fff;
                margin: 0 0 20px 0;
                line-height: 1.4;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .cmr-podcast-player {
                display: flex;
                align-items: center;
                gap: 15px;
                margin-top: auto;
            }
            .cmr-podcast-player .play-icon {
                width: 30px;
                height: 30px;
                border: 2px solid #fff;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .cmr-podcast-player .play-icon svg {
                width: 12px;
                height: 12px;
                fill: #fff;
                margin-left: 2px;
            }
            .cmr-podcast-player .waveform {
                flex: 1;
                height: 20px;
                background: repeating-linear-gradient(90deg, #555, #555 2px, transparent 2px, transparent 4px);
                opacity: 0.5;
            }
            .cmr-podcast-player .progress-bar-wrap {
                flex: 1;
                height: 4px;
                background: #555;
                border-radius: 2px;
                position: relative;
            }
            .cmr-podcast-player .progress-bar-inner {
                width: 30%;
                height: 100%;
                background: #fff;
                border-radius: 2px;
            }
            .cmr-podcast-player .time {
                font-size: 11px;
                color: #aaa;
                font-weight: 600;
            }
            .cmr-podcast-player .controls {
                display: flex;
                align-items: center;
                gap: 10px;
                color: #fff;
            }
            .cmr-podcast-player .controls svg {
                width: 16px;
                height: 16px;
                fill: none;
                stroke: currentColor;
                stroke-width: 2;
            }
            .cmr-podcast-player .volume {
                display: flex;
                align-items: center;
                gap: 5px;
            }
            .cmr-podcast-player .volume-bar {
                width: 40px;
                height: 4px;
                background: #fff;
                border-radius: 2px;
            }
            @media (max-width: 768px) {
                .cmr-podcast-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 20px;
                }
                .cmr-podcast-player .waveform, .cmr-podcast-player .progress-bar-wrap {
                    display: none;
                }
            }
        </style>

        <div class="cmr-podcast-carousel-section">
            <div class="cmr-podcast-carousel-inner">
                <div class="cmr-podcast-breadcrumb">
                    Home > <span>CMR Live</span>
                </div>
                
                <div class="cmr-podcast-header">
                    <div class="cmr-podcast-title-area">
                        <h2>CMR Live Podcast and Top View</h2>
                        <p>Browse premium research reports across industries, designed to deliver actionable insights and strategic clarity.</p>
                    </div>
                    <div class="cmr-podcast-nav-area" style="display: flex; align-items: center; gap: 15px;">
                        <div class="swiper-button-prev-custom" style="cursor:pointer; width:30px; height:30px; border-radius:50%; background:#222; display:flex; align-items:center; justify-content:center; color:#fff;">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </div>
                        <div class="cmr-podcast-pagination swiper-pagination-custom"></div>
                        <div class="swiper-button-next-custom" style="cursor:pointer; width:30px; height:30px; border-radius:50%; background:#222; display:flex; align-items:center; justify-content:center; color:#fff;">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </div>
                </div>

                <div class="swiper cmr-podcast-swiper">
                    <div class="swiper-wrapper">
                        <?php if ( ! empty($posts) ) : ?>
                            <?php 
                            foreach ( $posts as $post_obj ) : 
                                $thumbnail_url = get_the_post_thumbnail_url( $post_obj->ID, 'large' );
                                if ( ! $thumbnail_url ) {
                                    $thumbnail_url = 'https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/Why-Chipsets-are-the-New-Frontier-in-Smartphones1.jpg';
                                }
                                
                                $category_name = 'AUTOMOTIVE';
                                $terms = get_the_terms( $post_obj->ID, 'category' );
                                if ( $terms && ! is_wp_error( $terms ) ) {
                                    $category_name = $terms[0]->name;
                                }
                                
                                $post_date = get_the_date('d M Y', $post_obj);
                                $media_type = get_post_meta( $post_obj->ID, '_cmr_media_type', true );
                                $media_duration = get_post_meta( $post_obj->ID, '_cmr_media_duration', true );
                                $media_url = get_post_meta( $post_obj->ID, '_cmr_media_url', true );
                                
                                $type = $media_type ? $media_type : 'PODCAST';
                                $is_podcast = (strtoupper($type) === 'PODCAST');
                                $type_class = $is_podcast ? 'type-podcast' : 'type-topview';
                                
                                $duration = $media_duration ? $media_duration : '05:00 MINS';
                                $link = $media_url ? esc_url($media_url) : esc_url(get_permalink($post_obj->ID));
                            ?>
                            <div class="swiper-slide">
                                <a href="<?php echo $link; ?>" class="cmr-podcast-card" target="_blank" rel="noopener noreferrer">
                                    <div class="cmr-podcast-img-wrap">
                                        <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr(get_the_title($post_obj)); ?>">
                                        <div class="cmr-podcast-badge"><?php echo esc_html($duration); ?></div>
                                        <?php if ( ! $is_podcast ) : ?>
                                        <div class="cmr-podcast-play-btn">
                                            <svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="cmr-podcast-meta">
                                        <span class="<?php echo esc_attr($type_class); ?>"><?php echo esc_html($type); ?></span> &bull; 
                                        <?php echo esc_html(strtoupper($category_name)); ?> &bull; 
                                        <?php echo esc_html(strtoupper($post_date)); ?> &bull; 100 VIEW
                                    </div>
                                    
                                    <h3 class="cmr-podcast-title"><?php echo esc_html(get_the_title($post_obj)); ?></h3>
                                    
                                    <div class="cmr-podcast-player">
                                        <div class="play-icon">
                                            <svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                        </div>
                                        
                                        <?php if ( $is_podcast ) : ?>
                                            <div class="waveform"></div>
                                        <?php else : ?>
                                            <div class="progress-bar-wrap"><div class="progress-bar-inner"></div></div>
                                        <?php endif; ?>
                                        
                                        <div class="time">01:55 / 10:00</div>
                                        <?php if ( $is_podcast ) : ?>
                                        <div class="controls">
                                            <svg viewBox="0 0 24 24"><path d="M10 21A10 10 0 1 1 21 10M10 21V16M10 21H15" /><text x="12" y="14" font-size="6" stroke="none" fill="currentColor" text-anchor="middle">10</text></svg>
                                            <svg viewBox="0 0 24 24"><path d="M14 21A10 10 0 1 0 3 10M14 21V16M14 21H9" /><text x="12" y="14" font-size="6" stroke="none" fill="currentColor" text-anchor="middle">10</text></svg>
                                        </div>
                                        <div class="volume">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" stroke="none"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                                            <div class="volume-bar"></div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Swiper !== 'undefined') {
                    new Swiper('.cmr-podcast-swiper', {
                        slidesPerView: 1,
                        spaceBetween: 30,
                        autoplay: {
                            delay: 4000,
                            disableOnInteraction: false,
                        },
                        pagination: {
                            el: '.cmr-podcast-pagination',
                            clickable: true,
                        },
                        navigation: {
                            nextEl: '.swiper-button-next-custom',
                            prevEl: '.swiper-button-prev-custom',
                        },
                        breakpoints: {
                            768: {
                                slidesPerView: 1.1,
                            },
                            1024: {
                                slidesPerView: 1.12,
                            }
                        }
                    });
                }
            });
        </script>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_live_podcast_carousel', 'cmr_live_podcast_carousel_shortcode' );
