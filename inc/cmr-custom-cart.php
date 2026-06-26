<?php
/**
 * CMR Custom Cart Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_custom_cart_shortcode' ) ) {
    function cmr_custom_cart_shortcode() {
        // Enqueue necessary scripts/styles
        ob_start();
        ?>
        <style>
            .cmr-cart-wrapper {
                max-width: 1280px;
                margin: 40px auto;
                padding: 0 20px;
                font-family: 'Instrument Sans', sans-serif !important;
                color: #111827;
            }

            .cmr-back-link {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                color: #374151;
                font-weight: 600;
                font-size: 15px;
                text-decoration: none;
                margin-bottom: 30px;
            }

            .cmr-back-link:hover {
                color: #000;
            }

            .cmr-cart-grid {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 40px;
            }

            /* Box styles */
            .cmr-cart-box {
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 4px;
                margin-bottom: 30px;
                position: relative;
            }

            .cmr-cart-box-header {
                padding: 20px 30px;
                border-bottom: 1px solid #e5e7eb;
            }

            .cmr-cart-box-title {
                font-size: 18px;
                font-weight: 700;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .cmr-cart-box-title i {
                color: #6b7280;
                font-size: 16px;
            }

            /* Items List */
            .cmr-cart-items {
                padding: 0 30px;
            }

            .cmr-cart-item {
                display: flex;
                gap: 25px;
                padding: 30px 0;
                border-bottom: 1px solid #e5e7eb;
                position: relative;
            }

            .cmr-cart-item:last-child {
                border-bottom: none;
            }

            .cmr-ci-image {
                width: 120px;
                flex-shrink: 0;
            }

            .cmr-ci-image img {
                width: 100%;
                height: auto;
                border-radius: 4px;
                border: 1px solid #f3f4f6;
            }

            .cmr-ci-details {
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .cmr-ci-title {
                font-size: 16px;
                font-weight: 700;
                color: #111827;
                margin-bottom: 8px;
                line-height: 1.4;
                padding-right: 100px; /* Space for price */
            }

            .cmr-ci-brand {
                font-size: 14px;
                color: #6b7280;
                margin-bottom: 15px;
            }

            .cmr-ci-sku {
                font-size: 14px;
                font-weight: 600;
                color: #374151;
            }
            .cmr-ci-sku span {
                color: #6b7280;
                font-weight: 400;
            }

            .cmr-ci-price {
                position: absolute;
                top: 30px;
                right: 0;
                font-size: 20px;
                font-weight: 700;
                color: #111827;
            }

            .cmr-ci-remove {
                position: absolute;
                bottom: 30px;
                right: 0;
                color: #ef4444; /* Red */
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 5px;
                background: none;
                border: none;
                padding: 0;
            }

            .cmr-ci-remove:hover {
                text-decoration: underline;
            }

            /* Promo Box */
            .cmr-promo-box {
                padding: 20px 30px;
            }
            .cmr-promo-input-wrap {
                display: flex;
                gap: 10px;
                margin-top: 15px;
                margin-bottom: 10px;
            }
            .cmr-promo-input-wrap input {
                flex: 1;
                padding: 12px 15px;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                font-size: 15px;
                outline: none;
            }
            .cmr-promo-input-wrap input:focus {
                border-color: #6b46c1;
            }
            .cmr-promo-btn {
                background: #ffffff;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                padding: 0 20px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .cmr-promo-btn:hover {
                background: #f9fafb;
            }
            .cmr-promo-hint {
                font-size: 12px;
                color: #6b7280;
            }

            /* Applied Coupon State */
            .cmr-applied-coupon {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 12px 15px;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                margin-top: 15px;
                background: #f9fafb;
            }
            .cmr-ac-code {
                font-weight: 600;
                font-size: 15px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .cmr-ac-status {
                color: #10b981;
                font-size: 13px;
            }
            .cmr-ac-remove {
                background: none;
                border: none;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                color: #374151;
            }
            .cmr-ac-remove:hover {
                text-decoration: underline;
            }

            /* Order Summary Box */
            .cmr-summary-box {
                padding: 30px;
            }
            .cmr-summary-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 15px;
                font-size: 15px;
                color: #4b5563;
            }
            .cmr-summary-row.discount-row {
                color: #10b981;
            }
            .cmr-summary-total {
                display: flex;
                justify-content: space-between;
                margin-top: 30px;
                margin-bottom: 25px;
                font-size: 20px;
                font-weight: 700;
                color: #111827;
            }
            .cmr-checkout-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                background: #4b23a0;
                color: #ffffff;
                width: 100%;
                padding: 16px;
                border-radius: 50px;
                font-size: 16px;
                font-weight: 600;
                text-decoration: none;
                transition: background 0.2s;
                border: none;
                cursor: pointer;
            }
            .cmr-checkout-btn:hover {
                background: #391880;
                color: #ffffff;
            }

            /* Safe Checkout */
            .cmr-safe-checkout {
                margin-top: 25px;
            }
            .cmr-safe-checkout p {
                font-size: 14px;
                font-weight: 600;
                color: #111827;
                margin-bottom: 15px;
            }
            .cmr-payment-icons {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }
            .cmr-payment-icons img {
                height: 30px;
                border: 1px solid #e5e7eb;
                border-radius: 4px;
                padding: 2px 6px;
                background: #fff;
            }

            /* Loading Overlay */
            .cmr-cart-loading {
                position: absolute;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(255,255,255,0.7);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 100;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s;
            }
            .cmr-cart-loading.active {
                opacity: 1;
                pointer-events: all;
            }

            @media (max-width: 992px) {
                .cmr-cart-grid {
                    grid-template-columns: 1fr;
                }
                .cmr-ci-price {
                    position: static;
                    margin-top: 15px;
                }
                .cmr-ci-title {
                    padding-right: 0;
                }
                .cmr-ci-remove {
                    bottom: auto;
                    top: 30px;
                }
            }
            @media (max-width: 576px) {
                .cmr-cart-item {
                    flex-direction: column;
                    gap: 15px;
                }
                .cmr-ci-image {
                    width: 100%;
                    max-width: 200px;
                }
                .cmr-ci-remove {
                    top: 10px;
                    right: 0;
                }
            }
        </style>

        <div class="cmr-cart-wrapper" id="cmr-custom-cart-app">
            <?php echo cmr_get_cart_html(); ?>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const cartApp = document.getElementById('cmr-custom-cart-app');

                function bindCartEvents() {
                    // Remove Item
                    const removeBtns = cartApp.querySelectorAll('.cmr-ci-remove');
                    removeBtns.forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            const cartKey = this.getAttribute('data-cart-key');
                            updateCart('remove_item', { cart_key: cartKey });
                        });
                    });

                    // Apply Coupon
                    const applyBtn = cartApp.querySelector('.cmr-promo-btn');
                    if ( applyBtn ) {
                        applyBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            const code = cartApp.querySelector('#cmr_coupon_code').value;
                            if ( code ) {
                                updateCart('apply_coupon', { coupon_code: code });
                            }
                        });
                    }

                    // Remove Coupon
                    const removeCouponBtns = cartApp.querySelectorAll('.cmr-ac-remove');
                    removeCouponBtns.forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            const code = this.getAttribute('data-coupon');
                            updateCart('remove_coupon', { coupon_code: code });
                        });
                    });
                }

                function updateCart( actionName, dataPayload ) {
                    const loading = cartApp.querySelector('.cmr-cart-loading');
                    if (loading) loading.classList.add('active');

                    const formData = new FormData();
                    formData.append('action', 'cmr_cart_action');
                    formData.append('cart_action', actionName);
                    
                    for ( const key in dataPayload ) {
                        formData.append(key, dataPayload[key]);
                    }

                    fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if ( data.success ) {
                            cartApp.innerHTML = data.data.html;
                            bindCartEvents();
                            // Dispatch standard woo event so fragments (like header cart count) update
                            jQuery(document.body).trigger('wc_fragment_refresh');
                        } else {
                            if (loading) loading.classList.remove('active');
                            alert(data.data.message || 'An error occurred.');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        if (loading) loading.classList.remove('active');
                    });
                }

                // Initial Bind
                bindCartEvents();
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_custom_cart', 'cmr_custom_cart_shortcode' );


