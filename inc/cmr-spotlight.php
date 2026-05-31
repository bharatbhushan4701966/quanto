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
                <img src="https://via.placeholder.com/100x60?text=TOI" alt="TOI">
            </div>
            <div class="cmr-spotlight-cell r1-c5">
                <img src="https://via.placeholder.com/100x60?text=CNBC" alt="CNBC">
            </div>

            <!-- Row 2 Logos -->
            <div class="cmr-spotlight-cell r2-c1">
                <img src="https://via.placeholder.com/100x60?text=NDTV" alt="NDTV">
            </div>
            <div class="cmr-spotlight-cell r2-c2">
                <img src="https://via.placeholder.com/100x60?text=abp" alt="ABP News">
            </div>
            <div class="cmr-spotlight-cell r2-c3">
                <img src="https://via.placeholder.com/100x60?text=BBC" alt="BBC News">
            </div>
            <div class="cmr-spotlight-cell r2-c4">
                <img src="https://via.placeholder.com/100x60?text=Aaj+Tak" alt="Aaj Tak">
            </div>
            <div class="cmr-spotlight-cell r2-c5">
                <img src="https://via.placeholder.com/100x60?text=CNN" alt="CNN">
            </div>
            <div class="cmr-spotlight-cell r2-c6">
                <img src="https://via.placeholder.com/100x60?text=India+Today" alt="India Today">
            </div>

            <!-- Row 3 Logos -->
            <div class="cmr-spotlight-cell r3-c2">
                <img src="https://via.placeholder.com/100x60?text=Zee+News" alt="Zee News">
            </div>
            <div class="cmr-spotlight-cell r3-c3">
                <img src="https://via.placeholder.com/100x60?text=R.Bharat" alt="Republic Bharat">
            </div>

        </div>
    </div>
    <?php
    return ob_get_clean();
}
