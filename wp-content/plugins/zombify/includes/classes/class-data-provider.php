<?php
/**
 * Zombify Data Provider
 *
 * @package Zombify
 * @since   1.4.3
 * @version 1.4.3
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'ZF_Data_Provider' ) ) {

	final class ZF_Data_Provider {

		/**
		 * Holds template data
		 * @var array
		 */
		private static $data = array();

		/**
		 * Set template data
		 *
		 * @param string $key  Data key
		 * @param mixed $value Data value
		 */
		public static function set( $key, $value ) {
			static::$data[ $key ] = $value;
		}

		/**
		 * Get template data
		 *
		 * @param string $key     Data Key
		 * @param string $default Value to return if data key does not exists
		 *
		 * @return mixed|null
		 */
		public static function get( $key, $default = null ) {
			return isset( static::$data[ $key ] ) ? static::$data[ $key ] : $default;
		}

		/**
		 * Reset template data
		 *
		 * @param string $key Data key
		 */
		public static function reset( $key ) {
			if ( isset( static::$data[ $key ] ) ) {
				unset( static::$data[ $key ] );
			}
		}

		/**
		 * Get & clean data
		 *
		 * @param string $key   Data Key
		 * @param null $default Value to return if data key does not exists
		 *
		 * @return mixed
		 */
		public static function get_clean( $key, $default = null ) {
			$value = static::get( $key, $default );
			static::reset( $key );

			return $value;
		}

	}

}