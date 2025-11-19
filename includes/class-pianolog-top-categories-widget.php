<?php
/**
 * Pianolog Top-level (or Child) Categories Widget
 */

if ( ! class_exists( 'Pianolog_Top_Categories_Widget' ) && class_exists( 'WP_Widget' ) ) {
	class Pianolog_Top_Categories_Widget extends WP_Widget {
		public function __construct() {
			parent::__construct(
				'pianolog_top_categories',
				__( 'Pianolog: Top‑level Categories', 'pianolog-genesis-child' ),
				array(
					'classname'   => 'widget_pianolog_top_categories',
					'description' => __( 'Displays links to categories: either top‑level or children of a selected parent.', 'pianolog-genesis-child' ),
				)
			);
		}

		/**
		 * Front‑end display.
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Categories', 'pianolog-genesis-child' );
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

			$parent_id = isset( $instance['parent_category'] ) ? absint( $instance['parent_category'] ) : 0;

			$terms = get_terms(
				array(
					'taxonomy'   => 'category',
					'hide_empty' => true,
					'parent'     => $parent_id, // 0 for top-level, otherwise children of selected parent.
					'orderby'    => 'name',
					'order'      => 'ASC',
				)
			);
			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				return;
			}

			echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( ! empty( $title ) ) {
				echo '<h3 class="widget-title">' . esc_html( $title ) . '</h3>';
			}

			echo '<ul class="pianolog-top-categories">';
			foreach ( $terms as $term ) {
				$link = get_term_link( $term );
				if ( is_wp_error( $link ) ) {
					continue;
				}
				printf(
					'<li><a href="%1$s">%2$s</a></li>',
					esc_url( $link ),
					esc_html( $term->name )
				);
			}
			echo '</ul>';

			echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Backend form.
		 *
		 * @param array $instance Previously saved values.
		 */
		public function form( $instance ) {
			$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Categories', 'pianolog-genesis-child' );
			$parent_category = isset( $instance['parent_category'] ) ? absint( $instance['parent_category'] ) : 0;
			$field_id   = $this->get_field_id( 'title' );
			$field_name = $this->get_field_name( 'title' );
			?>
			<p>
				<label for="<?php echo esc_attr( $field_id ); ?>"><?php esc_html_e( 'Title:', 'pianolog-genesis-child' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'parent_category' ) ); ?>"><?php esc_html_e( 'Parent Category (optional):', 'pianolog-genesis-child' ); ?></label>
				<?php
				wp_dropdown_categories(
					array(
						'taxonomy'           => 'category',
						'hide_empty'         => false,
						'name'               => esc_attr( $this->get_field_name( 'parent_category' ) ),
						'id'                 => esc_attr( $this->get_field_id( 'parent_category' ) ),
						'show_option_none'   => __( '— Top‑level (no parent) —', 'pianolog-genesis-child' ),
						'option_none_value'  => '0',
						'selected'           => $parent_category,
						'orderby'            => 'name',
						'order'              => 'ASC',
					)
				);
				?>
				<small><?php esc_html_e( 'Leave as “Top‑level” to list top categories. Choose a parent to list only its direct children.', 'pianolog-genesis-child' ); ?></small>
			</p>
			<?php
		}

		/**
		 * Sanitize and save widget form values.
		 *
		 * @param array $new_instance New values.
		 * @param array $old_instance Old values.
		 *
		 * @return array
		 */
		public function update( $new_instance, $old_instance ) {
			$instance          = array();
			$instance['title'] = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
			$instance['parent_category'] = isset( $new_instance['parent_category'] ) ? absint( $new_instance['parent_category'] ) : 0;
			return $instance;
		}
	}
}


