<?php
/**
 * CMR Custom Checkout Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_custom_checkout_shortcode' ) ) {
    function cmr_custom_checkout_shortcode() {
        ob_start();
        ?>
        <style>
            /* ===== BASE WRAPPER ===== */
            .cmr-checkout-wrapper {
                max-width: 1280px;
                margin: 40px auto;
                padding: 0 20px;
                font-family: 'Instrument Sans', sans-serif !important;
                color: #111827;
            }
            .cmr-checkout-wrapper .cmr-back-link {
                display: none;
            }

            /* ===== TWO-COLUMN LAYOUT ===== */
            .cmr-checkout-wrapper .woocommerce-checkout {
                display: flex;
                gap: 40px;
                flex-wrap: wrap;
                align-items: flex-start;
            }

            /* Left Column */
            .cmr-checkout-wrapper #customer_details {
                flex: 2;
                min-width: 300px;
            }

            /* Right Column: Order Review */
            .cmr-checkout-wrapper #order_review_heading {
                display: none;
            }
            .cmr-checkout-wrapper #order_review {
                flex: 1;
                min-width: 300px;
                background: #ffffff;
                border: 1px solid #e5e7eb;
                padding: 30px;
                border-radius: 4px;
                position: sticky;
                top: 40px;
            }

            /* ===== SECTION HEADINGS ===== */
            .cmr-checkout-wrapper .woocommerce-billing-fields h3,
            .cmr-checkout-wrapper .woocommerce-shipping-fields h3,
            .cmr-checkout-wrapper .woocommerce-additional-fields h3,
            .cmr-checkout-wrapper #ship-to-different-address {
                font-size: 22px;
                font-weight: 700;
                color: #111827;
                margin: 0 0 25px 0;
                padding: 0;
                border: none;
            }

            /* ===== FORM ROW STRUCTURE ===== */
            .cmr-checkout-wrapper .woocommerce-checkout .form-row {
                position: relative;
                margin-bottom: 16px;
                width: 100%;
                box-sizing: border-box;
            }
            .cmr-checkout-wrapper .woocommerce-checkout .form-row-first {
                float: left;
                width: 48%;
            }
            .cmr-checkout-wrapper .woocommerce-checkout .form-row-last {
                float: right;
                width: 48%;
            }
            .cmr-checkout-wrapper .woocommerce-checkout .form-row-wide {
                width: 100%;
                clear: both;
            }
            .cmr-checkout-wrapper .woocommerce-checkout .form-row::after {
                content: "";
                display: table;
                clear: both;
            }

            /* ===== FLOATING LABELS ===== */
            .cmr-checkout-wrapper .woocommerce-checkout label {
                position: absolute;
                top: 6px;
                left: 14px;
                font-size: 11px;
                color: #9ca3af;
                font-weight: 400;
                z-index: 2;
                margin: 0;
                pointer-events: none;
                line-height: 1;
            }
            .cmr-checkout-wrapper .woocommerce-checkout label .required {
                display: none;
            }
            .cmr-checkout-wrapper .woocommerce-checkout .checkbox,
            .cmr-checkout-wrapper .woocommerce-checkout .woocommerce-form__label-for-checkbox {
                position: static !important;
                font-size: 14px;
                color: #6b7280;
            }

            /* ===== INPUT FIELDS ===== */
            .cmr-checkout-wrapper .woocommerce-checkout input[type="text"],
            .cmr-checkout-wrapper .woocommerce-checkout input[type="email"],
            .cmr-checkout-wrapper .woocommerce-checkout input[type="tel"],
            .cmr-checkout-wrapper .woocommerce-checkout input[type="password"],
            .cmr-checkout-wrapper .woocommerce-checkout input[type="number"],
            .cmr-checkout-wrapper .woocommerce-checkout select,
            .cmr-checkout-wrapper .woocommerce-checkout textarea {
                width: 100%;
                height: 54px;
                padding: 22px 14px 6px 14px;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                font-size: 15px;
                font-family: 'Instrument Sans', sans-serif;
                color: #111827;
                outline: none;
                background: #fff;
                box-sizing: border-box;
                transition: border-color 0.15s ease;
            }
            .cmr-checkout-wrapper .woocommerce-checkout input::placeholder,
            .cmr-checkout-wrapper .woocommerce-checkout textarea::placeholder {
                color: transparent !important;
            }
            .cmr-checkout-wrapper .woocommerce-checkout textarea {
                height: auto;
                min-height: 80px;
                padding-top: 26px;
            }
            .cmr-checkout-wrapper .woocommerce-checkout input:focus,
            .cmr-checkout-wrapper .woocommerce-checkout select:focus,
            .cmr-checkout-wrapper .woocommerce-checkout textarea:focus {
                border-color: #6b46c1;
                box-shadow: 0 0 0 1px #6b46c1;
            }

            /* ===== SELECT2 DROPDOWN ===== */
            .cmr-checkout-wrapper .select2-container {
                width: 100% !important;
            }
            .cmr-checkout-wrapper .select2-container--default .select2-selection--single {
                height: 54px;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                padding: 22px 14px 6px 14px;
                box-sizing: border-box;
                background: #fff;
            }
            .cmr-checkout-wrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 1;
                padding: 0;
                color: #111827;
                font-size: 15px;
            }
            .cmr-checkout-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 52px;
                right: 10px;
            }
            .cmr-checkout-wrapper .select2-container--open .select2-selection--single {
                border-color: #6b46c1;
                box-shadow: 0 0 0 1px #6b46c1;
            }

            /* ===== ORDER REVIEW TABLE ===== */
            .cmr-checkout-wrapper .shop_table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 0;
            }
            .cmr-checkout-wrapper .shop_table thead {
                display: none;
            }
            .cmr-checkout-wrapper .shop_table th,
            .cmr-checkout-wrapper .shop_table td {
                padding: 12px 0;
                border-bottom: 1px solid #e5e7eb;
                text-align: left;
                font-size: 14px;
                vertical-align: top;
            }
            .cmr-checkout-wrapper .shop_table .product-total,
            .cmr-checkout-wrapper .shop_table .cart-subtotal td,
            .cmr-checkout-wrapper .shop_table .order-total td,
            .cmr-checkout-wrapper .shop_table tfoot th {
                text-align: right;
                font-weight: 500;
            }
            .cmr-checkout-wrapper .shop_table .cart-subtotal th,
            .cmr-checkout-wrapper .shop_table .shipping th {
                font-weight: 500;
                color: #6b7280;
            }
            .cmr-checkout-wrapper .shop_table .shipping td {
                text-align: right;
                color: #6b7280;
                font-weight: 500;
            }
            .cmr-checkout-wrapper .shop_table .order-total {
                font-size: 18px;
                font-weight: 700;
                color: #111827;
            }
            .cmr-checkout-wrapper .shop_table .order-total th,
            .cmr-checkout-wrapper .shop_table .order-total td {
                border-bottom: none;
                padding-top: 20px;
            }

            /* ===== PAYMENT METHODS ===== */
            .cmr-checkout-wrapper #payment {
                background: transparent !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
            .cmr-checkout-wrapper #payment ul.payment_methods {
                padding: 0 !important;
                border: none !important;
                margin-bottom: 20px;
            }
            .cmr-checkout-wrapper #payment ul.payment_methods li {
                list-style: none;
                margin-bottom: 12px;
                border: 1px solid #e5e7eb;
                border-radius: 4px;
                padding: 14px;
            }
            .cmr-checkout-wrapper #payment div.payment_box {
                background: #fef2f2;
                padding: 15px;
                border-radius: 4px;
                font-size: 14px;
                color: #991b1b;
                margin-top: 10px;
                border: 1px solid #fca5a5;
            }
            .cmr-checkout-wrapper #payment div.payment_box::before {
                display: none;
            }

            /* ===== PLACE ORDER & RETURN TO CART ===== */
            .cmr-checkout-wrapper .woocommerce-checkout #payment .place-order {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 20px;
                padding-top: 20px;
                border-top: 1px solid #e5e7eb;
                margin-top: 20px;
            }
            .cmr-checkout-wrapper .cmr-checkout-return-btn {
                color: #4b5563 !important;
                text-decoration: none !important;
                font-weight: 500 !important;
                font-size: 14px !important;
                white-space: nowrap;
                flex-shrink: 0;
            }
            .cmr-checkout-wrapper .cmr-checkout-return-btn:hover {
                color: #111827 !important;
            }
            .cmr-checkout-wrapper .woocommerce-checkout #payment .place-order button {
                background: #4b23a0;
                color: #ffffff;
                padding: 14px 40px;
                border-radius: 50px;
                font-size: 15px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: background 0.2s;
                white-space: nowrap;
            }
            .cmr-checkout-wrapper .woocommerce-checkout #payment .place-order button:hover {
                background: #391880;
            }

            /* ===== TERMS & POLICY ===== */
            .cmr-checkout-wrapper .woocommerce-terms-and-conditions-wrapper {
                font-size: 13px;
                color: #9ca3af;
                border-top: 1px solid #e5e7eb;
                padding-top: 15px;
                margin-top: 15px;
            }
            .cmr-checkout-wrapper .woocommerce-terms-and-conditions-wrapper a {
                color: #6b46c1;
            }

            /* ===== CHECKBOX: Use same address ===== */
            .cmr-checkout-wrapper #ship-to-different-address label {
                position: static !important;
                font-size: 14px !important;
                color: #6b46c1 !important;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            /* ===== WOOCOMMERCE NOTICES ===== */
            .cmr-checkout-wrapper .woocommerce-error,
            .cmr-checkout-wrapper .woocommerce-message,
            .cmr-checkout-wrapper .woocommerce-info {
                border-radius: 4px;
                padding: 14px;
                margin-bottom: 20px;
                font-size: 14px;
            }

            /* ===== RESPONSIVE ===== */
            @media (max-width: 992px) {
                .cmr-checkout-wrapper .woocommerce-checkout {
                    flex-direction: column;
                }
                .cmr-checkout-wrapper #customer_details,
                .cmr-checkout-wrapper #order_review {
                    width: 100%;
                    flex: none;
                }
                .cmr-checkout-wrapper #order_review {
                    position: static;
                }
            }
            @media (max-width: 576px) {
                .cmr-checkout-wrapper .woocommerce-checkout .form-row-first,
                .cmr-checkout-wrapper .woocommerce-checkout .form-row-last {
                    float: none;
                    width: 100%;
                }
            }
        </style>
        
        <div class="cmr-checkout-wrapper">
            <a href="/cart/" class="cmr-back-link">&larr; Back to Cart</a>
            
            <?php 
            // Render the classic WooCommerce checkout
            echo do_shortcode('[woocommerce_checkout]'); 
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_custom_checkout', 'cmr_custom_checkout_shortcode' );

