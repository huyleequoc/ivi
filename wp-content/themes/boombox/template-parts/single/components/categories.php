<?php
/**
 * Template part to render single post categories list
 * @since 2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$html = boombox_terms_list_html( array(
	'category'  => true,
	'wrapper'   => 'p',
	'class'     => 'bb-cat-links size-lg s-post-cat-links',
	'microdata' => true,
) );
if( $html ) {
	echo Boombox_Template::get_clean( 'before' ) . $html . Boombox_Template::get_clean( 'after' );
}