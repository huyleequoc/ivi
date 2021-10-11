<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$plugin_management_service = GFY_Plugin_Management_Service::get_instance();
if( ! $plugin_management_service->is_plugin_active( 'mycred/mycred.php' ) ) {
	return;
}

/**
 * "Leaderboard" widget
 */
require_once GFY_CORE_DIR . 'classes/widgets/class-leaderboard-widget.php';

/**
 * "Featured Author" widget
 */
require_once GFY_CORE_DIR . 'classes/widgets/class-featured-author-widget.php';
