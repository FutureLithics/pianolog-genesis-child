<?php
/**
 * Pianolog Megamenu Walker
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Pianolog_Megamenu_Walker' ) && class_exists( 'Walker_Nav_Menu' ) ) {
	class Pianolog_Megamenu_Walker extends Walker_Nav_Menu {
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$is_top_level       = ( 0 === (int) $depth );
			$item_classes       = empty( $item->classes ) ? array() : (array) $item->classes;
			$is_mega_categories = $is_top_level && in_array( 'mega-categories', $item_classes, true );

			$classes = array_map( 'sanitize_html_class', $item_classes );
			if ( $is_mega_categories ) {
				$classes[] = 'menu-item-has-mega';
			}

			$output .= '<li class="' . esc_attr( implode( ' ', array_filter( $classes ) ) ) . '">';
			$attr  = ' href="' . esc_url( $item->url ) . '"';
			if ( $is_mega_categories ) {
				$attr .= ' aria-haspopup="true" aria-expanded="false"';
			}
			$output .= '<a' . $attr . '>' . esc_html( $item->title ) . '</a>';

			if ( $is_mega_categories ) {
					$specified_slugs = $this->get_specified_slugs( $item_classes );
					/**
					 * Allow programmatic override of specified slugs.
					 *
					 * @param array            $specified_slugs Array of category slugs to render at top level.
					 * @param WP_Post          $item            The current menu item.
					 * @param stdClass|array   $args            Menu args.
					 */
					$specified_slugs = apply_filters( 'pianolog_megamenu_slugs', $specified_slugs, $item, $args );


					$children = array();

					if ( ! empty( $specified_slugs ) ) {
						// Fetch specific categories by slug (allowing non–top-level terms).
						$maybe_terms = get_terms(
							array(
								'taxonomy'   => 'category',
								'slug'       => $specified_slugs,
								'hide_empty' => false,
							)
						);
						if ( ! is_wp_error( $maybe_terms ) && ! empty( $maybe_terms ) ) {
							// Build map by slug to preserve the order provided in $specified_slugs.
							$by_slug = array();
							foreach ( $maybe_terms as $t ) {
								$by_slug[ $t->slug ] = $t;
							}
							// Push only the specified terms in the order provided.
							foreach ( $specified_slugs as $slug ) {
								if ( isset( $by_slug[ $slug ] ) ) {
									$children[] = $by_slug[ $slug ];
								}
							}
							// Do not append unrelated top-level terms; honor only specified slugs.
						}
					}

					// Fallback: derive from the linked root category or site top-level categories.
					if ( empty( $children ) ) {
						$root_term_id = 0;
						if ( 'category' === $item->object ) {
							$root_term_id = (int) $item->object_id;
						}
						$children = get_terms(
							array(
								'taxonomy'   => 'category',
								'parent'     => $root_term_id,
								'hide_empty' => false,
							)
						);
					}

					if ( ! is_wp_error( $children ) && ! empty( $children ) ) {
					// Compute column span based on final count.
					$columns_interval = max( 1, intval( 12 / max( 1, count( $i = $children ) ) ) );
					$output          .= '<div class="mega-menu"><div class="mega-panel"><div class="container"><div class="row">';
					foreach ( $children as $term ) {
						$columns_interval_class = 'col-' . $columns_interval;
						
						$output .= '<div class="' . $columns_interval_class . ' mega-item">';
						$output .= '<a class="mega-parent" href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';

						$grandchildren = get_terms(
							array(
								'taxonomy'   => 'category',
								'parent'     => (int) $term->term_id,
								'hide_empty' => false,
							)
						);
						if ( ! is_wp_error( $grandchildren ) && ! empty( $grandchildren ) ) {
							$output .= '<ul class="mega-children">';
							foreach ( $grandchildren as $g ) {
								$output .= '<li><a href="' . esc_url( get_term_link( $g ) ) . '">' . esc_html( $g->name ) . '</a></li>';
							}
							$output .= '</ul>';
						}
						$output .= '</div>';
					}
					$output .= '</div></div></div></div>';
				}
			}
		}

		public function end_el( &$output, $item, $depth = 0, $args = null ) {
			$output .= '</li>';
		}

			/**
			 * Parse item CSS classes to find "mega-include-slug-{slug}" tokens.
			 *
			 * @param array $classes Menu item classes.
			 * @return array Array of slugs.
			 */
			protected function get_specified_slugs( $classes ) {
				$slugs = array();
				foreach ( (array) $classes as $class ) {
					if ( 0 === strpos( $class, 'mega-include-slug-' ) ) {
						$slug = substr( $class, strlen( 'mega-include-slug-' ) );
						if ( $slug !== '' ) {
							$slugs[] = sanitize_title( $slug );
						}
					}
				}
				// Deduplicate.
				$slugs = array_values( array_unique( $slugs ) );
				return $slugs;
			}
	}
}


