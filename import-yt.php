<?php
// Script to import YouTube playlist videos into CMR Media post type

$xml_string = file_get_contents( __DIR__ . '/yt_rss.xml');
if (!$xml_string) {
    exit;
}

// Convert UTF-16 to UTF-8 if needed, but it seems it's UTF-16 BOM from powershell.
// Let's strip the BOM and convert encoding if needed, or simply let simplexml handle it.
// Actually, PowerShell Out-File defaults to UTF-16LE. simplexml might choke on it.
$xml_string = mb_convert_encoding($xml_string, 'UTF-8', 'UTF-16LE');

// Remove any leading characters before <?xml
$start = strpos($xml_string, '<?xml');
if ($start !== false) {
    $xml_string = substr($xml_string, $start);
}

$xml = simplexml_load_string($xml_string);
if (!$xml) {
    exit;
}

$namespaces = $xml->getNamespaces(true);

$count = 0;
foreach ($xml->entry as $entry) {
    $yt = $entry->children($namespaces['yt']);
    $media = $entry->children($namespaces['media']);
    
    $video_id = (string) $yt->videoId;
    $title = (string) $entry->title;
    $url = (string) $entry->link->attributes()->href;
    
    // Duration is not reliably in RSS, so we will use a fallback or try to extract from media:group if available.
    $duration = "15:00 MINS"; // Fallback duration
    
    // Check if post already exists
    $existing = get_posts(array(
        'post_type' => 'cmr_media',
        'title' => $title,
        'post_status' => 'publish',
        'numberposts' => 1
    ));
    
    if (empty($existing)) {
        $post_id = wp_insert_post(array(
            'post_title' => $title,
            'post_type' => 'cmr_media',
            'post_status' => 'publish',
            'post_content' => ''
        ));
        
        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, '_cmr_media_type', 'TOP VIEW');
            update_post_meta($post_id, '_cmr_media_source', 'link');
            update_post_meta($post_id, '_cmr_media_url', $url);
            update_post_meta($post_id, '_cmr_media_duration', $duration);
            
            // Try to set featured image
            $thumbnail_url = "https://i.ytimg.com/vi/{$video_id}/maxresdefault.jpg";
            
            // Download and attach image (simplified)
            if ( ! function_exists( 'media_handle_sideload' ) ) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');
                require_once(ABSPATH . 'wp-admin/includes/image.php');
            }
            
            $tmp = download_url($thumbnail_url);
            if (!is_wp_error($tmp)) {
                $file_array = array(
                    'name' => $video_id . '.jpg',
                    'tmp_name' => $tmp
                );
                $thumb_id = media_handle_sideload($file_array, $post_id);
                if (!is_wp_error($thumb_id)) {
                    set_post_thumbnail($post_id, $thumb_id);
                } else {
                    @unlink($tmp);
                }
            }
            $count++;
        }
    }
}
