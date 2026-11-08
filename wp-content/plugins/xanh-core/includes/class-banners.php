<?php
/**
 * Module quản lý banner: hình ảnh, link trang đích, thứ tự, bật/tắt, vị trí hiển thị.
 *
 * @package XanhCore
 */

defined( 'ABSPATH' ) || exit;

class Xanh_Banners {

	const POST_TYPE = 'xanh_banner';

	public static function locations() {
		return array(
			'home_slider'  => __( 'Trang chủ – Slider đầu trang', 'xanh-core' ),
			'home_middle'  => __( 'Trang chủ – Banner giữa trang', 'xanh-core' ),
			'shop_top'     => __( 'Trang sản phẩm – Đầu trang', 'xanh-core' ),
			'sidebar'      => __( 'Thanh bên (tin tức)', 'xanh-core' ),
		);
	}

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_boxes' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( __CLASS__, 'save' ), 10, 2 );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column' ), 10, 2 );
		add_filter( 'manage_edit-' . self::POST_TYPE . '_sortable_columns', array( __CLASS__, 'sortable' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'admin_order' ) );
		add_filter( 'post_row_actions', array( __CLASS__, 'row_actions' ), 10, 2 );
		add_action( 'admin_post_xanh_toggle_banner', array( __CLASS__, 'toggle' ) );
		add_filter( 'enter_title_here', array( __CLASS__, 'title_placeholder' ), 10, 2 );
	}

	public static function register() {
		$cap = Xanh_Roles::BANNER_CAP;
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'          => array(
					'name'          => __( 'Banner', 'xanh-core' ),
					'singular_name' => __( 'Banner', 'xanh-core' ),
					'add_new'       => __( 'Thêm banner', 'xanh-core' ),
					'add_new_item'  => __( 'Thêm banner mới', 'xanh-core' ),
					'edit_item'     => __( 'Sửa banner', 'xanh-core' ),
					'all_items'     => __( 'Tất cả banner', 'xanh-core' ),
					'not_found'     => __( 'Chưa có banner nào', 'xanh-core' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_position'   => 21,
				'menu_icon'       => 'dashicons-images-alt2',
				'supports'        => array( 'title', 'thumbnail', 'page-attributes' ),
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
					'create_posts'           => $cap,
				),
			)
		);
	}

	public static function title_placeholder( $text, $post ) {
		return self::POST_TYPE === $post->post_type ? __( 'Tiêu đề banner (hiển thị trên ảnh)', 'xanh-core' ) : $text;
	}

	public static function meta_boxes() {
		add_meta_box( 'xanh_banner_meta', __( 'Thiết lập banner', 'xanh-core' ), array( __CLASS__, 'render_meta' ), self::POST_TYPE, 'normal', 'high' );
	}

	public static function render_meta( $post ) {
		wp_nonce_field( 'xanh_banner_save', 'xanh_banner_nonce' );
		$link     = get_post_meta( $post->ID, '_xanh_link', true );
		$subtitle = get_post_meta( $post->ID, '_xanh_subtitle', true );
		$button   = get_post_meta( $post->ID, '_xanh_button', true );
		$location = get_post_meta( $post->ID, '_xanh_location', true ) ?: 'home_slider';
		$active   = 'auto-draft' === $post->post_status ? '1' : get_post_meta( $post->ID, '_xanh_active', true );
		$new_tab  = get_post_meta( $post->ID, '_xanh_new_tab', true );
		?>
		<p><?php esc_html_e( 'Ảnh banner: dùng ô "Ảnh đại diện" bên phải. Kích thước đề xuất: 1200×900px (slider) hoặc 1200×400px (banner ngang).', 'xanh-core' ); ?></p>
		<table class="form-table" role="presentation">
			<tr>
				<th><label for="xanh_location"><?php esc_html_e( 'Vị trí hiển thị', 'xanh-core' ); ?></label></th>
				<td>
					<select id="xanh_location" name="xanh_location">
						<?php foreach ( self::locations() as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $location, $key ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="xanh_subtitle"><?php esc_html_e( 'Mô tả ngắn', 'xanh-core' ); ?></label></th>
				<td><textarea id="xanh_subtitle" name="xanh_subtitle" class="large-text" rows="2"><?php echo esc_textarea( $subtitle ); ?></textarea></td>
			</tr>
			<tr>
				<th><label for="xanh_link"><?php esc_html_e( 'Link trang đích', 'xanh-core' ); ?></label></th>
				<td>
					<input type="url" id="xanh_link" name="xanh_link" class="large-text" value="<?php echo esc_attr( $link ); ?>" placeholder="https://" />
					<label><input type="checkbox" name="xanh_new_tab" value="1" <?php checked( $new_tab, '1' ); ?> /> <?php esc_html_e( 'Mở trong tab mới', 'xanh-core' ); ?></label>
				</td>
			</tr>
			<tr>
				<th><label for="xanh_button"><?php esc_html_e( 'Chữ trên nút', 'xanh-core' ); ?></label></th>
				<td><input type="text" id="xanh_button" name="xanh_button" class="regular-text" value="<?php echo esc_attr( $button ); ?>" placeholder="<?php esc_attr_e( 'Mua sắm ngay', 'xanh-core' ); ?>" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Trạng thái', 'xanh-core' ); ?></th>
				<td><label><input type="checkbox" name="xanh_active" value="1" <?php checked( $active, '1' ); ?> /> <?php esc_html_e( 'Bật banner', 'xanh-core' ); ?></label></td>
			</tr>
		</table>
		<p class="description"><?php esc_html_e( 'Thứ tự hiển thị: chỉnh ô "Thứ tự" trong khung "Thuộc tính" (số nhỏ hiển thị trước).', 'xanh-core' ); ?></p>
		<?php
	}

	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST['xanh_banner_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['xanh_banner_nonce'] ) ), 'xanh_banner_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$location = sanitize_key( wp_unslash( $_POST['xanh_location'] ?? 'home_slider' ) );
		if ( ! array_key_exists( $location, self::locations() ) ) {
			$location = 'home_slider';
		}
		update_post_meta( $post_id, '_xanh_location', $location );
		update_post_meta( $post_id, '_xanh_link', esc_url_raw( wp_unslash( $_POST['xanh_link'] ?? '' ) ) );
		update_post_meta( $post_id, '_xanh_subtitle', sanitize_textarea_field( wp_unslash( $_POST['xanh_subtitle'] ?? '' ) ) );
		update_post_meta( $post_id, '_xanh_button', sanitize_text_field( wp_unslash( $_POST['xanh_button'] ?? '' ) ) );
		update_post_meta( $post_id, '_xanh_active', empty( $_POST['xanh_active'] ) ? '0' : '1' );
		update_post_meta( $post_id, '_xanh_new_tab', empty( $_POST['xanh_new_tab'] ) ? '0' : '1' );
	}

	public static function columns( $columns ) {
		return array(
			'cb'            => $columns['cb'],
			'xanh_image'    => __( 'Ảnh', 'xanh-core' ),
			'title'         => __( 'Tiêu đề', 'xanh-core' ),
			'xanh_location' => __( 'Vị trí', 'xanh-core' ),
			'xanh_link'     => __( 'Link đích', 'xanh-core' ),
			'menu_order'    => __( 'Thứ tự', 'xanh-core' ),
			'xanh_active'   => __( 'Trạng thái', 'xanh-core' ),
		);
	}

	public static function column( $column, $post_id ) {
		switch ( $column ) {
			case 'xanh_image':
				echo get_the_post_thumbnail( $post_id, array( 120, 60 ), array( 'style' => 'width:120px;height:60px;object-fit:cover;border-radius:4px' ) );
				break;
			case 'xanh_location':
				$loc = get_post_meta( $post_id, '_xanh_location', true );
				echo esc_html( self::locations()[ $loc ] ?? '—' );
				break;
			case 'xanh_link':
				$link = get_post_meta( $post_id, '_xanh_link', true );
				echo $link ? '<a href="' . esc_url( $link ) . '" target="_blank" rel="noopener">' . esc_html( wp_parse_url( $link, PHP_URL_PATH ) ?: $link ) . '</a>' : '—';
				break;
			case 'menu_order':
				echo (int) get_post_field( 'menu_order', $post_id );
				break;
			case 'xanh_active':
				$on = '1' === get_post_meta( $post_id, '_xanh_active', true );
				printf(
					'<span style="display:inline-block;padding:2px 10px;border-radius:10px;background:%s;color:#fff;font-weight:600">%s</span>',
					$on ? '#16A34A' : '#94A3B8',
					$on ? esc_html__( 'Đang bật', 'xanh-core' ) : esc_html__( 'Đang tắt', 'xanh-core' )
				);
				break;
		}
	}

	public static function sortable( $columns ) {
		$columns['menu_order'] = 'menu_order';
		return $columns;
	}

	public static function admin_order( $query ) {
		if ( is_admin() && $query->is_main_query() && self::POST_TYPE === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
		}
	}

	public static function row_actions( $actions, $post ) {
		if ( self::POST_TYPE !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}
		$on  = '1' === get_post_meta( $post->ID, '_xanh_active', true );
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=xanh_toggle_banner&post=' . $post->ID ), 'xanh_toggle_banner_' . $post->ID );
		$actions['xanh_toggle'] = '<a href="' . esc_url( $url ) . '">' . ( $on ? esc_html__( 'Tắt', 'xanh-core' ) : esc_html__( 'Bật', 'xanh-core' ) ) . '</a>';
		return $actions;
	}

	public static function toggle() {
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		check_admin_referer( 'xanh_toggle_banner_' . $post_id );
		if ( ! $post_id || self::POST_TYPE !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( esc_html__( 'Bạn không có quyền thực hiện thao tác này.', 'xanh-core' ), 403 );
		}
		$on = '1' === get_post_meta( $post_id, '_xanh_active', true );
		update_post_meta( $post_id, '_xanh_active', $on ? '0' : '1' );
		wp_safe_redirect( admin_url( 'edit.php?post_type=' . self::POST_TYPE ) );
		exit;
	}

	/**
	 * Lấy danh sách banner đang bật theo vị trí, đã sắp xếp.
	 *
	 * @return array<int, array{id:int, title:string, subtitle:string, link:string, button:string, new_tab:bool, image_id:int}>
	 */
	public static function get( $location, $limit = 10 ) {
		$posts = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
				'meta_query'     => array(
					'relation' => 'AND',
					array( 'key' => '_xanh_active', 'value' => '1' ),
					array( 'key' => '_xanh_location', 'value' => $location ),
				),
			)
		);
		return array_map(
			static function ( $p ) {
				return array(
					'id'       => $p->ID,
					'title'    => get_the_title( $p ),
					'subtitle' => (string) get_post_meta( $p->ID, '_xanh_subtitle', true ),
					'link'     => (string) get_post_meta( $p->ID, '_xanh_link', true ),
					'button'   => (string) get_post_meta( $p->ID, '_xanh_button', true ),
					'new_tab'  => '1' === get_post_meta( $p->ID, '_xanh_new_tab', true ),
					'image_id' => (int) get_post_thumbnail_id( $p ),
				);
			},
			$posts
		);
	}
}
