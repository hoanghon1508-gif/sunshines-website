<?php
/**
 * 404.
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

get_header();
xanh_page_hero( esc_html__( 'Không tìm thấy trang', 'xanh' ), esc_html__( 'Trang bạn tìm có thể đã bị xoá hoặc đổi địa chỉ.', 'xanh' ) );
?>
<div class="section">
	<div class="container container--narrow empty-state">
		<?php get_search_form(); ?>
		<p><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Về trang chủ', 'xanh' ); ?></a></p>
	</div>
</div>
<?php
get_footer();
