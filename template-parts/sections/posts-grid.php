<?php
/**
 * Posts Grid Section
 *
 * Expects $args = [
 *   'title' => string,
 *   'cta_text' => string,
 *   'cta_link' => string URL,
 *   'background' => 'manuscript' | 'white' | 'none',
 *   'query' => WP_Query instance,
 * ]
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title      = isset( $args['title'] ) ? $args['title'] : '';
$cta_text   = isset( $args['cta_text'] ) ? $args['cta_text'] : '';
$cta_link   = isset( $args['cta_link'] ) ? $args['cta_link'] : '';
$background = isset( $args['background'] ) ? $args['background'] : 'manuscript';
/** @var WP_Query $query */
$query      = isset( $args['query'] ) && $args['query'] instanceof WP_Query ? $args['query'] : null;
$show_title = isset( $args['show_title'] ) ? (bool) $args['show_title'] : true;

$bg_style = '';
if ( 'manuscript' === $background ) {
	$bg_style = 'background: var(--color-manuscript);';
} elseif ( 'white' === $background ) {
	$bg_style = 'background: #ffffff;';
}
$section_id = isset( $args['section_id'] ) ? (string) $args['section_id'] : '';
$id_attr = $section_id ? ' id="' . esc_attr( $section_id ) . '"' : '';
?>
<section<?php echo $id_attr; ?> class="posts-gear py-8" style="<?php echo esc_attr( $bg_style ); ?>">
	<div class="container flex flex-col items-center gap-4">
		<?php if ( $title && $show_title ) : ?>
		<div class="row justify-center items-center pb-3">
			<h2 class="posts-gear__title" style="font-family: var(--font-serif); font-weight: 700; margin: 0;">
				<?php echo esc_html( $title ); ?>
			</h2>
		</div>
		<?php endif; ?>

		<div class="gear-grid">
			<?php
			if ( $query && $query->have_posts() ) :
				while ( $query->have_posts() ) :
					$query->the_post();
					$thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
					?>
					<article <?php post_class( 'gear-card' ); ?>>
						<a class="gear-card__link" href="<?php the_permalink(); ?>">
							<div class="gear-card__image">
								<?php if ( $thumb_url ) : ?>
									<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
								<?php else : ?>
									<div class="gear-card__image--placeholder"></div>
								<?php endif; ?>
							</div>
							<div class="gear-card__content">
								<h3 class="gear-card__title"><?php the_title(); ?></h3>
								<p class="gear-card__excerpt">
									<?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 300 ) ); ?>
								</p>
								<div class="gear-card__meta">
									<span class="gear-card__author">
										<?php echo esc_html( get_the_author() ); ?>
									</span>
								</div>
							</div>
						</a>
					</article>
					<?php
				endwhile;
			else :
				?>
				<p><?php echo esc_html__( 'No posts found.', 'pianolog-genesis-child' ); ?></p>
				<?php
			endif;
			?>
		</div>
		<?php if ( ! empty( $args['load_more'] ) ) : ?>
			<div class="row justify-center items-center py-4 mt-4">
				<button class="posts-gear__cta posts-gear__loadmore px-3 py-2"
					data-category="<?php echo esc_attr( $args['category_slug'] ); ?>"
					data-per-page="<?php echo esc_attr( (string) ( $args['per_page'] ?? 9 ) ); ?>"
					data-next-page="2"
					type="button">
					<?php echo esc_html( $cta_text ? $cta_text : __( 'Load More', 'pianolog-genesis-child' ) ); ?>
				</button>
			</div>
		<?php elseif ( $cta_text && $cta_link ) : ?>
			<div class="row justify-center items-center py-4 mt-4">
				<a class="posts-gear__cta px-3 py-2" href="<?php echo esc_url( $cta_link ); ?>">
					<?php echo esc_html( $cta_text ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>


