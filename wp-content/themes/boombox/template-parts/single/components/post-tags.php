<?php
/**
 * Template part to render single post tags list
 * @since 2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

echo boombox_terms_list_html( array(
	'post_tag' => true,
	'class'    => 'bb-tags mb-md bb-mb-el',
	'wrapper'  => 'div',
	'microdata' => true
) );