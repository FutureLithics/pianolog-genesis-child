<?php
/**
 * Pianolog Genesis Child - Functions
 *
 * @package PianologGenesisChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PIANOLOG_CHILD_THEME_NAME', 'Pianolog Genesis Child' );
define( 'PIANOLOG_CHILD_THEME_VERSION', '0.1.0' );
define( 'PIANOLOG_CHILD_THEME_URL', 'https://pianolog.local' );

// Initialize the Genesis Framework (loads from the parent theme).
require_once get_template_directory() . '/lib/init.php';

// Replace the default Genesis header and control nav placement immediately after Genesis loads.
remove_action( 'genesis_header', 'genesis_do_header' );
remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );
add_action( 'genesis_header', 'pianolog_do_custom_header' );
remove_action( 'genesis_after_header', 'genesis_do_nav' );
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
remove_action( 'genesis_header', 'genesis_do_nav' );
remove_action( 'genesis_header', 'genesis_do_subnav' );
remove_action( 'genesis_before_header', 'genesis_skip_links', 5 );

// Load includes.
require_once get_stylesheet_directory() . '/includes/class-pianolog-megamenu-walker.php';
require_once get_stylesheet_directory() . '/includes/class-pianolog-mobile-walker.php';
/**
 * Theme setup.
 */
add_action( 'after_setup_theme', 'pianolog_child_setup' );
function pianolog_child_setup() {
	// Make child theme available for translation.
	load_child_theme_textdomain( 'pianolog-genesis-child', get_stylesheet_directory() . '/languages' );

	// Core markup and features.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);

	// Responsive viewport meta for Genesis.
	add_theme_support( 'genesis-responsive-viewport' );

	// Accessibility enhancements from Genesis.
	add_theme_support(
		'genesis-accessibility',
		array(
			'search-form',
			'headings',
			'rems',
			'drop-down-menu',
		)
	);

	// Menus (Genesis-style).
	add_theme_support(
		'genesis-menus',
		array(
			'primary'   => __( 'Primary Navigation Menu', 'pianolog-genesis-child' ),
			'secondary' => __( 'Secondary Navigation Menu', 'pianolog-genesis-child' ),
		)
	);

	// Custom logo support.
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 400,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	// Genesis widget areas and layout options.
	add_theme_support( 'genesis-after-entry-widget-area' );
	add_theme_support( 'genesis-footer-widgets', 3 );

	// Additional menu location for mobile.
	register_nav_menus(
		array(
			'mobile' => __( 'Mobile Navigation Menu', 'pianolog-genesis-child' ),
		)
	);
}

/**
 * Enqueue child theme styles.
 */
