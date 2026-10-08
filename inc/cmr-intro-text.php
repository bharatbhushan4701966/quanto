<?php
// Block direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// CMR Intro Text Shortcode
function cmr_register_intro_shortcode() {
    add_shortcode('cmr_intro', 'cmr_intro_text_shortcode');
}
add_action('init', 'cmr_register_intro_shortcode');

function cmr_intro_text_shortcode() {
    ob_start(); ?>
    <style>
        .cmr-intro-text-section {
            font-family: 'Instrument Sans', sans-serif !important;
            font-weight: 400 !important;
            font-style: normal !important;
            font-size: 16px !important;
            line-height: 1.6 !important;
            letter-spacing: 0 !important;
            vertical-align: middle !important;
            background: #ffffff !important;
            color: #000000 !important;
            padding: 60px 40px !important;
            max-width: 1200px !important;
            margin: 0 auto !important;
        }

        .cmr-intro-text-section p {
            font-family: inherit !important;
            font-size: inherit !important;
            color: inherit !important;
            line-height: inherit !important;
            font-weight: inherit !important;
            margin-bottom: 35px;
            margin-top: 0;
        }

        .cmr-intro-text-section p:last-of-type {
            margin-bottom: 0;
        }

        .cmr-intro-hidden-content {
            display: block !important;
            margin-top: 35px;
        }

        .cmr-intro-read-more {
            display: none !important;
        }
        
        @media (max-width: 768px) {
            .cmr-intro-text-section {
                font-size: 15px !important;
                line-height: 1.6 !important;
                padding: 30px 16px !important;
            }
            .cmr-intro-text-section p {
                margin-bottom: 20px;
            }
            .cmr-intro-hidden-content {
                margin-top: 20px;
            }
        }
    </style>
    <div class="cmr-intro-text-section">
        <p>The automotive industry is undergoing its most profound transformation in a century. Electrification, software-defined vehicles, connected ecosystems, autonomous technologies, and evolving consumer expectations are fundamentally reshaping how vehicles are designed, manufactured, sold, and experienced. At the same time, new business models, regulatory shifts, supply chain realignment, and the rise of intelligent mobility services are creating unprecedented opportunities and challenges.</p>
        
        <div class="cmr-intro-hidden-content">
            <p>CMR helps automotive and mobility leaders navigate this complexity with confidence. Combining independent research, market intelligence, strategic advisory, and deep industry expertise, we deliver the insights needed to anticipate market shifts, understand customer expectations, benchmark competitive performance, and identify emerging growth opportunities.</p>
            <p>Whether you are an OEM, Tier-1 supplier, technology provider, semiconductor company, charging infrastructure player, software platform, or mobility services provider, CMR equips you with the intelligence to make informed investments, refine market strategies, accelerate innovation, and stay ahead of an increasingly dynamic mobility ecosystem.</p>
            <p>Our research spans the entire mobility landscape, including electric vehicles (EVs), connected and software-defined vehicles, autonomous technologies, shared mobility, automotive AI, digital cockpits, telematics, charging infrastructure, batteries, mobility services, and the broader ACES transformation.</p>
            <p><strong>From understanding what's changing to defining what comes next, CMR helps you turn market intelligence into strategic advantage.</strong></p>
        </div>
    </div>
    <?php return ob_get_clean();
}
// Shortcode is already registered in functions.php, but this is the full replacement
