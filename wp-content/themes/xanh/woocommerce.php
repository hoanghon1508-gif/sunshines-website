<?php
/**
 * Khung cho mọi trang WooCommerce (danh sách sản phẩm, danh mục, chi tiết sản phẩm).
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_singular( 'product' ) ) :
	?>
	<div class="product-page">
		<div class="container">
			<?php xanh_breadcrumbs(); ?>
			<?php woocommerce_content(); ?>
		</div>
	</div>
	<?php
else :
	$title = woocommerce_page_title( false );
	$desc  = is_product_category() ? wp_strip_all_tags( term_description() ) : '';
	xanh_page_hero( esc_html( $title ), esc_html( $desc ) );

	$banners = function_exists( 'xanh_banners' ) ? xanh_banners( 'shop_top', 1 ) : array();
	?>
	<div class="section section--tight">
		<div class="container">
			<?php foreach ( $banners as $b ) : ?>
				<a class="wide-banner wide-banner--shop" href="<?php echo esc_url( $b['link'] ? $b['link'] : '#' ); ?>">
					<?php echo $b['image_id'] ? wp_get_attachment_image( $b['image_id'], 'xanh-wide', false, array( 'alt' => $b['title'] ) ) : ''; ?>
				</a>
			<?php endforeach; ?>
			<div class="shop-layout">
				<?php xanh_shop_filters(); ?>
				<div class="shop-main">
					<?php
					if ( woocommerce_product_loop() ) {
						echo '<div class="shop-toolbar">';
						woocommerce_result_count();
						woocommerce_catalog_ordering();
						echo '</div>';
						woocommerce_output_all_notices();
						woocommerce_product_loop_start();
						if ( wc_get_loop_prop( 'total' ) ) {
							while ( have_posts() ) {
								the_post();
								wc_get_template_part( 'content', 'product' );
							}
						}
						woocommerce_product_loop_end();
						xanh_pagination();
					} else {
						do_action( 'woocommerce_no_products_found' );
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
endif;

get_footer();
