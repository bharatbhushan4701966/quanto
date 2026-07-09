<?php
require_once('../../../wp-load.php');
if (!current_user_can('manage_options') && !isset($_GET['force'])) {
    die('Not allowed');
}

$paged = isset($_GET['paged']) ? (int)$_GET['paged'] : 1;
$posts_per_page = 50;

$posts = get_posts(array(
    'post_type' => 'post',
    'numberposts' => $posts_per_page,
    'paged' => $paged
));

if (empty($posts)) {
    echo "Done! All posts cleaned.";
    exit;
}

$updated_count = 0;
foreach ($posts as $p) {
    $content = $p->post_content;
    
    // Remove Elementor blocks
    $content = preg_replace('/<section[^>]*elementor-top-section[^>]*>.*?theme-post-title\.default.*?<\/section>/is', '', $content);
    $content = preg_replace('/<section[^>]*elementor-inner-section[^>]*>.*?Share This Post.*?<\/section>/is', '', $content);
    
    // Some posts might have <h1 class="elementor-heading-title elementor-size-default">
    $content = preg_replace('/<h1 class="elementor-heading-title elementor-size-default">.*?<\/h1>/is', '', $content);
    $content = preg_replace('/<h2 class="elementor-heading-title elementor-size-default">Share This Post<\/h2>/is', '', $content);

    if ($content !== $p->post_content) {
        wp_update_post(array(
            'ID' => $p->ID,
            'post_content' => $content
        ));
        $updated_count++;
    }
}

echo "Cleaned $updated_count posts on page $paged.<br>";
$next_page = $paged + 1;
echo "<script>setTimeout(function() { window.location.href = '?force=1&paged=$next_page'; }, 1000);</script>";
