<?php
/**
 * Shortcode for Industry Intelligence Section
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_industry_intelligence_shortcode' ) ) {
    function cmr_industry_intelligence_shortcode( $atts ) {
        wp_enqueue_style( 'cmr-industry-intelligence' );

        $atts = shortcode_atts( array(
            'posts_per_page' => 4, // 2 before CTA, 2 after CTA
        ), $atts );

        $query_args = array(
            'post_type'      => 'cmr_news',
            'posts_per_page' => $atts['posts_per_page'],
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        $insights_query = new WP_Query( $query_args );

        ob_start();
        ?>
        <div class="cmr-industry-intel-section">
            <?php if ( $insights_query->have_posts() ) : ?>
                <div class="cmr-industry-intel-list">
                    <?php
                    $post_count = 0;
                    while ( $insights_query->have_posts() ) : $insights_query->the_post();
                        $post_count++;
                        
                        $post_title = get_the_title();
                        $post_link = get_permalink();
                        $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                        if ( ! $thumbnail_url ) {
                            $thumbnail_url = 'https://via.placeholder.com/800x400?text=No+Image';
                        }
                        
                        // Categories / Tags
                        $category_name = 'Industry Intelligence';
                        // Read Time
                        $read_time = '4 min read';
                        ?>
                        
                        <div class="intel-list-item">
                            <div class="intel-item-image">
                                <a href="<?php echo esc_url( $post_link ); ?>">
                                    <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                                </a>
                            </div>
                            <div class="intel-item-content">
                                <div class="intel-meta">
                                    <span class="intel-tag">&mdash; <?php echo esc_html( $category_name ); ?></span>
                                    <span class="intel-read-time"><?php echo esc_html( $read_time ); ?></span>
                                </div>
                                <h3 class="intel-title">
                                    <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                                </h3>
                                <p class="intel-excerpt">
                                    Explore real-time trends, expert analysis, and strategic signals shaping the future of mobility and automotive ecosystems.
                                </p>
                                <a href="<?php echo esc_url( $post_link ); ?>" class="intel-more-link">More Details <i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i></a>
                            </div>
                        </div>

                        <?php
                        // Inject CTA after 2nd post
                        if ( $post_count === 2 ) {
                            ?>
                            <div class="intel-cta-banner">
                                <div class="intel-cta-title-wrap">
                                    <h3 class="intel-cta-title">Need deeper insights?</h3>
                                </div>
                                <div class="intel-cta-text-wrap">
                                    <p class="intel-cta-text">Talk to our analysts for tailored recommendations across your sector.</p>
                                </div>
                                <div class="intel-cta-button-wrap">
                                    <a href="#" class="intel-cta-btn">Get Industry Insights <i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i></a>
                                </div>
                            </div>
                            <?php
                        }
                    endwhile;
                    ?>
                </div>
                
                <div class="intel-load-more-wrap">
                    <button class="intel-load-more-btn">Load More</button>
                </div>
                
            <?php else : ?>
                <p>No insights found.</p>
            <?php endif; wp_reset_postdata(); ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_industry_intelligence', 'cmr_industry_intelligence_shortcode' );
