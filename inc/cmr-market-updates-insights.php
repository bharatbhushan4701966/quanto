<?php
/**
 * Shortcode for Market Updates & Strategic Insights
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! function_exists( 'cmr_market_updates_insights_shortcode' ) ) {
    function cmr_market_updates_insights_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'posts_per_page' => 9,
            'category'       => '', // e.g. 'market-updates'
        ), $atts, 'cmr_market_updates_insights' );

        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
        
        $query_args = array(
            'post_type'      => 'post',
            'posts_per_page' => 27, // 9 items * 3 pages (2 load mores)
            'paged'          => $paged,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        if ( ! empty( $atts['category'] ) ) {
            $query_args['category_name'] = $atts['category'];
        } else {
            // Default to market-updates if none specified
            $query_args['category_name'] = 'market-updates';
        }

        $insights_posts = get_posts( $query_args );
        $all_query = new WP_Query($query_args);
        $posts = $all_query->posts;
        $max_pages = $all_query->max_num_pages;

        ob_start();
        ?>
        <style>
            .cmr-mui-section {
                font-family: 'Instrument Sans', sans-serif !important;
                width: 100%;
                max-width: 1280px;
                margin: 0 auto;
                padding: 40px 20px;
                box-sizing: border-box;
            }
            .cmr-mui-header {
                margin-bottom: 30px;
            }
            .cmr-mui-title {
                font-size: 42px;
                font-weight: 600;
                color: #111;
                margin: 0 0 15px 0;
                line-height: 1.1;
                letter-spacing: -1px;
            }
            .cmr-mui-subtitle {
                font-size: 18px;
                color: #444;
                margin: 0;
                font-weight: 400;
            }
            .cmr-mui-nav-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px 0;
                margin-bottom: 30px;
                background: #fff;
                /* Sticky configuration */
                position: -webkit-sticky;
                position: sticky;
                top: 0;
                z-index: 100;
                border-bottom: 1px solid #eaeaea;
            }
            .cmr-mui-filters {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }
            .cmr-mui-filter-btn {
                padding: 8px 20px;
                border: 1px solid #ddd;
                border-radius: 30px;
                background: #fff;
                color: #333;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
                font-family: inherit;
            }
            .cmr-mui-filter-btn:hover {
                border-color: #6d4bdf;
                color: #6d4bdf;
            }
            .cmr-mui-filter-btn.active {
                border-color: #6d4bdf;
                color: #6d4bdf;
                box-shadow: 0 0 0 1px #6d4bdf;
            }
            .cmr-mui-search-wrap {
                position: relative;
                width: 250px;
            }
            .cmr-mui-search-wrap input {
                width: 100%;
                padding: 8px 40px 8px 20px;
                border: 1px solid #ddd;
                border-radius: 30px;
                font-size: 14px;
                font-family: inherit;
                box-sizing: border-box;
                outline: none;
            }
            .cmr-mui-search-wrap input:focus {
                border-color: #6d4bdf;
            }
            .cmr-mui-search-icon {
                position: absolute;
                right: 5px;
                top: 8px;
                width: 32px;
                height: 32px;
                background: #6d4bdf;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                cursor: pointer;
            }
            .cmr-mui-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
            }
            .cmr-mui-card {
                display: flex;
                flex-direction: column;
                background: #fff;
                height: 100%;
            }
            .cmr-mui-card-img {
                width: 100%;
                aspect-ratio: 4 / 3;
                object-fit: cover;
                display: block;
                border-radius: 4px;
                margin-bottom: 20px;
            }
            .cmr-mui-card-meta {
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-size: 13px;
                color: #888;
                margin-bottom: 12px;
            }
            .cmr-mui-card-cat-date {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .cmr-mui-card-cat-date::before {
                content: '';
                display: inline-block;
                width: 20px;
                height: 1px;
                background: #ccc;
            }
            .cmr-mui-card-title {
                font-size: 20px;
                font-weight: 600;
                color: #111;
                margin: 0 0 15px 0;
                line-height: 1.3;
                letter-spacing: -0.5px;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .cmr-mui-card-excerpt {
                font-size: 15px;
                color: #555;
                line-height: 1.5;
                margin: 0 0 20px 0;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .cmr-mui-read-more {
                display: inline-flex;
                align-items: center;
                font-size: 15px;
                font-weight: 600;
                color: #111;
                text-decoration: none;
                border-bottom: 1px solid #111;
                padding-bottom: 2px;
                align-self: flex-start;
                margin-top: auto;
                transition: opacity 0.2s;
            }
            .cmr-mui-read-more svg {
                width: 16px;
                height: 16px;
                margin-left: 5px;
            }
            .cmr-mui-card-hidden {
                display: none !important;
            }
            .cmr-mui-btn {
                background: #fff;
                color: #111;
                border: 1px solid #ccc;
                padding: 10px 40px;
                border-radius: 50px;
                font-size: 16px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s;
            }
            .cmr-mui-btn:hover {
                background: #f8f8f8;
                border-color: #aaa;
            }
            .cmr-mui-pagination {
                width: 100%;
                text-align: center;
                display: flex;
                justify-content: center;
            }
            .cmr-mui-pagination .nav-links {
                display: flex;
                justify-content: center;
                gap: 15px;
                align-items: center;
            }
            .cmr-mui-pagination .page-numbers {
                padding: 0;
                width: 40px;
                height: 40px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: none;
                border-radius: 50%;
                text-decoration: none;
                color: #333;
                font-size: 16px;
                font-weight: 500;
                background: transparent;
            }
            .cmr-mui-pagination .page-numbers.current {
                background: #6244d4;
                color: #fff;
            }
            .cmr-mui-pagination .page-numbers.prev, 
            .cmr-mui-pagination .page-numbers.next {
                color: #6244d4;
            }
            .cmr-mui-pagination .page-numbers.dots {
                width: auto;
            }
            .cmr-mui-read-more:hover {
                opacity: 0.7;
            }

            @media (max-width: 992px) {
                .cmr-mui-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
                .cmr-mui-nav-bar {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 20px;
                }
                .cmr-mui-nav-links {
                    flex-wrap: wrap;
                }
            }
            @media (max-width: 768px) {
                .cmr-mui-grid {
                    grid-template-columns: 1fr;
                }
            }
            
            /* Sticky Nav CSS */
            .cmr-mui-sticky-nav {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 15px 0;
                margin-bottom: 20px;
                border-bottom: 1px solid #eaeaea;
                background: #fff;
                z-index: 100;
                width: 100%;
            }
            .cmr-mui-nav-title {
                font-size: 22px;
                font-weight: 600;
                color: #111;
            }
            .cmr-mui-nav-links {
                display: flex;
                gap: 25px;
                align-items: center;
            }
            .cmr-mui-nav-links a {
                color: #111;
                text-decoration: none;
                font-size: 15px;
                font-weight: 500;
                transition: opacity 0.2s;
            }
            .cmr-mui-nav-links a:hover {
                opacity: 0.7;
            }
            .cmr-mui-nav-links a.expert-btn {
                font-weight: 600;
            }
            .cmr-mui-sticky-nav.intel-nav-fixed-js {
                position: fixed !important;
                left: 0;
                right: 0;
                padding: 15px 40px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                border-bottom: none;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                transition: top 0.2s ease-out;
            }
            @media (max-width: 768px) {
                .cmr-mui-sticky-nav.intel-nav-fixed-js {
                    padding: 15px 20px;
                }
            }
        </style>

        <div class="cmr-mui-section">
            <div class="cmr-mui-sticky-nav intel-nav-bar">
                <div class="cmr-mui-nav-title">
                    Market Updates
                </div>
                <div class="cmr-mui-nav-links">
                    <a href="#">Featured</a>
                    <a href="#">Latest Updates</a>
                    <a href="#">CMR live</a>
                    <a href="#">Reports</a>
                    <a href="#" class="expert-btn" style="display: inline-flex; align-items: center;">
                        Get expert insights 
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; margin-left: 4px;">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="cmr-mui-header" style="margin-top: 40px;">
                <h2 class="cmr-mui-title">Market Updates & Strategic Insights</h2>
                <p class="cmr-mui-subtitle">Insights, opinions, and analysis shaping enterprise decisions across industries.</p>
            </div>

            <div class="cmr-mui-nav-bar">
                <div class="cmr-mui-filters">
                    <button class="cmr-mui-filter-btn active">All</button>
                    <button class="cmr-mui-filter-btn">Automotive</button>
                    <button class="cmr-mui-filter-btn">Consumer Tech</button>
                    <button class="cmr-mui-filter-btn">Digital Supply Chain</button>
                    <button class="cmr-mui-filter-btn">More <svg style="width:10px;margin-left:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
                </div>
                <div class="cmr-mui-search-wrap">
                    <input type="text" placeholder="Search by name">
                    <div class="cmr-mui-search-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="cmr-mui-grid">
                <?php if ( ! empty($posts) ) : 
                    $post_count = 0;
                    foreach ( $posts as $post_obj ) : 
                        $post_count++;
                        $hidden_class = ( $post_count > 9 ) ? ' cmr-mui-card-hidden' : '';
                        
                        $thumbnail_url = get_the_post_thumbnail_url( $post_obj->ID, 'large' );
                        if ( ! $thumbnail_url ) {
                            $thumbnail_url = 'https://via.placeholder.com/600x400?text=Insight+Image';
                        }
                        
                        $category_name = 'Uncategorized';
                        $terms = get_the_terms( $post_obj->ID, 'category' );
                        if ( $terms && ! is_wp_error( $terms ) ) {
                            $category_name = $terms[0]->name;
                        }
                        
                        $post_date = get_the_date('d M Y', $post_obj);
                        
                        // Approximate read time
                        $content = $post_obj->post_content;
                        $word_count = str_word_count( strip_tags( $content ) );
                        $read_time = ceil( $word_count / 200 );
                        if ($read_time < 1) $read_time = 1;

                        $excerpt = get_the_excerpt($post_obj);
                        if ( empty( $excerpt ) ) {
                            $excerpt = wp_trim_words( $content, 20 );
                        }
                    ?>
                    <div class="cmr-mui-card<?php echo $hidden_class; ?>">
                        <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr(get_the_title($post_obj)); ?>" class="cmr-mui-card-img">
                        <div class="cmr-mui-card-meta">
                            <div class="cmr-mui-card-cat-date">
                                <span><?php echo esc_html($category_name); ?></span> | 
                                <span><?php echo esc_html($post_date); ?></span>
                            </div>
                            <div class="cmr-mui-card-read"><?php echo esc_html($read_time); ?> min read</div>
                        </div>
                        <h3 class="cmr-mui-card-title"><?php echo esc_html(get_the_title($post_obj)); ?></h3>
                        <p class="cmr-mui-card-excerpt"><?php echo esc_html(wp_strip_all_tags($excerpt)); ?></p>
                        <a href="<?php echo esc_url(get_permalink($post_obj->ID)); ?>" class="cmr-mui-read-more">
                            Read More 
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="7" y1="17" x2="17" y2="7"></line>
                                <polyline points="7 7 17 7 17 17"></polyline>
                            </svg>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>No insights found.</p>
                <?php endif; ?>
            </div>

            <?php if ( ! empty($posts) && count($posts) > 9 ) : ?>
                <div class="cmr-mui-actions" style="text-align: center; margin-top: 40px;">
                    <button type="button" id="cmr-mui-load-more" class="cmr-mui-btn">Load More</button>
                </div>
            <?php endif; ?>
            
            <?php if ( $max_pages > 1 ) : ?>
                <div id="cmr-mui-pagination-wrap" class="cmr-mui-pagination" style="display: <?php echo (!empty($posts) && count($posts) > 9) ? 'none' : 'block'; ?>; margin-top: 40px;">
                    <?php
                    $real_paged = max( 1, get_query_var( 'paged' ) );
                    $fake_current = $real_paged * 3;
                    $fake_total = $max_pages * 3;

                    echo '<div class="nav-links">';

                    // Prev button
                    if ($fake_current > 1) {
                        $prev_real = ceil(($fake_current - 1) / 3);
                        echo '<a class="prev page-numbers" href="?paged=' . $prev_real . '"><svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.5 15L1.5 8L8.5 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>';
                    }

                    // Just show a few surrounding pages
                    $start = max(1, $fake_current - 2);
                    $end = min($fake_total, $fake_current + 2);

                    if ($start > 1) {
                        echo '<a class="page-numbers" href="?paged=1">1</a>';
                        if ($start > 2) echo '<span class="page-numbers dots">...</span>';
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        $real_target = ceil($i / 3);
                        if ($i == $fake_current) {
                            echo '<span aria-current="page" class="page-numbers current">' . $i . '</span>';
                        } else {
                            echo '<a class="page-numbers" href="?paged=' . $real_target . '">' . $i . '</a>';
                        }
                    }

                    if ($end < $fake_total) {
                        if ($end < $fake_total - 1) echo '<span class="page-numbers dots">...</span>';
                        $real_target = ceil($fake_total / 3);
                        echo '<a class="page-numbers" href="?paged=' . $real_target . '">' . $fake_total . '</a>';
                    }

                    // Next button
                    if ($fake_current < $fake_total) {
                        $next_real = ceil(($fake_current + 1) / 3);
                        echo '<a class="next page-numbers" href="?paged=' . $next_real . '"><svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 15L8.5 8L1.5 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>';
                    }
                    echo '</div>';
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <script>
            // Filter functionality (AJAX Search)
            document.addEventListener('DOMContentLoaded', function() {
                var searchInput = document.querySelector('.cmr-mui-search-wrap input');
                var searchTimer;
                if (searchInput) {
                    searchInput.addEventListener('keyup', function(e) {
                        clearTimeout(searchTimer);
                        var val = e.target.value.trim();
                        
                        searchTimer = setTimeout(function() {
                            var grid = document.querySelector('.cmr-mui-grid');
                            if (!grid) return;

                            grid.innerHTML = '<p style="grid-column:1/-1; text-align:center; padding:40px; font-size:18px;">Searching...</p>';
                            
                            var loadMoreBtn = document.getElementById('cmr-mui-load-more');
                            if (loadMoreBtn) loadMoreBtn.style.display = 'none';
                            var paginationWrap = document.getElementById('cmr-mui-pagination-wrap');
                            if (paginationWrap) paginationWrap.style.display = 'none';

                            var formData = new FormData();
                            formData.append('action', 'cmr_insights_ajax_search');
                            formData.append('search_term', val);
                            formData.append('prefix', 'cmr-mui-');
                            formData.append('category', 'market-updates');
                            
                            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                                method: 'POST',
                                body: formData
                            })
                            .then(function(res) { return res.text(); })
                            .then(function(html) {
                                grid.innerHTML = html;
                            });
                        }, 600); // 600ms debounce
                    });
                }
                
                // Load More Functionality
                var loadMoreBtn = document.getElementById('cmr-mui-load-more');
                var paginationWrap = document.getElementById('cmr-mui-pagination-wrap');
                if (loadMoreBtn) {
                    loadMoreBtn.addEventListener('click', function() {
                        var hiddenCards = document.querySelectorAll('.cmr-mui-card.cmr-mui-card-hidden');
                        var cardsToShow = 9;
                        for (var i = 0; i < hiddenCards.length; i++) {
                            if (i < cardsToShow) {
                                hiddenCards[i].classList.remove('cmr-mui-card-hidden');
                            }
                        }
                        
                        var remainingHidden = document.querySelectorAll('.cmr-mui-card.cmr-mui-card-hidden');
                        if (remainingHidden.length === 0) {
                            loadMoreBtn.style.display = 'none';
                            if (paginationWrap) {
                                paginationWrap.style.display = 'block';
                            }
                        }
                    });
                }
                
                // Sticky Nav Functionality
                const sections = document.querySelectorAll('.cmr-mui-section');
                sections.forEach(section => {
                    const navBar = section.querySelector('.cmr-mui-sticky-nav');
                    if (!navBar) return;
                    
                    // Create a placeholder to prevent grid jumping when bar becomes fixed
                    const placeholder = document.createElement('div');
                    placeholder.className = 'cmr-mui-nav-placeholder';
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

                        // Trigger sticky as soon as the section touches the sticky offset
                        if (sectionRect.top <= stickyOffset && sectionRect.bottom > (navBar.offsetHeight + stickyOffset)) {
                            if (!navBar.classList.contains('intel-nav-fixed-js')) {
                                // Save original height
                                placeholder.style.height = navBar.offsetHeight + 'px';
                                const style = window.getComputedStyle(navBar);
                                placeholder.style.marginBottom = style.marginBottom;
                                
                                navBar.classList.add('intel-nav-fixed-js');
                                document.body.appendChild(navBar); // Escaping elementor transform context
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
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_market_updates_insights', 'cmr_market_updates_insights_shortcode' );
