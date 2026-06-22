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
        .cmr-foundation-wrap, .cmr-foundation-wrap * { box-sizing: border-box; }

        .cmr-foundation-wrap {
            position: relative;
            background: #ffffff;
            color: #1a1a2e;
            overflow: visible;
        }

        .cmr-foundation-panel {
            width: 100%;
            height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            padding-top: 0;
            padding-bottom: 20px;
        }

        .cmr-foundation-inner {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 1200px;
            padding: 0 20px;
            margin: 0 auto;
        }

        .cmr-foundation-title {
            font-size: 42px;
            font-weight: 600;
            line-height: 1.2;
            letter-spacing: -1px;
            max-width: 600px;
            margin-bottom: 60px;
            font-family: 'Instrument Sans', sans-serif;
            color: #111;
        }

        .cmr-foundation-content {
            display: flex;
            align-items: flex-start;
            gap: 100px;
        }

        .cmr-foundation-left {
            flex: 0 0 240px;
        }

        .cmr-foundation-nav-item {
            font-size: 28px;
            font-weight: 600;
            color: #d1d1d1;
            margin-bottom: 30px;
            cursor: pointer;
            transition: color 0.4s ease;
            font-family: 'Instrument Sans', sans-serif;
        }

        .cmr-foundation-nav-item:hover,
        .cmr-foundation-nav-item.active {
            color: #4e2ecf; /* Purple color matching the image */
        }

        .cmr-foundation-right {
            flex: 1;
            position: relative;
        }

        .cmr-foundation-block {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 1.5s ease, transform 1.5s ease;
            pointer-events: none;
        }

        .cmr-foundation-block:first-child {
            position: relative;
        }

        .cmr-foundation-block.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .cmr-foundation-block-desc {
            font-size: 16px;
            color: #444;
            line-height: 1.6;
            font-family: 'Instrument Sans', sans-serif;
            margin: 0;
        }

        @media (max-width: 768px) {
            .cmr-foundation-wrap { height: auto !important; }
            .cmr-foundation-panel { position: relative !important; height: auto !important; padding: 40px 0; }
            .cmr-foundation-title { font-size: 28px; margin-bottom: 40px; }
            .cmr-foundation-content { flex-direction: column; gap: 24px; }
            .cmr-foundation-left { flex: none; width: 100%; display: flex; gap: 20px; overflow-x: auto; padding-bottom: 10px; border-bottom: 1px solid #eaeaea; margin-bottom: 20px; }
            .cmr-foundation-nav-item { font-size: 20px; margin-bottom: 0; white-space: nowrap; }
            .cmr-foundation-block { position: relative; opacity: 1; pointer-events: auto; transform: none; display: none; }
            .cmr-foundation-block.show { display: block; }
        }
    </style>

    <div class="cmr-foundation-wrap">
        <div class="cmr-foundation-panel">
            <div class="cmr-foundation-inner">
                <h2 class="cmr-foundation-title">The Foundation Behind Every Insight We Deliver</h2>
                
                <div class="cmr-foundation-content">
                    <div class="cmr-foundation-left">
                        <div class="cmr-foundation-nav-item active" data-index="0">Purpose</div>
                        <div class="cmr-foundation-nav-item" data-index="1">Vision</div>
                        <div class="cmr-foundation-nav-item" data-index="2">Mission</div>
                    </div>
                    
                    <div class="cmr-foundation-right">
                        <div class="cmr-foundation-block show" data-index="0">
                            <p class="cmr-foundation-block-desc">We help organizations navigate change with confidence. By delivering data-driven intelligence and meaningful engagement, we enable leaders to identify opportunity, manage risk and make informed strategic decisions that drive sustainable growth.</p>
                        </div>
                        
                        <div class="cmr-foundation-block" data-index="1">
                            <p class="cmr-foundation-block-desc">We believe the future belongs to intelligent, interconnected ecosystems. As data, AI and emerging technologies reshape markets, organizations that act on real-time insights and predictive intelligence will lead. Our vision is to empower businesses of all sizes to unlock this advantage and build long-term, data-driven success.</p>
                        </div>
                        
                        <div class="cmr-foundation-block" data-index="2">
                            <p class="cmr-foundation-block-desc">We create the future by equipping leaders to act ahead of change. Through high-impact research, AI-powered analytics and strategic advisory, we anticipate market shifts, translate complex data into foresight and enable confident adoption, growth and differentiation. By combining intelligence with influence - through our thought leadership and executive forums, we turn insight into decisions that shape what comes next.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Failsafe wait for GSAP
            function initFoundationScroll() {
                if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
                    setTimeout(initFoundationScroll, 50);
                    return;
                }
                
                // If on mobile, ignore GSAP pinning entirely for better UX
                if (window.innerWidth <= 768) {
                    initMobileFoundation();
                    return;
                }
                
                gsap.registerPlugin(ScrollTrigger);
                
                const wraps = document.querySelectorAll('.cmr-foundation-wrap');
                
                wraps.forEach(wrap => {
                    const panel = wrap.querySelector('.cmr-foundation-panel');
                    const blocks = wrap.querySelectorAll('.cmr-foundation-block');
                    const navItems = wrap.querySelectorAll('.cmr-foundation-nav-item');
                    const totalBlocks = blocks.length;
                    
                    if (totalBlocks <= 1) return;
                    
                    // Set scroll duration (amount of pinning)
                    const scrollDuration = totalBlocks * window.innerHeight * 0.8;
                    
                    ScrollTrigger.create({
                        trigger: wrap,
                        start: "top top+=80", // Account for sticky headers if any
                        end: "+=" + scrollDuration,
                        pin: panel,
                        scrub: true,
                        onUpdate: self => {
                            // Calculate current active slide index based on progress
                            let rawIndex = self.progress * totalBlocks;
                            let idx = Math.min(totalBlocks - 1, Math.floor(rawIndex));
                            
                            // Prevent precision issues at the very end
                            if (self.progress > 0.99) idx = totalBlocks - 1;
                            
                            // Update active classes
                            navItems.forEach((m, i) => m.classList.toggle("active", i === idx));
                            blocks.forEach((s, i) => s.classList.toggle("show", i === idx));
                        }
                    });
                    
                    // Click handlers for menu items to scroll smoothly to their respective points
                    navItems.forEach((item, i) => {
                        item.addEventListener('click', function() {
                            const st = ScrollTrigger.getAll().find(t => t.trigger === wrap);
                            if (st) {
                                const targetY = st.start + (i / totalBlocks) * (st.end - st.start) + 10;
                                window.scrollTo({ top: targetY, behavior: 'smooth' });
                            }
                        });
                    });
                });
            }
            
            function initMobileFoundation() {
                const wraps = document.querySelectorAll('.cmr-foundation-wrap');
                wraps.forEach(wrap => {
                    const blocks = wrap.querySelectorAll('.cmr-foundation-block');
                    const navItems = wrap.querySelectorAll('.cmr-foundation-nav-item');
                    
                    navItems.forEach((item, i) => {
                        item.addEventListener('click', function() {
                            navItems.forEach(m => m.classList.remove('active'));
                            blocks.forEach(s => s.classList.remove('show'));
                            
                            this.classList.add('active');
                            blocks[i].classList.add('show');
                        });
                    });
                });
            }
            
            initFoundationScroll();
        });
    </script>
    <?php
    return ob_get_clean();
}
