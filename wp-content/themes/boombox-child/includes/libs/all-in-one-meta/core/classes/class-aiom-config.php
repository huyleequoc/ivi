<?php
/**
 * Library configuration
 *
 * @package "All In One Meta" library
 * @since   1.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'AIOM_Config' ) ) {

	/**
	 * Class AIOM_Config
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Config {

		/**
		 * Holds meta key name for post
		 * @var string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private static $post_meta_key = 'aiom';

		/**
		 * Holds meta key name for taxonomy
		 * @var string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private static $tax_meta_key = 'aiom';
		
		/**
		 * Holds meta key name for user
		 * @var string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private static $user_meta_key = 'aiom';

		/**
		 * Setup configuration
		 *
		 * @param array $args         Configuration array
		 * @throws Exception
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function setup( $args ) {
			$args = wp_parse_args( $args, array(
				'post_meta_key' => 'aiom',
				'tax_meta_key'  => 'aiom',
				'user_meta_key' => 'aiom',
			) );

			if ( ! $args[ 'post_meta_key' ] ) {
				throw new Exception( __( 'post_meta_key is required', 'aiom' ) );
			}

			if ( ! $args[ 'tax_meta_key' ] ) {
				throw new Exception( __( 'tax_meta_key is required', 'aiom' ) );
			}
			
			if ( ! $args[ 'user_meta_key' ] ) {
				throw new Exception( __( 'user_meta_key is required', 'aiom' ) );
			}

			static::$post_meta_key = $args[ 'post_meta_key' ];
			static::$tax_meta_key = $args[ 'tax_meta_key' ];
			static::$user_meta_key = $args[ 'user_meta_key' ];

		}

		/**
		 * Get post meta key
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function get_post_meta_key() {
			return static::$post_meta_key;
		}

		/**
		 * Get taxonomy meta key
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function get_tax_meta_key() {
			return static::$tax_meta_key;
		}
		
		/**
		 * Get user meta key
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function get_user_meta_key() {
			return static::$user_meta_key;
		}

	}

}