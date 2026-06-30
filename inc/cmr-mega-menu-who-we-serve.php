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
            gap: 14px;
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



