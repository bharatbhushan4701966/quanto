<?php
// Block direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// CMR Intro Enterprise Tech Shortcode
function cmr_register_intro_enterprise_tech_shortcode() {
    add_shortcode('cmr_intro_enterprise_tech', 'cmr_intro_enterprise_tech_shortcode');
}
add_action('init', 'cmr_register_intro_enterprise_tech_shortcode');

function cmr_intro_enterprise_tech_shortcode() {
    ob_start(); ?>
    <style>
        .cmr-intro-enterprise-tech-section {
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

        .cmr-intro-enterprise-tech-section p {
            font-family: inherit !important;
            font-size: inherit !important;
            color: inherit !important;
            line-height: inherit !important;
            font-weight: inherit !important;
            margin-bottom: 35px;
            margin-top: 0;
        }

        .cmr-intro-enterprise-tech-section p:last-of-type {
            margin-bottom: 0;
        }

        .cmr-intro-enterprise-tech-hidden-content {
            display: none;
            margin-top: 35px;
        }

        .cmr-intro-enterprise-tech-read-more {
            text-align: center;
            margin-top: 40px;
        }

        .cmr-read-more-btn-enterprise-tech {
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

        .cmr-read-more-btn-enterprise-tech:hover {
            color: #a78bfa;
        }

        .cmr-read-more-btn-enterprise-tech svg {
            margin-top: 2px;
            transition: transform 0.3s ease;
        }

        .cmr-read-more-btn-enterprise-tech.active svg {
            transform: rotate(180deg);
        }
        
        @media (max-width: 768px) {
            .cmr-intro-enterprise-tech-section {
                font-size: 15px !important;
                line-height: 1.6 !important;
                padding: 30px 16px !important;
            }
            .cmr-intro-enterprise-tech-section p {
                margin-bottom: 20px;
            }
            .cmr-intro-enterprise-tech-hidden-content {
                margin-top: 20px;
            }
            .cmr-intro-enterprise-tech-read-more {
                margin-top: 25px;
            }
        }
    </style>
    <div class="cmr-intro-enterprise-tech-section">
        <p>Enterprises today are being reshaped by an accelerating wave of digital transformation. Cloud platforms, enterprise software, AI and automation, cybersecurity, data infrastructure, and modern workplace technologies are redefining how organizations operate, collaborate, and compete. IT leaders are under growing pressure to modernize legacy systems, secure complex environments, and deliver measurable business value from every technology investment.</p>
        <p>AI is accelerating this shift further, moving from experimentation to enterprise-wide deployment across operations, customer engagement, decision-making, and IT management. At the same time, hybrid work models, evolving compliance requirements, cost optimization pressures, and the need for resilient, scalable infrastructure are creating both opportunities and complexity for enterprise technology leaders.</p>
        
        <div class="cmr-intro-enterprise-tech-hidden-content">
            <p>CMR helps technology vendors, enterprises, IT leaders, and ecosystem partners navigate this complexity with confidence. Combining independent research, market intelligence, and strategic advisory with deep enterprise technology expertise, we deliver the insights needed to understand adoption trends, benchmark vendor and competitive performance, and identify where to invest for maximum impact.</p>
            <p>Our expertise spans the enterprise technology landscape, including enterprise cloud and SaaS, data center and IT infrastructure, enterprise cybersecurity and governance, AI and automation, enterprise mobility and unified communications, IT services and managed services, and the broader digital transformation of enterprises across India and key regional markets.</p>
            <p><strong>From infrastructure to applications, CMR helps enterprises navigate technology change and turn insight into competitive advantage.</strong></p>
        </div>

        <div class="cmr-intro-enterprise-tech-read-more">
            <a href="#" class="cmr-read-more-btn-enterprise-tech"><span>Read More</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var readMoreBtnEnterpriseTech = document.querySelector('.cmr-read-more-btn-enterprise-tech');
            var hiddenContentEnterpriseTech = document.querySelector('.cmr-intro-enterprise-tech-hidden-content');
            var btnTextEnterpriseTech = readMoreBtnEnterpriseTech ? readMoreBtnEnterpriseTech.querySelector('span') : null;
            
            if(readMoreBtnEnterpriseTech && hiddenContentEnterpriseTech && btnTextEnterpriseTech) {
                readMoreBtnEnterpriseTech.addEventListener('click', function(e) {
                    e.preventDefault(); 
                    
                    if (hiddenContentEnterpriseTech.style.display === 'block') {
                        hiddenContentEnterpriseTech.style.display = 'none';
                        btnTextEnterpriseTech.textContent = 'Read More';
                        readMoreBtnEnterpriseTech.classList.remove('active');
                    } else {
                        hiddenContentEnterpriseTech.style.display = 'block';
                        btnTextEnterpriseTech.textContent = 'Read Less';
                        readMoreBtnEnterpriseTech.classList.add('active');
                    }
                });
            }
        });
    </script>
    <?php return ob_get_clean();
}
