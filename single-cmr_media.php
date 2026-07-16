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
    /* Audio Mode Styles */
    .cmr-audio-banner {
        width: 100%;
        height: 60vh;
        min-height: 400px;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cmr-audio-banner-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.65);
    }
    .cmr-audio-banner-content {
        position: relative;
        z-index: 2;
        text-align: center;
        color: #fff;
        padding: 0 20px;
        max-width: 900px;
    }
    .cmr-audio-breadcrumbs {
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 25px;
        color: #ddd;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .cmr-audio-breadcrumbs a {
        color: #ddd;
        text-decoration: none;
    }
    .cmr-audio-title {
        font-size: 52px;
        font-weight: 700;
        margin: 0 0 25px 0;
        color: #fff;
        line-height: 1.1;
        letter-spacing: -1px;
    }
    .cmr-audio-meta {
        font-size: 12px;
        font-weight: 700;
        color: #ddd;
        letter-spacing: 1px;
        text-transform: uppercase;
    }
    .cmr-audio-meta .dot {
        margin: 0 8px;
        opacity: 0.5;
    }
    .cmr-audio-player-wrapper {
        position: relative;
        z-index: 10;
        max-width: 900px;
        margin: -50px auto 50px auto;
        padding: 0 20px;
    }
    .cmr-audio-player-box {
        background: #fff;
        border: 4px solid #00A3FF;
        padding: 20px 30px;
        display: flex;
        align-items: center;
        gap: 25px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    }
    .audio-play-btn {
        background: transparent;
        border: none;
        cursor: pointer;
        color: #000;
        padding: 0;
        display: flex;
        transition: transform 0.2s;
    }
    .audio-play-btn:hover {
        transform: scale(1.05);
    }
    .audio-play-btn svg {
        width: 44px;
        height: 44px;
    }
    .audio-progress-container {
        flex: 1;
        height: 6px;
        background: #e0e0e0;
        border-radius: 3px;
        position: relative;
        cursor: pointer;
    }
    .audio-progress-fill {
        position: absolute;
        top: 0; left: 0; bottom: 0;
        background: #000;
        width: 0%;
        border-radius: 3px;
        pointer-events: none;
    }
    .audio-progress-thumb {
        position: absolute;
        top: 50%;
        left: 0%;
        transform: translate(-50%, -50%);
        width: 16px;
        height: 16px;
        background: #000;
        border-radius: 50%;
        pointer-events: none;
    }
    .audio-time {
        font-size: 13px;
        font-weight: 600;
        color: #333;
        min-width: 90px;
        text-align: center;
    }
    .audio-secondary-ctrls {
        display: flex;
        gap: 15px;
        align-items: center;
    }
    .audio-ctrl-icon {
        background: transparent;
        border: none;
        cursor: pointer;
        color: #333;
        padding: 0;
        display: flex;
        transition: color 0.2s;
    }
    .audio-ctrl-icon:hover {
        color: #00A3FF;
    }
    .audio-ctrl-icon svg {
        width: 22px;
        height: 22px;
    }
    
    @media (max-width: 768px) {
        .cmr-audio-title {
            font-size: 32px;
        }
        .cmr-audio-player-box {
            flex-wrap: wrap;
            padding: 15px;
            gap: 15px;
        }
        .audio-progress-container {
            order: 4;
            min-width: 100%;
            margin-top: 5px;
        }
        .audio-secondary-ctrls {
            margin-left: auto;
        }
    }
</style>

<?php
$is_audio_post = (strtoupper($media_type) === 'PODCAST') || preg_match('/\.(mp3|wav|ogg|m4a)$/i', $media_url);

// Provide a sample MP3 for testing if none is set but it's a podcast
if ($is_audio_post && !preg_match('/\.(mp3|wav|ogg|m4a)$/i', $media_url)) {
    $media_url = 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3';
}
?>

