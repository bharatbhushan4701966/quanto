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
                font-size: 16px !important;
                line-height: 1.6 !important;
                padding: 40px 20px !important;
            }
        }
    </style>
    <div class="cmr-intro-tech-section">
        <p>Consumer technology is no longer defined by the device — it's defined by the experience surrounding it. AI integration, ecosystem lock-in and shifting ownership models are rewriting the rules of how people choose, use and stay loyal to technology brands.</p>
        <p>Companies still competing on specs alone are losing ground to those competing on context — understanding not just what consumers buy, but why, when and what they expect next.</p>
        <p>The winners won't just ship better products. They'll build smarter relationships with the people who use them.</p>
        
        <div class="cmr-intro-tech-hidden-content">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
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
