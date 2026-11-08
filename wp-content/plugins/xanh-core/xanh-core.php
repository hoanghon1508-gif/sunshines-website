<?php
/**
 * Plugin Name:       XANH Core
 * Description:       Chức năng lõi của website: quản lý banner, form khách hàng, cài đặt liên hệ & mạng xã hội, phân quyền, bảo mật và SEO kỹ thuật cơ bản.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            XANH
 * Text Domain:       xanh-core
 * License:           GPL-2.0-or-later
 */

defined( 'ABSPATH' ) || exit;

define( 'XANH_CORE_VERSION', '1.0.0' );
define( 'XANH_CORE_FILE', __FILE__ );
define( 'XANH_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'XANH_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once XANH_CORE_DIR . 'includes/class-settings.php';
require_once XANH_CORE_DIR . 'includes/class-roles.php';
require_once XANH_CORE_DIR . 'includes/class-banners.php';
require_once XANH_CORE_DIR . 'includes/class-leads.php';
require_once XANH_CORE_DIR . 'includes/class-security.php';
require_once XANH_CORE_DIR . 'includes/class-seo.php';
require_once XANH_CORE_DIR . 'includes/class-performance.php';
require_once XANH_CORE_DIR . 'includes/functions.php';

Xanh_Settings::init();
Xanh_Roles::init();
Xanh_Banners::init();
Xanh_Leads::init();
Xanh_Security::init();
Xanh_SEO::init();
Xanh_Performance::init();

register_activation_hook( __FILE__, array( 'Xanh_Roles', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Xanh_Roles', 'deactivate' ) );
