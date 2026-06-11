<?php
/**
 * Shortcode for Industry Intelligence Stack Component
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_industry_stack_shortcode' ) ) {
    function cmr_industry_stack_shortcode( $atts ) {
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
                padding: 40px;
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
            <div class="cmr-stack-header">
                <h2>A Complete Industry Intelligence Stack</h2>
                <p>Everything modern leaders need to drive data-guided AI strategies.</p>
            </div>
            
            <div class="cmr-stack-grid">
                <!-- Card 1 -->
                <div class="cmr-stack-card">
                    <div class="cmr-stack-card-top">
                        <div class="cmr-stack-icon">
                            <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 0H34V15L15 34H0V0Z" fill="#4B12C2"/>
                            </svg>
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
                            <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 0H10C19.3888 0 27 7.61116 27 17C27 26.3888 19.3888 34 10 34H0V0Z" fill="#0035FF"/>
                            </svg>
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
                            <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 0L17 9L26 0L34 8L25 17L34 26L26 34L17 25L8 34L0 26L9 17L0 8L8 0Z" fill="#00C1BC"/>
                            </svg>
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
