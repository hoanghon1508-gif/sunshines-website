<?php
/**
 * Bộ icon SVG inline (không cần tải font icon).
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

/**
 * In ra icon SVG.
 *
 * @param string $name Tên icon.
 * @param int    $size Kích thước px.
 */
function xanh_icon( $name, $size = 24 ) {
	$paths = array(
		'cart'      => '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/>',
		'search'    => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
		'menu'      => '<path d="M3 6h18M3 12h18M3 18h18"/>',
		'close'     => '<path d="M18 6 6 18M6 6l12 12"/>',
		'phone'     => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
		'mail'      => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
		'pin'       => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
		'clock'     => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
		'truck'     => '<path d="M10 17h4V5H2v12h3"/><path d="M14 9h4l4 4v4h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>',
		'refresh'   => '<path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/><path d="M8 16H3v5"/>',
		'shield'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
		'headset'   => '<path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"/>',
		'arrow'     => '<path d="M5 12h14M13 5l7 7-7 7"/>',
		'chevron-l' => '<path d="m15 18-6-6 6-6"/>',
		'chevron-r' => '<path d="m9 18 6-6-6-6"/>',
		'user'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
		'image'     => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/>',
		'facebook'  => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
		'youtube'   => '<path d="M2.5 17a24 24 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.6 49.6 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24 24 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.6 49.6 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/>',
		'instagram' => '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".5"/>',
		'tiktok'    => '<path d="M9 12a4 4 0 1 0 4 4V2a5 5 0 0 0 5 5"/>',
		'zalo'      => '<path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 9 9 0 0 1-3.9-.9L3 21l1.9-4.6A8.4 8.4 0 0 1 12 3a8.4 8.4 0 0 1 9 8.5z"/>',
		'messenger' => '<path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 9 9 0 0 1-3.9-.9L3 21l1.9-4.6A8.4 8.4 0 0 1 12 3a8.4 8.4 0 0 1 9 8.5z"/><path d="m7 13 3-3 3 2 4-4"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="icon icon--%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		(int) $size,
		$paths[ $name ]
	);
}

/**
 * Biểu tượng Sun Shines (mặt trời + 2 lá) dạng SVG, dùng khi chưa tải file logo lên
 * (Giao diện → Tuỳ biến → Nhận dạng website → Logo).
 */
function xanh_leaf_logo( $size = 40 ) {
	static $n = 0;
	$gid = 'ssg' . ( ++$n );
	return sprintf(
		'<svg width="%1$d" height="%1$d" viewBox="0 0 64 64" fill="none" aria-hidden="true"><defs><linearGradient id="%2$s" x1="20" y1="56" x2="58" y2="10" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#1F8F3A"/><stop offset="1" stop-color="#8CC63F"/></linearGradient></defs><g stroke="#D9E021" stroke-width="2.4" stroke-linecap="round"><path d="M12.8 34.8A14 14 0 0 1 36.7 21"/><path d="M9.3 33L.9 34.4M9.6 25.6l-8.4-2.3M13 19.1l-6.7-5.7M18.8 14.6l-3.7-8M26 13V4.2M33.2 14.6l3.7-8M39 19.1l6.7-5.7"/></g><path d="M34 50c0-18 10-32 24-40 2 18-8 32-24 40Z" fill="url(#%2$s)"/><path d="M32 50c-6-10-12-14-19-19 11-1 19 7 19 19Z" fill="url(#%2$s)"/><path d="M36.5 46c5.5-10 11.5-20 18-30M29.5 46c-3.5-5.5-7.5-9.5-12-12.5" stroke="#fff" stroke-width="1.4" stroke-linecap="round"/><path d="m33 50-2 12" stroke="#1F8F3A" stroke-width="2.6" stroke-linecap="round"/></svg>',
		(int) $size,
		esc_attr( $gid )
	);
}
