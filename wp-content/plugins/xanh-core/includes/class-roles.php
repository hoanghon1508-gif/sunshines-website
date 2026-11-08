<?php
/**
 * Phân quyền: thêm quyền quản lý khách hàng (form) và vai trò "Nhân viên tư vấn".
 *
 * Các vai trò có sẵn:
 *  - Administrator: toàn quyền.
 *  - Shop Manager (WooCommerce): quản lý sản phẩm, đơn hàng, khách hàng form, banner.
 *  - Editor: quản lý trang, bài viết, banner, khách hàng form.
 *  - Nhân viên tư vấn (xanh_sales): chỉ xem/xử lý danh sách khách hàng gửi form.
 *
 * @package XanhCore
 */

defined( 'ABSPATH' ) || exit;

class Xanh_Roles {

	const LEAD_CAP   = 'manage_xanh_leads';
	const BANNER_CAP = 'manage_xanh_banners';
	const VERSION    = 1;

	public static function init() {
		// Đảm bảo quyền được cập nhật khi plugin được nâng cấp mà không kích hoạt lại.
		add_action( 'init', array( __CLASS__, 'maybe_upgrade' ), 5 );
	}

	public static function maybe_upgrade() {
		if ( (int) get_option( 'xanh_roles_version' ) !== self::VERSION ) {
			self::activate();
		}
	}

	public static function activate() {
		add_role(
			'xanh_sales',
			__( 'Nhân viên tư vấn', 'xanh-core' ),
			array(
				'read'         => true,
				self::LEAD_CAP => true,
			)
		);

		$map = array(
			'administrator' => array( self::LEAD_CAP, self::BANNER_CAP ),
			'shop_manager'  => array( self::LEAD_CAP, self::BANNER_CAP ),
			'editor'        => array( self::LEAD_CAP, self::BANNER_CAP ),
		);
		foreach ( $map as $role_name => $caps ) {
			$role = get_role( $role_name );
			if ( $role ) {
				foreach ( $caps as $cap ) {
					$role->add_cap( $cap );
				}
			}
		}
		update_option( 'xanh_roles_version', self::VERSION );
	}

	public static function deactivate() {
		remove_role( 'xanh_sales' );
		foreach ( array( 'administrator', 'shop_manager', 'editor' ) as $role_name ) {
			$role = get_role( $role_name );
			if ( $role ) {
				$role->remove_cap( self::LEAD_CAP );
				$role->remove_cap( self::BANNER_CAP );
			}
		}
		delete_option( 'xanh_roles_version' );
	}
}
