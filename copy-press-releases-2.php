<?php
require_once('../../../wp-load.php');

if (!current_user_can('manage_options') && !isset($_GET['force'])) {
    die('Not allowed');
}

$paged = isset($_GET['paged']) ? (int)$_GET['paged'] : 1;
$posts_per_page = 20;

// Find posts that belong to the "Press Releases" category
$args = array(
    'post_type'      => 'post',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'tax_query'      => array(
        array(
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => array('press-release-2', 'press-releases-2'),
        ),
    ),
);
$posts = get_posts($args);

if (empty($posts)) {
    echo "<h1>Done! All existing Press Releases have been copied and linked to CMR News.</h1>";
    exit;
}

echo "<h1>Copying Page $paged...</h1>";

$copied_count = 0;
foreach ($posts as $p) {
    // Check if already cloned
    $existing_clone_id = get_post_meta($p->ID, '_cmr_news_clone_id', true);
    if ($existing_clone_id && get_post_type($existing_clone_id) === 'cmr_news') {
        echo "Already copied: " . $p->post_title . "<br>";
        continue;
    }

    $clone_data = array(
        'post_title'   => $p->post_title,
        'post_content' => $p->post_content,
        'post_excerpt' => $p->post_excerpt,
        'post_status'  => $p->post_status,
        'post_author'  => $p->post_author,
        'post_type'    => 'cmr_news',
        'post_date'    => $p->post_date,
        'post_name'    => $p->post_name . '-cmrnews', // avoid slug clash just in case, though WP handles it
    );

    $clone_id = wp_insert_post($clone_data);
    if (!is_wp_error($clone_id)) {
        update_post_meta($p->ID, '_cmr_news_clone_id', $clone_id);
        update_post_meta($clone_id, '_original_post_id', $p->ID);

        // Sync thumbnail
        $thumbnail_id = get_post_thumbnail_id($p->ID);
        if ($thumbnail_id) {
            set_post_thumbnail($clone_id, $thumbnail_id);
        }

        // Set category
        wp_set_object_terms($clone_id, 'press-releases', 'cmr_news_category');

        $copied_count++;
        echo "Successfully Copied: " . $p->post_title . "<br>";
    } else {
        echo "Error copying: " . $p->post_title . "<br>";
    }
}

echo "<br>Finished page $paged. Copied $copied_count posts.<br>";
$next_page = $paged + 1;
echo "<script>setTimeout(function() { window.location.href = '?force=1&paged=$next_page'; }, 1000);</script>";
