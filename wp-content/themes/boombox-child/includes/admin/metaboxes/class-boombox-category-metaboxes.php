<?php
/**
 * Register a category meta box using a class.
 * @since 2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! class_exists( 'Boombox_Category_Metabox' ) ) {
	
	/**
	 * Class Boombox_Category_Metabox
	 * @since   2.5.0
	 * @version 2.5.0
	 */
	class Boombox_Category_Metabox {
		
		/**
		 * Holds single instance
		 * @var null
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		private static $_instance = null;
		
		/**
		 * Get single instance
		 * @return Boombox_Category_Metabox
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
		 * Boombox_Category_Metabox constructor.
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
				'id'       => 'bb-category-main-advanced-fields',
				'title'    => esc_html__( 'Boombox Category Advanced Fields', 'boombox' ),
				'taxonomy' => array( 'category' ),
			);
		}
		
		/**
		 * Get structure - Main box
		 * @return array
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		public function get_structure__main_box() {
			$choices_helper = Boombox_Choices_Helper::get_instance();
			$config = static::get_config__main_box();
			
			$structure = array(
				// tab: global
				'tab_global' => array(
					'title'  => esc_html__( 'Global', 'boombox' ),
					'active' => true,
					'icon'   => false,
					'order'  => 20,
					'fields' => array(
						// Category Icon
						'cat_icon_name' => array(
							'type'        => 'icons_dropdown',
							'label'       => esc_html__( 'Badge Icon', 'boombox' ),
							'order'       => 20,
							'default'     => '',
						),
						// Category image icon
						'term_image_icon_id'         => array(
							'type'        => 'image',
							'label'       => esc_html__( 'Custom Badge Icon', 'boombox' ),
							'description' => esc_html__( 'Upload .svg, .png, .jpg with optimal size of 80x80px', 'boombox' ),
							'order'       => 30,
							'default'     => '',
						),
						// Category badge background color
						'term_icon_background_color' => array(
							'type'       => 'color',
							'label'      => esc_html__( 'Badge Background Color', 'boombox' ),
							'standalone' => true,
							'order'      => 40,
							'default'    => boombox_get_theme_option( 'extras_badges_category_background_color' ),
						),
						// Category hide featured area
						'hide_featured_area' => array(
							'type'        => 'checkbox',
							'label'       => esc_html__( 'Hide Featured Area', 'boombox' ),
							'description' => esc_html__( 'Check to hide featured area for this category', 'boombox' ),
							'default'     => 0,
							'order'       => 50,
						),
						// Category hide featured media for attached posts
						'hide_attached_posts_featured_media' => array(
							'type'        => 'checkbox',
							'standalone'  => true,
							'label'       => esc_html__( 'Hide Attached Posts Featured Media', 'boombox' ),
							'description' => esc_html__( 'Check to hide featured media from the single article page of posts - attached to this category', 'boombox' ),
							'default'     => 0,
							'order'       => 60,
						),
						// other fields go here
					),
				),
				// tab: title area
				'tab_title_area' => array(
					'title'  => esc_html__( 'Title Area', 'boombox' ),
					'active' => false,
					'icon'   => false,
					'order'  => 30,
					'fields' => array(
						// Style
						'title_area_style' => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Style', 'boombox' ),
							'choices'         => array_merge( array( 'inherit' => esc_html__( 'Inherit', 'boombox' ), ), $choices_helper->get_template_header_style_choices() ),
							'default'         => 'inherit',
							'order'           => 20,
						),
						// Container Type
						'title_area_background_container' => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Container Type', 'boombox' ),
							'choices'         => array_merge( array( 'inherit' => esc_html__( 'Inherit', 'boombox' ) ), $choices_helper->get_template_header_background_container_choices() ),
							'default'         => 'inherit',
							'order'           => 30,
						),
						// Text Color
						'title_area_text_color' => array(
							'type'     => 'color',
							'label'    => esc_html__( 'Text Color', 'boombox' ),
							'order'    => 40,
							'default'  => '',
						),
						// Background Color
						'title_area_bg_color' => array(
							'type'     => 'color',
							'label'    => esc_html__( 'Background Color', 'boombox' ),
							'order'    => 50,
							'default'  => '',
						),
						// Gradient Color
						'title_area_gradient_color' => array(
							'type'     => 'color',
							'label'    => esc_html__( 'Gradient Color', 'boombox' ),
							'order'    => 60,
							'default'  => '',
						),
						// Gradient Direction
						'title_area_bg_gradient_direction' => array(
							'type'     => 'select',
							'label'    => esc_html__( 'Gradient Direction', 'boombox' ),
							'choices'  => $choices_helper->get_template_header_background_gradient_direction_choices(),
							'default'  => 'top',
							'order'    => 70,
						),
						// Background Image
						'title_area_background_image' => array(
							'type'        => 'image',
							'label'       => esc_html__( 'Background Image', 'boombox' ),
							'order'       => 80,
							'default'     => '',
						),
						// Background Image Size
						'title_area_background_image_size' => array(
							'type'     => 'select',
							'label'    => esc_html__( 'Background Image Size', 'boombox' ),
							'choices'  => $choices_helper->get_template_header_background_image_size_choices(),
							'default'  => 'auto',
							'order'    => 90,
						),
						// Background Image Position
						'title_area_background_image_position' => array(
							'type'     => 'select',
							'label'    => esc_html__( 'Background Image Position', 'boombox' ),
							'choices'  => $choices_helper->get_template_header_background_image_position_choices(),
							'default'  => 'center',
							'order'    => 100,
						),
						// Background Image Repeat
						'title_area_background_image_repeat' => array(
							'type'     => 'select',
							'label'    => esc_html__( 'Background Image Repeat', 'boombox' ),
							'choices'  => $choices_helper->get_template_header_background_image_repeat_choices(),
							'default'  => 'repeat-no',
							'order'    => 110,
						),
						// other fields go here
					)
				),
				// tab: Logo
				'tab_logo' => array(
					'title'  => esc_html__( 'Logo', 'boombox' ),
					'active' => false,
					'icon'   => false,
					'order'  => 60,
					'fields' => array(
						'boombox_term_logo_section_description' => array(
							'type'         => 'custom',
							'html'         => sprintf( '<p>%s</p><hr/>', esc_html__( 'Upload separate logo for this category to overwrite main logo for single posts attached to this category.', 'boombox' ) ),
							'order'        => 20,
							'sub_order'    => 20
						),
						// Logo
						'boombox_term_logo'                     => array(
							'type'         => 'image',
							'standalone'   => true,
							'label'        => esc_html__( 'Logo', 'boombox' ),
							'order'        => 20,
							'sub_order'    => 30,
							'default'      => '',
						),
						// Logo HDPI
						'boombox_term_logo_hdpi'                => array(
							'type'        => 'image',
							'standalone'  => true,
							'label'       => esc_html__( 'Logo HDPI', 'boombox' ),
							'description' => esc_html__( 'An image for High DPI screen (like Retina) should be twice as big.', 'boombox' ),
							'order'       => 30,
							'default'     => '',
						),
						// Logo width
						'boombox_term_logo_width'               => array(
							'type'     => 'number',
							'label'    => esc_html__( 'Logo Width', 'boombox' ),
							'order'    => 40,
							'default'  => ''
						),
						// Logo width
						'boombox_term_logo_height'              => array(
							'type'     => 'number',
							'label'    => esc_html__( 'Logo Height', 'boombox' ),
							'order'    => 50,
							'default'  => ''
						),
						// "Mobile Logo" heading
						'boombox_term_mobile_logo_heading'      => array(
							'type'         => 'custom',
							'html'         => sprintf( '<h2>%s</h2><hr/>', esc_html__( 'Mobile Logo', 'boombox' ) ),
							'order'        => 60,
							'sub_order'    => 20
						),
						// Mobile Logo
						'boombox_term_mobile_logo'              => array(
							'type'         => 'image',
							'standalone'   => true,
							'label'        => esc_html__( 'Logo', 'boombox' ),
							'order'        => 60,
							'sub_order'    => 30,
							'default'      => '',
						),
						// Mobile Logo HDPI
						'boombox_term_mobile_logo_hdpi'         => array(
							'type'        => 'image',
							'standalone'  => true,
							'label'       => esc_html__( 'Logo HDPI', 'boombox' ),
							'description' => esc_html__( 'An image for High DPI screen (like Retina) should be twice as big.', 'boombox' ),
							'order'       => 70,
							'default'     => '',
						),
						// Logo width
						'boombox_term_mobile_logo_width'        => array(
							'type'     => 'number',
							'label'    => esc_html__( 'Logo Width', 'boombox' ),
							'order'    => 80,
							'default'  => ''
						),
						// Logo width
						'boombox_term_mobile_logo_height'       => array(
							'type'     => 'number',
							'label'    => esc_html__( 'Logo Height', 'boombox' ),
							'order'    => 90,
							'default'  => ''
						),
					)
				),
				// other tabs go here
			);
			
			return apply_filters( 'boombox/admin/taxonomy/meta-boxes/structure', $structure, $config[ 'id' ], 'category' );
		}
	}
	
	$instance = Boombox_Category_Metabox::get_instance();
	new AIOM_Taxonomy_Metabox( $instance->get_config__main_box(), array( $instance, 'get_structure__main_box' ) );
	
}