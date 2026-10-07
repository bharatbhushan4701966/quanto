<?php
/**
 * Shortcode & Helper for Featured Video / Insight
 * Matches the Figma Card Design: Video on top, Date + Duration, Bold Title, Watch Link
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_get_video_embed_html' ) ) {
    function cmr_get_video_embed_html( $video_url, $poster_url = '' ) {
        if ( empty( $video_url ) ) {
            return '';
        }

        $video_url = trim( $video_url );

        // Raw iframe / embed code
        if ( strpos( $video_url, '<iframe' ) !== false || strpos( $video_url, '<video' ) !== false ) {
            return $video_url;
        }

        // YouTube
        if ( preg_match( '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $video_url, $matches ) ) {
            $yt_id = $matches[1];
            return '<iframe src="https://www.youtube.com/embed/' . esc_attr( $yt_id ) . '?rel=0&modestbranding=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe>';
        }

        // Vimeo
        if ( preg_match( '/(?:vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+))/i', $video_url, $matches ) ) {
            $vimeo_id = end( $matches );
            return '<iframe src="https://player.vimeo.com/video/' . esc_attr( $vimeo_id ) . '?title=0&byline=0&portrait=0" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
        }

        // Direct video file (mp4, webm, ogg)
        if ( preg_match( '/\.(mp4|webm|ogg)(\?.*)?$/i', $video_url ) ) {
            $poster_attr = ! empty( $poster_url ) ? ' poster="' . esc_url( $poster_url ) . '"' : '';
            return '<video controls preload="metadata"' . $poster_attr . ' playsinline><source src="' . esc_url( $video_url ) . '" type="video/mp4">Your browser does not support the video tag.</video>';
        }

        // WordPress oEmbed fallback
        $oembed = wp_oembed_get( $video_url );
        if ( $oembed ) {
            return $oembed;
        }

        return '<iframe src="' . esc_url( $video_url ) . '" frameborder="0" allowfullscreen loading="lazy"></iframe>';
    }
}

if ( ! function_exists( 'cmr_featured_insight_shortcode' ) ) {
    function cmr_featured_insight_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'video_url'    => '',
            'poster'       => '',
            'title'        => '',
            'date'         => '',
            'duration'     => '1 min',
            'btn_text'     => 'Watch',
            'link'         => '#',
            'target'       => '_self',
            'max_width'    => '600px',
            'post_type'    => 'cmr_news',
        ), $atts );

        $video_url = $atts['video_url'];
        $poster    = $atts['poster'];
        $title     = $atts['title'];
        $date      = $atts['date'];
        $duration  = ! empty( $atts['duration'] ) ? $atts['duration'] : '1 min';
        $btn_text  = ! empty( $atts['btn_text'] ) ? $atts['btn_text'] : 'Watch';
        $link      = ! empty( $atts['link'] ) ? $atts['link'] : '#';
        $target    = $atts['target'];
        $max_width = $atts['max_width'];

        // If manual video or title not supplied, query latest post as fallback
        if ( empty( $video_url ) && empty( $title ) ) {
            $query_args = array(
                'post_type'      => array( 'post', 'cmr_news' ),
                'posts_per_page' => 1,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            );
            $posts = get_posts( $query_args );
            if ( ! empty( $posts ) ) {
                $p = $posts[0];
                $title = get_the_title( $p );
                $date  = get_the_date( 'd F Y', $p );
                $link  = get_permalink( $p->ID );

                // Try to get video from custom field or content
                $custom_video = get_post_meta( $p->ID, 'video_url', true );
                if ( ! empty( $custom_video ) ) {
                    $video_url = $custom_video;
                } else {
                    $thumb_id = get_post_thumbnail_id( $p->ID );
                    if ( $thumb_id ) {
                        $poster = wp_get_attachment_image_url( $thumb_id, 'full' );
                    }
                }

                // Approximate reading / video time
                if ( empty( $duration ) || $duration === '22:44 min' ) {
                    $duration = '1 min';
                }
            }
        }

        if ( empty( $date ) ) {
            $date = date( 'd F Y' );
        }

        if ( empty( $title ) ) {
            $title = 'From ideas to innovation – Exclusive conversations with the trailblazers of India’s EV journey';
        }

        if ( empty( $duration ) || $duration === '22:44 min' ) {
            $duration = '1 min';
        }

        $video_embed = ! empty( $video_url ) ? cmr_get_video_embed_html( $video_url, $poster ) : '';

        ob_start();
        ?>
        <div class="cmr-fvi-card-wrapper" style="width: 100%; max-width: <?php echo esc_attr( $max_width ); ?>; margin: 0 auto;">
            <div class="cmr-fvi-card">
                
                <!-- Video / Media Box -->
                <div class="cmr-fvi-video-wrap">
                    <?php if ( ! empty( $video_embed ) ) : ?>
                        <?php echo $video_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php elseif ( ! empty( $poster ) ) : ?>
                        <a href="<?php echo esc_url( $link ); ?>" class="cmr-fvi-poster-link" <?php echo ( $target === '_blank' ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                            <img src="<?php echo esc_url( $poster ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="cmr-fvi-poster-img">
                        </a>
                    <?php else : ?>
                        <!-- Default Embedded Demo Video -->
                        <iframe src="https://www.youtube.com/embed/ScMzIvxBSi4?rel=0&modestbranding=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe>
                    <?php endif; ?>
                </div>

                <!-- Body Content -->
                <div class="cmr-fvi-body">
                    <!-- Meta info: Date & Duration -->
                    <div class="cmr-fvi-meta">
                        <div class="cmr-fvi-date">
                            <span class="cmr-fvi-dash"></span>
                            <span class="cmr-fvi-date-txt"><?php echo esc_html( $date ); ?></span>
                        </div>
                        <?php if ( ! empty( $duration ) ) : ?>
                            <div class="cmr-fvi-duration"><?php echo esc_html( $duration ); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Main Title -->
                    <h3 class="cmr-fvi-title">
                        <a href="<?php echo esc_url( $link ); ?>" <?php echo ( $target === '_blank' ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                            <?php echo esc_html( $title ); ?>
                        </a>
                    </h3>
                </div>

            </div>
        </div>

        <style id="cmr-fvi-styles">
            .cmr-fvi-card-wrapper {
                box-sizing: border-box;
            }
            .cmr-fvi-card {
                font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
                width: 100%;
                background: #FFFFFF;
                border: 1px solid #E5E7EB;
                border-radius: 0px;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                box-sizing: border-box;
                transition: border-color 0.25s ease, box-shadow 0.25s ease;
            }
            .cmr-fvi-card:hover {
                border-color: #CBD5E1;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
            }
            .cmr-fvi-video-wrap {
                position: relative;
                width: 100%;
                padding-top: 56.25%; /* 16:9 Aspect Ratio */
                background: #000000;
                overflow: hidden;
            }
            .cmr-fvi-video-wrap iframe,
            .cmr-fvi-video-wrap video,
            .cmr-fvi-video-wrap object,
            .cmr-fvi-video-wrap embed {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                border: none;
                object-fit: cover;
                display: block;
            }
            .cmr-fvi-poster-link {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                display: block;
            }
            .cmr-fvi-poster-img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            .cmr-fvi-play-icon {
                display: none !important;
            }
            .cmr-fvi-body {
                padding: 28px 32px 34px 32px;
                background: #FFFFFF;
                display: flex;
                flex-direction: column;
                flex-grow: 1;
                box-sizing: border-box;
            }
            .cmr-fvi-meta {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 18px;
                font-size: 14.5px;
                color: #475569;
                font-weight: 500;
            }
            .cmr-fvi-date {
                display: inline-flex;
                align-items: center;
                gap: 12px;
            }
            .cmr-fvi-dash {
                display: inline-block;
                width: 24px;
                height: 1.5px;
                background-color: #64748B;
            }
            .cmr-fvi-duration {
                color: #475569;
                font-size: 14px;
                font-weight: 500;
            }
            .cmr-fvi-title {
                font-family: inherit !important;
                font-size: 26px !important;
                font-weight: 600 !important;
                line-height: 1.34 !important;
                color: #0F172A !important;
                letter-spacing: -0.5px !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .cmr-fvi-title a {
                color: #0F172A !important;
                font-weight: 600 !important;
                text-decoration: none !important;
                transition: color 0.2s ease;
            }
            .cmr-fvi-title a:hover {
                color: #4F46E5 !important;
            }
            .cmr-fvi-action {
                display: none !important;
            }
            .cmr-fvi-btn {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                font-size: 16.5px;
                font-weight: 600;
                color: #0F172A !important;
                text-decoration: none !important;
                transition: all 0.25s ease;
                cursor: pointer;
            }
            .cmr-fvi-btn svg {
                width: 16px;
                height: 16px;
                transition: transform 0.25s ease;
            }
            .cmr-fvi-btn:hover {
                color: #4F46E5 !important;
            }
            .cmr-fvi-btn:hover svg {
                transform: translate(3px, -3px);
            }
            @media (max-width: 767.98px) {
                .cmr-fvi-body {
                    padding: 22px 20px 24px 20px;
                }
                .cmr-fvi-title {
                    font-size: 21px !important;
                    font-weight: 600 !important;
                    line-height: 1.35 !important;
                    margin: 0 !important;
                }
                .cmr-fvi-meta {
                    font-size: 13.5px;
                    margin-bottom: 14px;
                }
                .cmr-fvi-btn {
                    font-size: 15px;
                }
            }
        </style>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_featured_insight', 'cmr_featured_insight_shortcode' );
add_shortcode( 'cmr_featured_video_insight', 'cmr_featured_insight_shortcode' );
