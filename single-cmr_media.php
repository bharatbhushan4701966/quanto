<?php
/**
 * Single Template for CMR Media (Podcasts, Videos, Top Views, Webinars)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();
    
    $media_type = get_post_meta( get_the_ID(), '_cmr_media_type', true ) ?: 'PODCAST';
    $media_url = get_post_meta( get_the_ID(), '_cmr_media_url', true );
    $media_duration = get_post_meta( get_the_ID(), '_cmr_media_duration', true ) ?: '10 MIN';
    
    $views = '100 VIEW';
    $date = get_the_date('d M Y');
    $author = 'CMR INDIA';
    
    $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
    
    // For social sharing links
    $post_url = urlencode( get_permalink() );
    $post_title = urlencode( get_the_title() );
?>

<style>
    .cmr-media-single-wrapper {
        font-family: inherit;
        background-color: #fff;
        padding-bottom: 80px;
        margin-top: 120px;
    }
    
    /* Media Banner (Video Player Area) */
    .cmr-media-banner {
        width: 100%;
        height: 75vh;
        max-height: 800px;
        min-height: 400px;
        background-color: #000;
        position: relative;
        overflow: hidden;
    }
    .cmr-media-banner iframe,
    .cmr-media-banner video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    /* Content Container */
    .cmr-media-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 60px 20px 20px 20px;
    }
    
    /* Title and Meta */
    .cmr-media-title {
        font-size: 48px;
        font-weight: 600;
        color: #000;
        margin: 0 0 20px 0;
        line-height: 1.2;
        letter-spacing: -0.5px;
    }
    .cmr-media-meta {
        font-size: 13px;
        font-weight: 600;
        color: #333;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 60px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .cmr-media-meta .dot {
        font-size: 16px;
        line-height: 1;
    }
    
    /* Layout */
    .cmr-media-content-layout {
        display: grid;
        grid-template-columns: 100px 1fr;
        gap: 40px;
    }
    
    /* Share Icons */
    .cmr-media-share {
        display: flex;
        flex-direction: column;
        gap: 15px;
        position: sticky;
        top: 100px;
    }
    .share-icon {
        width: 48px;
        height: 48px;
        border: 1px solid #e0e0e0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        text-decoration: none;
        transition: all 0.3s ease;
        background: #fff;
    }
    .share-icon:hover {
        border-color: #000;
        color: #000;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .share-icon svg {
        width: 18px;
        height: 18px;
        fill: currentColor;
    }
    
    /* Main Content */
    .cmr-media-content {
        max-width: 900px;
    }
    .cmr-media-content h2 {
        font-size: 32px;
        font-weight: 600;
        color: #000;
        margin: 0 0 25px 0;
        letter-spacing: -0.5px;
    }
    .cmr-media-description {
        font-size: 16px;
        line-height: 1.8;
        color: #444;
        margin-bottom: 60px;
    }
    .cmr-media-description p {
        margin-bottom: 20px;
    }
    
    /* Key Parts */
    .cmr-media-key-parts h2 {
        margin-bottom: 30px;
    }
    .key-parts-list {
        list-style: none;
        padding: 0;
        margin: 0;
        border-top: 1px solid #f0f0f0;
    }
    .key-parts-list li {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 25px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .play-part-btn {
        width: 44px;
        height: 44px;
        border: 1px solid #e0e0e0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .play-part-btn:hover {
        border-color: #000;
        background: #fafafa;
    }
    .play-part-btn svg {
        width: 14px;
        height: 14px;
        fill: #000;
        margin-left: 2px;
    }
    .key-parts-list span {
        font-size: 17px;
        font-weight: 600;
        color: #000;
    }
    
    @media (max-width: 768px) {
        .cmr-media-banner {
            height: 50vh;
        }
        .cmr-media-title {
            font-size: 32px;
        }
        .cmr-media-content-layout {
            grid-template-columns: 1fr;
        }
        .cmr-media-share {
            flex-direction: row;
            position: static;
            margin-bottom: 30px;
        }
    }
</style>

<div class="cmr-media-single-wrapper">
    <!-- Media Banner -->
    <div class="cmr-media-banner">
        <?php if($media_url): ?>
            <?php 
                $is_youtube = strpos($media_url, 'youtube.com') !== false || strpos($media_url, 'youtu.be') !== false;
                if($is_youtube) {
                    $video_id = '';
                    if ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $media_url, $match ) ) {
                        $video_id = $match[1];
                    }
                    if($video_id) {
                        echo '<iframe id="cmr-main-video-player" width="100%" height="100%" src="https://www.youtube.com/embed/'.$video_id.'?autoplay=0&enablejsapi=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                    }
                } else {
                    echo '<video id="cmr-main-video-player" controls width="100%" poster="'.esc_url($thumbnail_url).'"><source src="'.esc_url($media_url).'" type="video/mp4"></video>';
                }
            ?>
        <?php else: ?>
            <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" style="width:100%; height:100%; object-fit:cover;">
        <?php endif; ?>
    </div>

    <div class="cmr-media-container">
        <!-- Title and Meta -->
        <h1 class="cmr-media-title"><?php the_title(); ?></h1>
        <div class="cmr-media-meta">
            <span><?php echo esc_html($views); ?></span>
            <span class="dot">&bull;</span>
            <span><?php echo esc_html($date); ?></span>
            <span class="dot">&bull;</span>
            <span><?php echo esc_html($media_duration); ?> <?php echo esc_html($media_type); ?></span>
            <span class="dot">&bull;</span>
            <span>BY <?php echo esc_html($author); ?></span>
        </div>
        
        <div class="cmr-media-content-layout">
            <!-- Left Side: Share Icons -->
            <div class="cmr-media-share">
                <!-- LinkedIn -->
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $post_url; ?>&title=<?php echo $post_title; ?>" target="_blank" rel="noopener noreferrer" class="share-icon" aria-label="Share on LinkedIn">
                    <svg viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                </a>
                <!-- X (Twitter) -->
                <a href="https://twitter.com/intent/tweet?url=<?php echo $post_url; ?>&text=<?php echo $post_title; ?>" target="_blank" rel="noopener noreferrer" class="share-icon" aria-label="Share on X">
                    <svg viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                </a>
                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $post_url; ?>" target="_blank" rel="noopener noreferrer" class="share-icon" aria-label="Share on Facebook">
                    <svg viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                </a>
                <!-- WhatsApp -->
                <a href="https://api.whatsapp.com/send?text=<?php echo $post_title; ?>%20<?php echo $post_url; ?>" target="_blank" rel="noopener noreferrer" class="share-icon" aria-label="Share on WhatsApp">
                    <svg viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.88-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.347-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.876 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                </a>
            </div>
            
            <!-- Right Side: Content -->
            <div class="cmr-media-content">
                <h2>About this <?php echo esc_html($media_type); ?></h2>
                <div class="cmr-media-description">
                    <?php 
                        $content = get_the_content();
                        if(empty($content)) {
                            // Dummy content from image if post is empty
                            echo '<p>In today\'s competitive landscape, generating leads is only half the battle—closing them effectively is where real value lies. This episode explores how organizations can refine their sales strategies using market intelligence, behavioral insights, and data-driven decision-making.</p>';
                            echo '<p>From understanding customer intent to optimizing engagement touchpoints, this discussion highlights the key factors influencing successful conversions across industries.</p>';
                        } else {
                            the_content();
                        }
                    ?>
                </div>
                
                <div class="cmr-media-key-parts">
                    <h2>Key parts of <?php echo esc_html(get_the_title()); ?></h2>
                    <ul class="key-parts-list">
                        <?php 
                        $key_parts_text = get_post_meta( get_the_ID(), '_cmr_media_key_parts', true );
                        
                        // Fallback to dummy data if dashboard field is empty
                        if ( empty( trim( $key_parts_text ) ) ) {
                            $key_parts_text = "00:00 - Introduction & Welcome\n01:30 - Understanding the Core Topics\n05:15 - Deep Dive into Market Trends\n10:45 - Key Takeaways & Conclusion";
                        }
                        
                        if ( !empty($key_parts_text) ) :
                            $lines = explode("\n", str_replace("\r", "", $key_parts_text));
                            foreach ( $lines as $line ) :
                                $line = trim($line);
                                if ( empty($line) ) continue;
                                
                                // Format: "00:05 - Title"
                                $parts = explode('-', $line, 2);
                                $time_str = isset($parts[0]) ? trim($parts[0]) : '';
                                $title_str = isset($parts[1]) ? trim($parts[1]) : '';
                                
                                if ( empty($time_str) || empty($title_str) ) continue;

                                // Convert "MM:SS" or "HH:MM:SS" to seconds
                                $time_parts = explode(':', $time_str);
                                $seconds = 0;
                                if (count($time_parts) === 2) {
                                    $seconds = intval($time_parts[0]) * 60 + intval($time_parts[1]);
                                } elseif (count($time_parts) === 3) {
                                    $seconds = intval($time_parts[0]) * 3600 + intval($time_parts[1]) * 60 + intval($time_parts[2]);
                                }
                        ?>
                        <li>
                            <button class="play-part-btn" data-time="<?php echo esc_attr($seconds); ?>"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></button>
                            <span><?php echo esc_html($time_str); ?> &ndash; <?php echo esc_html($title_str); ?></span>
                        </li>
                        <?php
                            endforeach;
                        else:
                        ?>
                            <!-- No key parts found for this media. Please add them in the WP Admin. -->
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Insight Feedback Section -->
            <div class="cmr-insight-feedback-section">
                <div class="feedback-container">
                    <h3 class="feedback-title">How helpful was this insight?</h3>
                    <p class="feedback-subtitle">Your feedback helps us improve our research quality</p>
                    <div class="feedback-actions">
                        <button class="feedback-btn js-feedback-helpful">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg> 
                            Yes, helpful
                        </button>
                        <button class="feedback-btn js-feedback-unhelpful">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transform: scaleY(-1);"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg> 
                            Not really
                        </button>
                    </div>
                    <div class="feedback-thanks" style="display: none;">
                        <div class="thanks-msg">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg> 
                            <span>Thanks for your feedback!</span>
                        </div>
                        <div class="thanks-action">
                            <span>Want deeper insights like this?</span> <a href="/contact">Talk to an Analyst ↗</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Related Top View Section -->
