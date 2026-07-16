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
        font-weight: 700;
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
        font-weight: 700;
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
                        echo '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/'.$video_id.'?autoplay=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                    }
                } else {
                    echo '<video controls width="100%" poster="'.esc_url($thumbnail_url).'"><source src="'.esc_url($media_url).'" type="video/mp4"></video>';
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
                    <h2>View to these key parts</h2>
                    <ul class="key-parts-list">
                        <!-- Normally this would be parsed from content or a repeater field, hardcoded fallback based on design -->
                        <li>
                            <button class="play-part-btn"><svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg></button>
                            <span>00:02 &ndash; Understanding Lead Quality vs Quantity</span>
                        </li>
                        <li>
                            <button class="play-part-btn"><svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg></button>
                            <span>00:05 &ndash; Role of Customer Intent in Conversion</span>
                        </li>
                        <li>
                            <button class="play-part-btn"><svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg></button>
                            <span>00:10 &ndash; Aligning Sales & Marketing Teams</span>
                        </li>
                        <li>
                            <button class="play-part-btn"><svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg></button>
                            <span>00:15 &ndash; Leveraging Data for Smarter Decisions</span>
                        </li>
                        <li>
                            <button class="play-part-btn"><svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg></button>
                            <span>00:20 &ndash; Future of AI in Lead Conversion</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
endwhile;

get_footer();
?>
