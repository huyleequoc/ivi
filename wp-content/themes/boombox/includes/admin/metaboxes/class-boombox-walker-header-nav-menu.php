<?php
/**
 * Custom walker class.
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if (  ! class_exists( 'Boombox_Walker_Nav_Menu_Edit' ) ) {

	// requiring the nav-menu.php file on every page load is not so wise
	require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

	class Boombox_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {

		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

			$item_output = '';

			parent::start_el( $item_output, $item, $depth, $args );

			// Inject $new_fields before: <div class="menu-item-actions description-wide submitbox">
			if ( $new_fields = Boombox_Nav_Menu_Item_Custom_Fields::get_field( $item, $depth, $args ) ) {
				$item_output = preg_replace( '/(?=<div[^>]+class="[^"]*submitbox)/', $new_fields, $item_output );
			}
			$output .= $item_output;
		}

	}
}
