<?php
/**
 * Template dự phòng: danh sách tin tức, danh mục, thẻ, tìm kiếm.
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_home() ) {
	$blog  = (int) get_option( 'page_for_posts' );
	$title = $blog ? get_the_title( $blog ) : __( 'Tin tức', 'xanh' );
	$sub   = '';
} elseif ( is_search() ) {
	/* translators: %s: search query */
	$title = sprintf( __( 'Kết quả tìm kiếm: “%s”', 'xanh' ), get_search_query( false ) );
	$sub   = '';
} else {
	$title = wp_strip_all_tags( get_the_archive_title() );
	$sub   = wp_strip_all_tags( get_the_archive_description() );
}

xanh_page_hero( esc_html( $title ), esc_html( $sub ) );
?>

<div class="section">
	<div class="container">
		<?php if ( is_home() || is_category() ) : ?>
			<?php
			$cats = get_categories( array( 'hide_empty' => true ) );
			if ( count( $cats ) > 1 ) :
				$blog_url = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );
				?>
				<nav class="chips" aria-label="<?php esc_attr_e( 'Chuyên mục', 'xanh' ); ?>">
					<a class="chip <?php echo is_home() ? 'is-active' : ''; ?>" href="<?php echo esc_url( $blog_url ); ?>"><?php esc_html_e( 'Tất cả', 'xanh' ); ?></a>
					<?php foreach ( $cats as $cat ) : ?>
						<a class="chip <?php echo is_category( $cat->term_id ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/card', 'post' );
				}
				?>
			</div>
			<?php xanh_pagination(); ?>
		<?php else : ?>
			<div class="empty-state">
				<p><?php esc_html_e( 'Chưa có nội dung phù hợp.', 'xanh' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
