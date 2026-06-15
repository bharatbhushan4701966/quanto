<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! function_exists( 'cmr_live_shortcode' ) ) {
    function cmr_live_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'posts_per_page' => 3,
            'category'       => '',
        ), $atts, 'cmr_live' );

        $query_args = array(
            'post_type'      => 'post',
            'posts_per_page' => intval( $atts['posts_per_page'] ),
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        if ( ! empty( $atts['category'] ) ) {
            $query_args['category_name'] = $atts['category'];
        }

        // We use get_posts but we'll use WP_Query to be safe
        $live_query = new WP_Query( $query_args );
        $posts = $live_query->posts;

        ob_start();
        ?>
        <style>
            .cmr-live-section {
                padding: 40px 0;
                max-width: 1200px;
                margin: 0 auto;
                font-family: inherit;
            }
            .cmr-live-header {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: flex-end;
                margin-bottom: 30px;
                border-bottom: 1px solid #ddd;
                padding-bottom: 20px;
            }
            .cmr-live-title-area {
                flex: 1;
                min-width: 300px;
                margin-right: 20px;
            }
            .cmr-live-title-area h2 {
                font-size: 42px;
                font-weight: 700;
                color: #111;
                margin: 0 0 10px 0;
                line-height: 1.2;
            }
            .cmr-live-title-area p {
                font-size: 16px;
                color: #444;
                margin: 0;
                line-height: 1.5;
            }
            .cmr-live-explore {
                font-size: 14px;
                font-weight: 600;
                color: #111;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                border-bottom: 1px solid #111;
                padding-bottom: 2px;
                margin-bottom: 5px;
            }
            .cmr-live-explore:hover {
                opacity: 0.8;
            }
            .cmr-live-explore svg {
                width: 14px;
                height: 14px;
                margin-left: 5px;
            }
            .cmr-live-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
            }
            @media (max-width: 991px) {
                .cmr-live-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
            @media (max-width: 767px) {
                .cmr-live-grid {
                    grid-template-columns: 1fr;
                }
                .cmr-live-header {
                    flex-direction: column;
                    align-items: flex-start;
                }
                .cmr-live-title-area {
                    margin-bottom: 20px;
                }
            }
            .cmr-live-card {
                display: flex;
                flex-direction: column;
                text-decoration: none;
                color: inherit;
            }
            .cmr-live-card:hover .cmr-live-play-btn {
                transform: scale(1.1);
            }
            .cmr-live-img-wrap {
                position: relative;
                width: 100%;
                aspect-ratio: 16/9;
                border-radius: 4px;
                overflow: hidden;
                margin-bottom: 15px;
                background: #f0f0f0;
            }
            .cmr-live-img-wrap img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            .cmr-live-duration {
                position: absolute;
                top: 10px;
                left: 10px;
                background: rgba(255, 255, 255, 0.9);
                color: #111;
                font-size: 11px;
                font-weight: 700;
                padding: 4px 8px;
                border-radius: 4px;
                z-index: 2;
                letter-spacing: 0.5px;
            }
            .cmr-live-play-btn {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 50px;
                height: 50px;
                background: rgba(0, 0, 0, 0.6);
                border: 2px solid #fff;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 2;
                transition: transform 0.2s;
            }
            .cmr-live-play-btn svg {
                width: 20px;
                height: 20px;
                fill: #fff;
                margin-left: 3px;
            }
            .cmr-live-meta {
                font-size: 12px;
                font-weight: 600;
                color: #666;
                text-transform: uppercase;
                margin-bottom: 8px;
                letter-spacing: 0.5px;
            }
            .cmr-live-meta-type {
                color: #007bff; /* Blue for the type (TOP VIEW / PODCAST) */
            }
            .cmr-live-title {
                font-size: 20px;
                font-weight: 700;
                color: #111;
                margin: 0;
                line-height: 1.3;
                letter-spacing: -0.5px;
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>

        <div class="cmr-live-section">
            <div class="cmr-live-header">
                <div class="cmr-live-title-area">
                    <h2>CMR Live</h2>
                    <p>Stream deep-dive research and real-world executive perspectives tailored for leaders navigating global market trends.</p>
                </div>
                <a href="/cmr-live" class="cmr-live-explore">
                    Explore More
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7 7 17 7 17 17"></polyline>
                    </svg>
                </a>
            </div>

            <div class="cmr-live-grid">
                <?php if ( ! empty($posts) ) : ?>
                    <?php 
                    $count = 0;
                    foreach ( $posts as $post_obj ) : 
                        $thumbnail_url = get_the_post_thumbnail_url( $post_obj->ID, 'large' );
                        if ( ! $thumbnail_url ) {
                            $thumbnail_url = 'https://via.placeholder.com/800x450?text=CMR+Live';
                        }
                        
                        $category_name = 'AUTOMOTIVE'; // Default fallback
                        $terms = get_the_terms( $post_obj->ID, 'category' );
                        if ( $terms && ! is_wp_error( $terms ) ) {
                            $category_name = $terms[0]->name;
                        }
                        
                        $post_date = get_the_date('d M Y', $post_obj);
                        
                        // Simulate video type/duration
                        $types = array('TOP VIEW', 'PODCAST', 'WEBINAR');
                        $type = $types[ $count % count($types) ];
                        $mins = rand(3, 15);
                        $secs = sprintf("%02d", rand(0, 59));
                        $duration = $mins . ':' . $secs . ' MINS';
                        
                        $count++;
                    ?>
                    <a href="<?php echo esc_url(get_permalink($post_obj->ID)); ?>" class="cmr-live-card">
                        <div class="cmr-live-img-wrap">
                            <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr(get_the_title($post_obj)); ?>">
                            <div class="cmr-live-duration"><?php echo esc_html($duration); ?></div>
                            <div class="cmr-live-play-btn">
                                <svg viewBox="0 0 24 24">
                                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                </svg>
                            </div>
                        </div>
                        <div class="cmr-live-meta">
                            <span class="cmr-live-meta-type"><?php echo esc_html($type); ?></span> &bull; 
                            <?php echo esc_html(strtoupper($category_name)); ?> &bull; 
                            <?php echo esc_html(strtoupper($post_date)); ?>
                        </div>
                        <h3 class="cmr-live-title"><?php echo esc_html(get_the_title($post_obj)); ?></h3>
                    </a>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>No streams found.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_live', 'cmr_live_shortcode' );
