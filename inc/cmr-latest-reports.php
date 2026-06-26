<?php
/**
 * CMR Latest Reports Section Shortcode (with AJAX filtering)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_latest_reports_shortcode' ) ) {
    function cmr_latest_reports_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'title' => 'Browse latest reports',
            'posts_per_page' => 8,
        ), $atts );

        // Get product categories for the filter
        $categories = get_terms( array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
        ) );

        ob_start();
        ?>
        <style>
            .cmr-latest-section {
                max-width: 1280px;
                margin: 60px auto;
                padding: 0 20px;
                font-family: 'Instrument Sans', sans-serif !important;
            }

            .cmr-latest-header {
                margin-bottom: 30px;
            }

            .cmr-latest-title {
                font-size: 32px;
                font-weight: 700;
                color: #000000;
                margin-bottom: 25px;
                letter-spacing: -0.5px;
            }

            .cmr-filter-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 20px;
                flex-wrap: wrap;
            }

            .cmr-filter-pills {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            .cmr-filter-pill {
                background: #ffffff;
                border: 1px solid #e5e7eb;
                color: #374151;
                padding: 8px 20px;
                border-radius: 50px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .cmr-filter-pill:hover, .cmr-filter-pill.active {
                background: #6b46c1;
                border-color: #6b46c1;
                color: #ffffff;
            }

            .cmr-search-wrapper {
                position: relative;
                width: 300px;
            }

            .cmr-search-wrapper input {
                width: 100%;
                padding: 10px 40px 10px 20px;
                border-radius: 50px;
                border: 1px solid #e5e7eb;
                font-size: 14px;
                outline: none;
            }

            .cmr-search-wrapper button {
                position: absolute;
                right: 5px;
                top: 50%;
                transform: translateY(-50%);
                width: 30px;
                height: 30px;
                border-radius: 50%;
                background: #6b46c1;
                color: #fff;
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }

            .cmr-latest-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin-top: 40px;
            }

            .cmr-lr-card {
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 0;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                transition: transform 0.3s ease;
            }

            .cmr-lr-card:hover {
                transform: translateY(-5px);
            }

            .cmr-lr-image-wrap {
                width: 100%;
                height: 220px;
                position: relative;
            }

            .cmr-lr-image-wrap img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .cmr-lr-badge {
                position: absolute;
                top: 15px;
                left: 15px;
                background: #ffffff;
                color: #ea580c; /* Orange for NEW */
                font-size: 11px;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 4px;
                z-index: 10;
                display: flex;
                align-items: center;
                gap: 5px;
                text-transform: uppercase;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }

            .cmr-lr-content {
                padding: 20px;
                display: flex;
                flex-direction: column;
                flex: 1;
            }

            .cmr-lr-category {
                font-size: 12px;
                color: #9ca3af;
                margin-bottom: 8px;
                font-weight: 500;
            }

            .cmr-lr-title {
                font-size: 16px;
                font-weight: 700;
                color: #111827;
                margin-bottom: 15px;
                line-height: 1.4;
                text-decoration: none;
            }

            .cmr-lr-title:hover {
                color: #6b46c1;
            }

            .cmr-lr-stars {
                color: #f59e0b;
                font-size: 13px;
                margin-bottom: 20px;
            }
            .cmr-lr-stars span {
                color: #6b7280;
                font-size: 12px;
                margin-left: 5px;
            }

            .cmr-lr-price {
                font-size: 20px;
                font-weight: 700;
                color: #111827;
                margin-bottom: 20px;
                margin-top: auto;
            }

            .cmr-lr-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                width: 100%;
                padding: 10px;
                background: #ffffff;
                border: 1px solid #d1d5db;
                border-radius: 50px;
                color: #374151;
                font-size: 14px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.2s ease;
            }

            .cmr-lr-btn:hover {
                background: #f3f4f6;
                color: #111827;
            }

            .cmr-load-more-wrap {
                text-align: center;
                margin-top: 40px;
            }

            .cmr-load-more-btn {
                background: #ffffff;
                border: 1px solid #d1d5db;
                color: #374151;
                padding: 12px 40px;
                border-radius: 50px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .cmr-load-more-btn:hover {
                background: #f3f4f6;
                color: #111827;
            }

            .cmr-pagination-wrap {
                display: none;
                justify-content: center;
                align-items: center;
                margin-top: 40px;
                gap: 5px;
            }
            
            .cmr-pagination-wrap .page-numbers {
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
                transition: all 0.2s ease;
            }
            
            .cmr-pagination-wrap .page-numbers:hover {
                opacity: 0.7;
            }
            
            .cmr-pagination-wrap .page-numbers.current {
                background: #6b46c1;
                color: #fff;
            }
            
            .cmr-pagination-wrap .page-numbers.prev, 
            .cmr-pagination-wrap .page-numbers.next {
                color: #6b46c1;
            }
            
            .cmr-pagination-wrap .page-numbers.dots {
                width: auto;
            }

            .cmr-loading-spinner {
                display: none;
                text-align: center;
                margin-top: 20px;
            }

            @media (max-width: 1024px) {
                .cmr-latest-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            @media (max-width: 768px) {
                .cmr-latest-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
                .cmr-filter-bar {
                    flex-direction: column;
                    align-items: flex-start;
                }
                .cmr-search-wrapper {
                    width: 100%;
                }
            }

            @media (max-width: 480px) {
                .cmr-latest-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <section class="cmr-latest-section">
            <div class="cmr-latest-header">
                <h2 class="cmr-latest-title"><?php echo esc_html( $atts['title'] ); ?></h2>
                <div class="cmr-filter-bar">
                    <div class="cmr-filter-pills">
                        <button class="cmr-filter-pill active" data-cat="">All</button>
                        <?php foreach ( $categories as $category ) : ?>
                            <button class="cmr-filter-pill" data-cat="<?php echo esc_attr( $category->slug ); ?>"><?php echo esc_html( $category->name ); ?></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="cmr-search-wrapper">
                        <input type="text" id="cmr-lr-search" placeholder="Search by name">
                        <button id="cmr-lr-search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </div>
            </div>
            
            <div class="cmr-latest-grid" id="cmr-latest-grid">
                <!-- Products will be loaded here via AJAX -->
            </div>

            <div class="cmr-loading-spinner" id="cmr-loading-spinner">
                <i class="fa-solid fa-circle-notch fa-spin fa-2x" style="color: #6b46c1;"></i>
            </div>

            <div class="cmr-load-more-wrap">
                <button class="cmr-load-more-btn" id="cmr-load-more-btn" style="display: none;">Load More</button>
            </div>
            
            <div class="cmr-pagination-wrap" id="cmr-pagination-wrap"></div>
        </section>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let currentPage = 1;
                let currentCategory = '';
                let currentSearch = '';
                let loadMoreCount = 0;
                const grid = document.getElementById('cmr-latest-grid');
                const loadMoreBtn = document.getElementById('cmr-load-more-btn');
                const paginationWrap = document.getElementById('cmr-pagination-wrap');
                const spinner = document.getElementById('cmr-loading-spinner');
                const searchInput = document.getElementById('cmr-lr-search');
                const searchBtn = document.getElementById('cmr-lr-search-btn');
                const filterPills = document.querySelectorAll('.cmr-filter-pill');

                function loadProducts( reset = false, pageNum = null ) {
                    if ( reset ) {
                        if (pageNum) {
                            currentPage = pageNum;
                        } else {
                            currentPage = 1;
                            loadMoreCount = 0;
                        }
                        grid.innerHTML = '';
                    }

                    spinner.style.display = 'block';
                    loadMoreBtn.style.display = 'none';
                    paginationWrap.style.display = 'none';

                    const formData = new FormData();
                    formData.append('action', 'cmr_load_reports');
                    formData.append('paged', currentPage);
                    formData.append('category', currentCategory);
                    formData.append('search', currentSearch);
                    formData.append('posts_per_page', <?php echo intval( $atts['posts_per_page'] ); ?>);

                    fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        spinner.style.display = 'none';
                        if ( data.success ) {
                            if ( reset ) {
                                grid.innerHTML = data.data.html;
                            } else {
                                grid.insertAdjacentHTML('beforeend', data.data.html);
                            }
                            
                            if ( data.data.has_more ) {
                                if ( loadMoreCount < 2 ) {
                                    loadMoreBtn.style.display = 'inline-block';
                                } else {
                                    paginationWrap.innerHTML = data.data.pagination;
                                    paginationWrap.style.display = 'flex';
                                    bindPaginationEvents();
                                }
                            } else if ( data.data.pagination && loadMoreCount >= 2 ) {
                                paginationWrap.innerHTML = data.data.pagination;
                                paginationWrap.style.display = 'flex';
                                bindPaginationEvents();
                            }
                        } else {
                            if ( reset ) {
                                grid.innerHTML = '<p>No reports found.</p>';
                            }
                        }
                    })
                    .catch(err => {
                        spinner.style.display = 'none';
                        console.error('Error fetching reports:', err);
                    });
                }

                function bindPaginationEvents() {
                    const pageLinks = paginationWrap.querySelectorAll('a.page-numbers');
                    pageLinks.forEach(link => {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            const href = this.getAttribute('href');
                            // Extract paged query param
                            const urlParams = new URLSearchParams(href.split('?')[1]);
                            const page = urlParams.get('paged') || 1;
                            loadProducts(true, parseInt(page));
                            // Scroll back to top of section
                            document.querySelector('.cmr-latest-section').scrollIntoView({ behavior: 'smooth' });
                        });
                    });
                }

                // Initial Load
                loadProducts(true);

                // Load More Click
                loadMoreBtn.addEventListener('click', function() {
                    currentPage++;
                    loadMoreCount++;
                    loadProducts();
                });

                // Filter Click
                filterPills.forEach(pill => {
                    pill.addEventListener('click', function() {
                        filterPills.forEach(p => p.classList.remove('active'));
                        this.classList.add('active');
                        currentCategory = this.getAttribute('data-cat');
                        loadProducts(true);
                    });
                });

                // Search
                searchBtn.addEventListener('click', function() {
                    currentSearch = searchInput.value;
                    loadProducts(true);
                });

                searchInput.addEventListener('keypress', function(e) {
                    if ( e.key === 'Enter' ) {
                        currentSearch = searchInput.value;
                        loadProducts(true);
                    }
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_latest_reports', 'cmr_latest_reports_shortcode' );

// AJAX Handler for Latest Reports
if ( ! function_exists( 'cmr_load_reports_ajax' ) ) {
    function cmr_load_reports_ajax() {
        $paged = isset( $_POST['paged'] ) ? intval( $_POST['paged'] ) : 1;
        $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
        $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
        $posts_per_page = isset( $_POST['posts_per_page'] ) ? intval( $_POST['posts_per_page'] ) : 8;

        $args = array(
            'limit' => $posts_per_page,
            'page'  => $paged,
            'status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        );

        if ( ! empty( $category ) ) {
            $args['category'] = array( $category );
        }

        $products = wc_get_products( $args );
        
        // Manual search filtering since wc_get_products 's' parameter can be inconsistent in some versions, but we'll try 's' first.
        // Wait, 's' works for standard queries but for wc_get_products it might need a specific parameter if not using WP_Query. 
        // We will use standard WP_Query for complex filtering just to be safe.
        
        $query_args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'orderby' => 'date',
            'order' => 'DESC',
        );

        if ( ! empty( $category ) ) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => $category,
                ),
            );
        }

        if ( ! empty( $search ) ) {
            $query_args['s'] = $search;
        }

        $query = new WP_Query( $query_args );

        if ( ! $query->have_posts() ) {
            wp_send_json_error( 'No products found' );
        }

        ob_start();
        while ( $query->have_posts() ) {
            $query->the_post();
            global $product;
            
            $image_url = wp_get_attachment_image_src( $product->get_image_id(), 'medium' );
            $image_url = $image_url ? $image_url[0] : 'https://via.placeholder.com/400x400';
            
            $cats = $product->get_category_ids();
            $cat_name = 'Report';
            if ( ! empty($cats) ) {
                $term = get_term_by( 'id', $cats[0], 'product_cat' );
                if ( $term ) {
                    $cat_name = $term->name;
                }
            }
            
            $badge_text = 'NEW';
            $badge_icon = 'fa-solid fa-circle-check';
            $badge_color = '#ea580c';
            
            if ( $product->is_featured() ) {
                $badge_text = 'FEATURED';
                $badge_icon = 'fa-solid fa-bookmark';
                $badge_color = '#6b46c1';
            }
            ?>
            <div class="cmr-lr-card">
                <div class="cmr-lr-image-wrap">
                    <div class="cmr-lr-badge" style="color: <?php echo esc_attr($badge_color); ?>;"><i class="<?php echo esc_attr($badge_icon); ?>"></i> <?php echo esc_html($badge_text); ?></div>
                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
                </div>
                <div class="cmr-lr-content">
                    <div class="cmr-lr-category">&mdash; <?php echo esc_html( $cat_name ); ?></div>
                    <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="cmr-lr-title"><?php echo esc_html( $product->get_name() ); ?></a>
                    
                    <div class="cmr-lr-stars">
                        <?php 
                        $rating = floatval( $product->get_average_rating() );
                        $count = intval( $product->get_review_count() );
                        for ( $s = 1; $s <= 5; $s++ ) {
                            if ( $s <= $rating ) echo '<i class="fa-solid fa-star"></i>';
                            elseif ( $s - 0.5 <= $rating ) echo '<i class="fa-solid fa-star-half-stroke"></i>';
                            else echo '<i class="fa-regular fa-star"></i>';
                        }
                        ?>
                        <span>(<?php echo $count; ?>)</span>
                    </div>
                    
                    <div class="cmr-lr-price">
                        <?php echo $product->get_price_html(); ?>
                    </div>
                    
                    <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="cmr-lr-btn">
                        Download Report <i class="fa-solid fa-arrow-down"></i>
                    </a>
                </div>
            </div>
            <?php
        }
        $html = ob_get_clean();

        $has_more = ( $query->max_num_pages > $paged );

        $pagination_html = '';
        if ( $query->max_num_pages > 1 ) {
            // Include custom SVG arrows for Prev/Next like the articles
            $prev_icon = '<svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.5 15L1.5 8L8.5 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            $next_icon = '<svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 15L8.5 8L1.5 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            
            $pagination_html = paginate_links( array(
                'base' => '%_%',
                'format' => '?paged=%#%',
                'current' => $paged,
                'total' => $query->max_num_pages,
                'prev_text' => $prev_icon,
                'next_text' => $next_icon,
                'type' => 'plain'
            ) );
        }

        wp_send_json_success( array(
            'html' => $html,
            'has_more' => $has_more,
            'pagination' => $pagination_html
        ) );
    }
}
add_action( 'wp_ajax_cmr_load_reports', 'cmr_load_reports_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_reports', 'cmr_load_reports_ajax' );
