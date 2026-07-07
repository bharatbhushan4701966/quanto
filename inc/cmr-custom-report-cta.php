<?php
/**
 * CMR Custom Report CTA Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_custom_report_cta_shortcode' ) ) {
    function cmr_custom_report_cta_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'title' => 'Need Custom Report?',
            'text'  => 'Get tailored insights, market analysis, and strategic recommendations designed around your specific requirements.',
            'button_text' => 'Get Industry Insights',
            'button_link' => '#',
        ), $atts );

        ob_start();
        ?>
        <style>
            .cmr-crc-wrapper {
                max-width: 1280px;
                margin: 40px auto;
                padding: 0 20px;
                font-family: 'Instrument Sans', sans-serif !important;
            }

            .cmr-crc-banner {
                background-color: #4b23a0; /* Purple background */
                border-radius: 4px;
                padding: 40px 50px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                color: #ffffff;
                gap: 40px;
            }

            .cmr-crc-left {
                flex: 1;
            }

            .cmr-crc-left h2 {
                font-size: 36px;
                font-weight: 600;
                color: #ffffff;
                margin: 0;
                line-height: 1.2;
                letter-spacing: -1px;
            }

            .cmr-crc-middle {
                flex: 1.5;
            }

            .cmr-crc-middle p {
                font-size: 15px;
                color: rgba(255, 255, 255, 0.9);
                line-height: 1.6;
                margin: 0;
                max-width: 450px;
            }

            .cmr-crc-right {
                flex: 0 0 auto;
            }

            .cmr-crc-btn {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                background-color: #ffffff;
                color: #4b23a0;
                font-size: 15px;
                font-weight: 600;
                padding: 15px 30px;
                border-radius: 50px;
                text-decoration: none;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .cmr-crc-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(0,0,0,0.1);
                color: #4b23a0;
            }

            .cmr-crc-btn i {
                font-size: 14px;
            }

            @media (max-width: 992px) {
                .cmr-crc-banner {
                    flex-direction: column;
                    text-align: center;
                    padding: 40px 20px;
                    gap: 25px;
                }
                
                .cmr-crc-middle p {
                    max-width: 100%;
                }
            }
        </style>

        <div class="cmr-crc-wrapper">
            <div class="cmr-crc-banner">
                <div class="cmr-crc-left">
                    <h2><?php echo esc_html( $atts['title'] ); ?></h2>
                </div>
                <div class="cmr-crc-middle">
                    <p><?php echo esc_html( $atts['text'] ); ?></p>
                </div>
                <div class="cmr-crc-right">
                    <a href="<?php echo esc_url( $atts['button_link'] ); ?>" class="cmr-crc-btn">
                        <?php echo esc_html( $atts['button_text'] ); ?>
                        <i class="fa-solid fa-arrow-up-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_custom_report_cta', 'cmr_custom_report_cta_shortcode' );
