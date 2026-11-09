<?php
// Cấu hình site thử nghiệm: tương đương bin/setup-site.sh --demo (không cần WP-CLI).
// Dùng: php configure.php <thư-mục-site> <đường-dẫn bin/seed-product.php>
$site = $argv[1];
$_SERVER['HTTP_HOST'] = '127.0.0.1:8088';
$_SERVER['REQUEST_URI'] = '/';
require $site . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
wp_set_current_user( 1 );

// WooCommerce: tạo bảng + trang.
WC_Install::install();
WC_Install::create_pages();
$opts = array(
	'woocommerce_default_country'       => 'VN',
	'woocommerce_currency'              => 'VND',
	'woocommerce_currency_pos'          => 'right_space',
	'woocommerce_price_thousand_sep'    => '.',
	'woocommerce_price_decimal_sep'     => ',',
	'woocommerce_price_num_decimals'    => 0,
	'woocommerce_enable_guest_checkout' => 'yes',
	'woocommerce_coming_soon'           => 'no',
	'woocommerce_cod_settings'          => array( 'enabled' => 'yes', 'title' => 'Thanh toán khi nhận hàng (COD)', 'description' => 'Thanh toán bằng tiền mặt khi nhận hàng.' ),
	'woocommerce_bacs_settings'         => array( 'enabled' => 'yes', 'title' => 'Chuyển khoản ngân hàng', 'description' => 'Chuyển khoản theo thông tin bên dưới.' ),
);
foreach ( $opts as $k => $v ) {
	update_option( $k, $v );
}
foreach ( array( 'shop' => array( 'san-pham', 'Sản phẩm' ), 'cart' => array( 'gio-hang', 'Giỏ hàng' ), 'checkout' => array( 'thanh-toan', 'Thanh toán' ), 'myaccount' => array( 'tai-khoan', 'Tài khoản' ) ) as $k => $v ) {
	$id = wc_get_page_id( $k );
	if ( $id > 0 ) {
		wp_update_post( array( 'ID' => $id, 'post_name' => $v[0], 'post_title' => $v[1] ) );
	}
}
update_option( 'woocommerce_permalinks', array( 'product_base' => '/san-pham-chi-tiet', 'category_base' => 'danh-muc', 'tag_base' => 'the-san-pham', 'attribute_base' => '', 'use_verbose_page_rules' => false ) );

function ensure_page( $slug, $title, $content = '', $tpl = '' ) {
	$p  = get_page_by_path( $slug );
	$id = $p ? $p->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => $title, 'post_content' => $content ) );
	if ( $tpl ) {
		update_post_meta( $id, '_wp_page_template', $tpl );
	}
	return $id;
}
$home    = ensure_page( 'trang-chu', 'Trang chủ' );
$news    = ensure_page( 'tin-tuc', 'Tin tức' );
$about   = ensure_page( 'gioi-thieu', 'Giới thiệu', '<!-- wp:paragraph --><p>Giới thiệu về công ty Sun Shines.</p><!-- /wp:paragraph -->[xanh_faq]Q: Bao lâu thì nhận được hàng?A: Từ 2–4 ngày làm việc.Q: Có được đổi trả không?A: Được đổi trả trong 7 ngày.[/xanh_faq]' );
$contact = ensure_page( 'lien-he', 'Liên hệ', '', 'page-templates/template-contact.php' );
$pol     = array();
foreach ( array( 'chinh-sach-doi-tra' => 'Chính sách đổi trả', 'chinh-sach-giao-hang' => 'Chính sách giao hàng', 'chinh-sach-bao-mat' => 'Chính sách bảo mật', 'huong-dan-thanh-toan' => 'Hướng dẫn thanh toán' ) as $s => $t ) {
	$pol[] = ensure_page( $s, $t, '<!-- wp:paragraph --><p>Nội dung ' . mb_strtolower( $t ) . '.</p><!-- /wp:paragraph -->' );
}
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home );
update_option( 'page_for_posts', $news );

function make_menu( $name, $loc, $ids ) {
	$m = wp_get_nav_menu_object( $name );
	if ( $m ) {
		wp_delete_nav_menu( $m->term_id );
	}
	$mid = wp_create_nav_menu( $name );
	foreach ( $ids as $id ) {
		wp_update_nav_menu_item( $mid, 0, array( 'menu-item-object-id' => $id, 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
	}
	$locs         = get_theme_mod( 'nav_menu_locations', array() );
	$locs[ $loc ] = $mid;
	set_theme_mod( 'nav_menu_locations', $locs );
}
make_menu( 'Menu chính', 'primary', array( $home, $about, wc_get_page_id( 'shop' ), $news, $contact ) );
make_menu( 'Về chúng tôi', 'footer_about', array( $about, $news, $contact ) );
make_menu( 'Chính sách', 'footer_policy', $pol );

// Bài viết & banner mẫu (sản phẩm thật tạo bằng bin/seed-product.php).
foreach ( array( 'Bí quyết chọn sản phẩm phù hợp cho gia đình bạn', 'Lợi ích của thảo mộc trong chăm sóc sức khỏe hằng ngày', 'Hướng dẫn bảo quản sản phẩm đúng cách' ) as $t ) {
	if ( ! get_posts( array( 'post_type' => 'post', 'title' => $t, 'fields' => 'ids' ) ) ) {
		wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => $t, 'post_excerpt' => 'Đoạn tóm tắt ngắn của bài viết giúp người đọc nắm nhanh nội dung chính.', 'post_content' => '<!-- wp:paragraph --><p>Nội dung bài viết mẫu.</p><!-- /wp:paragraph -->' ) );
	}
}
if ( ! get_posts( array( 'post_type' => 'xanh_banner', 'fields' => 'ids' ) ) ) {
	$b = wp_insert_post( array( 'post_type' => 'xanh_banner', 'post_status' => 'publish', 'post_title' => 'Trao sức khỏe, Gửi yêu thương', 'menu_order' => 1 ) );
	update_post_meta( $b, '_xanh_location', 'home_slider' );
	update_post_meta( $b, '_xanh_active', '1' );
	update_post_meta( $b, '_xanh_subtitle', 'Sun Shines mang đến các sản phẩm thảo mộc Mogan chọn lọc, an toàn và lành tính cho sức khỏe cả gia đình.' );
	update_post_meta( $b, '_xanh_link', home_url( '/san-pham/' ) );
	update_post_meta( $b, '_xanh_button', 'Mua sắm ngay' );
}
update_option( 'woocommerce_checkout_phone_field', 'required' );
update_option( 'woocommerce_checkout_company_field', 'hidden' );
update_option( 'woocommerce_checkout_address_2_field', 'hidden' );
require $argv[2];
flush_rewrite_rules();
echo "configured\n";
