<?php
/**
 * Template Name: Liên hệ
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	xanh_page_hero( get_the_title(), has_excerpt() ? esc_html( get_the_excerpt() ) : esc_html__( 'Chúng tôi luôn sẵn sàng hỗ trợ bạn.', 'xanh' ) );

	$items = array(
		array( 'pin', __( 'Địa chỉ', 'xanh' ), xanh_opt( 'address' ), '' ),
		array( 'phone', __( 'Hotline', 'xanh' ), xanh_opt( 'hotline' ), 'tel:' . preg_replace( '/[^0-9+]/', '', (string) xanh_opt( 'hotline' ) ) ),
		array( 'mail', __( 'Email', 'xanh' ), xanh_opt( 'email' ), 'mailto:' . xanh_opt( 'email' ) ),
		array( 'clock', __( 'Giờ làm việc', 'xanh' ), xanh_opt( 'working_hours' ), '' ),
	);
	?>
	<div class="section">
		<div class="container contact-grid">
			<div class="contact-info">
				<?php
				foreach ( $items as $it ) :
					if ( ! $it[2] ) {
						continue;
					}
					?>
					<div class="contact-card">
						<span class="contact-card__icon"><?php echo xanh_icon( $it[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<div>
							<p class="contact-card__label"><?php echo esc_html( $it[1] ); ?></p>
							<?php if ( $it[3] ) : ?>
								<a class="contact-card__value" href="<?php echo esc_url( $it[3], array( 'tel', 'mailto' ) ); ?>"><?php echo esc_html( $it[2] ); ?></a>
							<?php else : ?>
								<p class="contact-card__value"><?php echo esc_html( $it[2] ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
				<?php if ( get_the_content() ) : ?>
					<div class="entry-content"><?php the_content(); ?></div>
				<?php endif; ?>
			</div>

			<div class="card contact-form" id="lien-he-form">
				<h2 class="card__title"><?php esc_html_e( 'Gửi tin nhắn cho chúng tôi', 'xanh' ); ?></h2>
				<?php
				if ( function_exists( 'xanh_lead_form' ) ) {
					echo xanh_lead_notice(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo xanh_lead_form( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'contact',
						array(
							'anchor'       => 'lien-he-form',
							'show_email'   => true,
							'show_message' => true,
							'button'       => __( 'Gửi liên hệ', 'xanh' ),
						)
					);
				} else {
					esc_html_e( 'Vui lòng kích hoạt plugin Sun Shines Core để dùng form liên hệ.', 'xanh' );
				}
				?>
			</div>
		</div>

		<?php
		$map = xanh_opt( 'map_embed' );
		if ( $map && str_starts_with( $map, 'https://www.google.com/maps/embed' ) ) :
			?>
			<div class="container">
				<div class="map-embed">
					<iframe src="<?php echo esc_url( $map ); ?>" title="<?php esc_attr_e( 'Bản đồ', 'xanh' ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php
endwhile;

get_footer();
