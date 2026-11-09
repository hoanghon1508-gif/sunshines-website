<?php
/**
 * Header.
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="theme-color" content="#15803D" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Chuyển đến nội dung', 'xanh' ); ?></a>

<header class="site-header">
	<div class="topbar">
		<div class="container topbar__inner">
			<p class="topbar__text">
				<?php
				$hotline = xanh_opt( 'hotline' );
				if ( $hotline ) {
					printf( '%1$s <a href="tel:%2$s">%3$s</a>', esc_html__( 'Hotline:', 'xanh' ), esc_attr( preg_replace( '/[^0-9+]/', '', $hotline ) ), esc_html( $hotline ) );
				}
				$topbar = xanh_opt( 'topbar_text' );
				if ( $topbar ) {
					echo '<span class="topbar__note"><span class="topbar__dot" aria-hidden="true">·</span>' . esc_html( $topbar ) . '</span>';
				}
				?>
			</p>
			<?php if ( function_exists( 'xanh_social_links' ) && xanh_social_links() ) : ?>
				<ul class="topbar__social">
					<?php foreach ( xanh_social_links() as $key => $s ) : ?>
						<li><a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $s['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>

	<div class="navbar">
		<div class="container navbar__inner">
			<button class="navbar__toggle icon-btn" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="<?php esc_attr_e( 'Mở menu', 'xanh' ); ?>">
				<?php echo xanh_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>

			<div class="navbar__brand"><?php xanh_logo(); ?></div>

			<nav class="site-nav" id="site-nav" aria-label="<?php esc_attr_e( 'Menu chính', 'xanh' ); ?>">
				<div class="site-nav__head">
					<?php xanh_logo(); ?>
					<button class="site-nav__close icon-btn" type="button" aria-label="<?php esc_attr_e( 'Đóng menu', 'xanh' ); ?>"><?php echo xanh_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				</div>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'menu',
						'depth'          => 2,
						'fallback_cb'    => 'xanh_menu_fallback',
					)
				);
				?>
			</nav>

			<div class="navbar__actions">
				<button class="icon-btn" type="button" data-search-toggle aria-expanded="false" aria-controls="header-search" aria-label="<?php esc_attr_e( 'Tìm kiếm', 'xanh' ); ?>">
					<?php echo xanh_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
				<?php if ( function_exists( 'WC' ) ) : ?>
					<a class="icon-btn cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Giỏ hàng', 'xanh' ); ?>">
						<?php echo xanh_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="cart-count"><?php echo WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0; ?></span>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<div class="header-search" id="header-search" hidden>
			<div class="container"><?php get_search_form(); ?></div>
		</div>
	</div>
	<div class="nav-overlay" hidden></div>
</header>

<main id="main" class="site-main">
