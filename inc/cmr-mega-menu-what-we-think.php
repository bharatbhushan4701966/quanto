<?php
/* Mega Menu Shortcode: [cmr_mega_menu_what_we_think] */

add_shortcode('cmr_mega_menu_what_we_think', 'cmr_mega_menu_what_we_think_shortcode');

function cmr_mega_menu_what_we_think_shortcode($atts) {
    ob_start();

    // Fetch 1 latest product for the report section (fallback to mock if none)
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC'
    );
    $products = get_posts($args);
    $product = !empty($products) ? $products[0] : null;

    ?>
    <style>
        .cmr-mmt-wrapper {
            position: relative;
            font-family: 'Instrument Sans', sans-serif;
            width: 900px !important;
            max-width: none !important;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            background: #fff;
            display: flex;
            border-radius: 12px;
        }

        .cmr-mmt-wrapper::before {
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

        .cmr-mmt-left {
            width: 65%;
            padding: 30px;
            background: #fff;
            position: relative;
            z-index: 2;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
            display: flex;
            gap: 40px;
        }

        .cmr-mmt-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .cmr-mmt-right {
            width: 35%;
            padding: 30px;
            background: linear-gradient(135deg, #f3f5ff 0%, #edf9fb 50%, #e6faf7 100%);
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .cmr-mmt-label {
            font-size: 13px;
            font-weight: 600;
            color: #9ba4b5;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 0px;
        }

        .cmr-mmt-item {
            text-decoration: none;
            display: block;
        }
        
        .cmr-mmt-item:hover h4 {
            color: #6A35FF;
        }

        .cmr-mmt-item h4 {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0px;
            color: #111;
            margin: 0 0 6px 0;
            transition: color 0.2s ease;
        }

        .cmr-mmt-item p {
            font-size: 14px;
            color: #666;
            margin: 0;
            line-height: 1.4;
        }

        /* Report Card */
        .cmr-mmt-report-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            margin-top: 15px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .cmr-mmt-report-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .cmr-mmt-report-img {
            width: 100%;
            height: 140px;
            background: #000;
            overflow: hidden;
        }

        .cmr-mmt-report-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .cmr-mmt-report-content {
            padding: 15px;
            display: flex;
            flex-direction: column;
        }

        .cmr-mmt-report-cat {
            font-size: 12px;
            color: #999;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .cmr-mmt-report-cat::before {
            content: '—';
            color: #ccc;
        }

        .cmr-mmt-report-title {
            font-size: 15px;
            font-weight: 600;
            color: #111;
            line-height: 1.3;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cmr-mmt-report-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .cmr-mmt-report-rating {
            display: flex;
            align-items: center;
            gap: 2px;
            color: #f59e0b;
            font-size: 12px;
        }

        .cmr-mmt-report-rating-count {
            color: #666;
            margin-left: 4px;
        }

        .cmr-mmt-report-price {
            font-size: 16px;
            font-weight: 700;
            color: #111;
        }

        .cmr-mmt-report-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 15px;
            border: 1px solid #111;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            color: #111;
            background: transparent;
            transition: all 0.2s ease;
            width: max-content;
        }

        .cmr-mmt-report-card:hover .cmr-mmt-report-btn {
            background: #111;
            color: #fff;
        }

        @media (max-width: 768px) {
            .cmr-mmt-wrapper {
                flex-direction: column;
            }
            .cmr-mmt-left, .cmr-mmt-right {
                width: 100%;
            }
            .cmr-mmt-left {
                flex-direction: column;
            }
        }
    </style>

    <div class="cmr-mmt-wrapper">
        <div class="cmr-mmt-left">
            <div class="cmr-mmt-col">
                <div class="cmr-mmt-label">WHAT WE THINK</div>
                <a href="/research-reports" class="cmr-mmt-item">
                    <h4>Research Reports</h4>
                    <p>Data-driven insights and forecasts</p>
                </a>
                <a href="/industry-insights" class="cmr-mmt-item">
                    <h4>Industry Insights</h4>
                    <p>Expert analysis on emerging trends</p>
                </a>
                <a href="/market-updates" class="cmr-mmt-item">
                    <h4>Market Updates</h4>
                    <p>Latest developments shaping industries</p>
                </a>
            </div>
            <div class="cmr-mmt-col">
                <div class="cmr-mmt-label">CMR LIVE</div>
                <a href="/top-view" class="cmr-mmt-item">
                    <h4>Top View</h4>
                    <p>Watch expert perspectives and industry conversations.</p>
                </a>
                <a href="/podcasts" class="cmr-mmt-item">
                    <h4>Podcasts</h4>
                    <p>Expert conversations on trends and innovation.</p>
                </a>
            </div>
        </div>

        <div class="cmr-mmt-right">
            <div class="cmr-mmt-label">REPORT</div>
            
            <?php if ($product) : 
                $price = get_post_meta($product->ID, '_regular_price', true);
                $price_display = $price ? '$' . $price : '$450';
                $thumbnail_url = get_the_post_thumbnail_url( $product->ID, 'medium' );
                if ( ! $thumbnail_url ) {
                    $thumbnail_url = 'https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/Why-Chipsets-are-the-New-Frontier-in-Smartphones1.jpg';
                }
            ?>
            <a href="<?php echo esc_url(get_permalink($product->ID)); ?>" class="cmr-mmt-report-card">
                <div class="cmr-mmt-report-img">
                    <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr(get_the_title($product->ID)); ?>">
                </div>
                <div class="cmr-mmt-report-content">
                    <div class="cmr-mmt-report-cat">IT & Telecom</div>
                    <div class="cmr-mmt-report-title"><?php echo esc_html(get_the_title($product->ID)); ?></div>
                    <div class="cmr-mmt-report-meta">
                        <div class="cmr-mmt-report-rating">
                            ★ ★ ★ ★ ★ <span class="cmr-mmt-report-rating-count">(2)</span>
                        </div>
                        <div class="cmr-mmt-report-price"><?php echo esc_html($price_display); ?></div>
                    </div>
                    <div class="cmr-mmt-report-btn">
                        Download Report ↓
                    </div>
                </div>
            </a>
            <?php else : ?>
            <a href="#" class="cmr-mmt-report-card">
                <div class="cmr-mmt-report-img">
                    <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/Why-Chipsets-are-the-New-Frontier-in-Smartphones1.jpg" alt="Report">
                </div>
                <div class="cmr-mmt-report-content">
                    <div class="cmr-mmt-report-cat">IT & Telecom</div>
                    <div class="cmr-mmt-report-title">Steering Innovation: Technology Trends and Opportunities in India's Startup</div>
                    <div class="cmr-mmt-report-meta">
                        <div class="cmr-mmt-report-rating">
                            ★ ★ ★ ★ ★ <span class="cmr-mmt-report-rating-count">(2)</span>
                        </div>
                        <div class="cmr-mmt-report-price">$450</div>
                    </div>
                    <div class="cmr-mmt-report-btn">
                        Download Report ↓
                    </div>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

add_action('wp_footer', 'cmr_inject_what_we_think_mega_menu', 100);
function cmr_inject_what_we_think_mega_menu() {
    $mega_menu_html = do_shortcode('[cmr_mega_menu_what_we_think]');
    ?>
    <div id="cmr-hidden-mega-menu-think" style="display: none;">
        <?php echo $mega_menu_html; ?>
    </div>

    <style>
        .cmr-has-mega-menu-think {
            position: relative !important;
        }
        
        .cmr-mmt-wrapper-outer {
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

        .elementor-nav-menu--main .elementor-item:hover + .cmr-mmt-wrapper,
        .cmr-has-mega-menu-think:hover .cmr-mmt-wrapper {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
            top: 60px !important;
        }

        .cmr-has-mega-menu-think > a .sub-arrow {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var megaMenuThink = document.getElementById('cmr-hidden-mega-menu-think');
            if (!megaMenuThink) return;

            var navLinks = document.querySelectorAll('.menu-item > a, .elementor-item');
            
            navLinks.forEach(function(link) {
                var text = link.innerText.trim().toLowerCase();
                if (text === 'what we think') {
                    var parentLi = link.closest('li, .menu-item');
                    if (parentLi) {
                        parentLi.classList.add('cmr-has-mega-menu-think');
                        
                        var wrapperOuter = document.createElement('div');
                        wrapperOuter.className = 'cmr-mmt-wrapper-outer';
                        
                        while (megaMenuThink.childNodes.length > 0) {
                            wrapperOuter.appendChild(megaMenuThink.childNodes[0]);
                        }
                        
                        parentLi.appendChild(wrapperOuter);
                    }
                }
            });
            
            if (megaMenuThink) {
                megaMenuThink.remove();
            }
        });
    </script>
    <?php
}
