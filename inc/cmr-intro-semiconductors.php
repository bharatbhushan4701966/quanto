<?php
// Block direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// CMR Intro Semiconductors Shortcode
function cmr_register_intro_semiconductors_shortcode() {
    add_shortcode('cmr_intro_semiconductors', 'cmr_intro_semiconductors_shortcode');
}
add_action('init', 'cmr_register_intro_semiconductors_shortcode');

function cmr_intro_semiconductors_shortcode() {
    ob_start(); ?>
    <style>
        .cmr-intro-semi-section {
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

        .cmr-intro-semi-section p {
            font-family: inherit !important;
            font-size: inherit !important;
            color: inherit !important;
            line-height: inherit !important;
            font-weight: inherit !important;
            margin-bottom: 35px;
            margin-top: 0;
        }

        .cmr-intro-semi-section p:last-of-type {
            margin-bottom: 0;
        }

        .cmr-intro-semi-hidden-content {
            display: block !important;
            margin-top: 35px;
        }

        .cmr-intro-semi-read-more {
            display: none !important;
        }
        
        @media (max-width: 768px) {
            .cmr-intro-semi-section {
                font-size: 15px !important;
                line-height: 1.6 !important;
                padding: 30px 16px !important;
            }
            .cmr-intro-semi-section p {
                margin-bottom: 20px;
            }
            .cmr-intro-semi-hidden-content {
                margin-top: 20px;
            }
        }
    </style>
    <div class="cmr-intro-semi-section">
        <p>Semiconductors have become the foundation of the digital economy. From AI and cloud computing to smartphones, automotive, industrial automation, telecommunications, and edge intelligence, every major technology transformation is powered by advances in silicon. As demand accelerates, the industry faces new opportunities alongside increasing complexity, driven by geopolitical realignment, supply chain resilience, manufacturing investments, technology leadership, and evolving customer requirements.</p>
        <p>CMR helps semiconductor companies, technology providers, enterprises, investors, and ecosystem partners navigate this rapidly evolving landscape through independent research, market intelligence, and strategic advisory. We transform complex market dynamics into actionable intelligence that enables organizations to identify growth opportunities, evaluate emerging technologies, understand customer demand, and make confident strategic decisions.</p>
        
        <div class="cmr-intro-semi-hidden-content">
            <p>Our research spans the entire semiconductor value chain, including chip design, foundries, packaging and testing, memory, processors, AI accelerators, connectivity chipsets, automotive semiconductors, edge AI, industrial electronics, embedded systems, and the broader semiconductor supply ecosystem. By combining quantitative research, ecosystem intelligence, competitive analysis, customer insights, and executive advisory, we provide a comprehensive view of market opportunities and technology evolution.</p>
            <p>Whether you're expanding into new markets, launching next-generation silicon, strengthening ecosystem partnerships, evaluating manufacturing investments, or navigating the impact of AI and geopolitical shifts, CMR delivers the intelligence to reduce uncertainty, sharpen strategy, and accelerate growth.</p>
            <p><strong>From silicon innovation to market adoption, CMR helps organizations transform semiconductor intelligence into strategic advantage.</strong></p>
        </div>
    </div>
    <?php return ob_get_clean();
}
