<?php
/**
 * Gif To Video
 *
 * @package BoomBox_Theme_Extensions
 */

// Prevent direct script access
if ( ! defined( 'ABSPATH' ) ) {
    die ( 'No direct script access allowed' );
}

if ( ! defined( 'BBTE_GIF_TO_VIDEO_PATH' ) ) {
    define( 'BBTE_GIF_TO_VIDEO_PATH', BBTE_PLUGIN_PATH . '/boombox-gif-to-video/' );
}
if ( ! defined( 'BBTE_GIF_TO_VIDEO_URL' ) ) {
    define( 'BBTE_GIF_TO_VIDEO_URL', BBTE_PLUGIN_URL . '/boombox-gif-to-video/' );
}

/**
 * Include Lib
 */
include_once( BBTE_GIF_TO_VIDEO_PATH . 'lib/vendor/autoload.php' );

/**
 * Add Main Class
 */
include_once( BBTE_GIF_TO_VIDEO_PATH . 'class-gif-to-video.php' );