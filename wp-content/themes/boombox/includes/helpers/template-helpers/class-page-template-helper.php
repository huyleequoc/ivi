<?php
/**
 * Boombox Page Template Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Page_Template_Helper' ) ) {

	final class Boombox_Page_Template_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Page_Template_Helper|null
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
		 * Boombox_Page_Template_Helper constructor.
		 */
		private function __construct() {
		}

		/**
		 * A dummy magic method to prevent Boombox_Page_Template_Helper from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Get template options
		 * @param bool $paged
		 *
		 * @return array
		 */
		public function get_options( $paged = false ) {

			if ( boombox_is_fragment_cache_enabled() ) {
				?>
				<!-- mfunc <?php echo W3TC_DYNAMIC_SECURITY; ?>
					global $post;

					$featured_strip = ( in_array( 'page', boombox_get_theme_option( 'header_strip_visibility' ) )
										&& ( boombox_get_post_meta( $post->ID, 'boombox_strip_configuration' ) != 'none' ) );
					$featured_area = ( boombox_get_post_meta( $post->ID, 'boombox_featured_area_type' ) != 'disable' );
					if( wp_is_mobile() ) {
						$featured_strip = ( $featured_strip &&
											boombox_get_theme_option( 'mobile_global_enable_strip' ) );
						$featured_area = ( $featured_area &&
											boombox_get_theme_option( 'mobile_global_enable_featured_area' ) );
					}
				-->
				<?php
				global $post;

				$featured_strip = ( in_array( 'page', boombox_get_theme_option( 'header_strip_visibility' ) )
					&& ( boombox_get_post_meta( $post->ID, 'boombox_strip_configuration' ) != 'none' ) );
				$featured_area = ( boombox_get_post_meta( $post->ID, 'boombox_featured_area_type' ) != 'disable' );
				if( wp_is_mobile() ) {
					$featured_strip = ( $featured_strip &&
						boombox_get_theme_option( 'mobile_global_enable_strip' ) );
					$featured_area = ( $featured_area &&
						boombox_get_theme_option( 'mobile_global_enable_featured_area' ) );
				}
				?>
				<!-- /mfunc <?php echo W3TC_DYNAMIC_SECURITY; ?> -->
				<?php
			} else if ( boombox_is_page_cache_enabled() ) {
				global $post;
				$featured_strip = ( in_array( 'page', boombox_get_theme_option( 'header_strip_visibility' ) )
					&& ( boombox_get_post_meta( $post->ID, 'boombox_strip_configuration' ) != 'none' ) );
				$featured_area = ( boombox_get_post_meta( $post->ID, 'boombox_featured_area_type' ) != 'disable' );
			} else {
				global $post;
				$featured_strip = ( in_array( 'page', boombox_get_theme_option( 'header_strip_visibility' ) )
					&& ( boombox_get_post_meta( $post->ID, 'boombox_strip_configuration' ) != 'none' ) );
				$featured_area = ( boombox_get_post_meta( $post->ID, 'boombox_featured_area_type' ) != 'disable' );
				if( wp_is_mobile() ) {
					$featured_strip = ( $featured_strip &&
						boombox_get_theme_option( 'mobile_global_enable_strip' ) );
					$featured_area = ( $featured_area &&
						boombox_get_theme_option( 'mobile_global_enable_featured_area' ) );
				}
			}

			$query = NULL;

			$trending_type = false;
			if ( boombox_is_trending_page( 'trending' ) ) {
				$trending_type = 'trending';
			} elseif ( boombox_is_trending_page( 'hot' ) ) {
				$trending_type = 'hot';
			} elseif ( boombox_is_trending_page( 'popular' ) ) {
				$trending_type = 'popular';
			}

			$paged = $paged ? $paged : boombox_get_paged();
			$pagination_type = boombox_get_post_meta( $post->ID, 'boombox_pagination_type' );
			$posts_per_page = boombox_get_post_meta( $post->ID, 'boombox_posts_per_page' );
			if( $trending_type ) {
				$listing_type = boombox_get_post_meta( $post->ID, 'boombox_trending_listing_type' );
				if( ! $listing_type || ( 'none' == $listing_type ) ) {
					$listing_type = 'list';
				}
			} else {
				$listing_type = boombox_get_post_meta( $post->ID, 'boombox_listing_type' );
			}
			$is_grid = in_array( $listing_type, array( 'grid' ) ) ? true : false;
			$sidebar_type = boombox_get_post_meta( $post->ID, 'boombox_sidebar_type' );
			if( ! $sidebar_type ) {
				$sidebar_type = '1-sidebar-1_3';
			}

			if ( $paged > 1 ) {
				$featured_strip = apply_filters( 'boombox/enable_strip_on_paginated_page', $featured_strip );
				$featured_area = apply_filters( 'boombox/enable_featured_area_on_paginated_page', $featured_area );
			}

			if ( 'none' == $pagination_type ) {
				$posts_per_page = -1;
			}

			/**
			 * Exclude featured area posts
			 */
			$excluded_posts = array();
			if ( boombox_get_post_meta( $post->ID, 'boombox_featured_area_exclude_from_main_loop' ) ) {
				if ( $featured_area ) {
					$boombox_featured_query = Boombox_Template::init( 'featured-area' )->get_query();
					if ( NULL != $boombox_featured_query && $boombox_featured_query->found_posts ) {
						$excluded_posts = array_merge( $excluded_posts, wp_list_pluck( $boombox_featured_query->get_posts(), 'ID' ) );
					}
				}
			}

			if( $trending_type ) {

				add_action( 'boombox/loop-start', array( $this, 'replace_badges_with_number' ), 10, 1 );
				add_action( 'boombox/loop-end', array( $this, 'remove_badges_replace_with_numbers_handler' ), 10, 1 );

				/***** Adv */
				$instead_ad = false;
				$is_adv_enabled = boombox_is_adv_enabled( boombox_get_post_meta( $post->ID, 'boombox_page_ad' ) );
				if( $is_adv_enabled ) {
					$instead_ad = boombox_get_post_meta( $post->ID, 'boombox_inject_ad_instead_post' );
				}

				/***** Newsletter */
				$instead_newsletter = false;
				$is_newsletter_enabled = boombox_is_newsletter_enabled( boombox_get_post_meta( $post->ID, 'boombox_page_newsletter' ) );
				if( $is_newsletter_enabled ) {
					$instead_newsletter = boombox_get_post_meta( $post->ID, 'boombox_inject_newsletter_instead_post' );
				}

				/***** Product */
				$page_product_position = false;
				$page_product_count = false;
				$is_product_enabled = boombox_is_product_enabled( boombox_get_post_meta( $post->ID, 'boombox_page_products_inject' ) );
				if( $is_product_enabled ) {
					$page_product_position = boombox_get_post_meta( $post->ID, 'boombox_page_injected_products_position' );
					$page_product_count = boombox_get_post_meta( $post->ID, 'boombox_page_injected_products_count' );
				}

				$query = boombox_get_trending_posts(
					$trending_type,
					$posts_per_page,
					array(
						'paged'                 => $paged,
						'is_grid'               => $is_grid,
						'page_ad'               => $is_adv_enabled,
						'instead_ad'            => $instead_ad,
						'page_newsletter'       => $is_newsletter_enabled,
						'instead_newsletter'    => $instead_newsletter,
						'page_product'          => $is_product_enabled,
						'page_product_position' => $page_product_position,
						'page_product_count'    => $page_product_count,
					)
				);

			} else {
				// conditions
				$condition = boombox_get_post_meta( $post->ID, 'boombox_listing_condition' );
				if (
					! boombox_get_post_meta( $post->ID, 'boombox_hide_title_area' )
					&& ! boombox_get_post_meta( $post->ID, 'boombox_title_area_hide_filter' )
					&& isset( $_GET[ 'order' ] )
					&& $_GET[ 'order' ]
				) {
					$escaped_condition = esc_sql( $_GET[ 'order' ] );
					if ( array_key_exists( $escaped_condition, Boombox_Choices_Helper::get_instance()->get_conditions() ) ) {
						$condition = $escaped_condition;
					}
				}
				$time_range = boombox_get_post_meta( $post->ID, 'boombox_listing_time_range' );
				$categories = boombox_get_post_meta( $post->ID, 'boombox_listing_categories' );
				$tags = boombox_get_post_meta( $post->ID, 'boombox_listing_tags' );

				if ( 'none' != $listing_type ) {

					$args = array(
						'posts_per_page'      => $posts_per_page,
						'post_type'           => 'post',
						'paged'               => $paged,
						'posts_count'         => -1,
						'is_grid'             => $is_grid,
						'ignore_sticky_posts' => ! is_front_page(),
						'excluded_posts'      => $excluded_posts
					);

					$query = boombox_get_posts_query( $condition, $time_range, array( 'category' => $categories, 'tag' => $tags ), $args );

				}
			}

			if ( is_object( $query ) ) {
				$query->is_home = false;
				$query->is_page = true;
				$query->is_singular = true;
			}

			$template_settings = array(
				'title_area'               => ! boombox_get_post_meta( $post->ID, 'boombox_hide_title_area' ),
				'is_trending_page'         => boombox_is_trending( $post->ID ),
				'featured_strip'           => $featured_strip,
				'featured_area'            => $featured_area,
				'listing_type'             => $listing_type,
				'pagination_type'          => $pagination_type,
				'posts_per_page'           => $posts_per_page,
				'query'                    => $query,
				'enable_primary_sidebar'   => boombox_is_primary_sidebar_enabled( $sidebar_type ),
				'enable_secondary_sidebar' => boombox_is_secondary_sidebar_enabled( $sidebar_type ),
				'hidden_seo_title'         => true
			);

			return apply_filters( 'boombox/page_template_settings', $template_settings );
		}

		/**
		 * Prevent listing item render badges icons
		 * @param bool $allow Current status
		 *
		 * @return bool
		 */
		public function prevent_listing_item_render_badges( $allow ) {
			$allow = ! boombox_get_theme_option( 'extras_post_ranking_system_numeration_badges' );

			return $allow;
		}

		/**
		 * Allow listing item render numeric index
		 * @param bool $allow Current status
		 *
		 * @return bool
		 */
		public function allow_listing_item_render_index( $allow ) {
			$allow = boombox_get_theme_option( 'extras_post_ranking_system_numeration_badges' );

			return $allow;
		}

		/**
		 * Replace loop item badges with numeric indexes
		 * @param string $template Current template
		 */
		public function replace_badges_with_number( $template ) {
			if( 'page' == $template ) {
				add_filter( 'boombox/loop-item/show-badges', array( $this, 'prevent_listing_item_render_badges' ), 10, 1 );
				add_filter( 'boombox/loop-item/show-box-index', array( $this, 'allow_listing_item_render_index' ), 10, 1 );
			}
		}

		/**
		 * Remove functionality of replacing loop item badges with indexes
		 * @param string $template Current template
		 */
		public function remove_badges_replace_with_numbers_handler( $template ) {
			if( 'page' == $template ) {
				remove_filter( 'boombox/loop-item/show-badges', array( $this, 'prevent_listing_item_render_badges' ), 10 );
				remove_filter( 'boombox/loop-item/show-box-index', array( $this, 'allow_listing_item_render_index' ), 10 );
			}
		}

	}

}