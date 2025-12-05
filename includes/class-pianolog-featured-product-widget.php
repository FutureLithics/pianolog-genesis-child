<?php
/**
 * Pianolog Featured Product Widget
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Pianolog_Featured_Product_Widget' ) && class_exists( 'WP_Widget' ) ) {
	class Pianolog_Featured_Product_Widget extends WP_Widget {

		public function __construct() {
			parent::__construct(
				'pianolog_featured_product',
				__( 'Pianolog: Featured Product', 'pianolog-genesis-child' ),
				array(
					'classname'   => 'widget_pianolog_featured_product',
					'description' => __( 'Highlights a selected WooCommerce product with image, price, and call-to-action.', 'pianolog-genesis-child' ),
				)
			);
		}

		/**
		 * Render the widget output on the front-end.
		 *
		 * @param array $args     Display arguments.
		 * @param array $instance Saved values.
		 */
		public function widget( $args, $instance ) {
			$product_id = isset( $instance['product_id'] ) ? absint( $instance['product_id'] ) : 0;
			if ( ! $product_id ) {
				return;
			}

			$product_post = get_post( $product_id );
			if ( ! $product_post || 'product' !== $product_post->post_type ) {
				return;
			}

			$title          = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Featured Product', 'pianolog-genesis-child' );
			$title          = apply_filters( 'widget_title', $title, $instance, $this->id_base );
			$highlight      = isset( $instance['highlight_label'] ) ? $instance['highlight_label'] : '';
			$button_text    = isset( $instance['button_text'] ) && $instance['button_text'] !== '' ? $instance['button_text'] : __( 'View Product', 'pianolog-genesis-child' );
			$permalink      = get_permalink( $product_post );
			$restore_wc_filter = false;
			if (
				function_exists( 'is_product' )
				&& is_product()
				&& class_exists( 'WC_Template_Loader' )
				&& has_filter( 'post_thumbnail_html', array( 'WC_Template_Loader', 'unsupported_theme_single_featured_image_filter' ) )
			) {
				remove_filter( 'post_thumbnail_html', array( 'WC_Template_Loader', 'unsupported_theme_single_featured_image_filter' ) );
				$restore_wc_filter = true;
			}

			$thumb_html = get_the_post_thumbnail( $product_post, 'medium', array( 'class' => 'pianolog-featured-product__image' ) );

			if ( $restore_wc_filter ) {
				add_filter( 'post_thumbnail_html', array( 'WC_Template_Loader', 'unsupported_theme_single_featured_image_filter' ) );
			}
			$excerpt_source = $product_post->post_excerpt ? $product_post->post_excerpt : wp_strip_all_tags( $product_post->post_content );
			$excerpt        = $excerpt_source ? wp_trim_words( $excerpt_source, 25 ) : '';

			$price_html = '';
			if ( function_exists( 'wc_get_product' ) ) {
				$wc_product = wc_get_product( $product_id );
				if ( $wc_product ) {
					$price_html = $wc_product->get_price_html();
				}
			}

			echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( ! empty( $title ) ) {
				echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
			<div class="pianolog-featured-product">
				<?php if ( $highlight ) : ?>
					<span class="pianolog-featured-product__eyebrow"><?php echo esc_html( $highlight ); ?></span>
				<?php endif; ?>

				<?php if ( $thumb_html ) : ?>
					<a class="pianolog-featured-product__image-wrap" href="<?php echo esc_url( $permalink ); ?>">
						<?php echo wp_kses_post( $thumb_html ); ?>
					</a>
				<?php endif; ?>

				<h4 class="pianolog-featured-product__name">
					<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $product_post ) ); ?></a>
				</h4>

				<?php if ( $price_html ) : ?>
					<div class="pianolog-featured-product__price"><?php echo wp_kses_post( $price_html ); ?></div>
				<?php endif; ?>

				<?php if ( $excerpt ) : ?>
					<p class="pianolog-featured-product__excerpt"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>

				<a class="pianolog-featured-product__button button" href="<?php echo esc_url( $permalink ); ?>">
					<?php echo esc_html( $button_text ); ?>
				</a>
			</div>
			<?php

			echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Output widget admin form.
		 *
		 * @param array $instance Saved values.
		 */
		public function form( $instance ) {
			$defaults = array(
				'title'           => __( 'Featured Product', 'pianolog-genesis-child' ),
				'product_id'      => '',
				'button_text'     => __( 'View Product', 'pianolog-genesis-child' ),
				'highlight_label' => '',
			);
			$instance = wp_parse_args( (array) $instance, $defaults );

			$title_field_id   = $this->get_field_id( 'title' );
			$title_field_name = $this->get_field_name( 'title' );
			?>
			<p>
				<label for="<?php echo esc_attr( $title_field_id ); ?>"><?php esc_html_e( 'Title', 'pianolog-genesis-child' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $title_field_id ); ?>" name="<?php echo esc_attr( $title_field_name ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
			</p>
			<?php
			$product_field_id   = $this->get_field_id( 'product_id' );
			$product_field_name = $this->get_field_name( 'product_id' );
			$datalist_id        = $product_field_id . '_list';
			$products           = get_posts(
				array(
					'post_type'      => 'product',
					'posts_per_page' => 50,
					'post_status'    => 'publish',
					'orderby'        => 'title',
					'order'          => 'ASC',
				)
			);
			?>
			<p>
				<label for="<?php echo esc_attr( $product_field_id ); ?>"><?php esc_html_e( 'Product ID', 'pianolog-genesis-child' ); ?></label>
				<input
					class="widefat"
					id="<?php echo esc_attr( $product_field_id ); ?>"
					name="<?php echo esc_attr( $product_field_name ); ?>"
					type="text"
					inputmode="numeric"
					pattern="\d*"
					value="<?php echo esc_attr( $instance['product_id'] ); ?>"
					list="<?php echo esc_attr( $datalist_id ); ?>"
				>
				<?php if ( ! empty( $products ) ) : ?>
					<datalist id="<?php echo esc_attr( $datalist_id ); ?>">
						<?php foreach ( $products as $product ) : ?>
							<option value="<?php echo esc_attr( $product->ID ); ?>"><?php echo esc_html( $product->post_title ); ?></option>
						<?php endforeach; ?>
					</datalist>
					<small><?php esc_html_e( 'Start typing to see matching published products.', 'pianolog-genesis-child' ); ?></small>
				<?php else : ?>
					<small><?php esc_html_e( 'No published products found yet. Enter a product ID manually.', 'pianolog-genesis-child' ); ?></small>
				<?php endif; ?>
			</p>

			<?php
			$highlight_field_id   = $this->get_field_id( 'highlight_label' );
			$highlight_field_name = $this->get_field_name( 'highlight_label' );
			?>
			<p>
				<label for="<?php echo esc_attr( $highlight_field_id ); ?>"><?php esc_html_e( 'Highlight label (optional)', 'pianolog-genesis-child' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $highlight_field_id ); ?>" name="<?php echo esc_attr( $highlight_field_name ); ?>" type="text" value="<?php echo esc_attr( $instance['highlight_label'] ); ?>" placeholder="<?php esc_attr_e( 'Editor’s Pick, Staff Favorite…', 'pianolog-genesis-child' ); ?>">
			</p>
			<?php
			$button_field_id   = $this->get_field_id( 'button_text' );
			$button_field_name = $this->get_field_name( 'button_text' );
			?>
			<p>
				<label for="<?php echo esc_attr( $button_field_id ); ?>"><?php esc_html_e( 'Button Text', 'pianolog-genesis-child' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $button_field_id ); ?>" name="<?php echo esc_attr( $button_field_name ); ?>" type="text" value="<?php echo esc_attr( $instance['button_text'] ); ?>">
			</p>
			<?php
		}

		/**
		 * Save widget settings.
		 *
		 * @param array $new_instance New values.
		 * @param array $old_instance Old values.
		 *
		 * @return array
		 */
		public function update( $new_instance, $old_instance ) {
			$instance                     = array();
			$instance['title']            = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
			$instance['product_id']       = isset( $new_instance['product_id'] ) ? absint( $new_instance['product_id'] ) : 0;
			$instance['button_text']      = isset( $new_instance['button_text'] ) ? sanitize_text_field( $new_instance['button_text'] ) : __( 'View Product', 'pianolog-genesis-child' );
			$instance['highlight_label']  = isset( $new_instance['highlight_label'] ) ? sanitize_text_field( $new_instance['highlight_label'] ) : '';
			return $instance;
		}
	}
}


