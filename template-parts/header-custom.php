<?php
/**
 * Custom Site Header template.
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Open site header markup with Genesis attributes.
genesis_markup(
	array(
		'open'    => '<header %s>',
		'context' => 'site-header',
	)
);

genesis_structural_wrap( 'header' );
?>
	<div class="container row justify-between header-inner-wrap">
		<div class="site-branding col-3 flex items-center">
			<h1 class="site-title">
				<a class="text-white" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
					</a>
			</h1>
		</div>
		<div class="col-9 flex justify-end items-center">
			<?php
			// Primary navigation inside header with optional megamenu walker.
			if ( function_exists( 'wp_nav_menu' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'menu menu--primary flex items-center',
						'depth'          => 3,
						'fallback_cb'    => false,
						'walker'         => class_exists( 'Pianolog_Megamenu_Walker' ) ? new Pianolog_Megamenu_Walker() : null,
					)
				);
			}
			?>
			<button class="header-menu-toggle pl-4" aria-controls="mobile-menu" aria-expanded="false">
				<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
					<line x1="3" y1="6" x2="21" y2="6"></line>
					<line x1="3" y1="12" x2="21" y2="12"></line>
					<line x1="3" y1="18" x2="21" y2="18"></line>
				</svg>
			</button>
			<div class="header-search flex items-center pl-4">
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>

<?php
genesis_structural_wrap( 'header', 'close' );

// Mobile menu drawer and overlay.
get_template_part( 'template-parts/mobile', 'menu' );

// Close site header markup.
genesis_markup(
	array(
		'close'   => '</header>',
		'context' => 'site-header',
	)
);


