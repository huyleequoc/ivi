<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$plugin_management_service = GFY_Plugin_Management_Service::get_instance();
if( ! $plugin_management_service->is_plugin_active( 'mycred/mycred.php' ) ) {
	return;
}

require_once GFY_CORE_DIR . 'classes/shortcodes/class-shortcode-leaderboard.php';
GFY_Shortcode_Leaderboard::get_instance();