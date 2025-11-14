<?php
/**
 * Pianolog Mobile Walker
 *
 * Injects immediate child categories under category menu items for mobile menu.
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Pianolog_Mobile_Walker' ) && class_exists( 'Walker_Nav_Menu' ) ) {
	class Pianolog_Mobile_Walker extends Walker_Nav_Menu {
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes = array_map( 'sanitize_html_class', $classes );

			$is_category = ( 'category' === $item->object );
			$has_children = in_array( 'menu-item-has-children', $classes, true );

			// If category, we will create a synthetic child list with its immediate children.
			$child_terms = array();
			if ( $is_category ) {
				$child_terms = get_terms(
					array(
						'taxonomy'   => 'category',
						'parent'     => (int) $item->object_id,
						'hide_empty' => false,
					)
				);
				if ( ! is_wp_error( $child_terms ) && ! empty( $child_terms ) ) {
					$has_children = true;
					$classes[]    = 'menu-item-has-children';
				}
			}

			$output .= '<li class="' . esc_attr( implode( ' ', array_filter( $classes ) ) ) . '">';
			$output .= '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';

			// Add expand button if we know it has children (native submenu or injected category terms).
			if ( $has_children ) {
				$output .= '<button class="mobile-menu__expand" aria-expanded="false" type="button">'
					. '<svg aria-hidden="true" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"></polyline></svg>'
					. '</button>';
			}

			// If we injected category terms, render them as a sub-menu list (hidden by default, toggled via JS).
			if ( ! empty( $child_terms ) ) {
				$output .= '<ul class="sub-menu" hidden>';
				foreach ( $child_terms as $term ) {
					$output .= '<li class="menu-item"><a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
				}
				$output .= '</ul>';
			}
		}

		public function end_el( &$output, $item, $depth = 0, $args = null ) {
			$output .= '</li>';
		}
	}
}


