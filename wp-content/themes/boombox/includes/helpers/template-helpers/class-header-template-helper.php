<?php
/**
 * Boombox Header Template Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.4
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Header_Template_Helper' ) ) {

	final class Boombox_Header_Template_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return Boombox_Header_Template_Helper|null
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
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
		 * Hold component location
		 * @var string
		 */
		private $component_location = '';

		/**
		 * Set component location
		 *
		 * @param string $location Location
		 */
		public function set_component_location( $location ) {
			$this->component_location = $location;
		}

		/**
		 * Get component location
		 * @return string
		 */
		public function get_component_location() {
			return $this->component_location;
		}

		/**
		 * Boombox_Header_Template_Helper constructor.
		 */
		private function __construct() {
		}

		/**
		 * A dummy magic method to prevent Boombox_Header_Template_Helper from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Get menu components
		 *
		 * @param string $v Vertical: top, bottom
		 * @param string $h Horizontal: left, right
		 *
		 * @return array
		 */
		private function get_menu_components( $v, $h ) {
			$components = boombox_get_theme_option( 'header_layout_' . $v . '_components' );

			return $components[ $h ];
		}

		/**
		 * Get classes for components wrapper
		 *
		 * @param array $components Active components
		 * @param array $classes    Current classes
		 *
		 * @return string
		 */
		public function get_components_wrapper_classes( $components, $classes ) {
			array_walk( $components, function ( $c ) use ( &$classes ) {
				$classes[] = 'has-' . $c;
			} );

			return implode( ' ', $classes );
		}

		/**
		 * Get composition item template slug
		 *
		 * @param string $id Composition item ID
		 *
		 * @return bool|string
		 */
		public function get_composition_item_template_slug( $id ) {
			$map = array(
				'search'          => 'template-parts/header/components/search',
				'social'          => 'template-parts/header/components/social',
				'authentication'  => 'template-parts/header/components/authentication',
				'more-menu-icons' => 'template-parts/header/components/more-menu-icons',
				'badges'          => 'template-parts/header/components/badges',
				'burger-icon'     => 'template-parts/header/components/burger-icon',
				'button-compose'  => 'template-parts/header/components/button-compose',
				'mode-switcher'   => 'template-parts/header/components/mode-switcher',
			);

			return isset( $map[ $id ] ) ? $map[ $id ] : false;
		}

		/**
		 * Get elements
		 * @return array
		 */
		public function get_options() {

			$cache_key = 'header_settings';
			$header_settings = boombox_cache_get( $cache_key );

			if( ! $header_settings ) {

				$top_include_in_color_mode_class =  boombox_get_theme_option( 'header_design_top_include_in_color_mode' ) ? 'include-in-color-mode' : '';
				$bottom_include_in_color_mode_class =  boombox_get_theme_option( 'header_design_bottom_include_in_color_mode' ) ? 'include-in-color-mode' : '';

				/***** Basic configuration */
				$header_settings = array(
					'logo_position'    => boombox_get_theme_option( 'header_layout_logo_position' ),
					'pattern_position' => boombox_get_theme_option( 'header_design_pattern_position' ),
					'class'            => 'bb-header header-desktop',
					'before'           => '',
					'after'            => '',
					'top'              => array(
						'enable'     => boombox_get_theme_option( 'header_layout_top_header' ),
						'class'      => 'top-header ' . $top_include_in_color_mode_class,
						'has_menu'   => false,
						'has_ad'     => false,
						'components' => array(
							'left'  => array(),
							'right' => array(),
						),
						'before'     => '',
						'after'      => '',
					),
					'bottom'           => array(
						'enable'     => boombox_get_theme_option( 'header_layout_bottom_header' ),
						'class'      => 'bottom-header ' . $bottom_include_in_color_mode_class,
						'has_menu'   => false,
						'has_ad'     => false,
						'components' => array(
							'left'  => array(),
							'right' => array(),
						),
						'before'     => '',
						'after'      => '',
					),
				);

				/***** Top menu options */
				if ( $header_settings[ 'top' ][ 'enable' ] ) {
					// Composition
					$header_settings[ 'top' ][ 'composition' ] = boombox_get_theme_option( 'header_layout_top_layer_composition' );

					// Has Menu
					$header_settings[ 'top' ][ 'has_menu' ] = in_array( $header_settings[ 'top' ][ 'composition' ], array(
						'brand-l_menu-l', 'brand-l_menu-c', 'brand-l_menu-r',
					) );

					// Has Ad
					$header_settings[ 'top' ][ 'has_ad' ] = in_array( $header_settings[ 'top' ][ 'composition' ], array(
						'brand-l_ad-r',
					) );

					// Components
					$header_settings[ 'top' ][ 'components' ][ 'left' ] = $this->get_menu_components( 'top', 'left' );
					$header_settings[ 'top' ][ 'components' ][ 'right' ] = $this->get_menu_components( 'top', 'right' );

					// Classes
					$header_settings[ 'top' ][ 'class' ] .= ' ' . esc_attr( esc_html( boombox_get_theme_option( 'header_layout_top_header_height' ) ) );
                    $header_settings[ 'top' ][ 'class' ] .= ' ' . boombox_get_theme_option( 'header_layout_top_header_width' );

					if ( 'brand-c' == $header_settings[ 'top' ][ 'composition' ] ) {
						$header_settings[ 'top' ][ 'class' ] .= ' logo-center';
					}
					if ( $header_settings[ 'top' ][ 'has_menu' ] ) {
						switch ( $header_settings[ 'top' ][ 'composition' ] ) {
							case 'brand-l_menu-l':
								$header_settings[ 'top' ][ 'class' ] .= ' menu-left';
								break;
							case 'brand-l_menu-c':
								$header_settings[ 'top' ][ 'class' ] .= ' menu-center';
								break;
							case 'brand-l_menu-r':
								$header_settings[ 'top' ][ 'class' ] .= ' menu-right';
								break;
						}
					}
				} else {
					$header_settings[ 'class' ] .= ' no-top';
				}

				/***** Bottom menu options */
				if ( $header_settings[ 'bottom' ][ 'enable' ] ) {
					// Composition
					$header_settings[ 'bottom' ][ 'composition' ] = boombox_get_theme_option( 'header_layout_bottom_layer_composition' );

					// Has Menu
					$header_settings[ 'bottom' ][ 'has_menu' ] = in_array( $header_settings[ 'bottom' ][ 'composition' ], array(
						'brand-l_menu-l', 'brand-l_menu-c', 'brand-l_menu-r',
					) );

					// Has Ad
					$header_settings[ 'bottom' ][ 'has_ad' ] = in_array( $header_settings[ 'bottom' ][ 'composition' ], array(
						'brand-l_ad-r',
					) );

					// Components
					$header_settings[ 'bottom' ][ 'components' ][ 'left' ] = $this->get_menu_components( 'bottom', 'left' );
					$header_settings[ 'bottom' ][ 'components' ][ 'right' ] = $this->get_menu_components( 'bottom', 'right' );

					// Classes
					$header_settings[ 'bottom' ][ 'class' ] .= ' ' . esc_attr( esc_html( boombox_get_theme_option( 'header_layout_bottom_header_height' ) ) );
                    $header_settings[ 'bottom' ][ 'class' ] .= ' ' . boombox_get_theme_option( 'header_layout_bottom_header_width' );

					if ( 'brand-c' == $header_settings[ 'bottom' ][ 'composition' ] ) {
						$header_settings[ 'bottom' ][ 'class' ] .= ' logo-center';
					}
					if ( $header_settings[ 'bottom' ][ 'has_menu' ] ) {
						switch ( $header_settings[ 'bottom' ][ 'composition' ] ) {
							case 'brand-l_menu-l':
								$header_settings[ 'bottom' ][ 'class' ] .= ' menu-left';
								break;
							case 'brand-l_menu-c':
								$header_settings[ 'bottom' ][ 'class' ] .= ' menu-center';
								break;
							case 'brand-l_menu-r':
								$header_settings[ 'bottom' ][ 'class' ] .= ' menu-right';
								break;
						}
					}
				} else {
					$header_settings[ 'class' ] .= ' no-bottom';
				}

				/***** Shadow position */
				$shadow_position = boombox_get_theme_option( 'header_layout_shadow_position' );
				if ( ( 'none' != $shadow_position ) && $header_settings[ $shadow_position ][ 'enable' ] ) {
					$header_settings[ 'class' ] .= ' ' . $shadow_position . '-shadow';
				}

				/***** Pattern Position*/
				$pattern_position = boombox_get_theme_option( 'header_design_pattern_position' );
				if ( ( 'none' != $pattern_position ) && $header_settings[ $pattern_position ][ 'enable' ] ) {
					$header_settings[ 'class' ] .= ' ' . $pattern_position . '-bg';
				}

				/***** Sticky */
				$sticky_position = boombox_get_theme_option( 'header_layout_sticky_header' );
				// We need to disable header sticky if single post has enabled "Floating Navigation Bar" option
				if( is_single() && ( 'none' != boombox_get_theme_option( 'single_post_general_floating_navbar' ) ) ) {
					$sticky_position = 'none';
				}
				$sticky_type = boombox_get_theme_option( 'header_layout_sticky_type' );
				switch ( $sticky_position ) {
					case 'top':
						$header_settings[ 'class' ] = 'bb-show-desktop-header ' . $header_settings[ 'class' ];
						$header_settings[ 'top' ][ 'before' ] = '<div class="bb-sticky bb-sticky-nav sticky-' . $sticky_type . '">';
						$header_settings[ 'top' ][ 'after' ] = '</div>';
						$header_settings[ 'top' ][ 'class' ] .= ' bb-sticky-el';

						break;
					case 'bottom':
						$header_settings[ 'class' ] = 'bb-show-desktop-header ' . $header_settings[ 'class' ];
						$header_settings[ 'bottom' ][ 'before' ] = '<div class="bb-sticky bb-sticky-nav sticky-' . $sticky_type . '">';
						$header_settings[ 'bottom' ][ 'after' ] = '</div>';
						$header_settings[ 'bottom' ][ 'class' ] .= ' bb-sticky-el';

						break;
					case 'both':
						$header_settings[ 'class' ] .= ' bb-sticky-el';
						$header_settings[ 'before' ] = '<div class="bb-show-desktop-header bb-sticky bb-sticky-nav sticky-' . $sticky_type . '">';
						$header_settings[ 'after' ] = '</div>';

						break;
					default:
						$header_settings[ 'class' ] = 'bb-show-desktop-header ' . $header_settings[ 'class' ];
				}

				boombox_cache_set( $cache_key, $header_settings );

			}

			return apply_filters( 'boombox/header_template_settings', $header_settings );
		}

		/**
		 * Whether header has a component
		 *
		 * @param array $args       Search arguments {
		 *
		 * @type array  $location   Location: 'desktop', 'mobile'
		 * @type array  $horizontal Horizontal location: 'left', 'right'
		 * @type array  $veritcal   Vertical location: 'top', 'bottom'
		 *
		 * }
		 * @return bool
		 */
		public function has_component( $component, $args = array() ) {
			$r = wp_parse_args( $args, array(
				'location'   => array( 'desktop', 'mobile' ),
				'horizontal' => array( 'left', 'right' ),
				'vertical'   => array( 'top', 'bottom' ),
			) );

			$components = array();
			foreach ( $r[ 'location' ] as $location ) {
				if ( $location == 'desktop' ) {
					foreach ( $r[ 'vertical' ] as $vertical ) {
						$v_components = boombox_get_theme_option( 'header_layout_' . $vertical . '_components' );

						foreach ( $r[ 'horizontal' ] as $horizontal ) {
							if ( isset( $v_components[ $horizontal ] ) ) {
								$components = array_merge( $components, (array)$v_components[ $horizontal ] );
							}
						}
					}
				} else if ( $location == 'mobile' ) {
					$v_components = boombox_get_theme_option( 'mobile_header_components' );

					foreach ( $r[ 'horizontal' ] as $horizontal ) {
						if ( isset( $v_components[ $horizontal ] ) ) {
							$components = array_merge( $components, (array)$v_components[ $horizontal ] );
						}
					}
				}
			}

			return in_array( $component, array_unique( $components ) );
		}

		/**
		 * Get mobile header layout
		 * @return string
		 */
		public function get_mobile_layout() {
			$layout = boombox_get_theme_option( 'mobile_header_composition' );

			return apply_filters( 'boombox/mobile/header/layout', $layout );
		}

		/**
		 * Get mobile options
		 * return array
		 */
		public function get_mobile_options() {
			$layout = $this->get_mobile_layout();

			$set = boombox_get_theme_options_set( array(
				'mobile_header_sticky',
				'mobile_header_components',
				'single_post_general_floating_navbar'
			) );

			$before = '';
			$after = '';
			$class = 'bb-show-mobile-header';
			$components_before = '';
			$components_after = '';
			$components_class = '';

			/***** Sticky */
			// We need to disable header sticky if single post has enabled "Floating Navigation Bar" option
			if( is_single() && ( 'none' != $set['single_post_general_floating_navbar'] ) ) {
				$set[ 'mobile_header_sticky' ] = 'none';
			}

			if ( 'none' != $set[ 'mobile_header_sticky' ] ) {
				if ( in_array( $layout, array( 'brand-t', 'brand-b' ) ) ) {
					$components_before = '<div class="bb-sticky bb-sticky-nav sticky-' . $set[ 'mobile_header_sticky' ] . '">';
					$components_after = '</div>';
					$class = 'bb-show-mobile-header';
					$components_class = 'bb-sticky-el';
				} else {
					$before = '<div class="bb-show-mobile-header bb-sticky bb-sticky-nav sticky-' . $set[ 'mobile_header_sticky' ] . '">';
					$after = '</div>';
					$class = 'bb-sticky-el';
				}
			}

			if( boombox_get_theme_option( 'mobile_header_include_in_color_mode' ) ){
				$class .= ' include-in-color-mode';
			}

			$template_options = array(
				'before'            => $before,
				'after'             => $after,
				'class'             => $class,
				'components_before' => $components_before,
				'components_after'  => $components_after,
				'components_class'  => $components_class,
				'components'        => $set[ 'mobile_header_components' ],
			);

			return apply_filters( 'boombox/mobile/header/template_options', $template_options, $layout );
		}

		/**
		 * Get branding options for mobile template
		 * @return array
		 */
		public function get_mobile_branding_options() {
			$key = 'mobile_branding_options';
			if( is_single() ) {
				$key .= '_single_post';
			} elseif( is_category() ) {
				$key .= '_taxonomy_category_' . get_queried_object_id();
			}
			$data = boombox_cache_get( $key );

			if ( ! $data ) {
				$set = boombox_get_theme_options_set( array(
					'mobile_header_logo',
					'mobile_header_logo_hdpi',
					'mobile_header_logo_width',
					'mobile_header_logo_height'
				) );
				if( is_single() ) {
					$category_ids = wp_get_post_categories( get_the_ID(), array(
						'fields'     => 'ids',
						'number'     => 1,
						'meta_query' => array(
							'relation' => 'OR',
							array(
								'key'       => 'boombox_term_mobile_logo',
								'value'     => '',
								'compare'   => '!='
							),
							array(
								'key'       => 'boombox_term_mobile_logo_hdpi',
								'value'     => '',
								'compare'   => '!='
							)
						)
					) );
					if( ! empty( $category_ids ) ) {
						$cat_id = $category_ids[ 0 ];
						$term_logo = boombox_get_term_meta( $cat_id, 'boombox_term_mobile_logo' );
						$term_logo_hdpi = boombox_get_term_meta( $cat_id, 'boombox_term_mobile_logo_hdpi' );
						$set = array(
							'mobile_header_logo'        => $term_logo ? wp_get_attachment_url( $term_logo ) : '',
							'mobile_header_logo_hdpi'   => $term_logo_hdpi ? wp_get_attachment_url( $term_logo_hdpi ) : '',
							'mobile_header_logo_width'  => boombox_get_term_meta( $cat_id, 'boombox_term_mobile_logo_width' ),
							'mobile_header_logo_height' => boombox_get_term_meta( $cat_id, 'boombox_term_mobile_logo_height' )
						);
					}
				} elseif( is_category() ) {
					$cat_id = get_queried_object_id();
					$term_logo = boombox_get_term_meta( $cat_id, 'boombox_term_mobile_logo' );
					$term_logo_hdpi = boombox_get_term_meta( $cat_id, 'boombox_term_mobile_logo_hdpi' );
					if( $term_logo || $term_logo_hdpi ) {
						$set = array(
							'mobile_header_logo'        => $term_logo ? wp_get_attachment_url( $term_logo ) : '',
							'mobile_header_logo_hdpi'   => $term_logo_hdpi ? wp_get_attachment_url( $term_logo_hdpi ) : '',
							'mobile_header_logo_width'  => boombox_get_term_meta( $cat_id, 'boombox_term_mobile_logo_width' ),
							'mobile_header_logo_height' => boombox_get_term_meta( $cat_id, 'boombox_term_mobile_logo_height' )
						);;
					}
				}

				if ( ! empty ( $set[ 'mobile_header_logo' ] ) || ! empty ( $set[ 'mobile_header_logo_hdpi' ] ) ) {

					$logo_data = array();
					$logo_data[ 'width' ] = $set[ 'mobile_header_logo_width' ];
					$logo_data[ 'height' ] = $set[ 'mobile_header_logo_height' ];
					$logo_data[ 'src_2x' ] = array();

					if ( ! empty ( $set[ 'mobile_header_logo_hdpi' ] ) ) {
						$logo_data[ 'src_2x' ][] = $set[ 'mobile_header_logo_hdpi' ] . ' 2x';
					}

					if ( ! empty ( $set[ 'mobile_header_logo' ] ) ) {
						$logo_data[ 'src' ] = $set[ 'mobile_header_logo' ];
						$logo_data[ 'src_2x' ][] = $set[ 'mobile_header_logo' ] . ' 1x';
					} else {
						$logo_data[ 'src' ] = $set[ 'mobile_header_logo_hdpi' ];
					}

					$logo_data[ 'src_2x' ] = implode( ',', $logo_data[ 'src_2x' ] );

				} else {
					$logo_data = boombox_get_logo();
				}

				$data = array(
					'site_name' => get_bloginfo( 'name' ),
					'logo'      => $logo_data,
				);

				boombox_cache_set( $key, $data );

			}

			return apply_filters( 'boombox/mobile/header/branding_data', $data );

		}



	}

}