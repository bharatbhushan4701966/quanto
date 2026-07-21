<?php
// Shortcode for Nav Search Popup
add_shortcode('cmr_nav_search', 'cmr_nav_search_shortcode');
function cmr_nav_search_shortcode() {
    ob_start();
    ?>
    <style>
    .cmr-nav-search-container {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 5px;
        color: #fff; /* White on load */
    }
    
    .cmr-nav-search-trigger {
        background: transparent;
        border: none;
        color: inherit;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
        transition: color 0.3s ease;
    }
    
    .cmr-nav-search-trigger:hover {
        color: #7c3aed;
    }
    
    /* Change to black when header is sticky */
    .elementor-sticky--effects .cmr-nav-search-container,
    .is-sticky .cmr-nav-search-container,
    header.sticky .cmr-nav-search-container,
    .intel-nav-fixed-js .cmr-nav-search-container {
        color: #333;
    }

    .cmr-search-overlay-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        z-index: 99999999;
        display: none;
    }
    
    /* Fix for WordPress Admin Bar */
    .admin-bar .cmr-search-overlay-wrapper {
        top: 32px;
    }
    @media screen and (max-width: 782px) {
        .admin-bar .cmr-search-overlay-wrapper {
            top: 46px;
        }
    }

    .cmr-search-overlay-wrapper.active {
        display: block;
    }

    .cmr-search-top-bar {
        width: 100%;
        background: #ffffff;
        padding: 0 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .cmr-search-overlay-content {
        width: 100%;
        max-width: 1280px;
        display: flex;
        align-items: center;
        gap: 30px;
        justify-content: space-between;
    }
    
    .cmr-search-logo {
        flex-shrink: 0;
        display: flex;
        align-items: center;
    }
    .cmr-search-logo img {
        height: 35px; /* Adjust based on their actual logo proportions */
        width: auto;
    }

    /* Form Styles matching the screenshot */
    .cmr-custom-popup-form {
        display: flex;
        align-items: center;
        flex-grow: 1;
        background: #fff;
        border-radius: 50px;
        padding: 4px;
        position: relative;
        /* Gradient Border Trick */
        background-clip: padding-box;
        border: 1px solid transparent;
    }
    
    .cmr-custom-popup-form::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 50px;
        padding: 1.5px; /* border thickness */
        background: linear-gradient(90deg, #6b21a8, #06b6d4);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }

    .cmr-custom-popup-form .submit-btn {
        background: #6b21a8; /* Purple matching design */
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 16px;
        flex-shrink: 0;
        margin-left: 2px;
        z-index: 2;
    }
    
    .cmr-custom-popup-form input {
        border: none;
        outline: none;
        flex-grow: 1;
        padding: 10px 15px;
        font-size: 16px;
        background: transparent;
        box-shadow: none;
        color: #333;
        z-index: 2;
    }
    .cmr-custom-popup-form input:focus {
        box-shadow: none;
        outline: none;
    }

    .cmr-search-overlay-close {
        background: transparent;
        border: none;
        cursor: pointer;
        color: #666;
        transition: all 0.3s ease;
        flex-shrink: 0;
        padding: 0 15px;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cmr-search-overlay-close:hover {
        color: #000;
    }
    </style>
    
    <div class="cmr-nav-search-container">
        <!-- The trigger icon -->
        <button type="button" class="cmr-nav-search-trigger" onclick="cmrOpenNavSearch()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                <path d="M20 20L17 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
    
    <!-- The Overlay -->
    <div id="cmr-search-overlay" class="cmr-search-overlay-wrapper">
        <div class="cmr-search-top-bar">
            <div class="cmr-search-overlay-content">
                
                <div class="cmr-search-logo">
                    <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/cmrheaderlogo.svg" alt="CMR">
                </div>
                
                <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="cmr-custom-popup-form">
                    <button type="submit" class="submit-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2.5"/>
                            <path d="M20 20L17 17" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <input name="s" required value="<?php echo esc_html( get_search_query() ); ?>" type="search" placeholder="Search...">
                    
                    <button type="button" class="cmr-search-overlay-close" onclick="document.getElementById('cmr-search-overlay').classList.remove('active');">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 6L18 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </form>

            </div>
        </div>
    </div>

    <script>
    function cmrOpenNavSearch() {
        var overlay = document.getElementById('cmr-search-overlay');
        
        // Dynamically get the height of the current header
        var header = document.querySelector('header') || document.querySelector('.elementor-location-header') || document.getElementById('intel-nav-bar');
        var headerHeight = header ? header.offsetHeight : 80;
        
        var topBar = overlay.querySelector('.cmr-search-top-bar');
        if (topBar) {
            topBar.style.height = headerHeight + 'px';
        }

        // Move overlay to body to prevent stacking context z-index issues
        if (overlay.parentNode !== document.body) {
            document.body.appendChild(overlay);
        }
        overlay.classList.add('active');
        
        // Focus input
        setTimeout(function() {
            var input = overlay.querySelector('.cmr-custom-popup-form input[name="s"]');
            if (input) input.focus();
        }, 100);
    }

    // Change icon color on scroll (fallback if sticky classes are different)
    window.addEventListener('scroll', function() {
        var triggers = document.querySelectorAll('.cmr-nav-search-trigger');
        triggers.forEach(function(trigger) {
            if (window.scrollY > 50) {
                trigger.style.setProperty('color', '#333', 'important');
            } else {
                trigger.style.setProperty('color', '#fff', 'important');
            }
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
