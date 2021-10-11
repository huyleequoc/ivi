<?php
/**
 * The template part for displaying the site header pattern
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
<div class="pattern">
	<?php
	$header_pattern_type = boombox_get_theme_option( 'header_design_pattern_type' );
	$header_pattern_path = BOOMBOX_THEME_PATH . '/images/svg/header/' . $header_pattern_type;
	if ( is_file( $header_pattern_path ) ) {
		echo @file_get_contents( $header_pattern_path );
	}
	?>
</div>