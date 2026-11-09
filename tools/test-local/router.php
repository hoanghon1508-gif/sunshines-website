<?php
// Router cho PHP built-in server (thay cho .htaccess của WordPress).
$root = $_SERVER['DOCUMENT_ROOT'];
$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
if ( $path !== '/' && file_exists( $root . $path ) && ! is_dir( $root . $path ) ) {
	return false;
}
if ( is_dir( $root . $path ) && file_exists( rtrim( $root . $path, '/' ) . '/index.php' ) ) {
	$_SERVER['SCRIPT_NAME'] = rtrim( $path, '/' ) . '/index.php';
	chdir( $root . $path );
	require rtrim( $root . $path, '/' ) . '/index.php';
	return true;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require $root . '/index.php';
