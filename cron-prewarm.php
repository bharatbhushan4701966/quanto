<?php
/**
 * Standalone Server Cron: Component CSS & Elementor Cache Pre-Warmer
 * Runs every 3 hours directly from server crontab, cPanel or webhook (Independent of GitHub CI/CD)
 *
 * Usage via cURL (cPanel Cron / Webhook):
 *   curl -s "https://cmrindia.com/wp-content/themes/quanto/cron-prewarm.php?key=cmr_secure_2026"
 *
 * Usage via PHP CLI:
 *   php /path/to/wp-content/themes/quanto/cron-prewarm.php key=cmr_secure_2026
 */

@set_time_limit( 300 );
@ini_set( 'memory_limit', '512M' );

// 1. Verify Security Key
$auth_key = 'cmr_secure_2026';
$provided_key = '';

if ( php_sapi_name() === 'cli' ) {
    if ( isset( $argv ) ) {
        foreach ( $argv as $arg ) {
            if ( strpos( $arg, 'key=' ) === 0 ) {
                $provided_key = substr( $arg, 4 );
            }
        }
    }
} else {
    $provided_key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
}

// 2. Load WordPress environment if available
$wp_load_paths = array(
    dirname( __FILE__ ) . '/../../../wp-load.php',
    dirname( __FILE__ ) . '/../../../../wp-load.php',
    isset( $_SERVER['DOCUMENT_ROOT'] ) ? $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' : '',
);

$wp_loaded = false;
foreach ( $wp_load_paths as $path ) {
    if ( ! empty( $path ) && file_exists( $path ) ) {
        require_once $path;
        $wp_loaded = true;
        break;
    }
}

// Check key or WordPress admin session
$is_authorized = ( $provided_key === $auth_key );
if ( ! $is_authorized && $wp_loaded && function_exists( 'current_user_can' ) && current_user_can( 'manage_options' ) ) {
    $is_authorized = true;
}

if ( ! $is_authorized ) {
    header( 'HTTP/1.1 403 Forbidden' );
    die( json_encode( array( 'error' => 'Unauthorized. Valid security key required.' ) ) );
}

if ( ! headers_sent() ) {
    header( 'Content-Type: application/json; charset=utf-8' );
}

$start_time = microtime( true );
$log_results = array();
$base_url = function_exists( 'home_url' ) ? home_url() : 'https://cmrindia.com';

// 3. Trigger WordPress Internal Elementor Cache Generator & DB Backup if WP is loaded
if ( function_exists( 'cmr_prewarm_elementor_caches' ) ) {
    cmr_prewarm_elementor_caches();
    $log_results['elementor_cache_regenerated'] = true;
}

// 4. Prewarm Component URLs
$urls = array(
    $base_url . '/?quanto_footer=main',
    $base_url . '/?quanto_tab_build=cmr-gtm',
    $base_url . '/?quanto_tab_build=cmr-connect',
    $base_url . '/?quanto_tab_build=global-cta-banner',
    $base_url . '/?quanto_tab_build=similar-reports-by-industry',
    $base_url . '/?quanto_tab_build=your-next-big-decision-deserves-better-intelligence',
    $base_url . '/?quanto_tab_build=we-worked-with-largest-global-brands',
    $base_url . '/?quanto_tab_build=testimonials',
    $base_url . '/?quanto_tab_build=fotter-card',
    $base_url . '/?quanto_tab_build=your-challenge-our-research-your-advantage',
    $base_url . '/?quanto_tab_build=who-we-serve',
    $base_url . '/?cmr_prewarm_now=1&key=' . $auth_key,
);

$user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';

$url_statuses = array();
foreach ( $urls as $url ) {
    $code = 0;
    if ( function_exists( 'curl_init' ) ) {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_USERAGENT, $user_agent );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 15 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_exec( $ch );
        $code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        curl_close( $ch );
    } elseif ( function_exists( 'wp_remote_get' ) ) {
        $res = wp_remote_get( $url, array(
            'timeout'   => 15,
            'sslverify' => false,
            'headers'   => array( 'User-Agent' => $user_agent ),
        ) );
        $code = wp_remote_retrieve_response_code( $res );
    } else {
        @file_get_contents( $url );
        $code = 200;
    }
    $url_statuses[$url] = $code;
}

$elapsed = round( microtime( true ) - $start_time, 2 );

// 5. Append Log Entry
$log_file = dirname( __FILE__ ) . '/prewarm-cron.log';
$log_entry = sprintf(
    "[%s] Pre-warm executed in %ss | URLs: %d | Statuses: %s\n",
    date( 'Y-m-d H:i:s' ),
    $elapsed,
    count( $url_statuses ),
    json_encode( $url_statuses )
);
@file_put_contents( $log_file, $log_entry, FILE_APPEND | LOCK_EX );

echo json_encode( array(
    'status'   => 'success',
    'message'  => 'Pre-warm cron executed successfully on server.',
    'elapsed'  => $elapsed . 's',
    'urls'     => count( $url_statuses ),
    'details'  => $url_statuses,
), JSON_PRETTY_PRINT );
