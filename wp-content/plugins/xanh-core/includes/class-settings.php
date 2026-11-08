<?php
/**
 * Trang "Cài đặt website": thông tin liên hệ, mạng xã hội, liên hệ nhanh, reCAPTCHA.
 *
 * @package XanhCore
 */

defined( 'ABSPATH' ) || exit;

class Xanh_Settings {

	const OPTION = 'xanh_settings';

	/**
	 * Danh sách trường cài đặt, nhóm theo section.
	 *
	 * @return array<string, array{title:string, fields:array<string, array{label:string, type:string, help?:string}>}>
	 */
	public static function schema() {
		return array(
			'company' => array(
				'title'  => __( 'Thông tin doanh nghiệp', 'xanh-core' ),
				'fields' => array(
					'company_name'  => array( 'label' => __( 'Tên công ty', 'xanh-core' ), 'type' => 'text' ),
					'address'       => array( 'label' => __( 'Địa chỉ', 'xanh-core' ), 'type' => 'text' ),
					'hotline'       => array( 'label' => __( 'Hotline', 'xanh-core' ), 'type' => 'text' ),
					'email'         => array( 'label' => __( 'Email', 'xanh-core' ), 'type' => 'email' ),
					'working_hours' => array( 'label' => __( 'Giờ làm việc', 'xanh-core' ), 'type' => 'text' ),
					'topbar_text'   => array( 'label' => __( 'Dòng thông báo trên cùng', 'xanh-core' ), 'type' => 'text' ),
					'map_embed'     => array(
						'label' => __( 'Link nhúng Google Maps', 'xanh-core' ),
						'type'  => 'url',
						'help'  => __( 'Google Maps → Chia sẻ → Nhúng bản đồ → copy giá trị src của iframe (bắt đầu bằng https://www.google.com/maps/embed).', 'xanh-core' ),
					),
				),
			),
			'social'  => array(
				'title'  => __( 'Liên kết mạng xã hội', 'xanh-core' ),
				'fields' => array(
					'facebook'  => array( 'label' => 'Facebook', 'type' => 'url' ),
					'youtube'   => array( 'label' => 'YouTube', 'type' => 'url' ),
					'instagram' => array( 'label' => 'Instagram', 'type' => 'url' ),
					'tiktok'    => array( 'label' => 'TikTok', 'type' => 'url' ),
				),
			),
			'quick'   => array(
				'title'  => __( 'Nút liên hệ nhanh', 'xanh-core' ),
				'fields' => array(
					'quick_enabled' => array( 'label' => __( 'Hiển thị nút liên hệ nhanh', 'xanh-core' ), 'type' => 'checkbox' ),
					'zalo'          => array(
						'label' => __( 'Số điện thoại Zalo', 'xanh-core' ),
						'type'  => 'text',
						'help'  => __( 'Ví dụ: 0900000000. Nút Zalo sẽ mở https://zalo.me/<số>.', 'xanh-core' ),
					),
					'messenger'     => array( 'label' => __( 'Link Messenger (m.me/...)', 'xanh-core' ), 'type' => 'url' ),
				),
			),
			'forms'   => array(
				'title'  => __( 'Form & chống spam', 'xanh-core' ),
				'fields' => array(
					'lead_notify_email'    => array(
						'label' => __( 'Email nhận thông báo form', 'xanh-core' ),
						'type'  => 'email',
						'help'  => __( 'Để trống sẽ dùng email quản trị của website.', 'xanh-core' ),
					),
					'recaptcha_site_key'   => array(
						'label' => __( 'reCAPTCHA v3 – Site key', 'xanh-core' ),
						'type'  => 'text',
						'help'  => __( 'Để trống nếu không dùng reCAPTCHA. Form vẫn được chống spam bằng honeypot và giới hạn tần suất.', 'xanh-core' ),
					),
					'recaptcha_secret_key' => array( 'label' => __( 'reCAPTCHA v3 – Secret key', 'xanh-core' ), 'type' => 'password' ),
				),
			),
		);
	}

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
	}

	public static function defaults() {
		return array(
			'company_name'  => 'Công ty TNHH XANH',
			'address'       => '123 Đường ABC, Quận 1, TP. Hồ Chí Minh',
			'hotline'       => '0900 000 000',
			'email'         => 'lienhe@xanh.vn',
			'working_hours' => '8:00 – 18:00, Thứ 2 – Thứ 7',
			'topbar_text'   => 'Miễn phí giao hàng cho đơn từ 500.000 ₫',
			'quick_enabled' => 1,
		);
	}

	/**
	 * Lấy một giá trị cài đặt.
	 */
	public static function get( $key, $fallback = '' ) {
		$opts = wp_parse_args( (array) get_option( self::OPTION, array() ), self::defaults() );
		return isset( $opts[ $key ] ) && '' !== $opts[ $key ] ? $opts[ $key ] : $fallback;
	}

	public static function menu() {
		add_menu_page(
			__( 'Cài đặt website', 'xanh-core' ),
			__( 'Cài đặt website', 'xanh-core' ),
			'manage_options',
			'xanh-settings',
			array( __CLASS__, 'render' ),
			'dashicons-admin-site-alt3',
			58
		);
	}

	public static function register() {
		register_setting(
			'xanh_settings_group',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);

		foreach ( self::schema() as $section_id => $section ) {
			add_settings_section( 'xanh_' . $section_id, $section['title'], '__return_false', 'xanh-settings' );
			foreach ( $section['fields'] as $key => $field ) {
				add_settings_field(
					$key,
					$field['label'],
					array( __CLASS__, 'field' ),
					'xanh-settings',
					'xanh_' . $section_id,
					array(
						'key'       => $key,
						'field'     => $field,
						'label_for' => 'xanh_' . $key,
					)
				);
			}
		}
	}

	public static function sanitize( $input ) {
		$input = is_array( $input ) ? $input : array();
		$clean = array();
		foreach ( self::schema() as $section ) {
			foreach ( $section['fields'] as $key => $field ) {
				$value = $input[ $key ] ?? '';
				switch ( $field['type'] ) {
					case 'url':
						$clean[ $key ] = esc_url_raw( trim( (string) $value ) );
						break;
					case 'email':
						$clean[ $key ] = sanitize_email( (string) $value );
						break;
					case 'checkbox':
						$clean[ $key ] = empty( $value ) ? 0 : 1;
						break;
					case 'password':
						// Giữ secret cũ nếu ô để trống (không hiển thị lại secret trong HTML).
						$clean[ $key ] = '' === trim( (string) $value ) ? self::get( $key ) : sanitize_text_field( (string) $value );
						break;
					default:
						$clean[ $key ] = sanitize_text_field( (string) $value );
				}
			}
		}
		if ( ! empty( $clean['map_embed'] ) && ! str_starts_with( $clean['map_embed'], 'https://www.google.com/maps/embed' ) ) {
			add_settings_error( self::OPTION, 'map', __( 'Link Google Maps phải bắt đầu bằng https://www.google.com/maps/embed', 'xanh-core' ) );
			$clean['map_embed'] = '';
		}
		return $clean;
	}

	public static function field( $args ) {
		$key   = $args['key'];
		$field = $args['field'];
		$name  = self::OPTION . '[' . $key . ']';
		$id    = 'xanh_' . $key;
		$value = self::get( $key );

		if ( 'checkbox' === $field['type'] ) {
			printf(
				'<input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s />',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( (int) $value, 1, false )
			);
		} elseif ( 'password' === $field['type'] ) {
			printf(
				'<input type="password" class="regular-text" id="%1$s" name="%2$s" value="" autocomplete="new-password" placeholder="%3$s" />',
				esc_attr( $id ),
				esc_attr( $name ),
				$value ? esc_attr__( '•••••• (đã lưu – để trống nếu không đổi)', 'xanh-core' ) : ''
			);
		} else {
			printf(
				'<input type="%1$s" class="regular-text" id="%2$s" name="%3$s" value="%4$s" />',
				esc_attr( 'url' === $field['type'] || 'email' === $field['type'] ? $field['type'] : 'text' ),
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value )
			);
		}
		if ( ! empty( $field['help'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field['help'] ) );
		}
	}

	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Cài đặt website', 'xanh-core' ); ?></h1>
			<?php settings_errors( self::OPTION ); ?>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'xanh_settings_group' );
				do_settings_sections( 'xanh-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
