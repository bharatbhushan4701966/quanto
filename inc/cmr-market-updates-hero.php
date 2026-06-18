<?php
/**
 * Shortcode for Market Updates Hero Section
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_market_updates_hero_shortcode' ) ) {
    function cmr_market_updates_hero_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'post_type'      => 'cmr_news',
            'posts_per_page' => 5,
        ), $atts );

        $query_args = array(
            'post_type'      => array('post', 'cmr_news'),
            'posts_per_page' => $atts['posts_per_page'],
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'     => '_thumbnail_id',
                    'compare' => 'EXISTS'
                ),
            ),
        );

        $hero_posts = get_posts( $query_args );

        $posts_data = array();
        if ( !empty($hero_posts) ) {
            foreach ( $hero_posts as $post_obj ) {
                
                $thumbnail_url = get_the_post_thumbnail_url( $post_obj->ID, 'full' );
                if ( ! $thumbnail_url ) {
                    $thumbnail_url = 'https://via.placeholder.com/1200x800?text=Featured+Image';
                }
                
                $category_name = 'Market Updates';
                $terms = get_the_terms( $post_obj->ID, 'category' );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    $category_name = $terms[0]->name;
                }
                
                $post_date = get_the_date('M d, Y', $post_obj);
                
                $excerpt = get_the_excerpt($post_obj);
                if ( empty( $excerpt ) ) {
                    $content = $post_obj->post_content;
                    $excerpt = wp_trim_words( $content, 20 );
                } else {
                    $excerpt = wp_trim_words( $excerpt, 20 );
                }

                $posts_data[] = array(
                    'title'    => get_the_title($post_obj),
                    'link'     => get_permalink($post_obj->ID),
                    'image'    => $thumbnail_url,
                    'category' => $category_name,
                    'date'     => $post_date,
                    'excerpt'  => wp_kses_post( $excerpt ),
                );
            }
        }
                $slider_id = 'cmr-mu-hero-' . wp_rand(1000, 9999);

        ob_start();
        ?>
        <style>
            .cmr-mu-hero-wrap {
                font-family: 'Instrument Sans', sans-serif !important;
                max-width: 1280px;
                margin: 60px auto;
                padding: 0 20px;
                text-align: center;
            }
            .cmr-mu-hero-breadcrumbs {
                font-size: 13px;
                color: #555;
                margin-bottom: 25px;
            }
            .cmr-mu-hero-breadcrumbs a {
                color: #555;
                text-decoration: none;
            }
            .cmr-mu-hero-breadcrumbs span {
                margin: 0 8px;
            }
            
            .cmr-mu-hero-title {
                font-size: 60px;
                font-weight: 600;
                color: #111;
                margin: 0 0 15px 0;
                line-height: 1.1;
                letter-spacing: -1.5px;
            }
            .cmr-mu-hero-subtitle {
                font-size: 18px;
                color: #555;
                margin: 0 0 40px 0;
            }

            /* Search Bar */
            .cmr-mu-hero-search {
                position: relative;
                max-width: 800px;
                margin: 0 auto 30px auto;
            }
            .cmr-mu-hero-search input {
                width: 100%;
                height: 60px;
                padding: 0 70px;
                border: 1px solid #6B3FA0;
                border-radius: 40px;
                font-size: 16px;
                color: #333;
                background: #fff;
                box-sizing: border-box;
                outline: none;
            }
            .cmr-mu-hero-search input::placeholder {
                color: #aaa;
            }
            .cmr-mu-hero-search-icon-left {
                position: absolute;
                left: 25px;
                top: 50%;
                transform: translateY(-50%);
                color: #6B3FA0;
                display: flex;
            }
            .cmr-mu-hero-search-btn {
                position: absolute;
                right: 8px;
                top: 8px;
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: #6B3FA0;
                border: none;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background 0.3s ease;
            }
            .cmr-mu-hero-search-btn:hover {
                background: #502e7a;
            }

            /* Categories */
            .cmr-mu-hero-cats {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 12px;
                margin-bottom: 60px;
            }
            .cmr-mu-hero-cat-pill {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                background: #fff;
                border: 1px solid #eaeaea;
                border-radius: 30px;
                font-size: 13px;
                font-weight: 600;
                color: #111;
                text-decoration: none;
                transition: all 0.3s ease;
            }
            .cmr-mu-hero-cat-pill:hover {
                border-color: #6B3FA0;
                color: #6B3FA0;
            }
            .cmr-mu-hero-cat-pill svg {
                width: 14px;
                height: 14px;
                color: #666;
            }

            /* Slider */
            .cmr-mu-slider-wrapper {
                position: relative;
                width: 100%;
                overflow: hidden;
            }
            .cmr-mu-slider-track {
                display: flex;
                transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
            }
            .cmr-mu-slide {
                flex: 0 0 90%;
                margin-right: 30px;
                display: flex;
                background: #F8F9FB;
                border: 1px solid #eaeaea;
                min-height: 400px;
                text-align: left;
            }
            
            .cmr-mu-slide-img {
                flex: 0 0 55%;
                position: relative;
                overflow: hidden;
            }
            .cmr-mu-slide-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            .cmr-mu-slide-badge {
                position: absolute;
                top: 20px;
                left: 20px;
                background: rgba(255,255,255,0.9);
                color: #6B3FA0;
                padding: 6px 14px;
                border-radius: 4px;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 1px;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            
            .cmr-mu-slide-content {
                flex: 1;
                padding: 40px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            .cmr-mu-slide-meta {
                font-size: 13px;
                font-weight: 500;
                color: #666;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .cmr-mu-slide-meta::before {
                content: "";
                display: block;
                width: 24px;
                height: 1px;
                background: #ccc;
            }
            .cmr-mu-slide-title {
                font-size: 32px;
                font-weight: 700;
                color: #111;
                margin: 0 0 20px 0;
                line-height: 1.2;
                letter-spacing: -1px;
            }
            .cmr-mu-slide-desc {
                font-size: 16px;
                color: #444;
                line-height: 1.6;
                margin-bottom: 30px;
            }
            .cmr-mu-slide-link {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-size: 14px;
                font-weight: 700;
                color: #111;
                text-decoration: none;
                border-bottom: 1px solid #111;
                padding-bottom: 4px;
                align-self: flex-start;
                transition: color 0.3s ease, border-color 0.3s ease;
            }
            .cmr-mu-slide-link:hover {
                color: #6B3FA0;
                border-color: #6B3FA0;
            }
            .cmr-mu-slide-link svg {
                width: 14px;
                height: 14px;
            }

            /* Pagination */
            .cmr-mu-pagination {
                display: flex;
                justify-content: center;
                gap: 8px;
                margin-top: 30px;
            }
            .cmr-mu-dot {
                width: 20px;
                height: 4px;
                background: #e0e0e0;
                border-radius: 4px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            .cmr-mu-dot.active {
                background: #6B3FA0;
                width: 30px;
            }

            @media (max-width: 992px) {
                .cmr-mu-slide {
                    flex-direction: column;
                    flex: 0 0 100%;
                    margin-right: 0;
                }
                .cmr-mu-slide-img {
                    height: 250px;
                }
                .cmr-mu-slide-content {
                    padding: 30px 20px;
                }
                .cmr-mu-hero-title {
                    font-size: 38px;
                }
            }
        </style>

        <div class="cmr-mu-hero-wrap">
            <div class="cmr-mu-hero-breadcrumbs">
                <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                <span>&gt;</span>
                <span style="color: #111; font-weight: 500;">Market Updates</span>
            </div>

            <h1 class="cmr-mu-hero-title">Market Intelligence &<br>Real-Time Updates</h1>
            <p class="cmr-mu-hero-subtitle">Actionable insights. Real-time signals. Smarter decisions. Stay ahead of what moves markets.</p>

            <form id="cmr-mu-hero-search-form" class="cmr-mu-hero-search" action="<?php echo esc_url(home_url('/')); ?>" method="get">
                <div class="cmr-mu-hero-search-icon-left">
                    <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/cmrlogo-with-oly-c.svg" alt="CMR Logo" style="width: 24px; height: auto;">
                </div>
                <input type="text" name="s" placeholder="Search..." required>
                <button type="submit" class="cmr-mu-hero-search-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </button>
            </form>

            <div class="cmr-mu-hero-cats">
                <a href="#" class="cmr-mu-hero-cat-pill">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>
                    Automotive
                </a>
                <a href="#" class="cmr-mu-hero-cat-pill">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                    Consumer Tech
                </a>
                <a href="#" class="cmr-mu-hero-cat-pill">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    Digital Supply Chain
                </a>
                <a href="#" class="cmr-mu-hero-cat-pill">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="2"></circle><path d="M16.24 7.76a6 6 0 0 1 0 8.49m-8.48-.01a6 6 0 0 1 0-8.49m11.31-2.82a10 10 0 0 1 0 14.14m-14.14 0a10 10 0 0 1 0-14.14"></path></svg>
                    IT & Telecom
                </a>
                <a href="#" class="cmr-mu-hero-cat-pill">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>
                    Semiconductor
                </a>
            </div>

            <?php if ( ! empty($posts_data) ) : ?>
            <div class="cmr-mu-slider-wrapper" id="<?php echo esc_attr($slider_id); ?>">
                <div class="cmr-mu-slider-track">
                    <?php foreach ( $posts_data as $index => $post ) : ?>
                        <div class="cmr-mu-slide">
                            <div class="cmr-mu-slide-img">
                                <img src="<?php echo esc_url($post['image']); ?>" alt="<?php echo esc_attr($post['title']); ?>">
                                <div class="cmr-mu-slide-badge">
                                    <svg width="10" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg> FEATURED
                                </div>
                            </div>
                            <div class="cmr-mu-slide-content">
                                <div class="cmr-mu-slide-meta"><?php echo esc_html($post['category']); ?> | <?php echo esc_html($post['date']); ?></div>
                                <h3 class="cmr-mu-slide-title"><?php echo esc_html($post['title']); ?></h3>
                                <div class="cmr-mu-slide-desc"><?php echo wp_kses_post($post['excerpt']); ?></div>
                                <a href="<?php echo esc_url($post['link']); ?>" class="cmr-mu-slide-link">
                                    Read More 
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="cmr-mu-pagination" id="<?php echo esc_attr($slider_id); ?>-dots">
                <?php foreach ( $posts_data as $index => $post ) : ?>
                    <div class="cmr-mu-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo esc_attr($index); ?>"></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const slider = document.getElementById('<?php echo esc_js($slider_id); ?>');
                if (!slider) return;

                const track = slider.querySelector('.cmr-mu-slider-track');
                const slides = slider.querySelectorAll('.cmr-mu-slide');
                const dotsContainer = document.getElementById('<?php echo esc_js($slider_id); ?>-dots');
                const dots = dotsContainer ? dotsContainer.querySelectorAll('.cmr-mu-dot') : [];
                
                if (!track || slides.length === 0) return;

                let currentIndex = 0;

                function updateSlider(index) {
                    currentIndex = index;
                    const slideWidth = slides[0].getBoundingClientRect().width;
                    
                    // Gap is 30px, plus we use 90% flex basis if there's more than 1 slide, 
                    // but we can just measure exactly:
                    const slideStyle = window.getComputedStyle(slides[0]);
                    const marginRight = parseFloat(slideStyle.marginRight) || 0;
                    const moveAmount = (slideWidth + marginRight) * index;
                    
                    track.style.transform = 'translateX(-' + moveAmount + 'px)';

                    dots.forEach(function(dot, i) {
                        if (i === index) {
                            dot.classList.add('active');
                        } else {
                            dot.classList.remove('active');
                        }
                    });
                }

                dots.forEach(function(dot) {
                    dot.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-slide'));
                        updateSlider(index);
                    });
                });
                window.addEventListener('resize', function() {
                    updateSlider(currentIndex);
                });
            });
        </script>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var muSearchForm = document.getElementById('cmr-mu-hero-search-form');
            if (muSearchForm) {
                muSearchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var searchTerm = this.querySelector('input[name="s"]').value;
                    if (!searchTerm) return;
                    
                    var grid = document.querySelector('.cmr-mui-grid');
                    if (grid) {
                        grid.scrollIntoView({behavior: 'smooth', block: 'start'});
                        grid.innerHTML = '<p style="grid-column:1/-1; text-align:center; padding:40px; font-size:18px;">Searching...</p>';
                        
                        var loadMoreBtn = document.getElementById('cmr-mui-load-more');
                        if (loadMoreBtn) loadMoreBtn.style.display = 'none';
                        var paginationWrap = document.getElementById('cmr-mui-pagination-wrap');
                        if (paginationWrap) paginationWrap.style.display = 'none';

                        var formData = new FormData();
                        formData.append('action', 'cmr_insights_ajax_search');
                        formData.append('search_term', searchTerm);
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
                    }
                });
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_market_updates_hero', 'cmr_market_updates_hero_shortcode' );

