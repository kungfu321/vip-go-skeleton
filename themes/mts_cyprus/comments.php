<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package Cyprus
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) { ?>
	<p class="nocomments"><?php esc_html_e( 'This post is password protected. Enter the password to view comments.', 'cyprus' ); ?></p>
	<?php
	return;
}

if ( have_comments() ) :
?>
	<div id="comments">

		<div class="comment-title">

			<h4 class="total-comments"><?php comments_number( esc_html__( 'No Responses', 'cyprus' ), esc_html__( 'One Response', 'cyprus' ), esc_html__( '% Comments', 'cyprus' ) ); ?></h4>
		</div>

		<ol class="commentlist clearfix">
			<?php
			// List comments.
			wp_list_comments( 'callback=cyprus_comments' );

			// Comments pagination.
			the_comments_navigation( array(
				'prev_text' => '<i class="fa fa-angle-double-left"></i> ' . esc_html__( 'Older comments', 'cyprus' ),
				'next_text' => esc_html__( 'Newer Comments', 'cyprus' ) . ' <i class="fa fa-angle-double-right"></i>',
			) );
			?>
		</ol>

	</div>
<?php endif; ?>

<?php if ( comments_open() ) : ?>
	<div id="commentsAdd">

		<div id="respond" class="box m-t-6">
			<?php
			global $aria_req; $comments_args = array(
				'title_reply'          => '<h4><span>' . esc_html__( 'Leave a Reply', 'cyprus' ) . '</span></h4>',
				'comment_notes_before' => '',
				'comment_notes_after'  => '',
				'label_submit'         => esc_html__( 'Post Comment', 'cyprus' ),
				'comment_field'        => '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="' . esc_html__( 'Comment Text*', 'cyprus' ) . '"></textarea></p>',
				'fields'               => apply_filters( 'comment_form_default_fields', array(
					'author'  => '<p class="comment-form-author">' . ( $req ? '' : '' ) . '<input id="author" name="author" type="text" placeholder="' . esc_html__( 'Name*', 'cyprus' ) . '" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
					'email'   => '<p class="comment-form-email">' . ( $req ? '' : '' ) . '<input id="email" name="email" type="text" placeholder="' . esc_html__( 'Email*', 'cyprus' ) . '" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
					'url'     => '<p class="comment-form-url"><input id="url" name="url" type="text" placeholder="' . esc_html__( 'Website', 'cyprus' ) . '" value="' . esc_url( $commenter['comment_author_url'] ) . '" size="30" /></p>',
					'cookies' => '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"/>' .
					'<label for="wp-comment-cookies-consent">' . __( 'Save my name, email, and website in this browser for the next time I comment.', 'cyprus' ) . '</label></p>',
				) ),
			);
			comment_form( $comments_args );
			?>
		</div>

	</div>
<?php
endif;
