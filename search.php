<?php
/**
 * Search Results Template
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Replace default loop with custom grid.
remove_action( 'genesis_loop', 'genesis_do_loop' );

add_action( 'genesis_loop', function () {
	$term  = get_search_query();
	$title = sprintf(
		/* translators: %s: search term */
		esc_html__( 'Search results for “%s”', 'pianolog-genesis-child' ),
		$term
	);

	?>
	<section class="archive-header pt-5" style="background:#ffffff;">
		<div class="container text-center">
			<h1 style="font-family: var(--font-serif); font-size: 48px; font-weight: 700; margin: 0;">
				<?php echo esc_html( $title ); ?>
			</h1>
		</div>
	</section>
	<?php

	$paged = max( 1, (int) get_query_var( 'paged' ) );
	$query = new WP_Query(
		array(
			's'              => $term,
			'posts_per_page' => 12,
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'paged'          => $paged,
		)
	);

	get_template_part(
		'template-parts/sections/posts-grid',
		null,
		array(
			'title'        => '',
			'cta_text'     => '',
			'cta_link'     => '',
			'background'   => 'white',
			'show_title'   => false,
			'query'        => $query,
			'section_id'   => 'posts-grid-search',
		)
	);
	wp_reset_postdata();
} );

genesis();


