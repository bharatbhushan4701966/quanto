<?php
/**
 * Shortcode for Featured Insight
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_featured_insight_shortcode' ) ) {
    function cmr_featured_insight_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'post_type' => 'cmr_news',
        ), $atts );

        $query_args = array(
            'post_type'      => $atts['post_type'],
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        $featured_query = new WP_Query( $query_args );

        ob_start();
        ?>
        <style>
            .cmr-fi-container {
                font-family: 'Instrument Sans', sans-serif !important;
                max-width: 800px;
                margin: 0 auto;
                background: #f5f5f5;
            }
            .cmr-fi-image-wrap {
                position: relative;
                width: 100%;
                margin-bottom: 30px;
            }
            .cmr-fi-image-wrap img {
                width: 100%;
                height: auto;
                display: block;
            }
            .cmr-fi-badge {
                position: absolute;
                top: 20px;
                left: 20px;
                background: rgba(0, 0, 0, 0.4);
                color: #fff;
                padding: 6px 16px;
                border-radius: 20px;
                font-size: 14px;
                font-weight: 500;
                backdrop-filter: blur(4px);
            }
            .cmr-fi-meta {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                font-size: 14px;
                color: #666;
            }
            .cmr-fi-date {
                display: flex;
                align-items: center;
                gap: 15px;
            }
            .cmr-fi-date::before {
                content: '';
                display: block;
                width: 30px;
                height: 1px;
                background: #ccc;
            }
            .cmr-fi-read-time {
                color: #666;
            }
            .cmr-fi-title {
                font-size: 32px;
                font-weight: 600;
                color: #111;
                margin: 0 0 20px 0;
                line-height: 1.3;
                letter-spacing: -0.5px;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .cmr-fi-excerpt {
                font-size: 18px;
                color: #333;
                line-height: 1.6;
                margin-bottom: 30px;
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .cmr-fi-read-link {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-size: 16px;
                font-weight: 700;
                color: #111;
                text-decoration: none;
                transition: color 0.3s ease;
            }
            .cmr-fi-read-link:hover {
                color: #555;
            }
            .cmr-fi-read-link svg {
                width: 14px;
                height: 14px;
                transition: transform 0.3s ease;
            }
            .cmr-fi-read-link:hover svg {
                transform: translate(3px, -3px);
            }
            @media (max-width: 768px) {
                .cmr-fi-title {
                    font-size: 26px;
                }
                .cmr-fi-excerpt {
                    font-size: 16px;
                }
                .cmr-fi-badge {
                    top: 15px;
                    left: 15px;
                }
            }
        </style>
        <?php if ( $featured_query->have_posts() ) : ?>
            <div class="cmr-fi-container">
                <?php while ( $featured_query->have_posts() ) : $featured_query->the_post(); 
                    $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                    if ( ! $thumbnail_url ) {
                        $thumbnail_url = 'https://via.placeholder.com/800x500?text=Featured+Image';
                    }
                    $post_date = get_the_date('d F Y');
                    // Calculate reading time (approx 200 words per min)
                    $content = get_post_field( 'post_content', get_the_ID() );
                    $word_count = str_word_count( strip_tags( $content ) );
                    $read_time = ceil( $word_count / 200 );
                    if ($read_time < 1) $read_time = 1;
                ?>
                <div class="cmr-fi-image-wrap">
                    <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                    <span class="cmr-fi-badge">Featured</span>
                </div>
                
                <div class="cmr-fi-content">
                    <div class="cmr-fi-meta">
                        <div class="cmr-fi-date"><?php echo esc_html( $post_date ); ?></div>
                        <div class="cmr-fi-read-time"><?php echo esc_html( $read_time ); ?> min Read</div>
                    </div>
                    
                    <h2 class="cmr-fi-title"><?php echo esc_html( get_the_title() ); ?></h2>
                    
                    <div class="cmr-fi-excerpt">
                        <?php 
                        $excerpt = get_the_excerpt();
                        if ( empty( $excerpt ) ) {
                            $excerpt = wp_trim_words( $content, 25 );
                        }
                        echo wp_kses_post( $excerpt ); 
                        ?>
                    </div>
                    
                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="cmr-fi-read-link">
                        Read Insight
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p>No featured insight found.</p>
        <?php endif; wp_reset_postdata(); ?>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_featured_insight', 'cmr_featured_insight_shortcode' );
