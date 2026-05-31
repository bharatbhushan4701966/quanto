<?php
/**
 * CMR News Carousel Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'cmr_news_carousel', 'cmr_render_news_carousel_shortcode' );

function cmr_render_news_carousel_shortcode( $atts ) {
    wp_enqueue_style( 'cmr-media-coverage-style', get_template_directory_uri() . '/assets/css/cmr-media-coverage.css', array(), time() );

    $args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 5,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
    );
    $query = new WP_Query( $args );

    ob_start();
    ?>
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
                    $logo_id = get_post_meta( $post_id, '_cmr_news_source_logo_id', true );
                    $logo_url = '';
                    if ( $logo_id ) {
                        $logo_url = wp_get_attachment_image_url( $logo_id, 'thumbnail' );
                    }
                    // Word count for read time
                    $word_count = str_word_count( strip_tags( get_post_field( 'post_content', $post_id ) ) );
                    $reading_time = max( 1, ceil( $word_count / 200 ) );
                ?>
                <div class="swiper-slide">
                    <div class="cmr-nc-card" style="background-image: url('<?php echo esc_url( $bg_image ); ?>');">
                        <div class="cmr-nc-overlay"></div>
                        <div class="cmr-nc-card-content">
                            <?php if ( $query->current_post === 0 ) : ?>
                                <span class="cmr-nc-badge"><i class="fas fa-bookmark"></i> FEATURED</span>
                            <?php endif; ?>
                            
                            <div class="cmr-nc-meta-top">
                                <?php if ( $logo_url ) : ?>
                                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="Publisher Logo" class="cmr-nc-logo">
                                <?php endif; ?>
                            </div>
                            
                            <div class="cmr-nc-meta-bottom">
                                <div class="cmr-nc-meta-info">
                                    <span class="cmr-nc-publisher"><?php echo esc_html( $publisher_name ); ?></span>
                                    <span class="cmr-nc-date">Published <?php echo get_the_date('M d, Y'); ?></span>
                                    <span class="cmr-nc-time"><?php echo $reading_time; ?> mins</span>
                                </div>
                                
                                <h3 class="cmr-nc-post-title"><?php the_title(); ?></h3>
                                <div class="cmr-nc-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 15 ); ?></div>
                                
                                <a href="<?php the_permalink(); ?>" class="cmr-nc-read-btn">
                                    Read Coverage 
                                    <svg width="12" height="12" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 12L12 4M12 4H5.5M12 4V10.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swiper !== 'undefined') {
                new Swiper('.cmr-nc-carousel', {
                    slidesPerView: 1.3,
                    spaceBetween: 0,
                    centeredSlides: true,
                    loop: true,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    breakpoints: {
                        768: {
                            slidesPerView: 1.6,
                            centeredSlides: true,
                            spaceBetween: -15,
                        },
                        1024: {
                            slidesPerView: 1.4,
                            centeredSlides: true,
                            spaceBetween: -30,
                        }
                    }
                });
            }
        });
        </script>
        <?php else : ?>
            <p>No news items found.</p>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
}
