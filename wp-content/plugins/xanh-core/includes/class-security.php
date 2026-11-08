<?php
/**
 * Bảo mật & hardening WordPress cơ bản.
 *
 * @package XanhCore
 */

defined( 'ABSPATH' ) || exit;

class Xanh_Security {

	const MAX_ATTEMPTS = 5;
	const LOCKOUT      = 900; // 15 phút.

	public static function init() {
		// Tắt sửa code theme/plugin trong trang quản trị.
		if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
			define( 'DISALLOW_FILE_EDIT', true );
		}

		// Tắt XML-RPC (thường bị dùng để dò mật khẩu).
		add_filter( 'xmlrpc_enabled', '__return_false' );
		add_filter(
			'wp_headers',
			static function ( $headers ) {
				unset( $headers['X-Pingback'] );
				return $headers;
			}
		);

		// Ẩn phiên bản WordPress.
		remove_action( 'wp_head', 'wp_generator' );
		add_filter( 'the_generator', '__return_empty_string' );

		// Chặn dò tên đăng nhập.
		add_action( 'template_redirect', array( __CLASS__, 'block_author_enum' ) );
		add_filter( 'rest_endpoints', array( __CLASS__, 'restrict_user_endpoints' ) );

		// Giới hạn số lần đăng nhập sai.
		add_filter( 'authenticate', array( __CLASS__, 'check_lockout' ), 30, 3 );
		add_action( 'wp_login_failed', array( __CLASS__, 'login_failed' ) );
		add_action( 'wp_login', array( __CLASS__, 'login_success' ) );
		add_filter( 'login_errors', array( __CLASS__, 'generic_login_error' ) );

		// HTTP security headers.
		add_action( 'send_headers', array( __CLASS__, 'headers' ) );
	}

	public static function block_author_enum() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! is_user_logged_in() && ( isset( $_GET['author'] ) || is_author() ) ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	}

	public static function restrict_user_endpoints( $endpoints ) {
		if ( is_user_logged_in() ) {
			return $endpoints;
		}
		foreach ( array_keys( $endpoints ) as $route ) {
			if ( str_starts_with( $route, '/wp/v2/users' ) ) {
				unset( $endpoints[ $route ] );
			}
		}
		return $endpoints;
	}

	private static function ip_key() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		return 'xanh_login_' . md5( $ip );
	}

	public static function check_lockout( $user, $username, $password ) {
		if ( empty( $username ) && empty( $password ) ) {
			return $user;
		}
		$attempts = (int) get_transient( self::ip_key() );
		if ( $attempts >= self::MAX_ATTEMPTS ) {
			return new WP_Error(
				'xanh_locked',
				sprintf(
					/* translators: %d: minutes */
					__( 'Bạn đã đăng nhập sai quá nhiều lần. Vui lòng thử lại sau %d phút.', 'xanh-core' ),
					(int) ( self::LOCKOUT / 60 )
				)
			);
		}
		return $user;
	}

	public static function login_failed() {
		$key = self::ip_key();
		set_transient( $key, (int) get_transient( $key ) + 1, self::LOCKOUT );
	}

	public static function login_success() {
		delete_transient( self::ip_key() );
	}

	/**
	 * Không tiết lộ tên đăng nhập có tồn tại hay không.
	 */
	public static function generic_login_error( $error ) {
		if ( (int) get_transient( self::ip_key() ) >= self::MAX_ATTEMPTS ) {
			return $error; // Giữ thông báo khoá tạm thời.
		}
		if ( str_contains( $error, 'invalid_username' ) || str_contains( $error, 'incorrect_password' ) || str_contains( $error, 'invalid_email' ) || str_contains( $error, 'lostpassword' ) ) {
			return __( 'Tên đăng nhập hoặc mật khẩu không đúng.', 'xanh-core' );
		}
		return $error;
	}

	public static function headers() {
		if ( headers_sent() ) {
			return;
		}
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
		header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
		if ( is_ssl() ) {
			header( 'Strict-Transport-Security: max-age=31536000' );
		}
	}
}
