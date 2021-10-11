<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! defined( 'BOOMBOX_CHILD_THEME_PATH' ) ) {
	define( 'BOOMBOX_CHILD_THEME_PATH', trailingslashit( get_stylesheet_directory() ) );
}
if ( ! defined( 'BOOMBOX_CHILD_THEME_URL' ) ) {
	define( 'BOOMBOX_CHILD_THEME_URL', trailingslashit( get_stylesheet_directory_uri() ) );
}

add_action( 'wp_enqueue_scripts', 'boombox_child_enqueue_styles', 100 );
function boombox_child_enqueue_styles() {
	wp_enqueue_style( 'boombox-child-style', get_stylesheet_uri(), array(), boombox_get_assets_version() );
}