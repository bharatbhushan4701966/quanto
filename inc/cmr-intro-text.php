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
            display: none;
            margin-top: 35px;
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
            cursor: pointer;
        }

        .cmr-read-more-btn:hover {
            color: #a78bfa;
        }

        .cmr-read-more-btn svg {
            margin-top: 2px;
            transition: transform 0.3s ease;
        }

        .cmr-read-more-btn.active svg {
            transform: rotate(180deg);
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
            .cmr-intro-read-more {
                margin-top: 25px;
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

        <div class="cmr-intro-read-more">
            <a href="#" class="cmr-read-more-btn"><span>Read More</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var readMoreBtn = document.querySelector('.cmr-read-more-btn');
            var hiddenContent = document.querySelector('.cmr-intro-hidden-content');
            var btnText = readMoreBtn ? readMoreBtn.querySelector('span') : null;
            
            if(readMoreBtn && hiddenContent && btnText) {
                readMoreBtn.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevents the link from making the page jump to the top
                    
                    if (hiddenContent.style.display === 'block') {
                        hiddenContent.style.display = 'none';
                        btnText.textContent = 'Read More';
                        readMoreBtn.classList.remove('active');
                    } else {
                        hiddenContent.style.display = 'block';
                        btnText.textContent = 'Read Less';
                        readMoreBtn.classList.add('active');
                    }
                });
            }
        });
    </script>
    <?php return ob_get_clean();
}
// Shortcode is already registered in functions.php, but this is the full replacement
