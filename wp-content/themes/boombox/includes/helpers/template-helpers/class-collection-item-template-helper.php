<?php
/**
 * Boombox Featured Area Template Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Collection_Item_Template_Helper' ) ) {

	final class Boombox_Collection_Item_Template_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Collection_Item_Template_Helper|null
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
		 * Boombox_Collection_Item_Template_Helper constructor.
		 */
		private function __construct() {
		}

		/**
		 * A dummy magic method to prevent Boombox_Collection_Item_Template_Helper from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Get options
		 * @return array
		 */
		public function get_options() {
			$choices = Boombox_Choices_Helper::get_instance()->get_grid_hide_elements();

			if ( is_home() ) {

				$key = 'home_grid_elements';
				$template_options = boombox_cache_get( $key );

				if ( ! $template_options ) {
					$template_options = array();
					$hide_elements = (array)boombox_get_theme_option( 'home_main_posts_hide_elements' );

					foreach ( $choices as $name => $element ) {
						$template_options[ $name ] = ! in_array( $name, $hide_elements );
					}
					$template_options[ 'reading_time' ] = in_array( 'home', boombox_get_theme_option( 'extras_reading_time_visibility' ) );

					boombox_cache_set( $key, $template_options );
				}

				$template_options = apply_filters( 'boombox_home_grid_show_elements', $template_options );

			} else if ( is_page() ) {
				// in case of the listing, we need to take data from the original page stores in wp_the_query;
				global $wp_the_query;
				$page_id = $wp_the_query->queried_object_id;
				$key = 'page_' . $page_id . '_grid_elements';
				$template_options = boombox_cache_get( $key );

				if ( ! $template_options ) {
					$template_options = array();
					$hide_elements = (array)boombox_get_post_meta( $wp_the_query->queried_object_id, 'boombox_listing_hide_elements' );

					foreach ( $choices as $name => $element ) {
						$template_options[ $name ] = ! in_array( $name, $hide_elements );
					}
					$template_options[ 'reading_time' ] = in_array( 'page', boombox_get_theme_option( 'extras_reading_time_visibility' ) );

					boombox_cache_set( $key, $template_options );
				}

				$template_options = apply_filters( 'boombox_page_grid_show_elements', $template_options );

			} else if ( is_archive() ) {
				$key = 'archive_grid_elements';
				$template_options = boombox_cache_get( $key );

				if ( ! $template_options ) {
					$template_options = array();
					$hide_elements = (array)boombox_get_theme_option( 'archive_main_posts_hide_elements' );

					foreach ( $choices as $name => $element ) {
						$template_options[ $name ] = ! in_array( $name, $hide_elements );
					}

					$template_options[ 'reading_time' ] = in_array( 'archive', boombox_get_theme_option( 'extras_reading_time_visibility' ) );

					boombox_cache_set( $key, $template_options );
				}

				$template_options = apply_filters( 'boombox_archive_grid_show_elements', $template_options );

			} else if ( is_singular() ) {

				$key = 'single_post_grid_elements';
				$template_options = boombox_cache_get( $key );

				if ( ! $template_options ) {
					$template_options = array();
					$hide_elements = (array)boombox_get_theme_option( 'single_post_related_posts_grid_sections_hide_elements' );

					foreach ( $choices as $name => $element ) {
						$template_options[ $name ] = ! in_array( $name, $hide_elements );
					}

					boombox_cache_set( $key, $template_options );
				}

				$template_options = apply_filters( 'boombox_single_grid_show_elements', $template_options );

			} else if ( is_search() ) {
				$key = 'search_grid_elements';
				$template_options = boombox_cache_get( $key );

				if ( ! $template_options ) {
					$template_options = array();
					$hide_elements = boombox_get_theme_option( 'archive_main_posts_hide_elements' );

					foreach ( $choices as $name => $element ) {
						$template_options[ $name ] = ! in_array( $name, $hide_elements );
					}

					boombox_cache_set( $key, $template_options );
				}

				$template_options = apply_filters( 'boombox_search_grid_show_elements', $template_options );
			} else {
				$template_options = array_fill_keys( array_keys( $choices ), false );
			}

			// extra features
			$template_options = wp_parse_args( $template_options, array( 'reading_time' => false ) );

			return apply_filters( 'boombox_grid_template_options', $template_options );
		}

	}

}