<div class="cmr-related-media-section">
    <div class="cmr-container">
        <h2>Related Top View</h2>
        <div class="cmr-media-grid-wrap">
            <?php 
            $related_args = array(
                'post_type' => 'cmr_media',
                'posts_per_page' => 6,
                'post__not_in' => array(get_the_ID()),
            );
            $related_query = new WP_Query($related_args);
            if ($related_query->have_posts()) :
                while ($related_query->have_posts()) : $related_query->the_post();
                    $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                    if ( ! $thumbnail_url ) {
                        $thumbnail_url = 'https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/Why-Chipsets-are-the-New-Frontier-in-Smartphones1.jpg';
                    }
                    
                    $category_name = 'AUTOMOTIVE';
                    $terms = get_the_terms( get_the_ID(), 'category' );
                    if ( $terms && ! is_wp_error( $terms ) ) {
                        $category_name = $terms[0]->name;
                    }
                    
                    $post_date = get_the_date('d M Y');
                    $media_type = get_post_meta( get_the_ID(), '_cmr_media_type', true );
                    $media_duration = get_post_meta( get_the_ID(), '_cmr_media_duration', true );
                    
                    $mtype = $media_type ? $media_type : 'TOP VIEW';
                    $is_podcast = (strtoupper($mtype) === 'PODCAST');
                    $type_class = $is_podcast ? 'type-podcast' : 'type-topview';
                    $duration = $media_duration ? $media_duration : '05:00 MINS';
                    $link = esc_url(get_permalink(get_the_ID()));
            ?>
            <a href="<?php echo $link; ?>" class="cmr-browse-card" target="_blank" rel="noopener noreferrer">
                <div class="cmr-browse-img-wrap">
                    <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <div class="cmr-browse-badge"><?php echo esc_html($duration); ?></div>
                    <div class="cmr-browse-play-btn">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>
                
                <div class="cmr-browse-meta">
                    <span class="<?php echo esc_attr($type_class); ?>"><?php echo esc_html($mtype); ?></span> &bull; 
                    <?php echo esc_html(strtoupper($category_name)); ?> &bull; 
                    <?php echo esc_html(strtoupper($post_date)); ?>
                </div>
                
                <h3 class="cmr-browse-card-title"><?php echo esc_html(get_the_title()); ?></h3>
            </a>
            <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</div>

