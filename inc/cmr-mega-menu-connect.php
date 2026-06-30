<?php
/* Mega Menu Shortcode: [cmr_mega_menu_connect] */

add_shortcode('cmr_mega_menu_connect', 'cmr_mega_menu_connect_shortcode');

function cmr_get_mmc_posts($slug, $fallback_offset) {
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 20, // Fetch more to allow filtering
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'category_name'  => $slug
    );
    $raw_posts = get_posts($args);
    if (count($raw_posts) < 3) {
        $args['category_name'] = '';
        $args['offset'] = $fallback_offset;
        $raw_posts = get_posts($args);
    }
    
    $unique_posts = array();
    $seen_titles = array();
    
    foreach ($raw_posts as $p) {
        $title = trim($p->post_title);
        if (!isset($seen_titles[$title])) {
            $seen_titles[$title] = true;
            $unique_posts[] = $p;
            if (count($unique_posts) == 3) {
                break;
            }
        }
    }
    
    return $unique_posts;
}

function cmr_mega_menu_connect_shortcode($atts) {
    ob_start();

    // Fetch 3 distinct sets of posts
    $posts_enterprise = cmr_get_mmc_posts('enterprise-connect', 0);
    $posts_smb = cmr_get_mmc_posts('smb-connect', 3);
    $posts_channel = cmr_get_mmc_posts('channel-connect', 6);

    ?>
    <style>
        .cmr-mmc-wrapper {
            position: relative;
            font-family: 'Instrument Sans', sans-serif;
            width: 900px !important;
            max-width: none !important;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            background: #fff;
            background: #fff;
            display: flex;
        }
        
        .cmr-mmc-wrapper::before {
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

        .cmr-mmc-left {
            width: 35%;
            padding: 30px;
            background: #fff;
            position: relative;
            z-index: 2;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .cmr-mmc-right {
            width: 65%;
            padding: 30px;
            background: linear-gradient(135deg, #f3f5ff 0%, #edf9fb 50%, #e6faf7 100%);
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .cmr-mmc-label {
            font-size: 13px;
            font-weight: 600;
            color: #9ba4b5;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .cmr-mmc-menu-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .cmr-mmc-item {
            text-decoration: none;
            display: block;
            position: relative;
        }
        
        .cmr-mmc-item:hover h4 {
            color: #6A35FF;
        }

        .cmr-mmc-item-header {
            display: flex;
            align-items: center;
            margin-bottom: 6px;
        }

        .cmr-mmc-item h4 {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0px;
            color: #111;
            margin: 0;
            transition: color 0.2s ease;
            display: inline-flex;
            align-items: center;
        }

        .cmr-mmc-item p {
            font-size: 14px;
            color: #666;
            margin: 0;
            line-height: 1.4;
        }

        .cmr-mmc-item.active h4 {
            color: #6A35FF !important;
        }

        .cmr-mmc-item.active::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -30px;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-top: 12px solid transparent;
            border-bottom: 12px solid transparent;
            border-right: 12px solid #f3f5ff;
            z-index: 2;
        }

        .cmr-mmc-text-inner {
            border-bottom: 2px solid transparent;
            padding-bottom: 2px;
            display: inline-flex;
            align-items: center;
            transition: border-color 0.2s;
        }

        .cmr-mmc-item.active .cmr-mmc-text-inner {
            border-bottom: 2px solid #6A35FF;
        }

        .cmr-mmc-arrow {
            display: none;
            margin-left: 6px;
        }

        .cmr-mmc-item.active .cmr-mmc-arrow {
            display: inline-block;
        }

        /* POSTS LIST */
        .cmr-mmc-posts-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            flex: 1;
        }

        .cmr-mmc-post-card {
            display: flex !important;
            flex-direction: row !important;
            align-items: stretch;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .cmr-mmc-post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .cmr-mmc-post-img {
            width: 140px;
            flex-shrink: 0;
            background: #eaeaea;
            position: relative;
            min-height: 110px;
        }

        .cmr-mmc-post-img img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .cmr-mmc-post-content {
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
        }

        .cmr-mmc-post-date {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .cmr-mmc-post-title {
            font-size: 15px;
            font-weight: 600;
            color: #111;
            line-height: 1.3;
            margin: 0 0 10px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cmr-mmc-post-readmore {
            font-size: 13px;
            font-weight: 600;
            color: #111;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-bottom: 1px solid #111;
            padding-bottom: 1px;
            width: max-content;
        }

        .cmr-mmc-explore-btn {
            text-align: center;
            margin-top: 20px;
        }

        .cmr-mmc-explore-btn a {
            font-size: 16px;
            font-weight: 600;
            color: #000 !important;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s ease;
        }

        .cmr-mmc-explore-btn a:hover {
            color: #6A35FF;
        }

        @media (max-width: 768px) {
            .cmr-mmc-wrapper {
                flex-direction: column;
            }
            .cmr-mmc-left, .cmr-mmc-right {
                width: 100%;
            }
        }
                        
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var megaMenuTemplate = document.getElementById('cmr-mmc-wrapper-inner');
            if (!megaMenuTemplate) return;

            function injectMegaMenu() {
                var navLinks = document.querySelectorAll('.menu-item > a, .elementor-item');
                navLinks.forEach(function(link) {
                    var text = link.innerText.trim().toLowerCase();
                    if (text === 'connect') {
                        var parentLi = link.closest('li, .menu-item');
                        if (parentLi && !parentLi.classList.contains('cmr-has-mega-menu-connect')) {
                            parentLi.classList.add('cmr-has-mega-menu-connect');
                            var wrapperOuter = document.createElement('div');
                            wrapperOuter.className = 'cmr-mmc-wrapper-outer';
                            Array.from(megaMenuTemplate.childNodes).forEach(function(node) { wrapperOuter.appendChild(node.cloneNode(true)); });
                            parentLi.appendChild(wrapperOuter);
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
                        if (text === 'connect') {
                            var parentLi = link.closest('.cmr-has-mega-menu-connect');
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




