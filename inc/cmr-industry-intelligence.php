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
            'posts_per_page' => 6,
            'nav_title'      => 'Automotive',
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
            <div class="intel-nav-bar">
                <div class="intel-nav-title">
                    <?php echo esc_html( $atts['nav_title'] ); ?>
                </div>
                <div class="intel-nav-links">
                    <a href="#">Overview</a>
                    <a href="#">Insights</a>
                    <a href="#">Reports</a>
                    <a href="#">Market Updates</a>
                    <a href="#">Newsroom</a>
                    <a href="#">CMR in news</a>
                </div>
            </div>

            <?php if ( $insights_query->have_posts() ) : ?>
                <div class="intel-grid">
                    <?php
                    while ( $insights_query->have_posts() ) : $insights_query->the_post();
                        $post_title = get_the_title();
                        $post_link = get_permalink();
                        $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                        if ( ! $thumbnail_url ) {
                            $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
                        }
                        
                        // Categories / Tags
                        $category_name = 'Industry Intelligence';
                        $terms = get_the_terms( get_the_ID(), 'category' );
                        if ( $terms && ! is_wp_error( $terms ) ) {
                            $category_name = $terms[0]->name;
                        }

                        // Calculate reading time
                        $content = get_post_field( 'post_content', get_the_ID() );
                        $word_count = str_word_count( strip_tags( $content ) );
                        $read_time = ceil( $word_count / 200 );
                        if ($read_time < 1) $read_time = 1;
                        ?>
                        
                        <div class="intel-card">
                            <div class="intel-card-img">
                                <a href="<?php echo esc_url( $post_link ); ?>">
                                    <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                                </a>
                            </div>
                            <div class="intel-card-content">
                                <div class="intel-meta">
                                    <span class="intel-category">Industry Intelligence</span>
                                    <span class="intel-read-time"><?php echo esc_html( $read_time ); ?> min read</span>
                                </div>
                                <h3 class="intel-title">
                                    <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                                </h3>
                                <div class="intel-excerpt">
                                    <?php 
                                    $excerpt = get_the_excerpt();
                                    if ( empty( $excerpt ) ) {
                                        $excerpt = wp_trim_words( $content, 12 );
                                    } else {
                                        $excerpt = wp_trim_words( $excerpt, 12 );
                                    }
                                    echo wp_kses_post( $excerpt ); 
                                    ?>
                                </div>
                                <a href="<?php echo esc_url( $post_link ); ?>" class="intel-read-more">
                                    More Details 
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="7" y1="17" x2="17" y2="7"></line>
                                        <polyline points="7 7 17 7 17 17"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
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
