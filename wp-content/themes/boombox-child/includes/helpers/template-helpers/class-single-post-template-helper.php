<?php
/**
 * Boombox Single Post Template Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Single_Post_Template_Helper' ) ) {

	final class Boombox_Single_Post_Template_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Single_Post_Template_Helper|null
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
		 * Boombox_Single_Post_Template_Helper constructor.
		 */
		private function __construct() {}

		/**
		 * A dummy magic method to prevent Boombox_Single_Post_Template_Helper from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Get single post layout
		 * @param int|null|WP_Post $post Optional. Post ID or post object. Defaults to global $post.
		 * @return array
		 */
		public function get_layout_options( $post = null ) {

			$post = get_post( $post );

			$sidebar_type = boombox_get_theme_option( 'single_post_general_sidebar_type' );
			$sidebar_orientation = boombox_get_theme_option( 'single_post_general_sidebar_orientation' );
			$sidebar_reverse = false;

			if ( 'custom' == boombox_get_post_meta( $post->ID, 'boombox_layout' ) ) {
				$sidebar_type = boombox_get_post_meta( $post->ID, 'boombox_sidebar_type' );
				if( ! $sidebar_type ) {
					$sidebar_type = '1-sidebar-1_3';
				}
				$sidebar_orientation = boombox_get_post_meta( $post->ID, 'boombox_sidebar_orientation' );
				if( ! $sidebar_orientation ) {
					$sidebar_orientation = 'right';
				}
				$sidebar_reverse = boombox_get_post_meta( $post->ID, 'boombox_sidebar_reverse' );
			}

			return compact( 'sidebar_type', 'sidebar_orientation', 'sidebar_reverse' );
		}

		/**
		 * Get hide elements options
		 * @return array
		 */
		public function get_hide_elements_options() {
			$hide_elements_options = array();

			$choices = Boombox_Choices_Helper::get_instance()->get_post_hide_elements();
			$hide_elements = boombox_get_theme_option( 'single_post_general_hide_elements' );
			foreach ( $choices as $name => $element ) {
				$hide_elements_options[ $name ] = ! in_array( $name, $hide_elements );
			}
			$hide_elements_options[ 'breadcrumb' ] = in_array( 'post', boombox_get_theme_option( 'extras_breadcrumb_visibility' ) );

			return $hide_elements_options;
		}

		/**
		 * Get cache key
		 * @param int|null|WP_Post $post Optional. Post ID or post object. Defaults to global $post.
		 *
		 * @return string
		 */
		private function get_cache_key( $post, $size ) {
			$post = get_post( $post );

			return $post ? 'single_post_' . $post->ID . '_' . $size : false;
		}

		/**
		 * Get image microdata from HTML
		 * @param string $html HTML
		 *
		 * @return array
		 */
		private function get_image_data_from_html( $html ) {

			$url = '';
			$width = '';
			$height = '';
			
			if( $html ) {
				preg_match( '/src="([^"]+)/i', $html, $matches );
				$url = isset( $matches[ 1 ] ) ? $matches[ 1 ] : false;
				
				preg_match( '/width="([^"]+)/i', $html, $matches );
				$width = isset( $matches[ 1 ] ) ? absint( $matches[ 1 ] ) : false;
				
				preg_match( '/height="([^"]+)/i', $html, $matches );
				$height = isset( $matches[ 1 ] ) ? absint( $matches[ 1 ] ) : false;
			}

			return apply_filters( 'boombox/image_microdata', compact( 'url', 'width', 'height' ), $html );
		}

		/**
		 * Get post microdata
		 * @param int|null|WP_Post $post Optional. Post ID or post object. Defaults to global $post.
		 *
		 * @return mixed
		 */
		public function get_microdata( $post = null ) {

			$post = get_post( $post );
			
			// region Logo URL
			$logo_url = apply_filters( 'boombox/single/microdata/logo_url', '' );
			if( ! $logo_url ) {
				$boombox_logo = boombox_get_logo();
				if( $boombox_logo ) {
					$logo_url = $boombox_logo[ 'src' ];
				}
			}
			// endregion
			
			$microdata = array(
				'publisher' => array(
					'logo'      => $logo_url,
					'blogname'  => get_option( 'blogname' ),
					'permalink' => home_url( '/' ),
				),
				'post'      => array(
					'published' => get_the_date( 'c', $post ),
					'updated'   => get_the_modified_date( 'c', $post ),
					'permalink' => get_permalink( $post ),
				),
			);

			return apply_filters( 'boombox/single_post/microdata', $microdata );
		}
		
		/**
		 * Get previous / next posts
		 * @param string $location Location ID where navigation will be used. Required to provide ability to modify posts to different location separately
		 *
		 * @return array
		 */
		public function get_post_next_prev_posts( $location ) {
			$args = array( 'in_same_term' => false, 'excluded_terms' => '', 'taxonomy' => 'category' );
			$r = wp_parse_args( apply_filters( 'boombox/' . $location . '/prev_post_args', $args ), $args );
			$prev = get_previous_post( $r[ 'in_same_term' ], $r[ 'excluded_terms' ], $r[ 'taxonomy' ] );
			
			$args = array( 'in_same_term' => false, 'excluded_terms' => '', 'taxonomy' => 'category' );
			$r = wp_parse_args( apply_filters( 'boombox/' . $location . '/next_post_args', $args ), $args );
			$next = get_next_post( $r[ 'in_same_term' ], $r[ 'excluded_terms' ], $r[ 'taxonomy' ] );
			
			return compact( 'prev', 'next' );
		}

		/**
		 * Get template options
		 * @param string $image_size Optional. Image Size. Default: 'boombox_image768'
		 * @param int|WP_Post|null $post Optional. Post ID or post object. Defaults to global $post.
		 * @return array
		 * @since 2.5.0
		 * @version 2.5.0
		 */
		public function get_options( $image_size = 'boombox_image768', $post = null ) {

			$post = get_post( $post );

			$cache_key = $this->get_cache_key( $post->ID, $image_size );
			$single_post_settings = boombox_cache_get( $cache_key );

			if( ! $single_post_settings ) {

				// region Featured Strip
				if ( boombox_is_fragment_cache_enabled() ) {
					?>
					<!-- mfunc <?php echo W3TC_DYNAMIC_SECURITY; ?>
						$featured_strip = in_array( 'post', boombox_get_theme_option( 'header_strip_visibility' ) );

						if( wp_is_mobile() ) {
							$featured_strip = ( $featured_strip && boombox_get_theme_option( 'mobile_global_enable_strip' ) );
						}
					-->
					<?php
					$featured_strip = in_array( 'post', boombox_get_theme_option( 'header_strip_visibility' ) );
					if( wp_is_mobile() ) {
						$featured_strip = ( $featured_strip && boombox_get_theme_option( 'mobile_global_enable_strip' ) );
					}
					?>
					<!-- /mfunc <?php echo W3TC_DYNAMIC_SECURITY; ?> -->
					<?php
				} else if ( boombox_is_page_cache_enabled() ) {
					$featured_strip = in_array( 'post', boombox_get_theme_option( 'header_strip_visibility' ) );
				} else {
					$featured_strip = in_array( 'post', boombox_get_theme_option( 'header_strip_visibility' ) );
					if( wp_is_mobile() ) {
						$featured_strip = ( $featured_strip && boombox_get_theme_option( 'mobile_global_enable_strip' ) );
					}
				}
				// endregion

				// region NSFW
				$is_nsfw = boombox_is_nsfw_post( $post->ID );
				$protect_content = false;
				if ( $is_nsfw && boombox_get_theme_option( 'extras_nsfw_require_auth' ) ) {
					$protect_content = ( ! is_user_logged_in() && boombox_is_auth_allowed() );
				}
				// endregion

				// region Sponsored articles
				$sponsored_articles = boombox_get_theme_option( 'single_post_sponsored_articles_position' );
				// endregion
				
				// region Template
				$template = boombox_get_theme_option( 'single_post_general_layout' );
				if( 'custom' == boombox_get_post_meta( $post->ID, 'boombox_layout' ) ) {
					$template = boombox_get_post_meta( $post->ID, 'boombox_template' );
					if( ! $template ) {
						$template = 'style1';
					}
				}
				$template = apply_filters( 'boombox/single_post/template', $template );
				// endregion
				
				// region Media data
				$show_media = boombox_show_media_for_post( $post->ID );

				$featured_image = '';
				$featured_image_src = '';
				$featured_media = '';
				$featured_caption = '';

				switch( $template ) {
					case 'style1':
					case 'style2':
						if( $show_media ) {
							$featured_media = boombox_get_post_featured_video( $post->ID, $image_size, array(
								'template' => 'single'
							) );

							if( ! $featured_media ) {
								if( boombox_get_post_meta( $post->ID, 'boombox_post_gallery' ) ) {
									$featured_media = boombox_get_post_gallery_html( $image_size, $post );
								} elseif( boombox_has_post_thumbnail() && boombox_show_multipage_thumbnail() ) {
									$featured_media = boombox_get_post_thumbnail( null, $image_size, array(
										'play'     => true,
										'template' => 'single'
									) );
									if ( $featured_media ) {
										$featured_caption = boombox_get_post_thumbnail_caption();
									}
								}
							}
						}
						break;
					case 'style3':
					case 'style4':
					case 'style5':
					case 'style6':
						$show_post_thumbnail = boombox_has_post_thumbnail();
						if( $show_post_thumbnail ) {
							$featured_image = boombox_get_post_thumbnail( null, $image_size, array(
								'play'     => false,
								'template' => 'single'
							) );
							$featured_image_full_data = $this->get_image_data_from_html( boombox_get_post_thumbnail( null, 'full', array(
								'play'     => false,
								'template' => 'single'
							) ) );
							$featured_image_src = $featured_image_full_data[ 'url' ];
						}
					
						if( $show_media ) {
							$featured_media = boombox_get_post_featured_video( $post->ID, $image_size, array(
								'template' => 'single'
							) );
							if( ! $featured_media ) {
								if( boombox_get_post_meta( $post->ID, 'boombox_post_gallery' ) ) {
									$featured_media = boombox_get_post_gallery_html( $image_size, $post );
								} else {
									if ( $show_post_thumbnail ) {
										$featured_media = boombox_get_post_thumbnail( $post->ID, $image_size, array(
											'play'     => true,
											'template' => 'single'
										) );
									}
									if ( $featured_media && ( $featured_media == $featured_image ) ) {
										$featured_media = '';
									} else {
										$featured_caption = boombox_get_post_thumbnail_caption();
									}
								}
							}
						}
						
						break;
				}
				
				// endregion
				
				// region Layout
				$layout = $this->get_layout_options( $post );
				// endregion
				
				// region Elements
				$elements = $this->get_hide_elements_options();
				// endregion
				
				// region Microdata
				$f_image = $featured_image ? $featured_image : boombox_get_post_thumbnail( null, $image_size, array(
					'play'     => false,
					'template' => 'single'
				) );
				$microdata = array_merge( $this->get_microdata( $post ), array(
					'thumbnail' => $this->get_image_data_from_html( $f_image ),
				) );
				// endregion

				$single_post_settings = array(
					'elements'                    => $elements,
					'post_id'                     => $post->ID,
					'classes'                     => 'single post bb-post-single ' . $template,
					'is_nsfw'                     => $is_nsfw,
					'protect_content'             => $protect_content,
					'pagination_layout'           => boombox_get_theme_option( 'single_post_general_pagination_layout' ),
					'featured_strip'              => $featured_strip,
					'featured_image'              => $featured_image,
					'featured_media'              => $featured_media,
					'featured_caption'            => $featured_caption,
					'featured_image_src'          => $featured_image_src,
					'template'                    => $template,
					'reading_time'                => in_array( 'post', boombox_get_theme_option( 'extras_reading_time_visibility' ) ),
					'microdata'                   => $microdata,
					'side_navigation'             => boombox_get_theme_option( 'single_post_general_side_navigation' ),
					'enable_primary_sidebar'      => boombox_is_primary_sidebar_enabled( $layout['sidebar_type'] ),
					'enable_secondary_sidebar'    => boombox_is_secondary_sidebar_enabled( $layout['sidebar_type'] ),
					'share'                       => array(
						'top'    => boombox_get_theme_option( 'single_post_general_top_sharebar' ),
						'bottom' => boombox_get_theme_option( 'single_post_general_bottom_sharebar' ),
					),
					'image_size'                  => $image_size,
					'sponsored_articles_location' => array(
						'top'    => in_array( $sponsored_articles, array( 'top', 'both' ) ),
						'bottom' => in_array( $sponsored_articles, array( 'bottom', 'both' ) )
					)
				);

				boombox_cache_set( $cache_key, $single_post_settings );
			}
			
			return apply_filters( 'boombox/single_template_settings', $single_post_settings );
		}

	}

}