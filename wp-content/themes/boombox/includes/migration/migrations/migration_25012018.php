<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class migration_25012018 {

	/**
	 * Update menu locations
	 * @return true
	 */
	private static function update_menu_locations() {

		$need_update = false;

		/**** get existing menu locations */
		$menu_locations = get_nav_menu_locations();

		/**** copy "Top Header Menu" => "Burger Menu 1" */
		if( isset( $menu_locations[ 'top_header_nav' ] ) && ! isset( $menu_locations[ 'burger_mobile_menu_1' ] ) ) {
			$menu_locations[ 'burger_mobile_menu_1' ] = $menu_locations[ 'top_header_nav' ];
			$need_update = true;
		}

		/**** copy "Bottom Header Menu" => "Burger Menu 2" */
		if( isset( $menu_locations[ 'bottom_header_nav' ] ) && ! isset( $menu_locations[ 'burger_mobile_menu_2' ] ) ) {
			$menu_locations[ 'burger_mobile_menu_2' ] = $menu_locations[ 'bottom_header_nav' ];
			$need_update = true;
		}

		/**** copy "More Button Bottom Menu" => "Burger Menu 3" */
		if( isset( $menu_locations[ 'burger_bottom_nav' ] ) && ! isset( $menu_locations[ 'burger_mobile_menu_3' ] ) ) {
			$menu_locations[ 'burger_mobile_menu_3' ] = $menu_locations[ 'burger_bottom_nav' ];
			$need_update = true;
		}

		/**** set updated menu locations */
		if( $need_update ) {
			set_theme_mod( 'nav_menu_locations', $menu_locations );
		}

		return true;
	}

	/**
	 * Organize migration tasks
	 * @return bool
	 */
	public static function up() {
		return self::update_menu_locations();
	}

}