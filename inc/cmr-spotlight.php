<?php
/**
 * CMR Spotlight Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'cmr_spotlight', 'cmr_render_spotlight_shortcode' );

function cmr_render_spotlight_shortcode( $atts ) {
    // Enqueue styles
    wp_enqueue_style( 'cmr-spotlight-style', get_template_directory_uri() . '/assets/css/cmr-spotlight.css', array(), time() );

    ob_start();
    ?>
    <div class="cmr-spotlight-wrapper">
        <div class="cmr-spotlight-grid">
            
            <!-- Title -->
            <div class="cmr-spotlight-title-cell">
                <h2>CMR in the Spotlight</h2>
            </div>

            <!-- Row 1 Logos -->
            <div class="cmr-spotlight-cell r1-c4">
                <img src="https://en.wikipedia.org/wiki/Special:FilePath/The_times_of_india.svg?width=320" alt="TOI" style="max-height:50px;">
            </div>
            <div class="cmr-spotlight-cell r1-c5">
                <img src="https://en.wikipedia.org/wiki/Special:FilePath/CNBC_logo.svg?width=320" alt="CNBC" style="max-height:50px;">
            </div>

            <!-- Row 2 Logos -->
            <div class="cmr-spotlight-cell r2-c1">
                <img src="https://en.wikipedia.org/wiki/Special:FilePath/NDTV_logo.svg?width=320" alt="NDTV" style="max-height:60px;">
            </div>
            <div class="cmr-spotlight-cell r2-c2">
                <img src="https://en.wikipedia.org/wiki/Special:FilePath/ABP_News_logo.svg?width=320" alt="ABP News" style="max-height:60px;">
            </div>
            <div class="cmr-spotlight-cell r2-c3">
                <img src="https://en.wikipedia.org/wiki/Special:FilePath/BBC_News_2019.svg?width=320" alt="BBC News" style="max-height:40px;">
            </div>
            <div class="cmr-spotlight-cell r2-c4">
                <img src="https://en.wikipedia.org/wiki/Special:FilePath/Aaj_tak_logo.png?width=320" alt="Aaj Tak" style="max-height:60px;">
            </div>
            <div class="cmr-spotlight-cell r2-c5">
                <img src="https://en.wikipedia.org/wiki/Special:FilePath/CNN.svg?width=320" alt="CNN" style="max-height:45px;">
            </div>
            <div class="cmr-spotlight-cell r2-c6">
                <img src="https://en.wikipedia.org/wiki/Special:FilePath/India_Today_logo.png?width=320" alt="India Today" style="max-height:45px;">
            </div>

            <!-- Row 3 Logos -->
            <div class="cmr-spotlight-cell r3-c2">
                <img src="https://en.wikipedia.org/wiki/Special:FilePath/Zee_News_Logo_2025.svg?width=320" alt="Zee News" style="max-height:45px;">
            </div>
            <div class="cmr-spotlight-cell r3-c3">
                <img src="https://en.wikipedia.org/wiki/Special:FilePath/Republic_Bharat_Logo.jpg?width=320" alt="Republic Bharat" style="max-height:60px;">
            </div>

        </div>
    </div>
    <?php
    return ob_get_clean();
}
