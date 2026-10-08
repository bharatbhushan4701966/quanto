<?php
// Block direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// CMR Intro IT & Telecom Shortcode
function cmr_register_intro_it_telecom_shortcode() {
    add_shortcode('cmr_intro_it_telecom', 'cmr_intro_connected_tech_shortcode');
}
add_action('init', 'cmr_register_intro_it_telecom_shortcode');

function cmr_intro_connected_tech_shortcode() {
    ob_start(); ?>
    <style>
        .cmr-intro-ct-section {
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

        .cmr-intro-ct-section p {
            font-family: inherit !important;
            font-size: inherit !important;
            color: inherit !important;
            line-height: inherit !important;
            font-weight: inherit !important;
            margin-bottom: 35px;
            margin-top: 0;
        }

        .cmr-intro-ct-section p:last-of-type {
            margin-bottom: 0;
        }

        .cmr-intro-ct-hidden-content {
            display: block !important;
            margin-top: 35px;
        }

        .cmr-intro-ct-read-more {
            display: none !important;
        }
        
        @media (max-width: 768px) {
            .cmr-intro-ct-section {
                font-size: 15px !important;
                line-height: 1.6 !important;
                padding: 30px 16px !important;
            }
            .cmr-intro-ct-section p {
                margin-bottom: 20px;
            }
            .cmr-intro-ct-hidden-content {
                margin-top: 20px;
            }
        }
    </style>
    <div class="cmr-intro-ct-section">
        <p>The technology ecosystem is evolving faster than ever. Smartphones, PCs, smart devices, connected experiences, intelligent networks, cloud platforms, and digital infrastructure are converging to reshape how consumers engage with technology and how enterprises operate.</p>
        <p>AI is accelerating this transformation, bringing intelligence closer to users through AI-enabled devices, networks, applications, and infrastructure. At the same time, changing consumer expectations, enterprise digitalization, evolving business models, and rapid innovation cycles are creating new opportunities and challenges across the technology landscape.</p>
        <p>CMR helps technology companies, enterprises, investors, and ecosystem partners navigate these shifts through independent research, market intelligence, consumer and enterprise insights, and strategic advisory. We help organizations understand market dynamics, track competitive movements, identify emerging opportunities, and make informed decisions in an increasingly connected world.</p>
        
        <div class="cmr-intro-ct-hidden-content">
            <p>Our expertise spans the connected technology ecosystem, including smartphones, PCs, tablets, wearables, smart devices, consumer electronics, enterprise devices, semiconductors, telecom networks, 5G and emerging 6G, broadband, IoT, cloud and edge computing, AI-enabled technologies, digital infrastructure, and connected digital services.</p>
            <p>Through market tracking, forecasting, competitive intelligence, consumer research, enterprise studies, technology adoption analysis, and ecosystem research, CMR provides a holistic view of technology markets and the forces shaping their evolution.</p>
            <p>Whether you are launching a new device, evaluating technology investments, developing connected solutions, entering new markets, or strengthening your competitive positioning, CMR delivers the intelligence you need to understand customers, anticipate market shifts, and make confident strategic decisions.</p>
            <p><strong>From devices to networks and infrastructure, CMR helps organizations understand the connected technology ecosystem and navigate what comes next.</strong></p>
        </div>
    </div>
    <?php return ob_get_clean();
}
