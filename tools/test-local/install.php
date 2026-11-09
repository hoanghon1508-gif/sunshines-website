<?php
// Cài đặt site thử nghiệm (tương đương bin/setup-site.sh --demo) bằng PHP CLI.
$site = $argv[1];
$_SERVER['HTTP_HOST'] = '127.0.0.1:8088';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_NAME'] = '127.0.0.1';
define( 'WP_INSTALLING', true );
require $site . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( ! is_blog_installed() ) {
	wp_install( 'Sun Shines', 'admin', 'admin@example.com', true, '', 'admin123' );
	echo "installed\n";
}
update_option( 'blogdescription', 'Trao sức khỏe, Gửi yêu thương' );
update_option( 'timezone_string', 'Asia/Ho_Chi_Minh' );
update_option( 'date_format', 'd/m/Y' );
update_option( 'permalink_structure', '/%postname%/' );

foreach ( array( 'sqlite-database-integration/load.php', 'woocommerce/woocommerce.php', 'xanh-core/xanh-core.php' ) as $p ) {
	$r = activate_plugin( $p, '', false, false );
	echo $p, ': ', is_wp_error( $r ) ? $r->get_error_message() : 'ok', "\n";
}
switch_theme( 'xanh' );
echo 'theme: ', get_stylesheet(), "\n";
