<?php
add_action('wp_footer', function() {
    if (is_product()) {
        global $product;
        if (!$product) {
            $product = wc_get_product(get_the_ID());
        }
        $title     = esc_js(get_the_title());
        $sku       = esc_js($product->get_sku());
        $thumb     = get_the_post_thumbnail_url(get_the_ID(), 'woocommerce_thumbnail');
        $thumb     = $thumb ? esc_url($thumb) : '';
?>
<style>
#cmr-review-modal-overlay {
    display: none;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background: rgba(248, 250, 252, 0.97) !important;
    z-index: 2147483647 !important;
    align-items: center;
    justify-content: center;
    overflow-y: auto;
}
#cmr-review-modal-overlay.cmr-open {
    display: flex !important;
}
#cmr-review-modal-box {
    background: #fff;
    width: 100%;
    max-width: 560px;
    padding: 40px;
    position: relative;
    box-shadow: 0 8px 40px rgba(0,0,0,0.10);
    border-radius: 12px;
    margin: auto;
}
#cmr-review-modal-close {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 14px;
    color: #64748b;
    cursor: pointer;
    font-weight: 500;
    background: none;
    border: none;
    padding: 0;
    font-family: inherit;
    letter-spacing: -0.2px;
}
#cmr-review-modal-close:hover { color: #000; }
.cmr-rmpi {
    display: flex;
    gap: 18px;
    margin-bottom: 25px;
    margin-top: 10px;
    align-items: flex-start;
}
.cmr-rmpi img {
    width: 88px;
    height: auto;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    flex-shrink: 0;
}
.cmr-rmpi-details h3 {
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 6px 0;
    line-height: 1.3;
    letter-spacing: -0.4px;
    color: #0f172a;
}
.cmr-rmpi-details .cmr-rpub {
    font-size: 15px;
    color: #64748b;
    margin: 0 0 8px 0;
    letter-spacing: -0.2px;
}
.cmr-rmpi-details .cmr-rsku {
    font-size: 13px;
    color: #0f172a;
    margin: 0;
    font-weight: 600;
}
.cmr-rmpi-details .cmr-rsku span { color: #64748b; font-weight: 400; }

/* Form inner styles */
#cmr-review-modal-box .comment-form-rating label,
#cmr-review-modal-box .comment-form-comment label {
    display: block;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 12px;
    color: #000;
    letter-spacing: -0.3px;
}
#cmr-review-modal-box .comment-form-rating { margin-bottom: 22px; }
#cmr-review-modal-box .stars { font-size: 30px; }
#cmr-review-modal-box .stars span {
    display: flex !important;
    flex-direction: row !important;
    justify-content: flex-start !important;
}
#cmr-review-modal-box .stars a { 
    margin-right: 4px; 
    display: inline-block;
    position: relative;
    text-indent: -9999px;
    width: 20px;
}
#cmr-review-modal-box .stars a::before {
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    width: 20px;
    height: 20px;
    line-height: 1;
    font-family: star !important;
    content: "\73" !important;
    color: #cbd5e1 !important; /* Light gray for empty stars */
    text-indent: 0;
    font-variant: normal;
    text-transform: none;
    font-weight: 400;
}
#cmr-review-modal-box .stars a:hover::before,
#cmr-review-modal-box .stars:hover a::before,
#cmr-review-modal-box .stars.selected a.active::before,
#cmr-review-modal-box .stars.selected a:not(.active)::before {
    content: "\53" !important;
    color: #f59e0b !important; /* Orange for solid stars */
}
#cmr-review-modal-box .stars a:hover ~ a::before,
#cmr-review-modal-box .stars.selected a.active ~ a::before {
    content: "\73" !important;
    color: #cbd5e1 !important;
}
#cmr-review-modal-box .comment-form-comment textarea {
    width: 100%;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px;
    min-height: 120px;
    font-family: inherit;
    font-size: 14px;
    color: #475569;
    resize: vertical;
    box-sizing: border-box;
}
#cmr-review-modal-box #respond,
#cmr-review-modal-box .woocommerce-Reviews { margin: 0; }
#cmr-review-modal-box #comments { display: none !important; }
#cmr-review-modal-box #reply-title,
#cmr-review-modal-box .comment-notes,
#cmr-review-modal-box .comment-form-author,
#cmr-review-modal-box .comment-form-email,
#cmr-review-modal-box .logged-in-as { display: none !important; }
#cmr-review-modal-box .cmr-terms {
    font-size: 13px;
    color: #475569;
    margin: 18px 0;
}
#cmr-review-modal-box .cmr-terms a {
    color: #0f172a;
    text-decoration: underline;
    font-weight: 500;
}
#cmr-review-modal-box .form-submit { margin: 0; }
#cmr-review-modal-box .form-submit input[type="submit"] {
    width: 100%;
    background: #0f172a;
    color: #fff;
    padding: 15px;
    border-radius: 30px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: background 0.2s;
    letter-spacing: -0.1px;
    font-family: inherit;
}
#cmr-review-modal-box .form-submit input[type="submit"]:hover { background: #000; }

#cmr-review-modal-box .stars a::before,
#cmr-review-modal-box .stars a:hover::before,
#cmr-review-modal-box .stars a:focus::before,
#cmr-review-modal-box .stars a.active::before {
    font-family: star !important;
}
</style>

<div id="cmr-review-modal-overlay">
    <div id="cmr-review-modal-box">
        <button id="cmr-review-modal-close" onclick="document.getElementById('cmr-review-modal-overlay').classList.remove('cmr-open'); document.body.style.overflow='';">Close &times;</button>

        <div class="cmr-rmpi">
            <?php if ($thumb): ?>
            <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
            <?php endif; ?>
            <div class="cmr-rmpi-details">
                <h3><?php echo esc_html(get_the_title()); ?></h3>
                <p class="cmr-rpub">CyberMedia Research (CMR)</p>
                <p class="cmr-rsku"><span>SKU: </span><?php echo esc_html($product->get_sku()); ?></p>
            </div>
        </div>

        <div class="cmr-modal-form-wrapper">
            <?php comments_template('/woocommerce/single-product-reviews.php'); ?>
            <div class="cmr-terms">By submitting, you agree to <a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a></div>
        </div>
    </div>
</div>

<script>
(function() {
    // Move overlay to body immediately so it's never trapped in a transformed container
    document.addEventListener('DOMContentLoaded', function() {
        var overlay = document.getElementById('cmr-review-modal-overlay');
        if (overlay && overlay.parentNode !== document.body) {
            document.body.appendChild(overlay);
        }

        // Wire up the "Write a Review" button
        document.querySelectorAll('.cmr-lr-btn, [data-open-review-modal]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var overlay = document.getElementById('cmr-review-modal-overlay');
                if (overlay) {
                    overlay.classList.add('cmr-open');
                    document.body.style.overflow = 'hidden';
                }
            });
        });

        // Close on overlay background click
        var overlay = document.getElementById('cmr-review-modal-overlay');
        if (overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    overlay.classList.remove('cmr-open');
                    document.body.style.overflow = '';
                }
            });
        }

        // Support old onclick attribute style trigger
        window.cmrOpenReviewModal = function() {
            var overlay = document.getElementById('cmr-review-modal-overlay');
            if (overlay) {
                overlay.classList.add('cmr-open');
                document.body.style.overflow = 'hidden';
            }
        };
    });
})();
</script>
<?php
    }
});
