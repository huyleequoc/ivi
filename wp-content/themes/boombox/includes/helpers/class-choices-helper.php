<?php
/**
 * Boombox Choices Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Choices_Helper' ) ) {

	final class Boombox_Choices_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Choices_Helper|null
		 */
		public static function get_instance() {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_Choices_Helper constructor.
		 */
		private function __construct() {
			$this->setup_hooks();
		}

		/**
		 * A dummy magic method to prevent Boombox_Choices_Helper from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup hooks
		 */
		private function setup_hooks() {
			add_filter( 'boombox/listing_types_choices', array( $this, 'edit_listing_types_choices' ), 10, 1 );
		}

		/**
		 * Add to page additional listing types choices
		 *
		 * @param array $choices Current choices
		 *
		 * @return array
		 */
		public function edit_listing_types_choices( $choices ) {

			global $current_screen;

			if ( $current_screen && 'page' === $current_screen->id ) {

				array_unshift( $choices, array(
					'label' => esc_html__( 'None ( Show Page Content )', 'boombox' ),
					'value' => 'none',
					'image' => BOOMBOX_THEME_URL . 'images/listings/none.png',
				) );

			}

			return $choices;
		}

		/**
		 * Get published pages
		 * @param bool $allow_nothing Wheather to allow to select nothing
		 *
		 * @return array
		 */
		public function get_published_pages( $allow_nothing = true ) {
			$published_pages = Kirki_Helper::get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => -1 ) );
			if( $allow_nothing ) {
				$published_pages = array( 0 => __( '-- None --', 'boombox' ) ) + $published_pages;
			}

			return $published_pages;
		}

		/**
		 * Get featured area hide elements choices
		 * @return array
		 */
		public function get_featured_area_hide_elements() {
			$choices = array(
				'badges'      => __( 'Badges', 'boombox' ),
				'share_count' => __( 'Share Count', 'boombox' ),
				'views_count' => __( 'Views Count', 'boombox' ),
				'votes_count' => __( 'Votes Count', 'boombox' ),
				'post_title'  => __( 'Title', 'boombox' ),
				'author'      => __( 'Author', 'boombox' ),
				'date'        => __( 'Date', 'boombox' ),
			);

			return apply_filters( 'boombox_featured_area_hide_elements_choices', $choices );
		}

		/**
		 * Get featured area types
		 * @return array
		 */
		public function get_featured_area_types() {
			$choices = array(
				'disable'                => BOOMBOX_THEME_URL . 'images/featured-area/disable.png',
				'type-1long'             => BOOMBOX_THEME_URL . 'images/featured-area/type-1l.png',
				'type-1-with-newsletter' => BOOMBOX_THEME_URL . 'images/featured-area/type-1-with-newsletter.png',
				'type-1-1'               => BOOMBOX_THEME_URL . 'images/featured-area/type-1-1.png',
				'type-1-1small'          => BOOMBOX_THEME_URL . 'images/featured-area/type-1-1s.png',
				'type-1-1-landscape'     => BOOMBOX_THEME_URL . 'images/featured-area/type-1-1-landscape.png',
				'type-1-1-1'             => BOOMBOX_THEME_URL . 'images/featured-area/type-1-1-1.png',
				'type-1-1-1-stretched'   => BOOMBOX_THEME_URL . 'images/featured-area/type-1-1-1-stretched.png',
				'type-1-2'               => BOOMBOX_THEME_URL . 'images/featured-area/type-1-2.png',
				'type-1-2-1'             => BOOMBOX_THEME_URL . 'images/featured-area/type-1-2-1.png',
				'type-1-2-1-stretched'   => BOOMBOX_THEME_URL . 'images/featured-area/type-1-2-1-stretched.png',
				'type-1-3'               => BOOMBOX_THEME_URL . 'images/featured-area/type-1-3.png',
				'type-1-4'               => BOOMBOX_THEME_URL . 'images/featured-area/type-1-4.png',
				'type-1-4-stretched'     => BOOMBOX_THEME_URL . 'images/featured-area/type-1-4-stretched.png',
			);

			return apply_filters( 'boombox_featured_type_choices', $choices );
		}

		/**
		 * Get grid hide elements choices
		 * @return array
		 */
		public function get_grid_hide_elements() {
			$choices = array(
				'share_count'      => esc_html__( 'Shares Count', 'boombox' ),
				'views_count'      => esc_html__( 'Views Count', 'boombox' ),
				'votes_count'      => esc_html__( 'Votes Count', 'boombox' ),
				'categories'       => esc_html__( 'Categories', 'boombox' ),
				'tags'             => esc_html__( 'Tags', 'boombox' ),
				'comments_count'   => esc_html__( 'Comments Count', 'boombox' ),
				'title'            => esc_html__( 'Title', 'boombox' ),
				'media'            => esc_html__( 'Media', 'boombox' ),
				'subtitle'         => esc_html__( 'Subtitle', 'boombox' ),
				'author'           => esc_html__( 'Author', 'boombox' ),
				'date'             => esc_html__( 'Date', 'boombox' ),
				'excerpt'          => esc_html__( 'Excerpt (for Classic Listing Types)', 'boombox' ),
				'badges'           => esc_html__( 'Badges', 'boombox' ),
				'post_type_badges' => esc_html__( 'Post Type Badges', 'boombox' ),
				'share_bar'        => esc_html__( 'Share Bar', 'boombox' ),
			);

			return apply_filters( 'boombox_grid_hide_elements_choices', $choices );
		}

		/**
		 * Get share bar elements choices
		 * @return array
		 */
		public function get_share_bar_elements() {
			$choices = array(
				'share_count' => __( 'Share Count', 'boombox' ),
				'comments'    => __( 'Comments', 'boombox' ),
				'points'      => __( 'Points', 'boombox' ),
			);

			return apply_filters( 'boombox_share_bar_elements_choices', $choices );
		}

		/**
		 * Get grid hide elements choices for mobile
		 *
		 * @return array
		 */
		public function get_mobile_grid_hide_elements() {
			$choices = array(
				'share_count'      => esc_html__( 'Shares Count', 'boombox' ),
				'views_count'      => esc_html__( 'Views Count', 'boombox' ),
				'votes_count'      => esc_html__( 'Votes Count', 'boombox' ),
				'categories'       => esc_html__( 'Categories', 'boombox' ),
				'comments_count'   => esc_html__( 'Comments Count', 'boombox' ),
				'media'            => esc_html__( 'Media', 'boombox' ),
				'subtitle'         => esc_html__( 'Subtitle', 'boombox' ),
				'author'           => esc_html__( 'Author', 'boombox' ),
				'date'             => esc_html__( 'Date', 'boombox' ),
				'excerpt'          => esc_html__( 'Excerpt (for Classic Listing Types)', 'boombox' ),
				'badges'           => esc_html__( 'Badges', 'boombox' ),
				'post_type_badges' => esc_html__( 'Post Type Badges', 'boombox' ),
			);

			return apply_filters( 'boombox/mobile_grid_hide_elements_choices', $choices );
		}

		/**
		 * Get post hide elements choices for mobile
		 * @return array
		 */
		public function get_mobile_post_hide_elements() {
			$choices = array(
				'subtitle'            => esc_html__( 'Subtitle', 'boombox' ),
				'author'              => esc_html__( 'Author', 'boombox' ),
				'date'                => esc_html__( 'Date', 'boombox' ),
				'categories'          => esc_html__( 'Categories', 'boombox' ),
				'media'               => esc_html__( 'Media', 'boombox' ),
				'comments_count'      => esc_html__( 'Comments Count', 'boombox' ),
				'views'               => esc_html__( 'Views', 'boombox' ),
				'badges'              => esc_html__( 'Badges', 'boombox' ),
				'tags'                => esc_html__( 'Tags', 'boombox' ),
				'author_info'         => esc_html__( 'Author Info', 'boombox' ),
				'reactions'           => esc_html__( 'Reactions', 'boombox' ),
				'subscribe_form'      => esc_html__( 'Subscribe Form', 'boombox' ),
				'navigation'          => esc_html__( 'WP Navigation', 'boombox' ),
				'comments'            => esc_html__( 'WP Comments', 'boombox' ),
				'floating_navbar'     => esc_html__( 'Floating Navbar', 'boombox' ),
				'top_sharebar'        => esc_html__( 'Top Sharebar', 'boombox' ),
				'sticky_top_sharebar' => esc_html__( 'Sticky Top Sharebar', 'boombox' ),
				'bottom_sharebar'     => esc_html__( 'Bottom Sharebar', 'boombox' ),
				'next_prev_buttons'   => esc_html__( 'Next / Prev Buttons', 'boombox' ),
				'side_navigation'     => esc_html__( 'Side Navigation', 'boombox' ),
			);

			return apply_filters( 'boombox_mobile_post_hide_elements_choices', $choices );
		}

		/**
		 * Get single post hide elements choices
		 * @return array
		 */
		public function get_post_hide_elements() {

			$choices = array(
				'subtitle'       => esc_html__( 'Subtitle', 'boombox' ),
				'author'         => esc_html__( 'Author', 'boombox' ),
				'date'           => esc_html__( 'Date', 'boombox' ),
				'categories'     => esc_html__( 'Categories', 'boombox' ),
				'comments_count' => esc_html__( 'Comments Count', 'boombox' ),
				'views'          => esc_html__( 'Views', 'boombox' ),
				'badges'         => esc_html__( 'Badges', 'boombox' ),
				'tags'           => esc_html__( 'Tags', 'boombox' ),
			);

			return apply_filters( 'boombox_post_hide_elements_choices', $choices );
		}

		/**
		 * Get sidebar type choices
		 * @return array
		 */
		public function get_sidebar_types() {
			$choices = array(
				'1-sidebar-1_3'        => BOOMBOX_THEME_URL . 'images/sidebar/1-sidebar-1_3.png',
				'1-sidebar-1_4'        => BOOMBOX_THEME_URL . 'images/sidebar/1-sidebar-1_4.png',
				'2-sidebars-1_4-1_4'   => BOOMBOX_THEME_URL . 'images/sidebar/2-sidebars-1_4-1_4.png',
				'2-sidebars-small-big' => BOOMBOX_THEME_URL . 'images/sidebar/2-sidebars-small-big.png',
				'no-sidebar'           => BOOMBOX_THEME_URL . 'images/sidebar/no-sidebar.png',
			);

			return apply_filters( 'boombox/sidebar_type_choices', $choices );
		}

		/**
		 * Get sidebar orientation choices
		 * @return array
		 */
		public function get_sidebar_orientation() {
			$choices = array(
				'left'  => esc_html__( 'Left', 'boombox' ),
				'right' => esc_html__( 'Right', 'boombox' ),
			);

			return apply_filters( 'boombox/sidebar_orientation_choices', $choices );
		}

		/**
		 * Get primary sidebar choicess
		 * @return array
		 */
		public function get_primary_sidebars() {
			$choices = array(
				'default-sidebar' => __( 'Default', 'boombox' ),
				'page-sidebar-1'  => __( 'Page 1', 'boombox' ),
				'page-sidebar-2'  => __( 'Page 2', 'boombox' ),
				'page-sidebar-3'  => __( 'Page 3', 'boombox' ),
			);

			return apply_filters( 'boombox/primary_sidebar_choices', $choices );
		}

		/**
		 * Get secondary sidebar choices
		 * @return array
		 */
		public function get_secondary_sidebars() {
			$choices = array(
				'page-secondary' => __( 'Secondary', 'boombox' ),
				'page-sidebar-1' => __( 'Page 1', 'boombox' ),
				'page-sidebar-2' => __( 'Page 2', 'boombox' ),
				'page-sidebar-3' => __( 'Page 3', 'boombox' ),
			);

			return apply_filters( 'boombox/secondary_sidebar_choices', $choices );
		}

		/**
		 * Get listing type choices
		 *
		 * @param string $type Return type numeric | assoc
		 * @param array $exclude Items to exclude
		 *
		 * @return array
		 */
		public function get_listing_types( $type = 'value=>label', $exclude = array() ) {

			$choices = array(
				array(
					'label' => esc_attr__( 'Four Column', 'boombox' ),
					'value' => 'four-column',
					'image' => BOOMBOX_THEME_URL . 'images/listings/grid-small.png',
				),
				array(
					'label' => esc_attr__( 'Grid', 'boombox' ),
					'value' => 'grid',
					'image' => BOOMBOX_THEME_URL . 'images/listings/grid-large.png',
				),
				array(
					'label' => esc_attr__( 'Grid 2:1', 'boombox' ),
					'value' => 'grid-2-1',
					'image' => BOOMBOX_THEME_URL . 'images/listings/grid-2-1.png',
				),
				array(
					'label' => esc_attr__( 'List', 'boombox' ),
					'value' => 'list',
					'image' => BOOMBOX_THEME_URL . 'images/listings/list-large.png',
				),
				array(
					'label' => esc_attr__( 'List 2 ( small list )', 'boombox' ),
					'value' => 'list2',
					'image' => BOOMBOX_THEME_URL . 'images/listings/list-small.png',
				),
				array(
					'label' => esc_attr__( 'Classic', 'boombox' ),
					'value' => 'classic',
					'image' => BOOMBOX_THEME_URL . 'images/listings/classic.png',
				),
				array(
					'label' => esc_attr__( 'Classic 2 ( fixed height )', 'boombox' ),
					'value' => 'classic2',
					'image' => BOOMBOX_THEME_URL . 'images/listings/classic-fixed-height.png',
				),
				array(
					'label' => esc_attr__( 'Stream ( gif\'s & memes )', 'boombox' ),
					'value' => 'stream',
					'image' => BOOMBOX_THEME_URL . 'images/listings/stream.png',
				),
				array(
					'label' => esc_attr__( 'Mixed', 'boombox' ),
					'value' => 'mixed',
					'image' => BOOMBOX_THEME_URL . 'images/listings/mixed.png',
				),
				array(
					'label' => esc_attr__( 'Masonry: Boxed', 'boombox' ),
					'value' => 'masonry-boxed',
					'image' => BOOMBOX_THEME_URL . 'images/listings/masonry-boxed.png',
				),
				array(
					'label' => esc_attr__( 'Masonry: Stretched', 'boombox' ),
					'value' => 'masonry-stretched',
					'image' => BOOMBOX_THEME_URL . 'images/listings/masonry-stretched.png',
				),
			);

			/**** Let others to add choices */
			$choices = (array)apply_filters( 'boombox/listing_types_choices', $choices );

			if ( ! $type ) {
				$type = 'all';
			}

			if ( 'all' != $type ) {
				$type_array = explode( '=>', $type );
				if ( count( $type_array ) > 1 ) {

					if ( 'value=>image' != $type ) {
						$index   = $type_array[0];
						$field   = $type_array[1];
						$choices = wp_list_pluck( $choices, $field, $index );
					} else {
						$choices = $this->radio_image_list_pluck( $choices );
					}


					if ( ! empty( $exclude ) ) {
						$choices = array_diff_key( $choices, array_flip( $exclude ) );
					}
				}
			}

			return $choices;
		}


		/**
		 * Pluck image src and label from $choices
		 *
		 * @param $choices
		 *
		 * @return array
		 */
		public function radio_image_list_pluck( $choices ) {
			$result = array();

			if ( ! empty( $choices ) && is_array( $choices ) ) {
				foreach ( $choices as $key => $val ) {
					$result[ $val['value'] ] =
						array(
							'src'   => $val['image'],
							'label' => $val['label']
						);
				}
			}

			return $result;
		}

		/**
		 * Get conditions choices
		 * @return array
		 */
		public function get_conditions() {
			$choices = array();
			if( boombox_module_management_service()->is_module_active( 'prs' ) ) {
				$choices = Boombox_Rate_Criteria::get_criteria_names();
			}
			$choices = array_merge( $choices, array(
				'recent'   => esc_html__( 'Recent', 'boombox' ),
				'featured' => esc_html__( 'Featured', 'boombox' ),
				'random'   => esc_html__( 'Random', 'boombox' ),
			) );

			return apply_filters( 'boombox_conditions_choices', $choices );
		}

		/**
		 * Get trending conditions choices
		 * @return array
		 */
		public function get_trending_conditions() {
			$choices = array();
			if( boombox_module_management_service()->is_module_active( 'prs' ) ) {
				$choices = Boombox_Rate_Criteria::get_criteria_names();
			}

			return apply_filters( 'boombox_trending_conditions_choices', $choices );
		}

		/**
		 * Get time range choices
		 *
		 * @return array
		 */
		public function get_time_ranges() {

			$choices = array();
			if( boombox_module_management_service()->is_module_active( 'prs' ) ) {
				$choices = Boombox_Rate_Time_Range::get_time_range_names();
			}

			return apply_filters( 'boombox_time_range_choices', $choices );
		}

		/**
		 * Get pagination types
		 * @return array
		 */
		public function get_pagination_types() {
			$choices = array(
				'load_more'                 => esc_html__( 'Load More', 'boombox' ),
				'infinite_scroll'           => esc_html__( 'Infinite Scroll', 'boombox' ),
				'infinite_scroll_on_demand' => esc_html__( 'Infinite Scroll (first load via click)', 'boombox' ),
				'pages'                     => esc_html__( 'Numbering', 'boombox' ),
				'next_prev'                 => esc_html__( 'Next/Prev Buttons', 'boombox' ),
			);

			return apply_filters( 'boombox_pagination_choices', $choices );
		}

		/**
		 * Return list of categories
		 *
		 * @return array
		 */
		public function get_categories() {
			$choices = array(
				'' => esc_html__( 'None', 'boombox' )
			);

			$categories = get_categories( 'hide_empty=0' );
			foreach ( $categories as $category ) {
				$choices[ $category->slug ] = $category->name;
			}

			return $choices;
		}

		/**
		 * Get list of tags
		 *
		 * @return array
		 */
		public function get_tags() {
			$choices = array(
				'' => esc_html__( 'None', 'boombox' )
			);

			$tags = get_tags( 'hide_empty=0' );
			foreach ( $tags as $tag ) {
				$choices[ $tag->slug ] = $tag->name;
			}

			return $choices;
		}

		/**
		 * Get default fonts
		 *
		 * @return array
		 */
		public function get_default_fonts() {
			$choices = array(
				'Georgia, serif'                                       => 'Georgia, serif',
				'"Palatino Linotype", "Book Antiqua", Palatino, serif' => 'Palatino Linotype, "Book Antiqua", Palatino, serif',
				'"Times New Roman", Times, serif'                      => 'Times New Roman, Times, serif',
				'Arial, Helvetica'                                     => 'Arial, Helvetica, sans-serif',
				'"Arial Black", Gadget'                                => 'Arial Black, Gadget, sans-serif',
				'"Comic Sans MS", cursive'                             => 'Comic Sans MS, cursive, sans-serif',
				'Impact, Charcoal'                                     => 'Impact, Charcoal, sans-serif',
				'"Lucida Sans Unicode", "Lucida Grande"'               => 'Lucida Sans Unicode, Lucida Grande, sans-serif',
				'Tahoma, Geneva'                                       => 'Tahoma, Geneva, sans-serif',
				'"Trebuchet MS", Helvetica'                            => 'Trebuchet MS, Helvetica, sans-serif',
				'Verdana, Geneva'                                      => 'Verdana, Geneva, sans-serif',
			);

			return apply_filters( 'booombox/default_font_choices', $choices );
		}

		/**
		 * Get injection choices
		 * @return array
		 */
		public function get_injects() {
			$choices = array(
				'inject_into_posts_list' => esc_html__( 'Inject Into Posts List', 'boombox' ),
				'none'                   => esc_html__( 'None', 'boombox' ),
			);

			return apply_filters( 'boombox_page_ad_choices', $choices );
		}

		/**
		 * Get post featured image choices
		 *
		 * @return array
		 */
		public function get_post_featured_image_appearance() {
			$choices = array(
				'customizer' => esc_html__( 'Customizer Global Value', 'boombox' ),
				'show'       => esc_html__( 'Show', 'boombox' ),
				'hide'       => esc_html__( 'Hide', 'boombox' ),
			);

			return apply_filters( 'boombox/post/featured_images_choices', $choices );
		}

		/**
		 * Get strip visibility choices
		 * @return array
		 */
		public function get_strip_visibilities() {
			$choices = array(
				'home'    => __( 'Home', 'boombox' ),
				'archive' => __( 'Archive', 'boombox' ),
				'post'    => __( 'Single Post', 'boombox' ),
				'page'    => __( 'Page', 'boombox' )
			);

			return apply_filters( 'boombox/strip_visibility_choices', $choices );
		}

		/**
		 * Get strip configuration choices
		 * @return array
		 */
		public function get_strip_configurations() {
			$choices = array(
				'inherit'  => esc_html__( 'Inherit', 'boombox' ),
				'custom'   => esc_html__( 'Custom', 'boombox' ),
			);

			return apply_filters( 'boombox/strip_configurations_choices', $choices );
		}

		/**
		 * Get strip sizes choices
		 * @return array
		 */
		public function get_strip_sizes() {
			$choices = array(
				'small' => esc_html__( 'Small', 'boombox' ),
				'big'   => esc_html__( 'Big', 'boombox' ),
			);

			return apply_filters( 'boombox/strip_sizes_choices', $choices );
		}

		/**
		 * Get strip title position choices
		 * @return array
		 */
		public function get_strip_title_positions() {
			$choices = array(
				'inside'  => esc_html__( 'Inside', 'boombox' ),
				'outside' => esc_html__( 'Outside', 'boombox' ),
			);

			return apply_filters( 'boombox/strip_title_positions_choices', $choices );
		}

		/**
		 * Get strip dimension choices
		 */
		public function get_strip_dimensions() {
			$choices = array(
				'boxed'      => esc_html__( 'Boxed', 'boombox' ),
				'full_width' => esc_html__( 'Full Width', 'boombox' ),
			);

			return apply_filters( 'boombox/strip_dimension_choices', $choices );
		}

		/**
		 * Get strip type choices
		 * @return array
		 */
		public function get_strip_types() {
			$choices = array(
				'scrollable' => esc_html__( 'Scrollbox', 'boombox' ),
				'slider'     => esc_html__( 'Slider', 'boombox' ),
			);

			return apply_filters( 'boombox/strip_type_choices', $choices );
		}

		/**
		 * Get "View Full Post" button appearance choices
		 * @return array
		 */
		public function get_view_full_post_button_appearance_conditions() {
			$choices = array(
				'image_ratio'  => esc_html__( 'Image ratio is 1:3', 'boombox' ),
				'post_content' => esc_html__( 'Post has content', 'boombox' ),
			);

			return apply_filters( 'boombox_disable_view_full_post_button_choices', $choices );
		}

		/**
		 * Get "MP4" video control choices
		 * @return array
		 */
		public function get_mp4_video_player_controls() {
			$choices = array(
				'mute'          => esc_html__( 'Only mute', 'boombox' ),
				'full_controls' => esc_html__( 'Full controls', 'boombox' ),
			);

			return apply_filters( 'boombox_mp4_video_player_controls_choices', $choices );
		}

		/**
		 * Get "MP4" video auto play choices
		 * @return array
		 */
		public function get_mp4_video_player_auto_plays() {
			$choices = array(
				'scroll' => esc_html__( 'On Scroll', 'boombox' ),
				'hover'  => esc_html__( 'On Hover', 'boombox' ),
				'none'   => esc_html__( 'None', 'boombox' ),
			);

			return apply_filters( 'boombox_mp4_video_player_autoplay_choices', $choices );
		}

		/**
		 * Get "MP4" video player sound choices
		 * @return array
		 */
		public function get_mp4_video_player_sound_options() {
			$choices = array(
				'muted'      => esc_html__( 'Muted', 'boombox' ),
				'with_sound' => esc_html__( 'With Sound', 'boombox' ),
			);

			return apply_filters( 'boombox_mp4_video_player_sound_choices', $choices );
		}

		/**
		 * Get "MP4" video player click event handler choices
		 * @return array
		 */
		public function get_mp4_video_player_click_event_handlers() {
			$choices = array(
				'mute_unmute' => esc_html__( 'Mute / Unmute', 'boombox' ),
				'play_pause'  => esc_html__( 'Play / Pause', 'boombox' ),
			);

			return apply_filters( 'boombox_mp4_video_player_click_event_handler_choices', $choices );
		}

		/**
		 * Get post sortable sections choices
		 */
		public function get_post_sortable_sections() {
			$choices = array(
				'reactions'       => esc_html__( 'Reactions', 'boombox' ),
				'author_info'     => esc_html__( 'Author Info', 'boombox' ),
				'comments'        => esc_html__( 'WP Comments', 'boombox' ),
				'navigation'      => esc_html__( 'WP Navigation', 'boombox' ),
				'related_posts'   => esc_html__( '"Related Posts" Section', 'boombox' ),
				'more_from_posts' => esc_html__( '"More From" Section', 'boombox' ),
				'dont_miss_posts' => esc_html__( '"Don\'t Miss" Section', 'boombox' ),
			);

			return apply_filters( 'boombox/single_post/sortable_section_choices', $choices );
		}

		/**
		 * Get post template choices
		 * @return array
		 */
		public function get_post_templates() {
			$choices = array(
				'left-sidebar'  => esc_html__( 'Left Sidebar', 'boombox' ),
				'right-sidebar' => esc_html__( 'Right Sidebar', 'boombox' ),
				'no-sidebar'    => esc_html__( 'No Sidebar', 'boombox' ),
			);

			return apply_filters( 'boombox_post_template_choices', $choices );
		}

		/**
		 * Get header composition choices
		 * @return array
		 */
		public function get_header_compositions() {
			$choices = array(
				'brand-l_menu-l' => BOOMBOX_THEME_URL . 'images/header-componsitions/brand-l_menu-l.png',
				'brand-l_menu-c' => BOOMBOX_THEME_URL . 'images/header-componsitions/brand-l_menu-c.png',
				'brand-l_menu-r' => BOOMBOX_THEME_URL . 'images/header-componsitions/brand-l_menu-r.png',
				'brand-c'        => BOOMBOX_THEME_URL . 'images/header-componsitions/brand-c.png',
				'brand-l_ad-r'   => BOOMBOX_THEME_URL . 'images/header-componsitions/brand-l_ad-r.png',
			);

			return apply_filters( 'boombox/header_composition_choices', $choices );
		}

		/**
		 * Get header composition component choices
		 * @return array
		 */
		public function get_header_composition_component_choices() {
			$choices = array(
				'search'          => __( 'Search Icon', 'boombox' ),
				'social'          => __( 'Social Icon', 'boombox' ),
				'authentication'  => __( 'Authentication', 'boombox' ),
				'badges'          => __( 'Badges menu', 'boombox' ),
				'button-compose'  => __( '"Compose" Button', 'boombox' ),
				'burger-icon'     => __( 'Burger Icon', 'boombox' ),
				'mode-switcher'   => __( 'Color Mode Switcher', 'boombox' ),
			);

			return apply_filters( 'boombox/header_composition_component_choices', $choices );
		}

		/**
		 * Get template header style choices
		 * @return array
		 */
		public function get_template_header_style_choices() {
			$choices = array(
				'style1' => __( 'Style 1', 'boombox' ),
				'style2' => __( 'Style 2', 'boombox' ),
				'style3' => __( 'Style 3', 'boombox' ),
			);

			return apply_filters( 'boombox/template_header_style_choices', $choices );
		}

		/**
		 * Get template header background container choices
		 * @return array
		 */
		public function get_template_header_background_container_choices() {
			$choices = array(
				'boxed'     => __( 'Boxed', 'boombox' ),
				'stretched' => __( 'Stretched', 'boombox' ),
			);

			return apply_filters( 'boombox/template_header_background_container_choices', $choices );
		}

		/**
		 * Get template header background type choices
		 * @return array
		 */
		public function get_template_header_background_type_choices() {
			$choices = array(
				'none'     => __( 'None', 'boombox' ),
				'color'    => __( 'Colour', 'boombox' ),
				'gradient' => __( 'Gradient', 'boombox' ),
				'image'    => __( 'Image', 'boombox' ),
			);

			return apply_filters( 'boombox/template_header_background_type_choices', $choices );
		}

		/**
		 * Get template header background gradient direction choices
		 * @return array
		 */
		public function get_template_header_background_gradient_direction_choices() {
			$choices = array(
				'top'    => __( 'Top', 'boombox' ),
				'right'  => __( 'Right', 'boombox' ),
				'bottom' => __( 'Bottom', 'boombox' ),
				'left'   => __( 'Left', 'boombox' ),
			);

			return apply_filters( 'boombox/template_header_background_gradient_direction_choices', $choices );
		}

		/**
		 * Get template header background image size choices
		 * @return array
		 */
		public function get_template_header_background_image_size_choices() {
			$choices = array(
				'auto'  => __( 'Auto', 'boombox' ),
				'cover' => __( 'Cover', 'boombox' ),
			);

			return apply_filters( 'boombox/template_header_background_image_size_choices', $choices );
		}

		/**
		 * Get template header background image position choices
		 * @return array
		 */
		public function get_template_header_background_image_position_choices() {
			$choices = array(
				'center' => __( 'Center', 'boombox' ),
				'left'   => __( 'Left', 'boombox' ),
				'right'  => __( 'Right', 'boombox' ),
			);

			return apply_filters( 'boombox/template_header_background_image_position_choices', $choices );
		}

		/**
		 * Get template header background image position choices
		 * @return array
		 */
		public function get_template_header_background_image_repeat_choices() {
			$choices = array(
				'repeat-no' => __( 'No Repeat', 'boombox' ),
				'repeat'    => __( 'Repeat All', 'boombox' ),
				'repeat-x'  => __( 'Repeat Horizontally', 'boombox' ),
				'repeat-y'  => __( 'Repeat Vertically', 'boombox' ),
			);

			return apply_filters( 'boombox/template_header_background_image_repeat_choices', $choices );
		}

		/**
		 * Get header typography choices
		 * @return array
		 */
		public function get_header_typography_configuration_choices() {
			$choices = array(
				'inherit' => __( 'Inherit', 'boombox' ),
				'custom'  => __( 'Custom', 'boombox' ),
			);

			return apply_filters( 'boombox/header_typography_choices', $choices );
		}

		/**
		 * Get captcha type choices
		 * @return array
		 */
		public function get_captcha_type_choices() {
			$choices = array(
				'image'     => __( 'Image Captcha', 'boombox' ),
				'google'    => __( 'Google Recaptcha', 'boombox' ),
				'google_v3' => __( 'Google Recaptcha V3', 'boombox' )
			);

			return apply_filters( 'boombox/captcha_choices', $choices );
		}

		/**
		 * Get view count style choices
		 * @return array
		 */
		public function get_view_count_style_choices() {
			$choices = array(
				'rounded'   => __( 'Rounded', 'boombox' ),
				'full'      => __( 'Full', 'boombox' ),
			);

			return apply_filters( 'boombox/view_count_style_choices', $choices );
		}

		/**
		 * Get single post template types
		 * @return array
		 */
		public function get_single_templates() {
			$choices = array(
				'style1'    => BOOMBOX_THEME_URL . 'images/single/style1.jpg',
				'style2'    => BOOMBOX_THEME_URL . 'images/single/style2.jpg',
				'style3'    => BOOMBOX_THEME_URL . 'images/single/style3.jpg',
				'style4'    => BOOMBOX_THEME_URL . 'images/single/style4.jpg',
				'style5'    => BOOMBOX_THEME_URL . 'images/single/style5.jpg',
				'style6'    => BOOMBOX_THEME_URL . 'images/single/style6.jpg',
			);

			return apply_filters( 'boombox_single_template_choices', $choices );
		}

		// region Mobile
		/**
		 * Get mobile header composition choices
		 * @return array
		 */
		public function get_mobile_header_composition_choices() {
			$choices = array(
				'brand-l' => BOOMBOX_THEME_URL . 'images/mobile-header-compositions/brand-l.png',
				'brand-c' => BOOMBOX_THEME_URL . 'images/mobile-header-compositions/brand-c.png',
				'brand-t' => BOOMBOX_THEME_URL . 'images/mobile-header-compositions/brand-t.png',
				'brand-b' => BOOMBOX_THEME_URL . 'images/mobile-header-compositions/brand-b.png'
			);

			return apply_filters( 'boombox/mobile_header_composition_choices', $choices );
		}

		/**
		 * Get mobile header components choices
		 * @return array
		 */
		public function get_mobile_header_composition_component_choices() {
			$choices = array(
				'search'          => __( 'Search Icon', 'boombox' ),
				'social'          => __( 'Social Icon', 'boombox' ),
				'authentication'  => __( 'Authentication', 'boombox' ),
				'burger-icon'     => __( 'Burger Icon', 'boombox' ),
				'mode-switcher'   => __( 'Mode Switcher', 'boombox' ),
			);

			return apply_filters( 'boombox/mobile_header_composition_component_choices', $choices );
		}
		// endregion

		// region GDPR
		public function get_gdpr_checkox_visibility_choices() {
			$choices = array(
				'sign_up'       => __( 'Registration Popup Form', 'boombox' )
			);

			return apply_filters( 'boombox/gdpr_checkbox_visibility_choices', $choices );
		}
		// endregion

		public function get_image_sizes_choices() {
			$choices = array(
				// 200x150   -> crop
				array(
					'name'   => 'boombox_image200x150',
					'label'  => '200 x 150',
					'width'  => 200,
					'height' => 150,
					'crop'   => true,
					'has_2x' => true,
				),
				// 360xAuto  -> resize
				array(
					'name'   => 'boombox_image360',
					'label'  => '360 x Auto',
					'width'  => 360,
					'height' => 0,
					'crop'   => false,
					'has_2x' => true,
				),
				// 360x180   -> crop
				array(
					'name'   => 'boombox_image360x180',
					'label'  => '360 x 180',
					'width'  => 360,
					'height' => 180,
					'crop'   => true,
					'has_2x' => true,
				),
				// 360x270   -> crop
				array(
					'name'   => 'boombox_image360x270',
					'label'  => '360 x 270',
					'width'  => 360,
					'height' => 270,
					'crop'   => true,
					'has_2x' => true,
				),
				// 545xAuto  -> resize
				array(
					'name'   => 'boombox_image545',
					'label'  => '545 x Auto',
					'width'  => 545,
					'height' => 0,
					'crop'   => false,
					'has_2x' => true,
				),
				// 768x450   -> crop
				array(
					'name'   => 'boombox_image768x450',
					'label'  => '768 x 450',
					'width'  => 768,
					'height' => 450,
					'crop'   => true,
					'has_2x' => true,
				),
				// 768xAuto  -> resize
				array(
					'name'   => 'boombox_image768',
					'label'  => '768 x Auto',
					'width'  => 768,
					'height' => 0,
					'crop'   => false,
					'has_2x' => true,
				),
				// 1600xAuto -> resize
				array(
					'name'   => 'boombox_image1600',
					'label'  => '1600 x Auto',
					'width'  => 1600,
					'height' => 0,
					'crop'   => false,
					'has_2x' => false,
				),
			);

			return apply_filters( 'boombox/image_sizes_choices', $choices );
		}

	}

}