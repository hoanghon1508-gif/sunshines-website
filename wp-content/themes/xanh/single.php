<?php
/**
 * Chi tiết tin tức.
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'article' ); ?>>
		<header class="page-hero page-hero--article">
			<div class="container container--narrow">
				<?php xanh_breadcrumbs(); ?>
				<h1 class="page-hero__title"><?php the_title(); ?></h1>
				<p class="article__meta">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></time>
					<?php
					$cats = get_the_category();
					if ( $cats ) {
						echo ' · <a href="' . esc_url( get_category_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
					}
					?>
				</p>
			</div>
		</header>

		<div class="section section--tight">
			<div class="container container--narrow">
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="article__cover"><?php the_post_thumbnail( 'large', array( 'class' => 'is-lcp' ) ); ?></figure>
				<?php endif; ?>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>
				<?php
				$tags = get_the_tags();
				if ( $tags ) :
					?>
					<div class="chips chips--tags">
						<?php foreach ( $tags as $t ) : ?>
							<a class="chip" href="<?php echo esc_url( get_tag_link( $t ) ); ?>">#<?php echo esc_html( $t->name ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="share">
					<span><?php esc_html_e( 'Chia sẻ:', 'xanh' ); ?></span>
					<a href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( get_permalink() ) ); ?>" target="_blank" rel="noopener"><?php echo xanh_icon( 'facebook', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> Facebook</a>
				</div>
			</div>
		</div>
	</article>

	<?php
	$related = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'category__in'        => wp_get_post_categories( get_the_ID() ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( $related->have_posts() ) :
		?>
		<section class="section section--white">
			<div class="container">
				<h2 class="section__title"><?php esc_html_e( 'Bài viết liên quan', 'xanh' ); ?></h2>
				<div class="post-grid">
					<?php
					while ( $related->have_posts() ) {
						$related->the_post();
						get_template_part( 'template-parts/card', 'post', array( 'heading' => 'h3' ) );
					}
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
