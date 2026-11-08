<?php
/**
 * Trang chủ.
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

get_header();

$slides = function_exists( 'xanh_banners' ) ? xanh_banners( 'home_slider', 6 ) : array();
$shop   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
?>

<section class="hero" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Banner nổi bật', 'xanh' ); ?>">
	<div class="container">
		<?php if ( $slides ) : ?>
			<div class="hero__slider" data-slider>
				<?php foreach ( $slides as $i => $s ) : ?>
					<article class="hero__slide <?php echo 0 === $i ? 'is-active' : ''; ?>" aria-roledescription="slide" <?php echo 0 === $i ? '' : 'hidden'; ?>>
						<div class="hero__content">
							<?php if ( 0 === $i ) : ?>
								<h1 class="hero__title"><?php echo esc_html( $s['title'] ); ?></h1>
							<?php else : ?>
								<h2 class="hero__title"><?php echo esc_html( $s['title'] ); ?></h2>
							<?php endif; ?>
							<?php if ( $s['subtitle'] ) : ?>
								<p class="hero__text"><?php echo esc_html( $s['subtitle'] ); ?></p>
							<?php endif; ?>
							<?php if ( $s['link'] ) : ?>
								<div class="hero__actions">
									<a class="btn btn--primary btn--lg" href="<?php echo esc_url( $s['link'] ); ?>" <?php echo $s['new_tab'] ? 'target="_blank" rel="noopener"' : ''; ?>>
										<?php echo esc_html( $s['button'] ? $s['button'] : __( 'Xem ngay', 'xanh' ) ); ?>
									</a>
								</div>
							<?php endif; ?>
						</div>
						<div class="hero__media">
							<?php
							if ( $s['image_id'] ) {
								echo wp_get_attachment_image(
									$s['image_id'],
									'xanh-hero',
									false,
									array(
										'class' => 0 === $i ? 'is-lcp' : '',
										'alt'   => $s['title'],
										'sizes' => '(max-width: 768px) 100vw, 560px',
									)
								);
							} else {
								echo '<span class="img-placeholder">' . xanh_icon( 'image', 48 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div>
					</article>
				<?php endforeach; ?>
				<?php if ( count( $slides ) > 1 ) : ?>
					<div class="hero__dots" role="tablist">
						<?php foreach ( $slides as $i => $s ) : ?>
							<button type="button" class="hero__dot <?php echo 0 === $i ? 'is-active' : ''; ?>" data-slide="<?php echo (int) $i; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide number */ __( 'Banner %d', 'xanh' ), $i + 1 ) ); ?>"></button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<div class="hero__slide is-active">
				<div class="hero__content">
					<h1 class="hero__title"><?php echo esc_html( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : get_bloginfo( 'name' ) ); ?></h1>
					<p class="hero__text"><?php esc_html_e( 'Thêm banner trong trang quản trị: Banner → Thêm banner, chọn vị trí "Trang chủ – Slider đầu trang".', 'xanh' ); ?></p>
					<div class="hero__actions"><a class="btn btn--primary btn--lg" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Mua sắm ngay', 'xanh' ); ?></a></div>
				</div>
				<div class="hero__media"><span class="img-placeholder"><?php echo xanh_icon( 'image', 48 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></div>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="usp" aria-label="<?php esc_attr_e( 'Cam kết', 'xanh' ); ?>">
	<div class="container usp__grid">
		<?php
		$usp = array(
			array( 'truck', __( 'Giao hàng toàn quốc', 'xanh' ), __( 'Nhanh chóng 2–4 ngày', 'xanh' ) ),
			array( 'refresh', __( 'Đổi trả 7 ngày', 'xanh' ), __( 'Miễn phí nếu lỗi sản phẩm', 'xanh' ) ),
			array( 'shield', __( 'Hàng chính hãng', 'xanh' ), __( 'Cam kết chất lượng', 'xanh' ) ),
			array( 'headset', __( 'Tư vấn miễn phí', 'xanh' ), xanh_opt( 'hotline' ) ? __( 'Hotline', 'xanh' ) . ' ' . xanh_opt( 'hotline' ) : __( 'Hỗ trợ tận tình', 'xanh' ) ),
		);
		foreach ( $usp as $u ) :
			?>
			<div class="usp__item">
				<span class="usp__icon"><?php echo xanh_icon( $u[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<div>
					<p class="usp__title"><?php echo esc_html( $u[1] ); ?></p>
					<p class="usp__text"><?php echo esc_html( $u[2] ); ?></p>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<?php if ( function_exists( 'xanh_product_categories' ) && count( xanh_product_categories() ) >= 2 ) : // Chỉ hiện khi có từ 2 danh mục trở lên. ?>
	<section class="section">
		<div class="container">
			<div class="section__head">
				<div>
					<h2 class="section__title"><?php esc_html_e( 'Danh mục sản phẩm', 'xanh' ); ?></h2>
					<p class="section__sub"><?php esc_html_e( 'Chọn nhanh theo nhu cầu của bạn', 'xanh' ); ?></p>
				</div>
			</div>
			<div class="cat-grid">
				<?php foreach ( xanh_product_categories( 4 ) as $term ) : ?>
					<a class="cat-card" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
						<span class="cat-card__media">
							<?php
							$thumb = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
							if ( $thumb ) {
								echo wp_get_attachment_image( $thumb, 'woocommerce_thumbnail', false, array( 'alt' => $term->name ) );
							} else {
								echo '<span class="img-placeholder">' . xanh_icon( 'image', 40 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</span>
						<span class="cat-card__name"><?php echo esc_html( $term->name ); ?></span>
						<span class="cat-card__count">
							<?php
							/* translators: %d: number of products */
							echo esc_html( sprintf( _n( '%d sản phẩm', '%d sản phẩm', $term->count, 'xanh' ), $term->count ) );
							?>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( function_exists( 'wc_get_products' ) ) : ?>
	<?php
	$featured = wc_get_products(
		array(
			'status'   => 'publish',
			'limit'    => 8,
			'featured' => true,
		)
	);
	if ( count( $featured ) < 4 ) {
		$featured = wc_get_products(
			array(
				'status'  => 'publish',
				'limit'   => 8,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);
	}
	if ( $featured ) :
		?>
		<section class="section section--white">
			<div class="container">
				<div class="section__head">
					<div>
						<h2 class="section__title"><?php esc_html_e( 'Sản phẩm nổi bật', 'xanh' ); ?></h2>
						<p class="section__sub"><?php esc_html_e( 'Những sản phẩm được khách hàng yêu thích nhất', 'xanh' ); ?></p>
					</div>
					<a class="section__link" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Xem tất cả', 'xanh' ); ?> <?php echo xanh_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</div>
				<ul class="products columns-4">
					<?php
					foreach ( $featured as $product_obj ) {
						$GLOBALS['post'] = get_post( $product_obj->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						setup_postdata( $GLOBALS['post'] );
						wc_get_template_part( 'content', 'product' );
					}
					wp_reset_postdata();
					?>
				</ul>
			</div>
		</section>
	<?php endif; ?>
<?php endif; ?>

<?php
$middle = function_exists( 'xanh_banners' ) ? xanh_banners( 'home_middle', 2 ) : array();
foreach ( $middle as $b ) :
	?>
	<section class="section section--tight">
		<div class="container">
			<a class="wide-banner" href="<?php echo esc_url( $b['link'] ? $b['link'] : '#' ); ?>" <?php echo $b['new_tab'] ? 'target="_blank" rel="noopener"' : ''; ?>>
				<?php echo $b['image_id'] ? wp_get_attachment_image( $b['image_id'], 'xanh-wide', false, array( 'alt' => $b['title'] ) ) : ''; ?>
			</a>
		</div>
	</section>
<?php endforeach; ?>

<?php if ( function_exists( 'xanh_lead_form' ) ) : ?>
	<section class="section section--tight" id="dang-ky">
		<div class="container">
			<div class="promo">
				<div class="promo__text">
					<h2 class="promo__title"><?php esc_html_e( 'Đăng ký nhận tư vấn miễn phí', 'xanh' ); ?></h2>
					<p><?php esc_html_e( 'Để lại số điện thoại, đội ngũ của chúng tôi sẽ liên hệ trong 24 giờ.', 'xanh' ); ?></p>
					<?php echo xanh_lead_notice(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<?php
				echo xanh_lead_form( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'promo',
					array(
						'anchor'    => 'dang-ky',
						'show_name' => false,
						'button'    => __( 'Gửi đăng ký', 'xanh' ),
						'layout'    => 'inline',
						'class'     => 'xanh-form--promo',
					)
				);
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
$news = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
if ( $news->have_posts() ) :
	$blog = (int) get_option( 'page_for_posts' );
	?>
	<section class="section">
		<div class="container">
			<div class="section__head">
				<div>
					<h2 class="section__title"><?php esc_html_e( 'Tin tức & Kiến thức', 'xanh' ); ?></h2>
					<p class="section__sub"><?php esc_html_e( 'Cập nhật thông tin hữu ích mỗi tuần', 'xanh' ); ?></p>
				</div>
				<?php if ( $blog ) : ?>
					<a class="section__link" href="<?php echo esc_url( get_permalink( $blog ) ); ?>"><?php esc_html_e( 'Xem tất cả', 'xanh' ); ?> <?php echo xanh_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<?php endif; ?>
			</div>
			<div class="post-grid">
				<?php
				while ( $news->have_posts() ) {
					$news->the_post();
					get_template_part( 'template-parts/card', 'post', array( 'heading' => 'h3' ) );
				}
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
