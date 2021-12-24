<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Loop_Helper' ) ) {
	/**
	 * Class for adv and newsletter injection into archive and page listings
	 */
	class Boombox_Loop_Helper {

		protected static $loop_index = 0;
		protected static $current_page = 1;
		protected static $skip = false;

		protected static $pagination_type = false;
		protected static $is_ajax = false;
		protected static $adv_name = 'adv';
		protected static $newsletter_name = 'newsletter';
		protected static $product_name = 'product';
		protected static $post_name = 'post';

		protected static $is_adv_enabled = false;
		protected static $is_newsletter_enabled = false;
		protected static $is_product_enabled = false;

		protected static $replaced_count = 0;

		/**
		 * Set enabled injections
		 *
		 * @param $is_adv_enabled
		 * @param $is_newsletter_enabled
		 * @param $is_product_enabled
		 */
		protected static function set_injection_enabled( $is_adv_enabled, $is_newsletter_enabled, $is_product_enabled ) {
			if ( (bool)$is_adv_enabled ) {
				static::$is_adv_enabled = true;
			}
			if ( (bool)$is_newsletter_enabled ) {
				static::$is_newsletter_enabled = true;
			}
			if ( (bool)$is_product_enabled ) {
				static::$is_product_enabled = true;
			}
		}

		protected static $instead_adv;
		protected static $instead_newsletter;
		protected static $instead_products;
		protected static $page_layout = array();

		/**
		 * Prepare layout for rendering
		 *
		 * @param $posts_per_page
		 * @param $instead_adv
		 * @param $instead_newsletter
		 * @param $instead_products
		 */
		protected static function set_instead( $posts_per_page, $instead_adv, $instead_newsletter, $instead_products ) {

			static::$instead_adv = $instead_adv;
			static::$instead_newsletter = $instead_newsletter;
			static::$instead_products = $instead_products;

			$adv_positions = array();
			$newsletter_positions = array();
			$products_positions = array();

			$page_layout = array_fill( 0, $posts_per_page, static::$post_name );

			// adv
			if ( static::$is_adv_enabled ) {
				// show in last position if is dirty value
				static::$instead_adv[ 'position' ] = min( static::$instead_adv[ 'position' ], $posts_per_page );

				$adv_start = static::$instead_adv[ 'position' ] - 1;
				$adv_end = $adv_start + static::$instead_adv[ 'count' ];

				for ( $i = $adv_start; $i < $adv_end; $i++ ) {
					$adv_positions[ $i ] = static::$adv_name;
				}
			}

			// newsletter
			if ( static::$is_newsletter_enabled ) {
				// show in last position if is dirty value
				static::$instead_newsletter[ 'position' ] = min( static::$instead_newsletter[ 'position' ], $posts_per_page );

				$newsletter_start = static::$instead_newsletter[ 'position' ] - 1;
				$newsletter_end = $newsletter_start + static::$instead_newsletter[ 'count' ];

				for ( $i = $newsletter_start; $i < $newsletter_end; $i++ ) {
					$newsletter_positions[ $i ] = static::$newsletter_name;
				}
			}

			if ( ! empty( $newsletter_positions ) ) {
				$index = min( array_keys( $newsletter_positions ) );
				$replace_count = 0;

				if ( ! isset( $page_layout[ $index ] ) || ( isset( $page_layout[ $index ] ) && $page_layout[ $index ] == static::$post_name ) ) {
					$replace_count = 1;
					++static::$replaced_count;
					if ( $posts_per_page != -1 ) {
						$posts_per_page += $replace_count;
					}
				}
				array_splice( $page_layout, min( array_keys( $newsletter_positions ) ), $replace_count, $newsletter_positions );
			}

			if ( ! empty( $adv_positions ) ) {
				$index = min( array_keys( $adv_positions ) );
				$replace_count = 0;

				if ( ! isset( $page_layout[ $index ] ) || isset( $page_layout[ $index ] ) && $page_layout[ $index ] == static::$post_name ) {
					$replace_count = 1;
					++static::$replaced_count;
					if ( $posts_per_page != -1 ) {
						$posts_per_page += $replace_count;
					}
				}

				array_splice( $page_layout, min( array_keys( $adv_positions ) ), $replace_count, $adv_positions );
			}

			if ( $posts_per_page != -1 ) {
				for ( $i = 0; $i < static::$replaced_count; $i++ ) {
					$page_layout[] = static::$post_name;
				}
			}

			// product
			if ( static::$is_product_enabled ) {
				// show in last position if is dirty value
				static::$instead_products[ 'position' ] = min( static::$instead_products[ 'position' ], $posts_per_page );

				$products_set = array_fill( 0, static::$instead_products[ 'count' ], static::$product_name );

				$periods_count = floor( $posts_per_page / static::$instead_products[ 'position' ] );
				for ( $j = 1; $j <= $periods_count; $j++ ) {
					$set_position = static::$instead_products[ 'position' ] * $j;
					$products_positions[ $set_position ] = $products_set;
				}
			}

			if ( ! empty( $products_positions ) ) {
				$replace_count = 0;

				$products_positions_keys = array_keys( $products_positions );
				for ( $i = 0; $i < count( $products_positions_keys ); $i++ ) {

					$products_set_offset = $products_positions_keys[ $i ] + static::$instead_products[ 'count' ] * $i;
					$products_set = $products_positions[ $products_positions_keys[ $i ] ];

					array_splice( $page_layout, $products_set_offset, $replace_count, $products_set );
				}
			}

			static::$page_layout = $page_layout;

		}

		/**
		 * Init helper
		 *
		 * @param array $args
		 */
		public static function init( $args = array() ) {

			$args = wp_parse_args( $args, array(
				'is_adv_enabled'        => false,
				'instead_adv'           => 1,
				'is_newsletter_enabled' => false,
				'instead_newsletter'    => 1,
				'is_product_enabled'    => false,
				'page_product_position' => 1,
				'page_product_count'    => 1,
				'skip'                  => false,
				'posts_per_page'        => -1,
				'paged'                 => 1,
				'offset'                => false,
			) );

			$args[ 'posts_per_page' ] = intval( $args[ 'posts_per_page' ] ) ? intval( $args[ 'posts_per_page' ] ) : 1;
			$args[ 'offset' ] = absint( $args[ 'offset' ] ) ? absint( $args[ 'offset' ] ) : false;

			static::$current_page = absint( $args[ 'paged' ] ) ? absint( $args[ 'paged' ] ) : 1;
			static::$skip = (bool)$args[ 'skip' ];

			static::set_injection_enabled( (bool)$args[ 'is_adv_enabled' ], (bool)$args[ 'is_newsletter_enabled' ], (bool)$args[ 'is_product_enabled' ] );
			static::set_instead(
				$args[ 'posts_per_page' ],
				array(
					'position' => $args[ 'instead_adv' ],
					'count'    => 1,
				),
				array(
					'position' => $args[ 'instead_newsletter' ],
					'count'    => 1,
				),
				array(
					'position' => $args[ 'page_product_position' ],
					'count'    => $args[ 'page_product_count' ],
				)
			);
		}

		public static function set_pagination_type( $pagination_type ) {
			static::$pagination_type = $pagination_type;
		}

		/**
		 * Check if it is time to render adv
		 * @return bool
		 */
		protected static function is_time_for_adv() {

			if ( static::$is_ajax && static::$current_page > 1 ) {
				static::$page_layout = array_values( array_diff( static::$page_layout, array( static::$adv_name ) ) );
			}

			$is_time = (
				static::$is_adv_enabled
				&& isset( static::$page_layout[ static::$loop_index ] )
				&& ( static::$page_layout[ static::$loop_index ] == static::$adv_name )
			);

			return $is_time;
		}

		/**
		 * Check if it is time to render newsletter
		 * @return bool
		 */
		protected static function is_time_for_newsletter() {
			if ( static::$is_ajax && static::$current_page > 1 ) {
				static::$page_layout = array_values( array_diff( static::$page_layout, array( static::$newsletter_name ) ) );
			}

			$is_time = (
				static::$is_newsletter_enabled
				&& isset( static::$page_layout[ static::$loop_index ] )
				&& ( static::$page_layout[ static::$loop_index ] == static::$newsletter_name )
			);

			return $is_time;
		}

		/**
		 * Check if it is time to render product
		 * @return bool
		 */
		protected static function is_time_for_product() {

			$is_time = (
				static::$is_product_enabled
				&& isset( static::$page_layout[ static::$loop_index ] )
				&& ( static::$page_layout[ static::$loop_index ] == static::$product_name )
			);

			return $is_time;
		}

		/**
		 * have_post helper
		 * @return bool
		 */
		public static function have_posts() {

			if ( static::$loop_index == 0 ) {
				static::$page_layout = array_values( static::$page_layout );
			}

			global $wp_query;
			$posts_per_page = $wp_query->get( 'posts_per_page' );
			$page_layout_count = count( static::$page_layout );
			static::$is_ajax = in_array( static::$pagination_type, array( 'load_more', 'infinite_scroll', 'infinite_scroll_on_demand' ) );

			// skip the last post
			if ( $page_layout_count > 0 && $page_layout_count <= static::$loop_index && -1 != $posts_per_page ) {
				return false;
			}

			// if it's the time to show the adv or newsletter or product then we have something to show
			if ( static::is_time_for_adv() || static::is_time_for_newsletter() || static::is_time_for_product() ) {
				return true;
			}

			// as default
			return have_posts();
		}

		/**
		 * the_post helper
		 * @return stdClass {
		 *      Post data
		 *
		 * @type bool $is_injected   Is injected
		 * @type bool $is_adv        Is injected advertisement
		 * @type bool $is_newsletter Is injected newsletter
		 * @type bool $is_product    Is injected product
		 * }
		 */
		public static function the_post() {

			$is_adv = false;
			$is_newsletter = false;
			$is_product = false;

			global $wp_query;

			if ( static::is_time_for_adv() ) {
				do_action( 'boombox/loop-helper/adv',
					static::$instead_adv,
					$wp_query,
					static::$current_page,
					static::$loop_index,
					static::$replaced_count
				);

				$is_adv = true;
			} else if ( static::is_time_for_newsletter() ) {
				do_action( 'boombox/loop-helper/newsletter',
					static::$instead_newsletter,
					$wp_query,
					static::$current_page,
					static::$loop_index,
					static::$replaced_count
				);

				$is_newsletter = true;
			} else if ( static::is_time_for_product() ) {
				do_action( 'boombox/loop-helper/product',
					static::$instead_products,
					$wp_query,
					static::$current_page,
					static::$loop_index,
					static::$replaced_count
				);

				$is_product = true;
			} else {
				the_post();
			}

			++static::$loop_index;

			$the_post = new stdClass();
			$the_post->is_injected = ( $is_adv || $is_newsletter || $is_product );
			$the_post->is_adv = $is_adv;
			$the_post->is_newsletter = $is_newsletter;
			$the_post->is_product = $is_product;

			return $the_post;
		}

		/**
		 * Get loop index
		 * @return int
		 */
		public static function get_index() {
			return self::$loop_index;
		}

		/**
		 * Get current page
		 * @return int
		 */
		public static function get_page() {
			return self::$current_page;
		}
	}
}