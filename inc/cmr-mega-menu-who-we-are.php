<?php
/* Mega Menu Shortcode: [cmr_mega_menu_who_we_are] */

add_shortcode('cmr_mega_menu_who_we_are', 'cmr_mega_menu_who_we_are_shortcode');

function cmr_mega_menu_who_we_are_shortcode($atts) {
    ob_start();
    ?>
    <style>
        .cmr-mm-wrapper {
            position: relative;
            font-family: 'Instrument Sans', sans-serif;
            width: 900px !important;
            max-width: none !important;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            background: #fff;
            overflow: visible;
        }
        
        /* The top triangle arrow */
        .cmr-mm-wrapper::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 80px; /* Shifted arrow to align with nav item */
            transform: rotate(45deg);
            width: 16px;
            height: 16px;
            background: #fff;
            box-shadow: -3px -3px 5px rgba(0,0,0,0.03);
            border-radius: 2px;
            z-index: 0;
        }

        .cmr-mm-top {
            padding: 40px 40px 30px;
            position: relative;
            z-index: 1;
            background: #fff;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .cmr-mm-label {
            font-size: 13px;
            font-weight: 600;
            color: #9ba4b5;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 25px;
        }

        .cmr-mm-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px 40px;
        }

        .cmr-mm-item {
            text-decoration: none;
            display: block;
        }
        
        .cmr-mm-item:hover h4 {
            color: #6A35FF;
        }

        .cmr-mm-item h4 {
            font-size: 18px;
            font-weight: 600;
            color: #111;
            margin: 0 0 8px 0;
            transition: color 0.2s ease;
        }

        .cmr-mm-item p {
            font-size: 15px;
            color: #666;
            margin: 0;
            line-height: 1.4;
        }

        .cmr-mm-bottom {
            padding: 30px 40px 40px;
            background: linear-gradient(135deg, #f0f4ff 0%, #e6f7ff 50%, #e6fff2 100%);
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            position: relative;
            z-index: 1;
        }

        .cmr-mm-bottom-content {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .cmr-mm-image {
            flex: 0 0 280px;
            height: 200px;
            border-radius: 8px;
            overflow: hidden;
        }

        .cmr-mm-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .cmr-mm-locations {
            flex: 1;
        }

        .cmr-mm-locations h3 {
            font-size: 20px;
            font-weight: 700;
            color: #111;
            margin: 0 0 20px 0;
            line-height: 1.3;
            letter-spacing: -0.5px;
        }

        .cmr-mm-locations ul {
            list-style: none;
            padding: 0;
            margin: 0 0 25px 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .cmr-mm-locations li {
            font-size: 15px;
            color: #555;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }
        
        .cmr-mm-loc-icon {
            width: 16px;
            height: 16px;
            color: #6A35FF;
        }

        .cmr-mm-explore {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 16px;
            font-weight: 600;
            color: #111;
            text-decoration: none;
            border-bottom: 2px solid #111;
            padding-bottom: 2px;
            transition: color 0.2s, border-color 0.2s;
        }

        .cmr-mm-explore:hover {
            color: #6A35FF;
            border-color: #6A35FF;
        }

        @media (max-width: 768px) {
            .cmr-mm-grid {
                grid-template-columns: 1fr;
            }
            .cmr-mm-bottom-content {
                flex-direction: column;
            }
            .cmr-mm-image {
                flex: auto;
                width: 100%;
            }
        }
    </style>

    <div class="cmr-mm-wrapper">
        <div class="cmr-mm-top">
            <div class="cmr-mm-label">WHO WE ARE</div>
            <div class="cmr-mm-grid">
                <a href="/about-us" class="cmr-mm-item">
                    <h4>About Us</h4>
                    <p>Our story, expertise, and vision</p>
                </a>
                <a href="/leadership" class="cmr-mm-item">
                    <h4>Leadership</h4>
                    <p>Meet the leaders driving innovation</p>
                </a>
                <a href="/careers" class="cmr-mm-item">
                    <h4>Careers</h4>
                    <p>Build the future with us</p>
                </a>
                <a href="/contact-us" class="cmr-mm-item">
                    <h4>Contact Us</h4>
                    <p>Connect with our expert team</p>
                </a>
            </div>
        </div>

        <div class="cmr-mm-bottom">
            <div class="cmr-mm-label">GLOBAL PRESENCE</div>
            <div class="cmr-mm-bottom-content">
                <div class="cmr-mm-image">
                    <!-- Replace src with actual glass building image from media library -->
                    <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=800&auto=format&fit=crop" alt="Global Presence">
                </div>
                <div class="cmr-mm-locations">
                    <h3>Serving businesses across<br>key markets.</h3>
                    <ul>
                        <li>
                            <svg class="cmr-mm-loc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            Singapore
                        </li>
                        <li>
                            <svg class="cmr-mm-loc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            Gurugram
                        </li>
                        <li>
                            <svg class="cmr-mm-loc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            Bengaluru
                        </li>
                        <li>
                            <svg class="cmr-mm-loc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            Mumbai
                        </li>
                    </ul>
                    <a href="/explore" class="cmr-mm-explore">
                        Explore 
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                    </a>
                </div>
            </div>
        </div>
    <?php
    return ob_get_clean();
}

// Automatically inject the mega menu into the nav bar
add_action('wp_footer', 'cmr_inject_who_we_are_mega_menu', 100);
function cmr_inject_who_we_are_mega_menu() {
    // Generate the mega menu HTML
    $mega_menu_html = do_shortcode('[cmr_mega_menu_who_we_are]');
    ?>
    <div id="cmr-hidden-mega-menu" style="display: none;">
        <?php echo $mega_menu_html; ?>
    </div>

    <style>
        /* CSS to make the nav item act as a dropdown wrapper */
        .cmr-has-mega-menu {
            position: relative !important;
        }
        
        .cmr-mm-wrapper {
            position: absolute !important;
            top: 100%;
            left: -20px;
            transform: none;
            min-width: 900px !important;
            width: max-content !important;
            max-width: none !important;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
            padding-top: 15px;
            z-index: 9999;
        }

        /* Adjust Elementor menu wrapper if needed so it doesnt clip */
        .elementor-nav-menu--main {
            overflow: visible !important;
        }
        
        .elementor-widget-nav-menu .elementor-nav-menu {
            overflow: visible !important;
        }

        .cmr-has-mega-menu:hover .cmr-mm-wrapper {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Hide the default submenu arrow if there is one */
        .cmr-has-mega-menu > a .sub-arrow {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find the hidden mega menu
            var megaMenu = document.getElementById('cmr-hidden-mega-menu');
            if (!megaMenu) return;

            // Find all nav links to search for "Who we serve" or "Who we are"
            var navLinks = document.querySelectorAll('.menu-item > a, .elementor-item');
            
            navLinks.forEach(function(link) {
                var text = link.innerText.trim().toLowerCase();
                // Attach to "Who we serve" (or fallback to "who we are" if changed)
                if (text === 'who we serve' || text === 'who we are') {
                    var parentLi = link.closest('li, .menu-item'); // Get the wrapping li
                    if (parentLi) {
                        parentLi.classList.add('cmr-has-mega-menu');
                        // Move all contents of megaMenu into the li
                        while (megaMenu.childNodes.length > 0) {
                            parentLi.appendChild(megaMenu.childNodes[0]);
                        }
                    }
                }
            });
            
            // Remove the hidden container
            if (megaMenu) {
                megaMenu.remove();
            }
        });
    </script>
    <?php
}
