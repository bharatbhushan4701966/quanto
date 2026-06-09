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
            'section_title'  => 'Latest Insights',
            'section_desc'   => 'Explore expert analysis, research reports, and real-time market signals shaping industries and business strategy.',
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
            <div class="cmr-latest-insights-header" style="margin-bottom: 40px;">
                <?php if ( ! empty( $atts['section_title'] ) ) : ?>
                    <h2 class="cmr-latest-insights-title" style="font-size: 42px; font-weight: 700; color: #111; margin: 0 0 15px 0; letter-spacing: -1px;">
                        <?php echo esc_html( $atts['section_title'] ); ?>
                    </h2>
                <?php endif; ?>
                
                <?php if ( ! empty( $atts['section_desc'] ) ) : ?>
                    <p class="cmr-latest-insights-desc" style="font-size: 18px; color: #555; margin: 0; max-width: 800px; line-height: 1.5;">
                        <?php echo esc_html( $atts['section_desc'] ); ?>
                    </p>
                <?php endif; ?>
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
        
        <style>
        .cmr-filters-fixed-js {
            position: fixed !important;
            left: 0;
            width: 100%;
            z-index: 999999;
            background: #fff;
            padding-top: 15px !important;
            padding-bottom: 15px !important;
            padding-left: calc((100vw - 1280px) / 2) !important;
            padding-right: calc((100vw - 1280px) / 2) !important;
            margin-bottom: 0 !important;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            font-family: 'Instrument Sans', sans-serif !important;
        }
        @media (max-width: 1320px) {
            .cmr-filters-fixed-js {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
        }
        </style>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.cmr-latest-insights-section');
            sections.forEach(section => {
                const filterBar = section.querySelector('.cmr-insights-filters-bar');
                if (!filterBar) return;
                
                // Create a placeholder to prevent grid jumping when bar becomes fixed
                const placeholder = document.createElement('div');
                placeholder.className = 'cmr-insights-filters-placeholder';
                placeholder.style.height = '0px';
                placeholder.style.marginBottom = '0px';
                filterBar.parentNode.insertBefore(placeholder, filterBar);
                
                function updateSticky() {
                    const sectionRect = section.getBoundingClientRect();
                    
                    let stickyOffset = 0;
                    const wpAdminBar = document.getElementById('wpadminbar');
                    if (wpAdminBar && window.getComputedStyle(wpAdminBar).position === 'fixed') {
                        stickyOffset = wpAdminBar.offsetHeight;
                    }
                    const headers = document.querySelectorAll('header, [data-elementor-type="header"], .elementor-location-header, .elementor-sticky--active');
                    headers.forEach(h => {
                        if (h === filterBar || h.contains(filterBar)) return;
                        const hStyle = window.getComputedStyle(h);
                        if (hStyle.position === 'fixed' || hStyle.position === 'sticky' || h.classList.contains('elementor-sticky--active')) {
                            const hRect = h.getBoundingClientRect();
                            if (hRect.top <= stickyOffset + 10 && hRect.bottom > stickyOffset && hRect.bottom < (window.innerHeight / 2)) {
                                stickyOffset = hRect.bottom;
                            }
                        }
                    });

                    // Trigger sticky as soon as the section touches the sticky offset
                    if (sectionRect.top <= stickyOffset && sectionRect.bottom > (filterBar.offsetHeight + stickyOffset)) {
                        if (!filterBar.classList.contains('cmr-filters-fixed-js')) {
                            // Save original height
                            placeholder.style.height = filterBar.offsetHeight + 'px';
                            const style = window.getComputedStyle(filterBar);
                            placeholder.style.marginBottom = style.marginBottom;
                            
                            filterBar.classList.add('cmr-filters-fixed-js');
                            document.body.appendChild(filterBar); // Escaping elementor transform context
                        }
                        
                        if (sectionRect.bottom <= (filterBar.offsetHeight + stickyOffset)) {
                            filterBar.style.top = (sectionRect.bottom - filterBar.offsetHeight) + 'px';
                        } else {
                            filterBar.style.top = stickyOffset + 'px';
                        }
                    } else {
                        if (filterBar.classList.contains('cmr-filters-fixed-js')) {
                            filterBar.classList.remove('cmr-filters-fixed-js');
                            filterBar.style.top = '';
                            placeholder.parentNode.insertBefore(filterBar, placeholder.nextSibling);
                            placeholder.style.height = '0px';
                            placeholder.style.marginBottom = '0px';
                        }
                    }
                }
                
                window.addEventListener('scroll', updateSticky, { passive: true });
                window.addEventListener('resize', updateSticky, { passive: true });
                setTimeout(updateSticky, 100);
            });
        });
        </script>

        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_latest_insights', 'cmr_latest_insights_shortcode' );
