<?php
require_once('../../../wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

$api_base = 'https://cmrindia.com/wp-json/wp/v2';
$slug = isset($_GET['slug']) ? $_GET['slug'] : 'yes-chatbots-can-replace-humans';

echo "Fetching post from live site: $slug<br>";

// Fetch from LIVE
$response = wp_remote_get($api_base . "/posts?slug=" . $slug);
if (is_wp_error($response)) {
    die("Error fetching from API");
}
$posts = json_decode(wp_remote_retrieve_body($response), true);
if (empty($posts)) {
    die("Post not found on live site");
}

$post = $posts[0];
$content = $post['content']['rendered'];

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
            echo "Downloading image: $img_url<br>";
            
            // Allow all formats
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
                $content = str_replace($img_url, $local_url, $content);
                echo "Success: $local_url<br>";
            } else {
                echo "Failed: " . $attachment_id->get_error_message() . "<br>";
            }
        }
    }
}

// 4. Update Database
global $wpdb;
$existing_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = 'post' LIMIT 1", $slug));

if ($existing_id) {
    wp_update_post(array(
        'ID' => $existing_id,
        'post_content' => $content
    ));
    echo "Successfully updated post ID: $existing_id<br>";
} else {
    echo "Post not found in staging database to update.<br>";
}
echo "Done.";
