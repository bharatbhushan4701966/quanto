<?php
/**
 * Review order table template override for CMR Checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-checkout-review-order-table">

    <?php
    do_action( 'woocommerce_review_order_before_cart_contents' );

    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
            ?>
            <div class="cmr-checkout-product-card <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                <div class="cmr-product-thumb">
                    <?php 
                    $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( array( 75, 104 ) ), $cart_item, $cart_item_key );
                    echo $thumbnail;
                    ?>
                </div>
                <div class="cmr-product-details">
                    <h3 class="cmr-product-name"><?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ) . '&nbsp;'; ?></h3>
                    <p class="cmr-product-brand"><?php
                        $author_id = get_post_field( 'post_author', $_product->get_id() );
                        $author_name = $author_id ? get_the_author_meta( 'display_name', $author_id ) : '';
                        if ( ! $author_name || $author_name === 'admin' ) {
                            echo 'CyberMedia Research (CMR)';
                        } else {
                            echo esc_html( $author_name );
                        }
                    ?></p>
                </div>
            </div>
            <?php
        }
    }

    do_action( 'woocommerce_review_order_after_cart_contents' );
    ?>

    <h3 class="cmr-summary-heading">Order Summary</h3>

    <div class="cmr-summary-list">
        <div class="cmr-summary-row subtotal">
            <span class="cmr-row-label">Subtotal</span>
            <span class="cmr-row-value nowrap"><?php wc_cart_totals_subtotal_html(); ?></span>
        </div>

        <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
            <div class="cmr-summary-row coupon-discount">
                <span class="cmr-row-label">Discount (<?php echo esc_html( $code ); ?>)</span>
                <span class="cmr-row-value nowrap discount-green"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
            </div>
        <?php endforeach; ?>

        <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
            <?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
                <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
                    <div class="cmr-summary-row tax">
                        <span class="cmr-row-label"><?php echo esc_html( $tax->label ); ?></span>
                        <span class="cmr-row-value nowrap"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="cmr-summary-row tax">
                    <span class="cmr-row-label"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
                    <span class="cmr-row-value nowrap"><?php wc_cart_totals_taxes_total_html(); ?></span>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
            <div class="cmr-summary-row fee">
                <span class="cmr-row-label"><?php echo esc_html( $fee->name ); ?></span>
                <span class="cmr-row-value nowrap"><?php wc_cart_totals_fee_html( $fee ); ?></span>
            </div>
        <?php endforeach; ?>

        <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

        <div class="cmr-summary-row total-row">
            <span class="cmr-row-label total-label">Total</span>
            <span class="cmr-row-value nowrap total-amount"><?php wc_cart_totals_order_total_html(); ?></span>
        </div>

        <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
    </div>

</div>
