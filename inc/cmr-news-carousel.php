<?php
/**
 * CMR News Carousel Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'cmr_news_carousel', 'cmr_render_news_carousel_shortcode' );

function cmr_render_news_carousel_shortcode( $atts ) {
    wp_enqueue_style( 'swiper-bundle-style', get_template_directory_uri() . '/assets/css/swiper-bundle.min.css', array(), '7.0.8' );
    wp_enqueue_script( 'swiper-bundle-script', get_template_directory_uri() . '/assets/js/swiper-bundle.min.js', array('jquery'), '7.0.8', true );
    wp_enqueue_style( 'cmr-media-coverage-style', get_template_directory_uri() . '/assets/css/cmr-media-coverage.css', array(), time() );

    $args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 5,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
        'tax_query'      => array(
            array(
                'taxonomy' => 'cmr_news_category',
                'field'    => 'slug',
                'terms'    => 'cmr-in-news',
            ),
        ),
    );
    $query = new WP_Query( $args );

    ob_start();
    ?>
    <style>
    .cmr-nc-wrapper {
        width: 100%;
        max-width: 1280px;
        margin: 0 auto;
        padding: 0;
        font-family: 'Instrument Sans', sans-serif;
        box-sizing: border-box;
    }

    .cmr-nc-header {
        text-align: center;
        margin-bottom: 35px;
        padding: 0 16px;
    }

    .cmr-nc-title {
        font-size: 42px;
        font-weight: 700;
        letter-spacing: -1px;
        margin: 0 0 12px 0;
        color: #111111;
        line-height: 1.2;
    }

    .cmr-nc-subtitle {
        font-size: 16px;
        color: #555555;
        max-width: 620px;
        margin: 0 auto;
        line-height: 1.5;
    }

    .cmr-nc-carousel {
        width: 100% !important;
        overflow: hidden !important;
        padding: 10px 0 20px 0 !important;
        position: relative !important;
    }

    .cmr-nc-carousel .swiper-wrapper {
        align-items: stretch;
    }

    .cmr-nc-carousel .swiper-slide {
        transition: opacity 0.4s ease, transform 0.4s ease;
        opacity: 0.55;
        transform: scale(0.96);
        border-radius: 12px;
        box-sizing: border-box;
        height: auto;
    }

    .cmr-nc-carousel .swiper-slide-active {
        opacity: 1;
        transform: scale(1);
    }

    .cmr-nc-card {
        position: relative;
        height: 480px;
        border-radius: 12px;
        overflow: hidden;
        background-size: cover;
        background-position: center;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-sizing: border-box;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .cmr-nc-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.15) 0%, rgba(0, 0, 0, 0.25) 30%, rgba(10, 15, 30, 0.72) 62%, rgba(10, 15, 30, 0.95) 100%);
        z-index: 1;
        pointer-events: none;
    }

    /* Top Bar: Logo on left, Badge on right */
    .cmr-nc-top-bar {
        position: relative;
        z-index: 3;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 20px;
        width: 100%;
        box-sizing: border-box;
    }

    .cmr-nc-logo-wrap {
        display: flex;
        align-items: center;
    }

    .cmr-nc-logo {
        max-height: 38px;
        max-width: 120px;
        width: auto;
        height: auto;
        object-fit: contain;
        border-radius: 4px;
        display: block;
    }

    .cmr-nc-logo-bbc {
        background: #bb1919;
        color: #ffffff;
        font-weight: 800;
        font-size: 11px;
        padding: 3px 6px;
        border-radius: 3px;
        display: flex;
        flex-direction: column;
        line-height: 1.1;
        letter-spacing: 0.5px;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }

    .cmr-nc-logo-fallback {
        background: rgba(0, 0, 0, 0.5);
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 4px;
        backdrop-filter: blur(4px);
    }

    /* Featured Badge */
    .cmr-nc-badge {
        background: #ffffff;
        color: #5842c3;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        padding: 6px 14px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .cmr-nc-badge svg {
        color: #5842c3;
        fill: #5842c3;
        display: inline-block;
        vertical-align: -1px;
        margin-right: 5px;
    }

    /* Bottom Card Content */
    .cmr-nc-card-content {
        position: relative;
        z-index: 3;
        padding: 20px;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
    }

    .cmr-nc-meta-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        color: #e2e8f0;
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .cmr-nc-meta-left {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }

    .cmr-nc-meta-sep {
        opacity: 0.6;
        margin: 0 2px;
    }

    .cmr-nc-publisher {
        font-weight: 600;
        color: #ffffff;
    }

    .cmr-nc-date {
        color: #cbd5e1;
    }

    .cmr-nc-time {
        font-size: 13px;
        color: #cbd5e1;
        font-weight: 500;
        white-space: nowrap;
    }

    .cmr-nc-post-title {
        font-family: 'Instrument Sans', sans-serif;
        font-size: 21px;
        font-weight: 700;
        line-height: 1.3;
        color: #ffffff;
        margin: 0 0 10px 0;
        letter-spacing: -0.3px;
    }

    .cmr-nc-excerpt {
        font-family: 'Instrument Sans', sans-serif;
        font-size: 13.5px;
        line-height: 1.5;
        color: rgba(255, 255, 255, 0.9);
        margin: 0 0 16px 0;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .cmr-nc-read-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #ffffff;
        font-size: 16px;
        font-weight: 700;
        text-decoration: none;
        border-bottom: 2px solid #ffffff;
        padding-bottom: 3px;
        align-self: flex-start;
        transition: opacity 0.2s ease;
        line-height: 1.2;
    }

    .cmr-nc-read-btn:hover {
        opacity: 0.85;
    }

    /* Pagination: Horizontal Rounded Dashes */
    .cmr-nc-carousel .swiper-pagination {
        position: static !important;
        margin-top: 22px !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 8px !important;
        width: 100% !important;
    }

    .cmr-nc-carousel .swiper-pagination-bullet {
        width: 42px !important;
        height: 5px !important;
        border-radius: 4px !important;
        background: #ded8fa !important;
        opacity: 1 !important;
        margin: 0 !important;
        border: none !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
    }

    .cmr-nc-carousel .swiper-pagination-bullet-active {
        background: #5842c3 !important;
        opacity: 1 !important;
    }

    /* Mobile Edge-to-Edge and Card Peeking */
    @media (max-width: 768px) {
        .cmr-nc-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin: 0 !important;
            overflow: hidden !important;
        }

        .elementor-element:has(.cmr-nc-wrapper),
        .e-con:has(.cmr-nc-wrapper),
        .e-con-inner:has(.cmr-nc-wrapper),
        .elementor-widget:has(.cmr-nc-wrapper),
        .elementor-widget-container:has(.cmr-nc-wrapper) {
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            --padding-left: 0px !important;
            --padding-right: 0px !important;
        }

        .cmr-nc-header {
            padding: 0 16px !important;
            margin-bottom: 22px !important;
        }

        .cmr-nc-title {
            font-size: 26px !important;
            line-height: 1.25 !important;
            letter-spacing: -0.5px !important;
            margin-bottom: 8px !important;
        }

        .cmr-nc-subtitle {
            font-size: 14px !important;
            line-height: 1.45 !important;
        }

        .cmr-nc-carousel {
            padding-bottom: 15px !important;
        }

        .cmr-nc-card {
            height: 440px !important;
            border-radius: 12px !important;
        }

        .cmr-nc-top-bar {
            padding: 14px 16px !important;
        }

        .cmr-nc-card-content {
            padding: 16px 16px 20px 16px !important;
        }

        .cmr-nc-post-title {
            font-size: 18px !important;
            line-height: 1.35 !important;
            margin-bottom: 8px !important;
        }

        .cmr-nc-excerpt {
            font-size: 13px !important;
            line-height: 1.45 !important;
            margin-bottom: 14px !important;
        }

        .cmr-nc-read-btn {
            font-size: 15px !important;
        }

        .cmr-nc-carousel .swiper-pagination-bullet {
            width: 36px !important;
            height: 4.5px !important;
        }
    }
    </style>

    <div class="cmr-nc-wrapper">
        <div class="cmr-nc-header">
            <h2 class="cmr-nc-title">CMR in the News</h2>
            <p class="cmr-nc-subtitle">Stay updated with our latest press releases, media coverage, and strategic market announcements.</p>
        </div>

        <?php if ( $query->have_posts() ) : ?>
        <div class="swiper cmr-nc-carousel">
            <div class="swiper-wrapper">
                <?php while ( $query->have_posts() ) : $query->the_post(); 
                    $post_id = get_the_ID();
                    $bg_image = get_the_post_thumbnail_url( $post_id, 'large' );
                    if ( ! $bg_image ) {
                        $bg_image = 'https://via.placeholder.com/800x500';
                    }
                    $publisher_name = get_post_meta( $post_id, '_cmr_news_publisher_name', true );
                    if ( ! $publisher_name ) {
                        $publisher_name = 'BBC News';
                    }
                    $logo_id = get_post_meta( $post_id, '_cmr_news_source_logo_id', true );
                    $logo_url = '';
                    if ( $logo_id ) {
                        $logo_url = wp_get_attachment_image_url( $logo_id, 'thumbnail' );
                    }
                    
                    // Word count or custom reading time
                    $reading_time = get_post_meta( $post_id, '_cmr_news_reading_time', true );
                    if ( ! $reading_time ) {
                        $word_count = str_word_count( strip_tags( get_post_field( 'post_content', $post_id ) ) );
                        $reading_time = max( 1, ceil( $word_count / 200 ) );
                    }
                    
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

                    $is_featured = get_post_meta( $post_id, '_cmr_news_is_featured', true );
                    $show_featured = ( $query->current_post === 0 || $is_featured == '1' );
                ?>
                <div class="swiper-slide">
                    <div class="cmr-nc-card" style="background-image: url('<?php echo esc_url( $bg_image ); ?>');">
                        <div class="cmr-nc-overlay"></div>
                        
                        <!-- Top Bar: Logo on left, Featured badge on right -->
                        <div class="cmr-nc-top-bar">
                            <div class="cmr-nc-logo-wrap">
                                <?php if ( $logo_url ) : ?>
                                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $publisher_name ); ?>" class="cmr-nc-logo">
                                <?php elseif ( stripos( $publisher_name, 'bbc' ) !== false ) : ?>
                                    <div class="cmr-nc-logo-bbc">
                                        <span>BBC</span><span>NEWS</span>
                                    </div>
                                <?php elseif ( $publisher_name ) : ?>
                                    <span class="cmr-nc-logo-fallback"><?php echo esc_html( $publisher_name ); ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if ( $show_featured ) : ?>
                                <span class="cmr-nc-badge">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                                    </svg>
                                    FEATURED
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Bottom Card Content -->
                        <div class="cmr-nc-card-content">
                            <div class="cmr-nc-meta-info">
                                <div class="cmr-nc-meta-left">
                                    <span class="cmr-nc-publisher"><?php echo esc_html( $publisher_name ); ?></span>
                                    <span class="cmr-nc-meta-sep">|</span>
                                    <span class="cmr-nc-date">Published <?php echo get_the_date('M d, Y'); ?></span>
                                </div>
                                <span class="cmr-nc-time"><?php echo esc_html( $reading_time ); ?> mins</span>
                            </div>
                            
                            <h3 class="cmr-nc-post-title"><?php the_title(); ?></h3>
                            <div class="cmr-nc-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?></div>
                            
                            <a href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="cmr-nc-read-btn">
                                <span>Read Coverage</span> 
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 12L12 4M12 4H6M12 4V10" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
        
        <script>
        (function() {
            function initCmrNewsCarousel() {
                if (typeof Swiper === 'undefined') {
                    setTimeout(initCmrNewsCarousel, 120);
                    return;
                }
                document.querySelectorAll('.cmr-nc-carousel:not(.swiper-initialized)').forEach(function(carouselEl) {
                    var slideCount = carouselEl.querySelectorAll('.swiper-slide').length;
                    new Swiper(carouselEl, {
                        slidesPerView: 1.2,
                        spaceBetween: 12,
                        centeredSlides: true,
                        loop: slideCount > 1,
                        speed: 400,
                        pagination: {
                            el: carouselEl.querySelector('.swiper-pagination') || '.swiper-pagination',
                            clickable: true,
                        },
                        breakpoints: {
                            640: {
                                slidesPerView: 1.3,
                                spaceBetween: 16,
                                centeredSlides: true,
                            },
                            768: {
                                slidesPerView: 1.45,
                                spaceBetween: 20,
                                centeredSlides: true,
                            },
                            1024: {
                                slidesPerView: 1.25,
                                spaceBetween: 28,
                                centeredSlides: true,
                            }
                        }
                    });
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCmrNewsCarousel);
            } else {
                initCmrNewsCarousel();
            }
            window.addEventListener('load', initCmrNewsCarousel);
        })();
        </script>
        <?php else : ?>
            <p>No news items found.</p>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
}
