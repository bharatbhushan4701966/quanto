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
                <img src="https://www.google.com/s2/favicons?domain=timesofindia.indiatimes.com&sz=128" alt="TOI" style="max-height:60px;">
            </div>
            <div class="cmr-spotlight-cell r1-c5">
                <img src="https://upload.wikimedia.org/wikipedia/commons/e/e3/CNBC_logo.svg" alt="CNBC" style="max-height:50px;">
            </div>

            <!-- Row 2 Logos -->
            <div class="cmr-spotlight-cell r2-c1">
                <img src="https://www.google.com/s2/favicons?domain=ndtv.com&sz=128" alt="NDTV" style="max-height:60px;">
            </div>
            <div class="cmr-spotlight-cell r2-c2">
                <img src="https://www.google.com/s2/favicons?domain=news.abplive.com&sz=128" alt="ABP News" style="max-height:60px;">
            </div>
            <div class="cmr-spotlight-cell r2-c3">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/62/BBC_News_2019.svg" alt="BBC News" style="max-height:40px;">
            </div>
            <div class="cmr-spotlight-cell r2-c4">
                <img src="https://www.google.com/s2/favicons?domain=aajtak.in&sz=128" alt="Aaj Tak" style="max-height:60px;">
            </div>
            <div class="cmr-spotlight-cell r2-c5">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b1/CNN.svg" alt="CNN" style="max-height:45px;">
            </div>
            <div class="cmr-spotlight-cell r2-c6">
                <img src="https://upload.wikimedia.org/wikipedia/commons/a/ab/India_Today_logo.png" alt="India Today" style="max-height:45px;">
            </div>

            <!-- Row 3 Logos -->
            <div class="cmr-spotlight-cell r3-c2">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/27/Zee_News_Logo_2025.svg/330px-Zee_News_Logo_2025.svg.png" alt="Zee News" style="max-height:45px;">
            </div>
            <div class="cmr-spotlight-cell r3-c3">
                <img src="https://www.google.com/s2/favicons?domain=republicbharat.com&sz=128" alt="Republic Bharat" style="max-height:60px;">
            </div>

        </div>
    </div>
    <?php
    return ob_get_clean();
}
