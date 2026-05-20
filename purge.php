<?php
// Load WordPress
$wp_load = $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
if ( file_exists( $wp_load ) ) {
    require_once( $wp_load );
} else {
    $dir = __DIR__;
    while ( ! file_exists( $dir . '/wp-load.php' ) && $dir !== dirname($dir) ) {
        $dir = dirname($dir);
    }
    if ( file_exists( $dir . '/wp-load.php' ) ) {
        require_once( $dir . '/wp-load.php' );
    }
}

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Could not load WordPress.' );
}

// Flush all WordPress cache
wp_cache_flush();

// Flush WP Rocket cache
if ( function_exists( 'rocket_clean_domain' ) ) {
    rocket_clean_domain();
    echo "WP Rocket cache cleared.\n";
}

// Flush LiteSpeed cache
if ( class_exists( 'LiteSpeed_Cache_API' ) ) {
    LiteSpeed_Cache_API::purge_all();
    echo "LiteSpeed cache cleared.\n";
}

echo "Cache flushed successfully.";
