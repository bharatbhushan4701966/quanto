<?php
/* Mega Menu Shortcode: [cmr_mega_menu_who_we_serve] */

add_shortcode('cmr_mega_menu_who_we_serve', 'cmr_mega_menu_who_we_serve_shortcode');

function cmr_mega_menu_who_we_serve_shortcode($atts) {
    ob_start();
    ?>
    <style>
        .cmr-mms-wrapper {
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
        .cmr-mms-wrapper::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%) rotate(45deg);
            width: 16px;
            height: 16px;
            background: #fff;
            box-shadow: -3px -3px 5px rgba(0,0,0,0.03);
            border-radius: 2px;
            z-index: 0;
        }

        .cmr-mms-top {
            padding: 0px 30px 20px;
            position: relative;
            z-index: 1;
            background: #fff;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .cmr-mms-col {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .cmr-mms-label {
            font-size: 13px;
            font-weight: 600;
            color: #9ba4b5;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 0px;
        }

        .cmr-mms-bottom .cmr-mms-label {
            margin-bottom: 5px;
        }

        .cmr-mms-item {
            text-decoration: none;
            display: block;
        }
        
        .cmr-mms-item:hover h4 {
            color: #6A35FF;
        }

        .cmr-mms-item-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }

        .cmr-mms-item h4 {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0px;
            color: #111;
            margin: 0;
            transition: color 0.2s ease;
        }

        .cmr-mms-badge {
            font-size: 12px;
            font-weight: 600;
            color: #0b8a4f;
            background: #e6f7ec;
            padding: 2px 8px;
            border-radius: 12px;
        }

        .cmr-mms-item p {
            font-size: 15px;
            color: #666;
            margin: 0;
            line-height: 1.4;
        }

        .cmr-mms-bottom {
            padding: 10px 20px 30px;
            background: linear-gradient(135deg, #f3f5ff 0%, #edf9fb 50%, #e6faf7 100%);
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            position: relative;
            z-index: 1;
        }

        .cmr-mms-bottom .cmr-mms-label {
            margin-bottom: 20px;
        }

        .cmr-mms-insight-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .cmr-mms-insight-item {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 15px;
            text-decoration: none;
            color: #000 !important;
            font-weight: 600;
            font-size: 16px;
            white-space: nowrap;
            transition: color 0.2s ease;
        }

        .cmr-mms-insight-item:hover {
            color: #6A35FF;
        }

        .cmr-mms-insight-icon {
            color: #6A35FF;
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .cmr-mms-insight-icon svg {
            width: 100%;
            height: 100%;
        }

        .cmr-mms-insight-arrow {
            color: #9ba4b5;
            width: 14px;
            height: 14px;
            margin-left: 5px;
            transition: transform 0.2s;
        }

        .cmr-mms-insight-item:hover .cmr-mms-insight-arrow {
            transform: translateX(3px);
            color: #6A35FF;
        }

        @media (max-width: 768px) {
            .cmr-mms-top {
                grid-template-columns: 1fr;
            }
        }
                        @media (max-width: 1024px) {
            .cmr-mms-bottom { display: none !important; }
            .cmr-has-mega-menu-serve .cmr-mms-wrapper {
                position: static !important;
                transform: none !important;
                width: 100% !important;
                box-shadow: none !important;
                display: none;
                opacity: 1;
                visibility: visible;
                padding-top: 0;
                margin-top: 0;
            }
            .cmr-has-mega-menu-serve.cmr-mobile-open > .cmr-mms-wrapper {
                display: block !important;
            }
            .cmr-has-mega-menu-serve .cmr-mms-wrapper::before {
                display: none !important;
            }
            .cmr-mm-grid {
                grid-template-columns: 1fr !important;
                gap: 15px !important;
            }
            .cmr-mm-bottom-content {
                flex-direction: column !important;
                align-items: flex-start !important;
            }
        }
    </style>

    <div class="cmr-mms-wrapper">
        <div class="cmr-mms-top">
            <div class="cmr-mms-col">
                <div class="cmr-mms-label">WHO WE SERVE</div>
                
                <a href="<?php echo esc_url( home_url( '/automotive/' ) ); ?>" class="cmr-mms-item">
                    <div class="cmr-mms-item-header">
                        <h4>Automotive</h4>
                        <span class="cmr-mms-badge">New</span>
                    </div>
                    <p>Insights for the mobility ecosystem</p>
                </a>
                
                <a href="<?php echo esc_url( home_url( '/consumer-tech/' ) ); ?>" class="cmr-mms-item">
                    <div class="cmr-mms-item-header">
                        <h4>Consumer Tech</h4>
                    </div>
                    <p>Understanding digital consumer behavior</p>
                </a>
                
                <a href="<?php echo esc_url( home_url( '/digital-supply-chain/' ) ); ?>" class="cmr-mms-item">
                    <div class="cmr-mms-item-header">
                        <h4>Digital Supply Chain</h4>
                    </div>
                    <p>Intelligence for connected supply chains</p>
                </a>
            </div>
            
            <div class="cmr-mms-col">
                <div class="cmr-mms-label" style="visibility: hidden;">&nbsp;</div>
                <a href="<?php echo esc_url( home_url( '/it-telecom/' ) ); ?>" class="cmr-mms-item">
                    <div class="cmr-mms-item-header">
                        <h4>IT & Telecom</h4>
                    </div>
                    <p>Research across technology markets</p>
                </a>
                
                <a href="<?php echo esc_url( home_url( '/semiconductors/' ) ); ?>" class="cmr-mms-item">
                    <div class="cmr-mms-item-header">
                        <h4>Semiconductors</h4>
                        <span class="cmr-mms-badge">New</span>
                    </div>
                    <p>Tracking innovation and demand shifts</p>
                </a>
            </div>
        </div>

        <div class="cmr-mms-bottom">
            <div class="cmr-mms-label">INSIGHT</div>
            <div class="cmr-mms-insight-list">
                <a href="<?php echo esc_url( home_url( '/insight-1/' ) ); ?>" class="cmr-mms-insight-item">
                    <div class="cmr-mms-insight-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="9" y1="18" x2="15" y2="18"></line>
                            <line x1="10" y1="22" x2="14" y2="22"></line>
                            <path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 12 3a4.65 4.65 0 0 0-4.5 4.5c0 .89.28 1.5.83 2.1.84.89 1.47 1.83 1.67 2.9"></path>
                            <line x1="12" y1="7" x2="12" y2="10"></line>
                            <line x1="8" y1="11" x2="10" y2="11"></line>
                            <line x1="14" y1="11" x2="16" y2="11"></line>
                        </svg>
                    </div>
                    Debugging with product analytics
                    <svg class="cmr-mms-insight-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
                <a href="<?php echo esc_url( home_url( '/insight-2/' ) ); ?>" class="cmr-mms-insight-item">
                    <div class="cmr-mms-insight-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="9" y1="18" x2="15" y2="18"></line>
                            <line x1="10" y1="22" x2="14" y2="22"></line>
                            <path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 12 3a4.65 4.65 0 0 0-4.5 4.5c0 .89.28 1.5.83 2.1.84.89 1.47 1.83 1.67 2.9"></path>
                            <line x1="12" y1="7" x2="12" y2="10"></line>
                            <line x1="8" y1="11" x2="10" y2="11"></line>
                            <line x1="14" y1="11" x2="16" y2="11"></line>
                        </svg>
                    </div>
                    Why it's never too early to add product ana...
                    <svg class="cmr-mms-insight-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
                <a href="<?php echo esc_url( home_url( '/insight-3/' ) ); ?>" class="cmr-mms-insight-item">
                    <div class="cmr-mms-insight-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="9" y1="18" x2="15" y2="18"></line>
                            <line x1="10" y1="22" x2="14" y2="22"></line>
                            <path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 12 3a4.65 4.65 0 0 0-4.5 4.5c0 .89.28 1.5.83 2.1.84.89 1.47 1.83 1.67 2.9"></path>
                            <line x1="12" y1="7" x2="12" y2="10"></line>
                            <line x1="8" y1="11" x2="10" y2="11"></line>
                            <line x1="14" y1="11" x2="16" y2="11"></line>
                        </svg>
                    </div>
                    Data implementation, starting with the 'why'
                    <svg class="cmr-mms-insight-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            </div>
        </div>
    <?php
    return ob_get_clean();
}

// Automatically inject the mega menu into the nav bar
add_action('wp_footer', 'cmr_inject_who_we_serve_mega_menu', 100);
function cmr_inject_who_we_serve_mega_menu() {
    // Generate the mega menu HTML
    $mega_menu_html = do_shortcode('[cmr_mega_menu_who_we_serve]');
    ?>
    <div id="cmr-hidden-mega-menu-serve" style="display: none;">
        <?php echo $mega_menu_html; ?>
    </div>

    <style>
        /* CSS to make the nav item act as a dropdown wrapper */
        .cmr-has-mega-menu-serve {
            position: relative !important;
        }
        
        .cmr-mms-wrapper {
            position: absolute !important;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            width: max-content !important;
            max-width: none !important;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
            padding-top: 15px;
            z-index: 9999;
        }

        /* Show on hover */
        .elementor-nav-menu--main .elementor-item:hover + .cmr-mms-wrapper-outer,
        .cmr-has-mega-menu-serve:hover .cmr-mms-wrapper-outer,
        .elementor-nav-menu--main .elementor-item:hover + .cmr-mms-wrapper,
        .cmr-has-mega-menu-serve:hover .cmr-mms-wrapper {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
            top: 80px !important;
        }

        /* Hide the default submenu arrow if there is one */
        .cmr-has-mega-menu-serve > a .sub-arrow {
            display: none !important;
        }
            
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var megaMenuTemplate = document.getElementById('cmr-hidden-mega-menu-serve');
            if (!megaMenuTemplate) return;

            function injectMegaMenu() {
                var navLinks = document.querySelectorAll('.menu-item > a, .elementor-item');
                navLinks.forEach(function(link) {
                    var text = link.innerText.trim().toLowerCase();
                    if (text === 'who we serve') {
                        var parentLi = link.closest('li, .menu-item');
                        if (parentLi && !parentLi.classList.contains('cmr-has-mega-menu-serve')) {
                            parentLi.classList.add('cmr-has-mega-menu-serve');
                            Array.from(megaMenuTemplate.childNodes).forEach(function(node) { parentLi.appendChild(node.cloneNode(true)); });
                        }
                    }
                });
            }

            injectMegaMenu();
            setInterval(injectMegaMenu, 1000);

            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 1024) {
                    var link = e.target.closest('a');
                    if (link) {
                        var text = link.innerText.trim().toLowerCase();
                        if (text === 'who we serve') {
                            var parentLi = link.closest('.cmr-has-mega-menu-serve');
                            if (parentLi) {
                                e.preventDefault();
                                e.stopPropagation();
                                parentLi.classList.toggle('cmr-mobile-open');
                            }
                        }
                    }
                }
            }, true);
        });
    </script>
    <?php
}



