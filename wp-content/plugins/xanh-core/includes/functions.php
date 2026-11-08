<?php
/**
 * Hàm tiện ích cho theme.
 *
 * @package XanhCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lấy một giá trị trong "Cài đặt website".
 */
function xanh_setting( $key, $fallback = '' ) {
	return Xanh_Settings::get( $key, $fallback );
}

/**
 * Lấy banner đang bật theo vị trí.
 */
function xanh_banners( $location, $limit = 10 ) {
	return Xanh_Banners::get( $location, $limit );
}

/**
 * Danh sách liên kết mạng xã hội đã cấu hình.
 *
 * @return array<string, array{label:string, url:string}>
 */
function xanh_social_links() {
	$out = array();
	foreach ( array( 'facebook' => 'Facebook', 'youtube' => 'YouTube', 'instagram' => 'Instagram', 'tiktok' => 'TikTok' ) as $key => $label ) {
		$url = xanh_setting( $key );
		if ( $url ) {
			$out[ $key ] = array( 'label' => $label, 'url' => $url );
		}
		if ( 'facebook' === $key && xanh_setting( 'facebook2' ) ) {
			$out['facebook2'] = array(
				'label' => xanh_setting( 'facebook2_label', 'Facebook 2' ),
				'url'   => xanh_setting( 'facebook2' ),
				'icon'  => 'facebook',
			);
		}
	}
	$zalo = preg_replace( '/\D/', '', (string) xanh_setting( 'zalo' ) );
	if ( $zalo ) {
		$out['zalo'] = array( 'label' => 'Zalo', 'url' => 'https://zalo.me/' . $zalo );
	}
	return $out;
}

/**
 * Thông báo kết quả gửi form (đọc từ ?xanh_lead=...).
 */
function xanh_lead_notice() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$status   = isset( $_GET['xanh_lead'] ) ? sanitize_key( wp_unslash( $_GET['xanh_lead'] ) ) : '';
	$messages = array(
		'success' => array( 'success', __( 'Cảm ơn bạn! Chúng tôi đã nhận được thông tin và sẽ liên hệ sớm.', 'xanh-core' ) ),
		'invalid' => array( 'error', __( 'Vui lòng nhập đầy đủ họ tên và số điện thoại hợp lệ.', 'xanh-core' ) ),
		'limit'   => array( 'error', __( 'Bạn đã gửi quá nhiều lần. Vui lòng thử lại sau ít phút hoặc gọi hotline.', 'xanh-core' ) ),
		'captcha' => array( 'error', __( 'Xác minh chống spam không thành công. Vui lòng thử lại.', 'xanh-core' ) ),
		'error'   => array( 'error', __( 'Gửi không thành công. Vui lòng tải lại trang và thử lại.', 'xanh-core' ) ),
	);
	if ( ! isset( $messages[ $status ] ) ) {
		return '';
	}
	return sprintf(
		'<div class="xanh-notice xanh-notice--%1$s" role="%2$s">%3$s</div>',
		esc_attr( $messages[ $status ][0] ),
		'success' === $messages[ $status ][0] ? 'status' : 'alert',
		esc_html( $messages[ $status ][1] )
	);
}

/**
 * In form khách hàng.
 *
 * @param string $source  contact | consult | promo.
 * @param array  $args {
 *     @type string $anchor        id của khối chứa form (để cuộn lại sau khi gửi).
 *     @type int    $product_id    Sản phẩm (form tư vấn).
 *     @type bool   $show_email
 *     @type bool   $show_message
 *     @type bool   $show_name
 *     @type string $button
 *     @type string $class         Class bổ sung.
 *     @type string $layout        stacked | inline.
 * }
 */
