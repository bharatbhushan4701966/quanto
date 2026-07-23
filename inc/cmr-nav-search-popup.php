<?php
// Shortcodes for Nav Search Popup
add_shortcode('cmr_nav_search', 'cmr_nav_search_shortcode');
add_shortcode('cmr_nav_search_black', 'cmr_nav_search_black_shortcode');

function cmr_nav_search_black_shortcode() {
    return cmr_nav_search_shortcode(array('color' => 'black'));
}

function cmr_nav_search_shortcode($atts = array()) {
    static $overlay_rendered = false;
    
    $atts = shortcode_atts(array(
        'color' => 'white',
    ), $atts);
    
    $is_black = ($atts['color'] === 'black');
    $container_class = $is_black ? 'cmr-nav-search-black' : '';
    
    ob_start();
    
    if (!$overlay_rendered) {
        $overlay_rendered = true;
        // Output CSS only once
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
        
        .cmr-nav-search-container.cmr-nav-search-black {
            color: #333 !important; /* Force black */
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
            color: #6241ca;
        }
        
        /* Change to black when header is sticky */
        .elementor-sticky--effects .cmr-nav-search-container:not(.cmr-nav-search-black),
        .is-sticky .cmr-nav-search-container:not(.cmr-nav-search-black),
        header.sticky .cmr-nav-search-container:not(.cmr-nav-search-black),
        .intel-nav-fixed-js .cmr-nav-search-container:not(.cmr-nav-search-black) {
            color: #333;
        }

        .cmr-search-overlay-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 2147483647 !important;
            display: none;
        }
        
        /* Fix for WordPress Admin Bar */
        .admin-bar .cmr-search-overlay-wrapper {
            top: 32px;
            height: calc(100vh - 32px);
        }
        @media screen and (max-width: 782px) {
            .admin-bar .cmr-search-overlay-wrapper {
                top: 46px;
                height: calc(100vh - 46px);
            }
        }

        .cmr-search-overlay-wrapper.active {
            display: block;
        }

        .cmr-search-top-bar {
            width: 100%;
            background: #ffffff;
            padding: 25px 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .cmr-search-overlay-content {
            width: 100%;
            max-width: 1280px;
            display: flex;
            align-items: center;
            gap: 30px;
            justify-content: space-between;
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
            background: linear-gradient(90deg, #6241ca, #06b6d4);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        .cmr-custom-popup-form .submit-btn {
            background: #6241ca; /* Purple matching design */
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
        
        /* Hide native browser search cross icon */
        .cmr-custom-popup-form input[type="search"]::-webkit-search-decoration,
        .cmr-custom-popup-form input[type="search"]::-webkit-search-cancel-button,
        .cmr-custom-popup-form input[type="search"]::-webkit-search-results-button,
        .cmr-custom-popup-form input[type="search"]::-webkit-search-results-decoration {
            -webkit-appearance: none;
            display: none;
        }

        .cmr-search-overlay-close {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            width: 46px;
            height: 46px;
            cursor: pointer;
            color: #000;
            transition: all 0.3s ease;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .cmr-search-overlay-close:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .cmr-custom-popup-form .cat-icon {
            padding-left: 20px;
            display: flex;
            align-items: center;
            z-index: 2;
        }

        .cmr-custom-popup-form .clear-btn {
            background: transparent;
            border: none;
            color: #000;
            font-size: 18px;
            cursor: pointer;
            padding: 0 10px;
            display: flex;
            align-items: center;
            z-index: 2;
        }

        .cmr-custom-popup-form .results-badge {
            background: #ede9fe;
            color: #6241ca;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            margin: 0 10px;
            white-space: nowrap;
            z-index: 2;
        }
        </style>
        <?php
    } // End CSS
    ?>
    
    <div class="cmr-nav-search-container <?php echo esc_attr($container_class); ?>">
        <!-- The trigger icon -->
        <button type="button" class="cmr-nav-search-trigger" onclick="cmrOpenNavSearch()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                <path d="M20 20L17 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
    
    <?php
    if ($overlay_rendered === true) {
        $overlay_rendered = 'completed'; // Ensure JS/HTML is only output once
        ?>
        <!-- The Overlay -->
        <div id="cmr-search-overlay" class="cmr-search-overlay-wrapper">
            <div class="cmr-search-top-bar">
                <div class="cmr-search-overlay-content">

                    <button type="button" class="cmr-search-overlay-close" onclick="document.getElementById('cmr-search-overlay').classList.remove('active');">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 6L18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="cmr-custom-popup-form">
                        <div class="cat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="#6241ca" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2 17L12 22L22 17" stroke="#6241ca" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2 12L12 17L22 12" stroke="#6241ca" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <input name="s" required value="<?php echo esc_html( get_search_query() ); ?>" type="search" placeholder="Type here...">
                        
                        <button type="button" class="clear-btn" onclick="this.previousElementSibling.value=''; this.previousElementSibling.focus();">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 6L6 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6 6L18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        
                        <div class="results-badge">17 Results</div>

                        <button type="submit" class="submit-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                                <path d="M20 20L17 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <script>
        function cmrOpenNavSearch() {
            var overlay = document.getElementById('cmr-search-overlay');
            
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
                if (trigger.closest('.cmr-nav-search-black')) {
                    trigger.style.setProperty('color', '#333', 'important');
                    return;
                }
                
                if (window.scrollY > 50) {
                    trigger.style.setProperty('color', '#333', 'important');
                } else {
                    trigger.style.setProperty('color', '#fff', 'important');
                }
            });
        });

        // Close search overlay when clicking outside
        document.addEventListener('click', function(event) {
            var overlay = document.getElementById('cmr-search-overlay');
            var topBar = overlay.querySelector('.cmr-search-top-bar');
            
            if (overlay && overlay.classList.contains('active')) {
                // Check if the click is outside the top bar AND not on the trigger button
                if (topBar && !topBar.contains(event.target) && !event.target.closest('.cmr-nav-search-trigger')) {
                    overlay.classList.remove('active');
                }
            }
        });
        </script>
        <?php
    }
    
    return ob_get_clean();
}