<?php
endwhile;

?>

<script src="https://www.youtube.com/iframe_api"></script>
<script>
    var player;
    var playerType = 'none';

    function onYouTubeIframeAPIReady() {
        var iframe = document.getElementById('cmr-main-video-player');
        if (iframe && iframe.tagName.toLowerCase() === 'iframe') {
            playerType = 'youtube';
            player = new YT.Player('cmr-main-video-player', {
                events: {
                    'onReady': function(event) {
                        // ready
                    }
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var videoEl = document.getElementById('cmr-main-video-player');
        if (videoEl && videoEl.tagName.toLowerCase() === 'video') {
            playerType = 'html5';
            player = videoEl;
        }

        var buttons = document.querySelectorAll('.play-part-btn');
        buttons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var time = parseInt(this.getAttribute('data-time'), 10);
                if (playerType === 'youtube' && player && player.seekTo) {
                    player.seekTo(time, true);
                    player.playVideo();
                } else if (playerType === 'html5' && player) {
                    player.currentTime = time;
                    player.play();
                }
                
                // Scroll to video
                var banner = document.querySelector('.cmr-media-banner');
                if (banner) {
                    var offset = banner.getBoundingClientRect().top + window.scrollY - 100; // Account for sticky header
                    window.scrollTo({top: offset, behavior: 'smooth'});
                }
            });
        });

        // Feedback widget JS
        const feedbackBtns = document.querySelectorAll('.feedback-btn');
        const feedbackThanks = document.querySelector('.feedback-thanks');
        
        feedbackBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelector('.feedback-actions').style.display = 'none';
                feedbackThanks.style.display = 'flex';
            });
        });
    });
