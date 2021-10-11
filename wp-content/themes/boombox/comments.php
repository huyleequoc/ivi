<?php
/**
 * The template for displaying comments
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */

if ( post_password_required() ) {
	return;
} ?>

<div id="comments" class="comments">

	<h2 class="comments-title">
		<?php
		$comments_number = get_comments_number();
		printf(
		/* translators: 1: number of comments, 2: post title */
			_nx(
				'One Comment',
				'%s Comments',
				$comments_number,
				'comments title',
				'boombox'
			),
			number_format_i18n( $comments_number )
		); ?>
	</h2>

	<?php comment_form( boombox_get_comment_form_args() );
	if ( have_comments() ) {
		the_comments_navigation(); ?>

		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 42,
			) );
			?>
		</ol>

		<?php the_comments_navigation();
	}

	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) {
		printf( '<p class="no-comments">%s</p>', esc_html__( 'Comments are closed.', 'boombox' ) );
	} ?>

</div>