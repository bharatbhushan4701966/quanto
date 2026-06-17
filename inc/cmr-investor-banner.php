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
                        <div class="cmr-ib-exchange">NSE</div>
                        <div class="cmr-ib-price-row">
                            <div class="cmr-ib-price">₹ 77.0</div>
                            <div class="cmr-ib-change">+5.45 (+2.11%)</div>
                        </div>
                        <div class="cmr-ib-date">15 May 3:23 p.m.</div>
                    </div>
                    <a href="#" class="cmr-ib-link">
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
        <?php
        return ob_get_clean();
    }
}

add_shortcode( 'cmr_investor_banner', 'cmr_investor_banner_shortcode' );
