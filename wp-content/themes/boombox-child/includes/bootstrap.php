<?php
/**
 * Boombox functions and definitions
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Deprecated methods
 */
require_once ( 'deprecated.php' );

/**
 * Services
 */
require_once( 'services' . DIRECTORY_SEPARATOR . 'bootstrap.php' );

/**
 * Activation
 */
require_once ( 'admin' . DIRECTORY_SEPARATOR . 'activation' . DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR . 'bootstrap.php' );

/**
 * Helpers
 */
require_once( 'helpers' . DIRECTORY_SEPARATOR . 'bootstrap.php' );

/**
 * Functions
 */
require_once( 'functions.php' );

/**
 * Theme Setup
 */
require_once( 'theme-setup.php' );

/**
 * Badges Navigation Walker Class
 */
require_once( 'libs' . DIRECTORY_SEPARATOR . 'bootstrap.php' );

/**
 * Customizer additions
 */
require_once( 'customizer' . DIRECTORY_SEPARATOR . 'bootstrap.php' );

/**
 * Widgets
 */
require_once( 'widgets' . DIRECTORY_SEPARATOR . 'bootstrap.php' );

/**
 * Rate and Vote Restriction Modules
 */
require_once( BOOMBOX_RATE_VOTE_RESTRICTIONS_PATH . 'functions.php' );

/**
 * Plugins support
 */
require_once( 'plugins' . DIRECTORY_SEPARATOR . 'bootstrap.php' );