add_action( 'wp_enqueue_scripts', 'pianolog_child_enqueue_styles' );
function pianolog_child_enqueue_styles() {
	// Google Fonts: Cormorant Garamond (serif) and Source Sans Pro (sans-serif).
	wp_enqueue_style(
		'pianolog-google-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Source+Sans+Pro:wght@400;600;700&display=swap',
		array(),
		null
	);

	// Design tokens first (separate file to avoid @import at runtime).
	wp_enqueue_style(
		'pianolog-tokens',
		get_stylesheet_directory_uri() . '/assets/css/settings/tokens.css',
		array(),
		PIANOLOG_CHILD_THEME_VERSION
	);

	wp_enqueue_style( 'pianolog-genesis-child', get_stylesheet_uri(), array(), PIANOLOG_CHILD_THEME_VERSION );

	// Mobile menu JS.
	wp_enqueue_script(
		'pianolog-mobile-menu',
		get_stylesheet_directory_uri() . '/assets/js/mobile-menu.js',
		array(),
		PIANOLOG_CHILD_THEME_VERSION,
		true
	);

	// Threaded comments (needed for proper comment rendering on single posts).
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/**
 * Output custom header markup via template part.
 */
function pianolog_do_custom_header() {
	get_template_part( 'template-parts/header', 'custom' );
}

add_filter( 'pianolog_megamenu_slugs', function( $slugs, $item, $args ) {
	// Return array of slugs like ['news','events']
	return array( 'gear-reviews');
}, 10, 3 );

/**
 * Customize Genesis footer credits text.
 */
add_filter( 'genesis_footer_creds_text', function () {
	$year = date( 'Y' );
	$site_name = get_bloginfo( 'name' );
	/* translators: 1: copyright symbol and year, 2: site name */
	$copyright = sprintf( esc_html__( 'Copyright © %1$s %2$s', 'pianolog-genesis-child' ), esc_html( $year ), esc_html( $site_name ) );
	$powered   = sprintf(
		/* translators: %s: Future Lithics LLC link */
		esc_html__( 'Powered by %s', 'pianolog-genesis-child' ),
		'<a href="https://futurelithics.com" rel="noopener" target="_blank">Future Lithics LLC</a>'
	);
	return '<span class="footer-creds">' . $copyright . ' · ' . $powered . '</span>';
} );

/**
 * Hints for external resources (Google Fonts).
 */
add_filter( 'wp_resource_hints', function( $hints, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$hints[] = 'https://fonts.googleapis.com';
		$hints[] = 'https://fonts.gstatic.com';
	}
	return $hints;
}, 10, 2 );

/**
 * Defer theme scripts to improve main-thread availability.
 */
add_filter( 'script_loader_tag', function( $tag, $handle ) {
	$defer_handles = array(
		'pianolog-mobile-menu',
		'pianolog-posts-grid',
		'pianolog-email-signup',
	);
	if ( in_array( $handle, $defer_handles, true ) ) {
		if ( false === strpos( $tag, ' defer' ) ) {
			$tag = str_replace( '<script ', '<script defer ', $tag );
		}
	}
	return $tag;
}, 10, 2 );

/**
 * Contact Form 7: only load assets when needed.
 * - Disable global load, and enqueue on pages that contain the shortcode.
 */
add_filter( 'wpcf7_load_js', '__return_false' );
add_filter( 'wpcf7_load_css', '__return_false' );
add_action( 'wp', function () {
	if ( is_singular() ) {
		global $post;
		if ( $post && ( has_shortcode( (string) $post->post_content, 'contact-form-7' ) || has_block( 'contact-form-7/contact-form-selector', $post ) ) ) {
			if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
				wpcf7_enqueue_scripts();
			}
			if ( function_exists( 'wpcf7_enqueue_styles' ) ) {
				wpcf7_enqueue_styles();
			}
		}
	}
} );

/**
 * Widget: Email Signup (Brevo)
 */
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
			'title'       => __( 'Stay in the loop', 'pianolog-genesis-child' ),
			'description' => __( 'Get the latest lessons, reviews, and resources delivered to your inbox.', 'pianolog-genesis-child' ),
			'list_id'     => '',
			'button_text' => __( 'Subscribe', 'pianolog-genesis-child' ),
			'enable_doi'  => 0,
			'template_id' => '',
			'redirect_url'=> '',
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
		$title       = isset( $instance['title'] ) ? $instance['title'] : '';
		$description = isset( $instance['description'] ) ? $instance['description'] : '';
		$list_id     = isset( $instance['list_id'] ) ? absint( $instance['list_id'] ) : 0;
		$enable_doi  = ! empty( $instance['enable_doi'] );
		$template_id = isset( $instance['template_id'] ) ? absint( $instance['template_id'] ) : 0;
		$redirect_url= isset( $instance['redirect_url'] ) ? $instance['redirect_url'] : '';
		$btn_text    = isset( $instance['button_text'] ) ? $instance['button_text'] : __( 'Subscribe', 'pianolog-genesis-child' );

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

add_action( 'widgets_init', function () {
	register_widget( 'Pianolog_Email_Signup_Widget' );
} );

