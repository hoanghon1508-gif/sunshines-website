<?php
/**
 * Tối ưu tốc độ: bỏ script không cần thiết, ưu tiên WebP/AVIF khi tải ảnh lên,
 * lazy-load ảnh (WordPress mặc định) và cache header cho file tĩnh (xem .htaccess mẫu trong README).
 *
 * @package XanhCore
 */

defined( 'ABSPATH' ) || exit;

class Xanh_Performance {

	public static function init() {
		// Bỏ emoji script/style.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		add_filter( 'emoji_svg_url', '__return_false' );

		// Bỏ các thẻ head ít dùng.
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );

		// Tự tạo bản WebP khi tải ảnh JPEG/PNG lên (nếu máy chủ hỗ trợ).
		add_filter( 'image_editor_output_format', array( __CLASS__, 'webp_output' ) );

		// Chất lượng ảnh nén.
		add_filter(
			'wp_editor_set_quality',
			static function () {
				return 82;
			}
		);

		// Không tải block library CSS ở trang không dùng block.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'dequeue' ), 100 );
	}

	public static function webp_output( $formats ) {
		if ( wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
			$formats['image/jpeg'] = 'image/webp';
			$formats['image/png']  = 'image/webp';
		}
		return $formats;
	}

	public static function dequeue() {
		if ( is_front_page() || ( function_exists( 'is_woocommerce' ) && is_woocommerce() && ! is_product() ) ) {
			wp_dequeue_style( 'wp-block-library' );
			wp_dequeue_style( 'global-styles' );
			wp_dequeue_style( 'classic-theme-styles' );
		}
	}
}
