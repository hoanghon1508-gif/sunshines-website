<?php
/**
 * Nút liên hệ nhanh (gọi điện, Zalo, Messenger) cố định góc phải màn hình.
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

if ( ! xanh_opt( 'quick_enabled', 1 ) ) {
	return;
}
$hotline   = preg_replace( '/[^0-9+]/', '', (string) xanh_opt( 'hotline' ) );
$zalo      = preg_replace( '/\D/', '', (string) xanh_opt( 'zalo' ) );
$messenger = xanh_opt( 'messenger' );
if ( ! $hotline && ! $zalo && ! $messenger ) {
	return;
}
?>
<div class="quick-contact" aria-label="<?php esc_attr_e( 'Liên hệ nhanh', 'xanh' ); ?>">
	<?php if ( $messenger ) : ?>
		<a class="quick-contact__btn quick-contact__btn--messenger" href="<?php echo esc_url( $messenger ); ?>" target="_blank" rel="noopener" aria-label="Messenger">
			<?php echo xanh_icon( 'messenger' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="quick-contact__label">Messenger</span>
		</a>
	<?php endif; ?>
	<?php if ( $zalo ) : ?>
		<a class="quick-contact__btn quick-contact__btn--zalo" href="<?php echo esc_url( 'https://zalo.me/' . $zalo ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Nhắn tin Zalo', 'xanh' ); ?>">
			<span class="quick-contact__zalo">Zalo</span>
			<span class="quick-contact__label"><?php esc_html_e( 'Chat Zalo', 'xanh' ); ?></span>
		</a>
	<?php endif; ?>
	<?php if ( $hotline ) : ?>
		<a class="quick-contact__btn quick-contact__btn--phone" href="tel:<?php echo esc_attr( $hotline ); ?>" aria-label="<?php esc_attr_e( 'Gọi điện', 'xanh' ); ?>">
			<?php echo xanh_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="quick-contact__label"><?php echo esc_html( xanh_opt( 'hotline' ) ); ?></span>
		</a>
	<?php endif; ?>
</div>