add_action( 'wp_ajax_pianolog_subscribe', 'pianolog_handle_subscribe' );
add_action( 'wp_ajax_nopriv_pianolog_subscribe', 'pianolog_handle_subscribe' );
function pianolog_handle_subscribe() {
	if ( ! check_ajax_referer( 'pianolog_email_signup', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid request.', 'pianolog-genesis-child' ) ), 400 );
	}
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$list_id = isset( $_POST['list_id'] ) ? absint( $_POST['list_id'] ) : 0;
	$doi     = isset( $_POST['doi'] ) ? (int) $_POST['doi'] === 1 : false;
	$template_id = isset( $_POST['template_id'] ) ? absint( $_POST['template_id'] ) : 0;
	$redirect_url= isset( $_POST['redirect_url'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_url'] ) ) : '';
	if ( empty( $email ) || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please provide a valid email address.', 'pianolog-genesis-child' ) ), 400 );
	}
	$api_key = pianolog_get_brevo_api_key();
	if ( empty( $api_key ) ) {
		wp_send_json_error( array( 'message' => __( 'Email service is not configured.', 'pianolog-genesis-child' ) ), 500 );
	}
	if ( empty( $list_id ) ) {
		$list_id = absint( apply_filters( 'pianolog_brevo_list_id', 0 ) );
		if ( empty( $list_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Subscription list is not configured.', 'pianolog-genesis-child' ) ), 400 );
		}
	}

	// Choose endpoint: Double Opt-in vs. direct subscribe
	if ( $doi ) {
		if ( empty( $template_id ) || empty( $redirect_url ) ) {
			wp_send_json_error( array( 'message' => __( 'Double opt‑in requires a Template ID and Redirect URL.', 'pianolog-genesis-child' ) ), 400 );
		}
		// Preflight: Validate the transactional template exists and is active
		if ( apply_filters( 'pianolog_debug_email_signup', true ) ) {
			error_log( sprintf(
				'[Pianolog][DOI] Preflight check templateId=%s (listId=%s, redirectUrl=%s)',
				$template_id,
				$list_id,
				$redirect_url
			) );
		}
		$tpl_resp = wp_remote_get(
			'https://api.brevo.com/v3/smtp/templates/' . $template_id,
			array(
				'headers' => array(
					'accept'  => 'application/json',
					'api-key' => $api_key,
				),
				'timeout' => 12,
			)
		);
		if ( is_wp_error( $tpl_resp ) ) {
			if ( apply_filters( 'pianolog_debug_email_signup', true ) ) {
				error_log( '[Pianolog][DOI] Preflight WP_Error: ' . $tpl_resp->get_error_message() );
			}
			wp_send_json_error( array( 'message' => __( 'Unable to verify DOI template. Please try again.', 'pianolog-genesis-child' ) ), 500 );
		}
		$tpl_code = wp_remote_retrieve_response_code( $tpl_resp );
		$tpl_raw  = wp_remote_retrieve_body( $tpl_resp );
		if ( apply_filters( 'pianolog_debug_email_signup', true ) ) {
			error_log( sprintf(
				'[Pianolog][DOI] Preflight response code=%s body=%s',
				(string) $tpl_code,
				substr( (string) $tpl_raw, 0, 500 )
			) );
		}
		if ( $tpl_code !== 200 ) {
			$tpl_data = json_decode( $tpl_raw, true );
			$tpl_msg  = is_array( $tpl_data ) && isset( $tpl_data['message'] ) ? $tpl_data['message'] : __( 'Template not found or inaccessible.', 'pianolog-genesis-child' );
			wp_send_json_error( array( 'message' => sprintf( __( 'DOI template error: %s', 'pianolog-genesis-child' ), $tpl_msg ) ), 400 );
		}
		$tpl_data = json_decode( $tpl_raw, true );
		if ( is_array( $tpl_data ) && isset( $tpl_data['isActive'] ) && ! $tpl_data['isActive'] ) {
			wp_send_json_error( array( 'message' => __( 'The DOI template exists but is not Active. Please publish/activate it.', 'pianolog-genesis-child' ) ), 400 );
		}
		$endpoint = 'https://api.brevo.com/v3/contacts/doubleOptinConfirmation';
		$body     = array(
			'email'           => $email,
			'includeListIds'  => array( $list_id ),
			'templateId'      => $template_id,
			'redirectionUrl'  => $redirect_url,
			'attributes'      => new stdClass(),
		);
	} else {
		$endpoint = 'https://api.brevo.com/v3/contacts';
		$body     = array(
			'email'         => $email,
			'updateEnabled' => true,
			'listIds'       => array( $list_id ),
		);
	}
	$args     = array(
		'method'      => 'POST',
		'headers'     => array(
			'accept'       => 'application/json',
			'api-key'      => $api_key,
			'content-type' => 'application/json',
		),
		'timeout'     => 15,
		'data_format' => 'body',
		'body'        => wp_json_encode( $body ),
	);

	if ( apply_filters( 'pianolog_debug_email_signup', true ) ) {
		error_log( sprintf(
			'[Pianolog][DOI] POST %s payload=%s',
			$endpoint,
			substr( wp_json_encode( $body ), 0, 500 )
		) );
	}
	$response = wp_remote_post( $endpoint, $args );
	if ( is_wp_error( $response ) ) {
		if ( apply_filters( 'pianolog_debug_email_signup', true ) ) {
			error_log( '[Pianolog][DOI] POST WP_Error: ' . $response->get_error_message() );
		}
		wp_send_json_error( array( 'message' => $response->get_error_message() ), 500 );
	}
	$code = wp_remote_retrieve_response_code( $response );
	$raw  = wp_remote_retrieve_body( $response );
	if ( apply_filters( 'pianolog_debug_email_signup', true ) ) {
		error_log( sprintf(
			'[Pianolog][DOI] POST response code=%s body=%s',
			(string) $code,
			substr( (string) $raw, 0, 500 )
		) );
	}

	if ( $code >= 200 && $code < 300 ) {
		$msg = $doi
			? __( 'Thanks! Please check your email to confirm your subscription.', 'pianolog-genesis-child' )
			: __( 'Thanks! You are subscribed.', 'pianolog-genesis-child' );
		wp_send_json_success( array( 'message' => $msg ) );
	}
	$data = json_decode( $raw, true );
	$msg  = is_array( $data ) && isset( $data['message'] ) ? $data['message'] : __( 'Subscription failed. Please try again later.', 'pianolog-genesis-child' );
	wp_send_json_error( array( 'message' => $msg ), 400 );
}

add_action( 'wp_enqueue_scripts', function () {
	wp_register_script(
		'pianolog-email-signup',
		get_stylesheet_directory_uri() . '/assets/js/email-signup.js',
		array(),
		PIANOLOG_CHILD_THEME_VERSION,
		true
	);
	wp_localize_script(
		'pianolog-email-signup',
		'pianologEmailSignup',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'pianolog_email_signup' ),
		)
	);
	wp_enqueue_script( 'pianolog-email-signup' );
} );
/**
 * Provide Brevo API key via constant or filter.
 */
function pianolog_get_brevo_api_key(): string {
	$key = defined( 'PIANOLOG_BREVO_API_KEY' ) ? (string) constant( 'PIANOLOG_BREVO_API_KEY' ) : '';
	$key = apply_filters( 'pianolog_brevo_api_key', $key );
	return is_string( $key ) ? trim( $key ) : '';
}

/**
 * Ensure Brevo (Sendinblue) embed assets load in correct order and with required config.
 * This avoids timing/order issues when pasting snippets via Customizer.
 */
add_action( 'wp_enqueue_scripts', function () {
	// Only load Brevo’s own embed assets if explicitly enabled to avoid conflicts with custom widget.
	if ( ! apply_filters( 'pianolog_enable_brevo_embed', false) ) {
		return;
	}
	// 1) Required Brevo CSS (normally recommended in <head>)
	wp_enqueue_style(
		'brevo-forms',
		'https://sibforms.com/forms/end-form/build/sib-styles.css',
		array(),
		null
	);

	// 2) Main Brevo script (footer). Inject required window config BEFORE it.
	$brevo_handle = 'pianolog-brevo-main';
	wp_register_script(
		$brevo_handle,
		'https://sibforms.com/forms/end-form/build/main.js',
		array(),
		null,
		true
	);
	$brevo_config = <<<'JS'
window.REQUIRED_CODE_ERROR_MESSAGE = 'Please choose a country code';
window.LOCALE = 'en';
window.EMAIL_INVALID_MESSAGE = window.SMS_INVALID_MESSAGE = "The information provided is invalid. Please review the field format and try again.";
window.REQUIRED_ERROR_MESSAGE = "This field cannot be left blank. ";
window.GENERIC_INVALID_MESSAGE = "The information provided is invalid. Please review the field format and try again.";
window.translation = {
  common: {
    selectedList: '{quantity} list selected',
    selectedLists: '{quantity} lists selected',
    selectedOption: '{quantity} selected',
    selectedOptions: '{quantity} selected'
  }
};
var AUTOHIDE = Boolean(0);
JS;
	wp_add_inline_script( $brevo_handle, $brevo_config, 'before' );
	wp_enqueue_script( $brevo_handle );

	// 3) Let Brevo manage reCAPTCHA loading to avoid duplicate/ordering issues.

	// 4) Robust UI guard: manage loader state and recover from stuck submits
	$brevo_ui_guard = <<<'JS'
document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('sib-form');
  if (!form) return;
  var container = document.getElementById('sib-form-container') || form;
  var btn = form.querySelector('.sib-form-block__button');
  var loader = btn ? btn.querySelector('.progress-indicator__icon') : null;
  var success = document.getElementById('success-message');
  var error = document.getElementById('error-message');
  var recaptcha = form.querySelector('.g-recaptcha');
  if (recaptcha) recaptcha.removeAttribute('onclick');

  var pendingTimer = null;
  function resetButton() {
    if (pendingTimer) { clearTimeout(pendingTimer); pendingTimer = null; }
    if (btn) {
      btn.removeAttribute('disabled');
      btn.removeAttribute('aria-busy');
    }
    if (loader) { loader.classList.add('sib-hide-loader-icon'); }
  }
  function startGuard() {
    if (!btn) return;
    btn.setAttribute('disabled', 'disabled');
    btn.setAttribute('aria-busy', 'true');
    if (pendingTimer) clearTimeout(pendingTimer);
    pendingTimer = setTimeout(function () {
      var succShown = success && success.offsetParent !== null;
      var errShown = error && error.offsetParent !== null;
      if (!succShown && !errShown) {
        resetButton();
      }
    }, 8000);
  }
  // Guard on submit
  form.addEventListener('submit', function () { startGuard(); }, { once: false });
  // Observe DOM for success/error visibility changes
  var observer = new MutationObserver(function () {
    var succShown = success && success.offsetParent !== null;
    var errShown = error && error.offsetParent !== null;
    if (succShown || errShown) resetButton();
  });
  observer.observe(container, { attributes: true, childList: true, subtree: true });
});
JS;
	wp_add_inline_script( $brevo_handle, $brevo_ui_guard, 'after' );
} );

