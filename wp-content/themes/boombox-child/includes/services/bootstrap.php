<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Hook_Management_Service
 */
require_once( 'lib' . DIRECTORY_SEPARATOR . 'class-boombox-hook-management-service.php' );

/**
 * Plugin Management Service
 */
require_once( 'lib' . DIRECTORY_SEPARATOR . 'class-boombox-plugin-management-service.php' );

/**
 * Module Management Service
 */
require_once( 'lib' . DIRECTORY_SEPARATOR . 'class-boombox-module-management-service.php' );

/**
 * Functions
 */
require_once( 'functions.php' );