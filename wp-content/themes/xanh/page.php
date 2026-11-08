<?php
/**
 * Trang đơn giản (giới thiệu, chính sách…). Cũng dùng cho trang Giỏ hàng/Thanh toán/Tài khoản.
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

get_header();

$is_wc_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );

while ( have_posts() ) :
	the_post();
	xanh_page_hero( get_the_title() );
	?>
	<div class="section section--tight">
		<div class="container <?php echo $is_wc_page ? '' : 'container--narrow'; ?>">
			<?php if ( ! $is_wc_page && has_post_thumbnail() ) : ?>
				<figure class="article__cover"><?php the_post_thumbnail( 'large', array( 'class' => 'is-lcp' ) ); ?></figure>
			<?php endif; ?>
			<div class="entry-content <?php echo $is_wc_page ? 'entry-content--wc' : ''; ?>">
				<?php
				the_content();
				wp_link_pages();
				?>
			</div>
		</div>
	</div>
	<?php
endwhile;

get_footer();