// (Intentionally left blank) Brevo main.js will load and manage reCAPTCHA.

/**
 * Shortcode: [pianolog_related_posts post_id="123" count="4" title="Related Posts"]
 * - Auto-detects current post when post_id is omitted.
 * - Selects posts from the same primary category, then parent category, then most recent.
 * - Outputs a container-responsive grid using .related-post-grid classes.
 */
add_shortcode( 'pianolog_related_posts', function( $atts ) {
	$atts = shortcode_atts(
		array(
			'post_id' => 0,
			'count'   => 4,
			'title'   => __( 'Related Posts', 'pianolog-genesis-child' ),
		),
		$atts,
		'pianolog_related_posts'
	);

	$post_id = absint( $atts['post_id'] );
	if ( $post_id <= 0 ) {
		$post_id = get_the_ID();
	}
	if ( ! $post_id ) {
		return '';
	}

	$target_count = max( 1, absint( $atts['count'] ) );
	$exclude_ids  = array( $post_id );

	$categories    = get_the_category( $post_id );
	$primary_term  = ! empty( $categories ) ? $categories[0] : null;
	$collected_ids = array();
	$related_ids   = array();

	$collect_posts = function ( WP_Query $q, int $target ) use ( &$related_ids, &$collected_ids ) {
		if ( ! $q->have_posts() ) {
			return;
		}
		while ( $q->have_posts() && count( $related_ids ) < $target ) {
			$q->the_post();
			$pid = get_the_ID();
			if ( in_array( $pid, $collected_ids, true ) ) {
				continue;
			}
			$related_ids[] = $pid;
			$collected_ids[] = $pid;
		}
		wp_reset_postdata();
	};

	// 1) Same category as primary term.
	if ( $primary_term instanceof WP_Term ) {
		$q1 = new WP_Query(
			array(
				'posts_per_page' => $target_count,
				'post_status'    => 'publish',
				'no_found_rows'  => true,
				'category__in'   => array( (int) $primary_term->term_id ),
				'post__not_in'   => $exclude_ids,
			)
		);
		$collect_posts( $q1, $target_count );

		// 2) From parent category if not enough.
		if ( count( $related_ids ) < $target_count && $primary_term->parent ) {
			$q2 = new WP_Query(
				array(
					'posts_per_page' => $target_count - count( $related_ids ),
					'post_status'    => 'publish',
					'no_found_rows'  => true,
					'category__in'   => array( (int) $primary_term->parent ),
					'post__not_in'   => array_merge( $exclude_ids, $collected_ids ),
				)
			);
			$collect_posts( $q2, $target_count );
		}
	}

	// 3) Fallback to most recent posts if still short.
	if ( count( $related_ids ) < $target_count ) {
		$q3 = new WP_Query(
			array(
				'posts_per_page' => $target_count - count( $related_ids ),
				'post_status'    => 'publish',
				'no_found_rows'  => true,
				'post__not_in'   => array_merge( $exclude_ids, $collected_ids ),
			)
		);
		$collect_posts( $q3, $target_count );
	}

	if ( empty( $related_ids ) ) {
		return '';
	}

	ob_start();
	$rel_q = new WP_Query(
		array(
			'post__in'       => $related_ids,
			'orderby'        => 'post__in',
			'posts_per_page' => count( $related_ids ),
			'no_found_rows'  => true,
		)
	);
	get_template_part(
		'template-parts/sections/related-posts',
		null,
		array(
			'title' => $atts['title'],
			'query' => $rel_q,
		)
	);
	return (string) ob_get_clean();
} );

