<?php
/**
 * Register a post_tag meta box using a class.
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

// do nothing if base meta box class does not exists
if( ! class_exists( 'AIOM_Taxonomy_Metabox' ) ) {
	return;
}

if( ! class_exists( 'Boombox_Brand_Metabox' ) ) {
	
	/**
	 * Class Boombox_Brand_Metabox
	 * @since   2.5.0
	 * @version 2.5.0
	 */
	class Boombox_Brand_Metabox {
		
		/**
		 * Holds single instance
		 * @var null
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		private static $_instance = null;
		
		/**
		 * Get single instance
		 * @return Boombox_Brand_Metabox
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
		 * Boombox_Brand_Metabox constructor.
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		private function __construct() {
			$this->hooks();
		}
		
		/**
		 * Add hooks
		 * @since   2.5.0
		 * @version 2.5.0
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
				'id'       => 'bb-brand-main-advanced-fields',
				'title'    => esc_html__( 'Boombox Brand Advanced Fields', 'boombox-theme-extensions' ),
				'taxonomy' => array( 'brand' ),
				'context'  => 'normal',
				'priority' => 'high',
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
				// global tab
				'tab_global' => array(
					'title'  => esc_html__( 'Global', 'boombox-theme-extensions' ),
					'active' => true,
					'icon'   => false,
					'order'  => 20,
					'fields' => array(
						// Brand URL
						'brand_url' => array(
							'type'        => 'url',
							'name'        => 'brand_url',
							'label'       => esc_html__( 'Brand URL', 'boombox-theme-extensions' ),
							'order'       => 20,
							'default'     => '',
						),
						// Brand Logo
						'brand_logo_id' => array(
							'type'        => 'image',
							'name'        => 'brand_logo_id',
							'label'       => esc_html__( 'Brand Logo', 'boombox-theme-extensions' ),
							'order'       => 30,
							'default'     => '',
						),
						'brand_logo_width' => array(
							'type'        => 'number',
							'name'        => 'brand_logo_width',
							'label'       => esc_html__( 'Brand Logo Width (px)', 'boombox-theme-extensions' ),
							'order'       => 40,
							'default'     => '',
							'attributes'  => array(
								'min' => 0,
								'step' => 1
							)
						),
						'brand_logo_height' => array(
							'type'        => 'number',
							'name'        => 'brand_logo_height',
							'label'       => esc_html__( 'Brand Logo Height (px)', 'boombox-theme-extensions' ),
							'order'       => 50,
							'default'     => '',
							'attributes'  => array(
								'min' => 0,
								'step' => 1
							)
						),
						'brand_logo_hdpi_id' => array(
							'type'        => 'image',
							'name'        => 'brand_logo_hdpi_id',
							'label'       => esc_html__( 'Brand Logo HDPI', 'boombox-theme-extensions' ),
							'description' => __( 'An image for High DPI screen (like Retina) should be twice as big.', 'boombox-theme-extensions' ),
							'order'       => 60,
							'default'     => '',
						),
					)
				)
				// other tabs go here
			);
			
			return apply_filters( 'boombox/admin/taxonomy/meta-boxes/structure', $structure, $config[ 'id' ], 'brand' );
		}
	}
	
	$instance = Boombox_Brand_Metabox::get_instance();
	new AIOM_Taxonomy_Metabox( $instance->get_config__main_box(), array( $instance, 'get_structure__main_box' ) );
	
}