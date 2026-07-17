<?php
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
    echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
    return;
}
?>

<form name="checkout" method="post" class="cmr-checkout-wrap woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

    <div class="cmr-checkout-left">
        <div class="cmr-checkout-card">
            <h2 class="cmr-section-title">Shipping address</h2>

            <?php
            // Output each billing field manually so we can group them
            $fields = $checkout->get_checkout_fields( 'billing' );

            // NAME ROW (first + last side by side)
            echo '<div class="cmr-field-group">';
            echo '<label class="cmr-label">NAME</label>';
            echo '<div class="cmr-two-col">';
            if ( isset( $fields['billing_first_name'] ) ) {
                woocommerce_form_field( 'billing_first_name', array_merge( $fields['billing_first_name'], array( 'class' => array( 'cmr-half' ), 'label' => '', 'placeholder' => 'First' ) ), $checkout->get_value( 'billing_first_name' ) );
            }
            if ( isset( $fields['billing_last_name'] ) ) {
                woocommerce_form_field( 'billing_last_name', array_merge( $fields['billing_last_name'], array( 'class' => array( 'cmr-half' ), 'label' => '', 'placeholder' => 'Last' ) ), $checkout->get_value( 'billing_last_name' ) );
            }
            echo '</div>';
            echo '</div>';

            // ADDRESS ROW (full width)
            if ( isset( $fields['billing_address_1'] ) ) {
                echo '<div class="cmr-field-group">';
                echo '<label class="cmr-label">ADDRESS</label>';
                woocommerce_form_field( 'billing_address_1', array_merge( $fields['billing_address_1'], array( 'class' => array( 'cmr-full' ), 'label' => '', 'placeholder' => 'Street address, P.O. box, company name' ) ), $checkout->get_value( 'billing_address_1' ) );
                echo '</div>';
            }

            // LOCATION (City, State, Country, Pincode in a 2x2 grid)
            echo '<div class="cmr-field-group">';
            echo '<label class="cmr-label">LOCATION</label>';
            echo '<div class="cmr-two-col">';
            if ( isset( $fields['billing_city'] ) ) {
                woocommerce_form_field( 'billing_city', array_merge( $fields['billing_city'], array( 'class' => array( 'cmr-half' ), 'label' => '', 'placeholder' => 'City' ) ), $checkout->get_value( 'billing_city' ) );
            }
            if ( isset( $fields['billing_state'] ) ) {
                woocommerce_form_field( 'billing_state', array_merge( $fields['billing_state'], array( 'class' => array( 'cmr-half' ), 'label' => '', 'placeholder' => 'State' ) ), $checkout->get_value( 'billing_state' ) );
            }
            echo '</div>';
            echo '<div class="cmr-two-col">';
            if ( isset( $fields['billing_country'] ) ) {
                woocommerce_form_field( 'billing_country', array_merge( $fields['billing_country'], array( 'class' => array( 'cmr-half' ), 'label' => '', 'placeholder' => 'Country' ) ), $checkout->get_value( 'billing_country' ) );
            }
            if ( isset( $fields['billing_postcode'] ) ) {
                woocommerce_form_field( 'billing_postcode', array_merge( $fields['billing_postcode'], array( 'class' => array( 'cmr-half' ), 'label' => '', 'placeholder' => 'Pincode' ) ), $checkout->get_value( 'billing_postcode' ) );
            }
            echo '</div>';
            echo '</div>';

            // EMAIL ADDRESS
            if ( isset( $fields['billing_email'] ) ) {
                echo '<div class="cmr-field-group">';
                echo '<label class="cmr-label">EMAIL ADDRESS</label>';
                woocommerce_form_field( 'billing_email', array_merge( $fields['billing_email'], array( 'class' => array( 'cmr-full' ), 'label' => '', 'placeholder' => 'alexander@botanical.com' ) ), $checkout->get_value( 'billing_email' ) );
                echo '</div>';
            }

            // PHONE NUMBER with prefix
            if ( isset( $fields['billing_phone'] ) ) {
                echo '<div class="cmr-field-group">';
                echo '<label class="cmr-label">PHONE NUMBER</label>';
                echo '<div class="cmr-phone-wrap">';
                echo '<span class="cmr-phone-prefix">+91</span>';
                woocommerce_form_field( 'billing_phone', array_merge( $fields['billing_phone'], array( 'class' => array( 'cmr-full' ), 'label' => '', 'placeholder' => '00000-00000' ) ), $checkout->get_value( 'billing_phone' ) );
                echo '</div>';
                echo '</div>';
            }
            ?>

            <p class="cmr-terms-notice">
                By proceeding with your purchase you agree to our
                <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>
            </p>
        </div>

        <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
        <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
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
            // Show applied coupons
            foreach ( WC()->cart->get_coupons() as $code => $coupon ) {
                echo '<div class="cmr-applied-coupon">';
                echo '<span class="cmr-coupon-code">' . esc_html( $code ) . '</span>';
                echo '<span class="cmr-coupon-applied">Applied <span class="dot">&#9679;</span></span>';
                echo '<a href="' . esc_url( add_query_arg( 'remove_coupon', rawurlencode( $code ), wc_get_cart_url() ) ) . '" class="cmr-remove-coupon">Remove &times;</a>';
                echo '</div>';
            }
            ?>
            <div class="cmr-coupon-hints">Try: CMR10, CMRINDIA15, or FIRST20</div>
            <form class="cmr-coupon-form" method="post">
                <input type="text" name="coupon_code" class="cmr-coupon-input" placeholder="Enter coupon code" id="coupon_code">
                <button type="submit" name="apply_coupon" class="cmr-coupon-apply">Apply</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Product Summary -->
        <div class="cmr-order-box">
            <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                $_product = $cart_item['data'];
                if ( ! $_product || ! $_product->exists() ) continue;
                $img = $_product->get_image( array( 60, 80 ) );
                ?>
                <div class="cmr-order-item">
                    <div class="cmr-order-item-img"><?php echo $img; ?></div>
                    <div class="cmr-order-item-info">
                        <span class="cmr-item-name"><?php echo esc_html( $_product->get_name() ); ?></span>
                        <span class="cmr-item-author">CyberMedia Research (CMR)</span>
                    </div>
                </div>
            <?php endforeach; ?>

            <h4 class="cmr-summary-title">Order Summary</h4>
            <table class="cmr-summary-table">
                <tr>
                    <td>Subtotal</td>
                    <td class="cmr-amount"><?php echo WC()->cart->get_cart_subtotal(); ?></td>
                </tr>
                <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                <tr>
                    <td>Discount (<?php echo esc_html( wc_format_coupon_discount_amount( $coupon, WC()->cart ) ); ?>)</td>
                    <td class="cmr-amount cmr-discount">-<?php echo wc_price( WC()->cart->get_coupon_discount_amount( $code ) ); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if ( WC()->cart->get_taxes_total() > 0 ) : ?>
                <tr>
                    <td>Tax</td>
                    <td class="cmr-amount"><?php echo wc_price( WC()->cart->get_taxes_total() ); ?></td>
                </tr>
                <?php endif; ?>
                <tr class="cmr-total-row">
                    <td><strong>Total</strong></td>
                    <td class="cmr-amount"><strong><?php echo WC()->cart->get_total(); ?></strong></td>
                </tr>
            </table>

            <!-- Payment section -->
            <div id="order_review" class="woocommerce-checkout-review-order" style="display:none;">
                <?php do_action( 'woocommerce_checkout_order_review' ); ?>
            </div>

            <button type="submit" class="cmr-checkout-btn button alt" name="woocommerce_checkout_place_order" id="place_order" value="Proceed to Checkout">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:8px;"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                Proceed to Checkout
            </button>
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
