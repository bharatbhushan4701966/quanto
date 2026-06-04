<?php
/**
 * Shortcode for Stay Updated / Latest News Section
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_stay_updated_shortcode' ) ) {
    function cmr_stay_updated_shortcode( $atts ) {
        wp_enqueue_style( 'cmr-stay-updated' );

        $atts = shortcode_atts( array(
            'posts_per_page' => 3,
        ), $atts );

        $query_args = array(
            'post_type'      => 'cmr_news',
            'posts_per_page' => $atts['posts_per_page'],
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        $news_query = new WP_Query( $query_args );

        ob_start();
        ?>
        <div class="cmr-stay-updated-section">
            <div class="stay-updated-container">
                <h2 class="stay-updated-title">Stay up to date with the latest from CMR -<br>research, perspectives and industry news.</h2>
                
                <div class="stay-updated-controls">
                    <div class="stay-updated-filters">
                        <button class="filter-pill active">
                            <i class="fa-regular fa-newspaper"></i> Media Releases
                        </button>
                        <button class="filter-pill">
                            <i class="fa-solid fa-chart-pie"></i> Quarterly Results
                        </button>
                    </div>
                    <a href="#" class="explore-all-link">Explore All <i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i></a>
                </div>

                <?php if ( $news_query->have_posts() ) : ?>
                    <div class="stay-updated-grid">
                        <?php
                        while ( $news_query->have_posts() ) : $news_query->the_post();
                            
                            $post_title = get_the_title();
                            $post_link = get_permalink();
                            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                            if ( ! $thumbnail_url ) {
                                $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
                            }
                            
                            $category_name = 'Industry Intelligence';
                            $read_time = '5 min read'; // Simulated or custom field
                            $excerpt = wp_trim_words( get_the_excerpt(), 18, '...' );
                            ?>
                            
                            <div class="stay-updated-card">
                                <a href="<?php echo esc_url( $post_link ); ?>" class="stay-updated-img-link">
                                    <div class="stay-updated-image">
                                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                                    </div>
                                </a>
                                
                                <div class="stay-updated-meta">
                                    <span class="stay-updated-tag">&mdash; <?php echo esc_html( $category_name ); ?></span>
                                    <span class="stay-updated-read-time"><?php echo esc_html( $read_time ); ?></span>
                                </div>
                                
                                <h3 class="stay-updated-card-title">
                                    <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                                </h3>
                                
                                <p class="stay-updated-excerpt">
                                    <?php echo esc_html( $excerpt ); ?>
                                </p>
                                
                                <a href="<?php echo esc_url( $post_link ); ?>" class="stay-updated-more-link">More Details <i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i></a>
                            </div>

                        <?php endwhile; ?>
                    </div>
                <?php else : ?>
                    <p>No news found.</p>
                <?php endif; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_stay_updated', 'cmr_stay_updated_shortcode' );
