<?php
/**
 * Boombox Title Template Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Title_Template_Helper' ) ) {

	final class Boombox_Title_Template_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Title_Template_Helper|null
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
		 * Holds current options
		 * @var array
		 */
		private $options = array();

		/**
		 * Boombox_Title_Template_Helper constructor.
		 */
		private function __construct() {
		}

		/**
		 * A dummy magic method to prevent Boombox_Title_Template_Helper from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Get title badge
		 * @return string
		 */
		public function get_title_badge() {
			$queried_object = get_queried_object();
			$has_badge = false;
			$badge_class = '';
			$badge_taxonomy = '';
			$badge_term_id = 0;
			$badge = '';
			$badge_name = '';

			if ( $queried_object && is_object( $queried_object ) ) {
				if ( is_a( $queried_object, 'WP_Term' ) ) {
					if ( in_array( $queried_object->taxonomy, array( 'category', 'post_tag' ) ) ) {
						$term_icon = boombox_get_term_icon_html( $queried_object->term_id, $queried_object->name, $queried_object->taxonomy );
						if ( $term_icon ) {
							$badge_class = $queried_object->taxonomy;
							$badge_taxonomy = $queried_object->taxonomy;
							$badge_term_id = $queried_object->term_id;
							$badge = $term_icon;
							$badge_name = $queried_object->name;
							$has_badge = true;
						}
					} else if ( 'reaction' == $queried_object->taxonomy ) {
						$reaction_icon_url = boombox_get_reaction_icon_url( $queried_object->term_id );
						if ( ! empty( $reaction_icon_url ) ) {
							$badge_class = $queried_object->taxonomy;
							$badge_taxonomy = $queried_object->taxonomy;
							$badge_term_id = $queried_object->term_id;
							$badge_name = $queried_object->name;
							$badge = $reaction_icon_url ? '<img src="' . $reaction_icon_url . '" alt="' . $badge_name . '">' : '';
							$has_badge = true;
						}
					}
				} else if ( is_a( $queried_object, 'WP_Post' ) ) {
					if ( 'page' == $queried_object->post_type ) {

						$trending_type = false;

						while ( true ) {
							if ( boombox_is_trending_page( 'trending', $queried_object->ID ) ) {
								$trending_type = 'trending';
								break;
							}

							if ( boombox_is_trending_page( 'hot', $queried_object->ID ) ) {
								$trending_type = 'hot';
								break;
							}

							if ( boombox_is_trending_page( 'popular', $queried_object->ID ) ) {
								$trending_type = 'popular';
								break;
							}

							break;
						}

						if ( $trending_type ) {
							$badge_class = 'trending';
							$badge_taxonomy = 'trending';
							$badge_term_id = '';
							$trending_icon_name = boombox_get_trending_icon_name( 'icon', $trending_type );
							$badge = '<i class="bb-icon bb-ui-icon-' . $trending_icon_name . '"></i>';
							$badge_name = $queried_object->post_title;
							$has_badge = true;
						}
					}
				}
			}

			$html = '';
			if ( $has_badge ) {
				$badge_per_layout_specific_classes = array(
					'style3' => 'badge-md'
				);
				$layout = $this->get_layout();

				if( isset( $badge_per_layout_specific_classes[ $layout ] ) ) {
					$badge_class .= ' ' . $badge_per_layout_specific_classes[ $layout ];
				}
				$html = sprintf( '<span class="bb-badge badge %1$s"><span class="circle">%2$s</span><span class="text">%3$s</span></span>',
					apply_filters( 'boombox_badge_wrapper_advanced_classes', $badge_class, $badge_taxonomy, $badge_term_id ),
					$badge,
					$badge_name
				);
			}

			return $html;
		}

		/**
		 * Get title template layout
		 * @return string
		 */
		public function get_layout() {
			$template = 'style1';
			// archive template
			if ( is_archive() ) {
				$template = boombox_get_term_meta( get_queried_object_id(), 'title_area_style' );
				if( ! $template || ( 'inherit' == $template ) ) {
					$template = boombox_get_theme_option( 'archive_header_style' );
				}
			}
			// page template
			else if ( is_page() ) {
				$template = boombox_get_post_meta( get_the_ID(), 'boombox_title_area_style' );
			}

			return apply_filters( 'boombox/title_template_layout', $template );
		}

		/**
		 * Get options
		 */
		public function get_options() {

			$title = '';
			$sub_title = '';
			$filters = false;
			$badge = false;
			$width = false;
			$text_color = '';
			$background = array();
			$class = array(
				'primary'   => '',
				'secondary' => '',
			);
			$template = false;
			$trending_nav = false;
			$filter_data = array( 'choices' => array(), 'current' => '' );
			$breadcrumb = false;

			// archive template
			if ( is_archive() ) {
				$template = 'archive';
				if ( ! isset( $this->options[ $template ] ) ) {

					$term_id = get_queried_object_id();
					$title = get_the_archive_title();
					$sub_title = get_the_archive_description();
					$filters = boombox_get_theme_option( 'archive_header_filters' );
					$badge = boombox_get_theme_option( 'archive_header_enable_badge' );
					$text_color = boombox_get_term_meta( $term_id, 'title_area_text_color' );
					$breadcrumb = in_array( 'archive', boombox_get_theme_option( 'extras_breadcrumb_visibility' ) );
					$width = boombox_get_term_meta( $term_id, 'title_area_background_container' );
					if( ! $width || ( 'inherit' == $width ) ) {
						$width = boombox_get_theme_option( 'archive_header_background_container' );
					}

					$background = array(
						'features' => array(),
						'has_bg'   => false
					);

					/***** Background Color */
					$bg_color = boombox_get_term_meta( $term_id, 'title_area_bg_color' );
					if( $bg_color ) {

						$background['features'][] = 'color';
						$background['color'] = $bg_color;
						$background['has_bg'] = true;
						$class[ 'primary' ] .= ' has-bg-clr';
					}

					/***** Background Gradient */
					$bg_gr_color = boombox_get_term_meta( $term_id, 'title_area_gradient_color' );
					if( $bg_gr_color ) {
						$bg_color = $bg_color ? $bg_color : 'transparent';

						$direction = boombox_get_term_meta( $term_id, 'title_area_bg_gradient_direction' );
						$direction = $direction ? $direction : 'top';

						$background['features'][] = 'gradient';
						$background['start'] = $bg_color;
						$background['end'] = $bg_gr_color;
						$background['has_bg'] = true;
						$class[ 'primary' ] .= ' has-bg-gradient bg-gradient-' . $direction;
					}

					/***** Background Image */
					$is_default_bg = false;
					$bg_image_id = boombox_get_term_meta( $term_id, 'title_area_background_image' );
					if( ! $bg_image_id ) {
						$is_default_bg = true;
						$bg_image_id = boombox_get_theme_option( 'archive_header_default_background_image' );
					}
					if( $bg_image_id ) {
						$bg_image_data = wp_get_attachment_image_src( $bg_image_id, 'full' );

						if( $bg_image_data ) {

							$bg_img_size = 'cover';
							if( ! $is_default_bg ) {
								$bg_img_size = boombox_get_term_meta( $term_id, 'title_area_background_image_size' );
								$bg_img_size = $bg_img_size ? $bg_img_size : 'auto';
							}

							$bg_img_repeat = boombox_get_term_meta( $term_id, 'title_area_background_image_repeat' );
							$bg_img_repeat = $bg_img_repeat ? $bg_img_repeat : 'repeat-no';

							$background['features'][] = 'image';
							$background['url'] = $bg_image_data[0];
							$background['size'] = $bg_img_size;
							$background['repeat'] = $bg_img_repeat;
							$background['has_bg'] = true;

							$class[ 'primary' ] .= ' has-bg-img';
							$class[ 'secondary' ] .= ' bg-size-' . $bg_img_size . ' bg-' . $bg_img_repeat;

							if ( $bg_img_size == 'auto' ) {
								$bg_img_pos = boombox_get_term_meta( $term_id,'title_area_background_image_position' );
								$background[ 'position' ] = $bg_img_pos ? $bg_img_pos : 'center';

								$class[ 'secondary' ] .= ' bg-pos-' . $background[ 'position' ];
							}

						}
					}

					if ( $filters ) {
						$choices = Boombox_Choices_Helper::get_instance()->get_conditions();
						$default_choice = boombox_get_theme_option( 'archive_main_posts_default_order' );
						$current_choice = ( isset( $_GET[ 'order' ] ) && $_GET[ 'order' ] ) ?
							esc_sql( $_GET[ 'order' ] ) : $default_choice;
						if ( ! array_key_exists( $current_choice, $choices ) ) {
							$current_choice = $default_choice;
						}

						$queried_object = get_queried_object();
						if( is_a( $queried_object, 'WP_Term' ) ) {
							$current_url = get_term_link( $queried_object->term_id, $queried_object->taxonomy );
						} else {
							$current_url = get_permalink();
						}

						array_walk( $choices, function ( $label, $key ) use ( &$choices, $current_choice, $current_url ) {
							$choices[ $key ] = array(
								'label'  => $label,
								'url'    => esc_url( add_query_arg( 'order', $key, $current_url ) ),
								'active' => $current_choice == $key,
							);
						} );
						$filter_data[ 'choices' ] = $choices;
						$filter_data[ 'current' ] = $current_choice;

					}

					if ( $width ) {
						$class[ 'primary' ] .= ' ' . $width;
					}

					if ( $background['has_bg'] ) {
						$class[ 'primary' ] .= ' has-bg';
					}

				}

			}
			// page template
			else if ( is_page() ) {

				$template = 'page';
				if ( ! isset( $this->options[ $template ] ) ) {

					$post_id = get_the_ID();
					if( $post_id ) {
						$title = get_the_title( $post_id );
						$breadcrumb = in_array( 'page', boombox_get_theme_option( 'extras_breadcrumb_visibility' ) );
						$sub_title = '';
						if( boombox_is_trending( $post_id ) ) {
							$trending_nav = true;
						} else {
							$filters = ! boombox_get_post_meta( $post_id, 'boombox_title_area_hide_filter' );
							$filters = $filters && ( boombox_get_post_meta( $post_id, 'boombox_listing_type' ) != 'none' );
						}
						
						$badge = boombox_is_trending( $post_id );
						$text_color = boombox_get_post_meta( $post_id, 'boombox_title_area_text_color' );
						$width = boombox_get_post_meta( $post_id, 'boombox_title_area_background_container' );
						
						$background = array(
							'features' => array(),
							'has_bg'   => false
						);
						$bg_color = boombox_get_post_meta( $post_id, 'boombox_title_area_background_color' );
						if( $bg_color ) {
							
							$background[ 'features' ][] = 'color';
							$background[ 'color' ] = $bg_color;
							$background[ 'has_bg' ] = true;
							$class[ 'primary' ] .= ' has-bg-clr';
						}
						
						$bg_gr_color = boombox_get_post_meta( $post_id, 'boombox_title_area_background_gradient_color' );
						if( $bg_gr_color ) {
							$bg_color = $bg_color ? $bg_color : 'transparent';
							
							$direction = boombox_get_post_meta( $post_id, 'boombox_title_area_background_gradient_direction' );
							$direction = $direction ? $direction : 'top';
							
							$background[ 'features' ][] = 'gradient';
							$background[ 'start' ] = $bg_color;
							$background[ 'end' ] = $bg_gr_color;
							$background[ 'has_bg' ] = true;
							$class[ 'primary' ] .= ' has-bg-gradient bg-gradient-' . $direction;
						}
						
						$bg_image_id = boombox_get_post_meta( $post_id, 'boombox_title_area_background_image' );
						if( $bg_image_id ) {
							$bg_image_data = wp_get_attachment_image_src( $bg_image_id, 'full' );
							
							if( $bg_image_data ) {
								
								$bg_img_size = boombox_get_post_meta( $post_id, 'boombox_title_area_background_image_size' );
								$bg_img_size = $bg_img_size ? $bg_img_size : 'auto';
								
								$bg_img_repeat = boombox_get_post_meta( $post_id, 'boombox_title_area_background_image_repeat' );
								$bg_img_repeat = $bg_img_repeat ? $bg_img_repeat : 'repeat-no';
								
								$background[ 'features' ][] = 'image';
								$background[ 'url' ] = $bg_image_data[ 0 ];
								$background[ 'size' ] = $bg_img_size;
								$background[ 'repeat' ] = $bg_img_repeat;
								$background[ 'has_bg' ] = true;
								
								$class[ 'primary' ] .= ' has-bg-img';
								$class[ 'secondary' ] .= ' bg-size-' . $bg_img_size . ' bg-' . $bg_img_repeat;
								
								if( $bg_img_size == 'auto' ) {
									$bg_img_pos = boombox_get_post_meta( $post_id, 'boombox_title_area_background_image_position' );
									$background[ 'position' ] = $bg_img_pos ? $bg_img_pos : 'center';
									
									$class[ 'secondary' ] .= ' bg-pos-' . $background[ 'position' ];
								}
								
							}
						}
						
						if( $filters ) {
							$choices = Boombox_Choices_Helper::get_instance()->get_conditions();
							$default_choice = boombox_get_post_meta( $post_id, 'boombox_listing_condition' );
							$current_choice = ( isset( $_GET[ 'order' ] ) && $_GET[ 'order' ] ) ? esc_sql( $_GET[ 'order' ] ) : $default_choice;
							
							if( ! array_key_exists( $current_choice, $choices ) ) {
								$current_choice = $default_choice;
							}
							
							$queried_object = get_queried_object();
							$current_url = get_permalink( $queried_object->ID );
							
							array_walk( $choices, function ( $label, $key ) use ( &$choices, $current_choice, $current_url ) {
								$choices[ $key ] = array(
									'label'  => $label,
									'url'    => esc_url( add_query_arg( 'order', $key, $current_url ) ),
									'active' => $current_choice == $key,
								);
							} );
							$filter_data[ 'choices' ] = $choices;
							$filter_data[ 'current' ] = $current_choice;
							
						}
						
						if( $width ) {
							$class[ 'primary' ] .= ' ' . $width;
						}
						if( $background[ 'has_bg' ] ) {
							$class[ 'primary' ] .= ' has-bg';
						}
						
					}

				}

			}
			// search template
			else if( is_search() ) {
				$title = esc_html__( 'Search Results for:', 'boombox' ) . ' ' . esc_html( get_search_query() );
			}

			if ( ! isset( $this->options[ $template ] ) ) {
				$this->options[ $template ] = array(
					'title'           => $title,
					'sub_title'       => $sub_title,
					'badge'           => $badge ? $this->get_title_badge() : '',
					'filters'         => $filters,
					'trending_nav'    => $trending_nav,
					'width'           => $width,
					'text_color'      => $text_color,
					'background'      => $background,
					'class'           => $class,
					'filter_data'     => $filter_data,
					'breadcrumb'      => $breadcrumb
				);
			}

			return apply_filters( 'boombox/title_template_settings', $this->options[ $template ] );
		}

	}

}