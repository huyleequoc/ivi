<?php
/**
 * Register a user meta box using a class.
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! class_exists( 'Boombox_User_Metabox' ) ) {
	
	/**
	 * Class Boombox_User_Metabox
	 * @since   2.5.0
	 * @version 2.5.0
	 */
	class Boombox_User_Metabox {
		
		/**
		 * Holds single instance
		 * @var null
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		private static $_instance = null;
		
		/**
		 * Get single instance
		 * @return Boombox_User_Metabox
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		public static function get_instance() {
			if( null === static::$_instance ) {
				static::$_instance = new static();
			}
			
			return static::$_instance;
		}
		
		/**
		 * Boombox_User_Metabox constructor.
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		private function __construct() {
			$this->hooks();
		}
		
		/**
		 * Add hooks
		 */
		private function hooks() {
		}
		
		/**
		 * Get configuration - Main box
		 * @return array
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		public function get_config__main_box() {
			return array(
				'id'        => 'bb-user-main-advanced-fields',
				'title'     => esc_html__( 'Boombox User Advanced Fields', 'boombox' ),
				'role'      => array( '*' ),
			);
		}
		
		/**
		 * Get structure - Main box
		 * @return array
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		public function get_structure__main_box() {
			
			$config = static::get_config__main_box();
			$structure = array(
				// main tab
				'tab_main'      => array(
					'title'  => esc_html__( 'Main', 'boombox' ),
					'active' => true,
					'icon'   => false,
					'order'  => 20,
					'fields' => array(
						// other fields go here
					),
				),
				// other tabs go here
			);
			
			return apply_filters( 'boombox/admin/user/meta-boxes/structure', $structure, $config[ 'id' ] );
		}
		
	}
	
	$instance = Boombox_User_Metabox::get_instance();
	new AIOM_User_Metabox( $instance->get_config__main_box(), array( $instance, 'get_structure__main_box' ) );
	
}