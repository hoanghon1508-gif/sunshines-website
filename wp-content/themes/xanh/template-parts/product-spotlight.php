<?php
/**
 * Khối giới thiệu 1 sản phẩm nổi bật (dùng ở trang chủ khi cửa hàng chỉ có 1 sản phẩm).
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;
if ( ! $product instanceof WC_Product ) {
	return;
}
$pct      = xanh_discount_percent( $product );
$buy_url  = $product->is_purchasable() && $product->is_in_stock() && $product->is_type( 'simple' )
	? add_query_arg( 'add-to-cart', $product->get_id(), wc_get_checkout_url() )
	: $product->get_permalink();
$features = array_filter( array_map( 'trim', explode( "\n", (string) $product->get_meta( '_xanh_highlights' ) ) ) );
?>
<section class="section section--white spotlight" aria-labelledby="spotlight-title">
	<div class="container spotlight__grid">
		<a class="spotlight__media" href="<?php echo esc_url( $product->get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
			<?php
			if ( $product->get_image_id() ) {
				echo $product->get_image( 'woocommerce_single', array( 'sizes' => '(max-width: 960px) 100vw, 560px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo '<span class="img-placeholder">' . xanh_icon( 'image', 48 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
			<?php if ( $pct ) : ?>
				<span class="badge badge--sale spotlight__badge">-<?php echo (int) $pct; ?>%</span>
			<?php endif; ?>
		</a>
		<div class="spotlight__info">
			<p class="spotlight__eyebrow"><?php esc_html_e( 'Sản phẩm của Sun Shines', 'xanh' ); ?></p>
			<h2 class="spotlight__title" id="spotlight-title"><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h2>
			<div class="spotlight__price"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php if ( $product->get_short_description() ) : ?>
				<div class="spotlight__desc"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>
			<?php endif; ?>
			<?php if ( $features ) : ?>
				<ul class="spotlight__features">
					<?php foreach ( $features as $f ) : ?>
						<li><?php echo xanh_icon( 'shield', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $f ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<div class="spotlight__actions">
				<a class="btn btn--primary btn--lg" href="<?php echo esc_url( $buy_url ); ?>" rel="nofollow"><?php esc_html_e( 'Mua ngay', 'xanh' ); ?></a>
				<a class="btn btn--outline btn--lg" href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php esc_html_e( 'Xem chi tiết', 'xanh' ); ?></a>
			</div>
		</div>
	</div>
</section>
