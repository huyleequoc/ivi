<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! trait_exists( 'Boombox_Vote_Restriction_Trait' ) ) {
	/**
	 * Trait Boombox_Vote_Restriction_Trait
	 */
	trait Boombox_Vote_Restriction_Trait {
		/**
		 * @var string
		 */
		protected static $restriction_name;

		/**
		 * @return string
		 */
		public static function get_restriction_name() {
			return static::$restriction_name;
		}

		/**
		 * @return bool
		 */
		public static function is_restriction_enabled() {
			return Boombox_Vote_Restriction::restriction_exists( static::get_restriction_name() );
		}

		/**
		 * @var string Holds DB table name prefix excluded
		 */
		protected static $table_name;

		/**
		 * @return string Holds DB table name prefix included
		 */
		public static function get_table_name() {
			global $wpdb;

			return $wpdb->prefix . static::$table_name;
		}
	}
}