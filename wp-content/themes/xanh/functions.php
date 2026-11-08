<?php
/**
 * XANH theme bootstrap.
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

define( 'XANH_THEME_VERSION', '1.0.0' );
define( 'XANH_THEME_DIR', get_template_directory() );
define( 'XANH_THEME_URI', get_template_directory_uri() );

require XANH_THEME_DIR . '/inc/icons.php';
require XANH_THEME_DIR . '/inc/setup.php';
require XANH_THEME_DIR . '/inc/template-tags.php';

if ( class_exists( 'WooCommerce' ) ) {
	require XANH_THEME_DIR . '/inc/woocommerce.php';
}
