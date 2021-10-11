<?php
/**
 * Template part to render the attached file
 *
 * @since   2.6.9
 * @version 2.6.9
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$image_size = Boombox_Template::get_clean( 'image_size' );

?>

<div class="entry-attachment">

	<?php
	$attached_file_type = get_post_mime_type();
	$attachment_url     = wp_get_attachment_url();

	if ( wp_attachment_is( 'image' ) ) {
		echo wp_get_attachment_image( get_the_ID(), $image_size );
	} elseif ( wp_attachment_is( 'video' ) ) { ?>

		<video controls>
			<source src="<?php echo esc_url( $attachment_url ); ?>" type="<?php esc_attr_e( $attached_file_type ); ?>">
			<?php esc_html_e( 'Your browser does not support HTML video.', 'boombox' ); ?>
		</video>

	<?php } elseif ( wp_attachment_is( 'audio' ) ) { ?>

		<audio controls>
			<source src="<?php echo esc_url( $attachment_url ); ?>" type="<?php esc_attr_e( $attached_file_type ); ?>">
			<?php esc_html_e( 'Your browser does not support the audio element.', 'boombox' ); ?>
		</audio>

	<?php } else { ?>

		<a href="<?php echo esc_url( $attachment_url ); ?>" title="<?php esc_attr_e( get_the_title() ) ?>">
			<?php echo basename( esc_url( $attachment_url ) ); ?>
		</a>

	<?php }

	if ( has_excerpt() ) { ?>

		<div class="entry-caption">
			<?php the_excerpt(); ?>
		</div><!-- .entry-caption -->

	<?php } ?>

</div><!-- .entry-attachment -->