<div class="cmr-media-single-wrapper <?php echo $is_audio_post ? 'cmr-audio-mode' : ''; ?>">
    <?php if ($is_audio_post): ?>
        
        <!-- Audio Banner -->
        <div class="cmr-audio-banner" style="background-image: url('<?php echo esc_url($thumbnail_url); ?>');">
            <div class="cmr-audio-banner-overlay"></div>
            <div class="cmr-audio-banner-content">
                <div class="cmr-audio-breadcrumbs">
                    <a href="/">CMR Live</a> &rsaquo; <a href="#"><?php echo esc_html(ucwords(strtolower($media_type))); ?></a> &rsaquo; <?php the_title(); ?>
                </div>
                <h1 class="cmr-audio-title"><?php the_title(); ?></h1>
                <div class="cmr-audio-meta">
                    <span><?php echo esc_html(strtoupper($media_type)); ?></span> <span class="dot">&bull;</span> 
                    <span><?php echo esc_html(strtoupper($date)); ?></span> <span class="dot">&bull;</span> 
                    <span>BY <?php echo esc_html(strtoupper($author)); ?></span> <span class="dot">&bull;</span> 
                    <span><?php echo esc_html(strtoupper($media_duration)); ?></span> <span class="dot">&bull;</span> 
                    <span><?php echo esc_html(strtoupper($views)); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Floating Audio Player -->
        <div class="cmr-audio-player-wrapper">
            <div class="cmr-audio-player-box">
                <button id="cmr-audio-play-btn" class="audio-play-btn" aria-label="Play">
                    <!-- Play icon by default -->
                    <svg class="icon-play" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8" fill="currentColor"></polygon></svg>
                    <!-- Pause icon hidden by default -->
                    <svg class="icon-pause" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="display:none;"><circle cx="12" cy="12" r="10"></circle><line x1="10" y1="15" x2="10" y2="9"></line><line x1="14" y1="15" x2="14" y2="9"></line></svg>
                </button>
                <div class="audio-progress-container" id="cmr-audio-progress-container">
                    <div class="audio-progress-fill" id="cmr-audio-progress-fill"></div>
                    <div class="audio-progress-thumb" id="cmr-audio-progress-thumb"></div>
                </div>
                <div class="audio-time">
                    <span id="cmr-audio-current">00:00</span> / <span id="cmr-audio-total"><?php echo esc_html($media_duration); ?></span>
                </div>
                <div class="audio-secondary-ctrls">
                    <button class="audio-ctrl-icon" id="cmr-audio-rw" aria-label="Rewind 10s">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 17l-5-5 5-5"></path><path d="M18 17l-5-5 5-5"></path></svg>
                    </button>
                    <button class="audio-ctrl-icon" id="cmr-audio-ff" aria-label="Forward 10s">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 17l5-5-5-5"></path><path d="M6 17l5-5-5-5"></path></svg>
                    </button>
                    <button class="audio-ctrl-icon" aria-label="Volume">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                    </button>
                    <button class="audio-ctrl-icon" aria-label="Share">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </button>
                    <button class="audio-ctrl-icon" aria-label="More">
                        <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="2"></circle><circle cx="12" cy="5" r="2"></circle><circle cx="12" cy="19" r="2"></circle></svg>
                    </button>
                </div>
            </div>
            <audio id="cmr-native-audio" src="<?php echo esc_url($media_url); ?>" preload="metadata"></audio>
        </div>

        <div class="cmr-media-container">
            <!-- For Audio, we already have title/meta in the banner, skip them here -->

    <?php else: ?>

        <!-- Media Banner (Video) -->
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

    <?php endif; ?>

        
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
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg> 
                                <span>Thanks for your feedback!</span>
                            </div>
                            <div class="thanks-action">
                                <span>Want deeper insights like this?</span> 
                                <a href="/contact">Talk to an Analyst <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Closes cmr-media-content -->

        </div> <!-- Closes cmr-media-content-layout -->
    </div> <!-- Closes cmr-container -->
</div> <!-- Closes cmr-media-single-wrapper -->

<!-- CTA Banner Section via Elementor Template -->
<?php echo do_shortcode('[cmr_single_media_cta]'); ?>

