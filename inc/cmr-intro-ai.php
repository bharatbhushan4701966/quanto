<?php
// Block direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// CMR Intro AI Shortcode
function cmr_register_intro_ai_shortcode() {
    add_shortcode('cmr_intro_ai', 'cmr_intro_ai_shortcode');
}
add_action('init', 'cmr_register_intro_ai_shortcode');

function cmr_intro_ai_shortcode() {
    ob_start(); ?>
    <style>
        .cmr-intro-ai-section {
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

        .cmr-intro-ai-section p {
            font-family: inherit !important;
            font-size: inherit !important;
            color: inherit !important;
            line-height: inherit !important;
            font-weight: inherit !important;
            text-align: center !important;
            margin-bottom: 35px;
            margin-top: 0;
        }

        .cmr-intro-ai-section p:last-of-type {
            margin-bottom: 0;
        }

        .cmr-intro-ai-hidden-content {
            display: block !important;
            margin-top: 35px;
        }

        .cmr-intro-ai-read-more {
            display: none !important;
        }
        
        @media (max-width: 768px) {
            .cmr-intro-ai-section {
                font-size: 15px !important;
                line-height: 1.6 !important;
                padding: 30px 16px !important;
            }
            .cmr-intro-ai-section p {
                margin-bottom: 20px;
            }
            .cmr-intro-ai-hidden-content {
                margin-top: 20px;
            }
        }
    </style>
    <div class="cmr-intro-ai-section">
        <p>Artificial Intelligence is reshaping every industry at unprecedented speed. Generative AI, agentic systems, enterprise automation, edge AI, and domain-specific models are transforming how organisations operate, compete and create value. At the same time, new business models, evolving regulations, talent shifts, infrastructure demands and rising expectations around trust and governance are creating both significant opportunities and complex challenges.</p>
        <p>CMR helps technology and business leaders navigate this complexity with confidence. Combining independent research, market intelligence, strategic advisory and deep industry expertise, we deliver the insights needed to anticipate market shifts, understand adoption patterns, benchmark competitive performance and identify emerging growth opportunities.</p>
        
        <div class="cmr-intro-ai-hidden-content">
            <p>Whether you are an enterprise adopting AI, a technology vendor, semiconductor company, cloud provider, startup, investor or systems integrator, CMR equips you with the intelligence to make informed investments, refine market strategies, accelerate innovation and stay ahead in a rapidly evolving AI ecosystem.</p>
            <p>Our research spans the full AI landscape, including generative AI, agentic AI, enterprise AI platforms, AI infrastructure, edge and on-device AI, industry-specific applications, AI governance, talent and skills, and the broader impact of AI across sectors.</p>
            <p><strong>From experimentation to enterprise-wide deployment, CMR helps you turn AI insight into competitive advantage.</strong></p>
        </div>
    </div>
    <?php return ob_get_clean();
}
