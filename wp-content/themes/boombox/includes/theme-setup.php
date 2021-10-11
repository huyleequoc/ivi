<?php
/**
 * Boombox theme setup
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Hooks
 */
add_filter( 'query_vars', 'boombox_add_additional_query_vars' );
add_action( 'boombox/single/microdata', 'boombox_single_post_microdata', 10, 1 );

add_filter( 'body_class', 'boombox_edit_body_classes', 10, 1 );
add_filter( 'get_the_archive_title', 'boombox_get_the_archive_title', 10 );
add_filter( 'comment_form_fields', 'boombox_move_comment_field_to_bottom', 10 );
add_filter( 'wp_list_categories', 'boombox_archive_count_no_brackets', 10 );
add_filter( 'mce_buttons', 'boombox_add_next_page_button', 1, 2 );
add_filter( 'excerpt_more', 'boombox_excerpt_more', 10 );
add_filter( 'script_loader_tag', 'boombox_add_script_attribute', 10, 2 );
add_filter( 'boombox_reaction_icons_path', 'boombox_add_theme_reaction_icons_path', 10, 1 );
add_filter( 'widget_title', 'boombox_change_widget_title', 10, 3 );
add_filter( 'post_views_count', 'boombox_post_views_count', 10, 2 );
add_filter( 'post_points_count', 'boombox_post_points_count', 10, 2 );
add_filter( 'boombox_loop_item_url', 'boombox_loop_item_url', 10, 2 );
add_filter( 'boombox_loop_item_url_target', 'boombox_loop_item_url_target', 10, 3 );
add_filter( 'boombox_loop_item_url_rel', 'boombox_loop_item_url_rel', 10, 3 );
add_filter( 'post_class', 'boombox_edit_post_class', 10, 3 );

/**
 * Add additional query variables
 *
 * @param array $vars Current variables
 *
 * @return array
 * @since   2.5.6
 * @version 2.5.6
 */
