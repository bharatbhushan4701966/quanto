<?php
/**
 * Script to import pages from cmrindia.com as standard posts.
 * Automatically appends " 2" to all categories.
 */

require_once('../../../wp-load.php');

if (!current_user_can('manage_options') && !isset($_GET['force'])) {
    die('Not allowed');
}

require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

$source_domain = 'https://cmrindia.com';
$api_base = $source_domain . '/wp-json/wp/v2';
$log_file = __DIR__ . '/import-pages-log.txt';
$state_file = __DIR__ . '/import-pages-state.json';

function cmr_get_attachment_by_filename($url) {
    global $wpdb;
    $filename = basename($url);
    $name = preg_replace('/\.[^.]+$/', '', $filename);
    $query = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_name = %s LIMIT 1", $name);
    $id = $wpdb->get_var($query);
    if ($id) {
        return wp_get_attachment_url($id);
    }
    return false;
}

function cmr_log($message) {
    global $log_file;
    $date = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$date] $message\n", FILE_APPEND);
    echo $message . "<br>\n";
    flush();
}

cmr_log("Starting pages-as-posts import script...");

$state = array(
    'categories_done' => false,
    'posts_page' => 1,
    'category_map' => array()
);

if (file_exists($state_file)) {
    $state = json_decode(file_get_contents($state_file), true);
    cmr_log("Resuming from state: Page " . $state['posts_page']);
} else {
    cmr_log("Starting fresh import.");
}

function fetch_api($url) {
    $response = wp_remote_get($url, array('timeout' => 60));
    if (is_wp_error($response)) return false;
    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}

// 1. Fetch live categories and create " 2" versions locally
if (!$state['categories_done']) {
    cmr_log("Importing categories and creating ' 2' versions...");
    $all_cats = array();
    $page = 1;
    while (true) {
        $cats = fetch_api($api_base . "/categories?per_page=100&page=$page");
        if (empty($cats) || isset($cats['code'])) break;
        foreach ($cats as $cat) {
            $all_cats[] = $cat;
        }
        $page++;
    }
    
    foreach ($all_cats as $cat) {
        $new_name = $cat['name'] . " 2";
        $new_slug = $cat['slug'] . "-2";
        
        $term = term_exists($new_name, 'category');
        if (!$term) {
            $term = wp_insert_term($new_name, 'category', array(
                'slug' => $new_slug
            ));
        }
        if (!is_wp_error($term) && isset($term['term_id'])) {
            $state['category_map'][$cat['id']] = $term['term_id'];
        }
    }
    
    $state['categories_done'] = true;
    file_put_contents($state_file, json_encode($state));
    cmr_log("Categories mapped.");
}

// 2. Import Pages as Posts
cmr_log("Importing pages from page " . $state['posts_page']);
$max_pages_per_run = 5; 

$run_pages = 0;
while ($run_pages < $max_pages_per_run) {
    $page = $state['posts_page'];
    // Fetch from PAGES instead of POSTS
    $posts = fetch_api($api_base . "/pages?per_page=5&page=$page&_embed=1");
    
    if (empty($posts) || isset($posts['code'])) {
        cmr_log("No more pages found. Import complete.");
        if (file_exists($state_file)) unlink($state_file);
        break;
    }
    
    global $wpdb;

    foreach ($posts as $post) {
        $slug = $post['slug'];
        
        // We save pages as POSTS locally, so we check for post_type = 'post'
        $existing_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = 'post' LIMIT 1", $slug));
        
        $author_id = 1; // Default
        
        $content = $post['content']['rendered'];
        
        // Fix Smush lazy loaded images securely
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

        // Remove unwanted Elementor blocks
        $content = preg_replace('/<section[^>]*elementor-top-section[^>]*>.*?theme-post-title\.default.*?<\/section>/is', '', $content);
        $content = preg_replace('/<section[^>]*elementor-inner-section[^>]*>.*?Share This Post.*?<\/section>/is', '', $content);
        $content = preg_replace('/<h1 class="elementor-heading-title elementor-size-default">.*?<\/h1>/is', '', $content);
        $content = preg_replace('/<h2 class="elementor-heading-title elementor-size-default">Share This Post<\/h2>/is', '', $content);

        // Sideload Images
        preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches);
        if (!empty($matches[1])) {
            foreach (array_unique($matches[1]) as $img_url) {
                if (strpos($img_url, 'cmrindia.com') !== false) {
                    $local_url = cmr_get_attachment_by_filename($img_url);
                    if (!$local_url) {
                        cmr_log("Downloading inline image: $img_url");
                        sleep(2);
                        
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
                            cmr_log("Failed to download inline image: " . $attachment_id->get_error_message());
                        }
                    }
                    if ($local_url) {
                        $content = str_replace($img_url, $local_url, $content);
                    }
                }
            }
        }

        $content = preg_replace('/ srcset=[\'"][^\'"]+[\'"]/i', '', $content);
        $content = preg_replace('/ sizes=[\'"][^\'"]+[\'"]/i', '', $content);
        $content = str_replace('https://cmrindia.com', 'https://qai8358l95-staging.onrocket.site', $content);

        // Figure out categories (if the page had any on the live site)
        $local_cats = array();
        if (isset($post['categories']) && is_array($post['categories'])) {
            foreach ($post['categories'] as $cat_id) {
                if (isset($state['category_map'][$cat_id])) {
                    $local_cats[] = (int)$state['category_map'][$cat_id];
                }
            }
        }
        
        if ($existing_id) {
            cmr_log("Updating existing page-as-post: " . $post['title']['rendered']);
            wp_update_post(array(
                'ID' => $existing_id,
                'post_author' => $author_id,
                'post_content' => $content
            ));
            
            if (!empty($local_cats)) wp_set_post_categories($existing_id, $local_cats);
            continue;
        }
        
        $post_data = array(
            'post_title'    => $post['title']['rendered'],
            'post_content'  => $content,
            'post_excerpt'  => isset($post['excerpt']['rendered']) ? $post['excerpt']['rendered'] : '',
            'post_status'   => 'publish',
            'post_author'   => $author_id,
            'post_type'     => 'post', // Save as post
            'post_date'     => $post['date'],
            'post_date_gmt' => $post['date_gmt'],
            'post_name'     => $post['slug']
        );
        
        $post_id = wp_insert_post($post_data);
        if (is_wp_error($post_id)) {
            cmr_log("Error inserting post: " . $post['title']['rendered']);
            continue;
        }
        
        if (!empty($local_cats)) wp_set_post_categories($post_id, $local_cats);
        
        // Featured Image
        if (isset($post['_embedded']['wp:featuredmedia'][0]['source_url'])) {
            $image_url = $post['_embedded']['wp:featuredmedia'][0]['source_url'];
            cmr_log("Sideloading image: $image_url");
            $image_id = media_sideload_image($image_url, $post_id, $post['title']['rendered'], 'id');
            if (!is_wp_error($image_id)) {
                set_post_thumbnail($post_id, $image_id);
            } else {
                cmr_log("Error sideloading image: " . $image_id->get_error_message());
            }
        }
        
        cmr_log("Imported page as post: " . $post['title']['rendered'] . " (ID: $post_id)");
    }
    
    $state['posts_page']++;
    file_put_contents($state_file, json_encode($state));
    $run_pages++;
}

cmr_log("Batch finished. To continue, reload this script. Current page is now " . $state['posts_page']);
echo "<script>
    setTimeout(function() {
        window.location.reload();
    }, 2000);
</script>";
