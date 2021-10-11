<?php
/**
 * Template part to render single post author mini card
 * @since   2.5.0
 * @version 2.5.0
 * @var $helper Boombox_Single_Post_Template_Helper Template helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$html = boombox_generate_user_mini_card( array(
	'author'    => Boombox_Template::get_clean( 'author' ),
	'avatar'    => Boombox_Template::get_clean( 'avatar' ),
	'date'      => Boombox_Template::get_clean( 'date' ),
	'class'     => 'size-md',
	'microdata' => true,
) );

if ( $html ) {
	echo Boombox_Template::get_clean( 'before' ) . $html . Boombox_Template::get_clean( 'after' );
}