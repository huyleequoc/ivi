<?php
/**
 * Boombox Featured Area Template Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.4
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Featured_Area_Template_Helper' ) ) {

	final class Boombox_Featured_Area_Template_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Featured_Area_Template_Helper|null
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
		 * Boombox_Featured_Area_Template_Helper constructor.
		 */
		private function __construct() {
		}

		/**
		 * A dummy magic method to prevent Boombox_Featured_Area_Template_Helper from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * @var null|bool|string Current template
		 */
		private $template = null;

		/**
		 * Get area template
		 * @return null|bool|string
		 */
		public function get_template() {

			if( is_null( $this->template ) ) {

				if ( is_home() ) {
					$template = boombox_get_theme_option( 'home_featured_area_type' );
				} else if ( is_archive() ) {
					$template = boombox_get_theme_option( 'archive_featured_area_type' );
				} else if ( is_page() ) {
					$template = boombox_get_post_meta( get_the_ID(), 'boombox_featured_area_type' );
				} else {
					$template = false;
				}

				$this->template = ( 'disable' == $template ) ? false : $template;
			}

			return $this->template;
		}

		/**
		 * Set area template
		 * @param $template
		 */
		public function set_template( $template ) {
			$this->template = $template;
		}

		/**
		 * Get items count type area type
		 * @param string $type Area type
		 *
		 * @return int
		 */
		public function get_items_count_by_template( $type = '' ) {

			if( ! $type ) {
				$options = $this->get_options();
				$type = isset( $options[ 'type' ] ) ? $options[ 'type' ] : '';
			}

			switch( $type ) {
				case 'type-1long':
				case 'type-1-with-newsletter':
					$count = 1;
					break;

				case 'type-1-1':
				case 'type-1-1small':
				case 'type-1-1-landscape':
					$count = 2;
					break;

				case 'type-1-1-1':
				case 'type-1-2':
				case 'type-1-1-1-stretched':

					$count = 3;
					break;

				case 'type-1-2-1':
				case 'type-1-2-1-stretched':
				case 'type-1-3':

					$count = 4;
					break;

				case 'type-1-4':
				case 'type-1-4-stretched':

					$count = 5;
					break;

				case 'disable':
				default:
					$count = 0;
			}

			return $count;
		}

		/**
		 * Get query
		 * @return bool|mixed|null|WP_Query
		 */
		public function get_query() {
			$conditions = '';
			$time_range = '';
			$categories = array();
			$tags = array();
			$reactions = array();
			$excluded_categories = array();
			$is_page_query = false;
			$post_type = 'post';
			$is_grid = false;
			$template = false;
			$query = NULL;

			if ( is_archive() ) {
				$archive_obj = get_queried_object();
				if( $archive_obj ) {
					if ( is_category() ) {
						$categories = array( $archive_obj->slug );
					} else if ( is_tag() ) {
						$tags = array( $archive_obj->slug );
					} elseif( boombox_reactions_is_enabled() && taxonomy_exists( 'reaction' ) && is_tax( 'reaction' ) ) {
						$reactions = array( $archive_obj->slug );
					}
				}
				$conditions = boombox_get_theme_option( 'archive_featured_area_conditions' );
				$time_range = boombox_get_theme_option( 'archive_featured_area_time_range' );
				$template = boombox_get_theme_option( 'archive_featured_area_type' );
			} else if ( is_page() ) {
				$post_id = get_the_ID();

				$conditions = boombox_get_post_meta( $post_id, 'boombox_featured_area_conditions' );
				if ( is_front_page() && $conditions == 'featured' ) {
					$conditions = 'featured_frontpage';
				}
				$time_range = boombox_get_post_meta( $post_id, 'boombox_featured_area_time_range' );
				$categories = boombox_get_post_meta( $post_id, 'boombox_featured_area_category' );
				$tags = boombox_get_post_meta( $post_id, 'boombox_featured_area_tags' );
				$template = boombox_get_post_meta( $post_id, 'boombox_featured_area_type' );
			} else if ( is_home() ) {
				$template = boombox_get_theme_option( 'home_featured_area_type' );
				$conditions = boombox_get_theme_option( 'home_featured_area_conditions' );
				if( $conditions == 'featured' ) {
					$conditions = 'featured_frontpage';
				}
				$time_range = boombox_get_theme_option( 'home_featured_area_time_range' );
				$categories = boombox_get_theme_option( 'home_featured_area_category' );
				$tags = boombox_get_theme_option( 'home_featured_area_tags' );
			}

			if ( $template ) {

				$nsfw_terms = boombox_get_nsfw_terms();
				if ( ! empty( $nsfw_terms ) ) {
					$excluded_categories = wp_list_pluck( $nsfw_terms, 'term_id' );
				}

				$args = array(
					'posts_per_page'      => $this->get_items_count_by_template( $template ),
					'post_type'           => $post_type,
					'is_grid'             => $is_grid,
					'is_page_query'       => $is_page_query,
					'excluded_categories' => $excluded_categories,
				);

				$hash = md5( json_encode( array_merge( array(
					'conditions' => $conditions,
					'time_range' => $time_range,
					'categories' => $categories,
					'tags'       => $tags,
					'reactions'  => $reactions
				), $args ) ) );

				$query = boombox_cache_get( $hash );
				if ( ! $query ) {
					$query = boombox_get_posts_query( $conditions, $time_range, array( 'category' => $categories, 'tag' => $tags, 'reaction' => $reactions ), $args );
					boombox_cache_set( $hash, $query );
				}

			}

			return $query;
		}

		/**
		 * Fill area elements posts array with placeholder values to match correct count for specified template.
		 *
		 * @param array             $items Array of posts items
		 * @param null|WP_Post      $value Placeholder: should be null or WP_Post object
		 * @param string            $template Current template. Can be omitted to use current instance template if any.
		 *
		 * @return array
		 */
		public function fill_template_absentee_items( array $items, $value = null, $template = '' ) {
			$template = $template ? $template : $this->get_template();
			if( $template ) {
				$absentee_count = $this->get_items_count_by_template( $template ) - count( $items );
				if( $absentee_count > 0 ) {
					$value = apply_filters( 'boombox/featured-area/placeholder_value', $value, $template, $items );
					$value = $value && ! is_a( $value, 'WP_Post' ) ? null : $value;
					$items = array_merge( $items, array_fill( 0, $absentee_count, $value ) );
				}
			}

			return $items;
		}

		/**
		 * Get template part name for current item. Used as a $name parameter for get_template_part functions
		 * @param mixed|WP_Post $item Current item
		 *
		 * @return string
		 */
		public function get_item_template_part_name( $item ) {
			$name = 'placeholder';
			if( is_a( $item, 'WP_Post' ) ) {
				$name = 'item';

				global $post;
				$post = $item;
				setup_postdata( $post );
			}
			return $name;
		}

		/**
		 * Get elements
		 * @return array
		 */
		public function get_options() {

			$cache_key = 'featured_area_options';
			$options = boombox_cache_get( $cache_key );

			if( ! $options ) {

				$disable_gap = false;
				$options = array(
					'badges_count' => 2,
					'image_size'   => 'boombox_image768x450',
					'class'        => ''
				);

				// hide elements
				$hide_elements_choices = Boombox_Choices_Helper::get_instance()->get_featured_area_hide_elements();
				if ( is_home() ) {
					$disable_gap = boombox_get_theme_option( 'home_featured_area_disable_gap' );
					$hide_elements = (array)boombox_get_theme_option( 'home_featured_area_hide_elements' );
					foreach ( $hide_elements_choices as $name => $element ) {
						$options[ $name ] = ! in_array( $name, $hide_elements );
					}
				} else if ( is_archive() ) {
					$disable_gap = boombox_get_theme_option( 'archive_featured_area_disable_gap' );
					$hide_elements = (array)boombox_get_theme_option( 'archive_featured_area_hide_elements' );
					foreach ( $hide_elements_choices as $name => $element ) {
						$options[ $name ] = ! in_array( $name, $hide_elements );
					}
				} else if ( is_page() ) {
					global $wp_the_query;
					$disable_gap = boombox_get_post_meta( $wp_the_query->queried_object_id, 'boombox_featured_disable_gap' );
					$hide_elements = (array)boombox_get_post_meta( $wp_the_query->queried_object_id, 'boombox_featured_hide_elements' );
					foreach ( $hide_elements_choices as $name => $element ) {
						$options[ $name ] = ! in_array( $name, $hide_elements );
					}
				}

				// classes
				$options[ 'class' ] .= ' badges-' . ( $options[ 'badges' ] ? 'on' : 'off' );
				if ( $disable_gap ) {
					$options[ 'class' ] .= ' no-gap';
				}

				boombox_cache_set( $cache_key, $options );

			}

			return apply_filters( 'boombox/featured_area/template_options', $options );
		}

	}

}