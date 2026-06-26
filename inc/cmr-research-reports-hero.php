<?php
/**
 * CMR Research Reports Hero Section Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_research_reports_hero_shortcode' ) ) {
    function cmr_research_reports_hero_shortcode( $atts ) {
        // Shortcode attributes
        $atts = shortcode_atts( array(
            'title'       => 'Research Reports',
            'description' => 'Browse premium research reports across industries, designed to deliver actionable insights and strategic clarity.',
            'bg_image'    => 'https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/Gradients_2-1-scaled.png',
        ), $atts );

        ob_start();
        ?>
        <style>
            .cmr-research-hero {
                background-color: #0a0a0a;
                background-image: url('<?php echo esc_url($atts['bg_image']); ?>');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                padding: 100px 20px 80px;
                color: #ffffff;
                text-align: center;
                font-family: 'Instrument Sans', sans-serif !important;
                position: relative;
                overflow: hidden;
            }

            /* Add a dark overlay just in case the image is too bright */
            .cmr-research-hero::before {
                content: '';
                position: absolute;
                top: 0; left: 0; right: 0; bottom: 0;
                background: linear-gradient(135deg, rgba(10,10,10,0.9) 0%, rgba(20,20,20,0.7) 100%);
                z-index: 1;
            }

            .cmr-research-hero-content {
                position: relative;
                z-index: 2;
                max-width: 800px;
                margin: 0 auto;
            }

            .cmr-research-breadcrumbs {
                font-size: 13px;
                color: #a0a0a0;
                margin-bottom: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 8px;
            }

            .cmr-research-breadcrumbs a {
                color: #a0a0a0;
                text-decoration: none;
                transition: color 0.3s ease;
            }

            .cmr-research-breadcrumbs a:hover {
                color: #ffffff;
            }
            
            .cmr-research-breadcrumbs span.separator {
                font-size: 10px;
            }

            .cmr-research-hero h1 {
                font-size: 56px;
                font-weight: 600;
                color: #ffffff;
                margin: 0 0 20px 0;
                letter-spacing: -1.5px;
            }

            .cmr-research-hero p.desc {
                font-size: 18px;
                color: #fffff;
                line-height: 1.6;
                margin: 0 auto 40px auto;
                max-width: 650px;
            }

            /* Search Bar */
            .cmr-research-search-wrapper {
                position: relative;
                max-width: 638px;
                margin: 0 auto 30px auto;
            }

            .cmr-vp-hero-search {
                display: flex;
                align-items: center;
                background: #ffffff;
                border-radius: 50px;
                height: 48px;
                padding: 0 4px 0 20px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                border: 2px solid transparent;
                transition: border-color 0.3s ease;
            }
            
            .cmr-vp-hero-search:focus-within {
                border-color: #7b4cf6;
            }

            .cmr-vp-hero-search-icon-left {
                margin-right: 15px;
                display: flex;
                align-items: center;
            }

            .cmr-vp-hero-search input[type="text"] {
                flex: 1;
                border: none;
                background: transparent;
                padding: 0;
                height: 100%;
                font-size: 16px;
                font-family: inherit;
                color: #111;
                outline: none;
            }

            .cmr-vp-hero-search input[type="text"]::placeholder {
                color: #a0a0a0;
            }

            .cmr-vp-hero-search-btn {
                background: #6b46c1;
                color: #ffffff;
                border: none;
                width: 27px;
                height: 27px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background 0.3s ease, transform 0.2s ease;
                padding: 0;
            }

            .cmr-vp-hero-search-btn svg {
                width: 14px;
                height: 14px;
            }

            .cmr-vp-hero-search-btn:hover {
                background: #55359e;
                transform: scale(1.05);
            }

            /* Categories / Filters */
            .cmr-research-categories {
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 30px;
                margin-top: 30px;
            }

            .cmr-research-categories a {
                color: #ffffff;
                text-decoration: none;
                font-size: 15px;
                font-weight: 500;
                opacity: 0.8;
                transition: opacity 0.3s ease, color 0.3s ease;
            }

            .cmr-research-categories a:hover {
                opacity: 1;
                color: #a78bfa;
            }

            @media (max-width: 768px) {
                .cmr-research-hero h1 {
                    font-size: 40px;
                }
                .cmr-research-hero p.desc {
                    font-size: 16px;
                }
                .cmr-research-categories {
                    gap: 15px;
                }
            }
        </style>

        <section class="cmr-research-hero">
            <div class="cmr-research-hero-content">
                
                <div class="cmr-research-breadcrumbs">
                    <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a>
                    <span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
                    <span>Research</span>
                </div>

                <h1><?php echo esc_html( $atts['title'] ); ?></h1>
                
                <p class="desc"><?php echo esc_html( $atts['description'] ); ?></p>

                <div class="cmr-research-search-wrapper">
                    <form id="cmr-vp-hero-search-form" class="cmr-vp-hero-search" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
                        <div class="cmr-vp-hero-search-icon-left">
                            <img decoding="async" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/uploads/2026/06/cmrlogo-with-oly-c.svg" alt="CMR Logo" style="width: 24px; height: auto;">
                        </div>
                        <input type="text" name="s" placeholder="Search..." required="" value="<?php echo get_search_query(); ?>">
                        <input type="hidden" name="post_type" value="product" />
                        <button type="submit" class="cmr-vp-hero-search-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </button>
                    </form>
                </div>

                <div class="cmr-research-categories">
                    <!-- Link these to your actual product categories or pages -->
                    <a href="<?php echo esc_url( home_url('/product-category/automotive/') ); ?>">Automotive</a>
                    <a href="<?php echo esc_url( home_url('/product-category/consumer-tech/') ); ?>">Consumer Tech</a>
                    <a href="<?php echo esc_url( home_url('/product-category/digital-supply-chain/') ); ?>">Digital Supply Chain</a>
                    <a href="<?php echo esc_url( home_url('/product-category/it-telecom/') ); ?>">IT & Telecom</a>
                </div>

            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_research_hero', 'cmr_research_reports_hero_shortcode' );
