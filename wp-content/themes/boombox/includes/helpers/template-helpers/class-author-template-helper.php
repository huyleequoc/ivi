<?php
/**
 * Boombox Author Template Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Author_Template_Helper' ) ) {

	final class Boombox_Author_Template_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Author_Template_Helper|null
		 */
		public static function get_instance() {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Holds additional data
		 * @var array
		 */
		private $data = array();

		/**
		 * Setter
		 *
		 * @param string $name  Variable key
		 * @param mixed  $value Variable value
		 */
		public function __set( $name, $value ) {
			$this->data[ $name ] = $value;
		}

		/**
		 * Getter
		 *
		 * @param string $name Variable key
		 *
		 * @return mixed Variable value if it exists or null otherwise
		 */
		public function __get( $name ) {
			if ( array_key_exists( $name, $this->data ) ) {
				return $this->data[ $name ];
			}

			return null;
		}

		/**
		 * Boombox_Author_Template_Helper constructor.
		 */
		private function __construct() {
		}

		/**
		 * A dummy magic method to prevent Boombox_Author_Template_Helper from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Get template options
		 * @return array
		 */
		public function get_options() {

			$sidebar_type = boombox_get_theme_option( 'archive_main_posts_sidebar_type' );
			$template_settings = array(
				'listing_type'             => boombox_get_theme_option( 'archive_main_posts_listing_type' ),
				'pagination_type'          => boombox_get_theme_option( 'archive_main_posts_pagination_type' ),
				'enable_primary_sidebar'   => boombox_is_primary_sidebar_enabled( $sidebar_type ),
				'enable_secondary_sidebar' => boombox_is_secondary_sidebar_enabled( $sidebar_type ),
			);

			return apply_filters( 'boombox/author_template_settings', $template_settings );
		}

	}

}