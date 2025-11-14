<?php
/**
 * Template Part: Related Posts Grid
 *
 * Expected $args:
 * - title (string) Section title
 * - query (WP_Query) A prepared query of related posts
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = isset( $args['title'] ) ? (string) $args['title'] : __( 'Related Posts', 'pianolog-genesis-child' );
$query = isset( $args['query'] ) && $args['query'] instanceof WP_Query ? $args['query'] : null;

if ( ! $query ) {
	return;
}
?>
<section class="single-related py-4">
	<div class="container">
		<h2 style="font-family: var(--font-serif); font-weight: 700; margin: 0 0 0.75rem;"><?php echo esc_html( $title ); ?></h2>
		<div class="related-post-grid">
			<?php
			if ( $query->have_posts() ) :
				while ( $query->have_posts() ) :
					$query->the_post();
					$thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
					?>
					<article <?php post_class( 'related-card' ); ?>>
						<a class="related-card__link" href="<?php the_permalink(); ?>">
							<div class="related-card__image">
								<?php if ( $thumb_url ) : ?>
									<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
								<?php else : ?>
									<div class="related-card__placeholder"></div>
								<?php endif; ?>
							</div>
							<div class="related-card__content">
								<h3 class="related-card__title"><?php the_title(); ?></h3>
							</div>
						</a>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
			endif;
			?>
		</div>
	</div>
	</section>


