<?php
/**
 * Shortcode for Viewpoints & Strategic Insights
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! function_exists( 'cmr_viewpoints_insights_shortcode' ) ) {
    function cmr_viewpoints_insights_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'posts_per_page' => 9,
            'category'       => '', // e.g. 'viewpoints'
        ), $atts, 'cmr_viewpoints_insights' );

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
            // Default to viewpoints if none specified
            $query_args['category_name'] = 'viewpoint,viewpoints';
        }

        $insights_posts = get_posts( $query_args );
        $all_query = new WP_Query($query_args);
        $posts = $all_query->posts;
        $max_pages = $all_query->max_num_pages;

        ob_start();
        ?>
        <style>
            .cmr-vpi-section {
                font-family: 'Instrument Sans', sans-serif !important;
                width: 100%;
                max-width: 1280px;
                margin: 0 auto;
                padding: 40px 20px;
                box-sizing: border-box;
            }
            .cmr-vpi-header {
                margin-bottom: 30px;
            }
            .cmr-vpi-title {
                font-size: 42px;
                font-weight: 600;
                color: #111;
                margin: 0 0 15px 0;
                line-height: 1.1;
                letter-spacing: -1px;
            }
            .cmr-vpi-subtitle {
                font-size: 18px;
                color: #444;
                margin: 0;
                font-weight: 400;
            }
            .cmr-vpi-nav-bar {
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
            .cmr-vpi-filters {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }
            .cmr-vpi-filter-btn {
                padding: 10px 20px;
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
            .cmr-vpi-filter-btn:hover {
                border-color: #6d4bdf;
                color: #6d4bdf;
            }
            .cmr-vpi-filter-btn.active {
                border-color: #6d4bdf;
                color: #6d4bdf;
                box-shadow: 0 0 0 1px #6d4bdf;
            }
            .cmr-vpi-search-wrap {
                position: relative;
                width: 250px;
            }
            .cmr-vpi-search-wrap input {
                width: 100%;
                padding: 12px 40px 12px 20px;
                border: 1px solid #ddd;
                border-radius: 30px;
                font-size: 14px;
                font-family: inherit;
                box-sizing: border-box;
                outline: none;
            }
            .cmr-vpi-search-wrap input:focus {
                border-color: #6d4bdf;
            }
            .cmr-vpi-search-icon {
                position: absolute;
                right: 5px;
                top: 5px;
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
            .cmr-vpi-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
            }
            .cmr-vpi-card {
                display: flex;
                flex-direction: column;
                background: #fff;
                height: 100%;
            }
            .cmr-vpi-card-img {
                width: 100%;
                aspect-ratio: 4 / 3;
                object-fit: cover;
                display: block;
                border-radius: 4px;
                margin-bottom: 20px;
            }
            .cmr-vpi-card-meta {
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-size: 13px;
                color: #888;
                margin-bottom: 12px;
            }
            .cmr-vpi-card-cat-date {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .cmr-vpi-card-cat-date::before {
                content: '';
                display: inline-block;
                width: 20px;
                height: 1px;
                background: #ccc;
            }
            .cmr-vpi-card-title {
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
            .cmr-vpi-card-excerpt {
                font-size: 15px;
                color: #555;
                line-height: 1.5;
                margin: 0 0 20px 0;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .cmr-vpi-read-more {
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
            .cmr-vpi-read-more svg {
                width: 16px;
                height: 16px;
                margin-left: 5px;
            }
            .cmr-vpi-card-hidden {
                display: none !important;
            }
            .cmr-vpi-btn {
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
            .cmr-vpi-btn:hover {
                background: #f8f8f8;
                border-color: #aaa;
            }
            .cmr-vpi-pagination {
                width: 100%;
                text-align: center;
                display: flex;
                justify-content: center;
            }
            .cmr-vpi-pagination .nav-links {
                display: flex;
                justify-content: center;
                gap: 15px;
                align-items: center;
            }
            .cmr-vpi-pagination .page-numbers {
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
            .cmr-vpi-pagination .page-numbers.current {
                background: #6244d4;
                color: #fff;
            }
            .cmr-vpi-pagination .page-numbers.prev, 
            .cmr-vpi-pagination .page-numbers.next {
                color: #6244d4;
            }
            .cmr-vpi-pagination .page-numbers.dots {
                width: auto;
            }
            .cmr-vpi-read-more:hover {
                opacity: 0.7;
            }

            @media (max-width: 992px) {
                .cmr-vpi-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
                .cmr-vpi-nav-bar {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 20px;
                }
                .cmr-vpi-nav-links {
                    flex-wrap: wrap;
                }
            }
            @media (max-width: 768px) {
                .cmr-vpi-grid {
                    grid-template-columns: 1fr;
                }
            }
            
            /* Sticky Nav CSS */
            .cmr-vpi-sticky-nav {
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
            .cmr-vpi-nav-title {
                font-size: 22px;
                font-weight: 700;
                color: #111;
            }
            .cmr-vpi-nav-links {
                display: flex;
                gap: 25px;
                align-items: center;
            }
            .cmr-vpi-nav-links a {
                color: #111;
                text-decoration: none;
                font-size: 15px;
                font-weight: 500;
                transition: opacity 0.2s;
            }
            .cmr-vpi-nav-links a:hover {
                opacity: 0.7;
            }
            .cmr-vpi-nav-links a.expert-btn {
                font-weight: 600;
            }
            .cmr-vpi-sticky-nav.intel-nav-fixed-js {
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
                .cmr-vpi-sticky-nav.intel-nav-fixed-js {
                    padding: 15px 20px;
                }
            }
        </style>

        <div class="cmr-vpi-section">
            <div class="cmr-vpi-sticky-nav intel-nav-bar">
                <div class="cmr-vpi-nav-title">
                    Viewpoints
                </div>
                <div class="cmr-vpi-nav-links">
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

            <div class="cmr-vpi-header" style="margin-top: 40px;">
                <h2 class="cmr-vpi-title">Viewpoints & Strategic Insights</h2>
                <p class="cmr-vpi-subtitle">Track real-time shifts, growth signals, and strategic developments shaping key industries.</p>
            </div>

            <div class="cmr-vpi-nav-bar">
                <div class="cmr-vpi-filters">
                    <button class="cmr-vpi-filter-btn active">All</button>
                    <button class="cmr-vpi-filter-btn">Automotive</button>
                    <button class="cmr-vpi-filter-btn">Consumer Tech</button>
                    <button class="cmr-vpi-filter-btn">Digital Supply Chain</button>
                    <button class="cmr-vpi-filter-btn">More <svg style="width:10px;margin-left:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
                </div>
                <div class="cmr-vpi-search-wrap">
                    <input type="text" placeholder="Search by name">
                    <div class="cmr-vpi-search-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="cmr-vpi-grid">
                <?php if ( ! empty($posts) ) : 
                    $post_count = 0;
                    foreach ( $posts as $post_obj ) : 
                        $post_count++;
                        $hidden_class = ( $post_count > 9 ) ? ' cmr-vpi-card-hidden' : '';
                        
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
                    <div class="cmr-vpi-card<?php echo $hidden_class; ?>">
                        <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr(get_the_title($post_obj)); ?>" class="cmr-vpi-card-img">
                        <div class="cmr-vpi-card-meta">
                            <div class="cmr-vpi-card-cat-date">
                                <span><?php echo esc_html($category_name); ?></span> | 
                                <span><?php echo esc_html($post_date); ?></span>
                            </div>
                            <div class="cmr-vpi-card-read"><?php echo esc_html($read_time); ?> min read</div>
                        </div>
                        <h3 class="cmr-vpi-card-title"><?php echo esc_html(get_the_title($post_obj)); ?></h3>
                        <p class="cmr-vpi-card-excerpt"><?php echo esc_html(wp_strip_all_tags($excerpt)); ?></p>
                        <a href="<?php echo esc_url(get_permalink($post_obj->ID)); ?>" class="cmr-vpi-read-more">
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
                <div class="cmr-vpi-actions" style="text-align: center; margin-top: 40px;">
                    <button type="button" id="cmr-vpi-load-more" class="cmr-vpi-btn">Load More</button>
                </div>
            <?php endif; ?>
            
            <?php if ( $max_pages > 1 ) : ?>
                <div id="cmr-vpi-pagination-wrap" class="cmr-vpi-pagination" style="display: <?php echo (!empty($posts) && count($posts) > 9) ? 'none' : 'block'; ?>; margin-top: 40px;">
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
            // Filter functionality
            document.addEventListener('DOMContentLoaded', function() {
                var searchInput = document.querySelector('.cmr-vpi-search-wrap input');
                if (searchInput) {
                    searchInput.addEventListener('keyup', function(e) {
                        var val = e.target.value.toLowerCase();
                        var cards = document.querySelectorAll('.cmr-vpi-card');
                        cards.forEach(function(card) {
                            var title = card.querySelector('.cmr-vpi-card-title').innerText.toLowerCase();
                            if (title.indexOf(val) > -1) {
                                card.style.display = '';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    });
                }
                
                // Load More Functionality
                var loadMoreBtn = document.getElementById('cmr-vpi-load-more');
                var paginationWrap = document.getElementById('cmr-vpi-pagination-wrap');
                if (loadMoreBtn) {
                    loadMoreBtn.addEventListener('click', function() {
                        var hiddenCards = document.querySelectorAll('.cmr-vpi-card.cmr-vpi-card-hidden');
                        var cardsToShow = 9;
                        for (var i = 0; i < hiddenCards.length; i++) {
                            if (i < cardsToShow) {
                                hiddenCards[i].classList.remove('cmr-vpi-card-hidden');
                            }
                        }
                        
                        var remainingHidden = document.querySelectorAll('.cmr-vpi-card.cmr-vpi-card-hidden');
                        if (remainingHidden.length === 0) {
                            loadMoreBtn.style.display = 'none';
                            if (paginationWrap) {
                                paginationWrap.style.display = 'block';
                            }
                        }
                    });
                }
                
                // Sticky Nav Functionality
                const sections = document.querySelectorAll('.cmr-vpi-section');
                sections.forEach(section => {
                    const navBar = section.querySelector('.cmr-vpi-sticky-nav');
                    if (!navBar) return;
                    
                    // Create a placeholder to prevent grid jumping when bar becomes fixed
                    const placeholder = document.createElement('div');
                    placeholder.className = 'cmr-vpi-nav-placeholder';
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
add_shortcode( 'cmr_viewpoints_insights', 'cmr_viewpoints_insights_shortcode' );
