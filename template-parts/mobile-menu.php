<?php
/**
 * Mobile Menu Drawer
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="mobile-menu-overlay" data-mobile-menu-overlay hidden></div>
<aside id="mobile-menu" class="mobile-menu" aria-hidden="true">
	<div class="mobile-menu__header">
		<a class="mobile-menu__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
		</a>
		<button class="mobile-menu__close" aria-label="<?php esc_attr_e( 'Close menu', 'pianolog-genesis-child' ); ?>">
			<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
				<line x1="18" y1="6" x2="6" y2="18"></line>
				<line x1="6" y1="6" x2="18" y2="18"></line>
			</svg>
		</button>
	</div>
	<nav class="mobile-menu__nav">
		<?php
		$location = has_nav_menu( 'mobile' ) ? 'mobile' : 'primary';
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => 'mobile-menu__list',
				'depth'          => 2,
				'fallback_cb'    => false,
				'walker'         => class_exists( 'Pianolog_Mobile_Walker' ) ? new Pianolog_Mobile_Walker() : null,
			)
		);
		?>
	</nav>
	<div class="mobile-menu__search">
		<?php get_search_form(); ?>
	</div>
</aside>


