<?php
/**
 * Thẻ tin tức.
 *
 * @package Xanh
 */

defined( 'ABSPATH' ) || exit;

$heading = isset( $args['heading'] ) && in_array( $args['heading'], array( 'h2', 'h3' ), true ) ? $args['heading'] : 'h2';
?>
<article <?php post_class( 'post-card' ); ?>>
	<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php xanh_thumb( null, 'xanh-card', array( 'sizes' => '(max-width: 768px) 100vw, 400px' ) ); ?>
	</a>
	<div class="post-card__body">
		<time class="post-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></time>
		<<?php echo esc_html( $heading ); ?> class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></<?php echo esc_html( $heading ); ?>>
		<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
	</div>
</article>
