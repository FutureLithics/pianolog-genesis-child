<?php
/**
 * Pianolog Email Signup Widget (Brevo)
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Pianolog_Email_Signup_Widget' ) && class_exists( 'WP_Widget' ) ) {
	class Pianolog_Email_Signup_Widget extends WP_Widget {
		public function __construct() {
			parent::__construct(
				'pianolog_email_signup',
				__( 'Pianolog: Email Signup (Brevo)', 'pianolog-genesis-child' ),
				array(
					'classname'   => 'widget_pianolog_email_signup',
					'description' => __( 'Email signup form that subscribes contacts to a Brevo (Sendinblue) list.', 'pianolog-genesis-child' ),
				)
			);
		}

		public function form( $instance ) {
			$defaults = array(
				'title'        => __( 'Stay in the loop', 'pianolog-genesis-child' ),
				'description'  => __( 'Get the latest lessons, reviews, and resources delivered to your inbox.', 'pianolog-genesis-child' ),
				'list_id'      => '',
				'button_text'  => __( 'Subscribe', 'pianolog-genesis-child' ),
				'enable_doi'   => 0,
				'template_id'  => '',
				'redirect_url' => '',
			);
			$instance = wp_parse_args( (array) $instance, $defaults );
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'pianolog-genesis-child' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php esc_html_e( 'Description', 'pianolog-genesis-child' ); ?></label>
				<textarea class="widefat" rows="3" id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>"><?php echo esc_textarea( $instance['description'] ); ?></textarea>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'list_id' ) ); ?>"><?php esc_html_e( 'Brevo List ID', 'pianolog-genesis-child' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'list_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'list_id' ) ); ?>" type="number" value="<?php echo esc_attr( $instance['list_id'] ); ?>" placeholder="e.g. 3">
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_html_e( 'Button Text', 'pianolog-genesis-child' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['button_text'] ); ?>">
			</p>
			<hr>
			<p>
				<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'enable_doi' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'enable_doi' ) ); ?>" <?php checked( (int) $instance['enable_doi'], 1 ); ?> value="1">
				<label for="<?php echo esc_attr( $this->get_field_id( 'enable_doi' ) ); ?>"><?php esc_html_e( 'Enable Double Opt‑in', 'pianolog-genesis-child' ); ?></label>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'template_id' ) ); ?>"><?php esc_html_e( 'DOI Template ID', 'pianolog-genesis-child' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'template_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'template_id' ) ); ?>" type="number" value="<?php echo esc_attr( $instance['template_id'] ); ?>" placeholder="e.g. 12">
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'redirect_url' ) ); ?>"><?php esc_html_e( 'DOI Redirect URL', 'pianolog-genesis-child' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'redirect_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'redirect_url' ) ); ?>" type="url" value="<?php echo esc_attr( $instance['redirect_url'] ); ?>" placeholder="https://example.com/thank-you">
			</p>
			<p class="description">
				<?php esc_html_e( 'Provide your Brevo API key via wp-config.php (PIANOLOG_BREVO_API_KEY) or the "pianolog_brevo_api_key" filter.', 'pianolog-genesis-child' ); ?>
			</p>
			<?php
		}

		public function update( $new_instance, $old_instance ) {
			$instance                 = array();
			$instance['title']        = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
			$instance['description']  = isset( $new_instance['description'] ) ? wp_kses_post( $new_instance['description'] ) : '';
			$instance['list_id']      = isset( $new_instance['list_id'] ) ? absint( $new_instance['list_id'] ) : '';
			$instance['button_text']  = isset( $new_instance['button_text'] ) ? sanitize_text_field( $new_instance['button_text'] ) : __( 'Subscribe', 'pianolog-genesis-child' );
			$instance['enable_doi']   = isset( $new_instance['enable_doi'] ) ? 1 : 0;
			$instance['template_id']  = isset( $new_instance['template_id'] ) ? absint( $new_instance['template_id'] ) : '';
			$instance['redirect_url'] = isset( $new_instance['redirect_url'] ) ? esc_url_raw( $new_instance['redirect_url'] ) : '';
			return $instance;
		}

		public function widget( $args, $instance ) {
			$title        = isset( $instance['title'] ) ? $instance['title'] : '';
			$description  = isset( $instance['description'] ) ? $instance['description'] : '';
			$list_id      = isset( $instance['list_id'] ) ? absint( $instance['list_id'] ) : 0;
			$enable_doi   = ! empty( $instance['enable_doi'] );
			$template_id  = isset( $instance['template_id'] ) ? absint( $instance['template_id'] ) : 0;
			$redirect_url = isset( $instance['redirect_url'] ) ? $instance['redirect_url'] : '';
			$btn_text     = isset( $instance['button_text'] ) ? $instance['button_text'] : __( 'Subscribe', 'pianolog-genesis-child' );

			echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( ! empty( $title ) ) {
				echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			if ( $description ) {
				echo '<p class="sib-signup__description">' . wp_kses_post( $description ) . '</p>';
			}
			$nonce = wp_create_nonce( 'pianolog_email_signup' );
			?>
			<form class="sib_signup_form" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-action="pianolog_subscribe" data-nonce="<?php echo esc_attr( $nonce ); ?>" data-list-id="<?php echo esc_attr( $list_id ); ?>" data-doi="<?php echo esc_attr( $enable_doi ? '1' : '0' ); ?>" data-template-id="<?php echo esc_attr( $template_id ); ?>" data-redirect-url="<?php echo esc_attr( $redirect_url ); ?>">
				<div class="sib_loader" style="display:none">
					<img src="<?php echo esc_url( includes_url( 'images/spinner-2x.gif' ) ); ?>" alt="<?php esc_attr_e( 'Loading', 'pianolog-genesis-child' ); ?>" width="18" height="18">
				</div>
				<p class="sib-email-area">
					<?php esc_html_e( 'Email Address', 'pianolog-genesis-child' ); ?>*
					<input type="email" class="sib-email-area" name="email" required="required" placeholder="you@example.com">
				</p>
				<p>
					<button type="submit" class="sib-default-btn"><?php echo esc_html( $btn_text ); ?></button>
				</p>
				<input type="hidden" name="action" value="pianolog_subscribe">
				<input type="hidden" name="list_id" value="<?php echo esc_attr( $list_id ); ?>">
				<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">
				<input type="hidden" name="doi" value="<?php echo esc_attr( $enable_doi ? '1' : '0' ); ?>">
				<input type="hidden" name="template_id" value="<?php echo esc_attr( $template_id ); ?>">
				<input type="hidden" name="redirect_url" value="<?php echo esc_attr( $redirect_url ); ?>">
				<div class="sib_msg_disp" style="display:none"></div>
			</form>
			<?php
			echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}


