<?php
/**
 * CMR Modern Checkout Shortcode
 * Replicates the WooCommerce Block Checkout visual style using the classic checkout form.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_modern_checkout_shortcode' ) ) {
    function cmr_modern_checkout_shortcode() {
        ob_start();
        ?>
        <style>
            .cmr-modern-checkout-wrapper {
                max-width: 1280px;
                margin: 40px auto;
                padding: 0 20px;
                font-family: 'Instrument Sans', sans-serif !important;
                color: #111827;
            }
            .cmr-modern-checkout-wrapper .woocommerce-checkout {
                display: flex;
                flex-wrap: wrap;
                gap: 60px;
                align-items: flex-start;
            }
            
            /* Columns layout */
            .cmr-modern-checkout-wrapper .col2-set {
                width: 100%;
                float: none;
                margin: 0;
            }
            .cmr-modern-checkout-wrapper .col2-set .col-1, 
            .cmr-modern-checkout-wrapper .col2-set .col-2 {
                float: none;
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .cmr-modern-checkout-wrapper #customer_details {
                flex: 1 1 55%;
                min-width: 300px;
            }
            .cmr-modern-checkout-wrapper #order_review_heading {
                display: none;
            }
            .cmr-modern-checkout-wrapper #order_review {
                flex: 1 1 35%;
                min-width: 300px;
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 4px;
                padding: 0;
                position: sticky;
                top: 20px;
            }
            
            /* Section Headings */
            .cmr-modern-checkout-wrapper h3,
            .cmr-modern-checkout-wrapper .cmr-section-heading {
                font-size: 20px;
                font-weight: 700;
                color: #111827;
                margin-top: 30px;
                margin-bottom: 20px;
            }
            .cmr-modern-checkout-wrapper h3.cmr-section-heading-small,
            .cmr-modern-checkout-wrapper h3#cmr-shipping-heading {
                font-size: 18px;
            }
            .cmr-modern-checkout-wrapper #customer_details > h3:first-child {
                margin-top: 0;
            }
            
            /* Form fields styling */
            .cmr-modern-checkout-wrapper .form-row {
                margin-bottom: 15px !important;
                padding: 0 !important;
                position: relative;
            }
            .cmr-modern-checkout-wrapper .form-row::after {
                content: "";
                display: table;
                clear: both;
            }
            
            /* Exact Layout match */
            .cmr-modern-checkout-wrapper #billing_first_name_field,
            .cmr-modern-checkout-wrapper #shipping_first_name_field,
            .cmr-modern-checkout-wrapper #billing_city_field,
            .cmr-modern-checkout-wrapper #shipping_city_field,
            .cmr-modern-checkout-wrapper #billing_postcode_field,
            .cmr-modern-checkout-wrapper #shipping_postcode_field {
                width: calc(50% - 7.5px) !important;
                float: left !important;
                clear: both !important;
            }
            
            .cmr-modern-checkout-wrapper #billing_last_name_field,
            .cmr-modern-checkout-wrapper #shipping_last_name_field,
            .cmr-modern-checkout-wrapper #billing_state_field,
            .cmr-modern-checkout-wrapper #shipping_state_field,
            .cmr-modern-checkout-wrapper #billing_phone_field,
            .cmr-modern-checkout-wrapper #shipping_phone_field {
                width: calc(50% - 7.5px) !important;
                float: right !important;
                clear: none !important;
            }

            .cmr-modern-checkout-wrapper #billing_country_field,
            .cmr-modern-checkout-wrapper #shipping_country_field,
            .cmr-modern-checkout-wrapper #billing_address_1_field,
            .cmr-modern-checkout-wrapper #shipping_address_1_field,
            .cmr-modern-checkout-wrapper #billing_address_2_field,
            .cmr-modern-checkout-wrapper #shipping_address_2_field,
            .cmr-modern-checkout-wrapper #billing_email_field {
                width: 100% !important;
                clear: both !important;
            }
            
            /* Floating Labels */
            .cmr-modern-checkout-wrapper label {
                position: absolute;
                top: 19px;
                left: 15px;
                font-size: 14px;
                color: #6b7280;
                font-weight: 400;
                z-index: 2;
                margin: 0;
                pointer-events: none;
                line-height: 1;
                transition: all 0.2s ease-out;
            }
            .cmr-modern-checkout-wrapper .form-row.float-active label {
                top: 8px;
                font-size: 11px;
                font-weight: 500;
            }
            .cmr-modern-checkout-wrapper label .required { display: none; }
            .cmr-modern-checkout-wrapper .checkbox,
            .cmr-modern-checkout-wrapper .woocommerce-form__label-for-checkbox {
                position: static !important;
                font-size: 13px !important;
                color: #4b5563 !important;
                display: flex !important;
                align-items: center;
                gap: 10px;
                cursor: pointer;
            }
            .cmr-modern-checkout-wrapper input[type="checkbox"] {
                width: 18px;
                height: 18px;
                accent-color: #6b46c1;
                margin: 0;
                cursor: pointer;
            }

            /* Inputs */
            .cmr-modern-checkout-wrapper input[type="text"],
            .cmr-modern-checkout-wrapper input[type="email"],
            .cmr-modern-checkout-wrapper input[type="tel"],
            .cmr-modern-checkout-wrapper input[type="password"],
            .cmr-modern-checkout-wrapper select,
            .cmr-modern-checkout-wrapper textarea {
                width: 100%;
                height: 56px;
                padding: 24px 14px 8px 14px;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                font-size: 14px;
                color: #111827;
                background: #fff;
                outline: none;
                box-sizing: border-box;
                transition: border-color 0.2s;
            }
            .cmr-modern-checkout-wrapper input:focus,
            .cmr-modern-checkout-wrapper textarea:focus {
                border-color: #6b46c1;
                box-shadow: 0 0 0 1px #6b46c1;
            }
            .cmr-modern-checkout-wrapper input::placeholder,
            .cmr-modern-checkout-wrapper textarea::placeholder {
                color: transparent !important;
            }

            /* Select2 Overrides */
            .cmr-modern-checkout-wrapper .select2-container {
                width: 100% !important;
            }
            .cmr-modern-checkout-wrapper .select2-container--default .select2-selection--single {
                height: 56px;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                padding: 24px 14px 8px 14px;
                background: #fff;
            }
            .cmr-modern-checkout-wrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 1;
                padding: 0;
                color: #111827;
                font-size: 14px;
            }
            .cmr-modern-checkout-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 54px;
                right: 10px;
            }

            /* Order Summary */
            .cmr-modern-checkout-wrapper .cmr-order-summary-header {
                padding: 20px 24px;
                border-bottom: 1px solid #f3f4f6;
            }
            .cmr-modern-checkout-wrapper .cmr-order-summary-header h3 {
                margin: 0;
                font-size: 18px;
                font-weight: 600;
            }
            
            .cmr-modern-checkout-wrapper .shop_table {
                width: 100%;
                border-collapse: collapse;
                margin: 0;
            }
            .cmr-modern-checkout-wrapper .shop_table thead { display: none; }
            .cmr-modern-checkout-wrapper .shop_table th,
            .cmr-modern-checkout-wrapper .shop_table td {
                padding: 16px 24px;
                border-bottom: 1px solid #f3f4f6;
                text-align: left;
                font-size: 13px;
                color: #4b5563;
                font-weight: 400;
            }
            .cmr-modern-checkout-wrapper .shop_table .product-name {
                display: flex;
                align-items: flex-start;
                gap: 15px;
            }
            .cmr-modern-checkout-wrapper .cmr-product-thumb {
                width: 50px;
                height: 50px;
                flex-shrink: 0;
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 4px;
                position: relative;
            }
            .cmr-modern-checkout-wrapper .cmr-product-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 3px;
            }
            .cmr-modern-checkout-wrapper .cmr-qty-badge {
                position: absolute;
                top: -8px;
                right: -8px;
                background: #6b7280;
                color: white;
                font-size: 11px;
                font-weight: 600;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .cmr-modern-checkout-wrapper .product-total,
            .cmr-modern-checkout-wrapper .cart-subtotal td,
            .cmr-modern-checkout-wrapper .shipping td,
            .cmr-modern-checkout-wrapper .order-total td {
                text-align: right;
            }
            .cmr-modern-checkout-wrapper .order-total th {
                font-size: 16px;
                font-weight: 600;
                color: #111827;
                border-bottom: none;
            }
            .cmr-modern-checkout-wrapper .order-total td {
                font-size: 18px;
                font-weight: 700;
                color: #111827;
                border-bottom: none;
            }

            /* Custom Shipping Options Block (Moved left) */
            .cmr-modern-checkout-wrapper #cmr-shipping-block {
                margin-top: 30px;
                clear: both;
            }
            .cmr-modern-checkout-wrapper .cmr-shipping-methods {
                border: 1px solid #d1d5db;
                border-radius: 4px;
                padding: 16px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                cursor: pointer;
            }
            .cmr-modern-checkout-wrapper .cmr-shipping-methods label {
                position: static !important;
                font-size: 14px;
                color: #111827;
                display: flex;
                align-items: center;
                gap: 12px;
                pointer-events: auto;
                cursor: pointer;
            }
            .cmr-modern-checkout-wrapper .cmr-shipping-methods input[type="radio"] {
                width: 18px;
                height: 18px;
                accent-color: #111827;
                margin: 0;
            }

            /* Payment Block */
            .cmr-modern-checkout-wrapper #payment {
                background: transparent !important;
                border: none !important;
                padding: 0 !important;
                clear: both;
            }
            .cmr-modern-checkout-wrapper #payment ul.payment_methods {
                padding: 0 !important;
                border: none !important;
                margin: 0 0 20px 0;
            }
            .cmr-modern-checkout-wrapper #payment ul.payment_methods li {
                list-style: none;
                margin-bottom: 10px;
            }
            
            /* Error Notices */
            .cmr-modern-checkout-wrapper .woocommerce-error {
                background: #fef2f2;
                border: 1px solid #fca5a5;
                color: #991b1b;
                border-radius: 4px;
                padding: 16px 20px;
                font-size: 14px;
                margin-bottom: 20px;
                list-style: none;
                clear: both;
            }
            .cmr-modern-checkout-wrapper .woocommerce-error::before {
                display: none;
            }
            
            /* Buttons */
            .cmr-modern-checkout-wrapper .place-order {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 20px;
                margin-top: 30px;
            }
            .cmr-modern-checkout-wrapper .cmr-return-cart {
                color: #4b5563;
                text-decoration: none;
                font-weight: 500;
                font-size: 14px;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .cmr-modern-checkout-wrapper .cmr-return-cart:hover { color: #111827; }
            .cmr-modern-checkout-wrapper .place-order button {
                background: #4b23a0;
                color: #fff;
                border: none;
                padding: 14px 32px;
                border-radius: 50px;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s;
            }
            .cmr-modern-checkout-wrapper .place-order button:hover {
                background: #391880;
            }

            /* Coupon accordion */
            .cmr-modern-checkout-wrapper .checkout_coupon {
                display: none !important;
            }
            .cmr-modern-checkout-wrapper .cmr-coupon-accordion {
                padding: 16px 24px;
                border-bottom: 1px solid #f3f4f6;
                cursor: pointer;
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 13px;
                color: #4b5563;
            }
            .cmr-modern-checkout-wrapper .cmr-coupon-content {
                display: none;
                padding: 16px 24px;
                border-bottom: 1px solid #f3f4f6;
                background: #f9fafb;
            }
            .cmr-modern-checkout-wrapper .cmr-coupon-content input {
                width: calc(100% - 100px) !important;
                height: 44px !important;
                padding: 10px 14px !important;
                display: inline-block;
            }
            .cmr-modern-checkout-wrapper .cmr-coupon-content button {
                width: 90px;
                height: 44px;
                background: #e5e7eb;
                border: none;
                border-radius: 4px;
                font-weight: 600;
                color: #4b5563;
                cursor: pointer;
                float: right;
            }

            /* Terms */
            .cmr-modern-checkout-wrapper .woocommerce-terms-and-conditions-wrapper {
                font-size: 12px;
                color: #9ca3af;
                margin-top: 30px;
            }
            
            @media (max-width: 992px) {
                .cmr-modern-checkout-wrapper .woocommerce-checkout {
                    flex-direction: column;
                }
            }
        </style>
        
        <div class="cmr-modern-checkout-wrapper">
            <?php echo do_shortcode('[woocommerce_checkout]'); ?>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                
                // --- DOM Restructuring Engine ---
                var customerDetails = document.getElementById('customer_details');
                var orderReview = document.getElementById('order_review');
                
                if (!customerDetails || !orderReview) return;

                // 1. Rename heading
                var detailsHeading = customerDetails.querySelector('h3');
                if (detailsHeading) detailsHeading.innerText = 'Shipping address';
                
                // 2. Build Order Summary Card Header
                var summaryHeader = document.createElement('div');
                summaryHeader.className = 'cmr-order-summary-header';
                summaryHeader.innerHTML = '<h3>Order summary</h3>';
                orderReview.insertBefore(summaryHeader, orderReview.firstChild);

                // 3. Move Shipping Options to Left Column
                function extractShipping() {
                    var shippingRows = document.querySelectorAll('.woocommerce-shipping-totals');
                    var shippingBlock = document.getElementById('cmr-shipping-block');
                    
                    if (!shippingBlock) {
                        shippingBlock = document.createElement('div');
                        shippingBlock.id = 'cmr-shipping-block';
                        shippingBlock.innerHTML = '<h3 id="cmr-shipping-heading" class="cmr-section-heading">Shipping options</h3><div class="cmr-shipping-content"></div>';
                        customerDetails.appendChild(shippingBlock);
                    }
                    
                    var contentBox = shippingBlock.querySelector('.cmr-shipping-content');
                    contentBox.innerHTML = ''; // clear old
                    
                    shippingRows.forEach(function(row) {
                        var ul = row.querySelector('#shipping_method');
                        if (ul) {
                            var methods = ul.querySelectorAll('li');
                            methods.forEach(function(li) {
                                var wrapper = document.createElement('div');
                                wrapper.className = 'cmr-shipping-methods';
                                
                                var input = li.querySelector('input');
                                var label = li.querySelector('label');
                                
                                if (input && label) {
                                    var priceMatch = label.innerHTML.match(/<span[^>]*>(.*?)<\/span>/);
                                    var priceText = priceMatch ? priceMatch[0] : '';
                                    label.innerHTML = label.innerText.replace(priceText, '');
                                    
                                    var newLabel = document.createElement('label');
                                    newLabel.appendChild(input);
                                    newLabel.appendChild(document.createTextNode(' ' + label.innerText.trim()));
                                    
                                    var priceSpan = document.createElement('span');
                                    priceSpan.innerHTML = priceText || 'FREE';
                                    priceSpan.style.fontWeight = '600';
                                    priceSpan.style.fontSize = '13px';
                                    
                                    wrapper.appendChild(newLabel);
                                    wrapper.appendChild(priceSpan);
                                    contentBox.appendChild(wrapper);
                                }
                            });
                        }
                        row.style.display = 'none';
                    });
                }
                
                // 4. Move Payment Options and Email to Left Column
                function movePayment() {
                    var payment = document.getElementById('payment');
                    if (payment && payment.parentNode !== customerDetails) {
                        var paymentHeading = document.getElementById('cmr-payment-heading');
                        if (!paymentHeading) {
                            paymentHeading = document.createElement('h3');
                            paymentHeading.id = 'cmr-payment-heading';
                            paymentHeading.className = 'cmr-section-heading';
                            paymentHeading.innerText = 'Payment options';
                            customerDetails.appendChild(paymentHeading);
                        }
                        customerDetails.appendChild(payment);
                        
                        var placeOrder = payment.querySelector('.place-order');
                        if (placeOrder && !placeOrder.querySelector('.cmr-return-cart')) {
                            var returnLink = document.createElement('a');
                            returnLink.href = '/cart/';
                            returnLink.className = 'cmr-return-cart';
                            returnLink.innerHTML = '&larr; Return to Cart';
                            placeOrder.insertBefore(returnLink, placeOrder.firstChild);
                        }
                        
                        // Move Email to very bottom
                        var emailRow = document.getElementById('billing_email_field');
                        if (emailRow) {
                            var contactHeading = document.getElementById('cmr-contact-heading');
                            if (!contactHeading) {
                                contactHeading = document.createElement('h3');
                                contactHeading.id = 'cmr-contact-heading';
                                contactHeading.className = 'cmr-section-heading cmr-section-heading-small';
                                contactHeading.innerText = 'Contact information';
                                customerDetails.appendChild(contactHeading);
                            }
                            customerDetails.appendChild(emailRow);
                            
                            var guestText = document.getElementById('cmr-guest-text');
                            if (!guestText) {
                                guestText = document.createElement('p');
                                guestText.id = 'cmr-guest-text';
                                guestText.innerText = 'You are currently checking out as a guest.';
                                guestText.style.fontSize = '12px';
                                guestText.style.color = '#6b7280';
                                guestText.style.marginTop = '8px';
                                customerDetails.appendChild(guestText);
                            }
                        }
                    }
                }

                // 5. Build Add Coupon Accordion
                function buildCoupon() {
                    var table = document.querySelector('.shop_table');
                    var couponRow = document.querySelector('.checkout_coupon');
                    if (table && couponRow && !document.querySelector('.cmr-coupon-accordion')) {
                        var accordion = document.createElement('div');
                        accordion.className = 'cmr-coupon-accordion';
                        accordion.innerHTML = '<span>Add coupons</span><span>&or;</span>';
                        
                        var content = document.createElement('div');
                        content.className = 'cmr-coupon-content';
                        content.innerHTML = '<input type="text" id="cmr_coupon_code" placeholder="Coupon code"> <button type="button" id="cmr_apply_coupon">Apply</button>';
                        
                        table.parentNode.insertBefore(accordion, table);
                        table.parentNode.insertBefore(content, table);
                        
                        accordion.addEventListener('click', function() {
                            content.style.display = content.style.display === 'block' ? 'none' : 'block';
                        });
                        
                        document.getElementById('cmr_apply_coupon').addEventListener('click', function(e) {
                            e.preventDefault();
                            var code = document.getElementById('cmr_coupon_code').value;
                            var originalInput = document.querySelector('.checkout_coupon input[name="coupon_code"]');
                            var originalBtn = document.querySelector('.checkout_coupon button[name="apply_coupon"]');
                            if (originalInput && originalBtn) {
                                originalInput.value = code;
                                originalBtn.click();
                            }
                        });
                    }
                }
                
                // 6. Floating Label Interactions
                function updateFloatingLabels() {
                    var rows = document.querySelectorAll('.cmr-modern-checkout-wrapper .form-row');
                    rows.forEach(function(row) {
                        var input = row.querySelector('input, textarea, select');
                        if (input && input.type !== 'checkbox' && input.type !== 'radio') {
                            if (input.value.trim() !== '' || document.activeElement === input || input.tagName === 'SELECT') {
                                row.classList.add('float-active');
                            } else {
                                row.classList.remove('float-active');
                            }
                        }
                    });
                }
                document.addEventListener('input', updateFloatingLabels);
                document.addEventListener('focusin', updateFloatingLabels);
                document.addEventListener('focusout', updateFloatingLabels);
                setTimeout(updateFloatingLabels, 100);

                // Initial setup
                extractShipping();
                movePayment();
                buildCoupon();

                // On WooCommerce AJAX update
                if (typeof jQuery !== 'undefined') {
                    jQuery(document).on('updated_checkout', function() {
                        extractShipping();
                        movePayment();
                        updateFloatingLabels();
                    });
                }
                
                // Add placeholder fake labels to inputs for floating effect
                var inputs = document.querySelectorAll('.cmr-modern-checkout-wrapper .form-row input, .cmr-modern-checkout-wrapper .form-row textarea, .cmr-modern-checkout-wrapper .form-row select');
                inputs.forEach(function(input) {
                    if (!input.placeholder && input.tagName !== 'SELECT' && input.type !== 'checkbox') {
                        input.placeholder = ' ';
                    }
                });
                
            }, 500); // 500ms delay to let woo load
        });
        </script>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_modern_checkout', 'cmr_modern_checkout_shortcode' );
