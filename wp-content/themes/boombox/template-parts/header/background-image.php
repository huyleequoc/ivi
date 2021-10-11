<?php
/**
 * The template part for displaying the site background image
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$image = boombox_get_theme_option( 'design_body_background_image' );
if ( $image ) {
	$url = boombox_get_theme_option( 'design_body_background_link' ); ?>
	<div id="background-image" class="background-image" style="background-image: url('<?php echo $image; ?>')">
		<?php if ( $url ) { ?>
			<a class="link" href="<?php echo $url; ?>" target="_blank" rel="noopener"></a>
		<?php } ?>
	</div>
	<?php
}