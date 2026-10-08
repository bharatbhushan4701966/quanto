<?php
// Block direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// CMR Intro Tech Shortcode
function cmr_register_intro_tech_shortcode() {
    add_shortcode('cmr_intro_tech', 'cmr_intro_tech_shortcode');
}
add_action('init', 'cmr_register_intro_tech_shortcode');

function cmr_intro_tech_shortcode() {
    ob_start(); ?>
    <style>
        .cmr-intro-tech-section {
            font-family: 'Instrument Sans', sans-serif !important;
            font-weight: 400 !important;
            font-style: normal !important;
            font-size: 16px !important;
            line-height: 1.6 !important;
            letter-spacing: 0 !important;
            text-align: center !important;
            vertical-align: middle !important;
            background: #ffffff !important;
            color: #000000 !important;
            padding: 60px 40px !important;
            max-width: 1200px !important;
            margin: 0 auto !important;
        }

        .cmr-intro-tech-section p {
            font-family: inherit !important;
            font-size: inherit !important;
            color: inherit !important;
            line-height: inherit !important;
            font-weight: inherit !important;
            text-align: center !important;
            margin-bottom: 35px;
            margin-top: 0;
        }

        .cmr-intro-tech-section p:last-of-type {
            margin-bottom: 0;
        }

        .cmr-intro-tech-hidden-content {
            display: block !important;
            margin-top: 35px;
        }

        .cmr-intro-tech-read-more {
            display: none !important;
        }
        
        @media (max-width: 768px) {
            .cmr-intro-tech-section {
                font-size: 15px !important;
                line-height: 1.6 !important;
                padding: 30px 16px !important;
            }
            .cmr-intro-tech-section p {
                margin-bottom: 20px;
            }
            .cmr-intro-tech-hidden-content {
                margin-top: 20px;
            }
        }
    </style>
    <div class="cmr-intro-tech-section">
        <p>Technology markets are being reshaped by AI, cloud, connectivity, digital platforms, and changing customer expectations. </p>
        <p>Product lifecycles are shortening, innovation cycles are accelerating, and competition is intensifying across both consumer and enterprise markets. Success today depends not only on understanding where the market is today, but anticipating where it will move next.</p>
        <p>CMR helps technology companies, enterprises, investors, and ecosystem partners navigate this dynamic landscape through independent research, market intelligence, strategic advisory, and deep industry expertise. We translate complex market signals into actionable insights that enables organizations to identify emerging opportunities, validate strategic decisions, and accelerate sustainable growth.</p>
        
        <div class="cmr-intro-tech-hidden-content">
            <p>Our research spans the entire technology ecosystem, covering smartphones, PCs, wearables, smart devices, consumer electronics, semiconductors, AI, cloud, enterprise software, cybersecurity, digital infrastructure, telecom, 5G, IoT, and next-generation digital services. </p>
            <p> By combining quantitative research, qualitative insights, competitive intelligence, channel analysis, and end-user research, we provide a comprehensive view of evolving markets and customer needs.</p>
            <p>Whether you are launching a new product, entering a new market, refining your go-to-market strategy, strengthening competitive positioning, or evaluating the impact of emerging technologies, CMR equips you with the insights to make confident, evidence-based decisions.</p>
            <p><strong>From identifying market opportunities to shaping long-term growth strategies, CMR helps organizations transform intelligence into innovation, strategy into execution, and insight into lasting competitive advantage.</strong></p>
        </div>
    </div>
    <?php return ob_get_clean();
}
