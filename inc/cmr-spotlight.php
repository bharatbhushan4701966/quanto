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
                <img src="https://upload.wikimedia.org/wikipedia/commons/e/ee/The_times_of_india.svg" alt="TOI" style="max-height:40px;">
            </div>
            <div class="cmr-spotlight-cell r1-c5">
                <img src="https://upload.wikimedia.org/wikipedia/commons/e/e3/CNBC_logo.svg" alt="CNBC" style="max-height:50px;">
            </div>

            <!-- Row 2 Logos -->
            <div class="cmr-spotlight-cell r2-c1">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/62/NDTV_logo.svg" alt="NDTV" style="max-height:50px;">
            </div>
            <div class="cmr-spotlight-cell r2-c2">
                <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/ABP_News_logo.svg" alt="ABP News" style="max-height:35px;">
            </div>
            <div class="cmr-spotlight-cell r2-c3">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/62/BBC_News_2019.svg" alt="BBC News" style="max-height:40px;">
            </div>
            <div class="cmr-spotlight-cell r2-c4">
                <img src="https://upload.wikimedia.org/wikipedia/commons/e/e6/Aaj_tak_logo.png" alt="Aaj Tak" style="max-height:50px;">
            </div>
            <div class="cmr-spotlight-cell r2-c5">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b1/CNN.svg" alt="CNN" style="max-height:45px;">
            </div>
            <div class="cmr-spotlight-cell r2-c6">
                <img src="https://upload.wikimedia.org/wikipedia/commons/4/4c/India_Today_logo.svg" alt="India Today" style="max-height:45px;">
            </div>

            <!-- Row 3 Logos -->
            <div class="cmr-spotlight-cell r3-c2">
                <img src="https://upload.wikimedia.org/wikipedia/commons/e/ee/Zee_News_logo.svg" alt="Zee News" style="max-height:45px;">
            </div>
            <div class="cmr-spotlight-cell r3-c3">
                <img src="https://upload.wikimedia.org/wikipedia/commons/1/1a/Republic_Bharat_Logo.svg" alt="Republic Bharat" style="max-height:40px;">
            </div>

        </div>
    </div>
    <?php
    return ob_get_clean();
}
