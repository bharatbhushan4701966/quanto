<?php
/**
 * CMR Trending Now Section Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_trending_now_shortcode' ) ) {
    function cmr_trending_now_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'title' => 'Trending now',
            'limit' => 4,
        ), $atts );

        $args = array(
            'limit'   => intval( $atts['limit'] ),
            'status'  => 'publish',
            'orderby' => 'popularity', // Assuming we want trending/popular
            'order'   => 'DESC',
        );
        $products = wc_get_products( $args );

        ob_start();
        ?>
        <style>
            .cmr-trending-section {
                width: 100%;
                background-color: #f8f9fa;
                padding: 40px 0 50px;
                font-family: 'Instrument Sans', sans-serif !important;
            }

            .cmr-trending-inner {
                max-width: 1280px;
                margin: 0 auto;
                padding: 0 20px;
                box-sizing: border-box;
            }

            .cmr-trending-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 30px;
            }

            .cmr-trending-title {
                font-size: 32px;
                font-weight: 600;
                color: #000000;
                margin: 0;
                letter-spacing: -0.5px;
            }

            .cmr-trending-nav {
                display: flex;
                gap: 10px;
            }

            .cmr-trending-nav button {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #e9ecef;
                border: none;
                color: #000;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background 0.3s ease;
            }

            .cmr-trending-nav button:hover {
                background: #dee2e6;
            }

            .cmr-trending-grid {
                display: flex;
                gap: 20px;
                overflow-x: auto;
                scroll-behavior: smooth;
                scroll-snap-type: x mandatory;
                padding-bottom: 20px; /* Space for scrollbar */
                -ms-overflow-style: none; /* IE and Edge */
                scrollbar-width: none; /* Firefox */
            }

            .cmr-trending-grid::-webkit-scrollbar {
                display: none; /* Chrome, Safari and Opera */
            }

            .cmr-tn-card {
                background: #ffffff;
                border-radius: 0;
                overflow: hidden;
                border: 1px solid #e5e7eb;
                position: relative;
                transition: transform 0.3s ease;
                display: flex;
                flex-direction: row;
                height: 346px;
                flex: 0 0 calc(50% - 10px);
                scroll-snap-align: start;
            }

            .cmr-tn-card:hover {
                transform: translateY(-5px);
            }

            .cmr-tn-image-wrap {
                height: 100%;
                flex: 0 0 346px;
                width: 346px;
                aspect-ratio: 1 / 1;
                position: relative;
                overflow: hidden;
                background: #f8f9fa;
            }

            .cmr-tn-image-wrap img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: center;
                display: block;
            }

            .cmr-tn-badge {
                position: absolute;
                top: 15px;
                left: 15px;
                background: #ffffff;
                color: #06b6d4; /* Cyan/Teal color based on design */
                font-size: 11px;
                font-weight: 600;
                padding: 4px 10px;
                border-radius: 4px;
                z-index: 10;
                display: flex;
                align-items: center;
                gap: 5px;
                text-transform: uppercase;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }

            .cmr-tn-content {
                flex: 1;
                min-width: 0;
                padding: 24px 22px;
                display: flex;
                flex-direction: column;
                box-sizing: border-box;
            }

            .cmr-tn-category {
                font-size: 12px;
                color: #9ca3af;
                margin-bottom: 8px;
                text-transform: uppercase;
                font-weight: 600;
            }

            .cmr-tn-title {
                font-size: 18px;
                font-weight: 600;
                color: #111827;
                margin-bottom: 12px;
                line-height: 1.3;
                text-decoration: none;
            }

            .cmr-tn-title:hover {
                color: #6b46c1;
            }

            .cmr-tn-stars {
                color: #f59e0b;
                font-size: 14px;
                margin-bottom: 10px;
            }
            .cmr-tn-stars span {
                color: #d1d5db;
                font-size: 12px;
                margin-left: 5px;
            }

            .cmr-tn-brand {
                font-size: 13px;
                color: #6b7280;
                margin-bottom: 12px;
            }

            .cmr-tn-price {
                margin-top: auto;
                font-size: 20px;
                font-weight: 600;
                color: #111827;
            }

            .cmr-tn-price del {
                color: #9ca3af;
                font-size: 14px;
                font-weight: 400;
                margin-right: 5px;
            }

            @media (max-width: 992px) {
                .cmr-trending-inner {
                    padding: 0 16px !important;
                }
                .cmr-trending-grid {
                    gap: 16px !important;
                    scroll-padding-left: 16px !important;
                }
                .cmr-tn-card {
                    flex: 0 0 calc(65% - 16px);
                    height: 240px;
                }
                .cmr-tn-image-wrap {
                    flex: 0 0 240px;
                    width: 240px;
                }
                .cmr-tn-title {
                    font-size: 18px;
                }
            }

            @media (max-width: 576px) {
                .cmr-trending-inner {
                    padding: 0 16px !important;
                }
                .cmr-trending-grid {
                    gap: 16px !important;
                    scroll-padding-left: 16px !important;
                }
                .cmr-tn-card {
                    flex: 0 0 78% !important;
                    flex-direction: column !important;
                    height: auto !important;
                }
                .cmr-tn-image-wrap {
                    width: 100% !important;
                    height: auto !important;
                    aspect-ratio: 1 / 1 !important;
                    flex: none !important;
                }
                .cmr-tn-content {
                    width: 100% !important;
                    padding: 18px 16px !important;
                }
                .cmr-tn-title {
                    font-size: 17px !important;
                }
            }
        </style>

        <section class="cmr-trending-section">
            <div class="cmr-trending-inner">
                <div class="cmr-trending-header">
                    <h2 class="cmr-trending-title"><?php echo esc_html( $atts['title'] ); ?></h2>
                    <div class="cmr-trending-nav">
                        <button class="cmr-nav-prev" onclick="var g=document.querySelector('.cmr-trending-grid'); g.scrollBy({left: (window.innerWidth < 768 ? -g.clientWidth * 0.8 : -600), behavior: 'smooth'})"><i class="fa-solid fa-arrow-left"></i></button>
                        <button class="cmr-nav-next" onclick="var g=document.querySelector('.cmr-trending-grid'); g.scrollBy({left: (window.innerWidth < 768 ? g.clientWidth * 0.8 : 600), behavior: 'smooth'})"><i class="fa-solid fa-arrow-right"></i></button>
                    </div>
                </div>
                
                <div class="cmr-trending-grid">
                    <?php 
                    if ( ! empty( $products ) ) {
                        foreach ( $products as $product ) {
                        $image_url = wp_get_attachment_image_src( $product->get_image_id(), 'large' );
                        if ( ! $image_url ) {
                            $image_url = wp_get_attachment_image_src( $product->get_image_id(), 'medium_large' );
                        }
                        if ( ! $image_url ) {
                            $image_url = wp_get_attachment_image_src( $product->get_image_id(), 'medium' );
                        }
                        $image_url = $image_url ? $image_url[0] : 'https://via.placeholder.com/600x600';
                        
                        $cats = $product->get_category_ids();
                        $cat_name = 'Report';
                        if ( ! empty($cats) ) {
                            $term = get_term_by( 'id', $cats[0], 'product_cat' );
                            if ( $term ) {
                                $cat_name = $term->name;
                            }
                        }
                        ?>
                        <div class="cmr-tn-card">
                            <div class="cmr-tn-image-wrap">
                                <?php if ( function_exists( 'cmr_render_product_badge' ) ) { cmr_render_product_badge( $product, 'cmr-tn-badge' ); } ?>
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
                            </div>
                            <div class="cmr-tn-content">
                                <div class="cmr-tn-category">&mdash; <?php echo esc_html( $cat_name ); ?></div>
                                <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="cmr-tn-title"><?php echo esc_html( $product->get_name() ); ?></a>
                                <div class="cmr-tn-stars">
                                    <?php 
                                    $rating = floatval( $product->get_average_rating() );
                                    $count = intval( $product->get_review_count() );
                                    for ( $s = 1; $s <= 5; $s++ ) {
                                        if ( $s <= $rating ) echo '<i class="fa-solid fa-star"></i>';
                                        elseif ( $s - 0.5 <= $rating ) echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                        else echo '<i class="fa-regular fa-star"></i>';
                                    }
                                    ?>
                                    <span>(<?php echo $count; ?>)</span>
                                </div>
                                <div class="cmr-tn-brand">CyberMedia Research (CMR)</div>
                                <div class="cmr-tn-price">
                                    <?php echo $product->get_price_html(); ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>No trending products found.</p>';
                }
                ?>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_trending_now', 'cmr_trending_now_shortcode' );
