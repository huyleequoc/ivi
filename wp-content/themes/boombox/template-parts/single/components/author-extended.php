<?php
/**
 * The template part for displaying single post "Expanded Author" section.
 *
 * @since   2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

echo boombox_get_post_author_card( array(
	'class' => Boombox_Template::get_clean( 'class' )
) );