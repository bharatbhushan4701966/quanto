<?php
/**
 * Script to import posts, categories, tags, authors, and media from cmrindia.com.
 * Handles Category Hierarchy and Author mapping.
 */

$wp_load_paths = array(
    __DIR__ . '/../../../wp-load.php',
    __DIR__ . '/../../../../wp-load.php',
    __DIR__ . '/wp-load.php',
);
$wp_loaded = false;
foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $wp_loaded = true;
        break;
    }
}
if (!$wp_loaded) die("Error: Could not find wp-load.php");

require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

$source_domain = 'https://cmrindia.com';
$api_base = $source_domain . '/wp-json/wp/v2';
$log_file = __DIR__ . '/import-press-log.txt';
$state_file = __DIR__ . '/import-press-state.json';

function cmr_get_attachment_by_filename($url) {
    global $wpdb;
    $filename = basename($url);
    // Get the name without extension
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

cmr_log("Starting import script (v3)...");

$state = array(
    'categories_done' => false,
    'tags_done' => false,
    'posts_page' => 1,
    'category_map' => array(),
    'tag_map' => array(),
    'author_map' => array()
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

// 1. Import Categories with Hierarchy
if (!$state['categories_done']) {
    cmr_log("Importing categories...");
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
    
    // First pass: create all categories without parents
    foreach ($all_cats as $cat) {
        $term = term_exists($cat['name'], 'category');
        if (!$term) {
            $term = wp_insert_term($cat['name'], 'category', array(
                'description' => $cat['description'],
                'slug' => $cat['slug']
            ));
        }
        if (!is_wp_error($term) && isset($term['term_id'])) {
            $state['category_map'][$cat['id']] = $term['term_id'];
        }
    }
    
    // Second pass: assign parents
    foreach ($all_cats as $cat) {
        if ($cat['parent'] > 0 && isset($state['category_map'][$cat['parent']]) && isset($state['category_map'][$cat['id']])) {
            wp_update_term($state['category_map'][$cat['id']], 'category', array(
                'parent' => $state['category_map'][$cat['parent']]
            ));
        }
    }
    
    $state['categories_done'] = true;
    file_put_contents($state_file, json_encode($state));
    cmr_log("Categories imported.");
}

// 2. Import Tags
if (!$state['tags_done']) {
    cmr_log("Importing tags...");
    $page = 1;
    while (true) {
        $tags = fetch_api($api_base . "/tags?per_page=100&page=$page");
        if (empty($tags) || isset($tags['code'])) break;
        foreach ($tags as $tag) {
            $term = term_exists($tag['name'], 'post_tag');
            if (!$term) {
                $term = wp_insert_term($tag['name'], 'post_tag', array(
                    'description' => $tag['description'],
                    'slug' => $tag['slug']
                ));
            }
            if (!is_wp_error($term) && isset($term['term_id'])) {
                $state['tag_map'][$tag['id']] = $term['term_id'];
            }
        }
        $page++;
    }
    $state['tags_done'] = true;
    file_put_contents($state_file, json_encode($state));
    cmr_log("Tags imported.");
}

// Helper to map author
function get_local_author_id($remote_author, &$state) {
    if (!isset($remote_author['id'])) return 1;
    $remote_id = $remote_author['id'];
    
    if (isset($state['author_map'][$remote_id])) {
        return $state['author_map'][$remote_id];
    }
    
    $slug = $remote_author['slug'];
    $name = $remote_author['name'];
    $email = $slug . '@cmrindia.com';
    
    $user = get_user_by('slug', $slug);
    if (!$user) {
        $user_id = wp_insert_user(array(
            'user_login' => $slug,
            'user_pass'  => wp_generate_password(),
            'user_email' => $email,
            'display_name' => $name,
            'nickname'   => $name,
            'role'       => 'author'
        ));
        if (!is_wp_error($user_id)) {
            $state['author_map'][$remote_id] = $user_id;
            return $user_id;
        }
    } else {
        $state['author_map'][$remote_id] = $user->ID;
        return $user->ID;
    }
    return 1;
}

// 3. Import Posts
cmr_log("Importing posts from page " . $state['posts_page']);
$max_pages_per_run = 5; 

$run_pages = 0;
while ($run_pages < $max_pages_per_run) {
    $page = $state['posts_page'];
    $posts = fetch_api($api_base . "/posts?categories=1169&per_page=5&page=$page&_embed=1");
    
    if (empty($posts) || isset($posts['code'])) {
        cmr_log("No more posts found. Import complete.");
        if (file_exists($state_file)) unlink($state_file);
        break;
    }
    
    global $wpdb;

    foreach ($posts as $post) {
        $slug = $post['slug'];
        $existing_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = 'post' LIMIT 1", $slug));
        
        // Get Author
        $author_id = 1;
        if (isset($post['_embedded']['author'][0])) {
            $author_id = get_local_author_id($post['_embedded']['author'][0], $state);
        }
        
        $content = $post['content']['rendered'];
        // Fix Smush lazy loaded images securely: only remove dummy src if data-src exists
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

        // Remove unwanted Elementor blocks (Duplicated Post Title and Share This Post)
        $content = preg_replace('/<section[^>]*elementor-top-section[^>]*>.*?theme-post-title\.default.*?<\/section>/is', '', $content);
        $content = preg_replace('/<section[^>]*elementor-inner-section[^>]*>.*?Share This Post.*?<\/section>/is', '', $content);
        
        // Extra cleanup just in case the outer section tags were missed
        $content = preg_replace('/<h1 class="elementor-heading-title elementor-size-default">.*?<\/h1>/is', '', $content);
        $content = preg_replace('/<h2 class="elementor-heading-title elementor-size-default">Share This Post<\/h2>/is', '', $content);

        // Find all images in content and sideload them
        preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches);
        if (!empty($matches[1])) {
            foreach (array_unique($matches[1]) as $img_url) {
                if (strpos($img_url, 'cmrindia.com') !== false) {
                    $local_url = cmr_get_attachment_by_filename($img_url);
                    if (!$local_url) {
                        cmr_log("Downloading inline image: $img_url");
                        sleep(2); // Prevent 429 Too Many Requests
                        
                        // Temporarily bypass MIME type checks for sideloading
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

        // Remove srcset and sizes so it doesn't try to load missing thumbnails from live
        $content = preg_replace('/ srcset=[\'"][^\'"]+[\'"]/i', '', $content);
        $content = preg_replace('/ sizes=[\'"][^\'"]+[\'"]/i', '', $content);

        // Replace live domain with staging domain in content (for normal text links)
        $content = str_replace('https://cmrindia.com', 'https://qai8358l95-staging.onrocket.site', $content);

        if ($existing_id) {
            cmr_log("Updating existing post: " . $post['title']['rendered']);
            wp_update_post(array(
                'ID' => $existing_id,
                'post_author' => $author_id,
                'post_content' => $content
            ));
            
            // Map Categories to existing post to fix hierarchy issues
            $local_cats = array();
            if (isset($post['categories']) && is_array($post['categories'])) {
                foreach ($post['categories'] as $cat_id) {
                    if (isset($state['category_map'][$cat_id])) {
                        $local_cats[] = (int)$state['category_map'][$cat_id];
                    }
                }
            }
            if (!empty($local_cats)) wp_set_post_categories($existing_id, $local_cats);
            continue;
        }
        
        $post_data = array(
            'post_title'    => $post['title']['rendered'],
            'post_content'  => $content,
            'post_excerpt'  => $post['excerpt']['rendered'],
            'post_status'   => 'publish',
            'post_author'   => $author_id,
            'post_type'     => 'cmr_news', // Save directly as cmr_news
            'post_date'     => $post['date'],
            'post_date_gmt' => $post['date_gmt'],
            'post_name'     => $post['slug']
        );
        
        $post_id = wp_insert_post($post_data);
        if (is_wp_error($post_id)) {
            cmr_log("Error inserting post: " . $post['title']['rendered']);
            continue;
        }
        
        // Map Categories to cmr_news_category
        wp_set_object_terms($post_id, 'press-releases', 'cmr_news_category');
        $local_tags = array();
        if (isset($post['tags']) && is_array($post['tags'])) {
            foreach ($post['tags'] as $tag_id) {
                if (isset($state['tag_map'][$tag_id])) {
                    $local_tags[] = (int)$state['tag_map'][$tag_id];
                }
            }
        }
        if (!empty($local_tags)) wp_set_post_tags($post_id, $local_tags);
        
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
        
        cmr_log("Imported post: " . $post['title']['rendered'] . " (ID: $post_id)");
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
?>
