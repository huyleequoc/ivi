<?php
/**
 * Register a post meta box using a class.
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! class_exists( 'Boombox_Post_Metabox' ) ) {
	
	/**
	 * Class Boombox_Post_Metabox
	 * @since   2.5.0
	 * @version 2.5.0
	 */
	class Boombox_Post_Metabox {
		
		/**
		 * Holds single instance
		 * @var null
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		private static $_instance = null;
		
		/**
		 * Get single instance
		 * @return Boombox_Post_Metabox
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
		 * Boombox_Post_Metabox constructor.
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
		public function get_config__main_box__general() {
			return array(
				'id'        => 'bb-post-main-advanced-fields',
				'title'     => esc_html__( 'Boombox Post Advanced Fields', 'boombox' ),
				'post_type' => array( 'post' ),
				'context'   => 'normal',
				'priority'  => 'high',
			);
		}
		
		/**
		 * Get configuration - Side box - main
		 * @return array
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		public function get_config__side_box__general() {
			return array(
				'id'        => 'bb-post-side-main-advanced-fields',
				'title'     => esc_html__( 'Boombox Post Advanced Fields', 'boombox' ),
				'post_type' => array( 'post' ),
				'context'   => 'side',
				'priority'  => 'high',
			);
		}

		/**
		 * Get configuration - Side box - gallery
		 * @return array
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		public function get_config__side_box__gallery() {
			return array(
				'id'        => 'bb-post-side-gallery-advanced-fields',
				'title'     => esc_html__( 'Post Gallery', 'boombox' ),
				'post_type' => array( 'post' ),
				'context'   => 'side',
				'priority'  => 'high',
			);
		}
		
		/**
		 * Get structure - Main box
		 * @return array
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		public function get_structure__main_box__general() {
			
			$config = static::get_config__main_box__general();
			$choices_helper = Boombox_Choices_Helper::get_instance();
			
			$structure = array(
				// main tab
				'tab_main'      => array(
					'title'  => esc_html__( 'Main', 'boombox' ),
					'active' => true,
					'icon'   => false,
					'order'  => 20,
					'fields' => array(
						// Hide Featured Media
						'boombox_hide_featured_image' => array(
							'type'     => 'select',
							'label'    => esc_html__( 'Hide Featured Media', 'boombox' ),
							'choices'  => $choices_helper->get_post_featured_image_appearance(),
							'default'  => 'customizer',
							'order'    => 30,
						),
						// Layout
						'boombox_layout'              => array(
							'type'         => 'select',
							'label'        => esc_html__( 'Layout', 'boombox' ),
							'choices'      => array(
								'inherit' => esc_html__( 'Inherit', 'boombox' ),
								'custom'  => esc_html__( 'Custom', 'boombox' ),
							),
							'default'      => 'inherit',
							'order'        => 40,
							'sub_order'    => 20,
						),
						// Template
						'boombox_template'            => array(
							'type'            => 'radio-image',
							'label'           => esc_html__( 'Template', 'boombox' ),
							'choices'         => $choices_helper->get_single_templates(),
							'default'         => 'style1',
							'order'           => 40,
							'sub_order'       => 30,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_layout',
									'value'    => 'custom',
									'compare'  => '=',
								),
							),
						),
						// Sidebar Type
						'boombox_sidebar_type'        => array(
							'type'            => 'radio-image',
							'label'           => esc_html__( 'Sidebar Type', 'boombox' ),
							'choices'         => $choices_helper->get_sidebar_types(),
							'default'         => '1-sidebar-1_3',
							'order'           => 40,
							'sub_order'       => 40,
							'active_callback' => array(
								array(
									'field_id' => 'boombox_layout',
									'value'    => 'custom',
									'compare'  => '=',
								),
							),
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
							'order'           => 40,
							'sub_order'       => 50,
							'config'          => array( 'axis' => 'vertical' ),
							'active_callback' => array(
								array(
									'field_id' => 'boombox_layout',
									'value'    => 'custom',
									'compare'  => '=',
								),
							),
						),
						// Video URL
						'boombox_video_url'           => array(
							'type'        => 'video_url',
							'label'       => esc_html__( 'Video URL', 'boombox' ),
							'description' => esc_html__( 'Supported Videos: MP4, Youtube, Vimeo, Dailymotion, Vine, Odnoklassniki, Facebook, Instagram, Vidme, Vkontakte, Twitch, Coub, Twitter', 'boombox' ),
							'default'     => '',
							'order'       => 50,
							'class'       => 'regular-text',
						),
						// other fields go here
					),
				),
				// source / via tab
				'tab_source_via'      => array(
					'title'  => esc_html__( 'Source / Via', 'boombox' ),
					'active' => false,
					'icon'   => false,
					'order'  => 30,
					'fields' => array(
						// "Article Source" heading
						'boombox_article_source_heading'       => array(
							'type'            => 'custom',
							'html'            => sprintf( '<h2>%s</h2><hr/>', esc_html__( 'Article Source', 'boombox' ) ),
							'order'           => 20,
						),
						// Source URL
						'boombox_article_source_url' => array(
							'type'            => 'url',
							'label'           => esc_html__( 'Source URL', 'boombox' ),
							'order'           => 30,
						),
						// Source Label
						'boombox_article_source_label' => array(
							'type'            => 'text',
							'label'           => esc_html__( 'Source Label', 'boombox' ),
							'order'           => 40,
						),
						'boombox_article_source_follow' => array(
							'type' => 'radio',
							'label'           => esc_html__( 'Source Follow', 'boombox' ),
							'choices'         => array(
								'follow'    => esc_html__( 'Follow', 'boombox' ),
								'nofollow'  => esc_html__( 'No Follow', 'boombox' ),
							),
							'default'         => 'nofollow',
							'config'          => array( 'axis' => 'horizontal' ),
							'order'           => 50,
						),
						'boombox_article_source_target' => array(
							'label'    => esc_html__( 'Open in a new tab', 'boombox' ),
							'type'     => 'checkbox',
							'default'  => 0,
							'order'    => 60,
						),
						// "Article Via" heading
						'boombox_article_via_heading'       => array(
							'type'            => 'custom',
							'html'            => sprintf( '<h2>%s</h2><hr/>', esc_html__( 'Article Via', 'boombox' ) ),
							'order'           => 70,
						),
						// Via URL
						'boombox_article_via_url' => array(
							'type'            => 'url',
							'label'           => esc_html__( 'Via URL', 'boombox' ),
							'order'           => 80,
						),
						// Via Label
						'boombox_article_via_label' => array(
							'type'            => 'text',
							'label'           => esc_html__( 'Via Label', 'boombox' ),
							'order'           => 90,
						),
						'boombox_article_via_follow' => array(
							'type' => 'radio',
							'label'           => esc_html__( 'Via Follow', 'boombox' ),
							'choices'         => array(
								'follow'    => esc_html__( 'Follow', 'boombox' ),
								'nofollow'  => esc_html__( 'No Follow', 'boombox' ),
							),
							'default'         => 'nofollow',
							'config'          => array( 'axis' => 'horizontal' ),
							'order'           => 100,
						),
						'boombox_article_via_target' => array(
							'label'    => esc_html__( 'Open in a new tab', 'boombox' ),
							'type'     => 'checkbox',
							'default'  => 0,
							'order'    => 110,
						),
						// other fields go here
					),
				),
				// affiliate tab
				'tab_affiliate' => array(
					'title'  => esc_html__( 'Affiliate', 'boombox' ),
					'active' => false,
					'icon'   => false,
					'order'  => 40,
					'fields' => array(
						// Regular Price
						'boombox_post_regular_price'                   => array(
							'type'     => 'text',
							'label'    => esc_html__( 'Regular Price', 'boombox' ),
							'default'  => '',
							'order'    => 20,
							'class'    => 'regular-text',
						),
						// Discount Price
						'boombox_post_discount_price'                  => array(
							'type'     => 'text',
							'label'    => esc_html__( 'Discount Price', 'boombox' ),
							'default'  => '',
							'order'    => 30,
							'class'    => 'regular-text',
						),
						// Affiliate Link
						'boombox_post_affiliate_link'                  => array(
							'type'     => 'text',
							'label'    => esc_html__( 'Affiliate Link', 'boombox' ),
							'default'  => '',
							'order'    => 40,
							'class'    => 'regular-text',
						),
						// Post Link
						'boombox_post_affiliate_link_use_as_post_link' => array(
							'type'     => 'checkbox',
							'text'     => esc_html__( 'Use as post link', 'boombox' ),
							'default'  => 0,
							'order'    => 50,
						),

						// Affiliate Button Text Selector
						'boombox_post_affiliate_button_text_selector'              => array(
							'type'         => 'select',
							'label'        => esc_html__( 'Affiliate Button Text', 'boombox' ),
							'choices'      => array(
								'inherit' => esc_html__( 'Inherit', 'boombox' ),
								'custom'  => esc_html__( 'Custom', 'boombox' ),
							),
							'default'      => 'inherit',
							'order'        => 60,
							'sub_order'    => 20,
						),

						// Affiliate Button Text
						'boombox_post_affiliate_button_text'           => array(
							'type'     => 'text',
							'default'  => '',
							'order'    => 70,
							'class'    => 'regular-text',

							'active_callback' => array(
								array(
									'field_id' => 'boombox_post_affiliate_button_text_selector',
									'value'    => 'custom',
									'compare'  => '=',
								),
							),
						),
						// other fields go here
					)
				)
				// other tabs go here
			);
			
			return apply_filters( 'boombox/admin/post/meta-boxes/structure', $structure, $config[ 'id' ], 'post', $config[ 'context' ] );
		}
		
		/**
		 * Get structure - Side box - main
		 * @return array
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		public function get_structure__side_box__general() {
			
			$config = static::get_config__side_box__general();
			
			$structure = array(
				// global tab
				'tab_global' => array(
					'title'  => esc_html__( 'Global', 'boombox' ),
					'active' => true,
					'icon'   => false,
					'order'  => 20,
					'fields' => array(
						// Featured
						'boombox_is_featured'           => array(
							'type'       => 'checkbox',
							'text'       => esc_html__( 'Featured', 'boombox' ),
							'standalone' => true,
							'order'      => 20,
							'default'    => 0,
						),
						// Featured On Front Page
						'boombox_is_featured_frontpage' => array(
							'type'       => 'checkbox',
							'text'       => esc_html__( 'Featured On Front Page', 'boombox' ),
							'standalone' => true,
							'order'      => 30,
							'val'        => 10,
							'default'    => 0,
						),
						// Keep Trending
						'boombox_keep_trending'         => array(
							'type'       => 'checkbox',
							'text'       => esc_html__( 'Keep Trending', 'boombox' ),
							'standalone' => true,
							'order'      => 40,
							'val'        => PHP_INT_MAX,
							'default'    => 0,
						),
						// Keep Hot
						'boombox_keep_hot'              => array(
							'type'       => 'checkbox',
							'text'       => esc_html__( 'Keep Hot', 'boombox' ),
							'standalone' => true,
							'order'      => 50,
							'val'        => PHP_INT_MAX,
							'default'    => 0,
						),
						// Keep Popular
						'boombox_keep_popular'          => array(
							'type'       => 'checkbox',
							'text'       => esc_html__( 'Keep Popular', 'boombox' ),
							'standalone' => true,
							'order'      => 60,
							'val'        => PHP_INT_MAX,
							'default'    => 0,
						),
						// Visual Post
						'boombox_visual_post'           => array(
							'type'        => 'checkbox',
							'text'        => esc_html__( 'Visual Post', 'boombox' ),
							'description' => esc_html__( 'Visual Post Works For Mixed List', 'boombox' ),
							'standalone'  => true,
							'order'       => 70,
							'default'     => 0,
						),
					)
				)
				// other tabs go here
			);
			
			return apply_filters( 'boombox/admin/post/meta-boxes/structure', $structure, $config[ 'id' ], 'post', $config[ 'context' ] );
		}

		/**
		 * Get structure - Side box
		 * @return array
		 * @since   2.5.0
		 * @version 2.5.0
		 */
		public function get_structure__side_box__gallery() {

			$config = static::get_config__side_box__gallery();

			$structure = array(
				// global tab
				'tab_gallery' => array(
					'title'  => esc_html__( 'Gallery', 'boombox' ),
					'active' => true,
					'icon'   => false,
					'order'  => 20,
					'fields' => array(
						// Featured
						'boombox_post_gallery' => array(
							'type'       => 'gallery',
							'order'      => 20,
							'default'    => array(),
						),
					)
				)
				// other tabs go here
			);

			return apply_filters( 'boombox/admin/post/meta-boxes/structure', $structure, $config[ 'id' ], 'post', $config[ 'context' ] );
		}
		
	}
	
	$instance = Boombox_Post_Metabox::get_instance();
	
	new AIOM_Post_Metabox( $instance->get_config__main_box__general(), array( $instance, 'get_structure__main_box__general' ) );
	new AIOM_Post_Metabox( $instance->get_config__side_box__general(), array( $instance, 'get_structure__side_box__general' ) );
	new AIOM_Post_Metabox( $instance->get_config__side_box__gallery(), array( $instance, 'get_structure__side_box__gallery' ) );

}