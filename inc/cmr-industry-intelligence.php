<?php
/**
 * Shortcode for Industry Intelligence Section
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_industry_intelligence_shortcode' ) ) {
    function cmr_industry_intelligence_shortcode( $atts ) {
        wp_enqueue_style( 'cmr-industry-intelligence' );

        $atts = shortcode_atts( array(
            'posts_per_page'   => 6,
            'nav_title'        => 'Automotive',
            'show_nav'         => 'true',
            'section_title'    => 'Latest Industry Intelligence',
            'section_subtitle' => 'Explore real-time insights and strategic analysis shaping industries and business decisions.',
        ), $atts );

        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        $query_args = array(
            'post_type'      => array( 'post', 'cmr_news' ),
            'posts_per_page' => 20, // Fetch more to allow for deduplication skipping
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'paged'          => $paged,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => 'industry-connect',
                ),
            ),
        );

        $insights_query = new WP_Query( $query_args );
        $insights_posts = $insights_query->posts;

        ob_start();
        ?>
        <div class="cmr-industry-intel-section">
            
            <?php if ( ! empty( $atts['section_title'] ) ) : ?>
            <div class="cmr-intel-header" style="margin-bottom: 40px;">
                <h2 style="font-size: 44px; font-weight: 600; color: #111; margin: 0 0 12px 0; letter-spacing: -1px; font-family: 'Instrument Sans', sans-serif; line-height: 1.2;">
                    <?php echo esc_html( $atts['section_title'] ); ?>
                </h2>
                <?php if ( ! empty( $atts['section_subtitle'] ) ) : ?>
                <p style="font-size: 16px; color: #333; margin: 0; font-family: 'Instrument Sans', sans-serif; max-width: 800px; line-height: 1.5;">
                    <?php echo esc_html( $atts['section_subtitle'] ); ?>
                </p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ( $atts['show_nav'] === 'true' ) : ?>
            <div class="intel-nav-bar">
                <div class="intel-nav-title">
                    <?php echo esc_html( $atts['nav_title'] ); ?>
                </div>
                <div class="intel-nav-links">
                    <a href="#">Overview</a>
                    <a href="#">Insights</a>
                    <a href="#">Reports</a>
                    <a href="#cmr-market-updates">Market Updates</a>
                    <a href="#">Newsroom</a>
                    <a href="#">CMR in news</a>
                </div>
            </div>
            <?php endif; ?>

            <?php if ( $insights_query->have_posts() ) : ?>
                <div class="intel-grid">
                    <?php
                    $seen_titles = array();
                    $displayed_count = 0;
                    
                    while ( $insights_query->have_posts() && $displayed_count < $atts['posts_per_page'] ) : $insights_query->the_post();
                        $post_title = get_the_title();
                        
                        // Prevent duplicates if multiple copies of the same article exist
                        if ( in_array( $post_title, $seen_titles ) ) {
                            continue;
                        }
                        $seen_titles[] = $post_title;
                        $displayed_count++;
                        
                        $post_link = get_permalink();
                        $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                        if ( ! $thumbnail_url ) {
                            $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
                        }
                        
                        // Categories / Tags
                        $category_name = 'Industry Intelligence';
                        $terms = get_the_terms( get_the_ID(), 'category' );
                        if ( $terms && ! is_wp_error( $terms ) ) {
                            $category_name = $terms[0]->name;
                        }

                        // Calculate reading time
                        $content = get_the_content();
                        $word_count = str_word_count( strip_tags( $content ) );
                        $read_time = ceil( $word_count / 200 );
                        if ($read_time < 1) $read_time = 1;
                        ?>
                        
                        <div class="intel-card">
                            <div class="intel-card-img">
                                <a href="<?php echo esc_url( $post_link ); ?>">
                                    <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                                </a>
                            </div>
                            <div class="intel-card-content">
                                <div class="intel-meta">
                                    <span class="intel-category"><?php echo esc_html( $category_name ); ?></span>
                                    <span class="intel-read-time"><?php echo esc_html( $read_time ); ?> min read</span>
                                </div>
                                <h3 class="intel-title">
                                    <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                                </h3>
                                <div class="intel-excerpt">
                                    <?php 
                                    $excerpt = get_the_excerpt();
                                    if ( empty( $excerpt ) ) {
                                        $excerpt = wp_trim_words( $content, 12 );
                                    } else {
                                        $excerpt = wp_trim_words( $excerpt, 12 );
                                    }
                                    echo wp_kses_post( $excerpt ); 
                                    ?>
                                </div>
                                <a href="<?php echo esc_url( $post_link ); ?>" class="intel-read-more">
                                    More Details 
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="7" y1="17" x2="17" y2="7"></line>
                                        <polyline points="7 7 17 7 17 17"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                
                <div class="intel-pagination-wrap">
                    <?php if ( $paged == 1 && $insights_query->max_num_pages > 1 ) : ?>
                        <div class="intel-load-more-wrap" style="text-align: center; margin-top: 30px;">
                            <button class="intel-load-more-btn" data-page="1" data-max="<?php echo esc_attr($insights_query->max_num_pages); ?>" data-ajaxurl="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" style="background: transparent; border: 1px solid #ccc; color: #111; font-size: 14px; font-weight: 600; border-radius: 40px; cursor: pointer; transition: all 0.3s ease; width: 288px; height: 54px; display: inline-flex; justify-content: center; align-items: center;">Load More</button>
                        </div>
                    <?php elseif ( $insights_query->max_num_pages > 1 ) : ?>
                        <div class="intel-numeric-pagination" style="text-align: center; margin-top: 30px; display: flex; justify-content: center; gap: 10px;">
                            <?php 
                            echo paginate_links( array(
                                'total'   => $insights_query->max_num_pages,
                                'current' => $paged,
                                'format'  => '?paged=%#%',
                                'prev_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                                'next_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                            ) ); 
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const btn = document.querySelector('.intel-load-more-btn');
                    if (btn) {
                        let clickCount = 0;
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            let page = parseInt(btn.getAttribute('data-page'));
                            let max = parseInt(btn.getAttribute('data-max'));
                            let ajaxurl = btn.getAttribute('data-ajaxurl');
                            let nextPage = page + 1;
                            
                            btn.innerText = 'Loading...';
                            btn.disabled = true;

                            fetch(ajaxurl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: 'action=cmr_load_more_intel&page=' + nextPage + '&base_url=' + encodeURIComponent(window.location.pathname)
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.querySelector('.intel-grid').insertAdjacentHTML('beforeend', data.data.html);
                                    btn.setAttribute('data-page', nextPage);
                                    clickCount++;
                                    
                                    if (nextPage >= max || clickCount >= 2) {
                                        // Replace with pagination
                                        document.querySelector('.intel-pagination-wrap').innerHTML = '<div class="intel-numeric-pagination" style="text-align: center; margin-top: 30px; display: flex; justify-content: center; gap: 10px;">' + data.data.pagination + '</div>';
                                    } else {
                                        btn.innerText = 'Load More';
                                        btn.disabled = false;
                                    }
                                } else {
                                    btn.innerText = 'No more posts';
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                btn.innerText = 'Load More';
                                btn.disabled = false;
                            });
                        });
                    }
                });
                </script>
                
            <?php else : ?>
                <p>No insights found.</p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_industry_intelligence', 'cmr_industry_intelligence_shortcode' );
