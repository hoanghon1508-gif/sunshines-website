<?php
/**
 * Form tìm kiếm (tìm sản phẩm nếu có WooCommerce).
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

$xanh_search_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $xanh_search_id ); ?>"><?php esc_html_e( 'Tìm kiếm', 'xanh' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $xanh_search_id ); ?>" class="search-form__input" placeholder="<?php esc_attr_e( 'Tìm sản phẩm, bài viết…', 'xanh' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<input type="hidden" name="post_type" value="product" />
	<?php endif; ?>
	<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Tìm', 'xanh' ); ?></button>
</form>
