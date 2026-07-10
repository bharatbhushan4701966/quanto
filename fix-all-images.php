<?php
require_once('../../../wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

if (!current_user_can('manage_options') && !isset($_GET['force'])) {
    die('Not allowed');
}

$api_base = 'https://cmrindia.com/wp-json/wp/v2';
$paged = isset($_GET['paged']) ? (int)$_GET['paged'] : 1;
$posts_per_page = 10; // Process 10 posts at a time

// Get posts from local staging database
$local_posts = get_posts(array(
    'post_type' => 'post',
    'numberposts' => $posts_per_page,
    'paged' => $paged,
    'orderby' => 'ID',
    'order' => 'ASC'
));

if (empty($local_posts)) {
    echo "<h1>Done! All posts have been fixed and images processed.</h1>";
    exit;
}

echo "<h1>Processing Page $paged...</h1>";

$updated_count = 0;
foreach ($local_posts as $local_post) {
    $slug = $local_post->post_name;
    
    // Fetch clean original content from LIVE
    $response = wp_remote_get($api_base . "/posts?slug=" . $slug);
    if (is_wp_error($response)) {
        echo "Error fetching from live: $slug<br>";
        continue;
    }
    
    $live_data = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($live_data)) {
        echo "Post not found on live site: $slug<br>";
        continue;
    }

    $content = $live_data[0]['content']['rendered'];

    // 1. Clean Elementor Junk
    $content = preg_replace('/<section[^>]*elementor-top-section[^>]*>.*?theme-post-title\.default.*?<\/section>/is', '', $content);
    $content = preg_replace('/<section[^>]*elementor-inner-section[^>]*>.*?Share This Post.*?<\/section>/is', '', $content);
    $content = preg_replace('/<h1 class="elementor-heading-title elementor-size-default">.*?<\/h1>/is', '', $content);
    $content = preg_replace('/<h2 class="elementor-heading-title elementor-size-default">Share This Post<\/h2>/is', '', $content);

    // 2. Fix Lazy Load (Securely)
    $content = preg_replace_callback('/<img([^>]+)>/i', function($matches) {
        $img = $matches[0];
        if (preg_match('/data-src=[\'"]([^\'"]+)[\'"]/i', $img, $ds_match)) {
            $real_url = $ds_match[1];
            $img = preg_replace('/ src=[\'"][^\'"]+[\'"]/i', '', $img);
            $img = preg_replace('/ data-src=[\'"][^\'"]+[\'"]/i', '', $img);
            $img = str_replace('<img ', '<img src="' . $real_url . '" ', $img);
        }
        return $img;
    }, $content);

    $content = str_replace('data-srcset=', 'srcset=', $content);
    $content = str_replace('data-sizes=', 'sizes=', $content);

    // 3. Download images
    preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches);
    if (!empty($matches[1])) {
        foreach (array_unique($matches[1]) as $img_url) {
            if (strpos($img_url, 'cmrindia.com') !== false) {
                // Check if already downloaded
                global $wpdb;
                $filename = basename($img_url);
                $existing_attachment = $wpdb->get_var($wpdb->prepare("SELECT guid FROM $wpdb->posts WHERE post_type = 'attachment' AND guid LIKE %s LIMIT 1", '%' . $wpdb->esc_like($filename)));
                
                if ($existing_attachment) {
                    $local_url = $existing_attachment;
                } else {
                    echo "Downloading image: $img_url<br>";
                    sleep(1); // Anti-rate limit
                    
                    add_filter('upload_mimes', function($mimes) {
                        $mimes['svg'] = 'image/svg+xml';
                        $mimes['webp'] = 'image/webp';
                        $mimes['JPG'] = 'image/jpeg';
                        $mimes['PNG'] = 'image/png';
                        return $mimes;
                    });
                    add_filter('unfiltered_upload', '__return_true');
                    
                    $attachment_id = media_sideload_image($img_url, 0, null, 'id');
                    remove_filter('unfiltered_upload', '__return_true');
                    
                    if (!is_wp_error($attachment_id)) {
                        $local_url = wp_get_attachment_url($attachment_id);
                    } else {
                        $local_url = false;
                    }
                }
                
                if ($local_url) {
                    $content = str_replace($img_url, $local_url, $content);
                }
            }
        }
    }

    // 4. Update Database
    if ($content !== $local_post->post_content) {
        wp_update_post(array(
            'ID' => $local_post->ID,
            'post_content' => $content
        ));
        $updated_count++;
        echo "Updated post: {$local_post->post_title}<br>";
    }
}

echo "<br>Finished page $paged. Updated $updated_count posts.<br>";
$next_page = $paged + 1;
echo "<script>setTimeout(function() { window.location.href = '?force=1&paged=$next_page'; }, 2000);</script>";
