<?php
/**
 * CMR Featured Reports Section Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_featured_reports_shortcode' ) ) {
    function cmr_featured_reports_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'title' => 'Featured reports',
        ), $atts );

        // Try to get 3 products (can be marked as featured, or just recent)
        $args = array(
            'limit'   => 3,
            'status'  => 'publish',
            'orderby' => 'date',
            'order'   => 'DESC',
        );
        $products = wc_get_products( $args );

        ob_start();
        ?>
        <style>
            .cmr-featured-reports-section {
                max-width: 1200px;
                margin: 60px auto;
                padding: 0 20px;
                font-family: 'Instrument Sans', sans-serif !important;
            }

            .cmr-featured-reports-title {
                font-size: 32px;
                font-weight: 700;
                color: #000000;
                margin-bottom: 30px;
                letter-spacing: -0.5px;
            }

            .cmr-featured-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }

            .cmr-fr-card {
                background: #ffffff;
                border-radius: 0;
                overflow: hidden;
                border: 1px solid #e5e7eb;
                position: relative;
                transition: transform 0.3s ease;
                display: flex;
            }

            .cmr-fr-card:hover {
                transform: translateY(-5px);
            }

            .cmr-fr-badge {
                position: absolute;
                top: 15px;
                left: 15px;
                background: #ffffff;
                color: #6b46c1;
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

            .cmr-fr-badge i {
                font-size: 10px;
            }

            /* Big Card (Left) */
            .cmr-fr-large {
                flex-direction: column;
                height: 695px;
            }

            .cmr-fr-large .cmr-fr-image-wrap {
                width: 100%;
                height: 100%;
                position: absolute;
                top: 0;
                left: 0;
            }

            .cmr-fr-large .cmr-fr-image-wrap img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .cmr-fr-large .cmr-fr-image-wrap::after {
                content: '';
                position: absolute;
                top: 0; left: 0; right: 0; bottom: 0;
                background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.8) 100%);
            }

            .cmr-fr-large-content {
                position: relative;
                z-index: 2;
                padding: 60px 30px 30px;
                display: flex;
                flex-direction: column;
                height: 100%;
                color: #ffffff;
            }

            .cmr-fr-large-title {
                font-size: 24px;
                font-weight: 700;
                color: #ffffff;
                margin-bottom: 10px;
                line-height: 1.3;
                text-decoration: none;
            }

            .cmr-fr-stars {
                color: #f59e0b;
                font-size: 14px;
                margin-bottom: 10px;
            }
            .cmr-fr-stars span {
                color: #d1d5db;
                font-size: 12px;
                margin-left: 5px;
            }

            .cmr-fr-brand {
                font-size: 13px;
                color: #d1d5db;
                margin-bottom: auto;
            }



            /* Small Cards (Right) */
            .cmr-fr-small-col {
                display: flex;
                flex-direction: column;
                gap: 11px;
            }

            .cmr-fr-small {
                flex-direction: row;
                height: 342px;
            }

            .cmr-fr-small .cmr-fr-image-wrap {
                width: 45%;
                position: relative;
            }

            .cmr-fr-small .cmr-fr-image-wrap img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .cmr-fr-small-content {
                width: 55%;
                padding: 25px;
                display: flex;
                flex-direction: column;
            }

            .cmr-fr-category {
                font-size: 12px;
                color: #9ca3af;
                margin-bottom: 8px;
                text-transform: uppercase;
                font-weight: 600;
            }

            .cmr-fr-small-title {
                font-size: 18px;
                font-weight: 700;
                color: #111827;
                margin-bottom: 15px;
                line-height: 1.3;
                text-decoration: none;
            }

            .cmr-fr-small-title:hover {
                color: #6b46c1;
            }

            .cmr-fr-small .cmr-fr-brand {
                color: #6b7280;
                margin-bottom: 15px;
            }

            .cmr-fr-price {
                margin-top: auto;
                font-size: 20px;
                font-weight: 700;
                color: #111827;
            }

            .cmr-fr-price del {
                color: #9ca3af;
                font-size: 14px;
                font-weight: 400;
                margin-right: 5px;
            }

            @media (max-width: 992px) {
                .cmr-featured-grid {
                    grid-template-columns: 1fr;
                }
                .cmr-fr-large {
                    min-height: 400px;
                }
                .cmr-fr-small {
                    height: auto;
                }
            }

            @media (max-width: 576px) {
                .cmr-fr-small {
                    flex-direction: column;
                }
                .cmr-fr-small .cmr-fr-image-wrap {
                    width: 100%;
                    height: 200px;
                }
                .cmr-fr-small-content {
                    width: 100%;
                }
            }
        </style>

        <section class="cmr-featured-reports-section">
            <h2 class="cmr-featured-reports-title"><?php echo esc_html( $atts['title'] ); ?></h2>
            
            <div class="cmr-featured-grid">
                
                <?php if ( ! empty( $products ) && isset( $products[0] ) ) : 
                    $product = $products[0];
                    $image_url = wp_get_attachment_image_src( $product->get_image_id(), 'large' );
                    $image_url = $image_url ? $image_url[0] : 'https://via.placeholder.com/600x800';
                ?>
                <!-- Large Card -->
                <div class="cmr-fr-card cmr-fr-large">
                    <div class="cmr-fr-image-wrap">
                        <div class="cmr-fr-badge"><i class="fa-solid fa-bookmark"></i> Featured</div>
                        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
                    </div>
                    <div class="cmr-fr-large-content">
                        <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="cmr-fr-large-title"><?php echo esc_html( $product->get_name() ); ?></a>
                        <div class="cmr-fr-stars">
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
                        <div class="cmr-fr-brand">CyberMedia Research (CMR)</div>
                        
                    </div>
                </div>
                <?php endif; ?>

                <div class="cmr-fr-small-col">
                    <?php 
                    // Small Cards
                    for ( $i = 1; $i <= 2; $i++ ) {
                        if ( isset( $products[$i] ) ) {
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
                            <div class="cmr-fr-card cmr-fr-small">
                                <div class="cmr-fr-image-wrap">
                                    <div class="cmr-fr-badge"><i class="fa-solid fa-bookmark"></i> Featured</div>
                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
                                </div>
                                <div class="cmr-fr-small-content">
                                    <div class="cmr-fr-category">&mdash; <?php echo esc_html( $cat_name ); ?></div>
                                    <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="cmr-fr-small-title"><?php echo esc_html( $product->get_name() ); ?></a>
                                    <div class="cmr-fr-stars">
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
                                    <div class="cmr-fr-brand">CyberMedia Research (CMR)</div>
                                    <div class="cmr-fr-price">
                                        <?php echo $product->get_price_html(); ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>

            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_featured_reports', 'cmr_featured_reports_shortcode' );
