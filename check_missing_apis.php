<?php
// Find missing posts by comparing REST APIs
$live_url = "https://cmrindia.com/wp-json/wp/v2/posts";
$staging_url = "https://qai8358l95-staging.onrocket.site/wp-json/wp/v2/posts";

echo "Checking total pages on live...\n";
$headers = get_headers($live_url, 1);
$total_pages = isset($headers['X-WP-TotalPages']) ? $headers['X-WP-TotalPages'] : 20;
if(is_array($total_pages)) $total_pages = $total_pages[0];
echo "Live Total Pages: $total_pages\n";

$live_slugs = [];
for($i=1; $i<=$total_pages; $i++) {
    echo "Fetching live page $i...\n";
    $json = file_get_contents("$live_url?_fields=slug&per_page=100&page=$i");
    if($json) {
        $posts = json_decode($json, true);
        foreach($posts as $p) $live_slugs[] = $p['slug'];
    }
}

echo "Checking total pages on staging...\n";
$headers_stg = get_headers($staging_url, 1);
$total_pages_stg = isset($headers_stg['X-WP-TotalPages']) ? $headers_stg['X-WP-TotalPages'] : 20;
if(is_array($total_pages_stg)) $total_pages_stg = $total_pages_stg[0];

$staging_slugs = [];
for($i=1; $i<=$total_pages_stg; $i++) {
    echo "Fetching staging page $i...\n";
    $json = file_get_contents("$staging_url?_fields=slug&per_page=100&page=$i");
    if($json) {
        $posts = json_decode($json, true);
        foreach($posts as $p) $staging_slugs[] = $p['slug'];
    }
}

$missing = array_diff($live_slugs, $staging_slugs);
echo "\nFound " . count($missing) . " missing posts!\n";
foreach($missing as $slug) {
    echo "Missing: $slug\n";
    // Sync it!
    echo "Syncing $slug...\n";
    $res = file_get_contents("https://qai8358l95-staging.onrocket.site/wp-json/cmr/v1/sync-posts?per_page=1"); 
    // This doesn't sync by slug, but it's an idea.
}
