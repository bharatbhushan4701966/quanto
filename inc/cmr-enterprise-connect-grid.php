<?php
/**
 * CMR Enterprise Connect Grid Component
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'cmr_enterprise_connect_grid', 'cmr_enterprise_connect_grid_shortcode' );

function cmr_enterprise_connect_grid_shortcode() {
    ob_start();

    $unique_ids = cmr_get_unique_enterprise_post_ids();
    $sliced_ids = array_slice( $unique_ids, 4, 6 );

    $query = new WP_Query(); // Empty default
    if ( ! empty( $sliced_ids ) ) {
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in', // Maintain the correct date order from SQL
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
    }
    
    // Override max_num_pages so pagination knows exactly how many pages remain
    $query->max_num_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
    $query->found_posts = count( $unique_ids ) - 4;

    ?>
    <style>
        .cmr-enterprisecgd-wrapper {
            font-family: 'Instrument Sans', sans-serif;
            max-width: 1280px;
            margin: 0 auto;
            padding: 40px 20px;
            color: #111;
        }

        /* Top Navigation */
        .cmr-enterprisecgd-top-nav {
            display: flex;
            align-items: center;
            gap: 40px;
            padding: 20px 0;
            border-bottom: 1px solid #eaeaea;
            overflow-x: auto;
            white-space: nowrap;
            background: #fff;
            transition: box-shadow 0.3s ease;
        }

        .cmr-enterprisecgd-fixed-js {
            position: fixed !important;
            left: 0;
            right: 0;
            width: 100% !important;
            z-index: 999999 !important;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding-left: max(20px, calc(50vw - 640px)) !important;
            padding-right: max(20px, calc(50vw - 640px)) !important;
            margin: 0 !important;
            transition: none !important;
        }
        .cmr-enterprisecgd-top-nav a {
            text-decoration: none;
            color: #111;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s;
        }
        .cmr-enterprisecgd-top-nav a:hover,
        .cmr-enterprisecgd-top-nav a.active {
            font-weight: 700;
        }

        /* Header Area */
        .cmr-enterprisecgd-header {
            margin-bottom: 40px;
        }
        .cmr-enterprisecgd-header h1 {
            font-size: 45px;
            font-weight: 600;
            margin: 40px 0 15px 0;
            letter-spacing: -1px;
            color: #111;
        }
        .cmr-enterprisecgd-header p {
            font-size: 16px;
            color: #555;
            margin: 0;
            line-height: 1.5;
        }

        /* Filters and Search */
        .cmr-enterprisecgd-filters-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 20px;
        }
        .cmr-enterprisecgd-years {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .cmr-enterprisecgd-year-btn {
            background: transparent;
            border: 1px solid #eaeaea;
            border-radius: 40px;
            padding: 8px 20px;
            font-size: 14px;
            color: #111;
            cursor: pointer;
            transition: all 0.3s;
            outline: none;
            font-family: inherit;
        }
        .cmr-enterprisecgd-year-btn:hover {
            border-color: #6B3FA0;
            color: #6B3FA0;
        }
        .cmr-enterprisecgd-year-btn.active {
            border-color: #6B3FA0;
            color: #6B3FA0;
        }
        .cmr-enterprisecgd-more-dropdown {
            position: relative;
            display: inline-block;
        }
        .cmr-enterprisecgd-more-btn {
            background: transparent;
            border: 1px solid #eaeaea;
            border-radius: 40px;
            padding: 8px 20px;
            font-size: 14px;
            color: #111;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: inherit;
        }
        .cmr-enterprisecgd-search {
            position: relative;
            width: 300px;
        }
        .cmr-enterprisecgd-search input {
            width: 100%;
            padding: 10px 40px 10px 20px;
            border: 1px solid #eaeaea;
            border-radius: 40px;
            font-size: 14px;
            outline: none;
            font-family: inherit;
        }
        .cmr-enterprisecgd-search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: #6B3FA0;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        /* Grid */
        .cmr-enterprisecgd-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-bottom: 60px;
        }
        
        .cmr-enterprisecgd-card {
            display: flex;
            flex-direction: column;
        }
        
        .cmr-enterprisecgd-card-img-wrap {
            width: 100%;
            height: 240px;
            overflow: hidden;
            margin-bottom: 20px;
            background-color: #f4f4f4;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cmr-enterprisecgd-card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .cmr-enterprisecgd-card:hover .cmr-enterprisecgd-card-img-wrap img {
            transform: scale(1.05);
        }

        .cmr-enterprisecgd-card-meta {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #888;
            margin-bottom: 12px;
            align-items: center;
        }
        .cmr-enterprisecgd-card-label {
            color: #888;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .cmr-enterprisecgd-card-label::before {
            content: "";
            width: 16px;
            height: 1px;
            background: #888;
            display: inline-block;
        }
        .cmr-enterprisecgd-card-label span { margin: 0 4px; }

        .cmr-enterprisecgd-card-title {
            font-size: 18px;
            font-weight: 700;
            line-height: 1.4;
            margin: 0 0 12px 0;
            color: #111;
        }
        .cmr-enterprisecgd-card-title a {
            color: inherit;
            text-decoration: none;
            letter-spacing: 1px;
        }

        .cmr-enterprisecgd-card-excerpt {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
            margin: 0 0 20px 0;
            flex-grow: 1;
        }

        .cmr-enterprisecgd-card-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 600;
            color: #111;
            text-decoration: none;
            border-bottom: 1px solid #111;
            padding-bottom: 2px;
            align-self: flex-start;
            transition: color 0.3s, border-color 0.3s;
        }
        .cmr-enterprisecgd-card-link:hover {
            color: #6B3FA0;
            border-color: #6B3FA0;
        }

        /* Load More Button */
        .cmr-enterprisecgd-load-more-wrap {
            text-align: center;
        }
        .cmr-enterprisecgd-load-more {
            background: transparent;
            border: 1px solid #eaeaea;
            border-radius: 40px;
            padding: 14px 40px;
            font-size: 15px;
            font-weight: 600;
            color: #111;
            cursor: pointer;
            transition: all 0.3s;
            font-family: inherit;
        }
        .cmr-enterprisecgd-load-more:hover {
            border-color: #6B3FA0;
            color: #6B3FA0;
        }
        
        .cmr-enterprisecgd-loading {
            opacity: 0.5;
            pointer-events: none;
        }

        @media (max-width: 992px) {
            .cmr-enterprisecgd-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .cmr-enterprisecgd-grid {
                grid-template-columns: 1fr;
            }
            .cmr-enterprisecgd-filters-row {
                flex-direction: column;
                align-items: flex-start;
            }
            .cmr-enterprisecgd-search {
                width: 100%;
            }
        }
            .intel-numeric-pagination .page-numbers {
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
        .intel-numeric-pagination .page-numbers.current {
            background: #6A35FF;
            color: #fff;
        }
        .intel-numeric-pagination .page-numbers.prev, 
        .intel-numeric-pagination .page-numbers.next {
            color: #6A35FF;
        }
        .intel-numeric-pagination .page-numbers.dots {
            width: auto;
        }
    </style>

    <div class="cmr-enterprisecgd-wrapper">
        
        <!-- Top Nav -->
        <div class="cmr-enterprisecgd-top-nav">
            <a href="#" class="active">Enterprise Connect</a>
            <a href="#">Featured</a>
            <a href="#">Latest</a>
            <a href="#">Media Resources</a>
            <a href="#">Media Contacts</a>
            <a href="#">Market Updates</a>
            <a href="#">Reports</a>
            <a href="#">CMR in news</a>
        </div>

        <!-- Header -->
        <div class="cmr-enterprisecgd-header">
            <h1>Enterprise Connect</h1>
            <p>Explore expert analysis, research reports, and real-time market signals shaping industries and business strategy.</p>
        </div>

        <!-- Filters & Search -->
        <div class="cmr-enterprisecgd-filters-row">
            <div class="cmr-enterprisecgd-years" id="cmr-enterprisecgd-years">
                <button class="cmr-enterprisecgd-year-btn active" data-year="">All</button>
                <button class="cmr-enterprisecgd-year-btn" data-year="2026">2026</button>
                <button class="cmr-enterprisecgd-year-btn" data-year="2025">2025</button>
                <button class="cmr-enterprisecgd-year-btn" data-year="2024">2024</button>
                <button class="cmr-enterprisecgd-year-btn" data-year="2023">2023</button>
                <button class="cmr-enterprisecgd-year-btn" data-year="2022">2022</button>
                <button class="cmr-enterprisecgd-year-btn" data-year="2021">2021</button>
                <div class="cmr-enterprisecgd-more-dropdown">
                    <button class="cmr-enterprisecgd-more-btn">More <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
                </div>
            </div>
            <div class="cmr-enterprisecgd-search">
                <form id="cmr-enterprisecgd-search-form" onsubmit="return false;">
                    <input type="text" id="cmr-enterprisecgd-search-input" placeholder="Search by name">
                    <button type="submit" class="cmr-enterprisecgd-search-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Grid -->
        <div class="cmr-enterprisecgd-grid" id="cmr-enterprisecgd-grid">
            <?php
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $post_id = get_the_ID();
                    $title = get_the_title();
                    $link = get_permalink();
                    $excerpt = wp_trim_words( get_the_excerpt(), 18 );
                    if ( empty($excerpt) ) {
                        $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
                    }
                    $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
                    
                    $content = get_post_field( 'post_content', $post_id );
                    $word_count = str_word_count( strip_tags( $content ) );
                    $read_time = ceil( $word_count / 200 );
                    if ($read_time < 1) $read_time = 1;
                    $date = get_the_date('d M Y');
                    ?>
                    <div class="cmr-enterprisecgd-card">
                        <div class="cmr-enterprisecgd-card-img-wrap">
                            <a href="<?php echo esc_url($link); ?>" style="display: block; width: 100%; height: 100%;">
                                <?php if ( $bg_image ) : ?>
                                    <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                                <?php else : ?>
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #ccc;">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="cmr-enterprisecgd-card-meta">
                            <div class="cmr-enterprisecgd-card-label">Enterprise Connect <span>|</span> <?php echo esc_html($date); ?></div>
                            <div class="cmr-enterprisecgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                        </div>
                        <h3 class="cmr-enterprisecgd-card-title">
                            <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                        </h3>
                        <p class="cmr-enterprisecgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                        <a href="<?php echo esc_url($link); ?>" class="cmr-enterprisecgd-card-link">
                            Read full Release 
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                        </a>
                    </div>
                    <?php
                }
            } else {
                echo '<p>No Enterprise Connect found.</p>';
            }
            $has_more = $query->max_num_pages > 1;
            wp_reset_postdata();
            ?>
        </div>

        <!-- Load More -->
        <div class="cmr-enterprisecgd-load-more-wrap" style="display: <?php echo $has_more ? 'block' : 'none'; ?>;">
            <button class="cmr-enterprisecgd-load-more" id="cmr-enterprisecgd-load-more-btn">Load More</button>
        </div>
        <!-- Pagination -->
        <div class="cmr-enterprisecgd-pagination-wrap" style="display: none;"></div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentPage = 1;
        let currentYear = '';
        let currentSearch = '';
        
        const grid = document.getElementById('cmr-enterprisecgd-grid');
        const loadMoreBtn = document.getElementById('cmr-enterprisecgd-load-more-btn');
        const yearBtns = document.querySelectorAll('.cmr-enterprisecgd-year-btn');
        const searchForm = document.getElementById('cmr-enterprisecgd-search-form');
        const searchInput = document.getElementById('cmr-enterprisecgd-search-input');

        function fetchPosts(isLoadMore = false) {
            if (!isLoadMore) {
                currentPage = 1;
                grid.innerHTML = '<p>Loading...</p>';
            }
            
            if (loadMoreBtn) loadMoreBtn.classList.add('cmr-enterprisecgd-loading');
            
            const data = new FormData();
            data.append('action', 'cmr_load_more_enterprise_connect');
            data.append('page', currentPage);
            data.append('year', currentYear);
            data.append('search', currentSearch);

            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    if (!isLoadMore) {
                        grid.innerHTML = response.data.html || '<p>No Enterprise Connect found.</p>';
                    } else {
                        grid.insertAdjacentHTML('beforeend', response.data.html);
                    }
                    
                    const paginationWrap = document.querySelector('.cmr-enterprisecgd-pagination-wrap');
                    if (response.data.pagination) {
                        loadMoreBtn.parentElement.style.display = 'none';
                        if (paginationWrap) {
                            paginationWrap.innerHTML = '<div class="intel-numeric-pagination" style="text-align: center; margin-top: 30px; display: flex; justify-content: center; gap: 10px;">' + response.data.pagination + '</div>';
                            paginationWrap.style.display = 'block';
                        }
                    } else if (response.data.has_more) {
                        loadMoreBtn.parentElement.style.display = 'block';
                        if (paginationWrap) paginationWrap.style.display = 'none';
                    } else {
                        loadMoreBtn.parentElement.style.display = 'none';
                        if (paginationWrap) paginationWrap.style.display = 'none';
                    }
                }
                if (loadMoreBtn) loadMoreBtn.classList.remove('cmr-enterprisecgd-loading');
            })
            .catch(err => {
                console.error(err);
                if (loadMoreBtn) loadMoreBtn.classList.remove('cmr-enterprisecgd-loading');
            });
        }

        // Year Filter
        yearBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                yearBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentYear = this.getAttribute('data-year');
                fetchPosts(false);
            });
        });

        // Search
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                currentSearch = searchInput.value.trim();
                fetchPosts(false);
            });
        }

        // Load More
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function() {
                currentPage++;
                fetchPosts(true);
            });
        }

        // Sticky Nav Logic
        const sections = document.querySelectorAll('.cmr-enterprisecgd-wrapper');
        sections.forEach(section => {
            const navBar = section.querySelector('.cmr-enterprisecgd-top-nav');
            if (!navBar) return;
            
            const placeholder = document.createElement('div');
            placeholder.className = 'cmr-enterprisecgd-top-nav-placeholder';
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
                    if (!navBar.classList.contains('cmr-enterprisecgd-fixed-js')) {
                        placeholder.style.height = navBar.offsetHeight + 'px';
                        const style = window.getComputedStyle(navBar);
                        placeholder.style.marginBottom = style.marginBottom;
                        
                        navBar.classList.add('cmr-enterprisecgd-fixed-js');
                        document.body.appendChild(navBar); 
                    }
                    
                    if (sectionRect.bottom <= (navBar.offsetHeight + stickyOffset)) {
                        navBar.style.top = (sectionRect.bottom - navBar.offsetHeight) + 'px';
                    } else {
                        navBar.style.top = stickyOffset + 'px';
                    }
                } else {
                    if (navBar.classList.contains('cmr-enterprisecgd-fixed-js')) {
                        navBar.classList.remove('cmr-enterprisecgd-fixed-js');
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



