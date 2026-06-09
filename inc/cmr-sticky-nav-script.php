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
        left: 0;
        width: 100%;
        z-index: 999999;
        background: #fff;
        padding-left: calc((100vw - 1280px) / 2) !important;
        padding-right: calc((100vw - 1280px) / 2) !important;
        margin-bottom: 0 !important;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        font-family: 'Instrument Sans', sans-serif !important;
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
        
        function initStickyNav() {
            const navBars = document.querySelectorAll('.cmr-industry-intel-section .intel-nav-bar');
            navBars.forEach(navBar => {
                const section = navBar.closest('.cmr-industry-intel-section');
                
                // Create a 0-height marker placeholder
                const placeholder = document.createElement('div');
                placeholder.className = 'intel-nav-placeholder';
                placeholder.style.height = '0px';
                placeholder.style.visibility = 'hidden';
                navBar.parentNode.insertBefore(placeholder, navBar);

                let originalNavHeight = navBar.offsetHeight || 60;
                let originalMarginBottom = window.getComputedStyle(navBar).marginBottom;

                function updateSticky() {
                    const sectionRect = section.getBoundingClientRect();
                    const placeholderRect = placeholder.getBoundingClientRect();
                    
                    let totalOffset = 0;
                    const wpAdminBar = document.getElementById('wpadminbar');
                    if (wpAdminBar && window.getComputedStyle(wpAdminBar).position === 'fixed') {
                        totalOffset = wpAdminBar.offsetHeight;
                    }

                    // Dynamically find sticky headers
                    const headers = document.querySelectorAll('header, [data-elementor-type="header"], .elementor-location-header, .elementor-sticky--active');
                    headers.forEach(h => {
                        if (h === navBar || h.contains(navBar)) return;
                        const hRect = h.getBoundingClientRect();
                        const hStyle = window.getComputedStyle(h);
                        if ((hStyle.position === 'fixed' || hStyle.position === 'sticky' || h.classList.contains('elementor-sticky--active')) && hRect.top <= totalOffset + 10) {
                            if (hRect.bottom > totalOffset && hRect.bottom < (window.innerHeight / 2)) {
                                totalOffset = hRect.bottom;
                            }
                        }
                    });

                    // Trigger sticky when placeholder hits the top offset
                    if (placeholderRect.top <= totalOffset && sectionRect.bottom > (originalNavHeight + totalOffset)) {
                        if (!navBar.classList.contains('intel-nav-fixed-js')) {
                            // Lock in the dimensions of the navBar into the placeholder before extracting it!
                            placeholder.style.height = originalNavHeight + 'px';
                            placeholder.style.marginBottom = originalMarginBottom;
                            
                            navBar.classList.add('intel-nav-fixed-js');
                            document.body.appendChild(navBar); // Move to body to escape Elementor transform context
                        }
                        
                        // Push up if section is scrolling away
                        if (sectionRect.bottom <= (originalNavHeight + totalOffset)) {
                            navBar.style.top = (sectionRect.bottom - originalNavHeight) + 'px';
                        } else {
                            navBar.style.top = totalOffset + 'px';
                        }
                    } else {
                        // Remove sticky
                        if (navBar.classList.contains('intel-nav-fixed-js')) {
                            navBar.classList.remove('intel-nav-fixed-js');
                            navBar.style.top = '';
                            
                            // Re-insert navBar immediately after the placeholder
                            placeholder.parentNode.insertBefore(navBar, placeholder.nextSibling);
                            
                            // Reset placeholder to 0-height marker
                            placeholder.style.height = '0px';
                            placeholder.style.marginBottom = '0px';
                        }
                    }
                }

                window.addEventListener('scroll', updateSticky, { passive: true });
                window.addEventListener('resize', updateSticky, { passive: true });
                setTimeout(updateSticky, 100);
                setTimeout(updateSticky, 1000); // Failsafe for late render
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
