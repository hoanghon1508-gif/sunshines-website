<?php
/**
 * SEO kỹ thuật cơ bản: meta title/description, Open Graph, canonical, Schema Organization,
 * Schema FAQ, robots.txt kèm sitemap.
 *
 * WordPress đã có sẵn: sitemap XML (/wp-sitemap.xml), canonical cho bài viết/trang, thẻ <title>.
 * Nếu cài Yoast SEO hoặc Rank Math, module này tự tắt phần meta để tránh trùng lặp.
 *
 * @package XanhCore
 */

defined( 'ABSPATH' ) || exit;

class Xanh_SEO {

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box' ) );
		add_action( 'save_post', array( __CLASS__, 'save' ), 10, 2 );
		add_filter( 'robots_txt', array( __CLASS__, 'robots' ), 10, 2 );
		add_action( 'init', array( __CLASS__, 'faq_shortcode' ) );
		add_action( 'template_redirect', array( __CLASS__, 'boot_frontend' ) );
	}

	public static function seo_plugin_active() {
		return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath' );
	}

	public static function boot_frontend() {
		if ( self::seo_plugin_active() ) {
			return;
		}
		add_filter( 'pre_get_document_title', array( __CLASS__, 'title' ), 20 );
		add_action( 'wp_head', array( __CLASS__, 'head' ), 1 );
	}

	private static function post_types() {
		return array( 'post', 'page', 'product' );
	}

	/* -------------------------------- Meta box -------------------------------- */

	public static function meta_box() {
		if ( self::seo_plugin_active() ) {
			return;
		}
		foreach ( self::post_types() as $pt ) {
			add_meta_box( 'xanh_seo', __( 'SEO', 'xanh-core' ), array( __CLASS__, 'render' ), $pt, 'normal', 'low' );
		}
	}

	public static function render( $post ) {
		wp_nonce_field( 'xanh_seo_save', 'xanh_seo_nonce' );
		$title = get_post_meta( $post->ID, '_xanh_seo_title', true );
		$desc  = get_post_meta( $post->ID, '_xanh_seo_desc', true );
		?>
		<p>
			<label for="xanh_seo_title"><strong><?php esc_html_e( 'Meta Title', 'xanh-core' ); ?></strong> <small><?php esc_html_e( '(khuyến nghị ≤ 60 ký tự, để trống sẽ dùng tiêu đề bài)', 'xanh-core' ); ?></small></label>
			<input type="text" class="widefat" id="xanh_seo_title" name="xanh_seo_title" value="<?php echo esc_attr( $title ); ?>" maxlength="120" />
		</p>
		<p>
			<label for="xanh_seo_desc"><strong><?php esc_html_e( 'Meta Description', 'xanh-core' ); ?></strong> <small><?php esc_html_e( '(khuyến nghị 120–160 ký tự, để trống sẽ dùng đoạn tóm tắt)', 'xanh-core' ); ?></small></label>
			<textarea class="widefat" id="xanh_seo_desc" name="xanh_seo_desc" rows="3" maxlength="300"><?php echo esc_textarea( $desc ); ?></textarea>
		</p>
		<?php
	}

	public static function save( $post_id, $post ) {
		if ( ! in_array( $post->post_type, self::post_types(), true ) ) {
			return;
		}
		if ( ! isset( $_POST['xanh_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['xanh_seo_nonce'] ) ), 'xanh_seo_save' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		update_post_meta( $post_id, '_xanh_seo_title', sanitize_text_field( wp_unslash( $_POST['xanh_seo_title'] ?? '' ) ) );
		update_post_meta( $post_id, '_xanh_seo_desc', sanitize_textarea_field( wp_unslash( $_POST['xanh_seo_desc'] ?? '' ) ) );
	}

	/* -------------------------------- Frontend -------------------------------- */

	public static function title( $title ) {
		if ( is_singular() ) {
			$custom = get_post_meta( get_queried_object_id(), '_xanh_seo_title', true );
			if ( $custom ) {
				return $custom;
			}
		}
		return $title;
	}

	public static function description() {
		if ( is_singular() ) {
			$id   = get_queried_object_id();
			$desc = get_post_meta( $id, '_xanh_seo_desc', true );
			if ( ! $desc ) {
				$post = get_post( $id );
				$desc = $post->post_excerpt ? $post->post_excerpt : wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
			}
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$desc = term_description();
		} else {
			$desc = get_bloginfo( 'description' );
		}
		$desc = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $desc ) ) );
		return mb_strlen( $desc ) > 160 ? rtrim( mb_substr( $desc, 0, 157 ) ) . '…' : $desc;
	}

	private static function current_url() {
		if ( is_singular() ) {
			return (string) wp_get_canonical_url();
		}
		if ( is_front_page() ) {
			return home_url( '/' );
		}
		if ( is_category() || is_tag() || is_tax() ) {
			$link = get_term_link( get_queried_object() );
			return is_wp_error( $link ) ? '' : $link;
		}
		if ( is_post_type_archive() ) {
			return (string) get_post_type_archive_link( get_query_var( 'post_type' ) ?: 'post' );
		}
		if ( is_home() ) {
			return (string) get_permalink( get_option( 'page_for_posts' ) );
		}
		return '';
	}

	public static function head() {
		$desc  = self::description();
		$url   = self::current_url();
		$title = wp_get_document_title();
		$image = '';
		if ( is_singular() && has_post_thumbnail() ) {
			$image = (string) get_the_post_thumbnail_url( get_queried_object_id(), 'large' );
		} elseif ( has_custom_logo() ) {
			$image = (string) wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
		}

		if ( $desc ) {
			echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
		}
		// Canonical cho trang danh mục / trang chủ (WordPress chỉ tự thêm cho bài viết & trang).
		if ( $url && ! is_singular() && ! is_paged() ) {
			echo '<link rel="canonical" href="' . esc_url( $url ) . '" />' . "\n";
		}
		if ( is_search() || is_404() ) {
			echo '<meta name="robots" content="noindex, follow" />' . "\n";
		}

		$og = array(
			'og:locale'      => 'vi_VN',
			'og:type'        => is_singular( array( 'post' ) ) ? 'article' : ( is_singular( 'product' ) ? 'product' : 'website' ),
			'og:title'       => $title,
			'og:description' => $desc,
			'og:url'         => $url,
			'og:site_name'   => get_bloginfo( 'name' ),
			'og:image'       => $image,
		);
		foreach ( $og as $prop => $val ) {
			if ( $val ) {
				echo '<meta property="' . esc_attr( $prop ) . '" content="' . esc_attr( $val ) . '" />' . "\n";
			}
		}
		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";

		if ( is_front_page() ) {
			self::organization_schema();
		}
	}

	private static function organization_schema() {
		$same_as = array_values(
			array_filter(
				array(
					Xanh_Settings::get( 'facebook' ),
					Xanh_Settings::get( 'facebook2' ),
					Xanh_Settings::get( 'youtube' ),
					Xanh_Settings::get( 'instagram' ),
					Xanh_Settings::get( 'tiktok' ),
				)
			)
		);
		$data    = array(
			'@context' => 'https://schema.org',
			'@type'    => 'Organization',
			'name'     => Xanh_Settings::get( 'company_name', get_bloginfo( 'name' ) ),
			'url'      => home_url( '/' ),
		);
		if ( has_custom_logo() ) {
			$data['logo'] = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
		}
		if ( Xanh_Settings::get( 'hotline' ) ) {
			$data['contactPoint'] = array(
				'@type'       => 'ContactPoint',
				'telephone'   => Xanh_Settings::get( 'hotline' ),
				'contactType' => 'customer service',
				'areaServed'  => 'VN',
			);
		}
		if ( Xanh_Settings::get( 'address' ) ) {
			$data['address'] = array(
				'@type'          => 'PostalAddress',
				'streetAddress'  => Xanh_Settings::get( 'address' ),
				'addressCountry' => 'VN',
			);
		}
		if ( $same_as ) {
			$data['sameAs'] = $same_as;
		}
		echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	}

	/* -------------------------------- FAQ -------------------------------- */

	/**
	 * Shortcode [xanh_faq] … [/xanh_faq] với các câu hỏi dạng:
	 *   [xanh_faq]
	 *   Q: Câu hỏi 1?
	 *   A: Trả lời 1.
	 *   Q: Câu hỏi 2?
	 *   A: Trả lời 2.
	 *   [/xanh_faq]
	 * Hiển thị dạng accordion và tự thêm Schema FAQPage.
	 */
	public static function faq_shortcode() {
		add_shortcode( 'xanh_faq', array( __CLASS__, 'render_faq' ) );
	}

	public static function render_faq( $atts, $content = '' ) {
		$content = wp_strip_all_tags( str_replace( array( '<br />', '<br>', '</p>' ), "\n", (string) $content ) );
		preg_match_all( '/Q:\s*(.+?)\s*A:\s*(.+?)(?=\s*Q:|\z)/su', $content, $m, PREG_SET_ORDER );
		if ( ! $m ) {
			return '';
		}
		$items = array();
		$html  = '<div class="xanh-faq">';
		foreach ( $m as $pair ) {
			$q       = trim( $pair[1] );
			$a       = trim( $pair[2] );
			$items[] = array(
				'@type'          => 'Question',
				'name'           => $q,
				'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $a ),
			);
			$html   .= '<details class="xanh-faq__item"><summary>' . esc_html( $q ) . '</summary><div class="xanh-faq__answer">' . wpautop( esc_html( $a ) ) . '</div></details>';
		}
		$html .= '</div>';
		$json  = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $items,
		);
		$html .= '<script type="application/ld+json">' . wp_json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
		return $html;
	}

	/* -------------------------------- robots.txt -------------------------------- */

	public static function robots( $output, $public ) {
		if ( ! $public ) {
			return $output;
		}
		$output .= "Disallow: /?s=\n";
		if ( function_exists( 'wc_get_page_id' ) ) {
			foreach ( array( 'cart', 'checkout', 'myaccount' ) as $page ) {
				$id = wc_get_page_id( $page );
				if ( $id > 0 ) {
					$path = wp_parse_url( get_permalink( $id ), PHP_URL_PATH );
					if ( $path && '/' !== $path ) {
						$output .= 'Disallow: ' . $path . "\n";
					}
				}
			}
		}
		if ( ! str_contains( $output, 'Sitemap:' ) ) {
			$output .= "\nSitemap: " . esc_url_raw( home_url( '/wp-sitemap.xml' ) ) . "\n";
		}
		return $output;
	}
}