// Force override of Genesis footer markup/text
remove_action( 'genesis_footer', 'genesis_do_footer' );
add_action( 'genesis_footer', function () {
	get_template_part( 'template-parts/footer', 'custom' );
}, 5 );

/**
 * Widget: Top-level Categories
 */
class Pianolog_Top_Categories_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'pianolog_top_categories',
			__( 'Pianolog: Top‑level Categories', 'pianolog-genesis-child' ),
			array(
				'classname'   => 'widget_pianolog_top_categories',
				'description' => __( 'Displays links to top‑level (parent = 0) post categories.', 'pianolog-genesis-child' ),
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

		$terms = get_terms(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => true,
				'parent'     => 0,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return;
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( ! empty( $title ) ) {
			// Use a dedicated h3 regardless of sidebar title wrappers to satisfy design.
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
		$field_id   = $this->get_field_id( 'title' );
		$field_name = $this->get_field_name( 'title' );
		?>
		<p>
			<label for="<?php echo esc_attr( $field_id ); ?>"><?php esc_html_e( 'Title:', 'pianolog-genesis-child' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
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
		return $instance;
	}
}

add_action(
	'widgets_init',
	function () {
		register_widget( 'Pianolog_Top_Categories_Widget' );
	}
);
/**
 * Register custom Footer widget areas (3 columns).
 */
add_action( 'widgets_init', function () {
	for ( $i = 1; $i <= 3; $i++ ) {
		register_sidebar( array(
			/* translators: %s: column number. */
			'name'          => sprintf( __( 'Footer Column %s', 'pianolog-genesis-child' ), $i ),
			'id'            => 'footer-col-' . $i,
			'description'   => sprintf( __( 'Footer column %s widget area.', 'pianolog-genesis-child' ), $i ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}
} );

/**
 * Output custom footer widget grid above footer credits.
 */
add_action( 'genesis_before_footer', function () {
	$areas = array( 'footer-col-1', 'footer-col-2', 'footer-col-3' );

	$has_widgets = false;
	foreach ( $areas as $area ) {
		if ( is_active_sidebar( $area ) ) {
			$has_widgets = true;
			break;
		}
	}
	if ( ! $has_widgets ) {
		return;
	}

	echo '<div class="footer-widgets"><div class="wrap container">';
	foreach ( $areas as $area ) {
		echo '<div class="footer-widget-col ' . $area . '">';
		dynamic_sidebar( $area );
		echo '</div>';
	}
	echo '</div></div>';
}, 5 );
/**
 * Shortcode: [pianolog_posts_section category="gear-reviews" title="Gear Reviews" posts="6" cta_text="View all" background="manuscript"]
 */
add_shortcode( 'pianolog_posts_section', 'pianolog_posts_section_shortcode' );
function pianolog_posts_section_shortcode( $atts ): string {
	$atts = shortcode_atts(
		array(
			'category'   => '',
			'title'      => '',
			'posts'      => 6,
			'cta_text'   => __( 'View all', 'pianolog-genesis-child' ),
			'background' => 'manuscript', // manuscript | white | none
			// Aliases accepted below
			'count'      => '',
			'per_page'   => '',
			'limit'      => '',
			'number'     => '',
			'num'        => '',
			'posts_per_page' => '',
			'show_title' => 'true',
		),
		$atts,
		'pianolog_posts_section'
	);

	$category_slug = sanitize_title( $atts['category'] );
	if ( empty( $category_slug ) ) {
		return '';
	}

	// Resolve posts per page from multiple aliases and support 'all'.
	$raw_posts = '';
	foreach ( array( 'posts', 'count', 'per_page', 'limit', 'number', 'num', 'posts_per_page' ) as $key ) {
		if ( isset( $atts[ $key ] ) && $atts[ $key ] !== '' ) {
			$raw_posts = $atts[ $key ];
			break;
		}
	}
	$per_page = 6;
	$load_more = false;
	if ( is_string( $raw_posts ) && strtolower( trim( (string) $raw_posts ) ) === 'all' ) {
		$per_page  = 9; // initial batch
		$load_more = true;
	} elseif ( $raw_posts !== '' ) {
		$per_page = absint( $raw_posts );
		$per_page = $per_page > 0 ? $per_page : 6;
	}

	// Normalize show_title to boolean
	$show_title = true;
	$st = strtolower( trim( (string) $atts['show_title'] ) );
	if ( in_array( $st, array( 'false', '0', 'no', 'off' ), true ) ) {
		$show_title = false;
	}

	$term = get_category_by_slug( $category_slug );
	if ( ! $term ) {
		return '';
	}

	$query = new WP_Query(
		array(
			'posts_per_page' => $per_page,
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'category_name'  => $category_slug,
			'paged'          => 1,
		)
	);

	$args = array(
		'title'        => $atts['title'] ? $atts['title'] : $term->name,
		'cta_text'     => $load_more ? __( 'Load More', 'pianolog-genesis-child' ) : $atts['cta_text'],
		'cta_link'     => get_category_link( $term ),
		'background'   => strtolower( (string) $atts['background'] ),
		'section_id'   => 'posts-grid-' . $category_slug,
		'query'        => $query,
		'load_more'    => $load_more,
		'category_slug'=> $category_slug,
		'per_page'     => $per_page,
		'show_title'   => $show_title,
	);

	// Ensure JS for load more is enqueued once if needed.
	if ( $load_more ) {
		wp_enqueue_script( 'pianolog-posts-grid' );
	}

	ob_start();
	get_template_part( 'template-parts/sections/posts-grid', null, $args );
	$output = ob_get_clean();

	wp_reset_postdata();
	return $output ?: '';
}

// AJAX handler for Load More posts
add_action( 'wp_ajax_pianolog_load_more_posts', 'pianolog_load_more_posts' );
add_action( 'wp_ajax_nopriv_pianolog_load_more_posts', 'pianolog_load_more_posts' );
function pianolog_load_more_posts() {
	check_ajax_referer( 'pianolog_posts_grid', 'nonce' );

	$category_slug = isset( $_POST['category'] ) ? sanitize_title( wp_unslash( $_POST['category'] ) ) : '';
	$page          = isset( $_POST['page'] ) ? max( 2, absint( $_POST['page'] ) ) : 2;
	$per_page      = isset( $_POST['per_page'] ) ? max( 1, absint( $_POST['per_page'] ) ) : 9;

	if ( empty( $category_slug ) ) {
		wp_send_json_error( array( 'message' => 'Missing category' ), 400 );
	}

	$q = new WP_Query(
		array(
			'posts_per_page' => $per_page,
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'category_name'  => $category_slug,
			'paged'          => $page,
		)
	);

	ob_start();
	if ( $q->have_posts() ) {
		while ( $q->have_posts() ) {
			$q->the_post();
			$thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
			?>
			<article <?php post_class( 'gear-card' ); ?>>
				<a class="gear-card__link" href="<?php the_permalink(); ?>">
					<div class="gear-card__image">
						<?php if ( $thumb_url ) : ?>
							<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
						<?php else : ?>
							<div class="gear-card__image--placeholder"></div>
						<?php endif; ?>
					</div>
					<div class="gear-card__content">
						<h3 class="gear-card__title"><?php the_title(); ?></h3>
						<p class="gear-card__excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 300 ) ); ?></p>
						<div class="gear-card__meta"><span class="gear-card__author"><?php echo esc_html( get_the_author() ); ?></span></div>
					</div>
				</a>
			</article>
			<?php
		}
	}
	$html = ob_get_clean();
	wp_reset_postdata();

	wp_send_json_success( array( 'html' => $html, 'count' => $q->post_count ) );
}

// Enqueue posts-grid.js and localize
add_action( 'wp_enqueue_scripts', function () {
	wp_register_script(
		'pianolog-posts-grid',
		get_stylesheet_directory_uri() . '/assets/js/posts-grid.js',
		array(),
		PIANOLOG_CHILD_THEME_VERSION,
		true
	);
	wp_localize_script( 'pianolog-posts-grid', 'pianologPostsGrid', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'pianolog_posts_grid' ),
	) );
} );


add_filter('pianolog_brevo_api_key', fn() => defined('PIANOLOG_BREVO_API_KEY') ? PIANOLOG_BREVO_API_KEY : '');