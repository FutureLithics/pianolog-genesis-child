<?php
/**
 * Template: Default Page
 *
 * Simple page template: white background, centered title, content in container.
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Replace default Genesis loop.
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action(
	'genesis_loop',
	function () {
		if ( ! have_posts() ) {
			echo '<div class="container py-4"><p>' . esc_html__( 'Page not found.', 'pianolog-genesis-child' ) . '</p></div>';
			return;
		}
		while ( have_posts() ) :
			the_post();
			?>
			<section class="page-body py-5">
				<div class="container">
					<h1 class="page-title text-center"><?php the_title(); ?></h1>
					<article <?php post_class( 'single-article' ); ?>>
						<div class="single-content">
							<?php the_content(); ?>
						</div>
					</article>
				</div>
			</section>
			<?php
		endwhile;
	}
);

// Use full-width layout for pages by default.
add_filter(
	'genesis_site_layout',
	function ( $layout ) {
		if ( is_page() ) {
			return 'full-width-content';
		}
		return $layout;
	}
);

genesis();


