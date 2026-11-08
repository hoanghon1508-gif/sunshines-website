<?php
/**
 * Footer.
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer class="site-footer">
	<div class="container site-footer__grid">
		<div class="site-footer__col site-footer__brand">
			<p class="site-footer__name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
			<?php if ( xanh_opt( 'slogan' ) ) : ?>
				<p class="site-footer__slogan"><?php echo esc_html( xanh_opt( 'slogan' ) ); ?></p>
			<?php endif; ?>
			<ul class="contact-list">
				<?php if ( xanh_opt( 'company_name' ) ) : ?>
					<li><?php echo esc_html( xanh_opt( 'company_name' ) ); ?></li>
				<?php endif; ?>
				<?php if ( xanh_opt( 'address' ) ) : ?>
					<li><?php echo xanh_icon( 'pin', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( xanh_opt( 'address' ) ); ?></span></li>
				<?php endif; ?>
				<?php if ( xanh_opt( 'hotline' ) ) : ?>
					<li><?php echo xanh_icon( 'phone', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', xanh_opt( 'hotline' ) ) ); ?>"><?php echo esc_html( xanh_opt( 'hotline' ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( xanh_opt( 'email' ) ) : ?>
					<li><?php echo xanh_icon( 'mail', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="mailto:<?php echo esc_attr( antispambot( xanh_opt( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( xanh_opt( 'email' ) ) ); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>

		<?php
		$menus = array(
			'footer_about'  => __( 'Về chúng tôi', 'xanh' ),
			'footer_policy' => __( 'Chính sách', 'xanh' ),
		);
		foreach ( $menus as $location => $heading ) :
			if ( ! has_nav_menu( $location ) ) {
				continue;
			}
			?>
			<div class="site-footer__col">
				<p class="site-footer__heading"><?php echo esc_html( wp_get_nav_menu_name( $location ) ? wp_get_nav_menu_name( $location ) : $heading ); ?></p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => $location,
						'container'      => false,
						'menu_class'     => 'footer-menu',
						'depth'          => 1,
					)
				);
				?>
			</div>
		<?php endforeach; ?>

		<?php if ( function_exists( 'xanh_social_links' ) && xanh_social_links() ) : ?>
			<div class="site-footer__col">
				<p class="site-footer__heading"><?php esc_html_e( 'Kết nối', 'xanh' ); ?></p>
				<ul class="footer-menu footer-social">
					<?php foreach ( xanh_social_links() as $key => $s ) : ?>
						<li><a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener"><?php echo xanh_icon( $s['icon'] ?? $key, 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $s['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
	<div class="container site-footer__bottom">
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?><?php echo xanh_opt( 'company_name' ) ? ' – ' . esc_html( xanh_opt( 'company_name' ) ) : ''; ?>. <?php esc_html_e( 'Bảo lưu mọi quyền.', 'xanh' ); ?></p>
	</div>
</footer>

<?php get_template_part( 'template-parts/quick-contact' ); ?>

<?php wp_footer(); ?>
</body>
</html>
