<?php
$mts_options = get_option(MTS_THEME_NAME);
/**
 * The template for displaying the comments.
 *
 * This contains both the comments and the comment form.
 */

// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
die ( __('Please do not load this page directly. Thanks!', 'crypto' ) );
 
if ( post_password_required() ) { ?>
<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'crypto' ); ?></p>
<?php
return;
}
?>
<!-- You can start editing here. -->
<?php if ( comments_open() ) : ?>
	<div id="comments">
		<h4 class="comments-heading"><?php comments_number(__('Comments (No)', 'crypto' ), __('Comment (1)', 'crypto' ),  __('Comments (%)', 'crypto' ) );?></h4>
		<?php if($mts_options['mts_facebook_comments'] == 1) { ?>
			<div class="facebook-comments">
				<?php if ( post_password_required() ) : ?>
			    	    <p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'crypto' ); ?></p>
			        </div>
			        <?php return; ?>
			    <?php endif; ?>
			 
			    <?php if ( comments_open() ) : ?>
			        <div class="fb-comments" data-href="<?php the_permalink(); ?>" data-numposts="5" data-colorscheme="light" data-width="100%"></div>
			    <?php endif; ?>

			    <?php if ( ! comments_open() ) : ?>
					<p class="nocomments"></p>
			    <?php endif; ?>
		    </div>
	    <?php } ?>
	    <?php if ( get_comments_number() > 0 ) : ?>
		    <div class="commentlist-wrap">
				<ol class="commentlist">
					<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { // are there comments to navigate through ?>
						<div class="navigation">
							<div class="alignleft"><?php previous_comments_link() ?></div>
							<div class="alignright"><?php next_comments_link() ?></div>
						</div>
					<?php }
					
					wp_list_comments('callback=mts_comments');
					
					if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { // are there comments to navigate through ?>
						<div class="navigation">
							<div class="alignleft"><?php previous_comments_link() ?></div>
							<div class="alignright"><?php next_comments_link() ?></div>
						</div>
					<?php } ?>
				</ol>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php if ( comments_open() ) : ?>
	<div id="commentsAdd">
		<div id="respond" class="box m-t-6">
			<?php global $aria_req;
				$consent  = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';
				$comments_args = array(
					'title_reply'=>'<h4><span>'.__('Leave a Reply', 'crypto' ).'</span></h4>',
					'comment_notes_before' => '',
					'comment_notes_after' => '',
					'label_submit' => __( 'Post Comment', 'crypto' ),
					'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="1" aria-required="true" placeholder="'.__('Comment Text*', 'crypto' ).'"></textarea></p>',
					'fields' => apply_filters( 'comment_form_default_fields',
						array(
							'author' => '<p class="comment-form-author">'
							.( $req ? '' : '' ).'<input id="author" name="author" type="text" placeholder="'.__('Name*', 'crypto' ).'" value="'.esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
							'email' => '<p class="comment-form-email">'
							.($req ? '' : '' ) . '<input id="email" name="email" type="text" placeholder="'.__('Email*', 'crypto' ).'" value="' . esc_attr(  $commenter['comment_author_email'] ).'" size="30"'.$aria_req.' /></p>',
							'url' => '<p class="comment-form-url"><input id="url" name="url" type="text" placeholder="'.__('Website', 'crypto' ).'" value="' . esc_url( $commenter['comment_author_url'] ) . '" size="30" /></p>',
						) 
					)
				); 
			comment_form($comments_args); ?>
		</div>
	</div>
	<div class="comments-hide"><a href="#"><?php _e('Hide Comments', 'crypto'); ?></a></div>
<?php endif; // if you delete this the sky will fall on your head ?>
