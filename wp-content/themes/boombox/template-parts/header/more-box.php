<?php
/**
 * The template part for displaying the site header more box
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>

<div class="more-menu-item">
	<a id="more-menu-toggle" class="toggle" href="#">
		<i class="toggle-icon bb-ui-icon-bars"></i>
	</a>
	<?php boombox_get_template_part( 'template-parts/header/navigation/more' ); ?>
</div>