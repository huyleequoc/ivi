<?php
/**
 * AMP plugin functions
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( boombox_plugin_management_service()->is_plugin_active( 'amp/amp.php' ) ) {

	if ( ! class_exists( 'Boombox_Amp' ) ) {
		
		class Boombox_Amp {

			/**
			 * Holds class single instance
			 * @var null
			 */
			private static $_instance = null;

			/**
			 * Get instance
			 * @return Boombox_Amp|null
			 */
			public static function get_instance() {

				if ( null == static::$_instance ) {
					static::$_instance = new self();
				}

				return static::$_instance;

			}

			/**
			 * Boombox_Amp constructor.
			 */
			private function __construct() {
				require_once( 'classes' . DIRECTORY_SEPARATOR . 'class-amp-menu-walker.php' );
				require_once( 'classes' . DIRECTORY_SEPARATOR . 'class-amp-customizer-control-multiple-checkbox.php' );

				$this->hooks();

				do_action( 'boombox/amp/wakeup', $this );
			}

			/**
			 * A dummy magic method to prevent Boombox_Amp from being cloned.
			 *
			 */
			public function __clone() {
				throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
			}

			/**
			 * Holds customizer settings
			 * @var array
			 */
			private $customizer_settings = array();

			/**
			 * Setup Hooks
			 */
			private function hooks() {
				add_filter( 'amp_post_template_dir', array( $this, 'set_template_dir' ), 10, 1 );
				add_filter( 'amp_post_template_data', array( $this, 'post_template_data' ), 10, 2 );
				add_filter( 'amp_post_template_metadata', array( $this, 'post_template_metadata' ), 10, 2 );
				add_action( 'pre_amp_render_post', array( $this, 'setup_template_hooks' ), 10, 1 );
				add_action( 'boombox/amp/comments', array( $this, 'render_comments_add_button' ), 10, 1 );
				
				add_action( 'amp_customizer_init', array( $this, 'customizer_init' ), 20 );
				add_filter( 'amp_customizer_get_settings', array( $this, 'customizer_append_settings' ), 20, 1 );

				add_filter( 'boombox/admin/taxonomy/meta-boxes/structure', array( $this, 'term_meta_boxes_add_fields' ), 10, 3 );

				add_filter( 'amp_post_status_default_enabled', array( $this, 'post_amp_skip_backward_compatibility' ), 10, 2 );
				add_filter( 'amp_skip_post', array( $this, 'may_be_skip_post_amp_version' ), 10, 3 );

				add_filter( 'amp_post_template_analytics', array($this, 'boombox_amp_ajax_track_view'), 10, 2 );
			}
			
			/**
             * Check for AMP endpoint
			 * @return bool
			 */
			public function is_amp() {
				return function_exists( 'amp_is_request' ) ? amp_is_request() : false;
            }
			
			/**
			 * Init customizer options
			 */
			public function customizer_init() {
				add_action( 'amp_customizer_register_settings', array( $this, 'register_customizer_settings' ), 20, 1 );
				add_action( 'amp_customizer_register_ui', array( $this, 'customizer_register_ui' ), 20, 1 );

				add_action( 'amp_customizer_enqueue_scripts', array( $this, 'customizer_enqueue_scripts' ) );
				add_action( 'amp_customizer_enqueue_preview_scripts', array( $this, 'enqueue_customizer_preview_scripts' ) );
			}
			
			/**
			 * Add customizer settings
			 * @param $wp_customize
			 */
			public function register_customizer_settings( $wp_customize ) {

				$defaults = $this->get_customizer_defaults();

				// AMP Logo
				$wp_customize->add_setting( 'amp_customizer[boombox_logo]', array(
					'type'              => 'option',
					'default'           => $defaults['boombox_logo'],
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'postMessage'
				) );
				
				// AMP HDPI Logo
				$wp_customize->add_setting( 'amp_customizer[boombox_logo_hdpi]', array(
					'type'              => 'option',
					'default'           => $defaults['boombox_logo_hdpi'],
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'postMessage'
				) );

				// AMP Hide Elements
				$wp_customize->add_setting( 'amp_customizer[boombox_hide_elements]', array(
					'type'              => 'option',
					'default'           => $defaults['boombox_hide_elements'],
					'sanitize_callback' => array( 'Boombox_AMP_Customize_Control_Multiple_Checkbox', 'validate' ),
					'transport'         => 'postMessage'
				) );
			}
			
			/**
			 * Add customizer UI
			 * @param $wp_customize
			 */
			public function customizer_register_ui( $wp_customize ) {
				
				// remove default header background color option
				$wp_customize->remove_control( 'amp_header_background_color' );
				
				// header background color
				$wp_customize->add_control(
					new WP_Customize_Color_Control( $wp_customize, 'amp_header_background_color', array(
						'settings'   => 'amp_customizer[header_background_color]',
						'label'    => __( 'Header Background Color', 'amp' ),
						'section'  => 'amp_design',
						'priority' => 20
					) )
				);
				
				// Logo
				$wp_customize->add_control(
					new WP_Customize_Media_Control( $wp_customize, 'amp_boombox_logo', array(
						'settings'      => 'amp_customizer[boombox_logo]',
						'label'         => __( 'Logo', 'boombox' ),
						'description'   => sprintf( __( 'The logo should be a rectangle, not a square. The logo should fit in a 60px height and 600px width rectangle, and either be exactly 60px high (preferred), or exactly 600px wide. <a href="https://developers.google.com/search/docs/data-types/articles#logo-guidelines" target="_blank" rel="noopener">read more</a>', 'boombox' ) ),
						'section'       => 'amp_design',
						'priority'      => 50
					) )
				);

				// Logo HDPI
				$wp_customize->add_control(
					new WP_Customize_Media_Control( $wp_customize, 'amp_boombox_logo_hdpi', array(
						'settings'      => 'amp_customizer[boombox_logo_hdpi]',
						'label'         => __( 'Logo HDPI', 'boombox' ),
						'description' => esc_html__( 'An image for High DPI screen (like Retina) should be twice as big.', 'boombox' ),
						'section'       => 'amp_design',
						'priority'      => 53
					) )
				);

				// Hide Elements
				$wp_customize->add_control(
					new Boombox_AMP_Customize_Control_Multiple_Checkbox( $wp_customize, 'amp_boombox_hide_elements', array(
						'settings'      => 'amp_customizer[boombox_hide_elements]',
						'label'         => __( 'Hide Elements', 'boombox' ),
						'section'       => 'amp_design',
						'choices'       => array(
							'related_posts'   => esc_html( '"Related Posts" Section', 'boombox' ),
							'more_from_posts' => esc_html( '"More From" Section', 'boombox' ),
							'dont_miss_posts' => esc_html( '"Don\'t Miss" Section', 'boombox' ),
						),
						'priority'      => 54
					) )
				);

			}
			
			/**
			 * @param $settings
			 *
			 * @return mixed
			 */
			public function customizer_append_settings( $settings ) {

				$set = boombox_get_theme_options_set( array(
					'design_primary_color',
					'design_primary_text_color',
					'design_link_text_color',
					'extras_badges_reactions_background_color',
					'design_border_radius',
					'design_inputs_buttons_border_radius'
				) );

				$settings = array_merge( $settings, array(
					'design_primary_color'                          => $set['design_primary_color'],
					'design_primary_text_color'                     => $set['design_primary_text_color'],
					'design_link_text_color'                        => $set['design_link_text_color'],
					'extras_badges_reactions_background_color'      => $set['extras_badges_reactions_background_color'],
					'design_border_radius'                          => $set['design_border_radius'] . 'px',
					'design_inputs_buttons_border_radius'           => $set['design_inputs_buttons_border_radius'] . 'px',
					'constants'                                     => $this->get_customizer_constants()
				) );
				
				$settings = wp_parse_args( $settings, $this->get_customizer_defaults() );
				
				return $settings;
			}
			
			/**
			 * AMP customizer preview scripts
			 */
			public function enqueue_customizer_preview_scripts() {
				$min = boombox_get_minified_asset_suffix();

				// IMPORTANT: detach native one, as we have a custom
				wp_dequeue_script( 'amp-customizer-design-preview' );

				wp_enqueue_script(
					'bb-amp-customizer-design-preview',
					BOOMBOX_INCLUDES_URL . 'plugins/amp/assets/js/amp-customizer-design-preview' . $min . '.js',
					array( 'amp-customize-preview' ),
					false,
					true
				);
				wp_localize_script( 'bb-amp-customizer-design-preview', 'bamp_customizer_design', array(
					'constants' => $this->get_customizer_constants(),
				) );
			}

			/**
			 * AMP customizer control scripts
			 */
			public function customizer_enqueue_scripts() {
				$min = boombox_get_minified_asset_suffix();

				wp_enqueue_script(
					'bamp-customizer-controls',
					BOOMBOX_INCLUDES_URL . 'plugins/amp/assets/js/amp-customize-controls' . $min . '.js',
					array( 'jquery' ),
					false,
					true
				);
			}
			
			/**
			 * Get customizer constants
			 * @return array
			 */
			private function get_customizer_constants() {
				return array(
					'footer_bg_clr'             => '#121013',
					
					'dark_bg_clr'               => '#232323',
					'dark_border_clr'           => '#292c3b',
					'dark_txt_clr'              => '#ececec',
					'dark_sec_txt_clr'          => '#8c8d8f',
					'dark_sec_bg_clr'           => '#292c3b',
					
					'light_bg_clr'              => '#fff',
					'light_border_clr'          => '#ececec',
					'light_txt_clr'             => '#1f1f1f',
					'light_sec_txt_clr'         => '#a7a7a7',
					'light_sec_bg_clr'          => '#f7f7f7',
					'light_sidebar_border_clr'  => '#282828'
				);
			}
			
			/**
			 * Get customizer defaults
			 * @return array
			 */
			private function get_customizer_defaults() {
				return apply_filters( 'boombox/amp/customizer_defaults', array(
					'boombox_logo' => '',
					'boombox_logo_hdpi' => '',
					'boombox_hide_elements' => array()
				) );
			}
			
			/**
			 * Set plugin templates location within theme
			 * @param $dir
			 * @return string
			 */
			public function set_template_dir( $dir ) {
				$dir = BOOMBOX_THEME_PATH . 'amp';
				
				return $dir;
			}
			
			/**
			 * Setup template hooks
			 *
			 * @param $post_id
			 */
			public function setup_template_hooks( $post_id ) {
				add_filter( 'boombox/hide-badges', array( $this, 'bamp_hide_badges' ), 10, 1 );
				add_filter( 'boombox/term_personal_styles_query', array( $this, 'edit_term_personal_styles_query' ), 10, 1 );
			}
			
			/**
			 * Force hide trending badges
			 *
			 * @param $args
			 * @return mixed
			 */
			public function bamp_hide_badges( $args ) {
				$args['hide_category_badges'] = true;
				return $args;
			}

			/**
			 * Edit term styles query
			 * @param string $query Current query
			 *
			 * @return string
			 */
			public function edit_term_personal_styles_query( $query ) {

				$query.= " AND `tt`.`taxonomy` = 'reaction'";

				return $query;
			}

			/**
			 * Set template additional data
			 * @param array $data Current data
			 * @param WP_Post Current post object
			 *
			 * @return array
			 * @throws Exception
			 */
			public function post_template_data( $data, $post ) {

				$this->customizer_settings = $data[ 'customizer_settings' ];

				// scripts
				$data[ 'amp_component_scripts' ] = array_merge( $data[ 'amp_component_scripts' ], array(
					'amp-form'      => 'https://cdn.ampproject.org/v0/amp-form-0.1.js',
					'amp-sidebar'   => 'https://cdn.ampproject.org/v0/amp-sidebar-0.1.js'
				) );

				// theme settings
				$data[ 'boombox_settings' ] = (object)array(
					'is_customizer_preview' => is_customize_preview()
				);

				// template elements options
                $boombox_template_options = array_merge( Boombox_Template::init( 'post' )->get_hide_elements_options(), array(
                    'media'         => boombox_show_media_for_post( $post->ID ),
                    'author_info'   => in_array( 'author_info', boombox_get_theme_option( 'single_post_general_sections' ) ),
                    'navigation'    => in_array( 'navigation', boombox_get_theme_option( 'single_post_general_sections' ) ),
	                'related_posts' => ! in_array( 'related_posts', $data['customizer_settings']['boombox_hide_elements'] ),
	                'more_from_posts' => ! in_array( 'more_from_posts', $data['customizer_settings']['boombox_hide_elements'] ),
	                'dont_miss_posts' => ! in_array( 'dont_miss_posts', $data['customizer_settings']['boombox_hide_elements'] ),
                ) );
				$data[ 'boombox_template_options' ] = (object)$boombox_template_options;
				
				// footer settings
				$data[ 'boombox_footer_settings' ] = (object)Boombox_Template::init( 'footer' )->get_options();

				//template grid element settings
				$data[ 'boombox_template_grid_elements_options' ] = (object)Boombox_Template::init( 'collection-item' )->get_options();
				
				return $data;
			}

			/**
			 * Edit post metadata
			 * @param array $metadata Current metadata
			 * @param WP_Post $post Current post object
			 *
			 * @return array
			 */
			public function post_template_metadata( $metadata, $post ) {
				$metadata['@type'] = 'Article';
				$metadata['description'] = $post->post_excerpt;

				$logo = $this->get_logo( (int)$this->customizer_settings['boombox_logo'], (int)$this->customizer_settings['boombox_logo_hdpi'] );
				if( ! empty ( $logo ) ) {
					$src    = esc_url( $logo['src'] );
					$width  = absint( $logo['width'] );
					$height  = absint( $logo['height'] );

					$metadata['publisher']['logo'] = array(
						'@type' => 'ImageObject',
						'url'   => $src,
					);

					if( $width ) {
						$metadata['publisher']['logo']['width'] = $width;
					}
					if( $height ) {
						$metadata['publisher']['logo']['height'] = $height;
					}
				}

				$metadata['author']['url'] = esc_url( get_author_posts_url( $post->post_author ) );
				$metadata['author']['image'] = $this->get_avatar_url_from_html( get_avatar( $post->post_author, 133 ) );

				return $metadata;
			}
			
			/**
			 * Render categories list
			 *
			 * @param array $args
			 */
			public function post_categories_list( $args = array() ) {
				$args = wp_parse_args( $args, array(
					'before' => '',
					'after'  => '',
				) );
				echo $args['before'] . get_the_category_list( ' ' ) . $args['after'];
			}
			
			/**
			 * Render tags list
			 *
			 * @param array $args
			 */
			public function post_tags_list( $args = array() ) {
				$args = wp_parse_args( $args, array(
					'before' => '',
					'after'  => '',
				) );
				
				echo get_the_tag_list( $args['before'], '', $args['after'] );
			}
			
			/**
			 * Get current post author ID
			 * @return int
			 */
			public function get_current_post_author_id() {
				global $post;
				
				return (int) $post->post_author;
			}
			
			/**
			 * Get post author URL
			 *
			 * @param null $author_id
			 * @return string
			 */
			public function get_author_url( $author_id = null ) {
				return esc_url( get_author_posts_url( $author_id ) );
			}
			
			/**
			 * Get post author name
			 *
			 * @param null $author_id
			 * @return string
			 */
			public function get_author_name( $author_id = null ) {
				return wp_kses_post( get_the_author_meta( 'display_name', $author_id ) );
			}
			
			/**
			 * Get avatar URL from avatar HTML
			 *
			 * @param $html
			 * @return bool
			 */
			public function get_avatar_url_from_html( $html ) {
				preg_match('/src *= *["\']?([^"\']*)/i', $html, $thumbnail_url_matches);
				return isset( $thumbnail_url_matches[1] ) ? $thumbnail_url_matches[1] : false;
			}
			
			/**
			 * Render AMP image
			 *
			 * @param array $atts
			 * @param bool|true $echo
			 * @return string
			 */
			public function render_image( $atts = array(), $echo = true ) {
				array_walk( $atts, function( &$value, $key ){
					$value = sprintf( '%1$s="%2$s"', $key, $value );
				} );
				
				$atts = implode( ' ', array_values( $atts ) );
				
				$html = sprintf('<amp-img %s></amp-img>', $atts);
				if( $echo ) {
					echo $html;
				} else {
					return $html;
				}
			}
			
			/**
			 * Render post formated time
			 * @param null $post
			 */
			public function render_post_time_tag( $post = null ) {
				$display_format = apply_filters( 'boombox/post_date_render_format', 'difference' );

				if( 'difference' == $display_format ){
					$date = apply_filters( 'boombox_post_date' , human_time_diff( get_the_time( 'U', $post ), current_time( 'timestamp' ) ) . " " . esc_html__( 'ago', 'boombox' ) );
				}else{
					$format = get_option( 'date_format' );
					$date = date_i18n( $format, get_the_time( 'U' ) );
				}

				printf( '<time class="posted-on m-r-xs" datetime="%1$s">%2$s</time>', esc_attr( get_the_date( 'c', $post ) ), $date );
			}
			
			/**
			 * Get full version text
			 *
			 * @return string
			 */
			public function get_full_version_text() {
				return apply_filters( 'boombox/amp/full_version_text', esc_html__( 'View Full Version', 'boombox' ) );
			}
			
			/*
			 * AMP render add comment button
			 */
			public function render_comments_add_button( $instance ) {
				$btn_url = $instance->get( 'comments_link_url' );
				$btn_label = $instance->get( 'comments_link_text' );
				printf( '<div class="container text-center"><a class="bb-btn btn-default hvr-btm-shadow" href="%1$s">%2$s</a></div>', $btn_url, $btn_label );
			}
			
			/**
			 * Get AMP logo
			 * @param $logo_id
			 * @param $logo_2x_id
			 *
			 * @return mixed
			 */
			public function get_logo( $logo_id, $logo_2x_id ) {
				
				$amp_logo = wp_cache_get( 'boombox_amp_logo' );
				
				if( ! (bool) $amp_logo ) {
					
					if ( $logo_id || $logo_2x_id ) {
						
						$width   = '';
						$height  = '';
						$logo    = '';
						$logo_2x = '';
						
						if ( $logo_2x_id ) {
							$logo_2x_data = wp_get_attachment_image_src( $logo_2x_id, 'full' );
							$logo_2x      = $logo_2x_data[0];
							$width        = $logo_2x_data[1];
							$height       = $logo_2x_data[2];
						}
						
						if ( $logo_id ) {
							$logo_data = wp_get_attachment_image_src( $logo_id, 'full' );
							$logo      = $logo_data[0];
							$width     = $logo_data[1];
							$height    = $logo_data[2];
						}
						
						$amp_logo['width']  = $width;
						$amp_logo['height'] = $height;
						$amp_logo['src_2x'] = array();
						
						if ( ! empty ( $logo_2x ) ) {
							$amp_logo['src_2x'][] = $logo_2x . ' 2x';
						}
						
						if ( ! empty ( $logo ) ) {
							$amp_logo['src']      = $logo;
							$amp_logo['src_2x'][] = $logo . ' 1x';
						} else {
							$amp_logo['src'] = $logo_2x;
						}
						
						$amp_logo['src_2x'] = implode( ',', $amp_logo['src_2x'] );
					} else {
						$amp_logo = boombox_get_logo();
					}
					
					wp_cache_set( 'boombox_amp_logo', $amp_logo );
					
				}
				
				return $amp_logo;
				
			}
			
			/**
			 * Add AMP section to term metaboxes structure
			 * @param array $structure Current structure
			 * @param string $box_id Meta box ID
			 * @param string $taxonomy Taxonomy slug
			 * @return array
			 * @since 2.5.0
			 * @version 2.5.0
			 */
			public function term_meta_boxes_add_fields( $structure, $box_id, $taxonomy ) {
				
				$supported_categories = array( 'category', 'post_tag', 'reaction' );
				if( in_array( $taxonomy, $supported_categories ) ) {
					
					// AMP
					$structure[ 'tab_amp' ] = array(
						'title'  => esc_html__( 'AMP', 'boombox' ),
						'active' => false,
						'icon'   => false,
						'order'  => 40,
						'fields' => array(
							// Disable Posts AMP Version
							'boombox_skip_posts_amp' => array(
								'type'        => 'checkbox',
								'standalone'  => true,
								'label'       => esc_html__( 'Disable Posts AMP Version', 'boombox' ),
								'description' => esc_html__( 'Check to disable AMP version for posts in this term', 'boombox' ),
								'default'     => 0,
								'priority'    => 20,
							)
							// other fields go here
						)
					);
					
				}
				
				return $structure;
			}
			
			/**
			 * Add fields to category edit screen
			 *
			 * @param $instance
			 * @param $term
			 */
			public function term_meta_boxes_edit_fields( $instance, $term ) {
				$skip_amp_version = boombox_get_term_meta( $term->term_id, 'boombox_skip_posts_amp' );
				?>
				<tr class="form-field skip-posts-amp-wrap">
					<th scope="row"><label for="boombox_skip_posts_amp"><?php _e( 'Disable Posts AMP Version', 'boombox' ); ?></label></th>
					<td>
						<input name="boombox_skip_posts_amp" type="hidden" value="0">
						<input name="boombox_skip_posts_amp" id="boombox_skip_posts_amp" type="checkbox" <?php checked( $skip_amp_version, 1 ); ?> value="1">
						<p class="description"><?php _e( 'Check to disable AMP version for posts in this term', 'boombox' ); ?></p>
					</td>
				</tr>
				<?php
			}

			/**
			 * Backward compatibility for AMP Version skip
			 * @param bool $enabled Current status
			 * @param $post WP_Post Current post object
			 *
			 * @return bool
			 * @since 2.1.2 - AMP 0.6.0
			 * @versoin 2.1.2
			 */
			public function post_amp_skip_backward_compatibility( $enabled, $post ) {
				$enabled = ! boombox_get_post_meta( $post->ID, 'boombox_post_skip_amp' );

				return $enabled;
			}

			/**
			 * Conditional check to skip single post AMP version
			 * @param $skip
			 * @param $post_id
			 * @param $post
			 *
			 * @return bool
			 */
			public function may_be_skip_post_amp_version( $skip, $post_id, $post ) {

				if( ! $skip ) {
					while ( true ) {

						// check by attached categories condition
						$categories = wp_get_post_categories( $post_id, array(
							'fields'     => 'ids',
							'meta_query' => array(
								array(
									'key'     => 'boombox_skip_posts_amp',
									'value'   => 1,
									'compare' => '='
								)
							),
						) );
						if ( ! empty( $categories ) ) {
							$skip = true;
							break;
						}

						// check by attached tags condition
						$tags = wp_get_post_tags( $post_id, array(
							'fields'     => 'ids',
							'meta_query' => array(
								array(
									'key'     => 'boombox_skip_posts_amp',
									'value'   => 1,
									'compare' => '='
								)
							),
						) );
						if ( ! empty( $tags ) ) {
							$skip = true;
							break;
						}

						break;

					}
				}
				
				return $skip;
			}

			public function boombox_amp_ajax_track_view( $analytics, $post ){
			    
				$analytics[] = array(
					'config_data' => array(
						'requests' => array(
                            'base' => strtr( admin_url( 'admin-ajax.php' ), array( 'http://' => '//', 'https://' => '//' ) ),
							'boomboxTrackPageview' => '${base}?action=${action}&post_id=${post_id}&is_amp=${is_amp}&token=${token}'
						),
						'triggers' => array(
							'trackPageview' => array(
								'on'        => 'visible',
								'request'   => 'boomboxTrackPageview',
								'vars' => array(
                                    'action'    => 'boombox_ajax_track_view',
									'post_id'   => $post->ID,
                                    'is_amp'    => 1,
                                    'token'     => wp_create_nonce( 'boombox_ajax_track_view_security_token' )
								),
							)
						),
						'transport' => array(
                            'beacon' => false,
							'xhrpost'   => true,
                            'image' => false,
						),
					),
					'type' => '',
					'attributes' => version_compare( AMP__VERSION, '0.5', '>=' ) ? array() : ''
				);
				
				return $analytics;
			}
		}
	}
	
	function boombox_amp() {
		return Boombox_Amp::get_instance();
	}
	
	boombox_amp();
	
}