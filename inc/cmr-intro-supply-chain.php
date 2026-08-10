<?php
// Block direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// CMR Intro Supply Chain Shortcode
function cmr_register_intro_supply_chain_shortcode() {
    add_shortcode('cmr_intro_supply_chain', 'cmr_intro_supply_chain_shortcode');
}
add_action('init', 'cmr_register_intro_supply_chain_shortcode');

function cmr_intro_supply_chain_shortcode() {
    ob_start(); ?>
    <style>
        .cmr-intro-sc-section {
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

        .cmr-intro-sc-section p {
            font-family: inherit !important;
            font-size: inherit !important;
            color: inherit !important;
            line-height: inherit !important;
            font-weight: inherit !important;
            margin-bottom: 35px;
            margin-top: 0;
        }

        .cmr-intro-sc-section p:last-of-type {
            margin-bottom: 0;
        }

        .cmr-intro-sc-hidden-content {
            display: none;
            margin-top: 35px;
        }

        .cmr-intro-sc-read-more {
            text-align: center;
            margin-top: 40px;
        }

        .cmr-read-more-btn-sc {
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

        .cmr-read-more-btn-sc:hover {
            color: #a78bfa;
        }

        .cmr-read-more-btn-sc svg {
            margin-top: 2px;
            transition: transform 0.3s ease;
        }

        .cmr-read-more-btn-sc.active svg {
            transform: rotate(180deg);
        }
        
        @media (max-width: 768px) {
            .cmr-intro-sc-section {
                font-size: 15px !important;
                line-height: 1.6 !important;
                padding: 30px 16px !important;
            }
            .cmr-intro-sc-section p {
                margin-bottom: 20px;
            }
            .cmr-intro-sc-hidden-content {
                margin-top: 20px;
            }
            .cmr-intro-sc-read-more {
                margin-top: 25px;
            }
        }
    </style>
    <div class="cmr-intro-sc-section">
        <p>Supply Chains Are Being Reimagined. Every Decision Shapes Resilience.</p>
        <p>Build Smarter Supply Chains. Create Lasting Advantage.</p>
        <p>Global supply chains have evolved from operational functions into strategic growth engines. Rapid advances in AI, automation, robotics, IoT, cloud platforms, and predictive analytics are redefining how products are designed, sourced, manufactured, moved, and delivered. At the same time, geopolitical shifts, changing trade policies, sustainability mandates, and evolving customer expectations are compelling organizations to rethink resilience, agility, and operational efficiency.</p>
        <p>Success now depends on building supply chains that are intelligent, connected, data-driven, and adaptable.</p>
        
        <div class="cmr-intro-sc-hidden-content">
            <p>CMR helps organizations navigate this transformation through independent research, market intelligence, strategic advisory, and deep domain expertise. We provide decision-makers with a comprehensive understanding of market dynamics, technology adoption, competitive landscapes, customer expectations, and emerging investment opportunities across the digital supply chain ecosystem.</p>
            <p>Our research spans the technologies and platforms shaping next-generation supply chains, including AI-driven planning and forecasting, digital twins, Industrial IoT, smart manufacturing, robotics and automation, warehouse management, logistics technology, transportation visibility, procurement transformation, intelligent sourcing, cloud platforms, cybersecurity, and sustainability-led supply chain innovation.</p>
            <p>Whether you're a manufacturer modernizing operations, a technology provider developing digital supply chain solutions, a logistics company navigating industry disruption, or an enterprise strengthening operational resilience, CMR delivers the intelligence needed to evaluate market opportunities, understand buyer priorities, benchmark competitive performance, and make confident strategic decisions.</p>
            <p>Through quantitative and qualitative research, executive interviews, ecosystem analysis, customer and partner studies, competitive intelligence, and thought leadership, we help organizations identify growth opportunities, validate product strategies, strengthen go-to-market execution, and anticipate the trends shaping the future of global supply chains.</p>
            <p>From supply chain visibility to intelligent decision-making, CMR transforms market intelligence into strategic advantage, helping organizations build resilient, connected, and future-ready supply chains.</p>
        </div>

        <div class="cmr-intro-sc-read-more">
            <a href="#" class="cmr-read-more-btn-sc"><span>Read More</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var readMoreBtnSc = document.querySelector('.cmr-read-more-btn-sc');
            var hiddenContentSc = document.querySelector('.cmr-intro-sc-hidden-content');
            var btnTextSc = readMoreBtnSc ? readMoreBtnSc.querySelector('span') : null;
            
            if(readMoreBtnSc && hiddenContentSc && btnTextSc) {
                readMoreBtnSc.addEventListener('click', function(e) {
                    e.preventDefault(); 
                    
                    if (hiddenContentSc.style.display === 'block') {
                        hiddenContentSc.style.display = 'none';
                        btnTextSc.textContent = 'Read More';
                        readMoreBtnSc.classList.remove('active');
                    } else {
                        hiddenContentSc.style.display = 'block';
                        btnTextSc.textContent = 'Read Less';
                        readMoreBtnSc.classList.add('active');
                    }
                });
            }
        });
    </script>
    <?php return ob_get_clean();
}
