<?php
/**
 * Theme Search Form
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php echo esc_html_x( 'Search for:', 'label', 'pianolog-genesis-child' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search…', 'placeholder', 'pianolog-genesis-child' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
	</label>
	<button type="submit" class="search-submit" aria-label="<?php echo esc_attr_x( 'Search', 'submit button', 'pianolog-genesis-child' ); ?>">
		<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<circle cx="11" cy="11" r="7"></circle>
			<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
		</svg>
		<span class="screen-reader-text"><?php echo esc_html_x( 'Search', 'submit button', 'pianolog-genesis-child' ); ?></span>
	</button>
</form>