function xanh_lead_form( $source = 'contact', $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'anchor'       => '',
			'product_id'   => 0,
			'show_name'    => true,
			'show_email'   => false,
			'show_message' => false,
			'button'       => __( 'Gửi', 'xanh-core' ),
			'class'        => '',
			'layout'       => 'stacked',
		)
	);
	static $instance = 0;
	++$instance;
	$uid      = 'xf' . $instance;
	$site_key = xanh_setting( 'recaptcha_site_key' );

	if ( $site_key && xanh_setting( 'recaptcha_secret_key' ) ) {
		wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . rawurlencode( $site_key ), array(), null, array( 'strategy' => 'defer' ) ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_add_inline_script(
			'google-recaptcha',
			'document.addEventListener("submit",function(e){var f=e.target;if(!f.matches(".xanh-form")||f.dataset.ok)return;e.preventDefault();grecaptcha.ready(function(){grecaptcha.execute(' . wp_json_encode( $site_key ) . ',{action:"lead"}).then(function(t){f.querySelector("[name=g-recaptcha-response]").value=t;f.dataset.ok=1;f.requestSubmit?f.requestSubmit():f.submit();});});});'
		);
	}

	ob_start();
	?>
	<form class="xanh-form xanh-form--<?php echo esc_attr( $args['layout'] ); ?> <?php echo esc_attr( $args['class'] ); ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="xanh_lead" />
		<input type="hidden" name="xanh_source" value="<?php echo esc_attr( $source ); ?>" />
		<input type="hidden" name="xanh_anchor" value="<?php echo esc_attr( $args['anchor'] ); ?>" />
		<input type="hidden" name="xanh_ts" value="<?php echo esc_attr( Xanh_Leads::time_token() ); ?>" />
		<input type="hidden" name="g-recaptcha-response" value="" />
		<?php if ( $args['product_id'] ) : ?>
			<input type="hidden" name="xanh_product" value="<?php echo (int) $args['product_id']; ?>" />
		<?php endif; ?>
		<div class="xanh-form__hp" aria-hidden="true">
			<label for="<?php echo esc_attr( $uid ); ?>-web">Website</label>
			<input type="text" id="<?php echo esc_attr( $uid ); ?>-web" name="xanh_website" tabindex="-1" autocomplete="off" />
		</div>

		<div class="xanh-form__fields">
			<?php if ( $args['show_name'] ) : ?>
				<div class="xanh-field">
					<label for="<?php echo esc_attr( $uid ); ?>-name"><?php esc_html_e( 'Họ và tên', 'xanh-core' ); ?><?php echo 'contact' === $source ? ' *' : ''; ?></label>
					<input type="text" id="<?php echo esc_attr( $uid ); ?>-name" name="xanh_name" autocomplete="name" maxlength="120" placeholder="<?php esc_attr_e( 'Nguyễn Văn A', 'xanh-core' ); ?>" <?php echo 'contact' === $source ? 'required' : ''; ?> />
				</div>
			<?php endif; ?>
			<div class="xanh-field">
				<label for="<?php echo esc_attr( $uid ); ?>-phone"><?php esc_html_e( 'Số điện thoại', 'xanh-core' ); ?> *</label>
				<input type="tel" id="<?php echo esc_attr( $uid ); ?>-phone" name="xanh_phone" autocomplete="tel" inputmode="tel" required pattern="[0-9 +.\-]{9,15}" placeholder="<?php esc_attr_e( 'Số điện thoại của bạn', 'xanh-core' ); ?>" />
			</div>
			<?php if ( $args['show_email'] ) : ?>
				<div class="xanh-field">
					<label for="<?php echo esc_attr( $uid ); ?>-email">Email</label>
					<input type="email" id="<?php echo esc_attr( $uid ); ?>-email" name="xanh_email" autocomplete="email" placeholder="email@example.com" />
				</div>
			<?php endif; ?>
			<?php if ( $args['show_message'] ) : ?>
				<div class="xanh-field xanh-field--full">
					<label for="<?php echo esc_attr( $uid ); ?>-msg"><?php esc_html_e( 'Nội dung', 'xanh-core' ); ?></label>
					<textarea id="<?php echo esc_attr( $uid ); ?>-msg" name="xanh_message" rows="5" maxlength="3000" placeholder="<?php esc_attr_e( 'Bạn cần hỗ trợ gì?', 'xanh-core' ); ?>"></textarea>
				</div>
			<?php endif; ?>
		</div>
		<button type="submit" class="btn btn--primary xanh-form__submit"><?php echo esc_html( $args['button'] ); ?></button>
		<?php if ( $site_key ) : ?>
			<p class="xanh-form__legal"><?php esc_html_e( 'Trang được bảo vệ bởi reCAPTCHA của Google.', 'xanh-core' ); ?></p>
		<?php endif; ?>
	</form>
	<?php
	return (string) ob_get_clean();
}