</script>

<style>
/* Insight Feedback Section */
.cmr-insight-feedback-section {
    margin-top: 50px;
    padding: 30px 0;
    border-top: 1px solid #eaeaea;
}
.feedback-container {
    max-width: 800px;
}
.feedback-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 5px;
    color: #111;
}
.feedback-subtitle {
    font-size: 15px;
    color: #555;
    margin-bottom: 20px;
}
.feedback-actions {
    display: flex;
    gap: 15px;
}
.feedback-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: transparent;
    border: none;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    color: #444;
    transition: all 0.3s;
}
.feedback-btn svg {
    color: #FF3B30;
}
.feedback-btn.js-feedback-helpful svg {
    color: #6A35FF;
}
.feedback-btn:hover {
    opacity: 0.8;
}
.feedback-thanks {
    background: #f7f7f9;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.thanks-msg {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #6A35FF;
    font-weight: 700;
    font-size: 18px;
}
.thanks-action {
    background: #fff;
    border-radius: 20px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.thanks-action a {
    color: #6A35FF;
    text-decoration: none;
}
@media (max-width: 768px) {
    .feedback-thanks {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
}

/* Related Top View Section */
.cmr-related-media-section {
    padding: 60px 0;
    background-color: #fff;
    border-top: 1px solid #eaeaea;
}
.cmr-related-media-section .cmr-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}
.cmr-related-media-section h2 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 40px;
    color: #111;
}
.cmr-media-grid-wrap {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}
@media (max-width: 992px) {
    .cmr-media-grid-wrap {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 768px) {
    .cmr-media-grid-wrap {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
get_footer();
?>
