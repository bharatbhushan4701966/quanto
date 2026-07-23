<?php
// Shortcode for Nav Cart Icon
add_shortcode('cmr_nav_cart', 'cmr_nav_cart_shortcode');
function cmr_nav_cart_shortcode() {
    $cart_count = class_exists('WooCommerce') ? count(WC()->cart->get_cart()) : 0;
    
    ob_start();
    ?>
    <style>
    .cmr-nav-cart-container {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        width: 40px;
        height: 40px;
        color: #333; /* Black by default on all inner pages */
        transition: color 0.3s ease;
    }
    
    /* Make it white ONLY on the home page initially */
    body.home .cmr-nav-cart-container,
    body.home-page .cmr-nav-cart-container {
        color: #fff;
    }

    .cmr-nav-cart-container:hover {
        color: #4820B0 !important; /* Purple on hover */
    }
    
    /* Force black when header is sticky (overrides home page white) */
    .elementor-sticky--effects .cmr-nav-cart-container,
    .is-sticky .cmr-nav-cart-container,
    header.sticky .cmr-nav-cart-container,
    .intel-nav-fixed-js .cmr-nav-cart-container {
        color: #333 !important;
    }

    .cmr-nav-cart-container svg {
        width: 22px;
        height: 22px;
    }

    .cmr-nav-cart-badge {
        position: absolute;
        bottom: 5px;
        right: 4px;
        background: #00BFBC; /* Cyan/teal */
        color: #fff; /* white */
        font-size: 11px;
        font-family: Arial, sans-serif;
        font-weight: 800;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid transparent;
        line-height: 1;
    }
    </style>
    
    <a href="<?php echo function_exists('wc_get_cart_url') ? esc_url(wc_get_cart_url()) : '#'; ?>" class="cmr-nav-cart-container">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 8V6a4 4 0 0 0-8 0v2M4 9a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div class="cmr-nav-cart-badge">
            <span class="cmr-nav-cart-badge-count"><?php echo esc_html($cart_count); ?></span>
        </div>
    </a>

    <script>
    // Change icon color on scroll (matching search icon logic)
    window.addEventListener('scroll', function() {
        var carts = document.querySelectorAll('.cmr-nav-cart-container');
        carts.forEach(function(cart) {
            if (window.scrollY > 50) {
                cart.style.setProperty('color', '#333', 'important');
            } else {
                cart.style.setProperty('color', '#fff', 'important');
            }
        });
    });

    // Force badge update on WooCommerce cart events
    jQuery(document).ready(function($) {
        $(document.body).on('added_to_cart removed_from_cart updated_cart_totals updated_wc_div', function() {
            $.ajax({
                url: '<?php echo esc_js(home_url('/?wc-ajax=cmr_get_cart_count')); ?>',
                type: 'GET',
                cache: false,
                data: {
                    t: new Date().getTime()
                },
                success: function(response) {
                    if (response && response.success) {
                        $('.cmr-nav-cart-badge-count').text(response.data.count);
                    }
                }
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

// Custom AJAX endpoint to get cart count instantly
add_action('wc_ajax_cmr_get_cart_count', 'cmr_ajax_get_cart_count');
function cmr_ajax_get_cart_count() {
    if (class_exists('WooCommerce') && WC()->cart) {
        wp_send_json_success(array('count' => count(WC()->cart->get_cart())));
    }
    wp_send_json_error();
}

// Add fragment for AJAX update so the cart count updates when items are added
add_filter('woocommerce_add_to_cart_fragments', 'cmr_nav_cart_fragment_badge');
function cmr_nav_cart_fragment_badge($fragments) {
    $cart_count = class_exists('WooCommerce') ? count(WC()->cart->get_cart()) : 0;
    $fragments['div.cmr-nav-cart-badge'] = '<div class="cmr-nav-cart-badge"><span class="cmr-nav-cart-badge-count">' . esc_html($cart_count) . '</span></div>';
    return $fragments;
}

// Shortcode for Nav Cart Icon (Always Black)
add_shortcode('cmr_nav_cart_black', 'cmr_nav_cart_black_shortcode');
function cmr_nav_cart_black_shortcode() {
    $cart_count = class_exists('WooCommerce') ? count(WC()->cart->get_cart()) : 0;
    
    ob_start();
    ?>
    <style>
    .cmr-nav-cart-container-black {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        width: 40px;
        height: 40px;
        color: #333 !important;
        transition: color 0.3s ease;
    }
    .cmr-nav-cart-container-black:hover {
        color: #4820B0 !important;
    }
    .cmr-nav-cart-container-black svg {
        width: 22px;
        height: 22px;
    }
    .cmr-nav-cart-badge {
        position: absolute;
        bottom: 5px;
        right: 4px;
        background: #00BFBC; /* Cyan/teal */
        color: #fff; /* white */
        font-size: 11px;
        font-family: Arial, sans-serif;
        font-weight: 800;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid transparent;
        line-height: 1;
    }
    </style>
    
    <a href="<?php echo function_exists('wc_get_cart_url') ? esc_url(wc_get_cart_url()) : '#'; ?>" class="cmr-nav-cart-container-black">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 8V6a4 4 0 0 0-8 0v2M4 9a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div class="cmr-nav-cart-badge">
            <span class="cmr-nav-cart-badge-count"><?php echo esc_html($cart_count); ?></span>
        </div>
    </a>
    <script>
    jQuery(document).ready(function($) {
        $(document.body).on('added_to_cart removed_from_cart updated_cart_totals updated_wc_div', function() {
            $.ajax({
                url: '<?php echo esc_js(home_url('/?wc-ajax=cmr_get_cart_count')); ?>',
                type: 'GET',
                cache: false,
                data: {
                    t: new Date().getTime()
                },
                success: function(response) {
                    if (response && response.success) {
                        $('.cmr-nav-cart-badge-count').text(response.data.count);
                    }
                }
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
