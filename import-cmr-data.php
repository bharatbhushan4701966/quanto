<?php
/**
 * Script to import posts, categories, tags, authors, and media from cmrindia.com.
 * Run via HTTP request to this file or via CLI.
 */

// Attempt to find wp-load.php
$wp_load_paths = array(
    __DIR__ . '/../../../wp-load.php', // Assuming this is in wp-content/themes/quanto/
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

if (!$wp_loaded) {
    die("Error: Could not find wp-load.php. Run this script from the WordPress root or theme root.");
}

// Require admin dependencies for media_sideload_image
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

$source_domain = 'https://cmrindia.com';
$api_base = $source_domain . '/wp-json/wp/v2';

$log_file = __DIR__ . '/import-log.txt';
$state_file = __DIR__ . '/import-state.json';

function cmr_log($message) {
    global $log_file;
    $date = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$date] $message\n", FILE_APPEND);
    echo $message . "<br>\n";
    flush();
}

cmr_log("Starting import script...");

// State Management
$state = array(
    'categories_done' => false,
    'tags_done' => false,
    'authors_done' => false,
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
    if (is_wp_error($response)) {
        cmr_log("API Error for $url: " . $response->get_error_message());
        return false;
    }
    
    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}

// 1. Import Categories
if (!$state['categories_done']) {
    cmr_log("Importing categories...");
    $page = 1;
    while (true) {
        $cats = fetch_api($api_base . "/categories?per_page=100&page=$page");
        if (empty($cats) || isset($cats['code'])) break;
        
        foreach ($cats as $cat) {
            $term = term_exists($cat['name'], 'category');
            if (!$term) {
                $term = wp_insert_term(
                    $cat['name'],
                    'category',
                    array(
                        'description' => $cat['description'],
                        'slug' => $cat['slug']
                    )
                );
            }
            if (!is_wp_error($term) && isset($term['term_id'])) {
                $state['category_map'][$cat['id']] = $term['term_id'];
            }
        }
        $page++;
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
                $term = wp_insert_term(
                    $tag['name'],
                    'post_tag',
                    array(
                        'description' => $tag['description'],
                        'slug' => $tag['slug']
                    )
                );
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

$default_author_id = 1;

// 3. Import Posts
cmr_log("Importing posts from page " . $state['posts_page']);
$max_pages_per_run = 1; // 1 page per run to ensure we don't timeout (20 posts + 20 images)

$run_pages = 0;
while ($run_pages < $max_pages_per_run) {
    $page = $state['posts_page'];
    $posts = fetch_api($api_base . "/posts?per_page=20&page=$page&_embed=1");
    
    if (empty($posts) || isset($posts['code'])) {
        cmr_log("No more posts found. Import complete.");
        if (file_exists($state_file)) {
            unlink($state_file);
        }
        break;
    }
    
    foreach ($posts as $post) {
        $existing = get_page_by_title($post['title']['rendered'], OBJECT, 'post');
        if ($existing) {
            cmr_log("Skipping existing post: " . $post['title']['rendered']);
            continue;
        }
        
        $post_data = array(
            'post_title'    => $post['title']['rendered'],
            'post_content'  => $post['content']['rendered'],
            'post_excerpt'  => $post['excerpt']['rendered'],
            'post_status'   => 'publish',
            'post_author'   => $default_author_id,
            'post_type'     => 'post',
            'post_date'     => $post['date'],
            'post_date_gmt' => $post['date_gmt'],
            'post_name'     => $post['slug']
        );
        
        $post_id = wp_insert_post($post_data);
        if (is_wp_error($post_id)) {
            cmr_log("Error inserting post: " . $post['title']['rendered']);
            continue;
        }
        
        // Map Categories
        $local_cats = array();
        if (isset($post['categories']) && is_array($post['categories'])) {
            foreach ($post['categories'] as $cat_id) {
                if (isset($state['category_map'][$cat_id])) {
                    $local_cats[] = (int)$state['category_map'][$cat_id];
                }
            }
        }
        if (!empty($local_cats)) wp_set_post_categories($post_id, $local_cats);
        
        // Map Tags
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