add_action( 'woocommerce_review_order_before_submit', 'cmr_checkout_add_return_to_cart_button' );
function cmr_checkout_add_return_to_cart_button() {
    echo '<a href="' . esc_url( wc_get_cart_url() ) . '" class="cmr-checkout-return-btn" style="color: #4b5563; text-decoration: none; font-weight: 500; font-size: 14px;">&larr; Return to Cart</a>';
}

/**
 * Add product image to the classic WooCommerce checkout order review table.
 */
add_filter( 'woocommerce_cart_item_name', 'cmr_checkout_product_image_classic', 10, 3 );
function cmr_checkout_product_image_classic( $name, $cart_item, $cart_item_key ) {
    // Only apply on the checkout page order review table
    if ( ! is_checkout() || is_wc_endpoint_url() ) {
        return $name;
    }
    
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
    
    // Get the product thumbnail
    $thumbnail = $_product->get_image( array( 65, 65 ), array( 'class' => 'cmr-checkout-thumb', 'style' => 'width: 65px; height: 65px; object-fit: cover; border-radius: 4px; margin-right: 15px; vertical-align: top; float: left;' ) );
    
    // Get a short description (excerpt)
    $excerpt = $_product->get_short_description();
    if ( $excerpt ) {
        $excerpt = wp_trim_words( strip_tags( $excerpt ), 10, '...' );
        $excerpt_html = '<div style="font-size: 12px; color: #6b7280; margin-top: 5px; line-height: 1.4;">' . $excerpt . '</div>';
    } else {
        $excerpt_html = '';
    }

    return $thumbnail . '<div class="cmr-checkout-item-details" style="display:inline-block; vertical-align:top; width: calc(100% - 80px); line-height: 1.4;">' . $name . $excerpt_html . '</div><div style="clear:both;"></div>';
}

/**
 * Remove the order notes / additional information field.
 */
add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );
