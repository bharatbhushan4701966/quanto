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
            .cmr-checkout-wrapper .woocommerce-checkout .form-row {
                margin-bottom: 16px !important;
            }
            .cmr-checkout-wrapper .woocommerce-checkout .form-row::after {
                content: "";
                display: table;
                clear: both;
            }
            
            /* ===== FIELD LAYOUT MATCHING BLOCK CHECKOUT ===== */
            .cmr-checkout-wrapper #billing_city_field,
            .cmr-checkout-wrapper #shipping_city_field,
            .cmr-checkout-wrapper #billing_postcode_field,
            .cmr-checkout-wrapper #shipping_postcode_field {
                width: 48% !important;
                float: left !important;
                clear: both !important;
            }
            .cmr-checkout-wrapper #billing_state_field,
            .cmr-checkout-wrapper #shipping_state_field,
            .cmr-checkout-wrapper #billing_phone_field,
            .cmr-checkout-wrapper #shipping_phone_field {
                width: 48% !important;
                float: right !important;
                clear: none !important;
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
            .cmr-checkout-wrapper .woocommerce-checkout #billing_address_2_field input::placeholder,
            .cmr-checkout-wrapper .woocommerce-checkout #shipping_address_2_field input::placeholder {
                color: #9ca3af !important;
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
                border: none !important;
                border-bottom: 1px solid #e5e7eb !important;
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
                clear: both !important;
                width: 100%;
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
            .cmr-checkout-wrapper #ship-to-different-address {
                clear: both;
                display: block;
                width: 100%;
                margin-top: 20px;
                margin-bottom: 20px;
            }
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
                padding: 16px 20px;
                margin-bottom: 20px;
                font-size: 14px;
                background: #fef2f2;
                border: 1px solid #fca5a5;
                color: #991b1b;
            }
            .cmr-checkout-wrapper .woocommerce-error::before,
            .cmr-checkout-wrapper .woocommerce-message::before,
            .cmr-checkout-wrapper .woocommerce-info::before {
                display: none !important;
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
            
            /* ===== SUMMARY STEP (JS INJECTED) ===== */
            .cmr-checkout-wrapper .cmr-address-summary-box {
                border: 1px solid #e5e7eb;
                border-radius: 4px;
                padding: 20px;
                margin-bottom: 30px;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                background: #fff;
            }
            .cmr-checkout-wrapper .cmr-address-summary-content {
                color: #4b5563;
                font-size: 14px;
                line-height: 1.6;
            }
            .cmr-checkout-wrapper .cmr-address-summary-name {
                color: #111827;
                font-weight: 600;
                margin-bottom: 5px;
            }
            .cmr-checkout-wrapper .cmr-edit-address-btn {
                color: #6b7280;
                font-size: 14px;
                text-decoration: none;
                font-weight: 500;
                cursor: pointer;
            }
            .cmr-checkout-wrapper .cmr-edit-address-btn:hover {
                color: #111827;
            }
            .cmr-checkout-wrapper .cmr-save-address-btn {
                background: #f9fafb;
                border: 1px solid #d1d5db;
                color: #374151;
                padding: 10px 20px;
                border-radius: 4px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                margin-top: 10px;
                display: inline-block;
                width: auto;
            }
            .cmr-checkout-wrapper .cmr-save-address-btn:hover {
                background: #f3f4f6;
            }
        </style>
        
        <div class="cmr-checkout-wrapper">
            <a href="/cart/" class="cmr-back-link">&larr; Back to Cart</a>
            
            <?php 
            // Render the classic WooCommerce checkout
            echo do_shortcode('[woocommerce_checkout]'); 
            ?>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var checkoutWrapper = document.querySelector('.cmr-checkout-wrapper');
            if(!checkoutWrapper) return;

            // Wait a moment for Woo scripts to initialize
            setTimeout(function() {
                var billingFieldsWrapper = document.querySelector('.woocommerce-billing-fields__field-wrapper');
                var billingHeading = document.querySelector('.woocommerce-billing-fields h3');
                
                if (billingFieldsWrapper && billingHeading) {
                    // Create Save Button
                    var saveBtn = document.createElement('button');
                    saveBtn.type = 'button';
                    saveBtn.className = 'cmr-save-address-btn';
                    saveBtn.innerText = 'Save & Continue';
                    billingFieldsWrapper.appendChild(saveBtn);
                    
                    // Create Summary Box (Hidden initially)
                    var summaryBox = document.createElement('div');
                    summaryBox.className = 'cmr-address-summary-box';
                    summaryBox.style.display = 'none';
                    
                    var summaryContent = document.createElement('div');
                    summaryContent.className = 'cmr-address-summary-content';
                    
                    var editBtn = document.createElement('a');
                    editBtn.className = 'cmr-edit-address-btn';
                    editBtn.innerText = 'Edit';
                    editBtn.href = '#';
                    
                    summaryBox.appendChild(summaryContent);
                    summaryBox.appendChild(editBtn);
                    
                    // Insert summary box after heading
                    billingHeading.parentNode.insertBefore(summaryBox, billingHeading.nextSibling);
                    
                    // Save Button Logic
                    saveBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        var fName = document.getElementById('billing_first_name') ? document.getElementById('billing_first_name').value : '';
                        var lName = document.getElementById('billing_last_name') ? document.getElementById('billing_last_name').value : '';
                        var address1 = document.getElementById('billing_address_1') ? document.getElementById('billing_address_1').value : '';
                        var address2 = document.getElementById('billing_address_2') ? document.getElementById('billing_address_2').value : '';
                        var city = document.getElementById('billing_city') ? document.getElementById('billing_city').value : '';
                        
                        var stateSelect = document.getElementById('billing_state');
                        var state = '';
                        if(stateSelect) {
                            if(stateSelect.tagName === 'SELECT') {
                                state = stateSelect.options[stateSelect.selectedIndex] ? stateSelect.options[stateSelect.selectedIndex].text : '';
                            } else {
                                state = stateSelect.value;
                            }
                        }
                        
                        var postcode = document.getElementById('billing_postcode') ? document.getElementById('billing_postcode').value : '';
                        
                        var countrySelect = document.getElementById('billing_country');
                        var country = '';
                        if(countrySelect && countrySelect.tagName === 'SELECT') {
                            country = countrySelect.options[countrySelect.selectedIndex] ? countrySelect.options[countrySelect.selectedIndex].text : '';
                        }
                        
                        var phone = document.getElementById('billing_phone') ? document.getElementById('billing_phone').value : '';
                        
                        var fullAddressArray = [address1, address2, postcode, state, country, phone].filter(Boolean);
                        var fullAddressStr = fullAddressArray.join(', ');
                        
                        summaryContent.innerHTML = '<div class="cmr-address-summary-name">' + fName + ' ' + lName + '</div>' + 
                                                   '<div>' + fullAddressStr + '</div>';
                                                   
                        billingFieldsWrapper.style.display = 'none';
                        summaryBox.style.display = 'flex';
                    });
                    
                    // Edit Button Logic
                    editBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        summaryBox.style.display = 'none';
                        billingFieldsWrapper.style.display = 'block';
                    });
                }
                
                // Move Payment block to left column inside customer details
                function movePayment() {
                    var payment = document.getElementById('payment');
                    var customerDetails = document.getElementById('customer_details');
                    if(payment && customerDetails && payment.parentNode !== customerDetails) {
                        // Add Payment options heading if it doesn't exist
                        if (!document.getElementById('cmr-payment-heading')) {
                            var heading = document.createElement('h3');
                            heading.id = 'cmr-payment-heading';
                            heading.innerText = 'Payment options';
                            heading.style.marginTop = '30px';
                            heading.style.clear = 'both';
                            heading.style.display = 'block';
                            customerDetails.appendChild(heading);
                        }
                        customerDetails.appendChild(payment);
                    }
                }
                movePayment();
                if (typeof jQuery !== 'undefined') {
                    jQuery(document).on('updated_checkout', function() {
                        movePayment();
                    });
                }
                
            }, 500); // 500ms delay to ensure select2 is loaded
        });
        </script>
        
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
