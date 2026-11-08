<?php
/**
 * Tích hợp WooCommerce: thẻ sản phẩm, % giảm giá, form tư vấn, giỏ hàng mini, bộ lọc.
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tính % giảm giá của sản phẩm (hỗ trợ sản phẩm biến thể).
 */
function xanh_discount_percent( $product ) {
	if ( ! $product || ! $product->is_on_sale() ) {
		return 0;
	}
	if ( $product->is_type( 'variable' ) ) {
		$max = 0;
		foreach ( $product->get_children() as $child_id ) {
			$child = wc_get_product( $child_id );
			if ( $child && $child->is_on_sale() ) {
				$max = max( $max, xanh_discount_percent( $child ) );
			}
		}
		return $max;
	}
	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();
	if ( $regular <= 0 || $sale <= 0 || $sale >= $regular ) {
		return 0;
	}
	return (int) round( ( $regular - $sale ) / $regular * 100 );
}

/**
 * Nhãn "-22%" thay cho nhãn "Sale!" mặc định.
 */
add_filter(
	'woocommerce_sale_flash',
	static function ( $html, $post, $product ) {
		$pct = xanh_discount_percent( $product );
		return $pct ? '<span class="badge badge--sale">-' . (int) $pct . '%</span>' : '';
	},
	10,
	3
);

/**
 * Bỏ wrapper mặc định của WooCommerce (theme dùng woocommerce.php).
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
// Trang chi tiết đã có nhãn % cạnh giá, bỏ nhãn trùng trên ảnh.
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

/**
 * Trang chi tiết: % giảm giá cạnh giá + form đăng ký nhận tư vấn.
 */
add_action(
	'woocommerce_single_product_summary',
	static function () {
		global $product;
		$pct = xanh_discount_percent( $product );
		if ( $pct ) {
			/* translators: %d: percent */
			echo '<p class="single-discount"><span class="badge badge--sale">' . esc_html( sprintf( __( 'Giảm %d%%', 'xanh' ), $pct ) ) . '</span></p>';
		}
	},
	11
);

add_action(
	'woocommerce_single_product_summary',
	static function () {
		if ( ! function_exists( 'xanh_lead_form' ) ) {
			return;
		}
		global $product;
		echo '<div class="consult-box" id="tu-van">';
		echo '<h2 class="consult-box__title">' . esc_html__( 'Đăng ký nhận tư vấn về sản phẩm', 'xanh' ) . '</h2>';
		echo xanh_lead_notice(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo xanh_lead_form( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'consult',
			array(
				'anchor'     => 'tu-van',
				'product_id' => $product->get_id(),
				'button'     => __( 'Gửi', 'xanh' ),
				'layout'     => 'inline',
			)
		);
		echo '</div>';
	},
	35
);

/**
 * Số sản phẩm liên quan.
 */
add_filter(
	'woocommerce_output_related_products_args',
	static function ( $args ) {
		$args['posts_per_page'] = 4;
		$args['columns']        = 4;
		return $args;
	}
);

/**
 * Số sản phẩm mỗi trang và số cột.
 */
add_filter(
	'loop_shop_per_page',
	static function () {
		return 12;
	}
);
add_filter(
	'loop_shop_columns',
	static function () {
		return 3;
	}
);

/**
 * Bộ lọc "Đang giảm giá" (?on_sale=1).
 */
add_action(
	'woocommerce_product_query',
	static function ( $q ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['on_sale'] ) ) {
			$ids = wc_get_product_ids_on_sale();
			$q->set( 'post__in', $ids ? $ids : array( 0 ) );
		}
	}
);

/**
 * Cập nhật số lượng giỏ hàng trên header bằng AJAX.
 */
add_filter(
	'woocommerce_add_to_cart_fragments',
	static function ( $fragments ) {
		$fragments['span.cart-count'] = '<span class="cart-count">' . (int) WC()->cart->get_cart_contents_count() . '</span>';
		return $fragments;
	}
);

/**
 * Bỏ các trường không cần thiết ở Việt Nam (mã bưu chính, công ty, địa chỉ 2).
 */
