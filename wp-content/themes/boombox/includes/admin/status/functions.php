<?php
/**
 * Boombox theme status bootstrapping
 *
 * @package BoomBox_Theme
 * @since 2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Class Boombox Theme Status
 */
require_once ( 'class-theme-status.php' );

/**
 * Initialize theme status checker
 */
Boombox_Theme_Status::get_instance();