function boombox_add_additional_query_vars( $vars ) {
	$vars[] = 'bb-action';

	return $vars;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
if ( ! function_exists( 'boombox_setup' ) ) {

	function boombox_setup() {

		/*
		 * Make theme available for translation.
		 */
		load_theme_textdomain( 'boombox', get_stylesheet_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		 */
		add_theme_support( 'post-thumbnails' );

		$selected_sizes = boombox_get_theme_option( 'extras_image_sizes_active_sizes' );

		$active_sizes = array();
		foreach ( Boombox_Choices_Helper::get_instance()->get_image_sizes_choices() as $size_configuration ) {
			/* 1x */
			if ( in_array( $size_configuration['name'], $selected_sizes ) ) {
				$active_sizes[] = array(
					'name'   => $size_configuration['name'],
					'width'  => $size_configuration['width'],
					'height' => $size_configuration['height'],
					'crop'   => $size_configuration['crop']
				);
			}

			/* 2x */
			if ( in_array( $size_configuration['name'] . '-2x', $selected_sizes ) ) {
				$active_sizes[] = array(
					'name'   => $size_configuration['name'] . '-2x',
					'width'  => $size_configuration['width'] * 2,
					'height' => $size_configuration['height'] * 2,
					'crop'   => $size_configuration['crop']
				);
			}
		}

		$boombox_image_sizes = (array) apply_filters( 'boombox/add_image_sizes', $active_sizes );
		foreach ( $boombox_image_sizes as $args ) {
			$args = wp_parse_args( $args, array(
				'name'   => '',
				'width'  => 0,
				'height' => 0,
				'crop'   => false
			) );
			if ( ! $args['name'] ) {
				continue;
			}
			add_image_size( $args['name'], $args['width'], $args['height'], $args['crop'] );
		}

		// This theme uses wp_nav_menu() in five locations.
		register_nav_menus( array(
			'top_header_nav'       => esc_html__( 'Top Header Menu', 'boombox' ),
			'bottom_header_nav'    => esc_html__( 'Bottom Header Menu', 'boombox' ),
			'profile_nav'          => esc_html__( 'Profile Menu', 'boombox' ),
			'burger_mobile_menu_1' => esc_html__( 'Burger Menu 1', 'boombox' ),
			'burger_mobile_menu_2' => esc_html__( 'Burger Menu 2', 'boombox' ),
			'burger_mobile_menu_3' => esc_html__( 'Burger Menu 3', 'boombox' ),
			'burger_top_nav'       => esc_html__( 'More Button Top Menu', 'boombox' ),
			'burger_badges_nav'    => esc_html__( 'More Button Badges Menu', 'boombox' ),
			'burger_bottom_nav'    => esc_html__( 'More Button Bottom Menu', 'boombox' ),
			'badges_nav'           => esc_html__( 'Badges Menu', 'boombox' ),
			'featured_labels'      => esc_html__( 'Featured Labels Menu', 'boombox' ),
			'footer_nav'           => esc_html__( 'Footer Menu', 'boombox' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support( 'post-formats', array(
			'aside',
			'image',
			'video',
			'quote',
			'link',
			'gallery',
			'status',
			'audio',
			'chat',
		) );

		/**
		 * This theme styles the visual editor to resemble the theme style,
		 * specifically font, colors, icons, and column width.
		 */
		if ( $google_fonts_url = Boombox_Fonts_Helper::get_instance()->get_google_url() ) {
			add_editor_style( array( 'editor-style.css', $google_fonts_url ) );
		}

		/**
		 * Opting out of the block-based widgets editor
		 */
		remove_theme_support( 'widgets-block-editor' );
	}

}
add_action( 'after_setup_theme', 'boombox_setup', 10 );

/**
 * Sets the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function boombox_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'boombox_content_width', 1160 );
}

add_action( 'after_setup_theme', 'boombox_content_width', 0 );

/**
 * Get theme data
 *
 * @return false|WP_Theme
 */
function boombox_get_theme_data() {
	$theme = wp_get_theme();
	if ( is_child_theme() ) {
		$theme = $theme->parent();
	}

	return $theme;
}

if ( ! function_exists( 'boombox_get_assets_version' ) ) {
	/**
	 * Get assets version
	 *
	 * @return string
	 */
	function boombox_get_assets_version() {
		return apply_filters( 'boombox/assets_version', boombox_get_theme_data()->get( 'Version' ) );
	}
}

if ( ! function_exists( 'boombox_get_minified_asset_suffix' ) ) {

	/**
	 * Get the correct filename suffix for minified assets.
	 *
	 * @return string
	 *
	 * @since   2.1.2
	 * @version 2.1.2
	 */
	function boombox_get_minified_asset_suffix() {
		$ext = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		return $ext;
	}

}

/**
 * Enqueue styles.
 */
function boombox_styles() {
	$min = boombox_get_minified_asset_suffix();

	// Plugins
	wp_enqueue_style(
		'boombox-styles-min',
		BOOMBOX_THEME_URL . 'js/plugins/plugins' . $min . '.css',
		array(),
		boombox_get_assets_version()
	);

	// Icon fonts
	wp_enqueue_style(
		'boombox-icomoon-style',
		BOOMBOX_THEME_URL . 'fonts/icon-fonts/icomoon/icons.min.css',
		array(),
		boombox_get_assets_version()
	);
	boombox_enqueue_custom_icons_pack();

	// Add custom fonts, used in the main stylesheet.
	if ( $google_fonts_url = Boombox_Fonts_Helper::get_instance()->get_google_url() ) {
		wp_enqueue_style(
			'boombox-fonts',
			$google_fonts_url,
			array(),
			boombox_get_assets_version()
		);
	}

	// Theme main CSS
	wp_enqueue_style(
		'boombox-primary-style',
		BOOMBOX_THEME_URL . 'css/style' . $min . '.css',
		array( 'boombox-styles-min', 'boombox-icomoon-style' ),
		boombox_get_assets_version()
	);

	// RTL
	if ( is_rtl() ) {
		wp_enqueue_style(
			'boombox-style-rtl',
			BOOMBOX_THEME_URL . 'css/rtl' . $min . '.css',
			array( 'boombox-primary-style' ),
			boombox_get_assets_version()
		);
	}
}

add_action( 'wp_enqueue_scripts', 'boombox_styles', 10 );

/**
 * Check weather child theme has custom icons package
 *
 * @return null|array
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_get_custom_icons_package_data() {

	$data = null;

	if ( is_child_theme() ) {
		$folder = apply_filters( 'boombox/custom_icon_pack_path', 'icon-fonts' );
		if ( $folder ) {
			$folder    = trim( $folder, '/' );
			$json_path = get_stylesheet_directory() . '/' . $folder . '/selection.json';

			if ( file_exists( $json_path ) ) {
				$theme_url = trailingslashit( get_stylesheet_directory_uri() );
				$json_url  = $theme_url . $folder . '/selection.json';
				$style_url = $theme_url . $folder . '/style.css';

				$data = array(
					'json_path' => $json_path,
					'json_url'  => $json_url,
					'style_url' => $style_url
				);
			}
		}
	}

	return $data;
}

/**
 * Enqueue user icons custom set
 *
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_enqueue_custom_icons_pack() {
	$package = boombox_get_custom_icons_package_data();
	if ( ! $package ) {
		return;
	}

	wp_enqueue_style(
		'boombox-icomoon-user-set',
		$package['style_url'],
		array( 'boombox-icomoon-style' ),
		boombox_get_assets_version()
	);
}

/**
 * Enqueue scripts
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_scripts() {
	global $post;

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script(
			'comment-reply',
			'',
			array(),
			boombox_get_assets_version(),
			true
		);
	}

	$set = boombox_get_theme_options_set( array(
		'extra_authentication_enable_login_captcha',
		'extra_authentication_enable_registration_captcha',
		'extra_authentication_google_v3_recaptcha_site_key',
		'extras_video_control_mp4_video_player_controls',
		'extras_video_control_mp4_video_autoplay',
		'extras_video_control_mp4_video_sound',
		'extras_video_control_mp4_video_click_event_handler',
		'extras_video_control_enable_mp4_video_loop',
		'extras_gif_control_animation_event',
	) );

	$auth_captcha_type            = boombox_get_auth_captcha_type();
	$recaptcha_include_conditions = array(
		// login/registration condition
		'guests'       => (
			boombox_is_auth_allowed()
			&& ! is_user_logged_in()
			&& ( in_array( $auth_captcha_type, array( 'google', 'google_v3' ) ) )
			&& ( $set['extra_authentication_enable_login_captcha'] || $set['extra_authentication_enable_registration_captcha'] )
		),
		//pages with contact form shortcodes
		'contact_form' => ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'boombox_contact_form' ) ),
	);

	if ( $recaptcha_include_conditions['guests'] || $recaptcha_include_conditions['contact_form'] ) {

		$inline_scripts          = '';
		$google_recaptcha_handle = array(
			'name' => 'google-recaptcha',
			'url'  => 'https://www.google.com/recaptcha/api.js',
		);

		if ( 'google_v3' == $auth_captcha_type ) {
			$google_recaptcha_handle['url'] = add_query_arg( 'render', $set['extra_authentication_google_v3_recaptcha_site_key'], $google_recaptcha_handle['url'] );
		}

		/** - Contact form 7 - */
		if ( boombox_plugin_management_service()->is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
			wp_deregister_script( $google_recaptcha_handle['name'] );
			$inline_scripts = '
				if( "function" == typeof window.recaptchaCallback ) {
					recaptchaCallback();
				}
			';
		}

		wp_enqueue_script(
			$google_recaptcha_handle['name'],
			$google_recaptcha_handle['url'],
			array( 'jquery' ),
			boombox_get_assets_version()
		);

		/***** Buddypress registration */
		if ( boombox_plugin_management_service()->is_plugin_active( 'buddypress/bp-loader.php' ) && function_exists( 'bp_is_register_page' ) && bp_is_register_page() ) {
			if ( 'google' === $auth_captcha_type ) {
				$inline_scripts = sprintf( '
					jQuery( document ).on( "ready", function() {
						window.boomboxRecaptchaOnSubmit = function( token ){
							jQuery( "#signup_form" ).data( "_grec", 1 );
						}
					} );
					%s
				',
					$inline_scripts
				);
			} elseif ( 'google_v3' === $auth_captcha_type ) {
				$inline_scripts = sprintf( '
					grecaptcha.ready( function() {
                        grecaptcha.execute( document.getElementById( "bbp-register-captcha" ).getAttribute( "data-sitekey" ) )
                            .then( function( token ) {
                                document.getElementById( "g-recaptcha-response-buddypress" ).value = token;
                            } );
                    });
					%s',
					$inline_scripts
				);
			}

			wp_add_inline_script(
				$google_recaptcha_handle['name'],
				$inline_scripts,
				'after'
			);
		} /** - Standard pages - **/
		else {

			if ( 'google' === $auth_captcha_type ) {
				wp_deregister_script( $google_recaptcha_handle['name'] );
				$google_recaptcha_handle['url'] = add_query_arg( array(
					'render' => 'explicit',
					'onload' => 'boomboxOnloadCallback'
				), $google_recaptcha_handle['url'] );

				wp_enqueue_script(
					$google_recaptcha_handle['name'],
					add_query_arg(
						array( 'render' => 'explicit', 'onload' => 'boomboxOnloadCallback' ),
						$google_recaptcha_handle['url']
					),
					array( 'jquery' ),
					boombox_get_assets_version()
				);

				wp_add_inline_script(
					$google_recaptcha_handle['name'],
					sprintf( '
						var boomboxOnloadCallback = function() {
							jQuery( "body").trigger( "boombox/grecaptcha_loaded" );
							%s
						};
					',
						$inline_scripts
					),
					'after'
				);
			}
		}
	}

	$min = boombox_get_minified_asset_suffix();

	// Site main scripts
	wp_enqueue_script(
		'boombox-scripts-min',
		BOOMBOX_THEME_URL . 'js/scripts' . $min . '.js',
		array( 'jquery' ),
		boombox_get_assets_version(),
		true
	);
	wp_localize_script(
		'boombox-scripts-min',
		'boombox_global_vars',
		array(
			'boombox_gif_event'                                     => $set['extras_gif_control_animation_event'],
			'single_post_animated_hyena_gifs_excluded_js_selectors' => apply_filters( 'boombox/single_post/animated_hyena_gifs_excluded_js_selectors', array() ),
			'videoOptions'                                          => array(
				'playerControls' => $set['extras_video_control_mp4_video_player_controls'],
				'autoPlay'       => $set['extras_video_control_mp4_video_autoplay'],
				'sound'          => $set['extras_video_control_mp4_video_sound'],
				'clickEvent'     => $set['extras_video_control_mp4_video_click_event_handler'],
				'loop'           => $set['extras_video_control_enable_mp4_video_loop'] ? 1 : 0,
			),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'boombox_scripts', 10 );

/**
 * Get sidebars configuration
 *
 * @return array
 */
function boombox_get_sidebars() {
	return array(
		array(
			'name'        => esc_html__( 'Default', 'boombox' ),
			'id'          => 'default-sidebar',
			'description' => esc_html__( 'The widgets added here will appear on all the pages, except the post single and the page sidebar.', 'boombox' ),
		),
		array(
			'name'        => esc_html__( 'Page 1', 'boombox' ),
			'id'          => 'page-sidebar-1',
			'description' => esc_html__( 'Add widgets here to appear in your page sidebar.', 'boombox' ),
		),
		array(
			'name'        => esc_html__( 'Page 2', 'boombox' ),
			'id'          => 'page-sidebar-2',
			'description' => esc_html__( 'Add widgets here to appear in your page sidebar.', 'boombox' ),
		),
		array(
			'name'        => esc_html__( 'Page 3', 'boombox' ),
			'id'          => 'page-sidebar-3',
			'description' => esc_html__( 'Add widgets here to appear in your page sidebar.', 'boombox' ),
		),
		array(
			'name'        => esc_html__( 'Secondary', 'boombox' ),
			'id'          => 'page-secondary',
			'description' => esc_html__( 'Add widgets here to appear with three column listing type.', 'boombox' ),
		),
		array(
			'name'        => esc_html__( 'Post Single', 'boombox' ),
			'id'          => 'post-sidebar',
			'description' => esc_html__( 'Add widgets here to appear in your post sidebar.', 'boombox' ),
		),
		array(
			'name'        => esc_html__( 'Archive', 'boombox' ),
			'id'          => 'archive-sidebar',
			'description' => esc_html__( 'Add widgets here to appear in your post sidebar.', 'boombox' ),
		),
		array(
			'name'        => esc_html__( 'Footer Left', 'boombox' ),
			'id'          => 'footer-left-widgets',
			'description' => esc_html__( 'Add widgets here to appear in your footer left section.', 'boombox' ),
		),
		array(
			'name'        => esc_html__( 'Footer Middle', 'boombox' ),
			'id'          => 'footer-middle-widgets',
			'description' => esc_html__( 'Add widgets here to appear in your footer middle section.', 'boombox' ),
		),
		array(
			'name'        => esc_html__( 'Footer Right', 'boombox' ),
			'id'          => 'footer-right-widgets',
			'description' => esc_html__( 'Add widgets here to appear in your footer right section.', 'boombox' ),
		),
	);
}

/**
 * Registers a widget area.
 */
function boombox_widgets_init() {

	$register_sidebars = boombox_get_sidebars();

	foreach ( $register_sidebars as $register_sidebar ) {

		register_sidebar( array(
			'name'          => $register_sidebar['name'],
			'id'            => $register_sidebar['id'],
			'description'   => $register_sidebar['description'],
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}

}

add_action( 'widgets_init', 'boombox_widgets_init', 10 );

/**
 * User Authentication
 */
function boombox_authentication() {
	if ( ! is_user_logged_in() ) {
		require_once( BOOMBOX_INCLUDES_PATH . 'authentication/auth.php' );
	}
}

add_action( 'init', 'boombox_authentication', 10 );

/**
 * Custom Opengraph Meta Tags
 */
function boombox_meta_tags() {

	if ( ! is_single() ) {
		return;
	}

	if ( ! boombox_get_theme_option( 'extras_gif_control_enable_sharing' ) ) {
		return;
	}

	global $post;
	$thumbnail_id = get_post_thumbnail_id( $post );

	if ( ! $thumbnail_id ) {
		return;
	}

	$thumbnail_post = get_post( $thumbnail_id );
	if ( ! $thumbnail_post ) {
		return;
	}

	if ( "image/gif" != $thumbnail_post->post_mime_type ) {
		return;
	}
	list( $thumbnail_url, $thumbnail_width, $thumbnail_height, $thumbnail_is_intermediate ) = wp_get_attachment_image_src( $thumbnail_post->ID, 'full' );

	$opengraph = PHP_EOL . '<meta property="og:type" content="video.other" />';
	$opengraph .= PHP_EOL . sprintf( '<meta property="og:url" content="%s" />', $thumbnail_url );

	echo $opengraph;
}

add_action( 'wp_head', 'boombox_meta_tags', 0 );

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Current classes for body.
 *
 * @return array (Maybe) filtered body classes.
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_edit_body_classes( $classes ) {

	$set = boombox_get_theme_options_set( array(
		'home_main_posts_sidebar_type',
		'home_main_posts_sidebar_orientation',
		'archive_main_posts_sidebar_type',
		'archive_main_posts_sidebar_orientation',
		'archive_main_posts_template',
		'single_post_general_enable_full_post_button_conditions',
		'extras_badges_reactions_type',
		'extras_badges_position_on_thumbnails',
		'mobile_global_enable_sidebar',
		'design_background_style'
	) );

	/***** Color Mode */
	$classes['design_color_mode'] = 'boombox-theme-' . esc_attr( boombox_get_theme_option( 'design_color_mode' ) );

	/***** Sidebar types classes */
	// index template
	if ( is_home() ) {
		$classes['sidebar_position'] = boombox_get_body_classes_by_sidebar_type(
			$set['home_main_posts_sidebar_type'],
			$set['home_main_posts_sidebar_orientation']
		);
	} // archive template
	elseif ( is_archive() ) {
		$classes['sidebar_position'] = boombox_get_body_classes_by_sidebar_type(
			$set['archive_main_posts_sidebar_type'],
			$set['archive_main_posts_sidebar_orientation']
		);
	} // page template
	elseif ( is_page() ) {
		global $post;
		$sidebar_type = boombox_get_post_meta( $post->ID, 'boombox_sidebar_type' );
		if ( ! $sidebar_type ) {
			$sidebar_type = '1-sidebar-1_3';
		}
		$sidebar_orientation = boombox_get_post_meta( $post->ID, 'boombox_sidebar_orientation' );
		if ( ! $sidebar_orientation ) {
			$sidebar_orientation = 'right';
		}
		$classes['sidebar_position'] = boombox_get_body_classes_by_sidebar_type(
			$sidebar_type,
			$sidebar_orientation
		);
	} // single post
	elseif ( is_singular() ) {
		$layout                      = Boombox_Template::init( 'post' )->get_layout_options();
		$classes['sidebar_position'] = boombox_get_body_classes_by_sidebar_type(
			$layout['sidebar_type'],
			$layout['sidebar_orientation'],
			$layout['sidebar_reverse']
		);

		if ( boombox_is_post_trending( 'trending' ) ) {
			$classes[] = 'trending-post';
		}

		if ( boombox_is_nsfw_post() ) {
			$classes[] = 'nsfw-post';
		}
	} // search template
	elseif ( is_search() ) {
		$classes['sidebar_position'] = boombox_get_body_classes_by_sidebar_type(
			$set['archive_main_posts_sidebar_type'],
			$set['archive_main_posts_sidebar_orientation']
		);
	} // not found page
	elseif ( is_404() ) {
		$classes['sidebar_position'] = 'error404 no-sidebar';
	}

	// mobile
	if ( wp_is_mobile() && ! $set['mobile_global_enable_sidebar'] ) {
		$classes['sidebar_position'] = boombox_get_body_classes_by_sidebar_type(
			'no-sidebar',
			'right'
		);
	}

	/***** View Full Post Button Appearance */
	if (
		( is_page() || is_archive() || is_home() )
		&& in_array( 'image_ratio', $set['single_post_general_enable_full_post_button_conditions'] )
	) {
		$classes[] = 'has-full-post-button';
	}

	/***** Badges Reactions Type */
	if ( $set['extras_badges_reactions_type'] ) {
		$classes[] = 'badge-' . esc_attr( $set['extras_badges_reactions_type'] );
	}

	/***** Badges Position On Thumbnails */
	if ( $set['extras_badges_position_on_thumbnails'] ) {
		$classes[] = 'badges-' . esc_attr( $set['extras_badges_position_on_thumbnails'] );
	}

	/***** Background Style */
	if ( in_array( $set['design_background_style'], array( 'stretched', 'boxed' ) ) ) {
		$classes[] = 'bb-' . $set['design_background_style'] . '-view';
	} elseif ( 'cards' == $set['design_background_style'] ) {
		$classes[] = 'bb-cards-view flat-cards';
	} elseif ( 'material_cards' == $set['design_background_style'] ) {
		$classes[] = 'bb-cards-view material-cards';
	}

	/***** Background Image */
	if ( boombox_is_theme_option_changed( 'design_body_background_image' ) ) {
		$classes[] = 'with-background-media';
	}

	/***** Sign Up Classes */
	if ( did_action( 'before_signup_header' ) || did_action( 'activate_header' ) ) {
		$classes[] = 'page-activate-signup';
	}

	return $classes;
}

/**
 * Get media show option for specific post
 *
 * @param int $post_id Post ID
 *
 * @return bool
 *
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_show_media_for_post( $post_id ) {
	$configuration = boombox_get_post_meta( $post_id, 'boombox_hide_featured_image' );
	switch ( $configuration ) {
		case 'show':
			$show = true;
			break;
		case 'hide':
			$show = false;
			break;
		default:
			$show = (bool) boombox_get_theme_option( 'single_post_general_featured_media' );
	}

	return apply_filters( 'boombox/single/show_media', $show );
}

/**
 * Add Next Page/Page Break Button
 * in WordPress Visual Editor
 *
 * @param $buttons
 * @param $id
 *
 * @return mixed
 */
function boombox_add_next_page_button( $buttons, $id ) {

	/* only add this for content editor */
	if ( 'content' != $id ) {
		return $buttons;
	}

	/* add next page after more tag button */
	array_splice( $buttons, 13, 0, 'wp_page' );

	return $buttons;
}

/**
 * Filter the excerpt "read more" string.
 *
 * @param string $more "Read more" excerpt string.
 *
 * @return string (Maybe) modified "read more" excerpt string.
 */
function boombox_excerpt_more( $more ) {
	return '...';
}

/**
 * Filter some scripts to add additional options
 *
 * @param $tag    Current Tag
 * @param $handle Handle
 *
 * @return mixed Modified Tag
 */
function boombox_add_script_attribute( $tag, $handle ) {
	if ( in_array( $handle, array(
		'boombox-google-recaptcha',
		'facebook-jssdk',
		'boombox-google-platform',
		'boombox-google-client'
	) ) ) {
		return str_replace( ' src', ' id="' . $handle . '" async defer src', $tag );
	}

	return $tag;
}

/**
 * Detect if Registration is active
 */
function boombox_user_can_register() {
	return (bool) get_option( 'users_can_register' );
}

/**
 * Remove 'category' from archive title
 *
 * @param $title
 *
 * @return string
 */
function boombox_get_the_archive_title( $title ) {

	if ( is_category() || is_tag() || is_tax( 'reaction' ) ) {
		$title = single_term_title( '', false );
	}

	return $title;

}

/**
 * Moving the Comment Text Field to Bottom
 *
 * @param $fields
 *
 * @return mixed
 */
function boombox_move_comment_field_to_bottom( $fields ) {
	$comment_field = $fields['comment'];
	unset( $fields['comment'] );
	$fields['comment'] = $comment_field;

	return $fields;
}

/**
 * Remove Post Count Parentheses From Widget
 *
 * @param $variable
 *
 * @return mixed
 */
function boombox_archive_count_no_brackets( $variable ) {
	return strtr( $variable, array( '(' => '<span class="post_count"> ', ')' => ' </span>' ) );
}

/**
 * Check if a post status is registered.
 *
 * @see get_post_status_object()
 *
 * @param string $postStatus Post status name.
 *
 * @return bool Whether post status is registered.
 */
function post_status_exists( $postStatus ) {
	return (bool) get_post_status_object( $postStatus );
}

/**
 * Return Featured Strip default image URL
 *
 * @return string
 */
function boombox_get_default_image_url_for_featured_strip() {
	return apply_filters( 'boombox_default_image_for_featured_strip', BOOMBOX_THEME_URL . 'images/nophoto.png' );
}

/**
 * Returns site authentication status
 *
 * @return bool
 * @since   1.0.0
 * @version 2.0.0
 */
function boombox_is_auth_allowed() {
	return (bool) boombox_get_theme_option( 'extra_authentication_enable_site_auth' );
}

/**
 * Get authentication captcha type
 *
 * @return string
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_get_auth_captcha_type() {

	$set = boombox_get_theme_options_set( array(
		'extra_authentication_captcha_type',
		'extra_authentication_google_recaptcha_site_key',
		'extra_authentication_google_recaptcha_secret_key',
		'extra_authentication_google_v3_recaptcha_site_key',
		'extra_authentication_google_v3_recaptcha_secret_key'
	) );

	switch ( $set['extra_authentication_captcha_type'] ) {
		case 'image':
			$captcha_type = 'image';
			break;
		case 'google':
			$captcha_type = false;
			if ( $set['extra_authentication_google_recaptcha_site_key'] && $set['extra_authentication_google_recaptcha_secret_key'] ) {
				$captcha_type = 'google';
			}
			break;
		case 'google_v3':
			$captcha_type = false;
			if ( $set['extra_authentication_google_v3_recaptcha_site_key'] && $set['extra_authentication_google_v3_recaptcha_secret_key'] ) {
				$captcha_type = 'google_v3';
			}
			break;
		default:
			$captcha_type = false;
	}

	return $captcha_type;
}

/**
 * Validate image captcha
 *
 * @param string $key  The key in $_POST array where response is set
 * @param string $type login | register
 *
 * @return bool
 */
function boombox_validate_image_captcha( $key, $type ) {
	$session_key   = sprintf( 'boombox_%s_captcha_key', $type );
	$session_value = isset( $_SESSION[ $session_key ] ) ? strtolower( $_SESSION[ $session_key ] ) : '';
	$post_value    = isset( $_POST[ $key ] ) ? strtolower( $_POST[ $key ] ) : '';

	return ( $session_value && $post_value && ( $session_value == $post_value ) );
}

/**
 * Validate google captcha response
 *
 * @param string $key The key in $_POST array where response is set
 *
 * @return array
 */
function boombox_validate_google_captcha( $key ) {
	add_filter( 'http_request_timeout', 'boombox_recaptcha_http_request_timeout', 9999, 1 );

	$gcaptcha = array(
		'success'  => false,
		'message'  => '',
		'response' => wp_remote_retrieve_body( wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
			'body' => array(
				'secret'   => boombox_get_theme_option( 'extra_authentication_' . boombox_get_auth_captcha_type() . '_recaptcha_secret_key' ),
				'response' => isset( $_POST[ $key ] ) ? $_POST[ $key ] : '',
			),
		) ) ),
	);

	if ( ! is_wp_error( $gcaptcha['response'] ) ) {
		$gcaptcha['response'] = json_decode( $gcaptcha['response'], true );
		if ( isset( $gcaptcha['response']['success'] ) && $gcaptcha['response']['success'] ) {
			$gcaptcha['success'] = true;
		}
	}

	remove_filter( 'http_request_timeout', 'boombox_recaptcha_http_request_timeout', 9999 );

	return $gcaptcha;
}

/**
 * Sort icons array by key 'name'
 *
 * @param $a
 * @param $b
 *
 * @return int
 */
function icomoon_icon_sort_by_name( $a, $b ) {
	if ( $a['name'] < $b['name'] ) {
		return - 1;
	}
	if ( $a['name'] > $b['name'] ) {
		return 1;
	}

	return 0;
}

/**
 * Get "iconmoon" icons pack
 *
 * @return array
 */
function boombox_get_icomoon_icons_array() {

	$need_update            = false;
	$transient              = 'boombox-icons';
	$custom_icons_transient = 'boombox-icons-user-set';

	$icons_array = get_site_transient( $transient );
	if ( false === $icons_array ) {
		$icons_array = array();
		$url         = BOOMBOX_THEME_URL . 'fonts/icon-fonts/icomoon/selection.json';
		$response    = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			error_log( $response->get_error_message() );
		} else {
			$icons   = json_decode( $response['body'] );
			$exclude = array( 'skull-real' );
			if ( isset( $icons->icons ) && is_array( $icons->icons ) ) {
				foreach ( $icons->icons as $icon ) {
					$icon_name  = $icon->properties->name;
					$icon_names = explode( ', ', $icon_name );
					if ( ! in_array( $icon_names[0], $exclude ) ) {
						$icons_array[] = array(
							'icon'    => $icon_names[0],
							'name'    => $icon_name,
							'prefix'  => 'bb-icon-',
							'postfix' => ''
						);
					}
				}
				$need_update = true;
				usort( $icons_array, 'icomoon_icon_sort_by_name' );
			}
		}
	}

	if ( is_child_theme() ) {
		$custom_icons = get_site_transient( $custom_icons_transient );
		if ( false === $custom_icons ) {
			$custom_icons = array();
			$package      = boombox_get_custom_icons_package_data();

			if ( $package ) {
				$response = wp_remote_get( $package['json_url'] );

				if ( is_wp_error( $response ) ) {
					error_log( $response->get_error_message() );
				} else {
					$icons = json_decode( $response['body'] );
					if ( isset( $icons->icons ) && is_array( $icons->icons ) ) {
						foreach ( $icons->icons as $icon ) {
							$icon_name  = $icon->properties->name;
							$icon_names = explode( ', ', $icon_name );

							$custom_icons[] = array(
								'icon'    => $icon_names[0],
								'name'    => $icon_name,
								'prefix'  => 'bb-icon-',
								'postfix' => '-custom',
							);
						}
						$need_update = true;
						usort( $custom_icons, 'icomoon_icon_sort_by_name' );
						set_site_transient( $custom_icons_transient, $custom_icons, 30 * DAY_IN_SECONDS );
					}
				}
			}
			$icons_array = array_merge( $icons_array, $custom_icons );
		}
	}

	if ( $need_update ) {
		set_site_transient( $transient, $icons_array, 30 * DAY_IN_SECONDS );
	}

	return $icons_array;
}

/**
 * Purge icons caches
 *
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox__on_hook__wp__purge_icons_caches() {
	if ( isset( $_GET['boombox_purge_icons_cache'] ) && $_GET['boombox_purge_icons_cache'] ) {
		delete_site_transient( 'boombox-icons' );
		delete_site_transient( 'boombox-icons-user-set' );
	}
}

add_action( 'init', 'boombox__on_hook__wp__purge_icons_caches' );

/**
 * Get regular expression for provided type
 *
 * @param $type
 *
 * @return string
 */
function boombox_get_regex( $type ) {

	switch ( $type ) {
		case 'youtube':
			$regex = "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/";
			break;
		case 'vimeo':
			$regex = "/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/";
			break;
		case 'dailymotion':
			$regex = "/^.+dailymotion.com\/(?:video|swf\/video|embed\/video|hub|swf)\/([^&?]+)/";
			break;
		case 'vine':
			$regex = "/^http(?:s?):\/\/(?:www\.)?vine\.co\/v\/([a-zA-Z0-9]*)?/";
			break;
		case 'ok':
			$regex = "/^http(?:s?):\/\/(?:www\.)?(ok|odnoklassniki)\.ru\/video\/([a-zA-Z0-9]*)?/";
			break;
		case 'facebook':
			$regex = "/^(https?:\/\/www\.facebook\.com\/(?:video\.php\?v=\d+|.*?\/videos\/\d+)\/?)$/";
			break;
		case 'vidme':
			$regex = "/^http(?:s?):\/\/(?:www\.)?vid\.me\/([a-zA-Z0-9]*)?/";
			break;
		case 'vk':
			$regex = "/^(http(?:s?):)?\/\/(?:www\.)?vk\.com\/video_ext\.php\?(.*)/";
			break;
		case 'twitch':
			$regex = "/(^https:\/\/(?:www\.)?twitch\.tv)\/([^\/]+)?(\/)?([^\/]+)?(\/)?([a-zA-Z0-9-_]+)?/";
			break;
		case 'coub':
			$regex = "/^(http(?:s?):)?\/\/(?:www\.)?coub\.com\/(view|embed)\/([a-zA-Z0-9]*)?/";
			break;
		case 'twitter':
			$regex = "/http(?:s)?:\/\/(?:www\.)?twitter\.com\/([a-zA-Z0-9_]+)\/status(?:es)?\/([a-zA-Z0-9_]+)/";
			break;
		case 'instagram':
			$regex = "/(https?:\/\/)?([\w\.]*)instagram\.com\/p\/([a-zA-Z0-9_-]*)\/{0,1}/";
			break;
		default:
			$regex = '';
	}

	return $regex;
}

/**
 * Callback to modify widgets titles
 *
 * @param $title
 * @param $instance
 * @param $id_base
 *
 * @return string
 */
function boombox_change_widget_title( $title = '', $instance = array(), $id_base = '' ) {
	if ( 'tag_cloud' == $id_base ) {
		$title = ( isset( $instance['title'] ) && $instance['title'] ) ? $instance['title'] : '';
	}

	return $title;
}

/**
 * Callback to add fake views count to actual ones
 *
 * @param $views_count
 * @param $post_id
 *
 * @return mixed
 */
function boombox_post_views_count( $views_count, $post_id ) {
	$fake_count = boombox_get_theme_option( 'extras_post_ranking_system_fake_views_count' );
	if ( $fake_count > 0 ) {
		$cached_posts = boombox_cache_get( 'cached_posts' );
		if ( ! $cached_posts ) {
			$cached_posts = array();
		}

		if ( ! isset( $cached_posts[ $post_id ]['post'] ) ) {
			$cached_posts[ $post_id ] = isset( $cached_posts[ $post_id ] ) ? $cached_posts[ $post_id ] : array();
			$cached_posts[ $post_id ] = array_merge( $cached_posts[ $post_id ], array( 'post' => get_post( $post_id ) ) );

			boombox_cache_set( 'cached_posts', $cached_posts );
		}
		$fake_count += strlen( $cached_posts[ $post_id ]['post']->post_title );
	} else {
		$fake_count = 0;
	}

	return $views_count + $fake_count;
}

/**
 * Callback to add fake points count to actual ones
 *
 * @param $points_count
 * @param $post_id
 *
 * @return mixed
 */
function boombox_post_points_count( $points_count, $post_id ) {
	$fake_count = boombox_get_theme_option( 'extras_post_ranking_system_fake_points_count' );
	if ( $fake_count > 0 ) {
		$cached_posts = boombox_cache_get( 'cached_posts' );
		if ( ! $cached_posts ) {
			$cached_posts = array();
		}

		if ( ! isset( $cached_posts[ $post_id ]['post'] ) ) {
			$cached_posts[ $post_id ] = isset( $cached_posts[ $post_id ] ) ? $cached_posts[ $post_id ] : array();
			$cached_posts[ $post_id ] = array_merge( $cached_posts[ $post_id ], array( 'post' => get_post( $post_id ) ) );

			boombox_cache_set( 'cached_posts', $cached_posts );
		}
		$fake_count += strlen( $cached_posts[ $post_id ]['post']->post_title );
	} else {
		$fake_count = 0;
	}

	return $points_count + $fake_count;
}

/**
 * Callback to add fake shares count to actual ones
 *
 * @param $share_count
 * @param $post_id
 *
 * @return mixed
 */
function boombox_post_shares_count( $share_count, $post_id ) {
	$fake_count = (int) apply_filters( 'boombox/fake_share_count', 0 );

	if ( $fake_count > 0 ) {
		$cached_posts = boombox_cache_get( 'cached_posts' );
		if ( ! $cached_posts ) {
			$cached_posts = array();
		}

		if ( ! isset( $cached_posts[ $post_id ]['post'] ) ) {
			$cached_posts[ $post_id ] = isset( $cached_posts[ $post_id ] ) ? $cached_posts[ $post_id ] : array();
			$cached_posts[ $post_id ] = array_merge( $cached_posts[ $post_id ], array( 'post' => get_post( $post_id ) ) );

			boombox_cache_set( 'cached_posts', $cached_posts );
		}

		$index = 0;
		while ( $fake_count > 10 ) {
			++ $index;
			$fake_count = $fake_count / 10;
		}

		$base       = pow( 10, $index );
		$fake_count = ceil( $fake_count * $base + pow( 10, $index - 1 ) * strlen( $cached_posts[ $post_id ]['post']->post_title ) );
	} else {
		$fake_count = 0;
	}

	return $share_count + $fake_count;
}

/**
 * Callback to cache current post
 *
 * @param $current_post
 */
function boombox_cache_post( $current_post ) {
	$cached_posts = boombox_cache_get( 'cached_posts' );
	if ( ! $cached_posts ) {
		$cached_posts = array();
	}
	if ( ! isset( $cached_posts[ $current_post->ID ]['post'] ) ) {
		$cached_posts[ $current_post->ID ] = isset( $cached_posts[ $current_post->ID ] ) ? $cached_posts[ $current_post->ID ] : array();
		$cached_posts[ $current_post->ID ] = array_merge( $cached_posts[ $current_post->ID ], array( 'post' => $current_post ) );
	}

	boombox_cache_set( 'cached_posts', $cached_posts );
}

add_action( 'the_post', 'boombox_cache_post', 10, 1 );

/**
 * Get post meta
 *
 * @param int|string $post_id Post ID
 * @param string     $key     Meta key
 *
 * @return mixed
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_get_post_meta( $post_id, $key = '' ) {
	return aiom_get_post_meta( $post_id, $key );
}

/**
 * Get term meta
 *
 * @param string|int $term_id Term ID
 * @param string     $key     Meta key
 *
 * @return mixed
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_get_term_meta( $term_id, $key = '' ) {
	return aiom_get_term_meta( $term_id, $key );
}

/**
 * Get user meta
 *
 * @param string|int $user_id User ID
 * @param string     $key     Meta key
 *
 * @return mixed
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_get_user_meta( $user_id, $key = '' ) {
	return aiom_get_user_meta( $user_id, $key );
}

/**
 * Render microdata for single post
 *
 * @param $post_data
 */
function boombox_single_post_microdata( $post_data ) {

	if ( isset( $post_data['post_thumbnail_html'] ) ) {
		preg_match( '/src="([^"]+)/i', $post_data['post_thumbnail_html'], $thumbnail_url_matches );
		preg_match( '/width="([^"]+)/i', $post_data['post_thumbnail_html'], $thumbnail_width_matches );
		preg_match( '/height="([^"]+)/i', $post_data['post_thumbnail_html'], $thumbnail_height_matches );

		$src    = isset( $thumbnail_url_matches[1] ) ? $thumbnail_url_matches[1] : false;
		$width  = isset( $thumbnail_width_matches[1] ) ? absint( $thumbnail_width_matches[1] ) : false;
		$height = isset( $thumbnail_height_matches[1] ) ? absint( $thumbnail_height_matches[1] ) : false;

		if ( $src && $width && $height ) {

			printf(
				'<span class="mf-hide" itemprop=image itemscope itemtype="https://schema.org/ImageObject">
					<meta itemprop="url" content="%1$s">
					<meta itemprop="width" content="%2$d">
					<meta itemprop="height" content="%3$d">
				</span>',
				$src,
				$width,
				$height
			);
		}
	}

	$logo_microdata = '';
	$logo_url       = apply_filters( 'boombox/single/microdata/logo_url', '' );
	if ( ! $logo_url ) {
		$boombox_logo = boombox_get_logo();
		if ( ! $boombox_logo ) {
			$logo_url = $boombox_logo['src'];
		}
	}
	if ( $logo_url ) {
		$logo_microdata = sprintf(
			'<span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
				<meta itemprop="url" content="%1$s">
			</span>',
			esc_url( $logo_url )
		);
	}

	printf(
		'<span class="mf-hide" itemprop="publisher" itemscope="" itemtype="https://schema.org/Organization">
			%1$s
			<meta itemprop="name" content="%2$s">
			<meta itemprop="url" content="%3$s">
		</span>
		<meta itemscope content="" itemprop=mainEntityOfPage itemType="https://schema.org/WebPage" itemid="%4$s"/>',
		$logo_microdata,
		get_bloginfo( 'name' ),
		esc_url( home_url( '/' ) ),
		get_permalink()
	);
}

/**
 * Single post article structured data
 */
function boombox_single_article_structured_data() {
	$data = apply_filters( 'boombox/single/article-structured-data', array(
		"data-post-id" => get_the_ID(),
		"itemscope"    => "",
		"itemtype"     => "http://schema.org/Article",
	) );

	$data_array = array();
	foreach ( (array) $data as $property => $value ) {
		$data_array[] = sprintf( '%1$s="%2$s"', $property, $value );
	}

	echo implode( ' ', $data_array );
}

/**
 * Modify redirect url to home page
 *
 * @param $logout_url
 * @param $redirect
 *
 * @return string
 */
function boombox_logout_url( $logout_url, $redirect ) {
	$prefered_redirect_url = esc_url( home_url( '/' ) );
	if ( $redirect != $prefered_redirect_url ) {
		$logout_url = wp_logout_url( $prefered_redirect_url );
	}

	return $logout_url;
}

add_action( 'logout_url', 'boombox_logout_url', 10, 2 );

/**
 * Render footer popups
 */
function boombox_render_popups() {
	/* Popups for logged out users */
	if ( ! is_user_logged_in() ) {
		echo '<div class="light-modal-bg"></div>';

		get_template_part( 'template-parts/popups/login', 'form' );
		get_template_part( 'template-parts/popups/forgot-password', 'form' );

		$include_password_strength_meter = false;
		if ( boombox_user_can_register() ) {
			$include_password_strength_meter = true;
			get_template_part( 'template-parts/popups/registration', 'form' );
		}

		if ( in_array( get_query_var( 'bb-action' ), array( 'rp', 'reset-password' ) ) ) {
			$include_password_strength_meter = true;
			get_template_part( 'template-parts/popups/reset-password', 'form' );
		}

		if ( $include_password_strength_meter ) {
			wp_enqueue_script( 'password-strength-meter' );
		}
	}
}

add_action( 'boombox/after-footer', 'boombox_render_popups' );

/**
 * Change loop item URL
 *
 * @param $url
 * @param $post_id
 *
 * @return string
 */
function boombox_loop_item_url( $url, $post_id ) {
	$affiliate_url    = boombox_get_post_meta( $post_id, 'boombox_post_affiliate_link' );
	$use_as_post_link = boombox_get_post_meta( $post_id, 'boombox_post_affiliate_link_use_as_post_link' );

	if ( $affiliate_url && $use_as_post_link ) {
		$url = $affiliate_url;
	}

	return esc_url( $url );
}

/**
 * Change loop item URL target
 *
 * @param $target
 * @param $permalink
 * @param $url
 *
 * @return string
 */
function boombox_loop_item_url_target( $target, $permalink, $url ) {
	if ( $permalink != $url ) {
		$target = 'target="_blank"';
	}

	return $target;
}

/**
 * Change loop item URL rel attribute
 *
 * @param $target
 * @param $permalink
 * @param $url
 *
 * @return string
 */
function boombox_loop_item_url_rel( $rel, $permalink, $url ) {
	if ( $permalink != $url ) {
		$target = 'rel="nofollow noopener"';
	}

	return $rel;
}

/**
 * Set cache
 *
 * @param     $key
 * @param     $data
 * @param int $expire
 *
 * @return bool
 */
function boombox_cache_set( $key, $data, $expire = 0 ) {
	return wp_cache_set( $key, $data, 'boombox', $expire = 0 );
}

/**
 * Get value from cache
 *
 * @param      $key
 * @param bool $force
 * @param null $found
 *
 * @return bool|mixed
 */
function boombox_cache_get( $key, $force = false, &$found = null ) {
	if ( defined( 'BOOMBOX_DISABLE_CACHE' ) && BOOMBOX_DISABLE_CACHE ) {
		$contents = false;
	} else {
		$contents = wp_cache_get( $key, 'boombox', $force, $found );
	}

	return $contents;
}

/**
 * Check whether post is an "NSFW" one
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global `$post`.
 *
 * @return bool
 */
function boombox_is_nsfw_post( $post = null ) {
	if ( is_user_logged_in() ) {
		return false;
	}

	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}

	$checked_posts = boombox_cache_get( 'nsfw_checked_posts' );
	$checked_posts = $checked_posts ? $checked_posts : array();

	if ( ! isset( $checked_posts[ $post->ID ] ) ) {
		$nsfw_terms = boombox_get_nsfw_terms();

		$is_nsfw = false;
		foreach ( $nsfw_terms as $term ) {
			if ( has_term( $term->term_id, $term->taxonomy, $post ) ) {
				$is_nsfw = true;
				break;
			}
		}

		$checked_posts[ $post->ID ] = $is_nsfw;

		boombox_cache_set( 'nsfw_checked_posts', $checked_posts );
	}

	return $checked_posts[ $post->ID ];
}

/**
 * Add numerical words to numbers
 *
 * @param $number
 *
 * @return string
 */
function boombox_numerical_word( $number ) {

	if ( $number < 1000 ) {
		return $number;
	} elseif ( $number <= 1000000 ) {
		$scale_to = 1000;
		$suffix   = esc_html__( 'k', 'boombox' );
	} else {
		$scale_to = 1000000;
		$suffix   = esc_html__( 'M', 'boombox' );
	}

	$precision = 1;
	$multiple  = pow( 10, $precision );
	$number    = round( ( $number / $scale_to ) * $multiple ) / $multiple;

	return sprintf( '%1$s%2$s', $number, $suffix );
}

/**
 * Get custom reactions folder name
 *
 * @return string
 */
function boombox_theme_reactions_folder_name() {
	return apply_filters( 'boombox/reactions/custom_folder', 'reactions' );
}

/**
 * Add custom folder data to scan for icons
 *
 * @param $dirs
 *
 * @return array
 */
function boombox_add_theme_reaction_icons_path( $dirs ) {
	$theme_folder_name = boombox_theme_reactions_folder_name();
	array_unshift( $dirs, array(
		'path' => trailingslashit( get_stylesheet_directory() ) . $theme_folder_name . '/',
		'url'  => get_stylesheet_directory_uri() . '/' . $theme_folder_name . '/',
	) );

	return $dirs;
}

/**
 * Set optimal duration of HTTP request timeout for google recaptcha validating
 *
 * @param int $val Current timeout
 *
 * @return int
 */
function boombox_recaptcha_http_request_timeout( $val ) {
	return apply_filters( 'boombox/http_requuest_timeout', 5 );
}

/**
 * Edit post classes
 *
 * @param array $classes Current classes
 * @param array $class   Additional classes
 * @param int   $post_id Post ID
 *
 * @return array
 */
function boombox_edit_post_class( $classes, $class, $post_id ) {
	if ( boombox_is_nsfw_post( $post_id ) ) {
		$classes[] = 'bb-nsfw-post';
	}

	return $classes;
}

/**
 * Setup cookie consent script
 *
 * @since   2.5.5
 * @version 2.5.5
 */
function boombox_setup_setup_cookie_consent_script() {
	$script = boombox_get_theme_option( 'extras_gdpr_cookie_consent_script' );
	preg_replace( '<!--(.*?)-->', '', $script );
	if ( ! empty( $script ) ) {
		echo $script;
	}
}

add_action( 'wp_head', 'boombox_setup_setup_cookie_consent_script' );