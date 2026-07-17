<?php
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<div class="custom-cart-grid">
    <div class="cart-left-col">
        <!-- Cart Items Form -->
        <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
            <?php do_action( 'woocommerce_before_cart_table' ); ?>
            
            <div class="cart-header-title">
                <h3><i class="fa-solid fa-lock"></i> Shopping Cart (<?php echo WC()->cart->get_cart_contents_count(); ?> items)</h3>
            </div>

            <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                <tbody>
                    <?php do_action( 'woocommerce_before_cart_contents' ); ?>

                    <?php
                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                        $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                            ?>
                            <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                                <td class="product-thumbnail">
                                <?php
                                $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                                if ( ! $product_permalink ) {
                                    echo $thumbnail; // PHPCS: XSS ok.
                                } else {
                                    printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
                                }
                                ?>
                                </td>

                                <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
                                <?php
                                if ( ! $product_permalink ) {
                                    echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                                } else {
                                    echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                                }

                                do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

                                // Author / Category
                                $author = get_post_meta( $product_id, 'product_author', true );
                                if ( ! $author ) {
                                    $author = 'CyberMedia Research (CMR)';
                                }
                                echo '<div class="cart-product-author">' . esc_html( $author ) . '</div>';

                                // SKU
                                if ( $_product->get_sku() ) {
                                    echo '<div class="cart-product-sku">SKU: ' . esc_html( $_product->get_sku() ) . '</div>';
                                }

                                // Backorder notification
                                if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                                    echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
                                }
                                ?>
                                </td>

                                <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
                                    <?php
                                        echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                                    ?>
                                </td>

                                <td class="product-remove">
                                    <?php
                                        echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                            'woocommerce_cart_item_remove_link',
                                            sprintf(
                                                '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="fa-regular fa-trash-can"></i> Remove</a>',
                                                esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                                esc_html__( 'Remove this item', 'woocommerce' ),
                                                esc_attr( $product_id ),
                                                esc_attr( $_product->get_sku() )
                                            ),
                                            $cart_item_key
                                        );
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>

                    <?php do_action( 'woocommerce_cart_contents' ); ?>
                    
                    <tr>
                        <td colspan="4" class="actions">
                            <button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>
                            <?php do_action( 'woocommerce_cart_actions' ); ?>
                            <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                        </td>
                    </tr>

                    <?php do_action( 'woocommerce_after_cart_contents' ); ?>
                </tbody>
            </table>
            <?php do_action( 'woocommerce_after_cart_table' ); ?>
        </form>

        <!-- Pre-Checkout Login Form -->
        <div class="cart-login-prompt" id="cart-login-prompt" style="<?php if ( is_user_logged_in() ) echo 'display: none;'; ?>">
            <h3>Login to Continue Checkout</h3>
            <p>Please sign in to save your cart and proceed to secure payment.</p>
            
            <form class="woocommerce-form woocommerce-form-login login" method="post" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" style="border: none !important; padding: 0 !important; margin: 0 !important;">
                <?php do_action( 'woocommerce_login_form_start' ); ?>

                <label for="username"><?php esc_html_e( 'EMAIL ADDRESS', 'woocommerce' ); ?></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" required />

                <label for="password"><?php esc_html_e( 'PASSWORD', 'woocommerce' ); ?></label>
                <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required />

                <?php do_action( 'woocommerce_login_form' ); ?>

                <input type="hidden" name="redirect" value="<?php echo esc_url( wc_get_checkout_url() ); ?>" />
                <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                <button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Sign In ↗', 'woocommerce' ); ?></button>

                <?php do_action( 'woocommerce_login_form_end' ); ?>
            </form>
            <div class="cart-login-links">
                <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="forgot-password-link">Forgot Password?</a>
            </div>
            
            <div class="login-separator"><span>OR</span></div>
            
            <p class="create-account-text">Create an account to gain instant access to your premium market reports and insights.</p>
            
            <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="create-account-btn">Create Account ↗</a>
            
            <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px dashed #e5e7eb;">
                <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" onclick="var p=document.querySelectorAll('.wc-proceed-to-checkout a.checkout-button, .cart-collaterals .checkout-button, .cmr-checkout-btn'); p.forEach(function(b){ b.removeAttribute('disabled'); b.style.setProperty('opacity', '1', 'important'); b.style.setProperty('pointer-events', 'auto', 'important'); b.style.setProperty('cursor', 'pointer', 'important'); b.style.setProperty('filter', 'none', 'important'); }); document.getElementById('cart-login-prompt').style.setProperty('display', 'none', 'important'); window.location.href='<?php echo esc_url( wc_get_checkout_url() ); ?>'; return false;" style="font-size: 14px; font-weight: 600; color: #4820B0; text-decoration: underline;">Or continue as guest without creating an account ↓</a>
            </div>
        </div>
    </div>
    
    <div class="cart-right-col">
        <!-- Promo Code Box -->
        <?php if ( wc_coupons_enabled() ) { ?>
            <div class="cart-promo-box">
                <h4><i class="fa-solid fa-tag"></i> Promo Code</h4>
                <form class="checkout_coupon woocommerce-form-coupon" method="post">
                    <div class="coupon">
                        <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> 
                        <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?></button>
                    </div>
                </form>
            </div>
        <?php } ?>

        <div class="cart-collaterals">
            <?php
                /**
                 * Cart collaterals hook.
                 *
                 * @hooked woocommerce_cross_sell_display
                 * @hooked woocommerce_cart_totals - 10
                 */
                do_action( 'woocommerce_cart_collaterals' );
            ?>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var isLoggedOut = !document.body.classList.contains('logged-in') || <?php echo ! is_user_logged_in() ? 'true' : 'false'; ?>;
            var promptBox = document.getElementById('cart-login-prompt');
            var proceedBtns = document.querySelectorAll('.wc-proceed-to-checkout a.checkout-button, .cart-collaterals .checkout-button, .cmr-checkout-btn');
            if (isLoggedOut && promptBox) {
                promptBox.style.setProperty('display', 'block', 'important');
                proceedBtns.forEach(function(btn) {
                    btn.setAttribute('disabled', 'disabled');
                    btn.style.setProperty('opacity', '0.5', 'important');
                    btn.style.setProperty('pointer-events', 'none', 'important');
                    btn.style.setProperty('cursor', 'not-allowed', 'important');
                    btn.style.setProperty('filter', 'grayscale(1)', 'important');
                });
            } else if (promptBox) {
                promptBox.style.setProperty('display', 'none', 'important');
            }
        });
        </script>
        
        <div class="checkout-trust-badges">
            <strong>Guaranteed safe checkout</strong>
            <div class="trust-icons">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b0/Apple_Pay_logo.svg" alt="Apple Pay">
                <img src="https://upload.wikimedia.org/wikipedia/commons/f/f2/Google_Pay_Logo.svg" alt="Google Pay">
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard">
                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/American_Express_logo_%282018%29.svg" alt="AMEX">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa">
            </div>
        </div>
    </div>
</div>

<?php if ( ! is_user_logged_in() ) : ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var checkoutBtn = document.querySelector('.cart-collaterals .checkout-button');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function(e) {
            var loginBox = document.getElementById('cart-login-prompt');
            if (loginBox) {
                e.preventDefault();
                loginBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                loginBox.style.transition = 'box-shadow 0.3s ease, border-color 0.3s ease';
                loginBox.style.boxShadow = '0 0 0 4px rgba(72, 32, 176, 0.25)';
                loginBox.style.borderColor = '#4820B0';
                setTimeout(function() {
                    loginBox.style.boxShadow = '';
                    loginBox.style.borderColor = '';
                }, 1500);
            }
        });
    }
});
</script>
<?php endif; ?>

<?php do_action( 'woocommerce_after_cart' ); ?>