/**
 * Helper to generate cart HTML
 */
function cmr_get_cart_html() {
    if ( WC()->cart->is_empty() ) {
        ob_start();
        ?>
        <div class="cmr-cart-empty" style="text-align: center; padding: 100px 20px;">
            <i class="fa-solid fa-cart-shopping" style="font-size: 48px; color: #d1d5db; margin-bottom: 20px;"></i>
            <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 15px;">Your cart is currently empty!</h2>
            <p style="color: #6b7280; margin-bottom: 30px;">Browse our latest reports and add them to your cart.</p>
            <a href="/reports/" style="display: inline-block; background: #4b23a0; color: #fff; padding: 12px 30px; border-radius: 50px; text-decoration: none; font-weight: 600;">Return to Shop</a>
        </div>
        <?php
        return ob_get_clean();
    }

    ob_start();
    $cart_count = WC()->cart->get_cart_contents_count();
    ?>
    <a href="/reports/" class="cmr-back-link">&larr; Back to Reports</a>
    
    <div class="cmr-cart-grid">
        <div class="cmr-cart-loading"><i class="fa-solid fa-circle-notch fa-spin fa-2x" style="color: #6b46c1;"></i></div>
        
        <!-- Left Column -->
        <div class="cmr-cart-items-wrap">
            <div class="cmr-cart-box">
                <div class="cmr-cart-box-header">
                    <h3 class="cmr-cart-box-title"><i class="fa-solid fa-bag-shopping"></i> Shopping Cart (<?php echo esc_html( $cart_count ); ?> items)</h3>
                </div>
                
                <div class="cmr-cart-items">
                    <?php 
                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                        $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                            $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key );
                            $product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
                            $price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                            $sku = $_product->get_sku();
                            
                            ?>
                            <div class="cmr-cart-item">
                                <div class="cmr-ci-image">
                                    <?php echo $thumbnail; ?>
                                </div>
                                <div class="cmr-ci-details">
                                    <div class="cmr-ci-title"><?php echo wp_kses_post( $product_name ); ?></div>
                                    <div class="cmr-ci-brand">CyberMedia Research (CMR)</div>
                                    <?php if ( $sku ) : ?>
                                        <div class="cmr-ci-sku"><span>SKU:</span> <?php echo esc_html( $sku ); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="cmr-ci-price">
                                    <?php echo $price; ?>
                                </div>
                                <button class="cmr-ci-remove" data-cart-key="<?php echo esc_attr( $cart_item_key ); ?>">
                                    <i class="fa-regular fa-trash-can"></i> Remove
                                </button>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="cmr-cart-sidebar">
            
            <!-- Promo Box -->
            <div class="cmr-cart-box">
                <div class="cmr-promo-box">
                    <h3 class="cmr-cart-box-title" style="font-size: 16px;"><i class="fa-solid fa-tag"></i> Promo Code</h3>
                    
                    <?php 
                    $applied_coupons = WC()->cart->get_applied_coupons();
                    if ( empty( $applied_coupons ) ) : 
                    ?>
                        <div class="cmr-promo-input-wrap">
                            <input type="text" id="cmr_coupon_code" placeholder="CMR10">
                            <button class="cmr-promo-btn">Apply</button>
                        </div>
                        <div class="cmr-promo-hint">Try: CMR10, CMRINDIA15, or FIRST20</div>
                    <?php else : ?>
                        <?php foreach ( $applied_coupons as $coupon_code ) : ?>
                            <div class="cmr-applied-coupon">
                                <div class="cmr-ac-code">
                                    <?php echo esc_html( strtoupper( $coupon_code ) ); ?>
                                    <span class="cmr-ac-status">Applied <i class="fa-solid fa-circle-check"></i></span>
                                </div>
                                <button class="cmr-ac-remove" data-coupon="<?php echo esc_attr( $coupon_code ); ?>">Remove &times;</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Summary Box -->
            <div class="cmr-cart-box">
                <div class="cmr-summary-box">
                    <h3 class="cmr-cart-box-title" style="margin-bottom: 20px;">Order Summary</h3>
                    
                    <div class="cmr-summary-row">
                        <span>Subtotal (<?php echo esc_html( $cart_count ); ?> items)</span>
                        <span><?php echo wc_price( WC()->cart->get_subtotal() ); ?></span>
                    </div>
                    
                    <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
                        <?php 
                        $taxes = WC()->cart->get_taxes();
                        $tax_total = array_sum( $taxes );
                        if ( $tax_total > 0 ) :
                        ?>
                        <div class="cmr-summary-row">
                            <span>Tax</span>
                            <span><?php echo wc_price( $tax_total ); ?></span>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                        <div class="cmr-summary-row discount-row">
                            <span>Discount (<?php echo esc_html( $code ); ?>)</span>
                            <span>-<?php echo wc_price( WC()->cart->get_coupon_discount_amount( $code ) ); ?></span>
                        </div>
                    <?php endforeach; ?>

                    <div class="cmr-summary-total">
                        <span>Total</span>
                        <span><?php echo wc_price( WC()->cart->get_total('edit') ); ?></span>
                    </div>

                    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="cmr-checkout-btn">
                        <i class="fa-regular fa-credit-card"></i> Proceed to Checkout
                    </a>
                </div>
            </div>

            <div class="cmr-safe-checkout">
                <p>Guaranteed safe checkout</p>
                <div class="cmr-payment-icons">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b0/Apple_Pay_logo.svg" alt="Apple Pay" style="height:24px;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/f/f2/Google_Pay_Logo.svg" alt="Google Pay" style="height:24px;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" style="height:24px;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/American_Express_logo_%282018%29.svg" alt="Amex" style="height:24px;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" style="height:24px;">
                    <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/05/Visa.svg" alt="Visa" style="height:24px;">
                </div>
            </div>

        </div>
    </div>
    <?php
    return ob_get_clean();
}


