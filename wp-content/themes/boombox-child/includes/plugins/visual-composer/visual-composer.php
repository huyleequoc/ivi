<?php

/**
 * "Visual Composer" plugin functions
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! boombox_plugin_management_service()->is_plugin_active( 'js_composer/js_composer.php' ) ) {
	return;
}

if ( ! class_exists( 'Boombox_VC' ) ) {

	final class Boombox_VC {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return Boombox_VC|null
		 */
		public static function get_instance() {

			if (null == static::$_instance) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Holds elements path
		 * @var string
		 */
		private $elements_path = 'elements';

		/**
		 * Holds params path
		 * @var string
		 */
		private $params_path = 'params';

		/**
		 * Boombox_VC constructor.
		 */
		private function __construct() {
			$this->includes();
			$this->hooks();

			do_action( 'boombox/vc/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_VC from being cloned.
		 */
		public function __clone() {
			throw new Exception('Cloning ' . __CLASS__ . ' is forbidden');
		}

		/**
		 * Include required files
		 */
		private function includes() {

			/**
			 * Params
			 */
			require_once $this->params_path . DIRECTORY_SEPARATOR . 'number.php';

			/**
			 * Elements
			 */
			require_once $this->elements_path . DIRECTORY_SEPARATOR . 'listing.php';
		}

		/**
		 * Setup Hooks
		 */
		public function hooks() {
			add_filter( 'vc_autocomplete_boombox_listing_category_callback', array( $this, 'get_term_autocomplete_choices' ), 10, 3 );
			add_filter( 'vc_autocomplete_boombox_listing_post_tag_callback', array( $this, 'get_term_autocomplete_choices' ), 10, 3 );
		}

		/**
		 * Get choices for term autocomplete
		 *
		 * @param string $search_string     Searched keyword
		 * @param string $shortcode_name    Shortcode name
		 * @param string $taxonomy          Taxonomy name
		 *
		 * @return array
		 */
		public function get_term_autocomplete_choices( $search_string, $shortcode_name, $taxonomy ) {

			$data = array();

			$vc_taxonomies = get_terms( $taxonomy, array(
				'hide_empty' => false,
				'search' => $search_string,
			) );

			if ( is_array( $vc_taxonomies ) && ! empty( $vc_taxonomies ) ) {
				foreach ( $vc_taxonomies as $t ) {
					if ( is_object( $t ) ) {
						$data[] = array(
							'label' => $t->name,
							'value' => $t->slug,
						);
					}
				}
			}

			return $data;
		}

	}

	Boombox_VC::get_instance();

}