<!-- Related Media Section -->
<div class="cmr-related-media-section">
    <div class="cmr-container">
        <h2>Related <?php echo esc_html(ucwords(strtolower($media_type))); ?></h2>
        <div class="cmr-media-grid-wrap">
            <?php 
            $related_args = array(
                'post_type' => 'cmr_media',
                'posts_per_page' => 3,
                'post__not_in' => array(get_the_ID()),
                'meta_query' => array(
                    array(
                        'key' => '_cmr_media_type',
                        'value' => $media_type,
                        'compare' => '='
                    )
                )
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
    var audioPlayer = document.getElementById('cmr-native-audio');

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
    
    function formatTime(seconds) {
        if (isNaN(seconds)) return '00:00';
        var m = Math.floor(seconds / 60);
        var s = Math.floor(seconds % 60);
        return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
    }

    document.addEventListener('DOMContentLoaded', function() {
        var videoEl = document.getElementById('cmr-main-video-player');
        if (videoEl && videoEl.tagName.toLowerCase() === 'video') {
            playerType = 'html5';
            player = videoEl;
        }

        // Custom Audio Player Logic
        if (audioPlayer) {
            playerType = 'audio';
            player = audioPlayer;
            
            var playBtn = document.getElementById('cmr-audio-play-btn');
            var iconPlay = playBtn ? playBtn.querySelector('.icon-play') : null;
            var iconPause = playBtn ? playBtn.querySelector('.icon-pause') : null;
            var progressContainer = document.getElementById('cmr-audio-progress-container');
            var progressFill = document.getElementById('cmr-audio-progress-fill');
            var progressThumb = document.getElementById('cmr-audio-progress-thumb');
            var timeCurrent = document.getElementById('cmr-audio-current');
            var timeTotal = document.getElementById('cmr-audio-total');
            var rwBtn = document.getElementById('cmr-audio-rw');
            var ffBtn = document.getElementById('cmr-audio-ff');
            
            if (playBtn) {
                playBtn.addEventListener('click', function() {
                    if (audioPlayer.paused) {
                        audioPlayer.play();
                    } else {
                        audioPlayer.pause();
                    }
                });
            }
            
            audioPlayer.addEventListener('play', function() {
                if (iconPlay) iconPlay.style.display = 'none';
                if (iconPause) iconPause.style.display = 'block';
            });
            
            audioPlayer.addEventListener('pause', function() {
                if (iconPlay) iconPlay.style.display = 'block';
                if (iconPause) iconPause.style.display = 'none';
            });
            
            audioPlayer.addEventListener('loadedmetadata', function() {
                if (timeTotal) timeTotal.textContent = formatTime(audioPlayer.duration);
            });
            
            audioPlayer.addEventListener('timeupdate', function() {
                var percent = (audioPlayer.currentTime / audioPlayer.duration) * 100;
                if (progressFill) progressFill.style.width = percent + '%';
                if (progressThumb) progressThumb.style.left = percent + '%';
                if (timeCurrent) timeCurrent.textContent = formatTime(audioPlayer.currentTime);
            });
            
            if (progressContainer) {
                progressContainer.addEventListener('click', function(e) {
                    var rect = progressContainer.getBoundingClientRect();
                    var clickX = e.clientX - rect.left;
                    var percent = clickX / rect.width;
                    audioPlayer.currentTime = percent * audioPlayer.duration;
                });
            }
            
            if (rwBtn) {
                rwBtn.addEventListener('click', function() {
                    audioPlayer.currentTime = Math.max(0, audioPlayer.currentTime - 10);
                });
            }
            
            if (ffBtn) {
                ffBtn.addEventListener('click', function() {
                    audioPlayer.currentTime = Math.min(audioPlayer.duration, audioPlayer.currentTime + 10);
                });
            }
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
                } else if (playerType === 'audio' && player) {
                    player.currentTime = time;
                    player.play();
                }
                
                // Scroll to media
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
        
        if (feedbackBtns && feedbackThanks) {
            feedbackBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelector('.feedback-actions').style.display = 'none';
                    feedbackThanks.style.display = 'flex';
                });
            });
        }
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
    letter-spacing: normal !important;
}
.feedback-subtitle {
    font-size: 15px;
    color: #555;
    margin-bottom: 20px;
    letter-spacing: normal !important;
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
    letter-spacing: normal !important;
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
    letter-spacing: normal !important;
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
    letter-spacing: normal !important;
}
.thanks-action a {
    color: #6A35FF;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
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
    background-color: #f7f7f9;
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
.cmr-browse-card {
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: #000;
}
.cmr-browse-img-wrap {
    position: relative;
    width: 100%;
    aspect-ratio: 16/10;
    overflow: hidden;
    margin-bottom: 20px;
    background: #f5f5f5;
}
.cmr-browse-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.cmr-browse-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: #fff;
    color: #111;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 4px;
    z-index: 2;
}
.cmr-browse-play-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50px;
    height: 50px;
    background: transparent;
    border: 2px solid #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}
.cmr-browse-play-btn svg {
    width: 20px;
    height: 20px;
    fill: #fff;
    margin-left: 3px;
}
.cmr-browse-meta {
    font-size: 11px;
    font-weight: 600;
    color: #888;
    margin-bottom: 12px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.cmr-browse-meta .type-podcast {
    color: #00d2ff;
}
.cmr-browse-meta .type-topview {
    color: #2979ff;
}
.cmr-browse-card-title {
    font-size: 22px;
    font-weight: 600;
    color: #000;
    margin: 0;
    line-height: 1.3;
    letter-spacing: 0 !important;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<?php
// Render globally requested shortcodes at the bottom of the page
echo do_shortcode('[cmr_testimonials]');
echo do_shortcode('[cmr_global_brands]');
echo do_shortcode('[cmr_challenge]');
echo do_shortcode('[cmr_footer_card]');

get_footer();
?>
