<?php
/**
 * Boombox Featured Strip Template Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Featured_Strip_Template_Helper' ) ) {

	final class Boombox_Featured_Strip_Template_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Featured_Strip_Template_Helper|null
		 */
		public static function get_instance() {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Holds additional data
		 * @var array
		 */
		private $data = array();

		/**
		 * Setter
		 *
		 * @param string $name  Variable key
		 * @param mixed  $value Variable value
		 */
		public function __set( $name, $value ) {
			$this->data[ $name ] = $value;
		}

		/**
		 * Getter
		 *
		 * @param string $name Variable key
		 *
		 * @return mixed Variable value if it exists or null otherwise
		 */
		public function __get( $name ) {
			if ( array_key_exists( $name, $this->data ) ) {
				return $this->data[ $name ];
			}

			return null;
		}

		/**
		 * Boombox_Featured_Strip_Template_Helper constructor.
		 */
		private function __construct() {}

		/**
		 * A dummy magic method to prevent Boombox_Featured_Strip_Template_Helper from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Get query arguments for root configuration
		 * @return array
		 */
		public function get_root_query_config() {
			return array(
				boombox_get_theme_option( 'header_strip_conditions' ),
				boombox_get_theme_option( 'header_strip_time_range' ),
				boombox_get_theme_option( 'header_strip_category' ),
				boombox_get_theme_option( 'header_strip_tags' ),
				boombox_get_theme_option( 'header_strip_items_count' ),
			);
		}

		/**
		 * Get query
		 *
		 * @return WP_Query
		 */
		public function get_query() {
			$conditions = '';
			$time_range = '';
			$categories = array();
			$tags = array();
			$excluded_categories = array();
			$is_page_query = false;
			$post_type = 'post';
			$is_grid = false;
			$posts_per_page = 0;
			$query = NULL;

			/***** Archive template query */
			if ( is_archive() ) {
				if ( boombox_get_theme_option( 'archive_strip_configuration' ) == 'inherit' ) {
					list( $conditions, $time_range, $categories, $tags, $posts_per_page ) = $this->get_root_query_config();
				} else {
					$conditions = boombox_get_theme_option( 'archive_strip_conditions' );
					$time_range = boombox_get_theme_option( 'archive_strip_time_range' );
					$categories = boombox_get_theme_option( 'archive_strip_category' );
					$tags = boombox_get_theme_option( 'archive_strip_tags' );
					$posts_per_page = boombox_get_theme_option( 'archive_strip_items_count' );
				}
			/***** Page template query */
			} else if ( is_page() ) {
				$post_id = get_the_ID();
				if( boombox_get_post_meta( $post_id, 'boombox_strip_configuration' ) == 'inherit' ) {
					list( $conditions, $time_range, $categories, $tags, $posts_per_page ) = $this->get_root_query_config();
				} else {
					$conditions = boombox_get_post_meta( $post_id,'boombox_strip_conditions' );
					$time_range = boombox_get_post_meta( $post_id,'boombox_strip_time_range' );
					$categories = boombox_get_post_meta( $post_id, 'boombox_strip_category' );
					$tags = boombox_get_post_meta( $post_id,'boombox_strip_tags' );
					$posts_per_page = boombox_get_post_meta( $post_id,'boombox_strip_items_count' );
				}
			/***** Single post template query */
			} else if ( is_single() ) {
				if ( boombox_get_theme_option( 'single_post_strip_configuration' ) == 'inherit' ) {
					list( $conditions, $time_range, $categories, $tags, $posts_per_page ) = $this->get_root_query_config();
				} else {
					$conditions = boombox_get_theme_option( 'single_post_strip_conditions' );
					$time_range = boombox_get_theme_option( 'single_post_strip_time_range' );
					$categories = boombox_get_theme_option( 'single_post_strip_category' );
					$tags = boombox_get_theme_option( 'single_post_strip_tags' );
					$posts_per_page = boombox_get_theme_option( 'single_post_strip_items_count' );
				}
			}
			/***** Index template query */
			elseif( is_home() ) {
				list( $conditions, $time_range, $categories, $tags, $posts_per_page ) = $this->get_root_query_config();
			}
			/***** All other cases */
			else {
				return $query;
			}

			/***** exclude NDFW category posts for guests */
			if( ! is_user_logged_in() ) {
				$nsfw_terms = boombox_get_nsfw_terms();
				if ( ! empty( $nsfw_terms ) ) {
					$excluded_categories = wp_list_pluck( $nsfw_terms, 'term_id' );
				}
			}

			/***** Set minimal count */
			$posts_per_page = ( $posts_per_page == -1 ) ? -1 : max( 6, $posts_per_page );

			/***** Get posts query */
			return boombox_get_posts_query( $conditions, $time_range, array( 'category' => $categories, 'tag' => $tags ), array(
				'posts_per_page'      => $posts_per_page,
				'post_type'           => $post_type,
				'paged'               => 1,
				'posts_count'         => -1,
				'is_grid'             => $is_grid,
				'is_page_query'       => $is_page_query,
				'excluded_categories' => $excluded_categories,
			) );
		}

		/**
		 * Get query for footer strip
		 *
		 * @return WP_Query
		 */
		public function get_footer_query() {
			$conditions = boombox_get_theme_option( 'footer_strip_conditions' );
			$time_range = boombox_get_theme_option( 'footer_strip_time_range' );
			$categories = boombox_get_theme_option( 'footer_strip_category' );
			$tags = boombox_get_theme_option( 'footer_strip_tags' );
			$posts_per_page = boombox_get_theme_option( 'footer_strip_items_count' );
			$excluded_categories = array();
			$is_page_query = false;
			$post_type = 'post';
			$is_grid = false;

			$posts_per_page = $posts_per_page != -1 ? max( $posts_per_page, 6 ) : -1;

			$nsfw_terms = boombox_get_nsfw_terms();
			if ( ! empty( $nsfw_terms ) ) {
				$excluded_categories = wp_list_pluck( $nsfw_terms, 'term_id' );
			}

			$query = boombox_get_posts_query( $conditions, $time_range, array( 'category' => $categories, 'tag' => $tags ), array(
				'posts_per_page'      => $posts_per_page,
				'post_type'           => $post_type,
				'paged'               => 1,
				'posts_count'         => -1,
				'is_grid'             => $is_grid,
				'is_page_query'       => $is_page_query,
				'excluded_categories' => $excluded_categories,
			) );

			return $query;
		}

		/**
		 * Get root template options
		 * @return array
		 * {
		 *      Template options
		 * @type string   $size
		 * @type string   $title_position
		 * @type string   $width
		 * @type string   $type
		 * @type bool|int $gap
		 * }
		 */
		public function get_root_options() {
			return array(
				boombox_get_theme_option( 'header_strip_size' ),
				boombox_get_theme_option( 'header_strip_title_position' ),
				boombox_get_theme_option( 'header_strip_width' ),
				boombox_get_theme_option( 'header_strip_type' ),
				boombox_get_theme_option( 'header_strip_disable_gap' ),
			);
		}

		/**
		 * Get template options
		 * @return array
		 */
		public function get_options() {

			/***** Archive template options */
			if ( is_archive() ) {
				if ( boombox_get_theme_option( 'archive_strip_configuration' ) == 'inherit' ) {
					list( $size, $title_position, $width, $type, $disable_gap ) = $this->get_root_options();
				} else {
					$size = boombox_get_theme_option( 'archive_strip_size' );
					$title_position = boombox_get_theme_option( 'archive_strip_title_position' );
					$width = boombox_get_theme_option( 'archive_strip_width' );
					$type = boombox_get_theme_option( 'archive_strip_type' );
					$disable_gap = boombox_get_theme_option( 'archive_strip_disable_gap' );
				}
			}
			/*****  Page template options */
			else if ( is_page() ) {
				$post_id = get_the_ID();
				if( boombox_get_post_meta( $post_id, 'boombox_strip_configuration' ) == 'inherit' ) {
					list( $size, $title_position, $width, $type, $disable_gap ) = $this->get_root_options();
				} else {
					$size = boombox_get_post_meta( $post_id, 'boombox_strip_size' );
					$title_position = boombox_get_post_meta( $post_id, 'boombox_strip_title_position' );
					$width = boombox_get_post_meta( $post_id, 'boombox_strip_width' );
					$type = boombox_get_post_meta( $post_id, 'boombox_strip_type' );
					$disable_gap = boombox_get_post_meta( $post_id, 'boombox_strip_disable_gap' );
				}

			}
			/***** Single post template options */
			else if ( is_single() ) {
				if ( boombox_get_theme_option( 'single_post_strip_configuration' ) == 'inherit' ) {
					list( $size, $title_position, $width, $type, $disable_gap ) = $this->get_root_options();
				} else {
					$size = boombox_get_theme_option( 'single_post_strip_size' );
					$title_position = boombox_get_theme_option( 'single_post_strip_title_position' );
					$width = boombox_get_theme_option( 'single_post_strip_width' );
					$type = boombox_get_theme_option( 'single_post_strip_type' );
					$disable_gap = boombox_get_theme_option( 'single_post_strip_disable_gap' );
				}
			/*****  Index template options */
			} elseif( is_home() ) {
				list( $size, $title_position, $width, $type, $disable_gap ) = $this->get_root_options();
			/***** All other cases */
			} else {
				$size = 'big';
				$title_position = 'inside';
				$width = 'boxed';
				$type = 'slider';
				$disable_gap = false;
			}

			/***** constant classes */
			$classes = array(
				'container',
				'bb-featured-strip',
				'bb-stretched-mobile',
				'no-gutters',
			);

			/***** title classes */
			$classes[] = esc_attr( $title_position . '-title' );

			/***** size classes */
			if ( ! $size ) {
				$size = 'big';
			}
			$classes[] = esc_attr( $size . '-item' );

			/***** type classes */
			if ( $type == 'scrollable' ) {
				$classes[] = 'bb-scroll-area';
				$classes[] = 'arrow-control';
			} else {
				$classes[] = 'featured-carousel';
			}

			/***** width classes */
			if ( $width == 'full_width' ) {
				$classes[] = 'bb-stretched-full no-gutters';
			}

			/***** gap classes */
			if ( $disable_gap ) {
				$classes[] = 'no-gap';
			}

			$template_options = array(
				'title_position'     => $title_position,
				'classes'            => implode( ' ', $classes ),
				'post_format_badges' => boombox_get_theme_option( 'extras_badges_post_type_badges_on_strip' )
			);

			return apply_filters( 'boombox/featured_strip/template_options', $template_options );

		}

	}

}