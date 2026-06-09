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
            // Only apply sticky behavior to the intel-nav-bar inside cmr-industry-intel-section
            const navBars = document.querySelectorAll('.cmr-industry-intel-section .intel-nav-bar');
            navBars.forEach(navBar => {
                const section = navBar.parentElement;
                const placeholder = document.createElement('div');
                placeholder.className = 'intel-nav-placeholder';
                placeholder.style.height = '0px';
                placeholder.style.visibility = 'hidden';
                navBar.parentNode.insertBefore(placeholder, navBar);

                function updateSticky() {
                    const sectionRect = section.getBoundingClientRect();
                    const placeholderRect = placeholder.getBoundingClientRect();
                    const navHeight = navBar.offsetHeight || 60;
                    
                    let totalOffset = 0;
                    const wpAdminBar = document.getElementById('wpadminbar');
                    if (wpAdminBar && window.getComputedStyle(wpAdminBar).position === 'fixed') {
                        totalOffset = wpAdminBar.offsetHeight;
                    }

                    // Dynamically find the bottom of any sticky Elementor main header
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

                    if (placeholderRect.top <= totalOffset && sectionRect.bottom > (navHeight + totalOffset)) {
                        if (!navBar.classList.contains('intel-nav-fixed-js')) {
                            placeholder.style.height = navHeight + 'px';
                            const style = window.getComputedStyle(navBar);
                            placeholder.style.marginBottom = style.marginBottom;
                            
                            navBar.classList.add('intel-nav-fixed-js');
                            document.body.appendChild(navBar);
                        }
                        
                        if (sectionRect.bottom <= (navHeight + totalOffset)) {
                            navBar.style.top = (sectionRect.bottom - navHeight) + 'px';
                        } else {
                            navBar.style.top = totalOffset + 'px';
                        }
                    } else {
                        if (navBar.classList.contains('intel-nav-fixed-js')) {
                            navBar.classList.remove('intel-nav-fixed-js');
                            navBar.style.top = '';
                            placeholder.parentNode.insertBefore(navBar, placeholder);
                            placeholder.style.height = '0px';
                        }
                    }
                }

                window.addEventListener('scroll', updateSticky);
                window.addEventListener('resize', updateSticky);
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
