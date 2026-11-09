<?php
/**
 * "Thiết lập nhanh": cấu hình website bằng 1 nút bấm trong trang quản trị
 * (dành cho hosting không có SSH/WP-CLI). Chạy lại nhiều lần vẫn an toàn.
 *
 * Tương đương bin/setup-site.sh (không gồm cài plugin/gói ngôn ngữ, và không tạo dữ liệu demo).
 *
 * @package XanhCore
 */

defined( 'ABSPATH' ) || exit;

class Xanh_Setup {

	const PRODUCT_SLUG = 'thao-moc-mogan';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 20 );
		add_action( 'admin_post_xanh_quick_setup', array( __CLASS__, 'handle' ) );
	}

	public static function menu() {
		add_submenu_page(
			'xanh-settings',
			__( 'Thiết lập nhanh', 'xanh-core' ),
			__( 'Thiết lập nhanh', 'xanh-core' ),
			'manage_options',
			'xanh-quick-setup',
			array( __CLASS__, 'render' )
		);
	}

	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$log = get_transient( 'xanh_setup_log_' . get_current_user_id() );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Thiết lập nhanh website', 'xanh-core' ); ?></h1>
			<p><?php esc_html_e( 'Bấm nút bên dưới để tự động cấu hình website. Chạy lại nhiều lần vẫn an toàn: những gì đã có sẽ được giữ nguyên, chỉ bổ sung phần còn thiếu.', 'xanh-core' ); ?></p>
			<ul style="list-style:disc;padding-left:20px">
				<li><?php esc_html_e( 'Múi giờ Việt Nam, định dạng ngày dd/mm/yyyy, đường dẫn đẹp.', 'xanh-core' ); ?></li>
				<li><?php esc_html_e( 'WooCommerce: tiền VNĐ, Việt Nam, thanh toán khi nhận hàng (COD) + chuyển khoản, số điện thoại bắt buộc.', 'xanh-core' ); ?></li>
				<li><?php esc_html_e( 'Các trang: Trang chủ, Giới thiệu, Sản phẩm, Tin tức, Liên hệ, Giỏ hàng, Thanh toán, Tài khoản, 4 trang chính sách.', 'xanh-core' ); ?></li>
				<li><?php esc_html_e( 'Menu chính, menu chân trang.', 'xanh-core' ); ?></li>
				<li><?php esc_html_e( 'Sản phẩm Thảo Mộc Mogan (hộp 30 gói × 5g, 390.000 ₫) kèm ảnh.', 'xanh-core' ); ?></li>
			</ul>
			<?php if ( ! class_exists( 'WooCommerce' ) ) : ?>
				<div class="notice notice-warning inline"><p><?php esc_html_e( 'Chưa kích hoạt WooCommerce. Hãy cài và kích hoạt WooCommerce trước (Plugin → Cài mới → tìm "WooCommerce").', 'xanh-core' ); ?></p></div>
			<?php endif; ?>
			<?php if ( 'xanh' !== get_template() ) : ?>
				<div class="notice notice-warning inline"><p><?php esc_html_e( 'Chưa kích hoạt giao diện Sun Shines (Giao diện → Giao diện).', 'xanh-core' ); ?></p></div>
			<?php endif; ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="xanh_quick_setup" />
				<?php wp_nonce_field( 'xanh_quick_setup' ); ?>
				<?php submit_button( __( 'Chạy thiết lập', 'xanh-core' ), 'primary large' ); ?>
			</form>
			<?php if ( is_array( $log ) ) : ?>
				<h2><?php esc_html_e( 'Kết quả lần chạy gần nhất', 'xanh-core' ); ?></h2>
				<ol>
					<?php foreach ( $log as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
		</div>
		<?php
	}

	public static function handle() {
		check_admin_referer( 'xanh_quick_setup' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Bạn không có quyền thực hiện thao tác này.', 'xanh-core' ), 403 );
		}
		$log = self::run();
		set_transient( 'xanh_setup_log_' . get_current_user_id(), $log, HOUR_IN_SECONDS );
		wp_safe_redirect( admin_url( 'admin.php?page=xanh-quick-setup' ) );
		exit;
	}

	/**
	 * Chạy toàn bộ thiết lập. Trả về nhật ký từng bước.
	 *
	 * @return string[]
	 */
	public static function run() {
		$log = array();

		// 1. Chung.
		update_option( 'timezone_string', 'Asia/Ho_Chi_Minh' );
		update_option( 'date_format', 'd/m/Y' );
		update_option( 'time_format', 'H:i' );
		update_option( 'default_comment_status', 'closed' );
		$desc = get_option( 'blogdescription' );
		if ( '' === $desc || 'Just another WordPress site' === $desc || str_contains( (string) $desc, 'WordPress' ) ) {
			update_option( 'blogdescription', Xanh_Settings::get( 'slogan', 'Trao sức khỏe, Gửi yêu thương' ) );
		}
		if ( ! get_option( 'permalink_structure' ) ) {
			update_option( 'permalink_structure', '/%postname%/' );
		}
		$log[] = __( 'Đã đặt múi giờ, định dạng ngày, đường dẫn đẹp.', 'xanh-core' );

		// 2. WooCommerce.
		$shop_id = 0;
		if ( class_exists( 'WooCommerce' ) ) {
			$opts = array(
				'woocommerce_default_country'          => 'VN',
				'woocommerce_currency'                 => 'VND',
				'woocommerce_currency_pos'             => 'right_space',
				'woocommerce_price_thousand_sep'       => '.',
				'woocommerce_price_decimal_sep'        => ',',
				'woocommerce_price_num_decimals'       => 0,
				'woocommerce_calc_taxes'               => 'no',
				'woocommerce_enable_guest_checkout'    => 'yes',
				'woocommerce_coming_soon'              => 'no',
				'woocommerce_checkout_phone_field'     => 'required',
				'woocommerce_checkout_company_field'   => 'hidden',
				'woocommerce_checkout_address_2_field' => 'hidden',
			);
			foreach ( $opts as $k => $v ) {
				update_option( $k, $v );
			}
			$cod = (array) get_option( 'woocommerce_cod_settings', array() );
			if ( empty( $cod['enabled'] ) || 'yes' !== $cod['enabled'] ) {
				update_option( 'woocommerce_cod_settings', array_merge( $cod, array( 'enabled' => 'yes', 'title' => 'Thanh toán khi nhận hàng (COD)', 'description' => 'Thanh toán bằng tiền mặt khi nhận hàng.', 'instructions' => 'Bạn sẽ thanh toán khi nhận hàng.' ) ) );
			}
			$bacs = (array) get_option( 'woocommerce_bacs_settings', array() );
			if ( empty( $bacs['enabled'] ) ) {
				update_option( 'woocommerce_bacs_settings', array_merge( $bacs, array( 'enabled' => 'yes', 'title' => 'Chuyển khoản ngân hàng', 'description' => 'Chuyển khoản theo thông tin bên dưới. Đơn hàng được xử lý sau khi nhận được thanh toán.', 'instructions' => 'Vui lòng ghi mã đơn hàng trong nội dung chuyển khoản.' ) ) );
			}
			if ( class_exists( 'WC_Install' ) ) {
				WC_Install::create_pages();
			}
			$wc_pages = array(
				'shop'      => array( 'san-pham', 'Sản phẩm' ),
				'cart'      => array( 'gio-hang', 'Giỏ hàng' ),
				'checkout'  => array( 'thanh-toan', 'Thanh toán' ),
				'myaccount' => array( 'tai-khoan', 'Tài khoản' ),
			);
			foreach ( $wc_pages as $key => $v ) {
				$id = wc_get_page_id( $key );
				if ( $id > 0 ) {
					wp_update_post( array( 'ID' => $id, 'post_name' => $v[0], 'post_title' => $v[1] ) );
				}
			}
			$shop_id = wc_get_page_id( 'shop' );
			$perm    = (array) get_option( 'woocommerce_permalinks', array() );
			if ( empty( $perm['product_base'] ) || '/product' === $perm['product_base'] || 'product' === trim( (string) $perm['product_base'], '/' ) ) {
				update_option( 'woocommerce_permalinks', array_merge( $perm, array( 'product_base' => '/san-pham-chi-tiet', 'category_base' => 'danh-muc', 'tag_base' => 'the-san-pham' ) ) );
			}
			$log[] = __( 'Đã cấu hình WooCommerce: VNĐ, COD + chuyển khoản, trang Sản phẩm/Giỏ hàng/Thanh toán/Tài khoản.', 'xanh-core' );
		} else {
			$log[] = __( 'Bỏ qua WooCommerce (chưa kích hoạt).', 'xanh-core' );
		}

		// 3. Trang nội dung.
		$home    = self::ensure_page( 'trang-chu', 'Trang chủ' );
		$news    = self::ensure_page( 'tin-tuc', 'Tin tức' );
		$about   = self::ensure_page( 'gioi-thieu', 'Giới thiệu', '<!-- wp:paragraph --><p>Giới thiệu về công ty Sun Shines. Nội dung trang này được chỉnh sửa trong Trang → Giới thiệu.</p><!-- /wp:paragraph -->' );
		$contact = self::ensure_page( 'lien-he', 'Liên hệ', '', 'page-templates/template-contact.php' );
		$policy  = array();
		foreach ( array(
			'chinh-sach-doi-tra'   => 'Chính sách đổi trả',
			'chinh-sach-giao-hang' => 'Chính sách giao hàng',
			'chinh-sach-bao-mat'   => 'Chính sách bảo mật',
			'huong-dan-thanh-toan' => 'Hướng dẫn thanh toán',
		) as $slug => $title ) {
			$policy[] = self::ensure_page( $slug, $title, '<!-- wp:paragraph --><p>Nội dung ' . mb_strtolower( $title ) . ' (đang cập nhật).</p><!-- /wp:paragraph -->' );
		}
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
		update_option( 'page_for_posts', $news );
		update_option( 'wp_page_for_privacy_policy', $policy[2] );
		$log[] = __( 'Đã tạo/kiểm tra các trang và đặt trang chủ tĩnh.', 'xanh-core' );

		// 4. Menu (chỉ tạo khi vị trí menu còn trống).
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		$menus     = array(
			'primary'       => array( 'Menu chính', array_filter( array( $home, $about, $shop_id, $news, $contact ) ) ),
			'footer_about'  => array( 'Về chúng tôi', array( $about, $news, $contact ) ),
			'footer_policy' => array( 'Chính sách', $policy ),
		);
		$made = 0;
		foreach ( $menus as $loc => $m ) {
			if ( ! empty( $locations[ $loc ] ) && wp_get_nav_menu_object( $locations[ $loc ] ) ) {
				continue;
			}
			$obj     = wp_get_nav_menu_object( $m[0] );
			$menu_id = $obj ? $obj->term_id : wp_create_nav_menu( $m[0] );
			if ( is_wp_error( $menu_id ) ) {
				continue;
			}
			if ( ! wp_get_nav_menu_items( $menu_id ) ) {
				foreach ( $m[1] as $i => $page_id ) {
					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-object-id' => $page_id,
							'menu-item-object'    => 'page',
							'menu-item-type'      => 'post_type',
							'menu-item-status'    => 'publish',
							'menu-item-title'     => $page_id === $home ? 'Trang chủ' : '',
							'menu-item-position'  => $i + 1,
						)
					);
				}
			}
			$locations[ $loc ] = $menu_id;
			++$made;
		}
		set_theme_mod( 'nav_menu_locations', $locations );
		/* translators: %d: number of menus */
		$log[] = sprintf( __( 'Menu: đã gán %d vị trí còn trống (menu có sẵn được giữ nguyên).', 'xanh-core' ), $made );

		// 5. Sản phẩm.
		if ( class_exists( 'WooCommerce' ) ) {
			$pid   = self::seed_product();
			$log[] = $pid
				/* translators: %d: product ID */
				? sprintf( __( 'Sản phẩm Thảo Mộc Mogan: #%d (390.000 ₫).', 'xanh-core' ), $pid )
				: __( 'Không tạo được sản phẩm Thảo Mộc Mogan.', 'xanh-core' );
		}

		flush_rewrite_rules();
		$log[] = __( 'Hoàn tất. Hãy mở trang chủ để kiểm tra.', 'xanh-core' );
		return $log;
	}

	private static function ensure_page( $slug, $title, $content = '', $template = '' ) {
		$page = get_page_by_path( $slug );
		$id   = $page ? $page->ID : wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_name'    => $slug,
				'post_title'   => $title,
				'post_content' => $content,
			)
		);
		if ( $template && $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wp_page_template', $template );
		}
		return (int) $id;
	}

	/**
	 * Tạo/cập nhật sản phẩm Thảo Mộc Mogan (hộp 30 gói x 5g, 390.000 ₫).
	 * Thông tin lấy từ bao bì. Chạy lại an toàn.
	 *
	 * @return int ID sản phẩm (0 nếu lỗi).
	 */
	public static function seed_product() {
		if ( ! class_exists( 'WC_Product_Simple' ) ) {
			return 0;
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$term = term_exists( 'thao-moc-mogan', 'product_cat' );
		if ( ! $term ) {
			$term = wp_insert_term( 'Thảo Mộc Mogan', 'product_cat', array( 'slug' => 'thao-moc-mogan' ) );
		}
		$cat_id = is_wp_error( $term ) ? 0 : (int) $term['term_id'];

		// Ảnh: lấy từ theme, nhập vào Thư viện Media một lần.
		$image_id = (int) get_option( 'xanh_mogan_image_id' );
		if ( ! $image_id || ! get_post( $image_id ) ) {
			$image_id = 0;
			$src      = get_template_directory() . '/assets/images/thao-moc-mogan.jpg';
			if ( file_exists( $src ) ) {
				$tmp = wp_tempnam( 'thao-moc-mogan.jpg' );
				copy( $src, $tmp );
				$res = media_handle_sideload( array( 'name' => 'thao-moc-mogan.jpg', 'tmp_name' => $tmp ), 0, 'Thảo Mộc Mogan – Hộp 30 gói x 5g' );
				if ( ! is_wp_error( $res ) ) {
					$image_id = (int) $res;
					update_post_meta( $image_id, '_wp_attachment_image_alt', 'Hộp Thảo Mộc Mogan 30 gói x 5g' );
					update_option( 'xanh_mogan_image_id', $image_id, false );
				}
			}
		}

		$existing = get_page_by_path( self::PRODUCT_SLUG, OBJECT, 'product' );
		$is_new   = ! $existing;
		$product  = $existing ? wc_get_product( $existing->ID ) : new WC_Product_Simple();
		if ( ! $product ) {
			return 0;
		}

		// Sản phẩm đã có: chỉ bổ sung phần còn thiếu, không ghi đè chỉnh sửa của quản trị viên.
		if ( $is_new ) {
			$product->set_name( 'Thảo Mộc Mogan' );
			$product->set_slug( self::PRODUCT_SLUG );
			$product->set_status( 'publish' );
			$product->set_regular_price( '390000' );
			$product->set_featured( true );
			$product->set_manage_stock( false );
			$product->set_stock_status( 'instock' );
			$product->set_short_description( "Trà thảo mộc túi lọc từ <strong>cà gai leo, diệp hạ châu, xạ đen, mướp đắng, lá đơn</strong>.\nQuy cách: <strong>Hộp 30 gói × 5g</strong>." );
			$product->set_description(
				'<h2>Thành phần</h2>
<p>Cà gai leo, diệp hạ châu, xạ đen, mướp đắng, lá đơn.</p>
<h2>Quy cách</h2>
<p>Hộp 30 gói × 5g (túi lọc).</p>
<h2>Hướng dẫn sử dụng</h2>
<p>Cho 3 túi lọc vào bình giữ nhiệt, ấm hoặc cốc, đổ 0,5 – 1 lít nước sôi vào. Đợi 5 – 10 phút là có thể dùng.</p>
<p>Nên dùng sau ăn khoảng 30 phút và không dùng khi bụng quá đói.</p>
<h2>Hướng dẫn bảo quản</h2>
<p>Bảo quản nơi khô ráo, thoáng mát, tránh ánh nắng trực tiếp.</p>
<h2>Cảnh báo</h2>
<p>Không sử dụng sản phẩm quá hạn hoặc bao bì không còn nguyên vẹn.</p>
<p><strong>Sản phẩm này không phải là thuốc và không có tác dụng thay thế thuốc chữa bệnh.</strong></p>'
			);
			$product->update_meta_data(
				'_xanh_highlights',
				"Thành phần: cà gai leo, diệp hạ châu, xạ đen, mướp đắng, lá đơn\nQuy cách: hộp 30 gói × 5g, dạng túi lọc tiện dùng\nCách dùng: hãm 3 túi lọc với 0,5 – 1 lít nước sôi trong 5 – 10 phút"
			);
		}
		if ( $cat_id && ! $product->get_category_ids() ) {
			$product->set_category_ids( array( $cat_id ) );
		}
		if ( $image_id && ! $product->get_image_id() ) {
			$product->set_image_id( $image_id );
		}
		$product_id = (int) $product->save();

		// Ẩn sản phẩm mẫu cũ (bản demo trước đây).
		foreach ( get_posts( array( 'post_type' => 'product', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'post__not_in' => array( $product_id ) ) ) as $old_id ) {
			if ( str_starts_with( get_the_title( $old_id ), 'Thảo Mộc Mogan – Sản phẩm' ) ) {
				wp_trash_post( $old_id );
			}
		}
		// Banner chưa có ảnh: dùng ảnh sản phẩm.
		if ( $image_id ) {
			foreach ( get_posts( array( 'post_type' => 'xanh_banner', 'posts_per_page' => -1, 'fields' => 'ids' ) ) as $bid ) {
				if ( ! has_post_thumbnail( $bid ) ) {
					set_post_thumbnail( $bid, $image_id );
				}
			}
		}
		return $product_id;
	}
}
