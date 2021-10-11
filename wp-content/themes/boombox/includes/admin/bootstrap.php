<?php
/**
 * Boombox admin functions
 *
 * @package BoomBox_Theme
 * @since 1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Functions
 */
require_once( BOOMBOX_ADMIN_PATH . 'functions.php' );

/**
 * Theme Activation
 */
require_once( 'activation' . DIRECTORY_SEPARATOR . 'class-tgm-plugin-activation.php' );
require_once( 'activation' . DIRECTORY_SEPARATOR . 'plugins-activation.php' );
require_once( 'activation' . DIRECTORY_SEPARATOR . 'theme-activation.php' );

/**
 * Meta Boxes
 */
require_once( 'metaboxes' . DIRECTORY_SEPARATOR . 'class-boombox-post-metaboxes.php' );
require_once( 'metaboxes' . DIRECTORY_SEPARATOR . 'class-boombox-page-metaboxes.php' );
require_once( 'metaboxes' . DIRECTORY_SEPARATOR . 'class-boombox-user-metaboxes.php' );
require_once( 'metaboxes' . DIRECTORY_SEPARATOR . 'class-boombox-category-metaboxes.php' );
require_once( 'metaboxes' . DIRECTORY_SEPARATOR . 'class-boombox-tag-metaboxes.php' );
require_once( 'metaboxes' . DIRECTORY_SEPARATOR . 'class-boombox-attachment-metaboxes.php' );

require_once( 'metaboxes' . DIRECTORY_SEPARATOR . 'class-boombox-menu-item-custom-fields.php' );
require_once( 'metaboxes' . DIRECTORY_SEPARATOR . 'class-boombox-walker-header-nav-menu.php' );
require_once( 'metaboxes' . DIRECTORY_SEPARATOR . 'class-menu-metaboxes.php' );

/**
 * Theme Status
 */
require_once( 'status' . DIRECTORY_SEPARATOR . 'bootstrap.php' );

/**
 * Dummy data
 */
require_once( 'dummy-data' . DIRECTORY_SEPARATOR . 'bootstrap.php' );