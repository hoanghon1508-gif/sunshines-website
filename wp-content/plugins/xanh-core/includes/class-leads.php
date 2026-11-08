<?php
/**
 * Module quản lý form: lưu khách hàng gửi form liên hệ / đăng ký tư vấn vào trang quản trị.
 *
 * Chống spam (không cần plugin ngoài):
 *  - Honeypot (ô ẩn mà người thật không điền).
 *  - Time-trap có chữ ký: form gửi nhanh hơn 3 giây sau khi tải trang bị từ chối.
 *  - Giới hạn tần suất theo IP (5 lần / 10 phút).
 *  - reCAPTCHA v3 (tuỳ chọn, bật khi nhập key trong "Cài đặt website").
 *
 * Không dùng nonce cho form công khai để form vẫn chạy khi trang được cache.
 *
 * @package XanhCore
 */

defined( 'ABSPATH' ) || exit;

class Xanh_Leads {

	const POST_TYPE   = 'xanh_lead';
	const MIN_SECONDS = 3;
	const RATE_LIMIT  = 5;
	const RATE_WINDOW = 600;

	public static function sources() {
		return array(
			'contact' => __( 'Form liên hệ', 'xanh-core' ),
			'consult' => __( 'Đăng ký tư vấn sản phẩm', 'xanh-core' ),
			'promo'   => __( 'Đăng ký nhận tư vấn (trang chủ)', 'xanh-core' ),
		);
	}

