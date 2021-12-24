<?php
/**
 * Library meta boxes data saver
 *
 * @package "All In One Meta" library
 * @since   1.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! class_exists( 'AIOM_Data_Saver' ) ) {

	final class AIOM_Data_Saver {

		/**
		 * Holds class single instance
		 * @var null
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return AIOM_Data_Saver|null
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Holds data to save
		 * @var array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $data = array();

		/**
		 * Holds array to be saved also as standalone
		 * @var array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $standalone_data = array();

		/**
		 * AIOM_Data_Saver constructor.
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function __construct() {
			$this->hooks();
		}

		/**
		 * Setup Hooks
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function hooks() {
			add_action( 'aiom_save_post', array( $this, 'save_post' ), 9999, 1 );
			add_action( 'aiom_save_term', array( $this, 'save_term' ), 9999, 1 );
			add_action( 'aiom_save_user', array( $this, 'save_user' ), 9999, 1 );
		}

		/**
		 * Add data
		 * @param array $data Data to fill
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function add_data( $data ) {
			$this->data = array_merge( $this->data, (array)$data );
		}

		/**
		 * Add data to standalone stack
		 * @param array $data Fields array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function add_standalone_data( $data ) {
			$this->standalone_data = array_merge( $this->standalone_data, (array)$data );
		}

		/**
		 * Save post meta data
		 * @param int|string $post_id Post ID
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function save_post( $post_id ) {
			update_post_meta( $post_id, AIOM_Config::get_post_meta_key(), $this->data );

			foreach( $this->standalone_data as $meta_key => $meta_value ) {
				update_post_meta( $post_id, $meta_key, $meta_value );
			}

			add_filter( 'redirect_post_location', array( $this, 'maybe_edit_object_redirect_url' ), 99999999, 1 );
		}

		/**
		 * Save term meta data
		 * @param int|string $term_id Term ID
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function save_term( $term_id ) {
			update_term_meta( $term_id, AIOM_Config::get_tax_meta_key(), $this->data );

			foreach( $this->standalone_data as $meta_key => $meta_value ) {
				update_term_meta( $term_id, $meta_key, $meta_value );
			}

			add_filter( 'redirect_term_location', array( $this, 'maybe_edit_object_redirect_url' ), 99999999, 1 );
		}

		/**
		 * Save user meta data
		 * @param int|string $user_id User ID
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function save_user( $user_id ) {
			update_user_meta( $user_id, AIOM_Config::get_user_meta_key(), $this->data );

			foreach( $this->standalone_data as $meta_key => $meta_value ) {
				update_user_meta( $user_id, $meta_key, $meta_value );
			}

			add_filter( 'get_edit_user_link', array( $this, 'maybe_edit_object_redirect_url' ), 99999999, 1 );
		}

		/**
		 * Edit post redirect location and add active tab hash if any
		 * @param string $url     Current location
		 *
		 * @return string
		 */
		public function maybe_edit_object_redirect_url( $url ) {
			if(
				isset( $_POST[ 'has_aiom_data' ] )
				&& $_POST[ 'has_aiom_data' ]
				&& isset( $_POST[ 'aiom_active_tab_hash' ] )
				&& $_POST[ 'aiom_active_tab_hash' ]
			) {
				$url = add_query_arg( 'aiom-tab', $_POST[ 'aiom_active_tab_hash' ], $url );
			}

			return $url;
		}

	}

	AIOM_Data_Saver::get_instance();

}