<?php
/**
 * Shortcode for Industry Intelligence List with Banner
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_industry_intel_list_shortcode' ) ) {
    function cmr_industry_intel_list_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'posts_per_page' => 3,
            'category'       => '',
        ), $atts );

        $query_args = array(
            'post_type'      => 'cmr_news',
            'posts_per_page' => $atts['posts_per_page'],
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        if ( ! empty( $atts['category'] ) ) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field( $atts['category'] ),
                ),
            );
        }

        $insights_query = new WP_Query( $query_args );

        ob_start();
        ?>
        <style>
            .cmr-intel-list-wrapper {
                font-family: 'Instrument Sans', sans-serif !important;
                max-width: 1280px;
                margin: 0 auto;
                display: flex;
                flex-direction: column;
                gap: 40px;
            }
            .cmr-intel-list-item {
                display: flex;
                gap: 30px;
                align-items: center;
            }
            .cmr-intel-list-img {
                flex: 0 0 45%;
                aspect-ratio: 16 / 9;
                overflow: hidden;
            }
            .cmr-intel-list-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                transition: transform 0.3s ease;
            }
            .cmr-intel-list-img:hover img {
                transform: scale(1.05);
            }
            .cmr-intel-list-content {
                flex: 1;
                display: flex;
                flex-direction: column;
            }
            .cmr-intel-list-meta {
                display: flex;
                align-items: center;
                gap: 15px;
                font-size: 12px;
                color: #888;
                margin-bottom: 12px;
            }
            .cmr-intel-list-cat {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .cmr-intel-list-cat::before {
                content: '';
                display: block;
                width: 20px;
                height: 1px;
                background: #ccc;
            }
            .cmr-intel-list-title {
                font-size: 20px;
                font-weight: 600;
                color: #111;
                margin: 0 0 12px 0;
                line-height: 1.4;
                letter-spacing: -0.3px;
            }
            .cmr-intel-list-title a {
                color: #111;
                text-decoration: none;
                transition: color 0.3s ease;
            }
            .cmr-intel-list-title a:hover {
                color: #555;
            }
            .cmr-intel-list-excerpt {
                font-size: 15px;
                color: #555;
                line-height: 1.6;
                margin-bottom: 20px;
            }
            .cmr-intel-list-more {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 13px;
                font-weight: 600;
                color: #111;
                text-decoration: none;
                border-bottom: 1.5px solid #111;
                padding-bottom: 2px;
                align-self: flex-start;
                transition: all 0.3s ease;
            }
            .cmr-intel-list-more:hover {
                color: #555;
                border-color: #555;
            }
            .cmr-intel-list-more svg {
                width: 12px;
                height: 12px;
                transition: transform 0.3s ease;
            }
            .cmr-intel-list-more:hover svg {
                transform: translate(2px, -2px);
            }
            /* Banner */
            .cmr-intel-list-banner {
                background: #4B24B3;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 35px 40px;
                color: #fff;
                margin: 10px 0;
            }
            .cmr-intel-list-banner-left h3 {
                font-size: 45px;
                font-weight: 600;
                margin: 0;
                color: #fff;
                letter-spacing: -0.5px;
            }
            .cmr-intel-list-banner-mid {
                flex: 1;
                padding: 0 30px;
            }
            .cmr-intel-list-banner-mid p {
                font-size: 15px;
                line-height: 1.5;
                margin: 0;
                color: rgba(255, 255, 255, 0.9);
                max-width: 320px;
            }
            .cmr-intel-list-banner-right .download-btn {
                background: #ffffff !important;
                color: #111111 !important;
                text-decoration: none;
                padding: 12px 24px;
                border-radius: 40px;
                font-weight: 600;
                font-size: 14px;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
            }
            .cmr-intel-list-banner-right .download-btn:hover {
                background: #f0f0f0;
                transform: translateY(-2px);
            }
            .cmr-intel-list-banner-right .download-btn svg {
                width: 14px;
                height: 14px;
            }
            .cmr-intel-list-load-more {
                text-align: center;
                margin-top: 20px;
                width: 100%;
            }
            .cmr-intel-list-load-more button {
                background: transparent;
                border: 1px solid #ccc;
                color: #111;
                font-size: 14px;
                font-weight: 600;
                padding: 0;
                border-radius: 40px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-family: inherit;
                width: 288px;
                height: 54px;
                display: inline-flex;
                justify-content: center;
                align-items: center;
            }
            .cmr-intel-list-load-more button:hover {
                background: #fafafa;
                border-color: #111;
            }
            @media (max-width: 768px) {
                .cmr-intel-list-item {
                    flex-direction: column;
                    gap: 20px;
                }
                .cmr-intel-list-banner {
                    flex-direction: column;
                    text-align: center;
                    gap: 20px;
                    padding: 30px 20px;
                }
                .cmr-intel-list-banner-mid {
                    padding: 0;
                }
            }
        </style>

        <div class="cmr-intel-list-wrapper">
            <?php if ( $insights_query->have_posts() ) : ?>
                <div class="cmr-intel-list-items" style="display:flex; flex-direction:column; gap:40px; width: 100%;">
                <?php
                $count = 0;
                while ( $insights_query->have_posts() ) : $insights_query->the_post();
                    $count++;
                    $post_title = get_the_title();
                    $post_link = get_permalink();
                    $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                    if ( ! $thumbnail_url ) {
                        $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
                    }
                    
                    $category_name = 'Industry Intelligence';
                    $terms = get_the_terms( get_the_ID(), 'category' );
                    if ( $terms && ! is_wp_error( $terms ) ) {
                        $category_name = $terms[0]->name;
                    }

                    $content = get_post_field( 'post_content', get_the_ID() );
                    $word_count = str_word_count( strip_tags( $content ) );
                    $read_time = ceil( $word_count / 200 );
                    if ($read_time < 1) $read_time = 1;
                    ?>
                    
                    <div class="cmr-intel-list-item">
                        <div class="cmr-intel-list-img">
                            <a href="<?php echo esc_url( $post_link ); ?>">
                                <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                            </a>
                        </div>
                        <div class="cmr-intel-list-content">
                            <div class="cmr-intel-list-meta">
                                <span class="cmr-intel-list-cat"><?php echo esc_html( $category_name ); ?></span>
                                <span><?php echo esc_html( $read_time ); ?> min read</span>
                            </div>
                            <h3 class="cmr-intel-list-title">
                                <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                            </h3>
                            <div class="cmr-intel-list-excerpt">
                                <?php 
                                $excerpt = get_the_excerpt();
                                if ( empty( $excerpt ) ) {
                                    $excerpt = wp_trim_words( $content, 18 );
                                } else {
                                    $excerpt = wp_trim_words( $excerpt, 18 );
                                }
                                echo wp_kses_post( $excerpt ); 
                                ?>
                            </div>
                            <a href="<?php echo esc_url( $post_link ); ?>" class="cmr-intel-list-more">
                                More Details 
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="7" y1="17" x2="17" y2="7"></line>
                                    <polyline points="7 7 17 7 17 17"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <?php if ( $count === 2 ) : ?>
                        <div class="cmr-intel-list-banner">
                            <div class="cmr-intel-list-banner-left">
                                <h3>Need deeper insights?</h3>
                            </div>
                            <div class="cmr-intel-list-banner-mid">
                                <p>Talk to our analysts for tailored recommendations across your sector.</p>
                            </div>
                            <div class="cmr-intel-list-banner-right">
                                <a href="/contact" class="download-btn">
                                    Get Industry Insights 
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="7" y1="17" x2="17" y2="7"></line>
                                        <polyline points="7 7 17 7 17 17"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endwhile; ?>
                </div> <!-- /.cmr-intel-list-items -->
                
                <?php if ( $insights_query->max_num_pages > 1 ) : ?>
                <div class="cmr-intel-list-load-more">
                    <button data-page="1" data-max="<?php echo esc_attr( $insights_query->max_num_pages ); ?>" data-category="<?php echo esc_attr( $atts['category'] ); ?>" id="cmr-intel-load-more-btn">Load More</button>
                </div>
                <?php endif; ?>
            <?php else : ?>
                <p>No insights found.</p>
            <?php endif; wp_reset_postdata(); ?>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var loadMoreBtn = document.getElementById('cmr-intel-load-more-btn');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function() {
                    var button = this;
                    var currentPage = parseInt(button.getAttribute('data-page'));
                    var maxPage = parseInt(button.getAttribute('data-max'));
                    var cat = button.getAttribute('data-category');
                    var nextPage = currentPage + 1;
                    
                    button.textContent = 'Loading...';
                    button.disabled = true;
                    
                    var data = new FormData();
                    data.append('action', 'cmr_industry_intel_list_load_more');
                    data.append('page', nextPage);
                    if (cat) {
                        data.append('category', cat);
                    }
                    
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        body: data
                    })
                    .then(response => response.text())
                    .then(html => {
                        if (html.trim() !== '') {
                            var container = document.querySelector('.cmr-intel-list-items');
                            container.insertAdjacentHTML('beforeend', html);
                            button.setAttribute('data-page', nextPage);
                            button.textContent = 'Load More';
                            button.disabled = false;
                            
                            if (nextPage >= maxPage) {
                                button.parentElement.style.display = 'none';
                            }
                        } else {
                            button.parentElement.style.display = 'none';
                        }
                    })
                    .catch(err => {
                        console.error('Error loading more insights:', err);
                        button.textContent = 'Load More';
                        button.disabled = false;
                    });
                });
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_industry_intel_list', 'cmr_industry_intel_list_shortcode' );

