<?php
/**
 * CMR Media Releases General Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'cmr_media_releases_general', 'cmr_media_releases_general_shortcode' );

function cmr_media_releases_general_shortcode( $atts ) {
    ob_start();

    // Query 4 latest cmr_news posts (or press releases)
    $args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 4,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
    );
    $query = new WP_Query( $args );

    $posts_data = [];
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 20 );
            $bg_image = get_the_post_thumbnail_url( $post_id, 'large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/800x500';
            }

            // Estimate read time (rough calculation based on word count)
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 ); // Average 200 words per minute
            if ($read_time < 1) $read_time = 1;
            
            $document_id = get_post_meta( $post_id, '_cmr_news_document_id', true );
            $pdf_link = '';
            if ( $document_id ) {
                $pdf_link = wp_get_attachment_url( $document_id );
            }

            $posts_data[] = [
                'title'     => $title,
                'link'      => $link,
                'excerpt'   => $excerpt,
                'image'     => $bg_image,
                'read_time' => $read_time . ' min read',
                'pdf_link'  => $pdf_link
            ];
        }
    }
    wp_reset_postdata();

    // Fallback data if no posts found
    if ( empty( $posts_data ) ) {
        $posts_data = [
            [
                'title' => 'CMR India expands AI-driven intelligence capabilities',
                'excerpt' => 'Strengthening enterprise research solutions across key sectors to provide real-time strategic guidance for global stakeholders.',
                'image' => 'https://via.placeholder.com/800x500',
                'read_time' => '4 min read',
                'link' => '#',
                'pdf_link' => '#'
            ],
            [
                'title' => 'India\'s EV Market Accelerates 57% in Q1 2026...',
                'excerpt' => 'Electric vehicles continue to dominate the new car sales market...',
                'image' => 'https://via.placeholder.com/800x500',
                'read_time' => '3 min read',
                'link' => '#',
                'pdf_link' => ''
            ],
            [
                'title' => 'ADAS Market Grows 49%; CMR Survey 2026...',
                'excerpt' => 'Advanced Driver Assistance Systems are becoming standard...',
                'image' => 'https://via.placeholder.com/800x500',
                'read_time' => '5 min read',
                'link' => '#',
                'pdf_link' => '#'
            ],
            [
                'title' => 'Tech Infrastructure Spending Surges Post-Pandemic',
                'excerpt' => 'A closer look at how enterprises are scaling their tech stacks.',
                'image' => 'https://via.placeholder.com/800x500',
                'read_time' => '2 min read',
                'link' => '#',
                'pdf_link' => ''
            ]
        ];
    }

    $top_post = $posts_data[0];
    $bottom_posts = array_slice($posts_data, 0, 3); // Take first 3 for the bottom tabs
    ?>
    <style>
        .cmr-mrg-section {
            font-family: 'Instrument Sans', sans-serif;
            max-width: 1280px;
            margin: 0 auto;
            padding: 60px 20px;
            color: #111;
        }

        /* Top Featured Post */
        .cmr-mrg-featured {
            display: flex;
            background: #fff;
            border: 1px solid #eaeaea;
            margin-bottom: 24px;
            min-height: 400px;
            transition: all 0.4s ease;
        }
        
        .cmr-mrg-featured-img-wrap {
            flex: 0 0 50%;
            overflow: hidden;
            position: relative;
        }

        .cmr-mrg-featured-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            transition: opacity 0.4s ease;
        }

        .cmr-mrg-featured-content {
            flex: 0 0 50%;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .cmr-mrg-meta-top {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        .cmr-mrg-label {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .cmr-mrg-label::before {
            content: "";
            width: 24px;
            height: 1px;
            background: #666;
            display: inline-block;
        }

        .cmr-mrg-title {
            font-size: 40px;
            font-weight: 700;
            line-height: 1.1;
            margin: 0 0 20px 0;
            letter-spacing: 1px;
            color: #111;
        }

        .cmr-mrg-excerpt {
            font-size: 16px;
            color: #444;
            line-height: 1.6;
            margin: 0 0 40px 0;
        }

        .cmr-mrg-actions {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .cmr-mrg-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: white !important;
            border: 1px solid white !important;
            color: #000000 !important;
            text-decoration: none;
            padding: 16px 28px !important;
            border-radius: 50px !important;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .cmr-mrg-btn-primary svg path,
        .cmr-mrg-btn-primary svg polyline,
        .cmr-mrg-btn-primary svg line {
            stroke: #000000 !important;
        }
        
        .cmr-mrg-btn-primary:hover {
            background-color: #f5f5f5 !important;
            border-color: #f5f5f5 !important;
        }

        .cmr-mrg-btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: white !important;
            border: 1px solid white !important;
            color: #000000 !important;
            text-decoration: none;
            padding: 16px 28px !important;
            border-radius: 50px !important;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .cmr-mrg-btn-outline svg path,
        .cmr-mrg-btn-outline svg polyline,
        .cmr-mrg-btn-outline svg line {
            stroke: #000000 !important;
        }

        .cmr-mrg-btn-outline:hover {
            background-color: #f5f5f5 !important;
            border-color: #f5f5f5 !important;
        }

        /* Bottom Row Items */
        .cmr-mrg-nav {
            display: flex;
            gap: 24px;
        }

        .cmr-mrg-nav-item {
            flex: 1;
            display: flex;
            background: #f9f9fb;
            cursor: pointer;
            border-top: 3px solid transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }
        
        .cmr-mrg-nav-item:hover {
            background: #f0f0f5;
        }

        .cmr-mrg-nav-item.active {
            border-top-color: #00E5FF;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .cmr-mrg-nav-img-wrap {
            width: 120px;
            flex-shrink: 0;
        }

        .cmr-mrg-nav-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .cmr-mrg-nav-content {
            padding: 20px;
            flex: 1;
        }

        .cmr-mrg-nav-label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: #666;
            margin-bottom: 8px;
        }

        .cmr-mrg-nav-label::before {
            content: "";
            width: 16px;
            height: 1px;
            background: #666;
            display: inline-block;
        }

        .cmr-mrg-nav-title {
            font-size: 15px;
            font-weight: 600;
            line-height: 1.4;
            margin: 0;
            letter-spacing: 1px;
            color: #111;
        }

        @media (max-width: 992px) {
            .cmr-mrg-featured {
                flex-direction: column;
            }
            .cmr-mrg-featured-img-wrap {
                height: 300px;
            }
            .cmr-mrg-featured-content {
                padding: 40px;
            }
            .cmr-mrg-nav {
                flex-direction: column;
            }
        }
        
        @media (max-width: 576px) {
            .cmr-mrg-title {
                font-size: 28px;
            }
            .cmr-mrg-actions {
                flex-direction: column;
                align-items: flex-start;
            }
            .cmr-mrg-nav-item {
                flex-direction: column;
            }
            .cmr-mrg-nav-img-wrap {
                width: 100%;
                height: 180px;
            }
        }
    </style>

    <div class="cmr-mrg-section" id="cmr-mrg-app">
        <!-- Top Featured Area -->
        <div class="cmr-mrg-featured">
            <div class="cmr-mrg-featured-img-wrap">
                <img src="<?php echo esc_url($top_post['image']); ?>" alt="Featured Image" class="cmr-mrg-featured-img" id="cmr-mrg-main-img">
            </div>
            <div class="cmr-mrg-featured-content">
                <div class="cmr-mrg-meta-top">
                    <span class="cmr-mrg-label">Press Release</span>
                    <span id="cmr-mrg-main-time"><?php echo esc_html($top_post['read_time']); ?></span>
                </div>
                <h2 class="cmr-mrg-title" id="cmr-mrg-main-title"><?php echo esc_html($top_post['title']); ?></h2>
                <p class="cmr-mrg-excerpt" id="cmr-mrg-main-excerpt"><?php echo esc_html($top_post['excerpt']); ?></p>
                
                <div class="cmr-mrg-actions">
                    <a href="<?php echo esc_url($top_post['link']); ?>" class="cmr-mrg-btn-primary" id="cmr-mrg-main-link">
                        Read full Release
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                    </a>
                    <a href="<?php echo $top_post['pdf_link'] ? esc_url($top_post['pdf_link']) : '#'; ?>" 
                       class="cmr-mrg-btn-outline" 
                       id="cmr-mrg-main-pdf" 
                       target="_blank">
                        Download PDF
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Navigation Row -->
        <div class="cmr-mrg-nav">
            <?php foreach ( $bottom_posts as $index => $post ) : ?>
            <div class="cmr-mrg-nav-item <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                <div class="cmr-mrg-nav-img-wrap">
                    <img src="<?php echo esc_url($post['image']); ?>" alt="Thumbnail" class="cmr-mrg-nav-img">
                </div>
                <div class="cmr-mrg-nav-content">
                    <div class="cmr-mrg-nav-label">Media Releases</div>
                    <h4 class="cmr-mrg-nav-title"><?php echo esc_html($post['title']); ?></h4>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const postsData = <?php echo json_encode($bottom_posts); ?>;
        const navItems = document.querySelectorAll('.cmr-mrg-nav-item');
        
        const mainImg = document.getElementById('cmr-mrg-main-img');
        const mainTime = document.getElementById('cmr-mrg-main-time');
        const mainTitle = document.getElementById('cmr-mrg-main-title');
        const mainExcerpt = document.getElementById('cmr-mrg-main-excerpt');
        const mainLink = document.getElementById('cmr-mrg-main-link');
        const mainPdf = document.getElementById('cmr-mrg-main-pdf');

        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault(); // Optional, if you want them to be pure tabs
                
                // Remove active class from all
                navItems.forEach(nav => nav.classList.remove('active'));
                
                // Add active to clicked
                this.classList.add('active');
                
                // Get data index
                const idx = this.getAttribute('data-index');
                const data = postsData[idx];
                
                if (data) {
                    // Slight fade effect
                    mainImg.style.opacity = 0;
                    setTimeout(() => {
                        mainImg.src = data.image;
                        mainTime.textContent = data.read_time;
                        mainTitle.textContent = data.title;
                        mainExcerpt.textContent = data.excerpt;
                        mainLink.href = data.link;
                        
                        if (data.pdf_link) {
                            mainPdf.href = data.pdf_link;
                        } else {
                            mainPdf.href = '#';
                        }
                        mainPdf.style.display = 'inline-flex';
                        
                        mainImg.style.opacity = 1;
                    }, 200);
                }
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
