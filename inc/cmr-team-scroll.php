<?php
/**
 * CMR Team Scroll Shortcode
 * Displays specific team members in a horizontal scroll.
 * Shortcode: [cmr_team_scroll]
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

add_shortcode('cmr_team_scroll', 'cmr_team_scroll_shortcode');

function cmr_team_scroll_shortcode($atts) {
    $args = array(
        'post_type'      => 'quanto_team',
        'posts_per_page' => -1,
        'post_status'    => 'publish'
    );

    $team_query = new WP_Query($args);

    if (empty($team_query->posts)) {
        return '';
    }

    $allowed_names = array('Thomas George', 'Anil Chopra', 'Prabhu Ram');
    $team_members = array();
    
    foreach($team_query->posts as $p) {
        if (in_array($p->post_title, $allowed_names)) {
            $team_members[] = $p;
        }
    }

    // Sort to match the requested order
    usort($team_members, function($a, $b) use ($allowed_names) {
        return array_search($a->post_title, $allowed_names) - array_search($b->post_title, $allowed_names);
    });

    if (empty($team_members)) {
        return '';
    }

    ob_start();
    ?>
    <style>
        .cmr-team-scroll-section {
            padding: 60px 20px;
            font-family: 'Outfit', sans-serif;
            background: #fff;
            max-width: 1280px;
            margin: 0 auto;
        }
        .cmr-team-scroll-title {
            text-align: center;
            font-size: 40px;
            font-weight: 700;
            color: #000;
            margin-bottom: 50px;
            letter-spacing: -1px;
            line-height: 1.2;
        }
        
        .cmr-team-scroll-wrapper {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 20px;
        }
        .cmr-team-scroll-wrapper::-webkit-scrollbar {
            display: none;
        }
        
        .cmr-team-card {
            flex: 0 0 32%;
            min-width: 280px;
            scroll-snap-align: start;
            display: flex;
            flex-direction: column;
        }

        .cmr-team-card-image {
            width: 100%;
            aspect-ratio: 3/4;
            background: #f5f5f5;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }
        
        .cmr-team-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .cmr-team-card-name {
            font-size: 24px;
            font-weight: 600;
            color: #111;
            margin: 0 0 8px 0;
            letter-spacing: -0.5px;
        }

        .cmr-team-card-role {
            font-size: 16px;
            color: #444;
            margin: 0 0 20px 0;
            line-height: 1.4;
        }

        .cmr-team-card-social {
            display: flex;
            gap: 12px;
        }

        .cmr-social-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background-color: #000;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }
        .cmr-social-icon-btn:hover {
            background-color: #333;
        }
        .cmr-social-icon-btn svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }
        
        .cmr-team-view-all {
            text-align: center;
            margin-top: 40px;
        }
        .cmr-team-view-all a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            font-weight: 600;
            color: #111;
            text-decoration: none;
            border-bottom: 1px solid #111;
            padding-bottom: 4px;
            transition: opacity 0.2s ease;
        }
        .cmr-team-view-all a:hover {
            opacity: 0.7;
        }
        
        @media (max-width: 1024px) {
            .cmr-team-card {
                flex: 0 0 45%;
            }
        }
        @media (max-width: 768px) {
            .cmr-team-scroll-title {
                font-size: 32px;
                margin-bottom: 30px;
            }
            .cmr-team-card {
                flex: 0 0 85%;
            }
        }
    </style>

    <div class="cmr-team-scroll-section">
        <h2 class="cmr-team-scroll-title">Meet the people behind<br>our success</h2>
        
        <div class="cmr-team-scroll-wrapper">
            <?php foreach ($team_members as $team_post) : 
                $post_id = $team_post->ID;
                
                $role = get_post_meta($post_id, 'designation', true);
                if (!$role) $role = get_post_meta($post_id, '_team_role', true);
                if (!$role) $role = get_post_meta($post_id, 'role', true);
                if (!$role) $role = get_post_meta($post_id, 'job_title', true);
                if (!$role) $role = get_post_meta($post_id, 'position', true);
                if (!$role) $role = get_post_meta($post_id, 'member_designation', true);
                if (!$role) $role = get_post_meta($post_id, 'team_designation', true);

                $linkedin = get_post_meta($post_id, 'linkedin', true);
                if (!$linkedin) $linkedin = get_post_meta($post_id, '_linkedin_url', true);
                if (!$linkedin) $linkedin = get_post_meta($post_id, 'linkedin_url', true);
                if (!$linkedin) $linkedin = get_post_meta($post_id, 'linkedin_link', true);

                $twitter = get_post_meta($post_id, 'twitter', true);
                if (!$twitter) $twitter = get_post_meta($post_id, '_twitter_url', true);
                if (!$twitter) $twitter = get_post_meta($post_id, 'twitter_url', true);
                if (!$twitter) $twitter = get_post_meta($post_id, 'twitter_link', true);
                if (!$twitter) $twitter = get_post_meta($post_id, 'x_url', true);

                $image_url = get_the_post_thumbnail_url($post_id, 'large');
                if (!$image_url) {
                    $image_url = 'https://via.placeholder.com/600x800.png?text=No+Image'; 
                }
            ?>
                <!-- DEBUG META FOR <?php echo esc_html($team_post->post_title); ?>: 
                <?php 
                $all_meta = get_post_meta($post_id);
                foreach($all_meta as $mk => $mv) {
                    echo esc_html($mk) . ' => ' . esc_html(print_r($mv, true)) . "\n";
                }
                ?>
                -->
                <div class="cmr-team-card">
                    <div class="cmr-team-card-image">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($team_post->post_title); ?>">
                    </div>
                    <h3 class="cmr-team-card-name"><?php echo esc_html($team_post->post_title); ?></h3>
                    <p class="cmr-team-card-role"><?php echo esc_html($role); ?></p>
                    
                    <div class="cmr-team-card-social">
                        <?php if ($linkedin) : ?>
                            <a href="<?php echo esc_url($linkedin); ?>" target="_blank" class="cmr-social-icon-btn" aria-label="LinkedIn">
                                <svg viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                        <?php if ($twitter) : ?>
                            <a href="<?php echo esc_url($twitter); ?>" target="_blank" class="cmr-social-icon-btn" aria-label="X (Twitter)">
                                <svg viewBox="0 0 24 24">
                                    <path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cmr-team-view-all">
            <a href="/who-we-are">View Team 
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 11L11 1M11 1H3M11 1V9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
