<?php
/**
 * The template for the sidebar
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

?>
<div class="bb-col col-sidebar">
	<aside id="secondary" class="sidebar widget-area">
		<?php
		$boombox_sidebar_id = '';
		$boombox_default_sidebar_id = 'default-sidebar';

		if ( is_page() ) {
			global $post;
			$boombox_sidebar_id = boombox_get_post_meta( $post->ID, 'boombox_primary_sidebar' );
			$boombox_sidebar_id = empty( $boombox_sidebar_id ) ? '' : $boombox_sidebar_id;
		} else if ( is_single() ) {
			$boombox_sidebar_id = 'post-sidebar';
		} else if ( is_archive() ) {
			$boombox_sidebar_id = 'archive-sidebar';
		}

		if ( empty( $boombox_sidebar_id ) || ! is_active_sidebar( $boombox_sidebar_id ) ) {
			$boombox_sidebar_id = $boombox_default_sidebar_id;
		}

		$boombox_sidebar_id = apply_filters( 'boombox/sidebar_id', $boombox_sidebar_id );

		if ( is_active_sidebar( $boombox_sidebar_id ) ) {
			dynamic_sidebar( $boombox_sidebar_id );
		} ?>

	</aside>
</div>
