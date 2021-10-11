<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class GFY_Shortcode_Management_Service {

	/**
	 * Holds class single instance
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get instance
	 * @return GFY_Shortcode_Management_Service|null
	 */
	public static function get_instance() {

		if( null == static::$_instance ) {
			static::$_instance = new GFY_Shortcode_Management_Service();
		}

		return static::$_instance;

	}

	/**
	 * GFY_Shortcode_Management_Service constructor.
	 */
	private function __construct() {}

	/**
	 * A dummy magic method to prevent GFY_Shortcode_Management_Service from being cloned.
	 *
	 */
	public function __clone() {
		throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
	}

	/**
	 * Do shortcode wrapper
	 * @param string $shortcode Shortcode name
	 * @param array $attributes Shortcode attributes
	 * @return string
	 */
	public function do_shortcode( $shortcode, $attributes = array() ) {
		foreach( $attributes as $attr => $value ) {
			$attributes[ $attr ] = sprintf( '%s="%s"', $attr, $value );
		}
		$attributes = implode( ' ', $attributes );

		return do_shortcode( sprintf( '[%s %s]', $shortcode, $attributes ) );
	}

}