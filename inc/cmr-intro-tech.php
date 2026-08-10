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
            margin-bottom: 35px;
            margin-top: 0;
        }

        .cmr-intro-tech-section p:last-of-type {
            margin-bottom: 0;
        }

        .cmr-intro-tech-hidden-content {
            display: none;
            margin-top: 35px;
        }

        .cmr-intro-tech-read-more {
            text-align: center;
            margin-top: 40px;
        }

        .cmr-read-more-btn-tech {
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

        .cmr-read-more-btn-tech:hover {
            color: #a78bfa;
        }

        .cmr-read-more-btn-tech svg {
            margin-top: 2px;
            transition: transform 0.3s ease;
        }

        .cmr-read-more-btn-tech.active svg {
            transform: rotate(180deg);
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
            .cmr-intro-tech-read-more {
                margin-top: 25px;
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

        <div class="cmr-intro-tech-read-more">
            <a href="#" class="cmr-read-more-btn-tech"><span>Read More</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var readMoreBtnTech = document.querySelector('.cmr-read-more-btn-tech');
            var hiddenContentTech = document.querySelector('.cmr-intro-tech-hidden-content');
            var btnTextTech = readMoreBtnTech ? readMoreBtnTech.querySelector('span') : null;
            
            if(readMoreBtnTech && hiddenContentTech && btnTextTech) {
                readMoreBtnTech.addEventListener('click', function(e) {
                    e.preventDefault(); 
                    
                    if (hiddenContentTech.style.display === 'block') {
                        hiddenContentTech.style.display = 'none';
                        btnTextTech.textContent = 'Read More';
                        readMoreBtnTech.classList.remove('active');
                    } else {
                        hiddenContentTech.style.display = 'block';
                        btnTextTech.textContent = 'Read Less';
                        readMoreBtnTech.classList.add('active');
                    }
                });
            }
        });
    </script>
    <?php return ob_get_clean();
}
