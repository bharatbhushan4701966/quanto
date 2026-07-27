<?php
add_action('wp_footer', function() {
    if (is_product()) {
        global $product;
        if (!$product) {
            $product = wc_get_product(get_the_ID());
        }
?>
<!-- Review Modal (Full Page) -->
<div id="cmr-review-modal" class="cmr-modal-overlay" style="display: none;">
	<div class="cmr-modal-content">
		<div class="cmr-modal-close" onclick="document.getElementById('cmr-review-modal').style.display='none';">Close &times;</div>
		
		<div class="cmr-modal-product-info">
			<div class="cmr-modal-product-img">
				<?php echo woocommerce_get_product_thumbnail('woocommerce_thumbnail'); ?>
			</div>
			<div class="cmr-modal-product-details">
				<h3><?php echo esc_html(get_the_title()); ?></h3>
				<p class="cmr-publisher">CyberMedia Research (CMR)</p>
				<p class="cmr-sku"><span>SKU:</span> <?php echo esc_html($product->get_sku()); ?></p>
			</div>
		</div>

		<div class="cmr-modal-form-wrapper">
			<?php comments_template( 'woocommerce/single-product-reviews' ); ?>
			<div class="cmr-modal-terms-text">By submitting, you agree to <a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a></div>
		</div>
	</div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var modal = document.getElementById("cmr-review-modal");
        if (modal && modal.parentNode !== document.body) {
            document.body.appendChild(modal);
        }
    });
</script>

<?php
    }
});