add_filter(
	'woocommerce_default_address_fields',
	static function ( $fields ) {
		foreach ( array( 'company', 'address_2', 'postcode' ) as $key ) {
			if ( isset( $fields[ $key ] ) ) {
				$fields[ $key ]['required'] = false;
				$fields[ $key ]['hidden']   = true;
			}
		}
		return $fields;
	}
);
add_filter(
	'woocommerce_get_country_locale',
	static function ( $locale ) {
		$locale['VN']['postcode'] = array(
			'required' => false,
			'hidden'   => true,
		);
		return $locale;
	}
);

/**
 * Danh mục sản phẩm (dùng cho trang chủ & bộ lọc).
 *
 * @return WP_Term[]
 */
function xanh_product_categories( $limit = 8 ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => 0,
			'number'     => $limit,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			'orderby'    => 'meta_value_num',
			'meta_key'   => 'order', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		)
	);
	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Bộ lọc ở thanh bên trang sản phẩm.
 */
function xanh_shop_filters() {
	$base = is_product_category() ? get_term_link( get_queried_object() ) : wc_get_page_permalink( 'shop' );
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$min     = isset( $_GET['min_price'] ) ? absint( $_GET['min_price'] ) : null;
	$max     = isset( $_GET['max_price'] ) ? absint( $_GET['max_price'] ) : null;
	$on_sale = ! empty( $_GET['on_sale'] );
	// phpcs:enable
	$current = is_product_category() ? get_queried_object_id() : 0;
	?>
	<aside class="shop-filters" aria-label="<?php esc_attr_e( 'Bộ lọc sản phẩm', 'xanh' ); ?>">
		<button class="shop-filters__toggle btn btn--outline" type="button" aria-expanded="false" aria-controls="shop-filters-body"><?php esc_html_e( 'Bộ lọc', 'xanh' ); ?></button>
		<div class="shop-filters__body" id="shop-filters-body">
			<div class="filter-group">
				<h2 class="filter-group__title"><?php esc_html_e( 'Danh mục', 'xanh' ); ?></h2>
				<ul>
					<li><a class="filter-link <?php echo $current ? '' : 'is-active'; ?>" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Tất cả sản phẩm', 'xanh' ); ?></a></li>
					<?php foreach ( xanh_product_categories( 30 ) as $term ) : ?>
						<li><a class="filter-link <?php echo $current === $term->term_id ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?> <span>(<?php echo (int) $term->count; ?>)</span></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="filter-group">
				<h2 class="filter-group__title"><?php esc_html_e( 'Khoảng giá', 'xanh' ); ?></h2>
				<ul>
					<?php
					$ranges = array(
						array( null, 100000, __( 'Dưới 100.000 ₫', 'xanh' ) ),
						array( 100000, 300000, __( '100.000 – 300.000 ₫', 'xanh' ) ),
						array( 300000, 500000, __( '300.000 – 500.000 ₫', 'xanh' ) ),
						array( 500000, null, __( 'Trên 500.000 ₫', 'xanh' ) ),
					);
					foreach ( $ranges as $r ) :
						$active = $min === $r[0] && $max === $r[1];
						$url    = remove_query_arg( array( 'min_price', 'max_price', 'paged' ), $base );
						if ( ! $active ) {
							$url = add_query_arg( array_filter( array( 'min_price' => $r[0], 'max_price' => $r[1] ), 'is_int' ), $url );
						}
						if ( $on_sale ) {
							$url = add_query_arg( 'on_sale', 1, $url );
						}
						?>
						<li><a class="filter-link filter-link--check <?php echo $active ? 'is-active' : ''; ?>" href="<?php echo esc_url( $url ); ?>" rel="nofollow"><?php echo esc_html( $r[2] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="filter-group">
				<h2 class="filter-group__title"><?php esc_html_e( 'Khuyến mãi', 'xanh' ); ?></h2>
				<?php
				$sale_url = $on_sale ? remove_query_arg( 'on_sale' ) : add_query_arg( 'on_sale', 1, remove_query_arg( 'paged' ) );
				?>
				<a class="filter-link filter-link--check <?php echo $on_sale ? 'is-active' : ''; ?>" href="<?php echo esc_url( $sale_url ); ?>" rel="nofollow"><?php esc_html_e( 'Đang giảm giá', 'xanh' ); ?></a>
			</div>
		</div>
	</aside>
	<?php
}
