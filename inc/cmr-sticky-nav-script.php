<?php
/**
 * Sticky Navigation Script for intel-nav-bar
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action('wp_footer', function() {
    ?>
    <style>
    .intel-nav-fixed-js {
        position: fixed !important;
        left: 50% !important;
        transform: translateX(-50%);
        width: 100%;
        max-width: 1280px;
        z-index: 999999;
        background: transparent !important;
        padding-left: 20px !important;
        padding-right: 20px !important;
        margin-bottom: 0 !important;
        font-family: 'Instrument Sans', sans-serif !important;
        box-sizing: border-box !important;
    }
    .intel-nav-fixed-js::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100vw;
        height: 100%;
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        z-index: -1;
    }
    .intel-nav-fixed-js .cmr-nav-btn-subscribe {
        display: inline-flex !important;
    }
    @media (max-width: 1320px) {
        .intel-nav-fixed-js {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }
    }
    @media (max-width: 768px) {
        .intel-nav-fixed-js {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
        .intel-nav-fixed-js .intel-nav-title {
            display: none !important;
        }
        .intel-nav-fixed-js .intel-nav-links {
            overflow-x: auto !important;
            white-space: nowrap !important;
            width: 100% !important;
            gap: 15px !important;
            padding-bottom: 5px !important;
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: none !important;
        }
        .intel-nav-fixed-js .intel-nav-links::-webkit-scrollbar {
            display: none !important;
        }
        .intel-nav-fixed-js .cmr-nav-btn-subscribe {
            display: none !important;
        }
    }
    </style>
    <script>
    if (!window.cmrStickyNavInitialized) {
        window.cmrStickyNavInitialized = true;
        
        function initStickyNav() {            // Sticky Nav logic for both Industry Intelligence and Latest Insights shortcodes
            const sections = document.querySelectorAll('.cmr-industry-intelligence, .cmr-latest-insights-section, .cmr-industry-intel-section, .cmr-marketing-services-section, .cmr-consulting-advisory-section, .cmr-enterprisecgd-wrapper, .cmr-channelcgd-wrapper, .cmr-smbcgd-wrapper, .cmr-mrg-wrapper');
            sections.forEach(section => {
                const navBar = section.querySelector('.cmr-industry-nav-bar, .cmr-latest-nav-bar, .intel-nav-bar');
                if (!navBar) return;
                
                const placeholder = document.createElement('div');
                placeholder.className = 'cmr-nav-placeholder';
                placeholder.style.height = '0px';
                placeholder.style.marginBottom = '0px';
                navBar.parentNode.insertBefore(placeholder, navBar);
                
                function updateSticky() {
                    const sectionRect = section.getBoundingClientRect();
                    let stickyOffset = 0;
                    const wpAdminBar = document.getElementById('wpadminbar');
                    if (wpAdminBar && window.getComputedStyle(wpAdminBar).position === 'fixed') {
                        stickyOffset = wpAdminBar.offsetHeight;
                    }
                    const headers = document.querySelectorAll('header, [data-elementor-type="header"], .elementor-location-header, .elementor-sticky--active');
                    headers.forEach(h => {
                        if (h === navBar || h.contains(navBar)) return;
                        const hStyle = window.getComputedStyle(h);
                        if (hStyle.position === 'fixed' || hStyle.position === 'sticky' || h.classList.contains('elementor-sticky--active')) {
                            const hRect = h.getBoundingClientRect();
                            if (hRect.top <= stickyOffset + 10 && hRect.bottom > stickyOffset && hRect.bottom < (window.innerHeight / 2)) {
                                stickyOffset = hRect.bottom;
                            }
                        }
                    });

                    let boundaryBottom = sectionRect.bottom;
                    
                    let testimonialsSection = document.getElementById('cmr-testimonials-section') || 
                                              document.getElementById('testimonials') || 
                                              document.querySelector('.elementor-element-82ef444') ||
                                              document.querySelector('.elementor-widget-testimonial-carousel') ||
                                              document.querySelector('.elementor-widget-testimonial');
                                              
                    if (!testimonialsSection) {
                        const headings = Array.from(document.querySelectorAll('h1, h2, h3, h4, h5, h6')).filter(h => h.textContent.toLowerCase().includes('testimonial'));
                        if (headings.length > 0) {
                            testimonialsSection = headings[0].closest('.elementor-section') || headings[0].closest('section') || headings[0].parentElement;
                        }
                    }

                    if (testimonialsSection) {
                        boundaryBottom = testimonialsSection.getBoundingClientRect().top;
                    } else {
                        const footer = document.querySelector('footer, .elementor-location-footer');
                        if (footer) {
                            boundaryBottom = footer.getBoundingClientRect().top;
                        }
                    }

                    if (sectionRect.top <= stickyOffset && boundaryBottom > (navBar.offsetHeight + stickyOffset)) {
                        if (!navBar.classList.contains('intel-nav-fixed-js')) {
                            placeholder.style.height = navBar.offsetHeight + 'px';
                            const style = window.getComputedStyle(navBar);
                            placeholder.style.marginBottom = style.marginBottom;
                            navBar.classList.add('intel-nav-fixed-js');
                            document.body.appendChild(navBar); 
                            const subscribeBtn = navBar.querySelector('.cmr-nav-btn-subscribe');
                            if (subscribeBtn) subscribeBtn.style.setProperty('display', 'flex', 'important');
                        }
                        
                        if (boundaryBottom <= (navBar.offsetHeight + stickyOffset)) {
                            navBar.style.top = (boundaryBottom - navBar.offsetHeight) + 'px';
                        } else {
                            navBar.style.top = stickyOffset + 'px';
                        }
                    } else {
                        if (navBar.classList.contains('intel-nav-fixed-js')) {
                            navBar.classList.remove('intel-nav-fixed-js');
                            navBar.style.top = '';
                            placeholder.parentNode.insertBefore(navBar, placeholder.nextSibling);
                            placeholder.style.height = '0px';
                            placeholder.style.marginBottom = '0px';
                            const subscribeBtn = navBar.querySelector('.cmr-nav-btn-subscribe');
                            if (subscribeBtn) subscribeBtn.style.setProperty('display', 'none', 'important');
                        }
                    }
                }
                
                window.addEventListener('scroll', updateSticky, { passive: true });
                window.addEventListener('resize', updateSticky, { passive: true });
                setTimeout(updateSticky, 100);
                setTimeout(updateSticky, 1000); // Failsafe for late render
                
                // Add smooth scrolling for anchor links inside this navBar
                const links = navBar.querySelectorAll('.intel-nav-links a');
                links.forEach(link => {
                    link.addEventListener('click', function(e) {
                        const href = this.getAttribute('href');
                        const linkText = this.innerText.toLowerCase().trim();
                        
                        // Special handling for Overview / Featured to scroll to top
                        if (linkText === 'overview' || linkText === 'featured' || href === '#top') {
                            e.preventDefault();
                            e.stopPropagation(); // Prevent Elementor from hijacking
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                            return;
                        }
                        
                        // For other links, check if they have a hash and point to the current page
                        if (!href) return;
                        
                        const hashIndex = href.indexOf('#');
                        if (hashIndex === -1) return;
                        
                        // If it's a full URL, check if the path matches the current page
                        if (hashIndex > 0) {
                            const linkUrl = new URL(href, window.location.href);
                            if (linkUrl.pathname !== window.location.pathname) {
                                return; // Different page, let the browser handle it
                            }
                        }
                        
                        const targetId = href.substring(hashIndex + 1);
                        if (!targetId) return;
                        
                        let targetElement = document.getElementById(targetId);
                        
                        // Fallback 1: Try known shortcode wrapper selectors directly
                        if (!targetElement) {
                            const selectorMap = {
                                'reports': '.cmr-latest-section, .cmr-featured-reports-section, [id^="reports-"]',
                                'cmr-in-news': '.cmr-media-coverage-wrapper, .cmr-mc-wrapper, [class*="cmr-dmc-"]',
                                'cmr-market-updates': '.cmr-mui-section, .cmr-market-updates-section',
                                'featured': '.cmr-cancg-section, .cmr-enterprisecg-section, .cmr-smbcg-section, .cmr-featured-insight-section',
                                'latest': '.cmr-channelcgd-wrapper, .cmr-enterprisecgd-wrapper, .cmr-smbcgd-wrapper'
                            };

                            // Try exact match first, then partial match
                            if (selectorMap[targetId]) {
                                targetElement = document.querySelector(selectorMap[targetId]);
                            }
                            if (!targetElement) {
                                for (const [key, selector] of Object.entries(selectorMap)) {
                                    if (targetId.includes(key) || key.includes(targetId)) {
                                        targetElement = document.querySelector(selector);
                                        if (targetElement) break;
                                    }
                                }
                            }
                        }

                        // Fallback 2: Search by heading text for Elementor sections missing IDs
                        if (!targetElement) {
                            const headings = Array.from(document.querySelectorAll('h1, h2, h3, h4, h5, h6, .elementor-heading-title'));
                            let matchingHeading = null;
                            
                            if (targetId.includes('report')) {
                                matchingHeading = headings.find(h => {
                                    const txt = h.textContent.toLowerCase().trim();
                                    return (txt.includes('reports') || txt.includes('featured reports') || txt.includes('latest reports'))
                                        && !h.closest('.intel-nav-bar') && !h.closest('[class*="-nav"]');
                                });
                            } else if (targetId.includes('insight')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('insight'));
                            } else if (targetId.includes('market-update') || targetId.includes('market')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('market updates') || h.textContent.toLowerCase().includes('updates'));
                            } else if (targetId.includes('featured')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('featured'));
                            } else if (targetId.includes('latest')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('latest'));
                            } else if (targetId.includes('media-resource')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('media resource'));
                            } else if (targetId.includes('media-contact')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('media contact') || h.textContent.toLowerCase().includes('contact us'));
                            } else if (targetId.includes('newsroom') || targetId.includes('news')) {
                                matchingHeading = headings.find(h => {
                                    const txt = h.textContent.toLowerCase().trim();
                                    return (txt.includes('media coverage') || txt.includes('cmr in news') || txt.includes('cmr media coverage') || txt.includes('newsroom') || txt.includes('cmr live'))
                                        && !h.closest('.intel-nav-bar') && !h.closest('[class*="-nav"]');
                                });
                            } else if (targetId.includes('explore')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('explore industry intelligence') || h.textContent.toLowerCase().includes('intelligence'));
                            } else if (targetId === 'cmr-footer-card-section' || targetId.includes('subscribe')) {
                                // Search for headings or widgets containing "CMR Connect" or "Subscribe Now"
                                const possibleCards = Array.from(document.querySelectorAll('h1, h2, h3, h4, h5, h6, .elementor-heading-title, .elementor-button-text'));
                                matchingHeading = possibleCards.find(el => {
                                    const txt = el.textContent.toLowerCase();
                                    return txt.includes('cmr connect') || txt.includes('monthly digest') || (txt.includes('subscribe now') && !el.closest('.intel-nav-bar'));
                                });
                            }
                            
                            if (matchingHeading) {
                                targetElement = matchingHeading.closest('.elementor-section') || matchingHeading.closest('.e-con-parent') || matchingHeading.closest('.e-con-full') || matchingHeading.closest('.cmr-latest-section') || matchingHeading.closest('.cmr-media-coverage-wrapper') || matchingHeading.closest('.cmr-mc-wrapper') || matchingHeading.closest('[class*="cmr-"]') || matchingHeading.parentElement;
                            }
                        }

                        if (targetElement) {
                            e.preventDefault();
                            e.stopPropagation(); // Prevent Elementor from hijacking
                            
                            // Re-calculate the total sticky offset dynamically
                            let stickyOffset = 0;
                            const wpAdminBar = document.getElementById('wpadminbar');
                            if (wpAdminBar && window.getComputedStyle(wpAdminBar).position === 'fixed') {
                                stickyOffset = wpAdminBar.offsetHeight;
                            }
                            const headers = document.querySelectorAll('header, [data-elementor-type="header"], .elementor-location-header, .elementor-sticky--active');
                            headers.forEach(h => {
                                if (h === navBar || h.contains(navBar)) return;
                                const hStyle = window.getComputedStyle(h);
                                if (hStyle.position === 'fixed' || hStyle.position === 'sticky' || h.classList.contains('elementor-sticky--active')) {
                                    const hRect = h.getBoundingClientRect();
                                    if (hRect.top <= stickyOffset + 10 && hRect.bottom > stickyOffset && hRect.bottom < (window.innerHeight / 2)) {
                                        stickyOffset = hRect.bottom;
                                    }
                                }
                            });
                            
                            // The nav bar itself will be sticky, so we add its height to the offset
                            const navHeight = navBar.offsetHeight || 60;
                            const finalOffset = stickyOffset + navHeight + 20; // 20px breathing room
                            
                            const targetPosition = targetElement.getBoundingClientRect().top + window.scrollY;
                            
                            window.scrollTo({
                                top: targetPosition - finalOffset,
                                behavior: 'smooth'
                            });
                        }
                    }, true); // Use capture phase to beat Elementor's native scroll
                });
            });
        }

        // Dynamic Anchor Script: assign IDs to Elementor sections based on headings
        // so sticky navbar anchor links work regardless of page builder setup
        function assignDynamicAnchors() {
            var allHeadings = document.querySelectorAll('h1, h2, h3, h4, h5, h6, .elementor-heading-title');
            
            allHeadings.forEach(function(h) {
                // Skip headings inside nav bars
                if (h.closest('.intel-nav-bar') || h.closest('[class*="-nav-bar"]')) return;
                
                var text = h.innerText.toLowerCase().trim();
                var section = h.closest('.elementor-top-section') || h.closest('section') || h.closest('.elementor-section') || h.closest('.e-con') || h.closest('.e-con-parent') || h.closest('.e-con-full');
                
                if (!section) return;
                // Don't overwrite existing IDs
                if (section.id) return;
                
                // "CMR in news" section
                if (text.includes("recognition of cmr in news") || text.includes("featured media coverage") || text === "cmr in news" || text.includes("cmr media coverage") || text === "media coverage") {
                    section.id = 'cmr-in-news';
                }
                
                // "Reports" section
                if (text.includes("similar reports") || text.includes("browse latest reports") || text.includes("featured reports") || text === "reports" || text.includes("latest reports")) {
                    if (!section.id) section.id = 'reports';
                }
                
                // "Market Updates" section
                if (text.includes("market intelligence &") || text.includes("market updates")) {
                    if (!section.id) section.id = 'cmr-market-updates';
                }
                
                // "Explore Industry Intelligence" section
                if (text.includes("explore industry intelligence") || text.includes("explore our industry intelligence")) {
                    if (!section.id) section.id = 'explore-industry-intelligence';
                }
                
                // "Featured Intelligence" / "Newsroom" section
                if (text.includes("featured intelligence") || (text.includes("media releases") && !text.includes("featured media")) || text === "newsroom") {
                    if (!section.id || (section.id !== 'cmr-in-news')) section.id = 'newsroom';
                }
                
                // "Insights" / "Latest Insights" section
                if (text.includes("latest insights") && !section.id) {
                    section.id = 'overview';
                }
                
                // "Trends" section
                if (text.includes("industry intelligence trends") && !section.id) {
                    section.id = 'trends';
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                assignDynamicAnchors();
                initStickyNav();
            });
        } else {
            assignDynamicAnchors();
            initStickyNav();
        }
    }
    </script>
    <?php
});