	public static function statuses() {
		return array(
			'new'       => __( 'Mới', 'xanh-core' ),
			'contacted' => __( 'Đã liên hệ', 'xanh-core' ),
			'done'      => __( 'Hoàn tất', 'xanh-core' ),
			'spam'      => __( 'Không hợp lệ', 'xanh-core' ),
		);
	}

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'admin_post_nopriv_xanh_lead', array( __CLASS__, 'handle' ) );
		add_action( 'admin_post_xanh_lead', array( __CLASS__, 'handle' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_boxes' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( __CLASS__, 'save_status' ), 10, 2 );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( __CLASS__, 'filters' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'apply_filters' ) );
		add_action( 'admin_post_xanh_export_leads', array( __CLASS__, 'export' ) );
		add_action( 'manage_posts_extra_tablenav', array( __CLASS__, 'export_button' ) );
		add_filter( 'add_menu_classes', array( __CLASS__, 'menu_badge' ) );
	}

	public static function register() {
		$cap = Xanh_Roles::LEAD_CAP;
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'          => array(
					'name'          => __( 'Khách hàng (form)', 'xanh-core' ),
					'singular_name' => __( 'Khách hàng', 'xanh-core' ),
					'edit_item'     => __( 'Chi tiết khách hàng', 'xanh-core' ),
					'all_items'     => __( 'Danh sách khách hàng', 'xanh-core' ),
					'not_found'     => __( 'Chưa có khách hàng nào gửi form', 'xanh-core' ),
					'search_items'  => __( 'Tìm khách hàng', 'xanh-core' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_position'   => 22,
				'menu_icon'       => 'dashicons-groups',
				'supports'        => array( 'title' ),
				'map_meta_cap'    => true,
				'capability_type' => 'post',
				'capabilities'    => array(
					'edit_posts'             => $cap,
					'edit_others_posts'      => $cap,
					'edit_published_posts'   => $cap,
					'edit_private_posts'     => $cap,
					'publish_posts'          => $cap,
					'delete_posts'           => $cap,
					'delete_others_posts'    => $cap,
					'delete_published_posts' => $cap,
					'delete_private_posts'   => $cap,
					'read_private_posts'     => $cap,
					'create_posts'           => 'do_not_allow',
				),
			)
		);
	}

	/* ---------------------------------------------------------------------
	 * Xử lý form gửi từ giao diện
	 * ------------------------------------------------------------------ */

	/**
	 * Giá trị cho ô time-trap: "timestamp|chữ ký".
	 */
	public static function time_token() {
		$ts = time();
		return $ts . '|' . wp_hash( 'xanh_lead_' . $ts, 'nonce' );
	}

	private static function client_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
	}

	public static function normalize_phone( $phone ) {
		$phone = preg_replace( '/[\s.\-()]/', '', (string) $phone );
		if ( str_starts_with( $phone, '+84' ) ) {
			$phone = '0' . substr( $phone, 3 );
		} elseif ( str_starts_with( $phone, '84' ) && strlen( $phone ) === 11 ) {
			$phone = '0' . substr( $phone, 2 );
		}
		return $phone;
	}

	public static function is_valid_phone( $phone ) {
		return (bool) preg_match( '/^0[1-9][0-9]{8,9}$/', $phone );
	}

	private static function redirect( $status, $anchor ) {
		$back = wp_get_referer();
		if ( ! $back ) {
			$back = home_url( '/' );
		}
		$url = add_query_arg( 'xanh_lead', $status, remove_query_arg( 'xanh_lead', $back ) );
		if ( $anchor ) {
			$url .= '#' . rawurlencode( $anchor );
		}
		wp_safe_redirect( $url );
		exit;
	}

	public static function handle() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- form công khai, xem chú thích đầu file.
		$source = sanitize_key( wp_unslash( $_POST['xanh_source'] ?? 'contact' ) );
		if ( ! array_key_exists( $source, self::sources() ) ) {
			$source = 'contact';
		}
		$anchor = sanitize_key( wp_unslash( $_POST['xanh_anchor'] ?? '' ) );

		// 1. Honeypot.
		if ( ! empty( $_POST['xanh_website'] ) ) {
			self::redirect( 'success', $anchor ); // Giả vờ thành công để bot không thử lại.
		}

		// 2. Time-trap có chữ ký.
		$token = sanitize_text_field( wp_unslash( $_POST['xanh_ts'] ?? '' ) );
		$parts = explode( '|', $token );
		if ( 2 !== count( $parts ) || ! hash_equals( wp_hash( 'xanh_lead_' . $parts[0], 'nonce' ), $parts[1] ) || ( time() - (int) $parts[0] ) < self::MIN_SECONDS ) {
			self::redirect( 'error', $anchor );
		}

		// 3. Giới hạn tần suất theo IP.
		$ip_key = 'xanh_lead_rl_' . md5( self::client_ip() );
		$count  = (int) get_transient( $ip_key );
		if ( $count >= self::RATE_LIMIT ) {
			self::redirect( 'limit', $anchor );
		}

		// 4. reCAPTCHA v3 (nếu cấu hình).
		$secret = Xanh_Settings::get( 'recaptcha_secret_key' );
		if ( $secret && Xanh_Settings::get( 'recaptcha_site_key' ) ) {
			$resp = wp_remote_post(
				'https://www.google.com/recaptcha/api/siteverify',
				array(
					'timeout' => 8,
					'body'    => array(
						'secret'   => $secret,
						'response' => sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ?? '' ) ),
						'remoteip' => self::client_ip(),
					),
				)
			);
			$data = is_wp_error( $resp ) ? null : json_decode( wp_remote_retrieve_body( $resp ), true );
			if ( empty( $data['success'] ) || ( isset( $data['score'] ) && (float) $data['score'] < 0.5 ) ) {
				self::redirect( 'captcha', $anchor );
			}
		}

		// 5. Kiểm tra dữ liệu.
		$name       = sanitize_text_field( wp_unslash( $_POST['xanh_name'] ?? '' ) );
		$phone      = self::normalize_phone( sanitize_text_field( wp_unslash( $_POST['xanh_phone'] ?? '' ) ) );
		$email      = sanitize_email( wp_unslash( $_POST['xanh_email'] ?? '' ) );
		$message    = sanitize_textarea_field( wp_unslash( $_POST['xanh_message'] ?? '' ) );
		$product_id = absint( $_POST['xanh_product'] ?? 0 );
		// phpcs:enable

		if ( ! self::is_valid_phone( $phone ) || ( 'contact' === $source && '' === $name ) ) {
			self::redirect( 'invalid', $anchor );
		}
		$name    = mb_substr( $name, 0, 120 );
		$message = mb_substr( $message, 0, 3000 );

		set_transient( $ip_key, $count + 1, self::RATE_WINDOW );

		$title   = trim( ( $name ? $name : __( 'Khách hàng', 'xanh-core' ) ) . ' – ' . $phone );
		$lead_id = wp_insert_post(
			array(
				'post_type'   => self::POST_TYPE,
				'post_status' => 'publish',
				'post_title'  => $title,
			),
			true
		);
		if ( is_wp_error( $lead_id ) ) {
			self::redirect( 'error', $anchor );
		}

		$meta = array(
			'_xanh_name'    => $name,
			'_xanh_phone'   => $phone,
			'_xanh_email'   => $email,
			'_xanh_message' => $message,
			'_xanh_source'  => $source,
			'_xanh_product' => $product_id && get_post( $product_id ) ? $product_id : 0,
			'_xanh_page'    => esc_url_raw( (string) wp_get_referer() ),
			'_xanh_status'  => 'new',
		);
		foreach ( $meta as $k => $v ) {
			update_post_meta( $lead_id, $k, $v );
		}

		self::notify( $lead_id, $meta );
		do_action( 'xanh_lead_created', $lead_id, $meta );

		self::redirect( 'success', $anchor );
	}

	private static function notify( $lead_id, $meta ) {
		$to = Xanh_Settings::get( 'lead_notify_email', get_option( 'admin_email' ) );
		if ( ! is_email( $to ) ) {
			return;
		}
		$lines = array(
			__( 'Nguồn', 'xanh-core' ) . ': ' . ( self::sources()[ $meta['_xanh_source'] ] ?? $meta['_xanh_source'] ),
			__( 'Họ tên', 'xanh-core' ) . ': ' . $meta['_xanh_name'],
			__( 'Điện thoại', 'xanh-core' ) . ': ' . $meta['_xanh_phone'],
			'Email: ' . $meta['_xanh_email'],
		);
		if ( $meta['_xanh_product'] ) {
			$lines[] = __( 'Sản phẩm', 'xanh-core' ) . ': ' . get_the_title( $meta['_xanh_product'] );
		}
		$lines[] = __( 'Nội dung', 'xanh-core' ) . ': ' . $meta['_xanh_message'];
		$lines[] = '';
		$lines[] = __( 'Xem chi tiết', 'xanh-core' ) . ': ' . admin_url( 'post.php?action=edit&post=' . $lead_id );

		wp_mail(
			$to,
			/* translators: %s: site name */
			sprintf( __( '[%s] Khách hàng mới gửi form', 'xanh-core' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ),
			implode( "\n", $lines )
		);
	}

	/* ---------------------------------------------------------------------
	 * Trang quản trị
	 * ------------------------------------------------------------------ */

	public static function meta_boxes() {
		add_meta_box( 'xanh_lead_info', __( 'Thông tin khách hàng', 'xanh-core' ), array( __CLASS__, 'render_info' ), self::POST_TYPE, 'normal', 'high' );
		add_meta_box( 'xanh_lead_status', __( 'Trạng thái xử lý', 'xanh-core' ), array( __CLASS__, 'render_status' ), self::POST_TYPE, 'side', 'high' );
	}

	public static function render_info( $post ) {
		$rows = array(
			__( 'Họ tên', 'xanh-core' )     => esc_html( get_post_meta( $post->ID, '_xanh_name', true ) ),
			__( 'Điện thoại', 'xanh-core' ) => self::phone_link( get_post_meta( $post->ID, '_xanh_phone', true ) ),
			'Email'                          => esc_html( get_post_meta( $post->ID, '_xanh_email', true ) ),
			__( 'Nguồn', 'xanh-core' )      => esc_html( self::sources()[ get_post_meta( $post->ID, '_xanh_source', true ) ] ?? '' ),
			__( 'Sản phẩm', 'xanh-core' )   => self::product_link( (int) get_post_meta( $post->ID, '_xanh_product', true ) ),
			__( 'Trang gửi', 'xanh-core' )  => esc_html( get_post_meta( $post->ID, '_xanh_page', true ) ),
			__( 'Thời gian', 'xanh-core' )  => esc_html( get_the_date( 'd/m/Y H:i', $post ) ),
			__( 'Nội dung', 'xanh-core' )   => nl2br( esc_html( get_post_meta( $post->ID, '_xanh_message', true ) ) ),
		);
		echo '<table class="form-table" role="presentation">';
		foreach ( $rows as $label => $value ) {
			echo '<tr><th>' . esc_html( $label ) . '</th><td>' . ( '' !== $value ? $value : '—' ) . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- đã escape ở trên.
		}
		echo '</table>';
	}

	private static function phone_link( $phone ) {
		return $phone ? '<a href="tel:' . esc_attr( $phone ) . '">' . esc_html( $phone ) . '</a>' : '';
	}

	private static function product_link( $product_id ) {
		if ( ! $product_id ) {
			return '';
		}
		return '<a href="' . esc_url( get_edit_post_link( $product_id ) ?: get_permalink( $product_id ) ) . '">' . esc_html( get_the_title( $product_id ) ) . '</a>';
	}

	public static function render_status( $post ) {
		wp_nonce_field( 'xanh_lead_status', 'xanh_lead_status_nonce' );
		$current = get_post_meta( $post->ID, '_xanh_status', true ) ?: 'new';
		echo '<select name="xanh_status" style="width:100%">';
		foreach ( self::statuses() as $key => $label ) {
			printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $key ), selected( $current, $key, false ), esc_html( $label ) );
		}
		echo '</select>';
		echo '<p><label for="xanh_note">' . esc_html__( 'Ghi chú nội bộ', 'xanh-core' ) . '</label><textarea id="xanh_note" name="xanh_note" rows="4" style="width:100%">' . esc_textarea( get_post_meta( $post->ID, '_xanh_note', true ) ) . '</textarea></p>';
	}

	public static function save_status( $post_id ) {
		if ( ! isset( $_POST['xanh_lead_status_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['xanh_lead_status_nonce'] ) ), 'xanh_lead_status' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$status = sanitize_key( wp_unslash( $_POST['xanh_status'] ?? 'new' ) );
		if ( array_key_exists( $status, self::statuses() ) ) {
			update_post_meta( $post_id, '_xanh_status', $status );
		}
		update_post_meta( $post_id, '_xanh_note', sanitize_textarea_field( wp_unslash( $_POST['xanh_note'] ?? '' ) ) );
	}

	public static function columns( $columns ) {
		return array(
			'cb'           => $columns['cb'],
			'title'        => __( 'Khách hàng', 'xanh-core' ),
			'xanh_phone'   => __( 'Điện thoại', 'xanh-core' ),
			'xanh_email'   => 'Email',
			'xanh_source'  => __( 'Nguồn', 'xanh-core' ),
			'xanh_product' => __( 'Sản phẩm', 'xanh-core' ),
			'xanh_status'  => __( 'Trạng thái', 'xanh-core' ),
			'date'         => __( 'Thời gian', 'xanh-core' ),
		);
	}

	public static function column( $column, $post_id ) {
		switch ( $column ) {
			case 'xanh_phone':
				echo self::phone_link( get_post_meta( $post_id, '_xanh_phone', true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
			case 'xanh_email':
				echo esc_html( get_post_meta( $post_id, '_xanh_email', true ) );
				break;
			case 'xanh_source':
				echo esc_html( self::sources()[ get_post_meta( $post_id, '_xanh_source', true ) ] ?? '' );
				break;
			case 'xanh_product':
				echo self::product_link( (int) get_post_meta( $post_id, '_xanh_product', true ) ) ?: '—'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
			case 'xanh_status':
				$s      = get_post_meta( $post_id, '_xanh_status', true ) ?: 'new';
				$colors = array( 'new' => '#DC2626', 'contacted' => '#F59E0B', 'done' => '#16A34A', 'spam' => '#94A3B8' );
				printf(
					'<span style="display:inline-block;padding:2px 10px;border-radius:10px;background:%1$s;color:#fff;font-weight:600">%2$s</span>',
					esc_attr( $colors[ $s ] ?? '#94A3B8' ),
					esc_html( self::statuses()[ $s ] ?? $s )
				);
				break;
		}
	}

	public static function filters( $post_type ) {
		if ( self::POST_TYPE !== $post_type ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current = sanitize_key( wp_unslash( $_GET['xanh_status'] ?? '' ) );
		echo '<select name="xanh_status"><option value="">' . esc_html__( 'Tất cả trạng thái', 'xanh-core' ) . '</option>';
		foreach ( self::statuses() as $key => $label ) {
			printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $key ), selected( $current, $key, false ), esc_html( $label ) );
		}
		echo '</select>';
	}

	public static function apply_filters( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() || self::POST_TYPE !== $query->get( 'post_type' ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$status = sanitize_key( wp_unslash( $_GET['xanh_status'] ?? '' ) );
		if ( $status && array_key_exists( $status, self::statuses() ) ) {
			$query->set( 'meta_query', array( array( 'key' => '_xanh_status', 'value' => $status ) ) );
		}
		// Tìm theo số điện thoại / email (lưu trong meta) ngoài tiêu đề.
		$search = $query->get( 's' );
		if ( $search ) {
			$ids = get_posts(
				array(
					'post_type'      => self::POST_TYPE,
					'fields'         => 'ids',
					'posts_per_page' => 200,
					'meta_query'     => array(
						'relation' => 'OR',
						array( 'key' => '_xanh_phone', 'value' => self::normalize_phone( $search ), 'compare' => 'LIKE' ),
						array( 'key' => '_xanh_email', 'value' => $search, 'compare' => 'LIKE' ),
					),
				)
			);
			if ( $ids ) {
				$query->set( 's', '' );
				$query->set( 'post__in', $ids );
			}
		}
	}

	public static function export_button( $which ) {
		global $typenow;
		if ( 'top' !== $which || self::POST_TYPE !== $typenow || ! current_user_can( Xanh_Roles::LEAD_CAP ) ) {
			return;
		}
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=xanh_export_leads' ), 'xanh_export_leads' );
		echo '<div class="alignleft actions"><a class="button" href="' . esc_url( $url ) . '">' . esc_html__( 'Xuất Excel (CSV)', 'xanh-core' ) . '</a></div>';
	}

	public static function export() {
		check_admin_referer( 'xanh_export_leads' );
		if ( ! current_user_can( Xanh_Roles::LEAD_CAP ) ) {
			wp_die( esc_html__( 'Bạn không có quyền thực hiện thao tác này.', 'xanh-core' ), 403 );
		}
		$ids = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=khach-hang-' . gmdate( 'Y-m-d' ) . '.csv' );
		$out = fopen( 'php://output', 'w' );
		fwrite( $out, "\xEF\xBB\xBF" ); // BOM để Excel đọc đúng tiếng Việt.
		fputcsv( $out, array( 'Thời gian', 'Họ tên', 'Điện thoại', 'Email', 'Nguồn', 'Sản phẩm', 'Trạng thái', 'Nội dung', 'Ghi chú' ) );
		foreach ( $ids as $id ) {
			$product = (int) get_post_meta( $id, '_xanh_product', true );
			$row     = array(
				get_the_date( 'd/m/Y H:i', $id ),
				get_post_meta( $id, '_xanh_name', true ),
				get_post_meta( $id, '_xanh_phone', true ),
				get_post_meta( $id, '_xanh_email', true ),
				self::sources()[ get_post_meta( $id, '_xanh_source', true ) ] ?? '',
				$product ? get_the_title( $product ) : '',
				self::statuses()[ get_post_meta( $id, '_xanh_status', true ) ] ?? '',
				get_post_meta( $id, '_xanh_message', true ),
				get_post_meta( $id, '_xanh_note', true ),
			);
			// Chặn CSV injection khi mở bằng Excel.
			$row = array_map(
				static function ( $v ) {
					$v = (string) $v;
					return preg_match( '/^[=+\-@\t\r]/', $v ) ? "'" . $v : $v;
				},
				$row
			);
			fputcsv( $out, $row );
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		exit;
	}

	/**
	 * Hiển thị số khách hàng mới trên menu quản trị.
	 */
	public static function menu_badge( $menu ) {
		if ( ! current_user_can( Xanh_Roles::LEAD_CAP ) ) {
			return $menu;
		}
		$count = get_transient( 'xanh_new_leads_count' );
		if ( false === $count ) {
			$count = count(
				get_posts(
					array(
						'post_type'      => self::POST_TYPE,
						'fields'         => 'ids',
						'posts_per_page' => 100,
						'meta_query'     => array( array( 'key' => '_xanh_status', 'value' => 'new' ) ),
					)
				)
			);
			set_transient( 'xanh_new_leads_count', $count, 5 * MINUTE_IN_SECONDS );
		}
		if ( ! $count ) {
			return $menu;
		}
		foreach ( $menu as $i => $item ) {
			if ( isset( $item[2] ) && 'edit.php?post_type=' . self::POST_TYPE === $item[2] ) {
				$menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . (int) $count . '</span></span>';
			}
		}
		return $menu;
	}
}

add_action(
	'xanh_lead_created',
	static function () {
		delete_transient( 'xanh_new_leads_count' );
	}
);
add_action(
	'save_post_' . Xanh_Leads::POST_TYPE,
	static function () {
		delete_transient( 'xanh_new_leads_count' );
	}
);