/**
 * AJAX Handler for custom cart interactions
 */
add_action( 'wp_ajax_cmr_cart_action', 'cmr_cart_action_handler' );
add_action( 'wp_ajax_nopriv_cmr_cart_action', 'cmr_cart_action_handler' );

function cmr_cart_action_handler() {
    $action = isset( $_POST['cart_action'] ) ? sanitize_text_field( $_POST['cart_action'] ) : '';

    if ( ! WC()->cart ) {
        wp_send_json_error( array( 'message' => 'Cart not initialized.' ) );
    }

    try {
        switch ( $action ) {
            case 'remove_item':
                $cart_item_key = isset( $_POST['cart_key'] ) ? sanitize_text_field( $_POST['cart_key'] ) : '';
                if ( $cart_item_key ) {
                    WC()->cart->remove_cart_item( $cart_item_key );
                }
                break;

            case 'apply_coupon':
                $coupon_code = isset( $_POST['coupon_code'] ) ? sanitize_text_field( $_POST['coupon_code'] ) : '';
                if ( $coupon_code ) {
                    WC()->cart->add_discount( $coupon_code );
                }
                break;

            case 'remove_coupon':
                $coupon_code = isset( $_POST['coupon_code'] ) ? sanitize_text_field( $_POST['coupon_code'] ) : '';
                if ( $coupon_code ) {
                    WC()->cart->remove_coupon( $coupon_code );
                }
                break;
        }

        // Calculate totals to ensure accuracy
        WC()->cart->calculate_totals();

        wp_send_json_success( array(
            'html' => cmr_get_cart_html()
        ) );
    } catch ( Exception $e ) {
        wp_send_json_error( array( 'message' => $e->getMessage() ) );
    }
}
