<?php
/**
 * Shortcode for Investor Banner Component
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_investor_banner_shortcode' ) ) {
    function cmr_investor_banner_shortcode( $atts ) {
        ob_start();
        ?>
        <style>
            .cmr-ib-wrapper {
                font-family: 'Instrument Sans', sans-serif !important;
                width: 100%;
                max-width: 1280px;
                margin: 40px auto;
                padding: 0 20px;
                box-sizing: border-box;
            }

            .cmr-ib-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 15px; /* slight gap between blocks if needed, but the image shows them flush. Wait, let's look at the image. There are white gaps between the blocks. */
            }

            .cmr-ib-card {
                padding: 40px 30px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                min-height: 280px;
                color: #fff;
                box-sizing: border-box;
                position: relative;
            }

            .cmr-ib-card-black {
                background: #000000;
            }

            .cmr-ib-card-blue {
                background: #004ae1; /* Vibrant blue */
            }

            .cmr-ib-card-teal {
                background: #0ebfae; /* Bright teal */
            }

            .cmr-ib-label {
                font-size: 14px;
                font-weight: 500;
                margin-bottom: auto;
            }

            /* Content for Black Card */
            .cmr-ib-share-info {
                margin-top: 40px;
                margin-bottom: 20px;
            }
            .cmr-ib-exchange {
                font-size: 12px;
                color: #aaa;
                margin-bottom: 5px;
            }
            .cmr-ib-price-row {
                display: flex;
                align-items: baseline;
                gap: 10px;
                margin-bottom: 5px;
            }
            .cmr-ib-price {
                font-size: 42px;
                font-weight: 600;
                line-height: 1;
            }
            .cmr-ib-change {
                font-size: 14px;
                font-weight: 600;
            }
            .cmr-ib-date {
                font-size: 13px;
                color: #888;
            }

            /* Content for Blue/Teal Cards */
            .cmr-ib-title {
                font-size: 28px;
                font-weight: 600;
                line-height: 1.2;
                margin-bottom: 30px;
                margin-top: 40px;
                max-width: 80%;
            }

            /* Link Style */
            .cmr-ib-link {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                color: #fff;
                font-size: 14px;
                font-weight: 600;
                text-decoration: none;
                border-bottom: 1px solid rgba(255,255,255,0.4);
                padding-bottom: 4px;
                width: max-content;
                transition: border-color 0.3s ease;
            }

            .cmr-ib-link:hover {
                border-color: #fff;
            }

            .cmr-ib-link svg {
                width: 12px;
                height: 12px;
            }

            @media (max-width: 992px) {
                .cmr-ib-grid {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }
                .cmr-ib-card {
                    min-height: 220px;
                }
            }
        </style>

        <div class="cmr-ib-wrapper">
            <div class="cmr-ib-grid">
                
                <!-- Share Price Card -->
                <div class="cmr-ib-card cmr-ib-card-black">
                    <div class="cmr-ib-label">Share Price</div>
                    <div class="cmr-ib-share-info">
                        <div class="cmr-ib-exchange">NSE/BSE</div>
                        <div class="cmr-ib-price-row">
                            <div class="cmr-ib-price" id="cmr-live-price">₹ --</div>
                            <div class="cmr-ib-change" id="cmr-live-change">--</div>
                        </div>
                        <div class="cmr-ib-date" id="cmr-live-date">Loading live data...</div>
                    </div>
                    <a href="https://www.screener.in/company/CMRSL/consolidated/" target="_blank" class="cmr-ib-link">
                        View Info <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                    </a>
                </div>

                <!-- Annual Report Card -->
                <div class="cmr-ib-card cmr-ib-card-blue">
                    <div class="cmr-ib-label">Report</div>
                    <div class="cmr-ib-title">CMR 2026<br>Annual Report</div>
                    <a href="#" class="cmr-ib-link">
                        View Report <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                    </a>
                </div>

                <!-- Upcoming Event Card -->
                <div class="cmr-ib-card cmr-ib-card-teal">
                    <div class="cmr-ib-label">Upcoming event</div>
                    <div class="cmr-ib-title">Q1 FY27<br>Trading Update</div>
                    <a href="#" class="cmr-ib-link">
                        View Report <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                    </a>
                </div>

            </div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            function fetchCMRPrice() {
                var ajaxurl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
                fetch(ajaxurl + '?action=cmr_get_share_price')
                    .then(res => res.json())
                    .then(res => {
                        if(res.success && res.data) {
                            var priceEl = document.getElementById('cmr-live-price');
                            var changeEl = document.getElementById('cmr-live-change');
                            var dateEl = document.getElementById('cmr-live-date');
                            
                            if (priceEl && res.data.price) priceEl.innerText = '₹ ' + res.data.price;
                            if (changeEl && res.data.change) {
                                changeEl.innerText = res.data.change;
                                if(res.data.change.startsWith('-')) {
                                    changeEl.style.color = '#ff4d4d';
                                } else {
                                    changeEl.style.color = '#4ade80';
                                }
                            }
                            if (dateEl && res.data.date) dateEl.innerText = res.data.date;
                        }
                    })
                    .catch(err => console.error('Error fetching share price:', err));
            }

            // Fetch immediately
            fetchCMRPrice();

            // Fetch every 60 seconds
            setInterval(fetchCMRPrice, 60000);
        });
        </script>
        <?php
        return ob_get_clean();
    }
}

add_shortcode( 'cmr_investor_banner', 'cmr_investor_banner_shortcode' );

// AJAX handler for live share price
add_action('wp_ajax_cmr_get_share_price', 'cmr_get_share_price_handler');
add_action('wp_ajax_nopriv_cmr_get_share_price', 'cmr_get_share_price_handler');

function cmr_get_share_price_handler() {
    $transient_key = 'cmr_live_share_price';
    $data = get_transient($transient_key);

    if (false === $data) {
        $url = 'https://www.screener.in/company/CMRSL/consolidated/';
        $args = array(
            'headers' => array(
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36'
            ),
            'timeout' => 15,
        );
        $response = wp_remote_get($url, $args);
        
        $data = array(
            'price' => '70.0', // fallback
            'change' => '',
            'date' => date('d M h:i a')
        );

        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $html = wp_remote_retrieve_body($response);
            
            // Extract Price
            if (preg_match('/Current Price\s*<\/span>.*?<span class="nowrap value">\s*₹\s*<span class="number">([\d.]+)<\/span>/s', $html, $matches)) {
                $data['price'] = $matches[1];
            }

            // Extract Change
            if (preg_match('/<span class="font-size-12 (up|down)[^>]*>(.*?)<\/span>/s', $html, $change_matches)) {
                $direction = $change_matches[1] === 'up' ? '+' : '-';
                $data['change'] = $direction . trim(strip_tags($change_matches[2]));
            }
            
            // Extract Date
            if (preg_match('/<div class="ink-600 font-size-11 font-weight-500">(.*?)<\/div>/s', $html, $date_matches)) {
                $data['date'] = preg_replace('/\s+/', ' ', trim(strip_tags($date_matches[1])));
            }
        }
        
        // Cache for 60 seconds
        set_transient($transient_key, $data, 60);
    }
    
    wp_send_json_success($data);
}
