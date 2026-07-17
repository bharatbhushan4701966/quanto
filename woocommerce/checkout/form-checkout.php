<?php
defined( 'ABSPATH' ) || exit;

// Remove the default coupon form at the top
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
    echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
    return;
}
?>

<style>
/* Critical Inline Checkout Grid Rules (Bypasses all CDN/Browser CSS Caches) */
.cmr-checkout-wrap {
    display: grid !important;
    grid-template-columns: 1fr 380px !important;
    gap: 32px !important;
    max-width: 1100px !important;
    margin: 40px auto !important;
    padding: 0 20px !important;
    align-items: start !important;
}
@media (max-width: 900px) {
    .cmr-checkout-wrap {
        grid-template-columns: 1fr !important;
    }
}
.woocommerce-billing-fields h3 {
    display: none !important;
}
.woocommerce-billing-fields__field-wrapper {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 16px !important;
    margin-bottom: 20px !important;
    align-items: end !important;
}
/* Explicit Ordering & Column Spans */
#billing_first_name_field { order: 1 !important; grid-column: 1 !important; }
#billing_last_name_field  { order: 2 !important; grid-column: 2 !important; }
#billing_address_1_field  { order: 3 !important; grid-column: 1 / -1 !important; }
#billing_address_2_field  { display: none !important; }
#billing_city_field       { order: 4 !important; grid-column: 1 !important; }
#billing_state_field      { order: 5 !important; grid-column: 2 !important; }
#billing_country_field    { order: 6 !important; grid-column: 1 !important; }
#billing_postcode_field   { order: 7 !important; grid-column: 2 !important; }
#billing_phone_field      { order: 8 !important; grid-column: 1 / -1 !important; }
#billing_email_field      { order: 9 !important; grid-column: 1 / -1 !important; }

.cmr-checkout-wrap .form-row {
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    float: none !important;
}
.cmr-label,
.cmr-checkout-wrap .form-row label {
    display: block !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.08em !important;
    color: #374151 !important;
    margin-bottom: 8px !important;
    text-transform: uppercase !important;
}
.cmr-checkout-wrap .optional {
    display: none !important;
}
.woocommerce-shipping-fields {
    display: none !important;
}
/* Make sure Order Summary right column stays visible and styled */
.cmr-checkout-right {
    width: 100% !important;
    background: #fff;
}
</style>

<form name="checkout" method="post" class="cmr-checkout-wrap woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">


    <div class="cmr-checkout-left">
        <div class="cmr-checkout-card">
            <h2 class="cmr-section-title">Shipping address</h2>
            
            <?php if ( $checkout->get_checkout_fields() ) : ?>
                <div class="cmr-billing-fields">
                    <?php do_action( 'woocommerce_checkout_billing' ); ?>
                </div>
            <?php endif; ?>

            <p class="cmr-terms-notice">
                By proceeding with your purchase you agree to our
                <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>
            </p>
        </div>
    </div>

    <div class="cmr-checkout-right">
        <!-- Promo Code -->
        <?php if ( wc_coupons_enabled() ) : ?>
        <div class="cmr-promo-box">
            <div class="cmr-promo-header">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                Promo Code
            </div>
            <?php
            foreach ( WC()->cart->get_coupons() as $code => $coupon ) {
                echo '<div class="cmr-applied-coupon">';
                echo '<span class="cmr-coupon-code">' . esc_html( $code ) . '</span>';
                echo '<span class="cmr-coupon-applied">Applied <span class="dot">&#9679;</span></span>';
                echo '<a href="' . esc_url( add_query_arg( 'remove_coupon', rawurlencode( $code ), wc_get_cart_url() ) ) . '" class="cmr-remove-coupon">Remove &times;</a>';
                echo '</div>';
            }
            ?>
            <div class="cmr-coupon-hints">Try: CMR10, CMRINDIA15, or FIRST20</div>
            
            <div class="cmr-coupon-form">
                <input type="text" name="coupon_code" class="cmr-coupon-input" placeholder="Enter coupon code" id="coupon_code">
                <button type="button" class="cmr-coupon-apply" id="apply_custom_coupon">Apply</button>
            </div>
        </div>
        <?php endif; ?>

        <!-- Product Summary -->
        <div class="cmr-order-box">
            <h4 class="cmr-summary-title">Order Summary</h4>
            <div id="order_review" class="woocommerce-checkout-review-order">
                <?php do_action( 'woocommerce_checkout_order_review' ); ?>
            </div>
        </div>

        <!-- Trust Badges -->
        <div class="cmr-trust-badges">
            <p class="cmr-trust-label">Guaranteed safe checkout</p>
            <div class="cmr-trust-icons">
                <div class="cmr-icon-pill">
                    <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/05/ApplePay.svg" alt="Apple Pay">
                </div>
                <div class="cmr-icon-pill">
                    <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/05/GooglePay.svg" alt="Google Pay">
                </div>
                <div class="cmr-icon-pill">
                    <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/05/Mastercard.svg" alt="Mastercard">
                </div>
                <div class="cmr-icon-pill">
                    <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/05/Amex.svg" alt="Amex">
                </div>
                <div class="cmr-icon-pill">
                    <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/05/PayPal.svg" alt="PayPal">
                </div>
                <div class="cmr-icon-pill">
                    <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/05/Visa.svg" alt="Visa">
                </div>
            </div>
        </div>
    </div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

<script>
jQuery(document).ready(function($) {
    // Make our custom coupon button work
    $('#apply_custom_coupon').on('click', function(e) {
        e.preventDefault();
        var code = $('#coupon_code').val();
        if(code) {
            // Append a hidden coupon form inside the main checkout form and submit it
            var data = {
                security: wc_checkout_params.apply_coupon_nonce,
                coupon_code: code
            };
            
            $.ajax({
                type: 'POST',
                url: wc_checkout_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'apply_coupon' ),
                data: data,
                success: function( code ) {
                    $('.woocommerce-error, .woocommerce-message').remove();
                    if ( code ) {
                        $('form.checkout').before( code );
                        $( document.body ).trigger( 'applied_coupon_in_checkout', [ $('#coupon_code').val() ] );
                        $( document.body ).trigger( 'update_checkout', { update_shipping_method: false } );
                    }
                },
                dataType: 'html'
            });
        }
    });
});
</script>
