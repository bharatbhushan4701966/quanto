<?php
require_once('wp-load.php');

echo "Test Page Check:\n";
$target_page = get_page_by_path( 'similar-reports-by-industry' );
if ( ! $target_page ) {
    $target_page = get_page_by_path( 'test' );
}
if($target_page) {
    echo "Found similar reports page ID: " . $target_page->ID . "\n";
} else {
    echo "Missing similar reports page!\n";
}

echo "Home Page Check:\n";
$home_page = get_page_by_path( 'home' );
if($home_page) {
    echo "Found home page ID: " . $home_page->ID . "\n";
} else {
    $front = get_option('page_on_front');
    if($front) {
        echo "Found front page via option ID: " . $front . "\n";
    } else {
        echo "Missing home page!\n";
    }
}
