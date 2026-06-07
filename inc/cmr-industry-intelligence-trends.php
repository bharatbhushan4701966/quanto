<?php
/**
 * Shortcode for Industry Intelligence Trends Section
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_industry_intelligence_trends_shortcode' ) ) {
    function cmr_industry_intelligence_trends_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'posts_per_page' => 6,
        ), $atts );

        $query_args = array(
            'post_type'      => 'cmr_news',
            'posts_per_page' => $atts['posts_per_page'],
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        $trends_query = new WP_Query( $query_args );

        ob_start();
        ?>
        <style>
            .cmr-intel-trends-wrapper {
                font-family: 'Instrument Sans', sans-serif !important;
                max-width: 1280px;
                margin: 50px auto;
                padding: 0 20px;
                overflow: hidden; /* Hide outer overflow */
            }
            .cmr-intel-trends-header {
                margin-bottom: 40px;
            }
            .cmr-intel-trends-header h2 {
                font-size: 36px;
                font-weight: 700;
                color: #111;
                margin: 0 0 10px 0;
                letter-spacing: -1px;
            }
            .cmr-intel-trends-header p {
                font-size: 16px;
                color: #555;
                margin: 0;
            }
            
            .cmr-intel-trends-track-container {
                overflow: hidden; /* Important for GSAP */
                width: 100%;
            }

            .cmr-intel-trends-track {
                display: flex;
                gap: 20px;
                width: max-content; /* Let it be as wide as needed for GSAP */
                padding-bottom: 20px;
            }

            .cmr-intel-trends-card {
                /* We set fixed width for cards so they overflow the screen */
                width: calc((1280px - 80px) / 3); /* approx 1/3 of max container width */
                max-width: 400px; 
                flex-shrink: 0;
                display: flex;
                flex-direction: column;
            }
            
            @media (max-width: 992px) {
                .cmr-intel-trends-card {
                    width: calc(50vw - 30px);
                }
            }
            @media (max-width: 768px) {
                .cmr-intel-trends-card {
                    width: calc(85vw - 20px);
                }
            }

            .cmr-intel-trends-img {
                width: 100%;
                aspect-ratio: 16 / 9;
                overflow: hidden;
                margin-bottom: 20px;
            }

            .cmr-intel-trends-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                transition: transform 0.3s ease;
            }

            .cmr-intel-trends-img:hover img {
                transform: scale(1.05);
            }

            .cmr-intel-trends-meta {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 13px;
                color: #888;
                margin-bottom: 15px;
            }

            .cmr-intel-trends-cat {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .cmr-intel-trends-cat::before {
                content: '';
                display: block;
                width: 20px;
                height: 1px;
                background: #ccc;
            }

            .cmr-intel-trends-title {
                font-size: 18px;
                font-weight: 700;
                line-height: 1.4;
                margin: 0 0 25px 0;
                letter-spacing: -0.3px;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .cmr-intel-trends-title a {
                color: #111;
                text-decoration: none;
                transition: color 0.3s ease;
            }

            .cmr-intel-trends-title a:hover {
                color: #555;
            }

            .cmr-intel-trends-more {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 14px;
                font-weight: 700;
                color: #111;
                text-decoration: none;
                border-bottom: 1.5px solid #111;
                padding-bottom: 2px;
                align-self: flex-start;
                margin-top: auto;
                transition: color 0.3s ease, border-color 0.3s ease;
            }

            .cmr-intel-trends-more:hover {
                color: #555;
                border-color: #555;
            }

            .cmr-intel-trends-more svg {
                width: 12px;
                height: 12px;
                transition: transform 0.3s ease;
            }

            .cmr-intel-trends-more:hover svg {
                transform: translate(2px, -2px);
            }
        </style>

        <div class="cmr-intel-trends-wrapper" id="cmr-intel-trends-section">
            <div class="cmr-intel-trends-header">
                <h2>Industry Intelligence Trends</h2>
                <p>Track emerging shifts, growth signals, and market movements in real time.</p>
            </div>
            
            <?php if ( $trends_query->have_posts() ) : ?>
                <div class="cmr-intel-trends-track-container">
                    <div class="cmr-intel-trends-track" id="cmr-intel-trends-track">
                        <?php
                        while ( $trends_query->have_posts() ) : $trends_query->the_post();
                            $post_title = get_the_title();
                            $post_link = get_permalink();
                            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                            if ( ! $thumbnail_url ) {
                                $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
                            }
                            
                            // Category
                            $category_name = 'Industry Intelligence';
                            $terms = get_the_terms( get_the_ID(), 'category' );
                            if ( $terms && ! is_wp_error( $terms ) ) {
                                $category_name = $terms[0]->name;
                            }

                            // Reading time
                            $content = get_post_field( 'post_content', get_the_ID() );
                            $word_count = str_word_count( strip_tags( $content ) );
                            $read_time = ceil( $word_count / 200 );
                            if ($read_time < 1) $read_time = 1;
                            ?>
                            
                            <div class="cmr-intel-trends-card">
                                <div class="cmr-intel-trends-img">
                                    <a href="<?php echo esc_url( $post_link ); ?>">
                                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" loading="lazy" />
                                    </a>
                                </div>
                                <div class="cmr-intel-trends-meta">
                                    <span class="cmr-intel-trends-cat"><?php echo esc_html( $category_name ); ?></span>
                                    <span><?php echo esc_html( $read_time ); ?> min read</span>
                                </div>
                                <h3 class="cmr-intel-trends-title">
                                    <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                                </h3>
                                <a href="<?php echo esc_url( $post_link ); ?>" class="cmr-intel-trends-more">
                                    More Details 
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="7" y1="17" x2="17" y2="7"></line>
                                        <polyline points="7 7 17 7 17 17"></polyline>
                                    </svg>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php else : ?>
                <p>No trends found.</p>
            <?php endif; wp_reset_postdata(); ?>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                gsap.registerPlugin(ScrollTrigger);
                
                let track = document.getElementById("cmr-intel-trends-track");
                let section = document.getElementById("cmr-intel-trends-section");
                
                if (track && section) {
                    function getScrollAmount() {
                        let trackWidth = track.scrollWidth;
                        // Move left enough to show the end of the track.
                        return -(trackWidth - window.innerWidth + 40); 
                    }
                    
                    const tween = gsap.to(track, {
                        x: getScrollAmount,
                        ease: "none"
                    });
    
                    ScrollTrigger.create({
                        trigger: section,
                        start: "center center", // Pin when section reaches center
                        end: () => `+=${getScrollAmount() * -1}`, // Scroll length based on track width
                        pin: true,
                        animation: tween,
                        scrub: 1,
                        invalidateOnRefresh: true
                    });
                }
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_industry_intelligence_trends', 'cmr_industry_intelligence_trends_shortcode' );
