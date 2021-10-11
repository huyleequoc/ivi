<?php
/**
 * The template for the secondary sidebar
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.1.2
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>

<div class="bb-col col-sidebar-secondary" xmlns="http://www.w3.org/1999/html">
	<aside id="secondary-small" class="sidebar-secondary widget-area">
		<?php
		$secondary_sidebar_id = 'page-secondary';
		if( is_page() ) {
			$secondary_sidebar_id = boombox_get_post_meta( get_the_ID(), 'boombox_secondary_sidebar' );
		}
		$secondary_sidebar_id = apply_filters( 'boombox/secondary-sidebar-id', $secondary_sidebar_id );

		if ( is_active_sidebar( $secondary_sidebar_id ) ) {
			dynamic_sidebar( $secondary_sidebar_id );
		}
		?>
	</aside>
</div>