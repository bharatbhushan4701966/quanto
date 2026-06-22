<?php
/*
 * Foundation Scroll Component Shortcode
 * Shortcode: [cmr_foundation_scroll]
 */

add_shortcode('cmr_foundation_scroll', 'cmr_foundation_scroll_shortcode');

function cmr_foundation_scroll_shortcode($atts) {
    ob_start();
    ?>
    <style>
        .cmr-foundation-section {
            font-family: 'Instrument Sans', sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 20px;
            color: #111;
        }

        .cmr-foundation-title {
            font-size: 42px;
            font-weight: 600;
            line-height: 1.2;
            letter-spacing: -1px;
            max-width: 600px;
            margin-bottom: 60px;
        }

        .cmr-foundation-container {
            display: flex;
            gap: 60px;
            position: relative;
        }

        .cmr-foundation-left {
            width: 30%;
            position: sticky;
            top: 120px;
            align-self: flex-start;
            border-right: 1px solid #eaeaea;
        }

        .cmr-foundation-nav-item {
            font-size: 28px;
            font-weight: 600;
            color: #d1d1d1;
            margin-bottom: 30px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .cmr-foundation-nav-item.active {
            color: #4e2ecf; /* Purple color matching the image */
        }

        .cmr-foundation-right {
            width: 70%;
        }

        .cmr-foundation-block {
            min-height: 50vh;
            padding-bottom: 60px;
        }

        .cmr-foundation-block-title {
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 20px;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .cmr-foundation-block-desc {
            font-size: 16px;
            color: #444;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .cmr-foundation-container {
                flex-direction: column;
            }
            .cmr-foundation-left {
                width: 100%;
                position: static;
                border-right: none;
                border-bottom: 1px solid #eaeaea;
                padding-bottom: 20px;
                margin-bottom: 40px;
                display: flex;
                gap: 20px;
            }
            .cmr-foundation-nav-item {
                font-size: 20px;
                margin-bottom: 0;
            }
            .cmr-foundation-right {
                width: 100%;
            }
            .cmr-foundation-block {
                min-height: auto;
                padding-bottom: 40px;
            }
        }
    </style>

    <div class="cmr-foundation-section">
        <h2 class="cmr-foundation-title">The Foundation Behind Every Insight We Deliver</h2>
        
        <div class="cmr-foundation-container">
            <div class="cmr-foundation-left">
                <div class="cmr-foundation-nav-item active" data-target="cmr-purpose">Purpose</div>
                <div class="cmr-foundation-nav-item" data-target="cmr-vision">Vision</div>
                <div class="cmr-foundation-nav-item" data-target="cmr-mission">Mission</div>
            </div>
            
            <div class="cmr-foundation-right">
                <div id="cmr-purpose" class="cmr-foundation-block">
                    <p class="cmr-foundation-block-desc">We help organizations navigate change with confidence. By delivering data-driven intelligence and meaningful engagement, we enable leaders to identify opportunity, manage risk and make informed strategic decisions that drive sustainable growth.</p>
                </div>
                
                <div id="cmr-vision" class="cmr-foundation-block">
                    <p class="cmr-foundation-block-desc">We believe the future belongs to intelligent, interconnected ecosystems. As data, AI and emerging technologies reshape markets, organizations that act on real-time insights and predictive intelligence will lead. Our vision is to empower businesses of all sizes to unlock this advantage and build long-term, data-driven success.</p>
                </div>
                
                <div id="cmr-mission" class="cmr-foundation-block">
                    <p class="cmr-foundation-block-desc">We create the future by equipping leaders to act ahead of change. Through high-impact research, AI-powered analytics and strategic advisory, we anticipate market shifts, translate complex data into foresight and enable confident adoption, growth and differentiation. By combining intelligence with influence - through our thought leadership and executive forums, we turn insight into decisions that shape what comes next.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const blocks = document.querySelectorAll('.cmr-foundation-block');
            const navItems = document.querySelectorAll('.cmr-foundation-nav-item');
            
            if (!blocks.length || !navItems.length) return;

            const observerOptions = {
                root: null,
                rootMargin: '-50% 0px -50% 0px',
                threshold: 0
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const id = entry.target.id;
                        
                        // Remove active class from all
                        navItems.forEach(item => item.classList.remove('active'));
                        
                        // Add active class to corresponding nav item
                        const activeNav = document.querySelector(`.cmr-foundation-nav-item[data-target="${id}"]`);
                        if (activeNav) {
                            activeNav.classList.add('active');
                        }
                    }
                });
            }, observerOptions);

            blocks.forEach(block => observer.observe(block));

            // Add click functionality for smooth scrolling
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const targetBlock = document.getElementById(targetId);
                    if (targetBlock) {
                        // Account for potential fixed headers
                        const yOffset = -100; 
                        const y = targetBlock.getBoundingClientRect().top + window.pageYOffset + yOffset;
                        window.scrollTo({top: y, behavior: 'smooth'});
                    }
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
