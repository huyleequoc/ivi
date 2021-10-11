<?php

/**
 * WC listing shortcode functions
 *
 * @package BoomBox_Theme
 * @since 1.0.0
 * @version 2.1.2
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_VC_Listing' ) ) {

	final class Boombox_VC_Listing {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_VC_Listing|null
		 */
		public static function get_instance() {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Holds shortcode attributes
		 * @var array
		 */
		private $_attributes = array();

		/**
		 * Get shortcode attributes
		 * @return array
		 */
		public function get_attributes() {
			return $this->_attributes;
		}

		/**
		 * Boombox_VC constructor.
		 */
		private function __construct() {
			$this->hooks();
		}

		/**
		 * A dummy magic method to prevent Boombox_VC_Listing from being cloned.
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup Hooks
		 */
		public function hooks() {
			add_shortcode( 'boombox_listing', array( $this, 'render' ) );
			add_action( 'vc_before_init', array( $this, 'init' ) );
		}

		/**
		 * Init
		 */
		public function init() {

			$min = boombox_get_minified_asset_suffix();
			$choices_helper = Boombox_Choices_Helper::get_instance();

			$params = array(
				'name'     => __( 'Boombox Listing', 'boombox' ),
				'base'     => 'boombox_listing',
				'class'    => '',
				'category' => __( 'Boombox', 'boombox' ),
				'admin_enqueue_css' => BOOMBOX_INCLUDES_URL . 'plugins/visual-composer/elements/admin-styles' . $min . '.css',
				'params'   => array(
					//region Posts Loop
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Listing Type', 'boombox' ),
						'value'       => $choices_helper->get_listing_types( 'label=>value' ),
						'std'         => 'grid',
						'param_name'  => 'type',
						'description' => __( 'Choose listing type.', 'boombox' ),
						'group'       => __( 'Posts Loop', 'boombox' )
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Listing Condition', 'boombox' ),
						'value'       => array_flip( $choices_helper->get_conditions() ),
						'std'         => 'recent',
						'param_name'  => 'condition',
						'description' => __( 'Choose listing condition.', 'boombox' ),
						'group'       => __( 'Posts Loop', 'boombox' )
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Listing Time Range', 'boombox' ),
						'value'       => array_flip( $choices_helper->get_time_ranges() ),
						'std'         => 'all',
						'param_name'  => 'time_range',
						'description' => __( 'Choose listing time range.', 'boombox' ),
						'group'       => __( 'Posts Loop', 'boombox' )
					),
					array(
						'type'        => 'autocomplete',
						'heading'     => __( 'Listing Category', 'boombox' ),
						'param_name'  => 'category',
						'std'         => '',
						'description' => __( 'Choose listing category.', 'boombox' ),
						'settings'    => array(
							'multiple'       => true,
							'min_length'     => 0,
							'unique_values'  => true,
							'display_inline' => false,
							'delay'          => 500,
							'auto_focus'     => true,
						),
						'group'       => __( 'Posts Loop', 'boombox' )
					),
					array(
						'type'        => 'autocomplete',
						'heading'     => __( 'Listing Tag', 'boombox' ),
						'param_name'  => 'post_tag',
						'std'         => '',
						'description' => __( 'Choose listing tag.', 'boombox' ),
						'settings'    => array(
							'multiple'       => true,
							'min_length'     => 0,
							'unique_values'  => true,
							'display_inline' => true,
							'delay'          => 500,
							'auto_focus'     => true,
						),
						'group'       => __( 'Posts Loop', 'boombox' )
					),
					array(
						'type'        => 'boombox_number',
						'heading'     => __( 'Posts Count', 'boombox' ),
						'std'         => get_option( 'posts_per_page' ),
						'min'         => 1,
						'param_name'  => 'posts_per_page',
						'description' => __( 'Choose posts count per page.', 'boombox' ),
						'group'       => __( 'Posts Loop', 'boombox' )
					),
					array(
						'type'        => 'boombox_number',
						'heading'     => __( 'Offset', 'boombox' ),
						'std'         => 0,
						'min'         => 1,
						'param_name'  => 'offset',
						'description' => __( 'Choose offset.', 'boombox' ),
						'group'       => __( 'Posts Loop', 'boombox' )
					),
					// endregion

					// region Layout
					array(
						'type'             => 'checkbox',
						'heading'          => __( 'Hide Elements', 'boombox' ),
						'edit_field_class' => 'boombox-vc-multicheck',
						'value'            => array_flip( $choices_helper->get_grid_hide_elements() ),
						'std'              => '',
						'param_name'       => 'hide_elements',
						'group'            => __( 'Layout', 'boombox' )
					),
					array(
						'type'             => 'checkbox',
						'heading'          => __( 'Share Bar Elements', 'boombox' ),
						'edit_field_class' => 'boombox-vc-multicheck',
						'value'            => array_flip( $choices_helper->get_share_bar_elements() ),
						'std'              => array( '' ),
						'param_name'       => 'share_bar_elements',
						'group'            => __( 'Layout', 'boombox' )
					)
					// endregion
				),
			);

			vc_map( $params );
		}

		/**
		 * Callback to setup grid item elements options for shortcode
		 * @hooked in "boombox_grid_template_options" filter
		 * @param array $options Current options
		 *
		 * @return array
		 */
		public function setup_grid_element_options( $options ) {
			$atts = $this->get_attributes();
			foreach( $atts[ 'hide_elements' ] as $element ) {
				$options[ $element ] = false;
			}

			return $options;
		}

		/**
		 * Callback to setup grid item share bar elements for shortcode
		 * @param array $elements Current elements
		 *
		 * @return array
		 */
		public function setup_share_bar_elements( $elements ) {
			$atts = $this->get_attributes();
			$elements = $atts[ 'share_bar_elements' ];

			return $elements;
		}

		/**
		 * Render shortcode
		 *
		 * @param array       $atts    Shortcode attributes
		 * @param null|string $content Shorcode content
		 *
		 * @return string
		 */
		public function render( $atts, $content = NULL ) {

			global $wp_query;
			$tmp_query = $wp_query;

			$atts = shortcode_atts( array(
				'type'               => 'grid',
				'condition'          => 'recent',
				'time_range'         => 'all',
				'category'           => '',
				'post_tag'           => '',
				'posts_per_page'     => get_option( 'posts_per_page' ),
				'offset'             => 0,
				'hide_elements'      => '',
				'share_bar_elements' => ''
			), $atts, 'boombox_listing' );

			if ( 'none' != $atts[ 'type' ] ) {

				$args = array(
					'posts_per_page'      => $atts[ 'posts_per_page' ],
					'offset'              => $atts[ 'offset' ],
					'post_type'           => 'post',
					'posts_count'         => -1,
					'is_grid'             => in_array( $atts[ 'type' ], array( 'grid' ) ),
					'ignore_sticky_posts' => ! is_front_page(),
				);

				$wp_query = boombox_get_posts_query(
					$atts[ 'condition' ],
					$atts[ 'time_range' ],
					array(
						'category' => explode( ', ', $atts[ 'category' ] ),
						'tag' => explode( ', ', $atts[ 'post_tag' ] )
					),
					$args
				);

				if ( is_object( $wp_query ) ) {
					$wp_query->is_page = true;
					$wp_query->is_singular = true;
					$wp_query->is_home = false;
				}

			}

			$this->_attributes = array(
				'hide_elements' 	 => explode( ',', $atts[ 'hide_elements' ] ),
				'share_bar_elements' => explode( ',', $atts[ 'share_bar_elements' ] ),
			);
			add_filter( 'boombox_grid_template_options', array( $this, 'setup_grid_element_options' ) );
			add_filter( 'boombox_posts_share_bar_elements', array( $this, 'setup_share_bar_elements' ) );

			ob_start();

			if ( Boombox_Loop_Helper::have_posts() ) {
				do_action( 'boombox/loop-start', 'vc', array( 'listing_type' => $atts[ 'type' ] ) ); ?>
				<div class="bb-post-collection <?php echo boombox_get_list_type_classes( $atts[ 'type' ], array( 'col-2' ) ); ?>">
					<ul id="post-items" class="post-items">
						<?php
						while ( Boombox_Loop_Helper::have_posts() ) {
							$the_post = Boombox_Loop_Helper::the_post();
							if ( $the_post->is_injected && $the_post->is_adv ) {
								$adv_settings = boombox_get_adv_settings( $atts[ 'type' ] );
								boombox_the_advertisement( $adv_settings[ 'location' ], array(
									'tag' => 'li',
									'class' => array( $adv_settings[ 'class' ], 'post-item' ),
									'in_the_loop' => true,
									'tmp_query' => $tmp_query,
									'cur_query' => $wp_query
								) );
							} else if ( $the_post->is_injected && $the_post->is_newsletter ) {
								echo boombox_get_mailchimp_form_html( array( 'tag' => 'li', 'class' => 'mb-md post-item' ) );
							} else if ( get_the_ID() ) {
								boombox_get_template_part( 'template-parts/listings/content-' . $atts[ 'type' ], get_post_format() );
							}
						} ?>
					</ul>
				</div>
				<?php
				do_action( 'boombox/loop-end', 'vc' );
			}
			$content = ob_get_clean();

			remove_filter( 'boombox_grid_template_options', array( $this, 'setup_grid_element_options' ) );
			remove_filter( 'boombox_posts_share_bar_elements', array( $this, 'setup_share_bar_elements' ) );

			$wp_query = $tmp_query;
			wp_reset_query();

			return $content;

		}

	}

	Boombox_VC_Listing::get_instance();

}