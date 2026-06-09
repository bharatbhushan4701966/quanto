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
        document.addEventListener('DOMContentLoaded', function() {
            const navBars = document.querySelectorAll('.intel-nav-bar');
            navBars.forEach(navBar => {
                const section = navBar.parentElement;
                const placeholder = document.createElement('div');
                placeholder.className = 'intel-nav-placeholder';
                placeholder.style.display = 'none';
                navBar.parentNode.insertBefore(placeholder, navBar);

                function updateSticky() {
                    const sectionRect = section.getBoundingClientRect();
                    const placeholderRect = placeholder.getBoundingClientRect();
                    const navHeight = navBar.offsetHeight || 60;
                    
                    let wpOffset = 0;
                    const wpAdminBar = document.getElementById('wpadminbar');
                    if (wpAdminBar && window.getComputedStyle(wpAdminBar).position === 'fixed') {
                        wpOffset = wpAdminBar.offsetHeight;
                    }

                    // Use placeholderRect.top to know when to stick, since placeholder stays in flow
                    if (placeholderRect.top <= wpOffset && sectionRect.bottom > (navHeight + wpOffset)) {
                        if (!navBar.classList.contains('intel-nav-fixed-js')) {
                            placeholder.style.height = navHeight + 'px';
                            placeholder.style.display = 'block';
                            const style = window.getComputedStyle(navBar);
                            placeholder.style.marginBottom = style.marginBottom;
                            
                            navBar.classList.add('intel-nav-fixed-js');
                            // Move to body to break out of z-index and overflow traps!
                            document.body.appendChild(navBar);
                        }
                        
                        if (sectionRect.bottom <= (navHeight + wpOffset)) {
                            navBar.style.top = (sectionRect.bottom - navHeight) + 'px';
                        } else {
                            navBar.style.top = wpOffset + 'px';
                        }
                    } else {
                        if (navBar.classList.contains('intel-nav-fixed-js')) {
                            navBar.classList.remove('intel-nav-fixed-js');
                            navBar.style.top = '';
                            // Move back to original location
                            placeholder.parentNode.insertBefore(navBar, placeholder);
                            placeholder.style.display = 'none';
                        }
                    }
                }

                window.addEventListener('scroll', updateSticky);
                window.addEventListener('resize', updateSticky);
                setTimeout(updateSticky, 100);
            });
        });
    }
    </script>
    <?php
});
