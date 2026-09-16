<?php
/**
 * Shortcode for Stay Updated / Latest News Section
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_stay_updated_shortcode' ) ) {
    function cmr_stay_updated_shortcode( $atts ) {
        wp_enqueue_style( 'cmr-stay-updated' );

        $atts = shortcode_atts( array(
            'posts_per_page' => 3,
        ), $atts );

        $query_args = array(
            'post_type'      => 'cmr_news',
            'posts_per_page' => $atts['posts_per_page'],
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        $news_query = new WP_Query( $query_args );

        ob_start();
        ?>
        <style>
        @media (max-width: 768px) {
            .elementor-element:has(.cmr-stay-updated-section),
            .e-con:has(.cmr-stay-updated-section),
            .e-con-boxed:has(.cmr-stay-updated-section),
            .e-con-inner:has(.cmr-stay-updated-section),
            .elementor-widget:has(.cmr-stay-updated-section),
            .elementor-widget-container:has(.cmr-stay-updated-section),
            .elementor-shortcode:has(.cmr-stay-updated-section),
            .elementor-column:has(.cmr-stay-updated-section),
            .elementor-section:has(.cmr-stay-updated-section) {
                padding-left: 0 !important;
                padding-right: 0 !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                --padding-left: 0px !important;
                --padding-right: 0px !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }

            .cmr-stay-updated-section {
                padding: 35px 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }

            .cmr-stay-updated-section .stay-updated-container {
                padding-left: 16px !important;
                padding-right: 16px !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }

            .cmr-stay-updated-section .stay-updated-title {
                font-size: 28px !important;
                font-weight: 600 !important;
                line-height: 1.25 !important;
                letter-spacing: -0.5px !important;
                color: #111111 !important;
                margin-top: 0 !important;
                margin-bottom: 24px !important;
                padding: 0 !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            .cmr-stay-updated-section .stay-updated-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 28px !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box !important;
            }

            .cmr-stay-updated-section .stay-updated-card {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box !important;
            }

            .cmr-stay-updated-section .stay-updated-img-link {
                width: 100% !important;
                margin-bottom: 14px !important;
                display: block !important;
            }

            .cmr-stay-updated-section .stay-updated-image {
                width: 100% !important;
                border-radius: 8px !important;
                overflow: hidden !important;
                text-align: left !important;
                margin-left: 0 !important;
                margin-right: auto !important;
                display: block !important;
            }

            .cmr-stay-updated-section .stay-updated-image img {
                width: 100% !important;
                height: auto !important;
                aspect-ratio: 16 / 9 !important;
                object-fit: cover !important;
                object-position: center left !important;
                border-radius: 8px !important;
                display: block !important;
                margin: 0 !important;
                margin-left: 0 !important;
                margin-right: auto !important;
            }

            .cmr-stay-updated-section .stay-updated-meta {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                margin-bottom: 10px !important;
                font-size: 13px !important;
                color: #64748b !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            .cmr-stay-updated-section .stay-updated-card-title {
                font-size: 18px !important;
                font-weight: 600 !important;
                line-height: 1.35 !important;
                color: #0f172a !important;
                margin: 0 0 10px 0 !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            .cmr-stay-updated-section .stay-updated-card-title a {
                color: #0f172a !important;
                text-decoration: none !important;
            }

            .cmr-stay-updated-section .stay-updated-excerpt {
                font-size: 14px !important;
                line-height: 1.55 !important;
                color: #475569 !important;
                margin-bottom: 14px !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            .cmr-stay-updated-section .stay-updated-more-link {
                font-size: 13px !important;
                font-weight: 600 !important;
                color: #111111 !important;
                display: inline-flex !important;
                align-items: center !important;
                gap: 6px !important;
                border-bottom: 1px solid #111111 !important;
                padding-bottom: 2px !important;
                text-decoration: none !important;
                margin-top: 0 !important;
            }
        }
        </style>

        <div class="cmr-stay-updated-section">
            <div class="stay-updated-container">
                <h2 class="stay-updated-title">Stay up to date with the latest from CMR -<br>research, perspectives and industry news.</h2>
                

                <?php if ( $news_query->have_posts() ) : ?>
                    <div class="stay-updated-grid">
                        <?php
                        while ( $news_query->have_posts() ) : $news_query->the_post();
                            
                            $post_title = get_the_title();
                            $post_link = get_permalink();
                            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                            if ( ! $thumbnail_url ) {
                                $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
                            }
                            
                            $category_name = 'Industry Intelligence';
                            $read_time = '5 min read'; // Simulated or custom field
                            $excerpt = wp_trim_words( get_the_excerpt(), 18, '...' );
                            ?>
                            
                            <div class="stay-updated-card" style="text-align: left; align-items: flex-start;">
                                <a href="<?php echo esc_url( $post_link ); ?>" class="stay-updated-img-link" style="display: block; width: 100%; text-align: left; margin: 0 0 14px 0; padding: 0;">
                                    <div class="stay-updated-image" style="width: 100%; text-align: left; margin-left: 0; margin-right: auto; border-radius: 8px; overflow: hidden;">
                                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" style="width: 100%; aspect-ratio: 16 / 9; object-fit: cover; object-position: center left; display: block; border-radius: 8px; margin: 0; margin-left: 0; margin-right: auto;" />
                                    </div>
                                </a>
                                
                                <div class="stay-updated-meta">
                                    <span class="stay-updated-tag">&mdash; <?php echo esc_html( $category_name ); ?></span>
                                    <span class="stay-updated-read-time"><?php echo esc_html( $read_time ); ?></span>
                                </div>
                                
                                <h3 class="stay-updated-card-title">
                                    <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                                </h3>
                                
                                <p class="stay-updated-excerpt">
                                    <?php echo esc_html( $excerpt ); ?>
                                </p>
                                
                                <a href="<?php echo esc_url( $post_link ); ?>" class="stay-updated-more-link">More Details <i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i></a>
                            </div>

                        <?php endwhile; ?>
                    </div>
                <?php else : ?>
                    <p>No news found.</p>
                <?php endif; wp_reset_postdata(); ?>
            </div>
        </div>
        <script>
        (function() {
            function resetStayUpdatedParentPadding() {
                if (window.innerWidth <= 768) {
                    var sections = document.querySelectorAll('.cmr-stay-updated-section');
                    sections.forEach(function(sec) {
                        var el = sec.parentElement;
                        while (el && el !== document.body) {
                            if (el.classList && (
                                el.classList.contains('elementor-element') ||
                                el.classList.contains('e-con') ||
                                el.classList.contains('e-con-inner') ||
                                el.classList.contains('elementor-widget') ||
                                el.classList.contains('elementor-widget-container') ||
                                el.classList.contains('elementor-column') ||
                                el.classList.contains('elementor-section') ||
                                el.classList.contains('elementor-container')
                            )) {
                                el.style.setProperty('padding-left', '0px', 'important');
                                el.style.setProperty('padding-right', '0px', 'important');
                                el.style.setProperty('margin-left', '0px', 'important');
                                el.style.setProperty('margin-right', '0px', 'important');
                                el.style.setProperty('--padding-left', '0px', 'important');
                                el.style.setProperty('--padding-right', '0px', 'important');
                                var inners = el.querySelectorAll('.e-con-inner');
                                inners.forEach(function(inn) {
                                    inn.style.setProperty('padding-left', '0px', 'important');
                                    inn.style.setProperty('padding-right', '0px', 'important');
                                    inn.style.setProperty('margin-left', '0px', 'important');
                                    inn.style.setProperty('margin-right', '0px', 'important');
                                    inn.style.setProperty('--padding-left', '0px', 'important');
                                    inn.style.setProperty('--padding-right', '0px', 'important');
                                });
                            }
                            el = el.parentElement;
                        }
                    });
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', resetStayUpdatedParentPadding);
            } else {
                resetStayUpdatedParentPadding();
            }
            window.addEventListener('resize', resetStayUpdatedParentPadding);
            setTimeout(resetStayUpdatedParentPadding, 100);
            setTimeout(resetStayUpdatedParentPadding, 500);
            setTimeout(resetStayUpdatedParentPadding, 1500);
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_stay_updated', 'cmr_stay_updated_shortcode' );
