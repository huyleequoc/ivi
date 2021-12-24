<?php
/**
 * BBPress plugin functions
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
if ( ! boombox_plugin_management_service()->is_plugin_active( 'bbpress/bbpress.php' ) ) {
	return;
}

if ( ! class_exists( 'Boombox_BBPress' ) ) {

	final class Boombox_BBPress {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return Boombox_BBPress|null
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_BBPress constructor.
		 */
		private function __construct() {
			$this->hooks();

			do_action( 'boombox/bbpress/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_BBPress from being cloned.
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup actions
		 */
		private function hooks() {
			add_filter( 'boombox/color_scheme_styles', array( $this, 'color_scheme_styles' ), 10, 1 );
			add_filter( 'bbp_body_class', array( $this, 'edit_body_classes' ), 10, 1 );
			add_filter( 'boombox/title_template_settings', array( $this, 'disable_title_template_filters' ), 10, 1 );
			add_filter( 'boombox/page_template_settings', array( $this, 'disable_template_featured_area' ), 10, 1 );
			add_filter( 'boombox/page_template_settings', array( $this, 'edit_page_template_settings' ), 10, 1 );
			add_filter( 'boombox/archive_template_settings', array( $this, 'disable_template_featured_area' ), 10, 1 );
			add_filter( 'boombox/index_template_settings', array( $this, 'disable_template_featured_area' ), 10, 1 );
			add_filter( 'boombox/allow_archive_query_modification', array( $this, 'prevent_archive_query_modification' ), 10, 1 );
			add_filter( 'boombox/title_template_layout', array( $this, 'edit_title_template_layout' ), 10, 1 );
			add_filter( 'get_the_post_type_description', array( $this, 'edit_post_type_description' ), 10, 2 );
			add_filter( 'bbp_default_styles', array( $this, 'edit_default_styles' ), 10, 1 );
		}

		/**
		 * Color scheme support
		 *
		 * @param $css
		 *
		 * @return string
		 * @see boombox_global_style_css for available colors
		 */
		public function color_scheme_styles( $css ) {
			$css .= '


            /* Heading Text Color */
            #bbpress-forums .bbp-forum-title,
            #bbpress-forums .bbp-topic-title,
             #bbp-search-results .bbp-header,
            #bbpress-forums li.bbp-header ul.forum-titles,
            #bbpress-forums ul.bbp-replies .bbp-header,
            #bbpress-forums ul.bbp-replies .bbp-footer,
             #bbpress-forums fieldset.bbp-form legend {
                color: %10$s;
            }

             /* Heading Font Family */
             #bbpress-forums .bbp-forum-title,
             #bbp-search-results .bbp-header,
             #bbpress-forums .bbp-topic-title,
            #bbpress-forums li.bbp-header ul.forum-titles,
            #bbpress-forums ul.bbp-replies .bbp-header,
            #bbpress-forums ul.bbp-replies .bbp-footer,
             #bbpress-forums fieldset.bbp-form legend {
                %16$s;
            }

            /* Global Border Color */
                li.bbp-body,
                div.bbp-forum-header, div.bbp-topic-header, div.bbp-reply-header,
                #bbpress-forums div.wp-editor-container,
                #bbp-search-results .search-item,
                #bbpress-forums div.bbp-the-content-wrapper div.quicktags-toolbar,
                #bbpress-forums li.bbp-header,
                .bbp_widget_login,
                #bbpress-forums div.bbp-reply-header,
                #bbpress-forums li.bbp-footer,.bbp-topic-pagination a,
                #bbpress-forums ul.bbp-lead-topic, 
                #bbpress-forums ul.bbp-topics, 
                #bbpress-forums ul.bbp-forums, 
                #bbpress-forums ul.bbp-replies, 
                #bbpress-forums ul.bbp-search-results,
                .bbp-topics-front ul.super-sticky,
                .bbp-topics ul.super-sticky,
                .bbp-forum-content ul.sticky,
                .bbp-pagination-links a,
                .bbp-pagination-links span.current,#bbpress-forums fieldset.bbp-form {
                    border-color: %13$s;
                }

            /* --secondary text color */
                .bbp-reply-post-date,
                .bbp-meta,
                span.bbp-admin-links,
                .bbp-body li.bbp-forum-topic-count,
                .bbp-body li.bbp-topic-voice-count,
                .bbp-body li.bbp-forum-reply-count,
                .bbp-body li.bbp-topic-reply-count,
                .bbp-topic-started-by,.bbp-topic-started-in,
                .bbp-pagination-count,.widget_display_stats dd,
                #bbpress-forums .bbp-forums-list li,
                div.bbp-breadcrumb .bbp-breadcrumb-current {
	                color: %9$s;
	            }

            /* --primary text */
                
                /* --primary bg color */
                .bbp-pagination-links a,
                 .bbp-topic-pagination a{
                  background-color: %6$s;
                }
                /* --primary text */
                .bbp-pagination-links a,
                 .bbp-topic-pagination a {
                  color: %7$s;
                }



            /* -secondary components bg color */
                span#subscription-toggle,
                #bbpress-forums .quicktags-toolbar,
                #bbpress-forums li.bbp-header,#bbpress-forums div.bbp-forum-header, #bbpress-forums div.bbp-topic-header, #bbpress-forums div.bbp-reply-header,#bbpress-forums div.even, #bbpress-forums ul.even,
                span#favorite-toggle,#bbpress-forums div.odd, #bbpress-forums ul.odd,#bbpress-forums li.bbp-header, #bbpress-forums li.bbp-footer {
                    background-color: %14$s;
                }

            /* -secondary components text color */
                span#subscription-toggle,
                #bbpress-forums li.bbp-header,
                span#favorite-toggle {
                    color:%23$s;
                }

            /* --border-radius for inputs, buttons */
                #bbpress-forums div.wp-editor-container {
                   -webkit-border-radius: %12$s;
                   -moz-border-radius: %12$s;
                   border-radius: %12$s;
                }

            /* --Global border-radius */
                .bbp-pagination-links a,
                .bbp-pagination-links span.current,
                .bbp_widget_login,
                 .bbp-topic-pagination a {
                   -webkit-border-radius: %11$s;
                   -moz-border-radius: %11$s;
                   border-radius: %11$s;
                }
                
                /* --Link color */
                #bbp-search-results .bbp-topic-title a {color:%21$s}
            ';


			return $css;
		}

		/**
		 * Get template sidebar type
		 * @return string
		 */
		private function get_sidebar_type() {
			if( bbp_is_forum_archive() || bbp_is_topic_archive() || bbp_is_search() ) {
				$sidebar_type = boombox_get_theme_option( 'archive_main_posts_sidebar_type' );
			} else {
				$sidebar_type = boombox_get_theme_option( 'single_post_general_sidebar_type' );
			}

			return apply_filters( 'boombox/bbpress/sidebar_type', $sidebar_type );
		}

		/**
		 * Get template sidebar orientation
		 * @return string
		 */
		private function get_sidebar_orientation() {
			if( bbp_is_forum_archive() || bbp_is_topic_archive() || bbp_is_search() ) {
				$sidebar_orientation = boombox_get_theme_option( 'archive_main_posts_sidebar_orientation' );
			} else {
				$sidebar_orientation = boombox_get_theme_option( 'single_post_general_sidebar_orientation' );;
			}

			return apply_filters( 'boombox/bbpress/sidebar_orientation', $sidebar_orientation );
		}

		/**
		 * Edit body classes
		 * @param array $classes Current classes
		 *
		 * @return array
		 */
		public function edit_body_classes( $classes ) {

			if( is_bbpress() ) {
				$classes[ 'sidebar_position' ] = boombox_get_body_classes_by_sidebar_type(
					$this->get_sidebar_type(),
					$this->get_sidebar_orientation()
				);
			}

			return $classes;
		}

		/**
		 * Disable title template fiters
		 * @param array $settings Title template current settings
		 *
		 * @return array
		 */
		public function disable_title_template_filters( $settings ) {

			if( is_bbpress() ) {

				// All bbPress template
				$settings[ 'filters' ] = false;

				if( is_single() ) {
					$settings['title'] = get_the_title();
				} else if( is_archive() ) {
					$forum_post_type_labels = bbp_get_forum_post_type_labels();
					$settings['title'] = $forum_post_type_labels['name'];
				}
			}

			return $settings;
		}

		/**
		 * Disable featured area
		 * @param array $settings The template current settings
		 *
		 * @return array
		 */
		public function disable_template_featured_area( $settings ) {

			if( is_bbpress() ) {
				$settings['featured_area'] = false;
			}

			return $settings;
		}

		/**
		 * Edit page template settings
		 * @param array  $settings The template current settings
		 *
		 * @return array
		 */
		public function edit_page_template_settings( $settings ) {

		    if( is_bbpress() ) {
                $sidebar_type = $this->get_sidebar_type();
                $settings['enable_primary_sidebar'] = boombox_is_primary_sidebar_enabled($sidebar_type);
                $settings['enable_secondary_sidebar'] = boombox_is_secondary_sidebar_enabled($sidebar_type);
            }

			return $settings;
		}

		/**
		 * Prevent archive query modification for bbPress pages
		 * @param bool $allow Current status
		 *
		 * @return bool
		 */
		public function prevent_archive_query_modification( $allow ) {
			if( is_bbpress() ) {
				$allow = false;
			}

			return $allow;
		}

		/**
		 * @param $layout
		 *
		 * @return string
		 */
		public function edit_title_template_layout( $layout ) {
			if( is_bbpress() ) {
				if( is_single() ) {
					$layout = boombox_get_theme_option( 'archive_header_style' );
				}
			}

			return $layout;
		}


		/**
		 * Edit post type description
		 * @param string $description Current description
		 * @param WP_Post_Type $post_type_obj Current post type
		 *
		 * @return string
		 */
		public function edit_post_type_description( $description, $post_type_obj ) {

			if( $post_type_obj->name == bbp_get_forum_post_type() ) {
				$description = '';
			}

			return $description;
		}

		/**
		 * Edit styles to use minify version when it's possible
		 * @param array $styles Current styles
		 *
		 * @return array
		 */
		public function edit_default_styles( $styles ) {

			$min = boombox_get_minified_asset_suffix();
			if( $min ) {
				$styles[ 'bbp-default' ][ 'file' ] = 'css/bbpress' . $min . '.css';

				if ( is_rtl() ) {
					$styles[ 'bbp-default-rtl' ][ 'file' ] = 'css/bbpress-rtl' . $min . '.css';
				}
			}


			return $styles;
		}

	}

	Boombox_BBPress::get_instance();
}