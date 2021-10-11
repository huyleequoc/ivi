<?php
/**
 * The template part for displaying the "Post Create" button
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.1.3
 *
 * @var $header_helper Boombox_Header_Template_Helper Header Template Helper
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! boombox_is_auth_allowed() ) {
	return;
}

$header_helper = Boombox_Template::init( 'header' );
echo boombox_get_create_post_button(
	array(
		'header-item',
		'create-post',
		'pos-' . $header_helper->get_component_location(),
	),
	esc_html__( boombox_get_theme_option( 'header_layout_button_text' ), 'boombox' ),
	boombox_get_theme_option( 'header_layout_button_plus_icon' ),
	boombox_get_theme_option( 'header_layout_button_link' )
); ?>