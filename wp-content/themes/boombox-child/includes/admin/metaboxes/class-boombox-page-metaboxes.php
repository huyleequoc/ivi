<?php
/**
 * Register a page meta box using a class.
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! class_exists( 'Boombox_Page_Metabox' ) ) {
	
	class Boombox_Page_Metabox {

	    const PAGE_VERSION = '2.0.0';
		
		/**
		 * Holds status of current page against trending ones
		 * @var bool
		 */
		private $is_trending_page = null;
		
		/**
		 * Get status of current page against trending ones
		 * @return bool
		 */
		public function is_trending_page() {
			if( null === $this->is_trending_page ) {
				$this->is_trending_page = $this->is_trending_admin_page_template( get_post() );
			}
			
			return $this->is_trending_page;
		}
		
		/**
		 * Holds single instance
		 * @var null
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		private static $_instance = null;
		
		/**
		 * Get single instance
		 * @return Boombox_Page_Metabox
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
		 * Boombox_Page_Metabox constructor.
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		private function __construct() {
		}
		
		/**
		 * Get trending page ID by trending type
		 *
		 * @param string $type 'trending' |'hot' |'popular'
		 *
		 * @return int
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		private function get_trending_page_id( $type ) {
			return absint( boombox_get_theme_option( 'extras_post_ranking_system_' . $type . '_page' ) );
		}
		
		/**
		 * Check current post against one of trending pages
		 *
		 * @param WP_Post $post Current post
		 *
		 * @return bool|null
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		public function is_trending_admin_page_template( $post ) {
			if( $post ) {
				$trending_pages = array(
					$this->get_trending_page_id( 'trending' ),
					$this->get_trending_page_id( 'hot' ),
					$this->get_trending_page_id( 'popular' )
				);
				
				return in_array( $post->ID, $trending_pages );
			}
			
			return null;
		}
		
		/**
		 * Get configuration - Main box
		 * @return array
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		public function get_config__main_box() {
			return array(
				'id'        => 'bb-page-main-advanced-fields',
				'title'     => esc_html__( 'Boombox Page Advanced Fields', 'boombox' ),
				'post_type' => array( 'page' ),
				'context'   => 'normal',
				'priority'  => 'high',
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
			
			$choices_helper = Boombox_Choices_Helper::get_instance();
			$is_trending_page = $this->is_trending_page();
			
			$excluded_listing_types = array();
			if( $is_trending_page ) {
				$excluded_listing_types[] = 'none';
			}
			$listing_types_choices = $choices_helper->get_listing_types( 'value=>image', $excluded_listing_types );
			$boombox_conditions_choices = $choices_helper->get_conditions();
			$boombox_time_range_choices = $choices_helper->get_time_ranges();
			$boombox_category_choices = $choices_helper->get_categories();
			
			$structure = array(
				// Main
				'tab_main'          => array(
					'title'  => esc_html__( 'Main', 'boombox' ),
					'active' => true,
					'icon'   => false,
					'order'  => 20,
					'fields' => array(
						// Sidebar Type
						'boombox_sidebar_type'        => array(
							'type'     => 'radio-image',
							'label'    => esc_html__( 'Sidebar Type', 'boombox' ),
							'choices'  => $choices_helper->get_sidebar_types(),
							'default'  => '1-sidebar-1_3',
							'order'    => 20,
						),
						// Sidebar Orientation
						'boombox_sidebar_orientation' => array(
							'type'            => 'radio',
							'label'           => esc_html__( 'Sidebar Orientation', 'boombox' ),
							'choices'         => array(
								'right' => esc_html__( 'Right', 'boombox' ),
								'left'  => esc_html__( 'Left', 'boombox' ),
							),
							'default'         => 'right',
							'order'           => 30,
							'config'          => array(
								'axis' => 'vertical',
							),
							'active_callback' => array(
								array(
									'field_id' => 'boombox_sidebar_type',
									'value'    => 'no-sidebar',
									'compare'  => '!=',
								),
							),
						),
						// Primary sidebar
						'boombox_primary_sidebar'     => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Primary Sidebar', 'boombox' ),
							'choices'         => $choices_helper->get_primary_sidebars(),
							'default'         => 'default-sidebar',
							'order'           => 40,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_sidebar_type',
									'value'    => 'no-sidebar',
									'compare'  => '!=',
								),
							),
						),
						// Secondary sidebar
						'boombox_secondary_sidebar'   => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Secondary Sidebar', 'boombox' ),
							'choices'         => $choices_helper->get_secondary_sidebars(),
							'default'         => 'page-secondary',
							'order'           => 50,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_sidebar_type',
									'value'    => array(
										'2-sidebars-1_4-1_4',
										'2-sidebars-small-big',
									),
									'compare'  => 'IN',
								),
							),
						),
						// other fields go here
					),
				),
				// Featured Area
				'tab_featured_area' => array(
					'title'  => esc_html__( 'Featured Area', 'boombox' ),
					'active' => false,
					'icon'   => false,
					'order'  => 30,
					'fields' => array(
						// Featured Area Type
						'boombox_featured_area_type'                   => array(
							'type'     => 'radio-image',
							'label'    => esc_html__( 'Area Type', 'boombox' ),
							'choices'  => $choices_helper->get_featured_area_types(),
							'default'  => 'disable',
							'order'    => 20,
						),
						// "Layout" heading
						'boombox_featured_area_layout_heading'         => array(
							'type'            => 'custom',
							'html'            => sprintf( '<h2>%s</h2><hr/>', esc_html__( 'Layout', 'boombox' ) ),
							'order'           => 30,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_featured_area_type',
									'value'    => 'disable',
									'compare'  => '!=',
								)
							),
						),
						// Disable Gap Between Thumbnails
						'boombox_featured_disable_gap'                 => array(
							'type'            => 'checkbox',
							'label'           => esc_html__( 'Disable Gap Between Thumbnails', 'boombox' ),
							'default'         => 0,
							'order'           => 40,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_featured_area_type',
									'value'    => 'disable',
									'compare'  => '!=',
								)
							),
						),
						// Featured Area Hide Elements
						'boombox_featured_hide_elements'               => array(
							'type'            => 'multicheck',
							'label'           => esc_html__( 'Hide Elements', 'boombox' ),
							'choices'         => $choices_helper->get_featured_area_hide_elements(),
							'default'         => array(),
							'order'           => 50,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_featured_area_type',
									'value'    => 'disable',
									'compare'  => '!=',
								)
							),
						),
						// "Posts Loop" heading
						'boombox_featured_area_posts_loop_heading'     => array(
							'type'            => 'custom',
							'html'            => sprintf( '<h2>%s</h2><hr/>', esc_html__( 'Posts Loop', 'boombox' ) ),
							'order'           => 60,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_featured_area_type',
									'value'    => 'disable',
									'compare'  => '!=',
								)
							),
						),
						// Featured Area Conditions
						'boombox_featured_area_conditions'             => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Order Criteria', 'boombox' ),
							'choices'         => $boombox_conditions_choices,
							'default'         => 'recent',
							'order'           => 70,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_featured_area_type',
									'value'    => 'disable',
									'compare'  => '!=',
								)
							),
						),
						// Featured Area Time Range
						'boombox_featured_area_time_range'             => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Time Range', 'boombox' ),
							'default'         => 'month',
							'choices'         => $boombox_time_range_choices,
							'order'           => 80,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_featured_area_type',
									'value'    => 'disable',
									'compare'  => '!=',
								)
							),
						),
						// Featured Area Categories
						'boombox_featured_area_category'               => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Categories Filter', 'boombox' ),
							'choices'         => $boombox_category_choices,
							'default'         => array( '' ),
							'order'           => 90,
							'attributes'      => array(
								'multiple' => 'multiple',
								'size'     => 6,
							),
							'active_callback' => array(
								array(
									'field_id' => 'boombox_featured_area_type',
									'value'    => 'disable',
									'compare'  => '!=',
								)
							),
						),
						// Featured Area Tags
						'boombox_featured_area_tags'                   => array(
							'type'            => 'textarea',
							'label'           => esc_html__( 'Tags Filter', 'boombox' ),
							'description'     => esc_html__( 'Comma separated list of tags slugs', 'boombox' ),
							'default'         => 0,
							'order'           => 100,
							'attributes'      => array(
								'rows' => 6,
							),
							'active_callback' => array(
								array(
									'field_id' => 'boombox_featured_area_type',
									'value'    => 'disable',
									'compare'  => '!=',
								)
							),
							'render_callback'  => array(
								$this,
								'render_tags_field'
							),
						),
						// Exclude Featured Entries From Main Posts Loop
						'boombox_featured_area_exclude_from_main_loop' => array(
							'type'            => 'checkbox',
							'label'           => esc_html__( 'Exclude Featured Entries From Main Posts Loop', 'boombox' ),
							'default'         => 1,
							'order'           => 110,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_featured_area_type',
									'value'    => 'disable',
									'compare'  => '!=',
								)
							),
						)
					)
				),
				// Posts Listing
				'tab_listing'       => array(
					'title'  => esc_html__( 'Posts Listing', 'boombox' ),
					'active' => false,
					'icon'   => false,
					'order'  => 40,
					'fields' => array(
						// Listing Type
						'boombox_listing_type'               => array(
							'type'     => 'radio-image',
							'name'     => $is_trending_page ? 'boombox_trending_listing_type' : 'boombox_listing_type',
							'label'    => esc_html__( 'Listing Type', 'boombox' ),
							'choices'  => $listing_types_choices,
							'default'  => $is_trending_page ? 'list' : 'none',
							'order'    => 20,
						),
						// "Layout" heading
						'boombox_listing'                    => array(
							'type'            => 'custom',
							'html'            => sprintf( '<h2>%s</h2><hr/>', esc_html__( 'Layout', 'boombox' ) ),
							'order'           => 30,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_listing_type',
									'value'    => 'none',
									'compare'  => '!=',
								)
							),
						),
						// Hide elements
						'boombox_listing_hide_elements'      => array(
							'type'            => 'multicheck',
							'label'           => esc_html__( 'Hide Elements', 'boombox' ),
							'choices'         => $choices_helper->get_grid_hide_elements(),
							'default'         => array( 'tags' ),
							'order'           => 40,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_listing_type',
									'value'    => 'none',
									'compare'  => '!=',
								),
							),
						),
						// Share Bar Elements
						'boombox_listing_share_bar_elements' => array(
							'type'            => 'multicheck',
							'label'           => esc_html__( 'Share Bar Elements', 'boombox' ),
							'choices'         => $choices_helper->get_share_bar_elements(),
							'default'         => array( 'tags' ),
							'order'           => 50,
							'active_callback' => array(
								'relation' => 'AND',
								array(
									'field_id' => 'boombox_listing_type',
									'value'    => 'none',
									'compare'  => '!=',
								),
								array(
									'field_id' => 'boombox_listing_hide_elements',
									'value'    => 'share_bar',
									'compare'  => 'NOT IN',
								),
							),
						),
						// "Posts Loop" heading
						'boombox_listing_loop_heading'       => array(
							'type'            => 'custom',
							'html'            => sprintf( '<h2>%s</h2><hr/>', esc_html__( 'Posts Loop', 'boombox' ) ),
							'order'           => 60,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_listing_type',
									'value'    => 'none',
									'compare'  => '!=',
								),
							),
						),
						// Pagination type
						'boombox_pagination_type'            => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Pagination Type', 'boombox' ),
							'choices'         => $choices_helper->get_pagination_types(),
							'default'         => 'load_more',
							'order'           => 70,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_listing_type',
									'value'    => 'none',
									'compare'  => '!=',
								)
							),
						),
						// Posts Per Page
						'boombox_posts_per_page'             => array(
							'type'            => 'number',
							'label'           => esc_html__( 'Posts Per Page', 'boombox' ),
							'order'           => 80,
							'default'         => get_option( 'posts_per_page' ),
							'attributes'      => array(
								'min' => 1,
							),
							'active_callback' => array(
								array(
									'field_id' => 'boombox_listing_type',
									'value'    => 'none',
									'compare'  => '!=',
								)
							),
						),
						// other fields go here
					)
				),
				// Title Area
				'tab_title_area'    => array(
					'title'  => esc_html__( 'Title Area', 'boombox' ),
					'active' => false,
					'icon'   => false,
					'order'  => 50,
					'fields' => array(
						// Disable Title Area
						'boombox_hide_title_area'                          => array(
							'type'     => 'checkbox',
							'label'    => esc_html__( 'Disable Title Area', 'boombox' ),
							'default'  => 0,
							'order'    => 20,
						),
						// Style
						'boombox_title_area_style'                         => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Style', 'boombox' ),
							'order'           => 30,
							'choices'         => $choices_helper->get_template_header_style_choices(),
							'default'         => 'style1',
							'active_callback' => array(
								array(
									'field_id' => 'boombox_hide_title_area',
									'compare'  => '==',
									'value'    => 0
								)
							)
						),
						// Container Type
						'boombox_title_area_background_container'          => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Container Type', 'boombox' ),
							'order'           => 40,
							'choices'         => $choices_helper->get_template_header_background_container_choices(),
							'default'         => 'boxed',
							'active_callback' => array(
								array(
									'field_id' => 'boombox_hide_title_area',
									'compare'  => '==',
									'value'    => 0
								)
							)
						),
						// Text Color
						'boombox_title_area_text_color'                    => array(
							'type'            => 'color',
							'label'           => esc_html__( 'Text Color', 'boombox' ),
							'order'           => 50,
							'default'         => '',
							'active_callback' => array(
								array(
									'field_id' => 'boombox_hide_title_area',
									'compare'  => '==',
									'value'    => 0
								),
							)
						),
						// Background Color
						'boombox_title_area_background_color'              => array(
							'type'            => 'color',
							'label'           => esc_html__( 'Background Color', 'boombox' ),
							'order'           => 60,
							'default'         => '',
							'active_callback' => array(
								array(
									'field_id' => 'boombox_hide_title_area',
									'compare'  => '==',
									'value'    => 0
								),
							)
						),
						// Gradient Color
						'boombox_title_area_background_gradient_color'     => array(
							'type'            => 'color',
							'label'           => esc_html__( 'Gradient Color', 'boombox' ),
							'order'           => 70,
							'default'         => '',
							'active_callback' => array(
								array(
									'field_id' => 'boombox_hide_title_area',
									'compare'  => '==',
									'value'    => 0,
								),
							),
						),
						// Gradient Direction
						'boombox_title_area_background_gradient_direction' => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Gradient Direction', 'boombox' ),
							'order'           => 80,
							'choices'         => $choices_helper->get_template_header_background_gradient_direction_choices(),
							'default'         => 'top',
							'active_callback' => array(
								array(
									'field_id' => 'boombox_hide_title_area',
									'compare'  => '==',
									'value'    => 0,
								),
							),
						),
						// Background Image
						'boombox_title_area_background_image'              => array(
							'type'            => 'image',
							'label'           => esc_html__( 'Background Image', 'boombox' ),
							'order'           => 90,
							'default'         => '',
							'active_callback' => array(
								array(
									'field_id' => 'boombox_hide_title_area',
									'compare'  => '==',
									'value'    => 0,
								),
							),
						),
						// Background Image Size
						'boombox_title_area_background_image_size'         => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Background Image Size', 'boombox' ),
							'order'           => 100,
							'choices'         => $choices_helper->get_template_header_background_image_size_choices(),
							'default'         => 'cover',
							'active_callback' => array(
								array(
									'field_id' => 'boombox_hide_title_area',
									'compare'  => '==',
									'value'    => 0,
								),
							),
						),
						// Background Image Position
						'boombox_title_area_background_image_position'     => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Background Image Position', 'boombox' ),
							'order'           => 110,
							'choices'         => $choices_helper->get_template_header_background_image_position_choices(),
							'default'         => 'center',
							'active_callback' => array(
								array(
									'field_id' => 'boombox_hide_title_area',
									'compare'  => '==',
									'value'    => 0,
								),
							),
						),
						// Background Image Repeat
						'boombox_title_area_background_image_repeat'       => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Background Image Repeat', 'boombox' ),
							'order'           => 120,
							'choices'         => $choices_helper->get_template_header_background_image_repeat_choices(),
							'default'         => 'repeat-no',
							'active_callback' => array(
								array(
									'field_id' => 'boombox_hide_title_area',
									'compare'  => '==',
									'value'    => 0,
								),
							),
						),
						// other fields go here
					),
				),
				// Posts Strip
				'tab_posts_strip'   => array(
					'title'  => esc_html__( 'Posts Strip', 'boombox' ),
					'active' => false,
					'icon'   => false,
					'order'  => 60,
					'fields' => array(
						// Configuration
						'boombox_strip_configuration'      => array(
							'type'     => 'select',
							'label'    => esc_html__( 'Configuration', 'boombox' ),
							'choices'  => array_merge( array( 'none' => esc_html__( 'None', 'boombox' ) ), $choices_helper->get_strip_configurations() ),
							'default'  => 'inherit',
							'order'    => 20,
						),
						// "Layout" heading
						'boombox_strip_layout_heading'     => array(
							'type'            => 'custom',
							'html'            => sprintf( '<h2>%s</h2><hr/>', esc_html__( 'Layout', 'boombox' ) ),
							'order'           => 30,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_strip_configuration',
									'compare'  => '==',
									'value'    => 'custom'
								)
							)
						),
						// Type
						'boombox_strip_type'               => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Type', 'boombox' ),
							'choices'         => $choices_helper->get_strip_types(),
							'default'         => 'slider',
							'order'           => 40,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_strip_configuration',
									'compare'  => '==',
									'value'    => 'custom'
								)
							)
						),
						// Width
						'boombox_strip_width'              => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Width', 'boombox' ),
							'order'           => 50,
							'choices'         => $choices_helper->get_strip_dimensions(),
							'default'         => 'boxed',
							'active_callback' => array(
								array(
									'field_id' => 'boombox_strip_configuration',
									'compare'  => '==',
									'value'    => 'custom'
								)
							)
						),
						// Size
						'boombox_strip_size'               => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Size', 'boombox' ),
							'choices'         => $choices_helper->get_strip_sizes(),
							'default'         => 'big',
							'order'           => 60,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_strip_configuration',
									'compare'  => '==',
									'value'    => 'custom'
								)
							)
						),
						// Titles Position
						'boombox_strip_title_position'     => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Titles Position', 'boombox' ),
							'choices'         => $choices_helper->get_strip_title_positions(),
							'default'         => 'inside',
							'order'           => 70,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_strip_configuration',
									'compare'  => '==',
									'value'    => 'custom'
								)
							)
						),
						// Gap Between Thumbnails
						'boombox_strip_disable_gap'        => array(
							'type'            => 'checkbox',
							'label'           => esc_html__( 'Disable Gap Between Thumbnails', 'boombox' ),
							'default'         => 1,
							'order'           => 80,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_strip_configuration',
									'compare'  => '==',
									'value'    => 'custom'
								)
							)
						),
						// "Posts Loop" heading
						'boombox_strip_posts_loop_heading' => array(
							'type'            => 'custom',
							'html'            => sprintf( '<h2>%s</h2><hr/>', esc_html__( 'Posts Loop', 'boombox' ) ),
							'order'           => 90,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_strip_configuration',
									'compare'  => '==',
									'value'    => 'custom'
								)
							)
						),
						// Order Criteria
						'boombox_strip_conditions'         => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Order Criteria', 'boombox' ),
							'choices'         => $choices_helper->get_conditions(),
							'default'         => 'recent',
							'order'           => 100,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_strip_configuration',
									'compare'  => '==',
									'value'    => 'custom'
								)
							)
						),
						// Time Range
						'boombox_strip_time_range'         => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Time Range', 'boombox' ),
							'choices'         => $choices_helper->get_time_ranges(),
							'default'         => 'all',
							'order'           => 110,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_strip_configuration',
									'compare'  => '==',
									'value'    => 'custom'
								)
							)
						),
						// Items Count
						'boombox_strip_items_count'        => array(
							'type'            => 'number',
							'label'           => esc_html__( 'Items Count', 'boombox' ),
							'description'     => esc_html__( 'Minimum count: 6. To show all items, please enter -1.', 'boombox' ),
							'default'         => 18,
							'order'           => 120,
							'attributes'      => array(
								'min'  => -1,
								'step' => 1
							),
							'active_callback' => array(
								array(
									'field_id' => 'boombox_strip_configuration',
									'compare'  => '==',
									'value'    => 'custom'
								)
							)
						),
						// Categories Filter
						'boombox_strip_category'           => array(
							'type'            => 'select',
							'label'           => esc_html__( 'Categories Filter', 'boombox' ),
							'choices'         => $choices_helper->get_categories(),
							'default'         => array( '' ),
							'order'           => 130,
							'attributes'      => array(
								'multiple' => 'multiple',
								'size'     => 6,
							),
							'active_callback' => array(
								array(
									'field_id' => 'boombox_strip_configuration',
									'compare'  => '==',
									'value'    => 'custom'
								)
							)
						),
						// Tags Filter
						'boombox_strip_tags'               => array(
							'type'            => 'textarea',
							'label'           => esc_html__( 'Tags Filter', 'boombox' ),
							'description'     => esc_html__( 'Comma separated list of tags slugs', 'boombox' ),
							'default'         => '',
							'order'           => 140,
							'attributes'      => array(
								'rows' => 6,
							),
							'active_callback' => array(
								array(
									'field_id' => 'boombox_strip_configuration',
									'compare'  => '==',
									'value'    => 'custom'
								)
							),
							'render_callback'  => array(
								$this,
								'render_tags_field'
							),
						),
					)
				),
				// other tabs go here
			);

			if( ! $this->is_trending_page ) {
				// Tab "Listing"
				$structure[ 'tab_listing' ][ 'fields' ] = array_merge( $structure[ 'tab_listing' ][ 'fields' ], array(
					// Order Criteria
					'boombox_listing_condition'  => array(
						'label'           => __( 'Order Criteria', 'boombox' ),
						'type'            => 'select',
						'choices'         => $boombox_conditions_choices,
						'order'           => 60,
						'sub_order'       => 10,
						'default'         => 'recent',
						'active_callback' => array(
							array(
								'field_id' => 'boombox_listing_type',
								'value'    => 'none',
								'compare'  => '!=',
							)
						),
						'sanitize_callback' => 'sanitize_text_field',
					),
					// Time Range
					'boombox_listing_time_range' => array(
						'label'           => __( 'Time Range', 'boombox' ),
						'type'            => 'select',
						'choices'         => $boombox_time_range_choices,
						'order'           => 60,
						'sub_order'       => 20,
						'default'         => 'all',
						'active_callback' => array(
							array(
								'field_id' => 'boombox_listing_type',
								'value'    => 'none',
								'compare'  => '!=',
							)
						),
						'sanitize_callback' => 'sanitize_text_field',
					),
					// Categories Filter
					'boombox_listing_categories' => array(
						'label'           => __( 'Categories Filter', 'boombox' ),
						'type'            => 'select',
						'choices'         => $boombox_category_choices,
						'order'           => 60,
						'sub_order'       => 30,
						'default'         => array( '' ),
						'active_callback' => array(
							array(
								'field_id' => 'boombox_listing_type',
								'value'    => 'none',
								'compare'  => '!=',
							)
						),
						'attributes'      => array(
							'multiple' => 'multiple',
						),
					),
					// Tags Filter
					'boombox_listing_tags'       => array(
						'label'           => __( 'Tags Filter', 'boombox' ),
						'description'     => __( 'Comma separated list of tags slugs', 'boombox' ),
						'type'            => 'textarea',
						'order'           => 60,
						'sub_order'       => 40,
						'default'         => '',
						'attributes'      => array(
							'rows' => '5',
						),
						'active_callback' => array(
							array(
								'field_id' => 'boombox_listing_type',
								'value'    => 'none',
								'compare'  => '!=',
							)
						),
						'sanitize_callback' => array( $this, 'sanitize_tags_field' ),
						'render_callback'   => array( $this, 'render_tags_field' ),
					),
				) );

				// Tab "Title Area"
				$structure[ 'tab_title_area' ][ 'fields' ] = array_merge( $structure[ 'tab_title_area' ][ 'fields' ], array(
					// Hide Filter
					'boombox_title_area_hide_filter' => array(
						'type'     => 'checkbox',
						'label'    => __( 'Hide Filter', 'boombox' ),
						'order'    => 130,
						'default'  => true,
						'sanitize_callback' => 'sanitize_text_field',
						'active_callback' => array(
							array(
								'field_id' => 'boombox_hide_title_area',
								'compare' => '==',
								'value'   => 0
							)
						)
					),
				) );
			}

			return apply_filters( 'boombox/admin/post/meta-boxes/structure', $structure, $config[ 'id' ], 'page', $config[ 'context' ] );
		}
		
		/**
		 * Render tags field value
		 *
		 * @param array|string $value Current value
		 *
		 * @return string
		 */
		public function render_tags_field( $value ) {
			return implode( ',', (array) $value );
		}
		
	}
	
	$instance = Boombox_Page_Metabox::get_instance();
	new AIOM_Post_Metabox( $instance->get_config__main_box(), array( $instance, 'get_structure__main_box' ) );
	
}