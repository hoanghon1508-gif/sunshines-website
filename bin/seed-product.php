<?php
/**
 * Tạo/cập nhật sản phẩm thật: Thảo Mộc Mogan (hộp 30 gói x 5g, 390.000 ₫).
 * Chạy: wp eval-file bin/seed-product.php   (an toàn khi chạy lại nhiều lần)
 *
 * @package XanhCore
 */

defined( 'ABSPATH' ) || exit;

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

// Danh mục.
$term = term_exists( 'thao-moc-mogan', 'product_cat' );
if ( ! $term ) {
	$term = wp_insert_term( 'Thảo Mộc Mogan', 'product_cat', array( 'slug' => 'thao-moc-mogan' ) );
}
$cat_id = (int) $term['term_id'];

// Ảnh sản phẩm (lấy từ theme, nhập vào Thư viện Media một lần).
$image_id = (int) get_option( 'xanh_mogan_image_id' );
if ( ! $image_id || ! get_post( $image_id ) ) {
	$src = get_template_directory() . '/assets/images/thao-moc-mogan.jpg';
	if ( file_exists( $src ) ) {
		$tmp = wp_tempnam( 'thao-moc-mogan.jpg' );
		copy( $src, $tmp );
		$image_id = media_handle_sideload( array( 'name' => 'thao-moc-mogan.jpg', 'tmp_name' => $tmp ), 0, 'Thảo Mộc Mogan – Hộp 30 gói x 5g' );
		if ( is_wp_error( $image_id ) ) {
			error_log( 'Không nhập được ảnh: ' . $image_id->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			$image_id = 0;
		} else {
			update_post_meta( $image_id, '_wp_attachment_image_alt', 'Hộp Thảo Mộc Mogan 30 gói x 5g' );
			update_option( 'xanh_mogan_image_id', $image_id, false );
		}
	}
}

// Sản phẩm.
$existing = get_page_by_path( 'thao-moc-mogan', OBJECT, 'product' );
$product  = $existing ? wc_get_product( $existing->ID ) : new WC_Product_Simple();

$product->set_name( 'Thảo Mộc Mogan' );
$product->set_slug( 'thao-moc-mogan' );
$product->set_status( 'publish' );
$product->set_regular_price( '390000' );
$product->set_sale_price( '' );
$product->set_featured( true );
$product->set_category_ids( array( $cat_id ) );
$product->set_manage_stock( false );
$product->set_stock_status( 'instock' );
$product->set_short_description(
	"Trà thảo mộc túi lọc từ <strong>cà gai leo, diệp hạ châu, xạ đen, mướp đắng, lá đơn</strong>.\nQuy cách: <strong>Hộp 30 gói × 5g</strong>."
);
$product->set_description(
	'<h2>Thành phần</h2>
<p>Cà gai leo, diệp hạ châu, xạ đen, mướp đắng, lá đơn.</p>
<h2>Quy cách</h2>
<p>Hộp 30 gói × 5g (túi lọc).</p>
<h2>Hướng dẫn sử dụng</h2>
<p>Cho 3 túi lọc vào bình giữ nhiệt, ấm hoặc cốc, đổ 0,5 – 1 lít nước sôi vào. Đợi 5 – 10 phút là có thể dùng.</p>
<p>Nên dùng sau ăn khoảng 30 phút và không dùng khi bụng quá đói.</p>
<h2>Hướng dẫn bảo quản</h2>
<p>Bảo quản nơi khô ráo, thoáng mát, tránh ánh nắng trực tiếp.</p>
<h2>Cảnh báo</h2>
<p>Không sử dụng sản phẩm quá hạn hoặc bao bì không còn nguyên vẹn.</p>
<p><strong>Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.</strong></p>'
);
if ( $image_id ) {
	$product->set_image_id( $image_id );
}
$product->update_meta_data(
	'_xanh_highlights',
	"Thành phần: cà gai leo, diệp hạ châu, xạ đen, mướp đắng, lá đơn\nQuy cách: hộp 30 gói × 5g, dạng túi lọc tiện dùng\nCách dùng: hãm 3 túi lọc với 0,5 – 1 lít nước sôi trong 5 – 10 phút"
);
$product_id = $product->save();

// Ẩn các sản phẩm mẫu cũ (nếu từng chạy bản demo trước đây).
foreach ( get_posts( array( 'post_type' => 'product', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'post__not_in' => array( $product_id ) ) ) as $old_id ) {
	if ( str_starts_with( get_the_title( $old_id ), 'Thảo Mộc Mogan – Sản phẩm' ) ) {
		wp_trash_post( $old_id );
	}
}

// Banner slider trang chủ dùng ảnh sản phẩm nếu chưa có ảnh.
if ( $image_id ) {
	foreach ( get_posts( array( 'post_type' => 'xanh_banner', 'posts_per_page' => -1, 'fields' => 'ids' ) ) as $bid ) {
		if ( ! has_post_thumbnail( $bid ) ) {
			set_post_thumbnail( $bid, $image_id );
		}
	}
}

if ( class_exists( 'WP_CLI' ) ) {
	WP_CLI::success( 'Sản phẩm Thảo Mộc Mogan: #' . $product_id );
}
