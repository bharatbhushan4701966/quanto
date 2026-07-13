<?php
/**
 * Shortcode for Latest Insights Section
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_latest_insights_render_grid' ) ) {
    function cmr_latest_insights_render_grid( $insights_query ) {
        if ( $insights_query->have_posts() ) : ?>
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
        <?php endif; 
        wp_reset_postdata();
    }
}

if ( ! function_exists( 'cmr_latest_insights_ajax_handler' ) ) {
    function cmr_latest_insights_ajax_handler() {
        check_ajax_referer( 'cmr_insights_nonce', 'security' );
        
        $category_slug = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
        
        $query_args = array(
            'post_type'      => array( 'post', 'cmr_news' ),
            'posts_per_page' => 4,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        
        if ( ! empty( $category_slug ) && $category_slug !== 'all' ) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $category_slug,
                ),
            );
        } else {
            // Default "All" falls back to industry-connect for now based on user request
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => 'industry-connect',
                ),
            );
        }
        
        $insights_query = new WP_Query( $query_args );
        cmr_latest_insights_render_grid( $insights_query );
        
        wp_die();
    }
    
    add_action( 'wp_ajax_cmr_filter_insights', 'cmr_latest_insights_ajax_handler' );
    add_action( 'wp_ajax_nopriv_cmr_filter_insights', 'cmr_latest_insights_ajax_handler' );
}

if ( ! function_exists( 'cmr_latest_insights_shortcode' ) ) {
    function cmr_latest_insights_shortcode( $atts ) {
        // Enqueue the specific CSS for this shortcode
        wp_enqueue_style( 'cmr-latest-insights' );
        
        // Pass AJAX info to JS
        wp_enqueue_script( 'jquery' );

        $atts = shortcode_atts( array(
            'posts_per_page' => 4,
            'nav_title'      => 'Automotive',
            'section_title'  => 'Latest Insights',
            'section_desc'   => 'Explore expert analysis, research reports, and real-time market signals shaping industries and business strategy.',
        ), $atts );

        $query_args = array(
            'post_type'      => array( 'post', 'cmr_news' ),
            'posts_per_page' => $atts['posts_per_page'],
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => 'industry-connect',
                ),
            ),
        );

        $insights_query = new WP_Query( $query_args );
        
        $nonce = wp_create_nonce( 'cmr_insights_nonce' );
        $ajax_url = admin_url( 'admin-ajax.php' );

        ob_start();
        ?>
        <div class="cmr-latest-insights-section">
            <div class="cmr-latest-nav-bar intel-nav-bar">
                <div class="intel-nav-title">
                    <?php echo esc_html( $atts['nav_title'] ); ?>
                </div>
                <div class="intel-nav-links" style="display: flex; align-items: center;">
                    <a href="#overview">Overview</a>
                    <a href="#cmr-latest-insights">Insights</a>
                    <a href="#reports">Reports</a>
                    <a href="#cmr-market-updates">Market Updates</a>
                    <a href="#newsroom">Newsroom</a>
                    <a href="#cmr-in-news">CMR in news</a>

                    <a href="#cmr-footer-card-section" class="cmr-nav-btn-subscribe" style="display: none; align-items: center; justify-content: center; background: #fff; color: #111; font-weight: 600; font-size: 14px; padding: 8px 16px; border-radius: 40px; text-decoration: none; border: 1px solid #111; margin-left: 15px; line-height: 1; transition: all 0.3s ease;">
                        Subscribe now
                        <svg style="margin-left: 6px;" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                    </a>
                </div>
            </div>

            <div class="cmr-latest-insights-header" style="margin-bottom: 40px; margin-top: 50px;">
                <?php if ( ! empty( $atts['section_title'] ) ) : ?>
                    <h2 class="cmr-latest-insights-title" style="font-size: 42px; font-weight: 600; color: #111; margin: 0 0 15px 0; letter-spacing: -1px;">
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
                    <button class="filter-btn active" data-category="all">All</button>
                    <button class="filter-btn" data-category="ev-growth">EV Growth</button>
                    <button class="filter-btn" data-category="battery-innovation">Battery Innovation</button>
                    <button class="filter-btn" data-category="oem-strategy">OEM Strategy</button>
                    <button class="filter-btn" data-category="supply-chain">Supply Chain</button>
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

            <div class="cmr-insights-grid-container" style="transition: opacity 0.3s ease;">
                <?php cmr_latest_insights_render_grid( $insights_query ); ?>
            </div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sticky Nav logic
            const sections = document.querySelectorAll('.cmr-latest-insights-section');
            sections.forEach(section => {
                const navBar = section.querySelector('.cmr-latest-nav-bar');
                if (!navBar) return;
                
                const placeholder = document.createElement('div');
                placeholder.className = 'cmr-latest-nav-placeholder';
                placeholder.style.height = '0px';
                placeholder.style.marginBottom = '0px';
                navBar.parentNode.insertBefore(placeholder, navBar);
                
                function updateSticky() {
                    const sectionRect = section.getBoundingClientRect();
                    let stickyOffset = 0;
                    const wpAdminBar = document.getElementById('wpadminbar');
                    if (wpAdminBar && window.getComputedStyle(wpAdminBar).position === 'fixed') {
                        stickyOffset = wpAdminBar.offsetHeight;
                    }
                    const headers = document.querySelectorAll('header, [data-elementor-type="header"], .elementor-location-header, .elementor-sticky--active');
                    headers.forEach(h => {
                        if (h === navBar || h.contains(navBar)) return;
                        const hStyle = window.getComputedStyle(h);
                        if (hStyle.position === 'fixed' || hStyle.position === 'sticky' || h.classList.contains('elementor-sticky--active')) {
                            const hRect = h.getBoundingClientRect();
                            if (hRect.top <= stickyOffset + 10 && hRect.bottom > stickyOffset && hRect.bottom < (window.innerHeight / 2)) {
                                stickyOffset = hRect.bottom;
                            }
                        }
                    });

                    if (sectionRect.top <= stickyOffset && sectionRect.bottom > (navBar.offsetHeight + stickyOffset)) {
                        if (!navBar.classList.contains('intel-nav-fixed-js')) {
                            placeholder.style.height = navBar.offsetHeight + 'px';
                            const style = window.getComputedStyle(navBar);
                            placeholder.style.marginBottom = style.marginBottom;
                            navBar.classList.add('intel-nav-fixed-js');
                            document.body.appendChild(navBar); 
                        }
                        
                        if (sectionRect.bottom <= (navBar.offsetHeight + stickyOffset)) {
                            navBar.style.top = (sectionRect.bottom - navBar.offsetHeight) + 'px';
                        } else {
                            navBar.style.top = stickyOffset + 'px';
                        }
                    } else {
                        if (navBar.classList.contains('intel-nav-fixed-js')) {
                            navBar.classList.remove('intel-nav-fixed-js');
                            navBar.style.top = '';
                            placeholder.parentNode.insertBefore(navBar, placeholder.nextSibling);
                            placeholder.style.height = '0px';
                            placeholder.style.marginBottom = '0px';
                        }
                    }
                }
                
                window.addEventListener('scroll', updateSticky, { passive: true });
                window.addEventListener('resize', updateSticky, { passive: true });
                setTimeout(updateSticky, 100);
            });
            
            // AJAX Filter Logic
            const filterBtns = document.querySelectorAll('.cmr-insights-filters .filter-btn');
            const gridContainer = document.querySelector('.cmr-insights-grid-container');
            
            if (filterBtns.length > 0 && gridContainer) {
                filterBtns.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        // Update active state
                        filterBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        
                        const category = this.getAttribute('data-category');
                        
                        // Add loading state
                        gridContainer.style.opacity = '0.5';
                        
                        const data = new URLSearchParams();
                        data.append('action', 'cmr_filter_insights');
                        data.append('category', category);
                        data.append('security', '<?php echo esc_js($nonce); ?>');
                        
                        fetch('<?php echo esc_url($ajax_url); ?>', {
                            method: 'POST',
                            body: data
                        })
                        .then(response => response.text())
                        .then(html => {
                            gridContainer.innerHTML = html;
                            gridContainer.style.opacity = '1';
                        })
                        .catch(error => {
                            console.error('Error fetching insights:', error);
                            gridContainer.style.opacity = '1';
                        });
                    });
                });
            }
        });
        </script>

        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_latest_insights', 'cmr_latest_insights_shortcode' );
