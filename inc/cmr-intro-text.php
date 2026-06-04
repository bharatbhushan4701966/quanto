<?php
// Block direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// CMR Intro Text Shortcode
function cmr_intro_text_shortcode() {
    ob_start(); ?>
    <style>
        .cmr-intro-text-section {
            font-family: 'Instrument Sans', sans-serif;
            font-weight: 400;
            font-style: normal;
            font-size: 26px;
            line-height: 38px;
            letter-spacing: 0;
            vertical-align: middle;
            background: #0F0F0F;
            color: #ffffff;
            padding: 60px 40px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .cmr-intro-text-section p {
            margin-bottom: 35px;
            margin-top: 0;
        }

        .cmr-intro-text-section p:last-of-type {
            margin-bottom: 0;
        }

        .cmr-intro-read-more {
            text-align: center;
            margin-top: 40px;
        }

        .cmr-read-more-btn {
            font-size: 16px;
            font-weight: 600;
            color: #8B5CF6;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s ease;
        }

        .cmr-read-more-btn:hover {
            color: #a78bfa;
        }

        .cmr-read-more-btn svg {
            margin-top: 2px;
        }
        
        @media (max-width: 768px) {
            .cmr-intro-text-section {
                font-size: 20px;
                line-height: 30px;
                padding: 40px 20px;
            }
        }
    </style>
    <div class="cmr-intro-text-section">
        <p>The automotive industry isn't just evolving, it's being remade from the ground up. Electrification, connectivity, autonomy, shared mobility and sustainability (the 5 RACES) are no longer emerging concepts. They are here, accelerating simultaneously and demanding strategic clarity.</p>
        <p>Companies that treat these shifts as isolated product decisions will fall behind. Those that recognise them as organisation-wide transformation imperatives — reorienting around customer needs, deploying AI with purpose and rethinking everything from the C-suite to the factory floor will lead.</p>
        <p>The question is no longer whether to respond. It's where to differentiate and who to partner with.</p>
        <div class="cmr-intro-read-more">
            <a href="#" class="cmr-read-more-btn">Read More <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></a>
        </div>
    </div>
    <?php return ob_get_clean();
}
add_shortcode('cmr_intro', 'cmr_intro_text_shortcode');
