<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get action assigner class instance
 *
 * @since 2.0.0
 * @return Boombox_Hook_Management_Service|null
 */
function boombox_hook_management_service() {
	return Boombox_Hook_Management_Service::get_instance();
}

/**
 * Get plugin management service instance
 *
 * @since 2.0.0
 * @return Boombox_Plugin_Management_Service|null
 */
function boombox_plugin_management_service() {
	return Boombox_Plugin_Management_Service::get_instance();
}

/**
 * Get module management service instance
 *
 * @since 2.0.0
 * @return Boombox_Module_Management_Service|null
 */
function boombox_module_management_service() {
	return Boombox_Module_Management_Service::get_instance();
}