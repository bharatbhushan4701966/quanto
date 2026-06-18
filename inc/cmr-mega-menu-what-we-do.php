<?php
/* Mega Menu Shortcode: [cmr_mega_menu_what_we_do] */

add_shortcode('cmr_mega_menu_what_we_do', 'cmr_mega_menu_what_we_do_shortcode');

function cmr_get_mmw_posts($slug, $fallback_offset) {
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'category_name'  => $slug
    );
    $posts = get_posts($args);
    if (count($posts) < 3) {
        $args['category_name'] = '';
        $args['offset'] = $fallback_offset;
        $posts = get_posts($args);
    }
    return $posts;
}

function cmr_mega_menu_what_we_do_shortcode($atts) {
    ob_start();

    // Content for tabs
    $tabs_content = array(
        'cmr-mmw-list-industry' => array(
            'label' => 'INDUSTRY INTELLIGENCE',
            'heading' => 'Industry Intelligence that drives smarter business decisions',
            'desc' => 'Access real-time industry intelligence, expert analysis and strategic insights to stay ahead of market shifts and competitive dynamics.',
            'list' => array(
                'Understand market trends & shifts',
                'Evaluate growth opportunities',
                'Make data-driven decisions',
                'Gain industry-specific insights'
            ),
            'link_text' => 'Explore industry intelligence',
            'link_url' => home_url('/industry-intelligence/'),
            'bottom_text' => 'Talk to our analysts for tailored recommendations across your sector.',
            'bottom_link_text' => 'Get Industry insights',
            'bottom_link_url' => home_url('/insights/')
        ),
        'cmr-mmw-list-consulting' => array(
            'label' => 'CONSULTING & ADVISORY',
            'heading' => 'Consulting & Advisory that drives business growth',
            'desc' => 'Expert guidance to help you navigate complex business challenges and accelerate your growth trajectory.',
            'list' => array(
                'Strategic business planning',
                'Operational optimization',
                'Risk management & compliance',
                'Digital transformation strategies'
            ),
            'link_text' => 'Explore consulting & advisory',
            'link_url' => home_url('/consulting-advisory/'),
            'bottom_text' => 'Talk to our advisors for customized solutions for your business.',
            'bottom_link_text' => 'Get Advisory insights',
            'bottom_link_url' => home_url('/insights/')
        ),
        'cmr-mmw-list-marketing' => array(
            'label' => 'MARKETING SERVICES',
            'heading' => 'Research-backed marketing solutions for your brand',
            'desc' => 'Leverage our deep industry knowledge to create high-impact marketing campaigns that resonate with your target audience.',
            'list' => array(
                'Go-to-market strategies',
                'Content & thought leadership',
                'Lead generation campaigns',
                'Brand positioning & messaging'
            ),
            'link_text' => 'Explore marketing services',
            'link_url' => home_url('/marketing-services/'),
            'bottom_text' => 'Talk to our marketing experts to elevate your brand presence.',
            'bottom_link_text' => 'Get Marketing insights',
            'bottom_link_url' => home_url('/insights/')
        )
    );

    ?>
    <style>
        .cmr-mmw-wrapper {
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
        
        .cmr-mmw-wrapper::before {
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

        @media (max-width: 1400px) {
            .cmr-mmw-wrapper::before {
                left: 20%;
            }
            
            @media (max-width: 1100px) {
                .cmr-mmw-wrapper::before {
                    left: 15%;
                }
            }
        }

        .cmr-mmw-left {
            width: 35%;
            padding: 30px;
            background: #fff;
            position: relative;
            z-index: 2;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .cmr-mmw-right {
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

        .cmr-mmw-label {
            font-size: 13px;
            font-weight: 600;
            color: #9ba4b5;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .cmr-mmw-menu-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .cmr-mmw-item {
            text-decoration: none;
            display: block;
            position: relative;
        }
        
        .cmr-mmw-item:hover h4 {
            color: #6A35FF;
        }

        .cmr-mmw-item-header {
            display: flex;
            align-items: center;
            margin-bottom: 6px;
        }

        .cmr-mmw-item h4 {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0px;
            color: #111;
            margin: 0;
            transition: color 0.2s ease;
            display: inline-flex;
            align-items: center;
        }

        .cmr-mmw-item p {
            font-size: 14px;
            color: #666;
            margin: 0;
            line-height: 1.4;
        }

        .cmr-mmw-item.active h4 {
            color: #6A35FF !important;
        }

        .cmr-mmw-item.active::after {
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

        .cmr-mmw-text-inner {
            border-bottom: 2px solid transparent;
            padding-bottom: 2px;
            display: inline-flex;
            align-items: center;
            transition: border-color 0.2s;
        }

        .cmr-mmw-item.active .cmr-mmw-text-inner {
            border-bottom: 2px solid #6A35FF;
        }

        .cmr-mmw-arrow {
            display: none;
            margin-left: 6px;
        }

        .cmr-mmw-item.active .cmr-mmw-arrow {
            display: inline-block;
        }

        /* CONTENT STYLES */
        .cmr-mmw-content-panel {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .cmr-mmw-heading {
            font-size: 28px;
            line-height: 1.15;
            font-weight: 600;
            color: #111;
            margin: 10px 0 15px 0;
            letter-spacing: -0.5px;
        }

        .cmr-mmw-desc {
            font-size: 15px;
            color: #444;
            line-height: 1.5;
            margin-bottom: 25px;
        }

        .cmr-mmw-features {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .cmr-mmw-feature-item {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 15px;
            color: #333;
            font-weight: 500;
        }

        .cmr-mmw-feature-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #eee9fe;
            color: #4e2ecf;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .cmr-mmw-explore-link {
            font-size: 16px;
            font-weight: 600;
            color: #4e2ecf;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            transition: color 0.2s ease;
        }

        .cmr-mmw-explore-link:hover {
            color: #3b23a3;
        }

        .cmr-mmw-bottom-bar {
            margin-top: auto;
            padding-top: 15px;
            font-size: 13px;
            color: #555;
            line-height: 1.4;
        }

        .cmr-mmw-bottom-bar a {
            color: #4e2ecf;
            font-weight: 600;
            text-decoration: none;
            margin-left: 5px;
        }

        .cmr-mmw-bottom-bar a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .cmr-mmw-wrapper {
                flex-direction: column;
            }
            .cmr-mmw-left, .cmr-mmw-right {
                width: 100%;
            }
        }
    </style>

    <div class="cmr-mmw-wrapper" id="cmr-mmw-wrapper-inner">
        <div class="cmr-mmw-left">
            <div class="cmr-mmw-label">WHAT WE DO</div>
            <div class="cmr-mmw-menu-list">
                <a href="<?php echo esc_url( home_url( '/industry-intelligence/' ) ); ?>" class="cmr-mmw-item cmr-mmw-item-hover-trigger active" data-target="cmr-mmw-list-industry">
                    <div class="cmr-mmw-item-header">
                        <h4>
                            <span class="cmr-mmw-text-inner">
                                Industry Intelligence
                                <svg class="cmr-mmw-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                            </span>
                        </h4>
                    </div>
                    <p>Market research and strategic insights</p>
                </a>
                
                <a href="<?php echo esc_url( home_url( '/consulting-advisory/' ) ); ?>" class="cmr-mmw-item cmr-mmw-item-hover-trigger" data-target="cmr-mmw-list-consulting">
                    <div class="cmr-mmw-item-header">
                        <h4>
                            <span class="cmr-mmw-text-inner">
                                Consulting & Advisory 
                                <svg class="cmr-mmw-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                            </span>
                        </h4>
                    </div>
                    <p>Expert guidance for business growth</p>
                </a>
                
                <a href="<?php echo esc_url( home_url( '/marketing-services/' ) ); ?>" class="cmr-mmw-item cmr-mmw-item-hover-trigger" data-target="cmr-mmw-list-marketing">
                    <div class="cmr-mmw-item-header">
                        <h4>
                            <span class="cmr-mmw-text-inner">
                                Marketing Services
                                <svg class="cmr-mmw-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                            </span>
                        </h4>
                    </div>
                    <p>Research-backed marketing solutions</p>
                </a>
            </div>
        </div>

        <div class="cmr-mmw-right">
            <?php
            $first_tab = true;
            foreach ($tabs_content as $tab_id => $tab_data) :
            ?>
            <div class="cmr-mmw-content-panel cmr-mmw-posts-list" id="<?php echo esc_attr($tab_id); ?>" style="<?php echo $first_tab ? 'display: flex;' : 'display: none;'; ?>">
                <div class="cmr-mmw-label"><?php echo esc_html($tab_data['label']); ?></div>
                <h2 class="cmr-mmw-heading"><?php echo esc_html($tab_data['heading']); ?></h2>
                <p class="cmr-mmw-desc"><?php echo esc_html($tab_data['desc']); ?></p>
                
                <div class="cmr-mmw-features">
                    <?php 
                    $counter = 1;
                    foreach ($tab_data['list'] as $list_item) : 
                    ?>
                        <div class="cmr-mmw-feature-item">
                            <span class="cmr-mmw-feature-num"><?php echo $counter++; ?></span>
                            <span class="cmr-mmw-feature-text"><?php echo esc_html($list_item); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div>
                    <a href="<?php echo esc_url($tab_data['link_url']); ?>" class="cmr-mmw-explore-link">
                        <?php echo esc_html($tab_data['link_text']); ?> 
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                    </a>
                </div>

                <div class="cmr-mmw-bottom-bar">
                    <?php echo esc_html($tab_data['bottom_text']); ?>
                    <a href="<?php echo esc_url($tab_data['bottom_link_url']); ?>"><?php echo esc_html($tab_data['bottom_link_text']); ?></a>
                </div>
            </div>
            <?php 
            $first_tab = false;
            endforeach; 
            ?>
        </div>
    </div>
    
    <script>
        // Use a unique function to avoid global scope pollution if called multiple times
        (function() {
            var wrapper = document.getElementById('cmr-mmw-wrapper-inner');
            if (!wrapper) return;
            var items = wrapper.querySelectorAll('.cmr-mmw-item-hover-trigger');
            var lists = wrapper.querySelectorAll('.cmr-mmw-posts-list');
            
            items.forEach(function(item) {
                item.addEventListener('mouseenter', function() {
                    // remove active class
                    items.forEach(function(i) { i.classList.remove('active'); });
                    // hide all lists
                    lists.forEach(function(l) { l.style.display = 'none'; });
                    
                    // activate this
                    this.classList.add('active');
                    var targetId = this.getAttribute('data-target');
                    var targetList = document.getElementById(targetId);
                    if (targetList) {
                        targetList.style.display = 'flex';
                    }
                });
            });
        })();
    </script>
    <?php
    return ob_get_clean();
}

// Automatically inject the mega menu into the nav bar
add_action('wp_footer', 'cmr_inject_what_we_do_mega_menu', 100);
function cmr_inject_what_we_do_mega_menu() {
    $mega_menu_html = do_shortcode('[cmr_mega_menu_what_we_do]');
    ?>
    <div id="cmr-hidden-mega-menu-do" style="display: none;">
        <?php echo $mega_menu_html; ?>
    </div>

    <style>
        .cmr-has-mega-menu-do {
            position: relative !important;
        }
        
        .cmr-mmw-wrapper-outer {
            position: absolute !important;
            top: 60px;
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

        .elementor-nav-menu--main .elementor-item:hover + .cmr-mmw-wrapper-outer,
        .cmr-has-mega-menu-do:hover .cmr-mmw-wrapper-outer {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
            top: 60px !important;
        }

        @media (max-width: 1400px) {
            .cmr-mmw-wrapper-outer {
                left: 0 !important;
                transform: translateX(-15%) !important;
            }
            .elementor-nav-menu--main .elementor-item:hover + .cmr-mmw-wrapper-outer,
            .cmr-has-mega-menu-do:hover .cmr-mmw-wrapper-outer {
                transform: translateX(-15%) translateY(0) !important;
            }
            
            @media (max-width: 1100px) {
                .cmr-mmw-wrapper-outer {
                    transform: translateX(-5%) !important;
                }
                .elementor-nav-menu--main .elementor-item:hover + .cmr-mmw-wrapper-outer,
                .cmr-has-mega-menu-do:hover .cmr-mmw-wrapper-outer {
                    transform: translateX(-5%) translateY(0) !important;
                }
            }
        }

        .cmr-has-mega-menu-do > a .sub-arrow {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var megaMenuDo = document.getElementById('cmr-hidden-mega-menu-do');
            if (!megaMenuDo) return;

            var navLinks = document.querySelectorAll('.menu-item > a, .elementor-item');
            
            navLinks.forEach(function(link) {
                var text = link.innerText.trim().toLowerCase();
                if (text === 'what we do') {
                    var parentLi = link.closest('li, .menu-item');
                    if (parentLi) {
                        parentLi.classList.add('cmr-has-mega-menu-do');
                        
                        var wrapperOuter = document.createElement('div');
                        wrapperOuter.className = 'cmr-mmw-wrapper-outer';
                        
                        while (megaMenuDo.childNodes.length > 0) {
                            wrapperOuter.appendChild(megaMenuDo.childNodes[0]);
                        }
                        
                        parentLi.appendChild(wrapperOuter);
                    }
                }
            });
            
            if (megaMenuDo) {
                megaMenuDo.remove();
            }
        });
    </script>
    <?php
}