// AJAX Handler for Load More
add_action( 'wp_ajax_cmr_industry_intel_list_load_more', 'cmr_industry_intel_list_load_more_ajax' );
add_action( 'wp_ajax_nopriv_cmr_industry_intel_list_load_more', 'cmr_industry_intel_list_load_more_ajax' );
function cmr_industry_intel_list_load_more_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $query_args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $paged,
    );
    
    // Add category filter if provided
    if ( isset( $_POST['category'] ) && ! empty( $_POST['category'] ) ) {
        $query_args['tax_query'] = array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $_POST['category'] ),
            )
        );
    }
    
    $insights_query = new WP_Query( $query_args );
    if ( $insights_query->have_posts() ) {
        while ( $insights_query->have_posts() ) {
            $insights_query->the_post();
            
            $post_title = get_the_title();
            $post_link = get_permalink();
            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
            if ( ! $thumbnail_url ) {
                $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
            }
            
            $category_name = 'Industry Intelligence';
            $terms = get_the_terms( get_the_ID(), 'category' );
            if ( $terms && ! is_wp_error( $terms ) ) {
                $category_name = $terms[0]->name;
            }

            $content = get_post_field( 'post_content', get_the_ID() );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            
            $excerpt = get_the_excerpt();
            if ( empty( $excerpt ) ) {
                $excerpt = wp_trim_words( $content, 18 );
            } else {
                $excerpt = wp_trim_words( $excerpt, 18 );
            }
            ?>
            <div class="cmr-intel-list-item">
                <div class="cmr-intel-list-img">
                    <a href="<?php echo esc_url( $post_link ); ?>">
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                    </a>
                </div>
                <div class="cmr-intel-list-content">
                    <div class="cmr-intel-list-meta">
                        <span class="cmr-intel-list-cat"><?php echo esc_html( $category_name ); ?></span>
                        <span><?php echo esc_html( $read_time ); ?> min read</span>
                    </div>
                    <h3 class="cmr-intel-list-title">
                        <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                    </h3>
                    <div class="cmr-intel-list-excerpt">
                        <?php echo wp_kses_post( $excerpt ); ?>
                    </div>
                    <a href="<?php echo esc_url( $post_link ); ?>" class="cmr-intel-list-more">
                        More Details 
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
            <?php
        }
    }
    wp_die();
}
