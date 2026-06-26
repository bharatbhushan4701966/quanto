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
                max-width: 1200px;
                margin: 60px auto;
                padding: 40px 20px;
                background-color: #f8f9fa; /* Light background based on the design */
                font-family: 'Instrument Sans', sans-serif !important;
            }

            .cmr-trending-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 30px;
            }

            .cmr-trending-title {
                font-size: 32px;
                font-weight: 700;
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
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                /* If they want a slider later, this grid can become a flex container */
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
                height: 342px;
            }

            .cmr-tn-card:hover {
                transform: translateY(-5px);
            }

            .cmr-tn-image-wrap {
                width: 45%;
                position: relative;
            }

            .cmr-tn-image-wrap img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .cmr-tn-badge {
                position: absolute;
                top: 15px;
                left: 15px;
                background: #ffffff;
                color: #06b6d4; /* Cyan/Teal color based on design */
                font-size: 11px;
                font-weight: 700;
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
                width: 55%;
                padding: 25px;
                display: flex;
                flex-direction: column;
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
                font-weight: 700;
                color: #111827;
                margin-bottom: 15px;
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
                margin-bottom: 15px;
            }

            .cmr-tn-price {
                margin-top: auto;
                font-size: 20px;
                font-weight: 700;
                color: #111827;
            }

            .cmr-tn-price del {
                color: #9ca3af;
                font-size: 14px;
                font-weight: 400;
                margin-right: 5px;
            }

            @media (max-width: 992px) {
                .cmr-trending-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 576px) {
                .cmr-tn-card {
                    flex-direction: column;
                    height: auto;
                }
                .cmr-tn-image-wrap {
                    width: 100%;
                    height: 200px;
                }
                .cmr-tn-content {
                    width: 100%;
                }
            }
        </style>

        <section class="cmr-trending-section">
            <div class="cmr-trending-header">
                <h2 class="cmr-trending-title"><?php echo esc_html( $atts['title'] ); ?></h2>
                <div class="cmr-trending-nav">
                    <button class="cmr-nav-prev"><i class="fa-solid fa-arrow-left"></i></button>
                    <button class="cmr-nav-next"><i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>
            
            <div class="cmr-trending-grid">
                <?php 
                if ( ! empty( $products ) ) {
                    // For now, let's just display the first 2 items to match the exact grid screenshot. 
                    // To implement the slider functionality later, JS will be needed.
                    $display_count = min( count($products), 2 );
                    for ( $i = 0; $i < $display_count; $i++ ) {
                        $product = $products[$i];
                        $image_url = wp_get_attachment_image_src( $product->get_image_id(), 'medium' );
                        $image_url = $image_url ? $image_url[0] : 'https://via.placeholder.com/400x400';
                        
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
                                <div class="cmr-tn-badge"><i class="fa-solid fa-bolt"></i> Trending</div>
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
                            </div>
                            <div class="cmr-tn-content">
                                <div class="cmr-tn-category">&mdash; <?php echo esc_html( $cat_name ); ?></div>
                                <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="cmr-tn-title"><?php echo esc_html( $product->get_name() ); ?></a>
                                <div class="cmr-tn-stars">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i>
                                    <span>(4)</span>
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
        </section>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_trending_now', 'cmr_trending_now_shortcode' );
