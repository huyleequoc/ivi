<?php
/**
 * WP QUADS plugin functions
 *
 * @package BoomBox_Theme
 * @since 1.0.0
 * @version 2.0.4
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! boombox_plugin_management_service()->is_plugin_active( 'quick-adsense-reloaded/quick-adsense-reloaded.php' ) || ! function_exists( 'quads_register_ad' ) ) {
	return;
}

if( ! class_exists( 'Boombox_WP_Quads' ) ) {

	class Boombox_WP_Quads {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return Boombox_WP_Quads|null
		 */
		public static function get_instance() {

			if (null == static::$_instance) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_WP_Quads constructor.
		 */
		private function __construct() {
			$this->hooks();
			$this->register_ads();

			do_action( 'boombox/wp_quads/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_WP_Quads from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception('Cloning ' . __CLASS__ . ' is forbidden');
		}

		/**
		 * Setup Hooks
		 */
		public function hooks() {
			add_action( 'admin_bar_menu', array( $this, 'simulate_the_content_filter' ), 999 );
			add_action( 'boombox/single/sortables/before_navigation', array( $this, 'render_ad_before_single_navigation' ) );
			add_action( 'boombox/single/sortables/after_comments', array( $this, 'render_ad_after_single_comments' ) );
			add_filter( 'boombox/admin/post/meta-boxes/structure', array( $this, 'edit_page_metaboxes_structure' ), 10, 4 );
			add_filter( 'boombox/customizer/fields/archive_main_posts', array( $this, 'add_inject_settings_to_customizer' ), 10, 3 );
			add_filter( 'boombox/customizer/fields/home_main_posts', array( $this, 'add_inject_settings_to_customizer' ), 10, 3 );
			add_filter( 'boombox/customizer_default_values', array( $this, 'edit_customizer_default_values' ), 10, 1 );
			add_action( 'boombox/before_page_content', array( $this, 'remove_page_ads' ), 10 );
			add_action( 'boombox/after_page_content', array( $this, 'revert_page_ads' ), 10 );
			add_filter( 'boombox/advertisement/hide_ads', array( $this, 'hide_ads_in_ajax_request' ), 10, 1 );

			if( boombox_plugin_management_service()->is_plugin_active( 'wp-quads-pro/wp-quads-pro.php' ) ) {
				add_action( 'pre_option_quads_wp_quads_pro_license_active', array( $this, 'simulate_license' ), 10, 1 );
			}
		}

		/**
		 * Simulate theme in build license
		 * @param mixed $value Current value
		 *
		 * @return stdClass
		 */
		public function simulate_license( $value ) {

			$quads_options = get_option( 'quads_settings' );
			$has_pro_key = isset( $quads_options[ 'quads_wp_quads_pro_license_key' ] ) && $quads_options[ 'quads_wp_quads_pro_license_key' ];
			if( ! $has_pro_key ) {
				$value = new stdClass();
				$value->success = true;
				$value->license = 'valid';
				$value->expires = 'lifetime';
			}

			return $value;
		}

		/**
		 * Simulate 'the_content' filter run for pages that does not have it
		 */
		public function simulate_the_content_filter() {

			global $wp_the_query, $the_content;
			if( $wp_the_query->is_singular() ) {
				$the_content = true;
			}

		}

		/**
		 * Get ads locations
		 * @return array
		 */
		private function get_locations() {

			/***** Header */
			$header = array(
				array(
					'location'    => 'boombox-inside-header',
					'description' => esc_html__( 'Inside Header', 'boombox' ),
				),
				array(
					'location' => 'boombox-after-header',
					'description' => esc_html__( 'After Header', 'boombox' ),
				)
			);

			/***** Index template */
			$home = array(
				array(
					'location'    => 'boombox-index-before-header',
					'description' => esc_html__( 'Home: Before Header', 'boombox' ),
				),
				array(
					'location'    => 'boombox-index-before-featured-area',
					'description' => esc_html__( 'Home: Before Featured Area', 'boombox' ),
				),
				array(
					'location'    => 'boombox-index-after-featured-area',
					'description' => esc_html__( 'Home: After Featured Area', 'boombox' ),
				),
				array(
					'location'    => 'boombox-index-before-content',
					'description' => esc_html__( 'Home: Before Content Theme Area', 'boombox' ),
				),
				array(
					'location'    => 'boombox-index-after-content',
					'description' => esc_html__( 'Home: After Content Theme Area', 'boombox' ),
				),
				array(
					'location'    => 'boombox-index-before-footer',
					'description' => esc_html__( 'Home: Before Footer', 'boombox' ),
				),
			);

			/***** Archive template */
			$archive = array(
				array(
					'location'    => 'boombox-archive-before-header',
					'description' => esc_html__( 'Archive: Before Header', 'boombox' ),
				),
				array(
					'location'    => 'boombox-archive-before-featured-area',
					'description' => esc_html__( 'Archive: Before Featured Area', 'boombox' ),
				),
				array(
					'location'    => 'boombox-archive-after-featured-area',
					'description' => esc_html__( 'Archive: After Featured Area', 'boombox' ),
				),
				array(
					'location'    => 'boombox-archive-before-content',
					'description' => esc_html__( 'Archive: Before Content Theme Area', 'boombox' ),
				),
				array(
					'location'    => 'boombox-archive-after-content',
					'description' => esc_html__( 'Archive: After Content Theme Area', 'boombox' ),
				),
				array(
					'location'    => 'boombox-archive-before-footer',
					'description' => esc_html__( 'Archive: Before Footer', 'boombox' ),
				),
			);

			/***** Page template */
			$page = array(
				array(
					'location'    => 'boombox-page-before-header',
					'description' => esc_html__( 'Page: Before Header', 'boombox' ),
				),
				array(
					'location'    => 'boombox-page-before-featured-area',
					'description' => esc_html__( 'Page: Before Featured Area', 'boombox' ),
				),
				array(
					'location'    => 'boombox-page-after-featured-area',
					'description' => esc_html__( 'Page: After Featured Area', 'boombox' ),
				),
				array(
					'location'    => 'boombox-page-before-content',
					'description' => esc_html__( 'Page: Before Content Theme Area', 'boombox' ),
				),
				array(
					'location'    => 'boombox-page-after-content',
					'description' => esc_html__( 'Page: After Content Theme Area', 'boombox' ),
				),
				array(
					'location'    => 'boombox-page-before-footer',
					'description' => esc_html__( 'Page: Before Footer', 'boombox' ),
				),
			);

			/***** Single template */
			$single = array(
				array(
					'location'    => 'boombox-single-before-header',
					'description' => esc_html__( 'Single: Before Header', 'boombox' ),
				),
				array(
					'location'    => 'boombox-single-before-content',
					'description' => esc_html__( 'Single: Before Content Theme Area', 'boombox' ),
				),
				array(
					'location'    => 'boombox-single-after-next-prev-buttons',
					'description' => esc_html__( 'Single: After "Next/Prev" Buttons', 'boombox' ),
				),
				array(
					'location'    => 'boombox-single-before-navigation',
					'description' => esc_html__( 'Single: Before Navigation Area', 'boombox' ),
				),
				array(
					'location'    => 'boombox-single-after-comments-section',
					'description' => esc_html__( 'Single: After Comments Section', 'boombox' ),
				),
				array(
					'location'    => 'boombox-single-after-also-like-section',
					'description' => esc_html__( 'Single: After "Also Like" Section', 'boombox' ),
				),
				array(
					'location'    => 'boombox-single-after-more-from-section',
					'description' => esc_html__( 'Single: After "More From" Section', 'boombox' ),
				),
				array(
					'location'    => 'boombox-single-after-dont-miss-section',
					'description' => esc_html__( 'Single: After "Don\'t miss" Section', 'boombox' ),
				),
				array(
					'location'    => 'boombox-single-before-footer',
					'description' => esc_html__( 'Single: Before Footer', 'boombox' ),
				),
			);

			/***** Listings template */
			$listing = array(
				array(
					'location'    => 'boombox-listing-type-grid-instead-post',
					'description' => esc_html__( 'Instead of "grid" or "three column" listing post', 'boombox' ),
				),
				array(
					'location'    => 'boombox-listing-type-non-grid-instead-post',
					'description' => esc_html__( 'Instead of none grid listing post', 'boombox' ),
				),
			);

			/***** Gallery popup */
			$gallery = array(
				array(
					'location'    => 'boombox-gallery-popup-header',
					'description' => esc_html__( 'Gallery popup', 'boombox' )
				)
			);

			/***** Sticky Bottom Area */
			$sticky = array(
				array(
					'location'    => 'boombox-sticky-bottom-desktop-area',
					'description' => esc_html__( 'Sticky Bottom Area (Desktop)', 'boombox' )
				),
				array(
					'location'    => 'boombox-sticky-bottom-mobile-area',
					'description' => esc_html__( 'Sticky Bottom Area (Mobile)', 'boombox' )
				)
			);

			$locations = array_merge( $header, $home, $archive, $page, $single, $listing, $gallery, $sticky );

			return apply_filters( 'boombox/custom_ad_locations', $locations );

		}

		/**
		 * Register ads custom areas
		 */
		private function register_ads() {
			$ad_locations = $this->get_locations();
			foreach( (array)$ad_locations as $ad_location ) {
				quads_register_ad( $ad_location );
			}
		}

		/**
		 * Get advertisement for location
		 * @param string $location Current location
		 * @param array $args Advertisement arguments
		 *
		 * @return string
		 */
		public function get_adv( $location, $args = array() ) {

			$html = '';

			$args = wp_parse_args( $args, array(
				'class'       => array(),
				'tag'         => 'aside',
				'tmp_query'   => false,
				'cur_query'   => false,
				'in_the_loop' => false,
				'before'      => '',
				'after'       => '',
			) );

			$hide_ads = false;
			if( is_singular() ){
				$config = boombox_get_post_meta( get_the_ID(), '_quads_config_visibility' );
				if( isset( $config['NoAds'] ) && $config['NoAds'] ){
					$hide_ads = true;
				}
			}

			$hide_ads = apply_filters( 'boombox/advertisement/hide_ads', $hide_ads, $location );
			if( ! $hide_ads ) {

				global $wp_query;
				if ( $args[ 'tmp_query' ] ) {
					$wp_query = $args[ 'tmp_query' ];
				}

				if ( function_exists( 'quads_ad' ) && $location ) {

					if( $args[ 'cur_query' ] ) {
						$query_condition = $args[ 'cur_query' ]->have_posts();
					} elseif( $args[ 'in_the_loop' ] ) {
						$query_condition = true;
					} else {
						$query_condition =  have_posts();
					}

					if ( $query_condition ) {

						$adv = quads_ad( array( 'location' => $location, 'echo' => false ) );

						if ( $adv ) {

							if ( is_array( $args[ 'class' ] ) ) {
								$args[ 'class' ] = trim( implode( ' ', $args[ 'class' ] ) );
							}
							$args[ 'class' ] = 'bb-advertisement ' . $args[ 'class' ];

							$html .= $args[ 'before' ];
							$html .= '<' . $args[ 'tag' ] . ' class="' . esc_attr( $args[ 'class' ] ) . '">';
							$html .= '<div class="inner">' . $adv . '</div>';
							$html .= '</' . $args[ 'tag' ] . '>';
							$html .= $args[ 'after' ];

						}

					}

				}

				if ( $args[ 'cur_query' ] ) {
					$wp_query = $args[ 'cur_query' ];
				}

			}

			return $html;

		}

		/**
		 * Edit page metaboxes structure and add additional fields
		 *
		 * @param array  $structure $structure  Current structure
		 * @param string $id Current instance
		 * @param string $post_type Current post type
		 * @param string $context Meta box context
		 * @return array
		 * @since 2.5.0
		 * @version 2.5.0
		 */
		public function edit_page_metaboxes_structure( $structure, $id, $post_type, $context ) {
			
			if( 'page' == $post_type ) {
				
				$choices_helper = Boombox_Choices_Helper::get_instance();
				$structure[ 'tab_listing' ][ 'fields' ] = array_merge( $structure[ 'tab_listing' ][ 'fields' ], array(
					
					// "Injects" heading
					'boombox_listing_injects_heading' => array(
						'type'            => 'custom',
						'html'            => sprintf( '<h2>%s</h2><hr/>', esc_html__( 'Injects', 'boombox' ) ),
						'order'           => 90,
						'active_callback' => array(
							array(
								'field_id' => 'boombox_listing_type',
								'value'    => 'none',
								'compare'  => '!=',
							),
						),
					),
					// Ad
					'boombox_page_ad'                 => array(
						'type'            => 'select',
						'label'           => esc_html__( 'Ad', 'boombox' ),
						'order'           => 90,
						'sub_order'       => 20,
						'choices'         => $choices_helper->get_injects(),
						'default'         => 'none',
						'active_callback' => array(
							array(
								'field_id' => 'boombox_listing_type',
								'value'    => 'none',
								'compare'  => '!=',
							),
						),
					),
					// Inject Ad As Post
					'boombox_inject_ad_instead_post'  => array(
						'type'            => 'number',
						'label'           => esc_html__( 'Inject Ad As Post', 'boombox' ),
						'order'           => 90,
						'sub_order'       => 30,
						'choices'         => $choices_helper->get_injects(),
						'default'         => 1,
						'attributes'      => array(
							'min' => 1,
						),
						'callback'        => array(
							$this,
							'sanitize_page_metaboxes_ad_instead_post_value'
						),
						'render_callback'  => array(
							$this,
							'render_page_metaboxes_ad_instead_post_value'
						),
						'active_callback' => array(
							'relation' => 'AND',
							array(
								'field_id' => 'boombox_listing_type',
								'value'    => 'none',
								'compare'  => '!=',
							),
							array(
								'field_id' => 'boombox_page_ad',
								'value'    => 'none',
								'compare'  => '!=',
							),
						),
					),
				) );
				
			}

			return $structure;
		}

		/**
		 * Sanitize page metaboxes instead post value on rendering
		 *
		 * @param int $value Current value
		 * @return int
		 * @since 2.5.0
		 * @version 2.5.0
		 */
		public function render_page_metaboxes_ad_instead_post_value( $value ) {
			$post = get_post();
			return max( 1, min( $value, boombox_get_post_meta( $post->ID, 'boombox_posts_per_page' ) ) );
		}

		/**
		 * Sanitize page metaboxes instead post value on saving
		 *
		 * @param mixed $value Current value
		 * @return int
		 * @since 2.5.0
		 * @version 2.5.0
		 */
		public function sanitize_page_metaboxes_ad_instead_post_value( $value ) {
			return min( max( 1, absint( $value ) ), absint( $_POST[ AIOM_Config::get_post_meta_key() ]['boombox_posts_per_page'] ) );
		}

		/**
		 * Render ad before single navigation section
		 */
		public function render_ad_before_single_navigation() {
			if( is_single() ) {
				boombox_the_advertisement( 'boombox-single-before-navigation', array( 'class' => 'large bb-before-nav-area', 'in_the_loop' => true ) );
			}
		}

		/**
		 * Render ad after single comments sections
		 */
		public function render_ad_after_single_comments() {
			boombox_the_advertisement( 'boombox-single-after-comments-section', array( 'class' => 'large bb-after-comments-sec', 'in_the_loop' => true ) );
		}

		/**
		 * Add extra fields to theme customizer
		 *
		 * @param array  $fields   Current fields
		 * @param string $section  Section ID
		 * @param array  $defaults Default values
		 *
		 * @return mixed
		 */
		public function add_inject_settings_to_customizer ( $fields, $section, $defaults ) {
			$priority = false;
			$section_prefix = '';
			if ( $section == boombox_customizer_get_archive_main_posts_section_id() ) {
				$priority = 110;
				$section_prefix = 'archive_main_posts_';
			} else if ( $section == boombox_customizer_get_home_main_posts_section_id() ) {
				$priority = 130;
				$section_prefix = 'home_main_posts_';
			}

			if ( $priority ) {
				$fields = array_merge( $fields, array(
					array(
						'settings' => $section_prefix . 'inject_ad',
						'label'    => esc_html__( 'Ad', 'boombox' ),
						'section'  => $section,
						'type'     => 'select',
						'priority' => $priority,
						'default'  => $defaults[ $section_prefix . 'inject_ad' ],
						'multiple' => 1,
						'choices'  => Boombox_Choices_Helper::get_instance()->get_injects(),
					),
					array(
						'settings' => $section_prefix . 'injected_ad_position',
						'label'    => esc_html__( 'Inject Ad As Post', 'boombox' ),
						'section'  => $section,
						'type'     => 'number',
						'priority' => $priority,
						'default'  => $defaults[ $section_prefix . 'injected_ad_position' ],
						'choices'  => array(
							'min'  => 1,
							'step' => 1,
						),
						'active_callback'    => array(
							array(
								'setting'  => $section_prefix . 'inject_ad',
								'value'    => 'none',
								'operator' => '!=',
							),
						),
					)
				) );
			}

			return $fields;
		}

		/**
		 * Setup default values for customizer extra fields
		 *
		 * @param $values
		 *
		 * @return mixed
		 */
		public function edit_customizer_default_values ( $values ) {
			$section_prefixes = array( 'archive_main_posts_', 'home_main_posts_' );
			foreach ( $section_prefixes as $prefix ) {
				$values[ $prefix . 'inject_ad' ] = 'none';
				$values[ $prefix . 'injected_ad_position' ] = 1;
			}

			return $values;
		}

		/**
		 * Force hide ads
		 * @param $hide
		 *
		 * @return bool
		 */
		public function force_hide( $hide ) {
			$hide = true;

			return $hide;
		}

		/**
		 * Remove page ads
		 */
		public function remove_page_ads() {
			global $post;

			if( ! $post->post_content ) {
				add_filter( 'quads_hide_ads', array( $this, 'force_hide' ), 10, 1 );
			}
		}

		/**
		 * Revert page ads
		 */
		public function revert_page_ads() {
			remove_filter( 'quads_hide_ads', array( $this, 'force_hide' ), 10 );
		}

		/**
		 * Hide ads in ajax request
		 * @param bool $hide Current state
		 *
		 * @return bool
		 */
		public function hide_ads_in_ajax_request( $hide ) {
			if( wp_doing_ajax() && boombox_get_paged() > 1 ) {
				$hide = true;
			}
			return $hide;
		}

	}

	Boombox_WP_Quads::get_instance();

}