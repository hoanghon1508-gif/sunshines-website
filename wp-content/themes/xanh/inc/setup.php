<?php
/**
 * Theme setup, menus, assets.
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		load_theme_textdomain( 'xanh', XANH_THEME_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 64,
				'width'       => 200,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width' => 600,
				'single_image_width'    => 900,
				'product_grid'          => array(
					'default_rows'    => 3,
					'default_columns' => 3,
					'max_columns'     => 4,
				),
			)
		);
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		register_nav_menus(
			array(
				'primary'       => __( 'Menu chính', 'xanh' ),
				'footer_about'  => __( 'Footer – Về chúng tôi', 'xanh' ),
				'footer_policy' => __( 'Footer – Chính sách', 'xanh' ),
			)
		);

		add_image_size( 'xanh-card', 768, 440, true );
		add_image_size( 'xanh-hero', 1200, 900, true );
		add_image_size( 'xanh-wide', 1600, 520, true );
	}
);

add_filter(
	'excerpt_length',
	static function () {
		return 28;
	}
);
add_filter(
	'excerpt_more',
	static function () {
		return '…';
	}
);

/**
 * CSS/JS.
 */
add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style( 'xanh-fonts', 'https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_style( 'xanh-main', XANH_THEME_URI . '/assets/css/main.css', array(), (string) filemtime( XANH_THEME_DIR . '/assets/css/main.css' ) );
		wp_enqueue_script( 'xanh-main', XANH_THEME_URI . '/assets/js/main.js', array(), (string) filemtime( XANH_THEME_DIR . '/assets/js/main.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	}
);

add_filter(
	'wp_resource_hints',
	static function ( $urls, $relation ) {
		if ( 'preconnect' === $relation ) {
			$urls[] = 'https://fonts.googleapis.com';
			$urls[] = array(
				'href'        => 'https://fonts.gstatic.com',
				'crossorigin' => 'anonymous',
			);
		}
		return $urls;
	},
	10,
	2
);

/**
 * Trang chủ: ảnh banner đầu tiên là LCP → tải ưu tiên, không lazy.
 */
add_filter(
	'wp_get_attachment_image_attributes',
	static function ( $attr ) {
		if ( ! empty( $attr['class'] ) && str_contains( $attr['class'], 'is-lcp' ) ) {
			$attr['loading']       = 'eager';
			$attr['fetchpriority'] = 'high';
		}
		return $attr;
	}
);
