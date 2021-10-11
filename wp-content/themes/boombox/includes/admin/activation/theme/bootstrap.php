<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Theme options
 */
require_once( 'functions.php' );

/**
 * Theme options
 */
require_once( 'class-theme-options.php' );

/**
 * Registration options
 */
require_once( 'class-theme-options-registration.php' );

/**
 * Init
 */
Boombox_Theme_Options::get_instance();