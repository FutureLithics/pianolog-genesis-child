<?php
/**
 * Comments Template
 *
 * Minimal, accessible comments list and form with threading support.
 *
 * @package PianologGenesisChild
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php esc_html_e( 'Comments', 'pianolog-genesis-child' ); ?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
					'reply_text'  => __( 'Reply', 'pianolog-genesis-child' ),
					'max_depth'   => get_option( 'thread_comments_depth' ),
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => '&larr; ' . __( 'Previous', 'pianolog-genesis-child' ),
				'next_text' => __( 'Next', 'pianolog-genesis-child' ) . ' &rarr;',
			)
		);
		?>

	<?php endif; ?>

	<?php
	if ( ! comments_open() && get_comments_number() ) :
		?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'pianolog-genesis-child' ); ?></p>
		<?php
	endif;
	?>

	<?php
	comment_form(
		array(
			'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h3>',
			'class_submit'       => 'submit',
		)
	);
	?>
</div>


