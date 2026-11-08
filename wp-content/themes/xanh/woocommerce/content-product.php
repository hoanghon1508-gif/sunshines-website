<?php
/**
 * Thẻ sản phẩm trong danh sách.
 *
 * Ghi đè template WooCommerce: woocommerce/templates/content-product.php
 *
 * @package Xanh
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}
$pct = xanh_discount_percent( $product );
?>
<li <?php wc_product_class( 'product-card', $product ); ?>>
	<a class="product-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( $product->get_image_id() ) {
			echo $product->get_image( 'woocommerce_thumbnail', array( 'sizes' => '(max-width: 600px) 50vw, 300px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			echo '<span class="img-placeholder">' . xanh_icon( 'image', 40 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
		<?php if ( ! $product->is_in_stock() ) : ?>
			<span class="badge badge--muted product-card__stock"><?php esc_html_e( 'Hết hàng', 'xanh' ); ?></span>
		<?php endif; ?>
	</a>
	<div class="product-card__body">
		<h3 class="product-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<div class="product-card__price">
			<?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $pct ) : ?>
				<span class="badge badge--sale">-<?php echo (int) $pct; ?>%</span>
			<?php endif; ?>
		</div>
		<div class="product-card__action">
			<?php woocommerce_template_loop_add_to_cart(); ?>
		</div>
	</div>
</li>
