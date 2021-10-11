<?php
/**
 * Register a Boombox_Reaction_Metabox using a class.
 *
 * @package BoomBox_Theme_Extensions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

// do nothing if base meta box class does not exists
if( ! class_exists( 'AIOM_Taxonomy_Metabox' ) ) {
	return;
}

if( ! class_exists( 'Boombox_Reaction_Metabox' ) ) {

	/**
	 * Class Boombox_Reaction_Metabox
	 * @since   2.5.0
	 * @version 2.5.0
	 */
	class Boombox_Reaction_Metabox {

		/**
		 * Holds single instance
		 * @var null
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		private static $_instance = null;

		/**
		 * Get single instance
		 * @return Boombox_Reaction_Metabox
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
		 * Boombox_Reaction_Metabox constructor.
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
			add_filter( 'aiom/custom_field_type_config', array( $this, 'aiom_add_custom_field_types' ), 10, 2 );
			add_action( 'admin_menu' , array( $this, 'remove_post_reaction_fields' ) );
			add_action( 'admin_footer' , array( $this, 'boombox_post_quick_edit_script' ) );
		}
		
		/**
		 * Get reactions SVG list
		 * @return array
		 * @since 2.5.0
		 * @version 2.5.0
		 */
		private function get_reactions_svg_list() {
			$reactions = array();
			$dirs = apply_filters( 'boombox_reaction_icons_path', array(
				array(
					'path' => BBTE_REACTIONS_PATH . 'svg/',
					'url'  => BBTE_REACTIONS_ICON_URL,
				),
			) );
			
			foreach ( (array) $dirs as $dir ) {
				if( is_dir( $dir[ 'path' ] ) ) {
					if( $dh = opendir( $dir[ 'path' ] ) ) {
						while ( ( $file = readdir( $dh ) ) !== false ) {
							if( in_array( $file, array( '.', '..', 'index.php' ) ) ) {
								continue;
							}
							
							$reactions_pathinfo = pathinfo( $file );
							$index = strtr( $reactions_pathinfo[ 'filename' ], array(
								' ' => '_',
								'-' => '_',
							) );
							
							$reactions_pathinfo[ 'filepath' ] = $dir[ 'url' ] . $reactions_pathinfo[ 'basename' ];
							$reactions_pathinfo[ 'filename' ] = strtr( $reactions_pathinfo[ 'filename' ], array(
								'_' => ' ',
								'-' => ' ',
							) );
							$reactions[ $index ] = $reactions_pathinfo;
						}
						closedir( $dh );
					}
				}
			}
			
			ksort( $reactions );
			
			return $reactions;
		}
		
		/**
		 * Removes a reaction meta box from post edit screen
		 * @since 2.5.0
		 * @version 2.5.0
		 */
		public function remove_post_reaction_fields(){
			remove_meta_box( 'reactiondiv' , 'post' , 'normal' );
		}
		
		/**
		 * Callback to setup AIOM custom field types configuration
		 * @param array $config Current configuration for field type
		 * @param string $type Sanitized field type
		 * @since 2.5.0
		 * @version 2.5.0
		 *
		 * @return array
		 */
		public function aiom_add_custom_field_types( $config, $type ) {
			
			$custom_field_types_path = BBTE_REACTIONS_PATH . 'lib' . DIRECTORY_SEPARATOR . 'aiom-custom-fields' . DIRECTORY_SEPARATOR;
			switch( $type ) {
				case 'reaction_icon':
					$config = array(
						'class' => 'Boombox_AIOM_Reaction_Icon_Field',
						'path'  => $custom_field_types_path . 'reaction-icon' . DIRECTORY_SEPARATOR . 'field.php'
					);
					break;
			}
			
			return $config;
		}
		
		/**
		 * Remove reactions from quick edit
		 * @since 2.5.0
		 * @version 2.5.0
		 */
		public function boombox_post_quick_edit_script() {
			$screen = get_current_screen();
			if ( $screen && $screen->post_type == 'post' && $screen->id == 'edit-post' ) { ?>
				<script type="text/javascript">
                    var reactions_container = jQuery('.inline-edit-categories .reaction-checklist');
                    reactions_container.siblings('input[type="hidden"],span.inline-edit-categories-label').remove();
                    reactions_container.remove();
				</script>
				<?php
			}
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
				'title'    => esc_html__( 'Boombox Reaction Advanced Fields', 'boombox-theme-extensions' ),
				'taxonomy' => array( 'reaction' ),
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
			$choices_helper = Boombox_Choices_Helper::get_instance();
			$config = static::get_config__main_box();
			$extras_badges_reactions_background_color = boombox_get_theme_option('extras_badges_reactions_background_color');

			$structure = array(
				// global tab
				'tab_global' => array(
					'title'  => esc_html__( 'Global', 'boombox-theme-extensions' ),
					'active' => true,
					'icon'   => false,
					'order'  => 20,
					'fields' => array(
						// Disable Vote
						'reaction_disable_vote' => array(
							'type'        => 'checkbox',
							'name'        => 'reaction_disable_vote',
							'standalone'  => true,
							'label'       => esc_html__( 'Disable Vote', 'boombox' ),
							'default'     => 0,
							'order'       => 20,
						),
						// Reaction Icon
						'reaction_icon_file_name' => array(
							'type'     => 'reaction-icon',
							'name'     => 'reaction_icon_file_name',
							'label'    => esc_html__( 'Reaction Icon', 'boombox-theme-extensions' ),
							'choices'  => $this->get_reactions_svg_list(),
							'default'  => '',
							'wrapper_attributes' => array(
								'id' => 'reaction_icon_file_name_wrapper',
								'data-default-color' => $extras_badges_reactions_background_color
							),
							'thumbnail_wrapper_attributes' => array(
								'style' => 'background:' . $extras_badges_reactions_background_color . ';'
							),
							'placeholder' => apply_filters( 'boombox_reaction_placeholder_img_src', BBTE_REACTIONS_URL . 'images/placeholder.png' ),
							'order'       => 30,
						),
						// Color Scheme
						'term_icon_color_scheme' => array(
							'type'        => 'select',
							'name'        => 'term_icon_color_scheme',
							'label'       => esc_html__( 'Color Scheme', 'boombox-theme-extensions' ),
							'choices'     => array(
								'default' => esc_html__( 'Default', 'boombox-theme-extensions' ),
								'custom'  => esc_html__( 'Custom', 'boombox-theme-extensions' ),
							),
							'default'     => 'default',
							'order'       => 40,
							'sub_order'   => 20,
						),
						// Color
						'term_icon_background_color' => array(
							'type'            => 'color',
							'name'            => 'term_icon_background_color',
							'standalone'      => true,
							'label'           => esc_html__( 'Icon Background Color', 'boombox-theme-extensions' ),
							'order'           => 40,
							'sub_order'       => 30,
							'default'         => $extras_badges_reactions_background_color,
							'active_callback' => array(
								array(
									'field_id' => 'term_icon_color_scheme',
									'value'    => 'custom',
									'compare'  => '==',
								),
							),
						),
					),
				),
				// title area
				'tab_title_area' => array(
					'title'  => esc_html__( 'Title Area', 'boombox-theme-extensions' ),
					'active' => false,
					'icon'   => false,
					'order'  => 30,
					'fields' => array(
						// Style
						'title_area_style' => array(
							'type'            => 'select',
							'name'            => 'title_area_style',
							'label'           => esc_html__( 'Style', 'boombox-theme-extensions' ),
							'choices'         => array_merge( array( 'inherit' => esc_html__( 'Inherit' ), ), $choices_helper->get_template_header_style_choices() ),
							'default'         => 'inherit',
							'order'           => 20,
						),
						// Container Type
						'title_area_background_container' => array(
							'type'            => 'select',
							'name'            => 'title_area_background_container',
							'label'           => esc_html__( 'Container Type', 'boombox-theme-extensions' ),
							'choices'         => array_merge( array( 'inherit' => esc_html__( 'Inherit' ) ), $choices_helper->get_template_header_background_container_choices() ),
							'default'         => 'inherit',
							'order'           => 30,
						),
						// Text Color
						'title_area_text_color' => array(
							'type'     => 'color',
							'name'     => 'title_area_text_color',
							'label'    => esc_html__( 'Text Color', 'boombox-theme-extensions' ),
							'order'    => 40,
							'default'  => '',
						),
						// Background Color
						'title_area_bg_color' => array(
							'type'     => 'color',
							'name'     => 'title_area_bg_color',
							'label'    => esc_html__( 'Background Color', 'boombox-theme-extensions' ),
							'order'    => 50,
							'default'  => '',
						),
						// Gradient Color
						'title_area_gradient_color' => array(
							'type'     => 'color',
							'name'     => 'title_area_gradient_color',
							'label'    => esc_html__( 'Gradient Color', 'boombox-theme-extensions' ),
							'order'    => 60,
							'default'  => '',
						),
						// Gradient Direction
						'title_area_bg_gradient_direction' => array(
							'type'     => 'select',
							'name'     => 'title_area_bg_gradient_direction',
							'label'    => esc_html__( 'Gradient Direction', 'boombox-theme-extensions' ),
							'choices'  => $choices_helper->get_template_header_background_gradient_direction_choices(),
							'default'  => 'top',
							'order'    => 70,
						),
						// Background Image
						'title_area_background_image' => array(
							'type'        => 'image',
							'name'        => 'title_area_background_image',
							'label'       => esc_html__( 'Background Image', 'boombox-theme-extensions' ),
							'order'       => 80,
							'default'     => '',
						),
						// Background Image Size
						'title_area_background_image_size' => array(
							'type'     => 'select',
							'name'     => 'title_area_background_image_size',
							'label'    => esc_html__( 'Background Image Size', 'boombox-theme-extensions' ),
							'choices'  => $choices_helper->get_template_header_background_image_size_choices(),
							'default'  => 'auto',
							'order'    => 90,
						),
						// Background Image Position
						'title_area_background_image_position' => array(
							'type'     => 'select',
							'name'     => 'title_area_background_image_position',
							'label'    => esc_html__( 'Background Image Position', 'boombox-theme-extensions' ),
							'choices'  => $choices_helper->get_template_header_background_image_position_choices(),
							'default'  => 'center',
							'order'    => 100,
						),
						// Background Image Repeat
						'title_area_background_image_repeat' => array(
							'type'     => 'select',
							'name'     => 'title_area_background_image_repeat',
							'label'    => esc_html__( 'Background Image Repeat', 'boombox-theme-extensions' ),
							'choices'  => $choices_helper->get_template_header_background_image_repeat_choices(),
							'default'  => 'repeat-no',
							'order'    => 110,
						),
						// other fields go here
					)
				),
				// other tabs go here
			);

			return apply_filters( 'boombox/admin/taxonomy/meta-boxes/structure', $structure, $config[ 'id' ], 'reaction' );
		}
	}

	$instance = Boombox_Reaction_Metabox::get_instance();
	new AIOM_Taxonomy_Metabox( $instance->get_config__main_box(), array( $instance, 'get_structure__main_box' ) );

}