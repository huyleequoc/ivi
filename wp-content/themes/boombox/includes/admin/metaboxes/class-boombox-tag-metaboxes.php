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

if( ! class_exists( 'Boombox_Tag_Metabox' ) ) {

	/**
	 * Class Boombox_Tag_Metabox
	 * @since   2.5.0
	 * @version 2.5.0
	 */
	class Boombox_Tag_Metabox {

		/**
		 * Holds single instance
		 * @var null
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		private static $_instance = null;

		/**
		 * Get single instance
		 * @return Boombox_Tag_Metabox
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
		 * Boombox_Tag_Metabox constructor.
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
				'id'       => 'bb-post-tag-main-advanced-fields',
				'title'    => esc_html__( 'Boombox Post Tag Advanced Fields', 'boombox' ),
				'taxonomy' => array( 'post_tag' ),
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
				// global tab
				'tab_global' => array(
					'title'  => esc_html__( 'Global', 'boombox' ),
					'active' => true,
					'icon'   => false,
					'order'  => 20,
					'fields' => array(
						// Tag Icon
						'post_tag_icon_name' => array(
							'type'        => 'icons_dropdown',
							'label'       => esc_html__( 'Badge Icon', 'boombox' ),
							'order'       => 20,
							'default'     => '',
						),
						// Tag image icon
						'term_image_icon_id' => array(
							'type'        => 'image',
							'label'       => esc_html__( 'Custom Badge Icon', 'boombox' ),
							'description' => __( 'Upload .svg, .png, .jpg with optimal size of 80x80px', 'boombox' ),
							'order'       => 30,
							'default'     => '',
						),
						// Tag badge background color
						'term_icon_background_color' => array(
							'type'       => 'color',
							'label'      => esc_html__( 'Badge Background Color', 'boombox' ),
							'standalone' => true,
							'order'      => 40,
							'default'    => boombox_get_theme_option( 'extras_badges_category_background_color' ),
						),
						// Tag hide featured media for attached posts
						'hide_attached_posts_featured_media' => array(
							'type'        => 'checkbox',
							'standalone'  => true,
							'label'       => esc_html__( 'Hide Attached Posts Featured Media', 'boombox' ),
							'description' => esc_html__( 'Check to hide featured media from the single article page of posts - attached to this tag', 'boombox' ),
							'default'     => 0,
							'order'       => 50,
						),
						// other fields go here
					),
				),
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
				// AMP
				'tab_amp' => array(
					'title'  => esc_html__( 'AMP', 'boombox' ),
					'active' => false,
					'icon'   => false,
					'order'  => 40,
					'fields' => array(
						// other fields go here
					)
				),
				// other tabs go here
			);

			return apply_filters( 'boombox/admin/taxonomy/meta-boxes/structure', $structure, $config[ 'id' ], 'post_tag' );
		}
	}

	$instance = Boombox_Tag_Metabox::get_instance();
	new AIOM_Taxonomy_Metabox( $instance->get_config__main_box(), array( $instance, 'get_structure__main_box' ) );

}