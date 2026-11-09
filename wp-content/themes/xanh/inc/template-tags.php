<?php
/**
 * Hàm hiển thị dùng trong template.
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Đọc cài đặt từ plugin XANH Core (có giá trị dự phòng nếu plugin chưa bật).
 */
function xanh_opt( $key, $fallback = '' ) {
	return function_exists( 'xanh_setting' ) ? xanh_setting( $key, $fallback ) : $fallback;
}

/**
 * Logo website.
 */
function xanh_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	// Logo Sun Shines đi kèm theme (biểu tượng mặt trời + lá) + tên website.
	printf(
		'<a class="logo" href="%1$s" rel="home"><img class="logo__mark" src="%2$s" width="54" height="48" alt="" decoding="async" /><span class="logo__text">%3$s</span></a>',
		esc_url( home_url( '/' ) ),
		esc_url( XANH_THEME_URI . '/assets/images/logo-mark.webp' ),
		esc_html( get_bloginfo( 'name' ) )
	);
}

/**
 * Favicon mặc định (logo Sun Shines) khi chưa đặt "Biểu tượng trang web" trong Tuỳ biến.
 */
add_action(
	'wp_head',
	static function () {
		if ( has_site_icon() ) {
			return;
		}
		$icon = XANH_THEME_URI . '/assets/images/site-icon.png';
		printf( '<link rel="icon" href="%1$s" sizes="512x512" type="image/png" />' . "\n" . '<link rel="apple-touch-icon" href="%1$s" />' . "\n", esc_url( $icon ) );
	},
	5
);

/**
 * Menu dự phòng khi chưa tạo menu trong Giao diện → Menu.
 */
function xanh_menu_fallback() {
	$items = array( home_url( '/' ) => __( 'Trang chủ', 'xanh' ) );
	foreach ( array( 'gioi-thieu' => __( 'Giới thiệu', 'xanh' ), 'lien-he' => __( 'Liên hệ', 'xanh' ) ) as $slug => $label ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			$items[ get_permalink( $page ) ] = $label;
		}
	}
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$items[ wc_get_page_permalink( 'shop' ) ] = __( 'Sản phẩm', 'xanh' );
	}
	if ( get_option( 'page_for_posts' ) ) {
		$items[ get_permalink( get_option( 'page_for_posts' ) ) ] = __( 'Tin tức', 'xanh' );
	}
	echo '<ul class="menu">';
	foreach ( $items as $url => $label ) {
		printf( '<li class="menu-item"><a href="%1$s">%2$s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * Breadcrumb + Schema BreadcrumbList.
 */
function xanh_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() ) ) {
		woocommerce_breadcrumb(
			array(
				'delimiter'   => '<span class="breadcrumbs__sep" aria-hidden="true">/</span>',
				'wrap_before' => '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'xanh' ) . '">',
				'wrap_after'  => '</nav>',
				'home'        => __( 'Trang chủ', 'xanh' ),
			)
		);
		return;
	}

	$crumbs = array( array( __( 'Trang chủ', 'xanh' ), home_url( '/' ) ) );
	$blog   = (int) get_option( 'page_for_posts' );

	if ( is_singular( 'post' ) ) {
		if ( $blog ) {
			$crumbs[] = array( get_the_title( $blog ), get_permalink( $blog ) );
		}
		$cats = get_the_category();
		if ( $cats ) {
			$crumbs[] = array( $cats[0]->name, get_category_link( $cats[0] ) );
		}
		$crumbs[] = array( get_the_title(), '' );
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor ) {
			$crumbs[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
		}
		$crumbs[] = array( get_the_title(), '' );
	} elseif ( is_home() ) {
		$crumbs[] = array( $blog ? get_the_title( $blog ) : __( 'Tin tức', 'xanh' ), '' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		if ( $blog ) {
			$crumbs[] = array( get_the_title( $blog ), get_permalink( $blog ) );
		}
		$crumbs[] = array( single_term_title( '', false ), '' );
	} elseif ( is_search() ) {
		$crumbs[] = array( __( 'Tìm kiếm', 'xanh' ), '' );
	} elseif ( is_404() ) {
		$crumbs[] = array( __( 'Không tìm thấy trang', 'xanh' ), '' );
	} else {
		$crumbs[] = array( wp_strip_all_tags( get_the_archive_title() ), '' );
	}

	$schema = array();
	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'xanh' ) . '">';
	foreach ( $crumbs as $i => $c ) {
		if ( $i ) {
			echo '<span class="breadcrumbs__sep" aria-hidden="true">/</span>';
		}
		if ( $c[1] ) {
			printf( '<a href="%1$s">%2$s</a>', esc_url( $c[1] ), esc_html( $c[0] ) );
		} else {
			printf( '<span aria-current="page">%s</span>', esc_html( $c[0] ) );
		}
		$schema[] = array_filter(
			array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => wp_strip_all_tags( $c[0] ),
				'item'     => $c[1] ? $c[1] : null,
			)
		);
	}
	echo '</nav>';
	echo '<script type="application/ld+json">' . wp_json_encode(
		array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $schema,
		),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	) . '</script>';
}

/**
 * Khối tiêu đề đầu trang (breadcrumb + H1).
 */
function xanh_page_hero( $title, $subtitle = '' ) {
	?>
	<header class="page-hero">
		<div class="container">
			<?php xanh_breadcrumbs(); ?>
			<h1 class="page-hero__title"><?php echo wp_kses_post( $title ); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p class="page-hero__sub"><?php echo wp_kses_post( $subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</header>
	<?php
}

/**
 * Phân trang.
 */
function xanh_pagination() {
	$links = paginate_links(
		array(
			'type'      => 'array',
			'prev_text' => xanh_icon( 'chevron-l', 18 ) . '<span class="screen-reader-text">' . esc_html__( 'Trang trước', 'xanh' ) . '</span>',
			'next_text' => xanh_icon( 'chevron-r', 18 ) . '<span class="screen-reader-text">' . esc_html__( 'Trang sau', 'xanh' ) . '</span>',
		)
	);
	if ( ! $links ) {
		return;
	}
	echo '<nav class="pagination" aria-label="' . esc_attr__( 'Phân trang', 'xanh' ) . '"><ul>';
	foreach ( $links as $link ) {
		echo '<li>' . $link . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core output.
	}
	echo '</ul></nav>';
}

/**
 * Ảnh đại diện hoặc khung ảnh tạm.
 */
function xanh_thumb( $post = null, $size = 'xanh-card', $attr = array() ) {
	if ( has_post_thumbnail( $post ) ) {
		echo get_the_post_thumbnail( $post, $size, $attr );
		return;
	}
	echo '<span class="img-placeholder">' . xanh_icon( 'image', 40 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
