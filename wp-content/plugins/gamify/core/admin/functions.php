<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'mycred_admin_enqueue', 'gfy_admin_enqueue' );
function gfy_admin_enqueue() {

	if( '-hooks' != substr( get_current_screen()->id, '-6' ) ) {
		return;
	}

	$plugin_data = GFY_Plugin_Management_Service::get_instance()->get_plugin_data( GFY_MAIN_FILE );
	$assets_url = trailingslashit( GFY_ADMIN_URL . 'assets' );
	$version = $plugin_data[ 'Version' ] && ! empty( $plugin_data[ 'Version' ] ) ? $plugin_data[ 'Version' ] : false;

	$ext = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	
	wp_enqueue_style( 'mycred-hook-content-repeater', $assets_url . 'css/repeater' . $ext . '.css', array(), $version );
	wp_enqueue_script( 'mycred-hook-content-repeater', $assets_url . 'js/repeater' . $ext . '.js', array( 'mycred-widgets' ), $version );
}