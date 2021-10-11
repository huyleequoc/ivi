<?php
/**
 * Boombox Featured Labels Template Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Featured_Labels_Template_Helper' ) ) {

	final class Boombox_Featured_Labels_Template_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return Boombox_Featured_Labels_Template_Helper|null
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
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
		 * Boombox_Featured_Labels_Template_Helper constructor.
		 */
		private function __construct() {
		}

		/**
		 * A dummy magic method to prevent Boombox_Featured_Labels_Template_Helper from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Get design options
		 * @return array
		 */
		public function get_designs_options() {

			$set = boombox_get_theme_options_set( array(
				'header_featured_labels_background_color',
				'header_featured_labels_text_color',
				'header_featured_labels_border_radius',
			) );

			if( ! $set[ 'header_featured_labels_background_color' ] ) {
				$set[ 'header_featured_labels_background_color' ] = 'transparent';
			}
			$design_options = array(
				'bg_clr'        => $set[ 'header_featured_labels_background_color' ],
				'text_clr'      => $set[ 'header_featured_labels_text_color' ],
				'border_radius' => $set[ 'header_featured_labels_border_radius' ],
			);

			return apply_filters( 'boombox/featured_labels_design_options', $design_options );
		}

		/**
		 * Get template options
		 * @return array
		 */
		public function get_options() {

			$is_visible = false;
			$class = '';

			$set = boombox_get_theme_options_set( array(
				'header_featured_labels_visibility',
				'header_featured_labels_disable_separator',
			) );

			// index template
			if ( is_home() ) {
				$is_visible = in_array( 'home', $set[ 'header_featured_labels_visibility' ] );
			} // archive template
			else if ( is_archive() ) {
				$is_visible = in_array( 'archive', $set[ 'header_featured_labels_visibility' ] );
			} // single post template
			else if ( is_single() ) {
				$is_visible = in_array( 'single_post', $set[ 'header_featured_labels_visibility' ] );
			} // page template
			else if ( is_page() ) {
				$is_visible = in_array( 'page', $set[ 'header_featured_labels_visibility' ] );
			}

			if ( $is_visible ) {
				if ( $set[ 'header_featured_labels_disable_separator' ] ) {
					$class = ' no-line';
				}
			}

			$template_settings = array(
				'is_visible' => $is_visible,
				'class'      => $class,
			);

			return apply_filters( 'boombox/featured_labels_template_settings', $template_settings );

		}

	}

}