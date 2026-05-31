<?php
/**
 * CMR Press Releases Horizontal Scroll Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'cmr_press_releases', 'cmr_render_press_releases_shortcode' );

function cmr_render_press_releases_shortcode( $atts ) {
    // Add inline CSS for the shortcode so we don't have to enqueue a new file
    ob_start();
    ?>
    <style>
        .cmr-pr-section {
            background-color: #000;
            color: #fff;
            padding: 80px 0;
            font-family: 'Inter', -apple-system, sans-serif;
            overflow: hidden;
            position: relative;
            /* To ensure gsap pinning looks correct */
            width: 100%;
        }

        .cmr-pr-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .cmr-pr-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 60px;
        }

        .cmr-pr-title-area h2 {
            font-size: 56px;
            font-weight: 700;
            color: #fff;
            margin: 0 0 10px 0;
            letter-spacing: -1.5px;
        }

        .cmr-pr-title-area p {
            font-size: 16px;
            color: #aaa;
            margin: 0;
        }

        .cmr-pr-explore-btn {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            border-bottom: 1px solid #fff;
            padding-bottom: 2px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: opacity 0.3s;
            margin-bottom: 5px;
        }

        .cmr-pr-explore-btn:hover {
            opacity: 0.8;
            color: #fff;
        }

        .cmr-pr-cards-track {
            display: flex;
            gap: 40px;
            padding-left: calc((100vw - 1260px) / 2); /* Start aligning with container */
            padding-right: 40px;
            width: max-content;
        }

        /* Fallback for small screens */
        @media (max-width: 1300px) {
            .cmr-pr-cards-track {
                padding-left: 20px;
            }
        }

        .cmr-pr-card {
            display: flex;
            background: #1a1a1a;
            border-radius: 12px;
            overflow: hidden;
            width: 800px; /* Fixed width for the scroll effect */
            height: 400px;
            flex-shrink: 0;
        }

        @media (max-width: 900px) {
            .cmr-pr-card {
                width: 85vw;
                flex-direction: column;
                height: auto;
            }
        }

        .cmr-pr-card-img {
            width: 50%;
            background-size: cover;
            background-position: center;
        }

        @media (max-width: 900px) {
            .cmr-pr-card-img {
                width: 100%;
                height: 250px;
            }
        }

        .cmr-pr-card-content {
            width: 50%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (max-width: 900px) {
            .cmr-pr-card-content {
                width: 100%;
                padding: 30px;
            }
        }

        .cmr-pr-meta {
            font-size: 13px;
            color: #bbb;
            margin-bottom: 15px;
        }

        .cmr-pr-card-title {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            margin: 0 0 15px 0;
            line-height: 1.3;
        }

        .cmr-pr-card-excerpt {
            font-size: 14px;
            color: #999;
            margin-bottom: 30px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cmr-pr-read-btn {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border-bottom: 1px solid #fff;
            padding-bottom: 2px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            align-self: flex-start;
            transition: opacity 0.3s;
        }

        .cmr-pr-read-btn:hover {
            opacity: 0.8;
            color: #fff;
        }
    </style>

    <?php
    $args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 5,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
        'tax_query'      => array(
            array(
                'taxonomy' => 'cmr_news_category',
                'field'    => 'slug',
                'terms'    => 'media-releases',
            ),
        ),
    );
    $query = new WP_Query( $args );
    
    // Fallback if 'media-releases' term is incorrect, just fetch all cmr_news for demonstration
    if ( !$query->have_posts() ) {
        $args['tax_query'] = array();
        $query = new WP_Query( $args );
    }
    ?>

    <div class="cmr-pr-section" id="cmr-pr-section">
        <div class="cmr-pr-container">
            <div class="cmr-pr-header">
                <div class="cmr-pr-title-area">
                    <h2>Press Release</h2>
                    <p>Track emerging shifts, growth signals, and market movements in real time.</p>
                </div>
                <a href="<?php echo get_post_type_archive_link('cmr_news'); ?>" class="cmr-pr-explore-btn">
                    Explore More 
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 12L12 4M12 4H5.5M12 4V10.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="cmr-pr-cards-track" id="cmr-pr-track">
            <?php if ( $query->have_posts() ) : ?>
                <?php while ( $query->have_posts() ) : $query->the_post(); 
                    $post_id = get_the_ID();
                    $bg_image = get_the_post_thumbnail_url( $post_id, 'large' );
                    if ( ! $bg_image ) {
                        $bg_image = 'https://via.placeholder.com/600x400';
                    }
                ?>
                <div class="cmr-pr-card panel">
                    <div class="cmr-pr-card-img" style="background-image: url('<?php echo esc_url( $bg_image ); ?>');"></div>
                    <div class="cmr-pr-card-content">
                        <div class="cmr-pr-meta">
                            Press Release | <?php echo get_the_date('d M Y'); ?>
                        </div>
                        <h3 class="cmr-pr-card-title"><?php the_title(); ?></h3>
                        <div class="cmr-pr-card-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></div>
                        <a href="<?php the_permalink(); ?>" class="cmr-pr-read-btn">
                            Read Coverage 
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 12L12 4M12 4H5.5M12 4V10.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="cmr-pr-card">
                    <div class="cmr-pr-card-content">
                        <p>No press releases found.</p>
                    </div>
                </div>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>

    <!-- Init GSAP ScrollTrigger for horizontal scroll -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);
            
            let track = document.getElementById("cmr-pr-track");
            let section = document.getElementById("cmr-pr-section");
            
            if (track && section) {
                // Calculate how far to move left
                function getScrollAmount() {
                    let trackWidth = track.scrollWidth;
                    return -(trackWidth - window.innerWidth + 40); // 40px for padding
                }
                
                const tween = gsap.to(track, {
                    x: getScrollAmount,
                    ease: "none"
                });

                ScrollTrigger.create({
                    trigger: section,
                    start: "center center",
                    end: () => `+=${getScrollAmount() * -1}`,
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
