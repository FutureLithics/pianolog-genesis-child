<?php
/**
 * Template Part: Site Footer
 *
 * Outputs footer markup with Genesis helpers and the filtered creds text.
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

genesis_markup( array( 'open' => '<footer %s>', 'context' => 'site-footer' ) );
genesis_structural_wrap( 'footer' );
echo '<p class="footer-creds">' . apply_filters( 'genesis_footer_creds_text', '' ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
genesis_structural_wrap( 'footer', 'close' );
// Allow themes/plugins to print custom footer scripts.
$pianolog_inline_footer_scripts = apply_filters( 'pianolog_footer_inline_scripts', '' );
if ( is_string( $pianolog_inline_footer_scripts ) && $pianolog_inline_footer_scripts !== '' ) {
	echo $pianolog_inline_footer_scripts; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
do_action( 'pianolog_footer_scripts' );
genesis_markup( array( 'close' => '</footer>', 'context' => 'site-footer' ) );


