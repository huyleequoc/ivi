<?php
/**
 * The template part for displaying ad before footer
 *
 * @package BoomBox_Theme
 * @since   2.0.4
 * @version 2.0.4
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$location = false;

if( is_home() ) {
	$location = 'boombox-index-before-footer';
} else if ( is_archive() ) {
	$location = 'boombox-archive-before-footer';
}  else if ( is_page() ) {
	$location = 'boombox-page-before-footer';
} else if ( is_single() ) {
	$location = 'boombox-single-before-footer';
}

if ( $location ) {
	boombox_the_advertisement( $location, array( 'class' => 'container large bb-before-footer' ) );
}