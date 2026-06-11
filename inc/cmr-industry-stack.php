<?php
/**
 * Shortcode for Industry Intelligence Stack Component
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_industry_stack_shortcode' ) ) {
    function cmr_industry_stack_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'icon_1' => 'https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/service-icon1.svg',
            'icon_2' => 'https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/Group-266.svg',
            'icon_3' => 'https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/Vector2.svg.svg',
        ), $atts );

        ob_start();
        ?>
        <style>
            .cmr-stack-wrapper {
                font-family: 'Instrument Sans', sans-serif;
                max-width: 1280px;
                margin: 0 auto;
                padding: 60px 20px;
                background-color: #f9fafb;
            }
            .cmr-stack-header {
                text-align: center;
                margin-bottom: 50px;
            }
            .cmr-stack-header h2 {
                font-size: 42px;
                font-weight: 700;
                margin: 0 0 15px 0;
                letter-spacing: -1px;
                color: #111;
            }
            .cmr-stack-header p {
                font-size: 16px;
                color: #555;
                margin: 0;
            }
            .cmr-stack-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
            }
            .cmr-stack-card {
                background: #fff;
                padding: 40px 40px 40px 40px;
                border-radius: 4px;
                display: flex;
                flex-direction: column;
                height: 402px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.03);
                position: relative;
                box-sizing: border-box;
            }
            .cmr-stack-card-top {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 30px;
            }
            .cmr-stack-icon {
                width: 48px;
                height: 48px;
                display: flex;
                align-items: center;
                justify-content: flex-start;
            }
            .cmr-stack-number {
                font-size: 16px;
                font-weight: 700;
                color: #111;
            }
            .cmr-stack-title {
                font-size: 24px;
                font-weight: 700;
                color: #111;
                margin: 0 0 15px 0;
                line-height: 1.3;
                letter-spacing: -1px;
            }
            .cmr-stack-desc {
                font-size: 15px;
                color: #555;
                line-height: 1.6;
                margin: 0 0 25px 0;
            }
            .cmr-stack-list {
                list-style: none;
                padding: 0;
                margin: 0;
                margin-top: auto;
            }
            .cmr-stack-list li {
                position: relative;
                padding-left: 15px;
                font-size: 14px;
                color: #444;
                margin-bottom: 12px;
                line-height: 1.4;
            }
            .cmr-stack-list li:last-child {
                margin-bottom: 0;
            }
            .cmr-stack-list li::before {
                content: '';
                position: absolute;
                left: 0;
                top: 7px;
                width: 4px;
                height: 4px;
                background-color: #111;
                border-radius: 50%;
            }
            @media (max-width: 992px) {
                .cmr-stack-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
            @media (max-width: 768px) {
                .cmr-stack-grid {
                    grid-template-columns: 1fr;
                }
                .cmr-stack-card {
                    height: auto;
                    min-height: 400px;
                }
            }
        </style>

        <div class="cmr-stack-wrapper">
            <div class="cmr-stack-grid">
                <!-- Card 1 -->
                <div class="cmr-stack-card">
                    <div class="cmr-stack-card-top">
                        <div class="cmr-stack-icon">
                            <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/service-icon1.svg" alt="Industry Intelligence Icon" style="width: 34px; height: 34px; object-fit: contain;">
                        </div>
                        <div class="cmr-stack-number">01</div>
                    </div>
                    <h3 class="cmr-stack-title">Industry<br>Intelligence</h3>
                    <p class="cmr-stack-desc">In-depth data, real-time signals, and expert insights to understand market trends and competitive dynamics.</p>
                    <ul class="cmr-stack-list">
                        <li>Market & product performance trackers</li>
                        <li>Sector trends & competitive landscape</li>
                        <li>Actionable intelligence for strategic decisions</li>
                    </ul>
                </div>

                <!-- Card 2 -->
                <div class="cmr-stack-card">
                    <div class="cmr-stack-card-top">
                        <div class="cmr-stack-icon">
                            <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/Group-266.svg" alt="Consulting & Advisory Icon" style="width: 34px; height: 34px; object-fit: contain;">
                        </div>
                        <div class="cmr-stack-number">02</div>
                    </div>
                    <h3 class="cmr-stack-title">Consulting &<br>Advisory</h3>
                    <p class="cmr-stack-desc">Expert-led strategies and future-ready roadmaps that turn market intelligence into growth — from opportunity assessment to go-to-market execution.</p>
                    <ul class="cmr-stack-list">
                        <li>Advisory support</li>
                        <li>Market entry & growth strategy</li>
                        <li>Data-driven decision frameworks</li>
                    </ul>
                </div>

                <!-- Card 3 -->
                <div class="cmr-stack-card">
                    <div class="cmr-stack-card-top">
                        <div class="cmr-stack-icon">
                            <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/Vector2.svg.svg" alt="Marketing Services Icon" style="width: 34px; height: 34px; object-fit: contain;">
                        </div>
                        <div class="cmr-stack-number">03</div>
                    </div>
                    <h3 class="cmr-stack-title">Marketing<br>Services</h3>
                    <p class="cmr-stack-desc">Actionable intelligence and analyst guidance to drive faster, confident business decisions.</p>
                    <ul class="cmr-stack-list">
                        <li>Advisory support</li>
                        <li>Market entry & growth strategy</li>
                        <li>Data-driven decision frameworks</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_industry_stack', 'cmr_industry_stack_shortcode' );
