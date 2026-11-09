<?php
/**
 * Tạo/cập nhật sản phẩm thật: Thảo Mộc Mogan (hộp 30 gói x 5g, 390.000 ₫).
 * Chạy: wp eval-file bin/seed-product.php   (an toàn khi chạy lại nhiều lần)
 *
 * Logic nằm trong plugin (Xanh_Setup::seed_product) để dùng chung với nút
 * "Cài đặt website → Thiết lập nhanh" trên hosting không có WP-CLI.
 *
 * @package XanhCore
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Xanh_Setup' ) ) {
	echo "Chưa kích hoạt plugin Sun Shines Core.\n";
	return;
}
$xanh_pid = Xanh_Setup::seed_product();
if ( class_exists( 'WP_CLI' ) ) {
	$xanh_pid ? WP_CLI::success( 'Sản phẩm Thảo Mộc Mogan: #' . $xanh_pid ) : WP_CLI::warning( 'Không tạo được sản phẩm (WooCommerce đã bật chưa?)' );
}
