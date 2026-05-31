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
                <img src="data:image/png;base64,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" alt="TOI" style="max-height:50px;">
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
