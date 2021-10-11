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
 * Check whether theme is activated
 * @return bool
 */
function boombox_is_registered() {
	return true;

	$is_registered = get_site_transient( 'boombox_theme_registration' );

	$is_registered = apply_filters( 'boombox/is_theme_registered', $is_registered );

	// Verified, access granted.
	if ( ! ! $is_registered ) {
		return true;
	}

	// Check if required plugin for activation is installed and active
	if ( ! boombox_plugin_management_service()->is_plugin_active( 'envato-market/envato-market.php' ) ) {
		return false;
	}

	$is_registered = false;
	$purchased_themes = envato_market()->api()->themes();

	foreach ( $purchased_themes as $purchased_theme ) {
		if (
			( 'boombox' === strtolower( $purchased_theme[ 'name' ] ) )
			&& ( 'px-lab' === strtolower( $purchased_theme[ 'author' ] ) )
		) {
			$is_registered = true;
			break;
		}
	}

	if ( $is_registered ) {
		$expire = 3 * DAY_IN_SECONDS;
		set_site_transient( 'boombox_theme_registration', true, $expire );
	}

	return $is_registered;
}