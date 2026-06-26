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
            .cmr-checkout-wrapper {
                max-width: 1280px;
                margin: 40px auto;
                padding: 0 20px;
                font-family: 'Instrument Sans', sans-serif !important;
                color: #111827;
            }

            .cmr-checkout-wrapper .cmr-back-link {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                color: #374151;
                font-weight: 600;
                font-size: 15px;
                text-decoration: none;
                margin-bottom: 30px;
            }

            .cmr-checkout-wrapper .cmr-back-link:hover {
                color: #000;
            }

            /* Two Column Checkout Layout */
            .cmr-checkout-wrapper .woocommerce-checkout {
                display: flex;
                gap: 40px;
                flex-wrap: wrap;
                align-items: flex-start;
            }
            
            /* Left Column: Forms */
            .cmr-checkout-wrapper #customer_details {
                flex: 2;
                min-width: 300px;
            }
            
            .cmr-checkout-wrapper .woocommerce-billing-fields h3,
            .cmr-checkout-wrapper .woocommerce-shipping-fields h3,
            .cmr-checkout-wrapper .woocommerce-additional-fields h3 {
                font-size: 20px;
                font-weight: 700;
                margin-bottom: 25px;
                border-bottom: 2px solid #f3f4f6;
                padding-bottom: 10px;
            }

            /* Right Column: Order Review */
            .cmr-checkout-wrapper #order_review_heading {
                display: none; /* Hide default heading */
            }
            
            .cmr-checkout-wrapper #order_review {
                flex: 1;
                min-width: 300px;
                background: #ffffff;
                border: 1px solid #e5e7eb;
                padding: 30px;
                border-radius: 4px;
            }

            /* Form Elements Styling */
            .cmr-checkout-wrapper .woocommerce-checkout input[type="text"],
            .cmr-checkout-wrapper .woocommerce-checkout input[type="email"],
            .cmr-checkout-wrapper .woocommerce-checkout input[type="tel"],
            .cmr-checkout-wrapper .woocommerce-checkout input[type="password"],
            .cmr-checkout-wrapper .woocommerce-checkout select,
            .cmr-checkout-wrapper .woocommerce-checkout textarea {
                width: 100%;
                padding: 12px 15px;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                font-size: 15px;
                outline: none;
                background: #fff;
            }
            
            .cmr-checkout-wrapper .woocommerce-checkout input:focus,
            .cmr-checkout-wrapper .woocommerce-checkout select:focus,
            .cmr-checkout-wrapper .woocommerce-checkout textarea:focus {
                border-color: #6b46c1;
            }
            
            .cmr-checkout-wrapper .woocommerce-checkout label {
                display: block;
                font-weight: 600;
                margin-bottom: 8px;
                font-size: 14px;
                color: #374151;
            }
            
            .cmr-checkout-wrapper .woocommerce-checkout .form-row {
                margin-bottom: 20px;
            }
            
            /* Select2 Overrides (WooCommerce defaults to Select2) */
            .cmr-checkout-wrapper .select2-container--default .select2-selection--single {
                height: 47px;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                display: flex;
                align-items: center;
            }
            .cmr-checkout-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 45px;
            }

            /* Order Table Styling */
            .cmr-checkout-wrapper .shop_table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 25px;
            }
            
            .cmr-checkout-wrapper .shop_table th,
            .cmr-checkout-wrapper .shop_table td {
                padding: 15px 0;
                border-bottom: 1px solid #e5e7eb;
                text-align: left;
            }
            
            .cmr-checkout-wrapper .shop_table th {
                font-weight: 600;
                color: #4b5563;
            }
            
            .cmr-checkout-wrapper .shop_table .product-total,
            .cmr-checkout-wrapper .shop_table .cart-subtotal td,
            .cmr-checkout-wrapper .shop_table .order-total td {
                text-align: right;
            }
            
            .cmr-checkout-wrapper .shop_table .order-total {
                font-size: 20px;
                font-weight: 700;
                color: #111827;
            }
            
            .cmr-checkout-wrapper .shop_table .order-total th,
            .cmr-checkout-wrapper .shop_table .order-total td {
                border-bottom: none;
                padding-top: 25px;
            }

            /* Payment Methods */
            .cmr-checkout-wrapper #payment {
                background: transparent;
                border-radius: 0;
            }
            
            .cmr-checkout-wrapper #payment ul.payment_methods {
                padding: 0;
                border-bottom: 1px solid #e5e7eb;
                margin-bottom: 20px;
            }
            
            .cmr-checkout-wrapper #payment ul.payment_methods li {
                list-style: none;
                margin-bottom: 15px;
            }
            
            .cmr-checkout-wrapper #payment div.payment_box {
                background: #f9fafb;
                padding: 15px;
                border-radius: 4px;
                font-size: 14px;
                color: #4b5563;
                margin-top: 10px;
            }
            .cmr-checkout-wrapper #payment div.payment_box::before {
                display: none; /* Hide woo default triangle */
            }

            /* Checkout Button */
            .cmr-checkout-wrapper .woocommerce-checkout #payment .place-order button {
                background: #4b23a0;
                color: #ffffff;
                width: 100%;
                padding: 16px;
                border-radius: 50px;
                font-size: 16px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: background 0.2s;
                margin-top: 10px;
            }
            
            .cmr-checkout-wrapper .woocommerce-checkout #payment .place-order button:hover {
                background: #391880;
            }

            @media (max-width: 992px) {
                .cmr-checkout-wrapper .woocommerce-checkout {
                    flex-direction: column;
                }
                .cmr-checkout-wrapper #customer_details,
                .cmr-checkout-wrapper #order_review {
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
