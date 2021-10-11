<?php
/**
 * Template part to render single post sub-title
 * @since 2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

echo boombox_get_post_subtitle( array(
	'classes'           => 'entry-summary entry-sub-title s-post-summary bb-mb-el',
	'microdata'         => true,
	'wrapper'           => 'h2',
	'subtitle'          => Boombox_Template::get_clean( 'subtitle' ),
	'reading_time'      => Boombox_Template::get_clean( 'reading_time' ),
	'reading_time_size' => Boombox_Template::get_clean( 'reading_time_size' )
) );