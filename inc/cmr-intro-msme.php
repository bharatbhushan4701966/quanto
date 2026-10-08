<?php
// Block direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// CMR Intro MSME Shortcode
function cmr_register_intro_msme_shortcode() {
    add_shortcode('cmr_intro_msme', 'cmr_intro_msme_shortcode');
}
add_action('init', 'cmr_register_intro_msme_shortcode');

function cmr_intro_msme_shortcode() {
    ob_start(); ?>
    <style>
        .cmr-intro-msme-section {
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

        .cmr-intro-msme-section p {
            font-family: inherit !important;
            font-size: inherit !important;
            color: inherit !important;
            line-height: inherit !important;
            font-weight: inherit !important;
            margin-bottom: 35px;
            margin-top: 0;
        }

        .cmr-intro-msme-section p:last-of-type {
            margin-bottom: 0;
        }

        .cmr-intro-msme-hidden-content {
            display: block !important;
            margin-top: 35px;
        }

        .cmr-intro-msme-read-more {
            display: none !important;
        }
        
        @media (max-width: 768px) {
            .cmr-intro-msme-section {
                font-size: 15px !important;
                line-height: 1.6 !important;
                padding: 30px 16px !important;
            }
            .cmr-intro-msme-section p {
                margin-bottom: 20px;
            }
            .cmr-intro-msme-hidden-content {
                margin-top: 20px;
            }
        }
    </style>
    <div class="cmr-intro-msme-section">
        <p>India's Micro, Small and Medium Enterprises (MSME) sector is undergoing rapid digital transformation. Cloud adoption, AI-powered tools, digital payments, e-commerce enablement, and modern workplace technologies are reshaping how micro, small, and medium enterprises operate, compete, and grow. At the same time, cost pressures, talent constraints, cybersecurity risks, and the demand for simpler, more affordable solutions are creating both opportunities and challenges for technology providers and business leaders.</p>
        <p>CMR helps technology vendors, service providers, and ecosystem partners navigate this complexity with confidence. Combining independent research, market intelligence, and strategic advisory with a deep understanding of the MSME landscape, we deliver the insights needed to anticipate demand shifts, decode buying behaviour, and identify high-potential growth opportunities.</p>
        
        <div class="cmr-intro-msme-hidden-content">
            <p>Whether you are a software vendor, hardware brand, telecom operator, cloud provider, fintech, channel partner, or enterprise serving the MSME segment, CMR equips you to prioritise the right segments, sharpen go-to-market strategies, accelerate adoption, and stay ahead in a fast-evolving market.</p>
            <p>Our research spans the full MSME technology landscape &mdash; cloud and SaaS, productivity and collaboration tools, affordable and right-sized cybersecurity, digital payments, e-commerce enablement, AI adoption, channel dynamics, and the broader digitisation of micro, small, and medium enterprises across India and key regional markets.</p>
            <p><strong>From adoption to scale, CMR helps you decode the MSME opportunity and turn it into market advantage.</strong></p>
        </div>
    </div>
    <?php return ob_get_clean();
}
