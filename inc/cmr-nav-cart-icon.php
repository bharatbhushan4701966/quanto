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
    <?php
    return ob_get_clean();
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
    <?php
    return ob_get_clean();
}

// ============================================================
// IMPORTANT: JavaScript is loaded via wp_footer, NOT inside
// the shortcodes, because Elementor caches shortcode output
// and would prevent updated JS from ever reaching the browser.
// ============================================================
add_action('wp_footer', 'cmr_nav_cart_badge_js', 99);
function cmr_nav_cart_badge_js() {
    // Only load on pages where WooCommerce is active
    if (!class_exists('WooCommerce')) return;
    
    // Pass the LIVE cart count from PHP to JS (this runs outside Elementor cache)
    $live_count = WC()->cart ? count(WC()->cart->get_cart()) : 0;
    ?>
    <script>
    (function() {
        // 1. Immediately set the correct count from PHP (bypasses Elementor cache)
        var liveCount = <?php echo intval($live_count); ?>;
        var badges = document.querySelectorAll('.cmr-nav-cart-badge-count');
        badges.forEach(function(b) { b.textContent = liveCount; });

        // 2. On cart page: count items from DOM after WooCommerce updates the table
        function updateBadgeFromCartPage() {
            var rows = document.querySelectorAll('.woocommerce-cart-form .cart_item, .woocommerce-cart-form__cart-item, tr.cart_item');
            var count = rows.length;
            // If we're on the cart page and there's an empty cart notice, count is 0
            if (document.querySelector('.cart-empty, .woocommerce-cart-form') !== null) {
                if (document.querySelector('.cart-empty')) {
                    count = 0;
                }
                var allBadges = document.querySelectorAll('.cmr-nav-cart-badge-count');
                allBadges.forEach(function(b) { b.textContent = count; });
            }
        }

        // 3. Listen for WooCommerce jQuery events
        if (typeof jQuery !== 'undefined') {
            jQuery(function($) {
                // These fire when items are added/removed/updated
                $(document.body).on('added_to_cart removed_from_cart updated_wc_div updated_cart_totals wc_cart_emptied wc_fragments_refreshed', function() {
                    setTimeout(updateBadgeFromCartPage, 500);
                });
            });
        }

        // 4. MutationObserver on the main content area as ultimate fallback
        function startObserving() {
            var target = document.querySelector('.woocommerce') || document.querySelector('#content') || document.querySelector('main');
            if (target) {
                var observer = new MutationObserver(function() {
                    setTimeout(updateBadgeFromCartPage, 500);
                });
                observer.observe(target, { childList: true, subtree: true });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startObserving);
        } else {
            startObserving();
        }

        // 5. Scroll color change for home page cart icon
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
    })();
    </script>
    <?php
}
