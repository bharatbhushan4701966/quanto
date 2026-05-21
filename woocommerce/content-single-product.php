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
						<img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/05/ApplePay.svg" alt="Apple Pay" width="38" height="24" />
					</div>
					<!-- Google Pay -->
					<div class="payment-icon" title="Google Pay">
						<img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/05/GooglePay.svg" alt="Google Pay" width="38" height="24" />
					</div>
					<!-- Mastercard -->
					<div class="payment-icon" title="Mastercard">
						<img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/05/Mastercard.svg" alt="Mastercard" width="38" height="24" />
					</div>
					<!-- American Express -->
					<div class="payment-icon" title="American Express">
						<img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/05/Amex.svg" alt="American Express" width="38" height="24" />
					</div>
					<!-- PayPal -->
					<div class="payment-icon" title="PayPal">
						<img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/05/PayPal.svg" alt="PayPal" width="38" height="24" />
					</div>
					<!-- Visa -->
					<div class="payment-icon" title="Visa">
						<img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/05/Visa.svg" alt="Visa" width="38" height="24" />
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
				// Safe reviews block – avoid loading full comment template
				if ( comments_open() ) {
					echo '<p><a href="' . esc_url( get_permalink() . '#reviews' ) . '">' . sprintf( esc_html__( '%s reviews', 'quanto' ), $review_count ) . '</a></p>';
				} else {
					echo '<p>' . esc_html__( 'Reviews are closed.', 'quanto' ) . '</p>';
				}
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
