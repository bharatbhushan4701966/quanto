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
    </style>
    <script>
    if (!window.cmrStickyNavInitialized) {
        window.cmrStickyNavInitialized = true;
        
        function initStickyNav() {            // Sticky Nav logic for both Industry Intelligence and Latest Insights shortcodes
            const sections = document.querySelectorAll('.cmr-industry-intelligence, .cmr-latest-insights-section');
            sections.forEach(section => {
                const navBar = section.querySelector('.cmr-industry-nav-bar, .cmr-latest-nav-bar');
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
                        
                        // Special handling for Overview to scroll to top
                        if (linkText === 'overview' || href === '#top') {
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
                        
                        // Fallback for Elementor sections missing IDs (like #reports, #newsroom, etc.)
                        if (!targetElement) {
                            const headings = Array.from(document.querySelectorAll('h1, h2, h3, h4, h5, h6'));
                            let matchingHeading = null;
                            if (targetId === 'reports') {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('similar reports'));
                            } else if (targetId === 'cmr-market-updates') {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('market updates'));
                            } else if (targetId === 'newsroom') {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('newsroom'));
                            } else if (targetId === 'cmr-in-news') {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('cmr in news'));
                            }
                            
                            if (matchingHeading) {
                                targetElement = matchingHeading.closest('.elementor-section') || matchingHeading.closest('.e-con-parent') || matchingHeading.closest('.e-con-full') || matchingHeading.parentElement;
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

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initStickyNav);
        } else {
            initStickyNav();
        }
    }
    </script>
    <?php
});
