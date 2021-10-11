<?php
/**
 * The template part for displaying ad before header
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.4
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$location = false;

if( is_home() ) {
	$location = 'boombox-index-before-header';
} else if ( is_archive() ) {
	$location = 'boombox-archive-before-header';
}  else if ( is_page() ) {
	$location = 'boombox-page-before-header';
} else if ( is_single() ) {
	$location = 'boombox-single-before-header';
}

if ( $location ) {
	boombox_the_advertisement( $location, array( 'class' => 'large bb-before-header' ) );
}