<?php
/**
 * Boombox Breadcrumb Template Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Breadcrumb_Template_Helper' ) ) {

	final class Boombox_Breadcrumb_Template_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return Boombox_Breadcrumb_Template_Helper|null
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Get home configuration
		 * @return array
		 */
		private function get_home() {
			return array(
				'label'      => esc_html__( 'Home', 'boombox' ),
				'url'        => esc_url( home_url( '/' ) ),
				'icon'       => apply_filters( 'boombox/breadcrumb/home_icon', false ) ? '<i class="root-icon bb-icon bb-ui-icon-home"></i>' : ''
			);
		}

		/**
		 * Get items
		 * @return array
		 */
		private function get_items() {

			$items = array();

			// taxonomy
			if ( is_category() || is_tax() ) {
				$term_obj = get_queried_object();
				if ( 0 != $term_obj->parent ) {
					$parents = get_ancestors( $term_obj->term_id, $term_obj->taxonomy, 'taxonomy' );
					if ( ! empty( $parents ) ) {
						$parents = array_reverse( $parents );
						foreach ( $parents as $parent_id ) {
							$term_parent = get_term( $parent_id, $term_obj->taxonomy );
							// If there was an error, continue to the next term.
							if ( null == $term_parent || is_wp_error( $term_parent ) ) {
								continue;
							}

							$term_parent_link = get_term_link( $term_parent );
							// If there was an error, continue to the next term.
							if ( is_wp_error( $term_parent_link ) ) {
								continue;
							}

							$items[] = array(
								'label' => $term_parent->name,
								'url'   => $term_parent_link
							);
						}
					}
				}
			}
			// page
			else if ( is_page() ) {
				$post_obj = get_queried_object();
				if ( 0 != $post_obj->post_parent ) {
					$parents = get_ancestors( $post_obj->ID, $post_obj->post_type, 'post_type' );
					if ( ! empty( $parents ) ) {
						$parents = array_reverse( $parents );
						foreach ( $parents as $parent_id ) {
							$post_parent = get_post( $parent_id );
							// If it's not a WP_POST object, continue to the next post.
							if ( ! is_a( $post_parent, 'WP_POST' ) ) {
								continue;
							}

							$items[] = array(
								'label' => $post_parent->post_title,
								'url'   => get_permalink( $post_parent->ID )
							);
						}
					}
				}

			}
			// single
			else if ( is_singular() ) {
				$post_obj = get_queried_object();
				$tax_objects = get_object_taxonomies( $post_obj, 'objects' );
				$primary_taxonomy_slug = null;

				foreach ( $tax_objects as $tax_slug => $tax_object ) {
					if ( $tax_object->public ) {
						$primary_taxonomy_slug = $tax_slug;
						break;
					}
				}

				if ( ! is_null( $primary_taxonomy_slug ) ) {
					$terms = wp_get_post_terms( $post_obj->ID, $primary_taxonomy_slug );
					$primary_term = null;
					if ( is_array( $terms ) && ! empty( $terms ) ) {
						foreach ( $terms as $term ) {
							if ( is_wp_error( $term ) ) {
								break;
							}
							$primary_term = $term;
							break;
						}
					}

					if ( $primary_term ) {
						// add parents
						$parents = get_ancestors( $primary_term->term_id, $primary_taxonomy_slug, 'taxonomy' );
						if ( ! empty( $parents ) ) {
							$parents = array_reverse( $parents );
							foreach ( $parents as $parent_id ) {
								$term_parent = get_term( $parent_id, $primary_taxonomy_slug );
								// If there was an error, continue to the next term.
								if ( null == $term_parent || is_wp_error( $term_parent ) ) {
									continue;
								}

								$term_parent_link = get_term_link( $term_parent );
								// If there was an error, continue to the next term.
								if ( is_wp_error( $term_parent_link ) ) {
									continue;
								}

								$items[] = array(
									'label' => $term_parent->name,
									'url'   => $term_parent_link
								);
							}
						}

						// add current
						$primary_term_parent_link = get_term_link( $primary_term );
						if ( ! is_wp_error( $primary_term_parent_link ) ) {
							$items[] = array(
								'label' => $primary_term->name,
								'url'   => $primary_term_parent_link
							);
						}

					}

				}
			}

			return $items;
		}

		/**
		 * Retrieve the archive title based on the queried object.
		 *
		 * @return string Archive title.
		 */
		private function get_the_archive_title() {
			$title = '';

			if ( is_category() ) {
				/* Category archive title */
				$title = single_cat_title( '', false );
			} elseif ( is_tag() ) {
				/* Tag archive title */
				$title = single_tag_title( '', false );
			} elseif ( is_author() ) {
				/* translators: Author archive title. 1: Author name */
				$title = sprintf( esc_html__( 'Author: %s', 'boombox' ), '<span class="vcard">' . get_the_author() . '</span>' );
			} elseif ( is_year() ) {
				/* Yearly archive title*/
				$title = get_the_date( _x( 'Y', 'yearly archives date format', 'boombox' ) );
			} elseif ( is_month() ) {
				/*Monthly archive title */
				$title = get_the_date( _x( 'F Y', 'monthly archives date format', 'boombox' ) );
			} elseif ( is_day() ) {
				/* translators: Daily archive title. 1: Date */
				$title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'boombox' ) );
			} elseif ( is_tax( 'post_format' ) ) {
				if ( is_tax( 'post_format', 'post-format-aside' ) ) {
					$title = _x( 'Asides', 'post format archive title', 'boombox' );
				} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
					$title = _x( 'Galleries', 'post format archive title', 'boombox' );
				} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
					$title = _x( 'Images', 'post format archive title', 'boombox' );
				} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
					$title = _x( 'Videos', 'post format archive title', 'boombox' );
				} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
					$title = _x( 'Quotes', 'post format archive title', 'boombox' );
				} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
					$title = _x( 'Links', 'post format archive title', 'boombox' );
				} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
					$title = _x( 'Statuses', 'post format archive title', 'boombox' );
				} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
					$title = _x( 'Audio', 'post format archive title', 'boombox' );
				} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
					$title = _x( 'Chats', 'post format archive title', 'boombox' );
				}
			} elseif ( is_post_type_archive() ) {
				/* translators: Post type archive title. 1: Post type name */
				$title = sprintf( esc_html__( 'Archives: %s', 'boombox' ), post_type_archive_title( '', false ) );
			} elseif ( is_tax() ) {
				/*  Taxonomy term archive title */
				$title = single_term_title( '', false );
			} else {
				$title = esc_html__( 'Archives', 'boombox' );
			}

			/**
			 * Filters the archive title.
			 *
			 * @param string $title Archive title to be displayed.
			 */
			return apply_filters( 'boombox/breadcrumb/get_the_archive_title', $title );
		}

		/**
		 * Get tail
		 * @return string
		 */
		private function get_tail() {

			$tail = '';
			if ( is_archive() ) {
				$tail = $this->get_the_archive_title();
			}
			// single
			elseif ( is_singular() ) {
				global $post;
				if( ! ( ( 'page' == get_option( 'show_on_front' ) ) && ( $post->ID == get_option( 'page_on_front' ) ) ) ) {
					$tail = $post->post_title;
				}
			}
			// search
			elseif ( is_search() ) {
				$tail = sprintf( '%s: %s', esc_html__( 'Search results for', 'boombox' ), get_search_query() );
			}
			// 404
			elseif ( is_404() ) {
				$tail = esc_html__( 'Error 404', 'boombox' );
			}

			return $tail;
		}

		/**
		 * Get options
		 * @return array
		 */
		public function get_options() {

			$template_options = array(
				'home'  => $this->get_home(),
				'items' => $this->get_items(),
				'tail'  => $this->get_tail(),
				'separator' => '<i class="sep-icon bb-icon bb-ui-icon-angle-right"></i>',
			);

			return apply_filters( 'boombox/breadcrumb/template_options', $template_options );
		}


	}

}