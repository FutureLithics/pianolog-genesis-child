<?php
/**
 * Single Product Template
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action(
	'genesis_loop',
	function () {
		if ( ! have_posts() ) {
			echo '<div class="container py-4"><p>' . esc_html__( 'Product not found.', 'pianolog-genesis-child' ) . '</p></div>';
			return;
		}

		the_post();

		$hero_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
		?>
		<section class="single-hero">
			<?php if ( $hero_url ) : ?>
				<div class="single-hero__bg" style="<?php echo 'background-image:url(' . esc_url( $hero_url ) . ');'; ?>"></div>
			<?php endif; ?>
			<div class="container text-center flex flex-col items-center justify-center h-100">
				<h1 class="single-hero__title text-white mb-0">
					<?php the_title(); ?>
				</h1>
			</div>
		</section>

		<section class="single-body py-5">
			<div class="container">
				<div class="row">
					<div class="col-9">
						<article <?php post_class( 'single-article' ); ?>>
							<div class="single-content">
								<?php the_content(); ?>
								<?php
								$tags = get_the_tags();
								if ( $tags ) :
									?>
									<div class="single-tags mt-3">
										<ul class="single-tags__list">
											<?php foreach ( $tags as $tag ) : ?>
												<li class="single-tags__item">
													<a class="tag-pill" href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>"><?php echo esc_html( $tag->name ); ?></a>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
									<?php
								endif;
								?>
							</div>
						</article>
						<?php if ( comments_open() || get_comments_number() ) : ?>
							<div class="single-comments mt-3">
								<?php comments_template(); ?>
							</div>
						<?php endif; ?>
					</div>
					<aside class="col-3 single-sidebar">
						<?php
						if ( is_active_sidebar( 'sidebar' ) ) {
							dynamic_sidebar( 'sidebar' );
						}
						?>
					</aside>
				</div>
			</div>
		</section>
		<?php
	}
);

add_filter(
	'genesis_site_layout',
	function ( $layout ) {
		if ( is_singular( 'product' ) ) {
			return 'full-width-content';
		}

		return $layout;
	}
);

genesis();

