<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'custom-product-container', $product ); ?>>

	<div class="custom-product-top-row">
		<!-- Left Column: Product Gallery / Image -->
		<div class="custom-product-image-col">
			<?php
			$image_id = $product->get_image_id();
			if ( $image_id ) {
				$image_url = wp_get_attachment_image_url( $image_id, 'full' );
				echo '<div class="custom-product-main-image"><img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $product->get_name() ) . '" /></div>';
			} else {
				echo '<div class="custom-product-main-image">' . wc_placeholder_img( 'full' ) . '</div>';
			}
			?>
		</div>

		<!-- Right Column: Product Info -->
		<div class="custom-product-info-col">
			<!-- Badge: New/Featured -->
			<?php
			$is_new = ( time() - get_the_time( 'U' ) < 30 * DAY_IN_SECONDS );
			if ( $product->is_featured() || $is_new ) {
				echo '<span class="custom-new-badge"><i class="fa-solid fa-star"></i> NEW</span>';
			}
			?>

			<!-- Category Link -->
			<div class="custom-product-category">
				<?php
				$categories = wp_get_post_terms( $product->get_id(), 'product_cat' );
				if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
					$cat = $categories[0];
					echo '<a href="' . esc_url( get_term_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a>';
				}
				?>
			</div>

			<!-- Title -->
			<h1 class="custom-product-title"><?php the_title(); ?></h1>

			<!-- Publisher/Author -->
			<div class="custom-product-author">
				<?php
				$author = get_post_meta( $product->get_id(), 'product_author', true );
				if ( ! $author ) {
					$author = $product->get_attribute( 'author' );
				}
				if ( ! $author ) {
					$author = 'CyberMedia Research (CMR)';
				}
				echo esc_html( $author );
				?>
			</div>

			<!-- Rating Row -->
			<div class="custom-product-rating-row">
				<?php
				$rating_count = $product->get_rating_count();
				$review_count = $product->get_review_count();
				$average      = $product->get_average_rating();
				
				// Re-use WooCommerce star-rating markup
				echo wc_get_rating_html( $average, $rating_count );
				?>
				<a href="#reviews" class="custom-review-count">
					(<?php echo esc_html( $review_count ); ?> Reviews)
				</a>
			</div>

			<!-- SKU -->
			<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
				<div class="custom-product-sku">
					SKU: <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? esc_html( $sku ) : esc_html__( 'N/A', 'woocommerce' ); ?></span>
				</div>
			<?php endif; ?>

			<!-- Price -->
			<div class="custom-product-price-display">
				<?php echo $product->get_price_html(); ?>
			</div>

			<!-- Action Buttons Row -->
			<div class="custom-product-actions-row">
				<!-- Download Button (WooCommerce Single Product Add to Cart Form) -->
				<div class="custom-download-button-wrapper">
					<?php woocommerce_template_single_add_to_cart(); ?>
				</div>

				<!-- Talk to Analyst Button (Opens Elementor Popup 7637) -->
				<a class="custom-talk-analyst-btn" href="#elementor-action%3Aaction%3Dpopup%3Aopen%26settings%3DeyJpZCI6Ijc2MzciLCJ0b2dnbGUiOmZhbHNlfQ%3D%3D">
					Talk to an Analyst <svg class="arrow-up-right" viewBox="0 0 15 15" width="14" height="14" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-left: 8px;"><path d="M2.04895 4.52205V3.54978C2.07104 3.12993 2.4025 2.79848 2.80025 2.79848L11.396 2.77638C11.8159 2.79848 12.1473 3.12993 12.1473 3.52768V12.1455C12.1473 12.5433 11.8159 12.8747 11.396 12.8968H10.4237C10.0039 12.8747 9.67244 12.5433 9.65035 12.1234L9.78293 6.90853L3.44106 13.2504C3.1317 13.5598 2.68976 13.5598 2.3804 13.2504L1.6733 12.5433C1.38603 12.256 1.36394 11.792 1.6733 11.4826L8.01516 5.14077L2.82234 5.29545C2.4025 5.27335 2.04895 4.96399 2.04895 4.52205Z" fill="currentColor"></path></svg>
				</a>
			</div>

			<!-- Guaranteed Safe Checkout -->
			<div class="custom-safe-checkout">
				<span class="safe-checkout-label">Guaranteed safe checkout</span>
				<div class="payment-icons">
					<!-- Apple Pay -->
					<div class="payment-icon" title="Apple Pay">
						<svg viewBox="0 0 50 32" width="38" height="24"><rect width="50" height="32" rx="4" fill="#000000"/><path d="M22.02 18.06c-.19.34-.48.62-.87.84s-.85.33-1.38.33h-1.63V13.8h1.61c.54 0 .99.11 1.34.33s.62.51.8 1c.06.16.09.35.09.56v1.37c0 .22-.05.42-.16.59v.41zm-2.25-2.7c0-.21-.06-.37-.18-.48s-.3-.16-.54-.16h-.77v1.89h.77c.24 0 .42-.06.54-.17s.18-.28.18-.49v-.59zm6.66.52c0 .41-.07.78-.2 1.1s-.32.61-.57.84-.53.4-.85.52-.67.17-1.04.17c-.55 0-1.02-.12-1.42-.36s-.7-.57-.91-1c-.13-.27-.2-.59-.2-.95v-.91c0-.42.07-.79.2-1.12s.32-.61.57-.84.54-.39.87-.51.68-.17 1.05-.17c.56 0 1.03.12 1.42.36s.7.57.91 1c.13.27.2.59.2.95v.93zm-.95-.91c0-.23-.04-.44-.12-.62s-.19-.34-.35-.46-.35-.18-.57-.18-.42.06-.57.18-.28.28-.36.46-.12.39-.12.62v.89c0 .24.04.45.12.63s.2.34.36.46.35.18.57.18.42-.06.57-.18.28-.28.35-.46.13-.39.13-.63v-.89zm5.34 2.87c.23-.23.44-.5.64-.8v.7h.85V15.5h-2.3v.85h1.37c-.12.21-.26.4-.41.56s-.32.3-.5.4-.38.15-.59.15c-.29 0-.52-.09-.7-.27s-.27-.47-.27-.88v-2.31h-.89v2.33c0 .66.15 1.15.45 1.48s.72.49 1.25.49c.45 0 .82-.12 1.1-.36z" fill="#FFFFFF"/></svg>
					</div>
					<!-- Google Pay -->
					<div class="payment-icon" title="Google Pay">
						<svg viewBox="0 0 50 32" width="38" height="24"><rect width="50" height="32" rx="4" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="1"/><path d="M15.5 12h-2.2v8h2.2c1.2 0 2.2-.4 3-1.1.8-.7 1.2-1.7 1.2-2.9s-.4-2.2-1.2-2.9c-.8-.7-1.8-1.1-3-1.1zm-.2 6.2h-.9v-4.4h.9c.7 0 1.2.2 1.6.6s.6.9.6 1.6-.2 1.2-.6 1.6-.9.6-1.6.6zm10.7-6.2c-1.3 0-2.3.5-3 1.4-.7-.9-1.7-1.4-3-1.4-1 0-1.8.3-2.4.9v-.8h-2.1v8h2.2v-4.4c0-.6.2-1.1.5-1.4.3-.3.7-.5 1.2-.5.8 0 1.2.5 1.2 1.5v4.8h2.2v-4.4c0-.6.2-1.1.5-1.4.3-.3.7-.5 1.2-.5.8 0 1.2.5 1.2 1.5v4.8h2.2v-5.2c0-2.6-1.2-3.9-3.7-3.9zm9.5 0h-2.2l-2.4 5.6-2.4-5.6h-2.2l3.6 8-1.4 3.1h2.2l4.8-11.1z" fill="#4B5563"/></svg>
					</div>
					<!-- Mastercard -->
					<div class="payment-icon" title="Mastercard">
						<svg viewBox="0 0 50 32" width="38" height="24"><rect width="50" height="32" rx="4" fill="#0F172A"/><circle cx="21" cy="16" r="9" fill="#EB001B"/><circle cx="29" cy="16" r="9" fill="#F79E1B" fill-opacity="0.85"/></svg>
					</div>
					<!-- American Express -->
					<div class="payment-icon" title="American Express">
						<svg viewBox="0 0 50 32" width="38" height="24"><rect width="50" height="32" rx="4" fill="#0070CD"/><path d="M12 11h2.5l1.2 2.6 1.2-2.6H19.5v10H17.5v-6.5l-2.3 5h-1.2l-2.3-5V21H12V11zm9.5 0h5v2h-3v2h2.5v2H23.5v2h3v2h-5V11zm6 0h2.5l2.2 4.4 2.2-4.4H35v10h-2V13.8l-3.3 6.7h-1.4l-3.3-6.7V21h-2V11z" fill="#FFFFFF"/></svg>
					</div>
					<!-- PayPal -->
					<div class="payment-icon" title="PayPal">
						<svg viewBox="0 0 50 32" width="38" height="24"><rect width="50" height="32" rx="4" fill="#003087"/><path d="M21.5 10.5h4.2c2.5 0 3.8 1.1 3.5 3.2-.4 2.6-2.2 3.8-4.7 3.8h-2.5l-1.1 5.5h-2.5l3.1-12.5zm1.9 4.8h1.8c1.1 0 1.8-.5 2-.1.5-.2.2-1.3-.6-1.3h-1.8l-.8 2.8z" fill="#0079C1"/><path d="M24.5 13.5h4.2c2.5 0 3.8 1.1 3.5 3.2-.4 2.6-2.2 3.8-4.7 3.8h-2.5l-1.1 5.5h-2.5l3.1-12.5zm1.9 4.8h1.8c1.1 0 1.8-.5 2-.1.5-.2.2-1.3-.6-1.3h-1.8l-.8 2.8z" fill="#00457C" style="mix-blend-mode:multiply"/></svg>
					</div>
					<!-- Visa -->
					<div class="payment-icon" title="Visa">
						<svg viewBox="0 0 50 32" width="38" height="24"><rect width="50" height="32" rx="4" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="1"/><path d="M12.5 11h2.8l1.8 6.4 1.1-5.1c-.2-.6-.7-1.1-1.3-1.3h-4.4v.8l1.8.4.2 3.8 2.8-6.3zm9.2 0h-2.4l-3.3 10h2.6l.6-1.8h3.2l.3 1.8h2.3l-3.3-10zm-2 6.2l1-3.2.6 3.2h-1.6zm8.8-6.2l-2 10h2.5l2-10h-2.5zm9 3c0-2-1.8-2.5-3.2-2.8-.7-.2-1-.5-1-.8s.4-.6 1-.6c1 0 1.6.4 1.6.4l.4-2.1c-.4-.2-1.2-.4-2-.4-2.4 0-4.1 1.2-4.1 3.3 0 2.2 2 2.6 3.5 3 .9.2 1.2.6 1.2.9s-.5.7-1.3.7c-1.3 0-2-.5-2-.5l-.4 2.2c.6.3 1.7.5 2.6.5 2.5.1 4.3-1.1 4.3-3.3z" fill="#1A1F71"/></svg>
					</div>
				</div>
			</div>

			<!-- Share Row -->
			<?php
			$share_url   = urlencode( get_permalink() );
			$share_title = urlencode( get_the_title() );
			?>
			<div class="custom-share-row">
				<span class="share-label">Share:</span>
				<div class="share-icons">
					<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>" class="share-btn" target="_blank" rel="noopener" title="Share on LinkedIn">
						<i class="fa-brands fa-linkedin-in"></i>
					</a>
					<a href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>" class="share-btn" target="_blank" rel="noopener" title="Share on Twitter/X">
						<i class="fa-brands fa-x-twitter"></i>
					</a>
					<a href="https://pinterest.com/pin/create/button/?url=<?php echo $share_url; ?>" class="share-btn" target="_blank" rel="noopener" title="Pin on Pinterest">
						<i class="fa-brands fa-pinterest-p"></i>
					</a>
					<a href="https://api.whatsapp.com/send?text=<?php echo $share_title; ?>%20<?php echo $share_url; ?>" class="share-btn" target="_blank" rel="noopener" title="Share on WhatsApp">
						<i class="fa-brands fa-whatsapp"></i>
					</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Bottom Section: Custom Tabs -->
	<div class="custom-product-tabs-wrapper">
		<ul class="custom-tabs-nav">
			<li class="tab-nav-item active" data-tab="report-highlights">Report Highlights</li>
			<li class="tab-nav-item" data-tab="related-reports">Related Reports</li>
			<li class="tab-nav-item" data-tab="reviews">Reviews (<?php echo esc_html( $review_count ); ?>)</li>
			<li class="tab-nav-item" data-tab="industry-reports">Similar Reports by Industry</li>
		</ul>

		<div class="custom-tabs-content">
			<!-- Tab Panel: Report Highlights -->
			<div id="tab-panel-report-highlights" class="tab-content-panel active">
				<div class="report-highlights-content-wrapper">
					<?php the_content(); ?>
				</div>
			</div>

			<!-- Tab Panel: Related Reports -->
			<div id="tab-panel-related-reports" class="tab-content-panel">
				<?php
				// Render related products standard WooCommerce loop
				woocommerce_related_products( array(
					'posts_per_page' => 4,
					'columns'        => 4,
					'orderby'        => 'rand',
				) );
				?>
			</div>

			<!-- Tab Panel: Reviews -->
			<div id="tab-panel-reviews" class="tab-content-panel">
				<?php
				if ( comments_open() ) {
					comments_template();
				} else {
					echo '<p>' . esc_html__( 'Reviews are closed.', 'quanto' ) . '</p>';
				}
				?>
			</div>

			<!-- Tab Panel: Similar Reports by Industry -->
			<div id="tab-panel-industry-reports" class="tab-content-panel">
				<?php
				// Get products in the same category
				$cat_ids = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
				if ( ! empty( $cat_ids ) ) {
					woocommerce_product_loop_start();
					$similar_args = array(
						'post_type'      => 'product',
						'posts_per_page' => 4,
						'post__not_in'   => array( $product->get_id() ),
						'tax_query'      => array(
							array(
								'taxonomy' => 'product_cat',
								'field'    => 'term_id',
								'terms'    => $cat_ids,
							),
						),
					);
					$similar_loop = new WP_Query( $similar_args );
					if ( $similar_loop->have_posts() ) {
						while ( $similar_loop->have_posts() ) : $similar_loop->the_post();
							wc_get_template_part( 'content', 'product' );
						endwhile;
					} else {
						echo '<p class="no-reports-msg">' . esc_html__( 'No similar reports found.', 'quanto' ) . '</p>';
					}
					wp_reset_postdata();
					woocommerce_product_loop_end();
				}
				?>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	var tabNavItems = document.querySelectorAll('.custom-tabs-nav .tab-nav-item');
	var tabPanels = document.querySelectorAll('.custom-tabs-content .tab-content-panel');

	tabNavItems.forEach(function(item) {
		item.addEventListener('click', function() {
			var targetTab = this.getAttribute('data-tab');

			// Remove active class from all tabs & panels
			tabNavItems.forEach(function(nav) { nav.classList.remove('active'); });
			tabPanels.forEach(function(panel) { panel.classList.remove('active'); });

			// Add active class to selected tab & panel
			this.classList.add('active');
			var activePanel = document.getElementById('tab-panel-' + targetTab);
			if (activePanel) {
				activePanel.classList.add('active');
			}
		});
	});
});
</script>

<?php do_action( 'woocommerce_after_single_product' ); ?>
