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

        $query_args = array(
            'post_type'      => 'post',
            'posts_per_page' => intval( $atts['posts_per_page'] ),
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

        ob_start();
        ?>
        <style>
            .cmr-mui-section {
                font-family: 'Instrument Sans', sans-serif !important;
                width: 100%;
                max-width: 1200px;
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
                padding: 12px 40px 12px 20px;
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
            .cmr-mui-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
            }
            .cmr-mui-card {
                display: flex;
                flex-direction: column;
                background: #fff;
            }
            .cmr-mui-card-img {
                width: 100%;
                height: 240px;
                object-fit: cover;
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
            }
            .cmr-mui-card-excerpt {
                font-size: 15px;
                color: #555;
                line-height: 1.5;
                margin: 0 0 20px 0;
                flex-grow: 1;
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
                transition: opacity 0.2s;
            }
            .cmr-mui-read-more svg {
                width: 14px;
                height: 14px;
                margin-left: 6px;
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
            }
            @media (max-width: 768px) {
                .cmr-mui-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <div class="cmr-mui-section">
            <div class="cmr-mui-header">
                <h2 class="cmr-mui-title">Market Updates & Strategic Insights</h2>
                <p class="cmr-mui-subtitle">Track real-time shifts, growth signals, and strategic developments shaping key industries.</p>
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
                <?php if ( ! empty($insights_posts) ) : ?>
                    <?php foreach ( $insights_posts as $post_obj ) : 
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
                    <div class="cmr-mui-card">
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
        </div>

        <script>
            // Sticky functionality is handled by CSS position: sticky
            // Search functionality wrapper
            document.addEventListener('DOMContentLoaded', function() {
                var searchInput = document.querySelector('.cmr-mui-search-wrap input');
                if (searchInput) {
                    searchInput.addEventListener('keyup', function(e) {
                        var val = e.target.value.toLowerCase();
                        var cards = document.querySelectorAll('.cmr-mui-card');
                        cards.forEach(function(card) {
                            var title = card.querySelector('.cmr-mui-card-title').innerText.toLowerCase();
                            if (title.indexOf(val) > -1) {
                                card.style.display = '';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    });
                }
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_market_updates_insights', 'cmr_market_updates_insights_shortcode' );
