<?php
/**
 * Shortcode for Latest Insights Section
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_latest_insights_shortcode' ) ) {
    function cmr_latest_insights_shortcode( $atts ) {
        // Enqueue the specific CSS for this shortcode
        wp_enqueue_style( 'cmr-latest-insights' );

        $atts = shortcode_atts( array(
            'posts_per_page' => 4,
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
        <div class="cmr-latest-insights-section">
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

            <div class="cmr-insights-filters-bar">
                <div class="cmr-insights-filters">
                    <button class="filter-btn active">All</button>
                    <button class="filter-btn">EV Growth</button>
                    <button class="filter-btn">Battery Innovation</button>
                    <button class="filter-btn">OEM Strategy</button>
                    <button class="filter-btn">Supply Chain</button>
                </div>
                <div class="cmr-insights-search">
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <input type="search" class="search-field" placeholder="Search by name" value="<?php echo get_search_query(); ?>" name="s" />
                        <button type="submit" class="search-submit">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <input type="hidden" name="post_type" value="cmr_news" />
                    </form>
                </div>
            </div>

            <?php if ( $insights_query->have_posts() ) : ?>
                <div class="cmr-insights-grid">
                    <?php
                    $post_count = 0;
                    while ( $insights_query->have_posts() ) : $insights_query->the_post();
                        $post_count++;
                        
                        $post_title = get_the_title();
                        $post_link = get_permalink();
                        $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                        if ( ! $thumbnail_url ) {
                            $thumbnail_url = 'https://via.placeholder.com/800x600?text=No+Image';
                        }
                        
                        // Categories / Tags (simulated as "Media Releases" based on mockup)
                        $category_name = 'Media Releases';
                        $terms = get_the_terms( get_the_ID(), 'category' );
                        if ( $terms && ! is_wp_error( $terms ) ) {
                            $category_name = $terms[0]->name;
                        }

                        if ( $post_count === 1 ) {
                            // Featured Left Card
                            ?>
                            <div class="cmr-insights-featured">
                                <a href="<?php echo esc_url( $post_link ); ?>" class="insights-featured-card" style="background-image: url('<?php echo esc_url( $thumbnail_url ); ?>');">
                                    <div class="insights-featured-overlay"></div>
                                    <div class="insights-featured-content">
                                        <span class="insights-tag">&mdash; <?php echo esc_html( $category_name ); ?></span>
                                        <h3 class="insights-title"><?php echo esc_html( $post_title ); ?></h3>
                                        <span class="insights-more-link">More Details <i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i></span>
                                    </div>
                                </a>
                            </div>
                            <div class="cmr-insights-stack">
                            <?php
                        } else {
                            // Stacked Right Cards
                            ?>
                                <a href="<?php echo esc_url( $post_link ); ?>" class="insights-stacked-card">
                                    <div class="insights-stacked-image">
                                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                                    </div>
                                    <div class="insights-stacked-content">
                                        <span class="insights-tag">&mdash; <?php echo esc_html( $category_name ); ?></span>
                                        <h4 class="insights-title"><?php echo esc_html( $post_title ); ?></h4>
                                        <span class="insights-more-link">More Details <i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i></span>
                                    </div>
                                </a>
                            <?php
                        }

                    endwhile;
                    
                    if ( $post_count > 1 ) {
                        echo '</div>'; // close cmr-insights-stack
                    }
                    ?>
                </div>
            <?php else : ?>
                <p>No insights found.</p>
            <?php endif; wp_reset_postdata(); ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_latest_insights', 'cmr_latest_insights_shortcode' );
