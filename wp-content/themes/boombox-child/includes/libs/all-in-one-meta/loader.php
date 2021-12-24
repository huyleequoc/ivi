<?php
/**
 * Library loader
 *
 * @package "All In One Meta" library
 * @since   1.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Define lib path
 */
if( ! defined( 'AIOM_PATH' ) ) {
	define( 'AIOM_PATH', trailingslashit( __DIR__ ) );
}

if( ! defined( 'AIOM_URL' ) ) {
	define( 'AIOM_URL', str_replace( wp_normalize_path( untrailingslashit( ABSPATH ) ), site_url(), wp_normalize_path( AIOM_PATH ) ) );
}

/**
 * Configuration
 */
require_once 'core/classes/class-aiom-config.php';

/**
 * Hooks
 */
require_once 'core/classes/class-aiom-hooks.php';

/**
 * Saver
 */
require_once 'core/classes/class-aiom-data-saver.php';

/**
 * Base post class
 */
require_once 'core/classes/class-aiom-post.php';

/**
 * Base taxonomy class
 */
require_once 'core/classes/class-aiom-taxonomy.php';

/**
 * Base user class
 */
require_once 'core/classes/class-aiom-user.php';

/**
 * Public functions
 */
require_once 'core/functions.php';