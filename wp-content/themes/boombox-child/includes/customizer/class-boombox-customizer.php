<?php
/**
 * Boombox customizer class
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.7.7
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Customizer' ) ) {

	final class Boombox_Customizer {

		const CONFIG_ID = 'boombox';
		const OPTION_NAME = 'boombox_theme';

		/**
		 * Hold default values
		 * @var array
		 */
		private $default_values = array();

		/**
		 * Setup default values
		 */
		private function set_default_values() {

			/**** "Site Identity" section */
			$site_identity = array(
				'branding_show_tagline' => true,
				'branding_logo'         => '',
				'branding_logo_width'   => '',
				'branding_logo_height'  => '',
				'branding_logo_hdpi'    => '',
				'branding_logo_small'   => '',
				'footer_general_text'   => esc_html__( 'All Rights Reserved', 'boombox' ),
				'branding_404_image'    => BOOMBOX_THEME_URL . 'images/404.png',
			);

			/***** "Design" section */
			$design = array(
				'design_logo_font_family'                      => array(
					'font-family' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
					'font-style'  => 'normal',
					'font-weight' => 400,
					'subsets'     => array( 'latin', 'latin-ext' ),
				),
				'design_primary_font_family'                   => array(
					'font-family' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
					'font-style'  => 'normal',
					'font-weight' => 400,
					'subsets'     => array( 'latin', 'latin-ext' ),
				),
				'design_secondary_font_family'                 => array(
					'font-family' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
					'font-style'  => 'normal',
					'font-weight' => 400,
					'subsets'     => array( 'latin', 'latin-ext' ),
				),
				'design_post_titles_font_family'               => array(
					'font-family' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
					'font-style'  => 'normal',
					'font-weight' => 400,
					'subsets'     => array( 'latin', 'latin-ext' ),
				),
				'design_general_text_font_size'                => '16',
				'design_single_post_heading_font_size'         => '45',
				'design_widget_heading_font_size'              => '17',
				'design_color_mode'                            => 'light',
				'design_background_style'                      => 'stretched',
				'design_body_background_color'                 => '#f5f5f5',
				'design_body_background_image'                 => '',
				'design_body_background_image_type'            => 'cover',
				'design_body_background_link'                  => '',
				'design_content_background_color'              => '#ffffff',
				'design_primary_color'                         => '#ffe400',
				'design_primary_text_color'                    => '#000000',
				'design_base_text_color'                       => '#1f1f1f',
				'design_secondary_text_color'                  => '#a3a3a3',
				'design_heading_text_color'                    => '#1f1f1f',
				'design_link_text_color'                       => '#f43547',
				'design_secondary_components_background_color' => '#f7f7f7',
				'design_secondary_components_text_color'       => '#1f1f1f',
				'design_border_color'                          => '#ececec',
				'design_border_radius'                         => 6,
				'design_inputs_buttons_border_radius'          => 24,
				'design_social_icons_border_radius'            => 24,
			);

			/***** "Header->Layout" section */
			$header_layout = array(
				'header_layout_logo_position' => 'top',

				'header_layout_top_header'            => true,
				'header_layout_top_layer_composition' => 'brand-l_menu-l',
				'header_layout_top_components'        => array(
					'left'  => array(),
					'right' => array( 'authentication', 'search' ),
				),
				'header_layout_top_header_width'      => 'boxed',
				'header_layout_top_header_height'     => 'large',

				'header_layout_bottom_header'            => false,
				'header_layout_bottom_layer_composition' => 'brand-l_menu-l',
				'header_layout_bottom_components'        => array(
					'left'  => array(),
					'right' => array(),
				),
				'header_layout_bottom_header_width'      => 'boxed',
				'header_layout_bottom_header_height'     => 'large',

				'header_layout_shadow_position'    => 'none',
				'header_layout_logo_margin_top'    => 15,
				'header_layout_logo_margin_bottom' => 15,
				'header_layout_badges_position'    => 'outside',
				'header_layout_more_menu_position' => 'top',
				'header_layout_community_text'     => get_bloginfo( 'name' ) . ' ' . esc_html__( 'community', 'boombox' ),
				'header_layout_sticky_header'      => 'none',
				'header_layout_sticky_type'        => 'classic',
				'header_layout_button'             => true,
				'header_layout_button_text'        => esc_html__( 'Create a post', 'boombox' ),
				'header_layout_button_link'        => '',
				'header_layout_button_plus_icon'   => false,
			);

			/***** "Header->Colour & Style" section */
			$header_design = array(
				'header_design_site_title_color'          => '#1f1f1f',
				'header_design_top_background_color'      => '#ffe400',
				'header_design_top_gradient_color'        => '',
				'header_design_top_text_color'            => '#505050',
				'header_design_top_text_hover_color'      => '#505050',
				'header_design_top_include_in_color_mode'    => false,
				'header_design_bottom_background_color'   => '#ffffff',
				'header_design_bottom_gradient_color'     => '',
				'header_design_bottom_text_color'         => '#ffe400',
				'header_design_bottom_text_hover_color'   => '#505050',
				'header_design_bottom_include_in_color_mode' => false,
				'header_design_button_background_color'   => '#1f1f1f',
				'header_design_button_text_color'         => '#ffffff',
				'header_design_pattern_position'          => 'top',
				'header_design_pattern_type'              => 'rags-header.svg',
			);

			/***** "Header->Typography" section */
			$header_typography = array(
				'header_typography_top_menu_configuration'    => 'inherit',
				'header_typography_top_menu'                  => array(
					'font-family'    => 'Montserrat',
					'font-size'      => '12px',
					'font-style'     => 'normal',
					'font-weight'    => 600,
					'variant'        => 600,
					'letter-spacing' => '1px',
					'text-transform' => 'uppercase',
					'subsets'        => array(),
				),
				'header_typography_bottom_menu_configuration' => 'inherit',
				'header_typography_bottom_menu'               => array(
					'font-family'    => 'Montserrat',
					'font-size'      => '18px',
					'font-style'     => 'normal',
					'font-weight'    => 700,
					'variant'        => 700,
					'letter-spacing' => '1px',
					'text-transform' => 'capitalize',
					'subsets'        => array(),
				),
				'header_typography_sub_menu_configuration'    => 'inherit',
				'header_typography_sub_menu'                  => array(
					'font-family'    => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
					'font-size'      => '14px',
					'font-style'     => 'normal',
					'font-weight'    => 500,
					'variant'        => 500,
					'letter-spacing' => '0px',
					'text-transform' => 'none',
					'subsets'        => array( 'latin', 'latin-ext' ),
				),
			);

			$header_feaured_labels = array(
				'header_featured_labels_visibility'        => array( 'home', 'page' ),
				'header_featured_labels_background_color'  => '',
				'header_featured_labels_text_color'        => '#000000',
				'header_featured_labels_border_radius'     => 18,
				'header_featured_labels_disable_separator' => false,
			);

			/***** "Header->Posts Strip" section */
			$header_strip = array(
				'header_strip_visibility'     => array( 'home', 'page' ),
				'header_strip_size'           => 'big',
				'header_strip_title_position' => 'inside',
				'header_strip_width'          => 'boxed',
				'header_strip_type'           => 'slider',
				'header_strip_disable_gap'    => false,
				'header_strip_conditions'     => 'recent',
				'header_strip_time_range'     => 'all',
				'header_strip_category'       => '',
				'header_strip_tags'           => '',
				'header_strip_items_count'    => 18,
			);

			/**** "Footer->General" section */
			$footer_general = array(
				'footer_general_footer_top'    => true,
				'footer_general_footer_bottom' => true,
				'footer_general_social_icons'  => true,
			);

			/**** "Footer->Posts Strip" section */
			$footer_strip = array(
				'footer_strip_enable'      => true,
				'footer_strip_conditions'  => 'recent',
				'footer_strip_time_range'  => 'all',
				'footer_strip_category'    => '',
				'footer_strip_tags'        => '',
				'footer_strip_items_count' => 18,
			);

			/**** "Footer->Design" section */
			$footer_design = array(
				'footer_design_top_background_color'    => '#1f1f1f',
				'footer_design_top_primary_color'       => '#ffe400',
				'footer_design_top_primary_text_color'  => '#000000',
				'footer_design_top_heading_color'       => '#ffffff',
				'footer_design_top_text_color'          => '#ffffff',
				'footer_design_top_link_color'          => '#ffffff',
				'footer_design_bottom_background_color' => '#282828',
				'footer_design_bottom_text_color'       => '#ffffff',
				'footer_design_bottom_text_hover_color' => '#ffe400',
				'footer_design_pattern_position'        => 'top',
				'footer_design_pattern_type'            => 'rags-footer.svg',
			);

			/***** "Home->Main Posts" section */
			$home_main_posts = array(
				'home_main_posts_sidebar_type'        => '1-sidebar-1_3',
				'home_main_posts_sidebar_orientation' => 'right',
				'home_main_posts_listing_type'        => 'grid',
				'home_main_posts_hide_elements'       => array( 'tags' ),
				'home_main_posts_share_bar_elements'  => array( 'share_count', 'points' ),
				'home_main_posts_condition'           => 'recent',
				'home_main_posts_time_range'          => 'all',
				'home_main_posts_category'            => '',
				'home_main_posts_tags'                => '',
				'home_main_posts_pagination_type'     => 'load_more',
				'home_main_posts_posts_per_page'      => get_option( 'posts_per_page' ),
			);

			/***** "Home->Featured Area" section */
			$home_featured_area = array(
				'home_featured_area_type'                   => 'type-1-1',
				'home_featured_area_disable_gap'            => false,
				'home_featured_area_hide_elements'          => array(),
				'home_featured_area_conditions'             => 'recent',
				'home_featured_area_time_range'             => 'all',
				'home_featured_area_category'               => '',
				'home_featured_area_tags'                   => '',
				'home_featured_area_exclude_from_main_loop' => true,
			);

			/***** "Home->Visual Composer Content" section */
			$home_visual_composer_content = array();

			/**** "Archive->Main Posts" section */
			$archive_main_posts = array(
				'archive_main_posts_sidebar_type'        => '1-sidebar-1_3',
				'archive_main_posts_sidebar_orientation' => 'right',
				'archive_main_posts_template'            => 'right-sidebar',
				'archive_main_posts_listing_type'        => 'grid',
				'archive_main_posts_hide_elements'       => array( 'tags' ),
				'archive_main_posts_share_bar_elements'  => array( 'share_count', 'points' ),
				'archive_main_posts_default_order'       => 'recent',
				'archive_main_posts_pagination_type'     => 'load_more',
				'archive_main_posts_posts_per_page'      => get_option( 'posts_per_page' ),
			);

			/**** "Archive->Header" section */
			$archive_header = array(
				'archive_header_disable'                  => false,
				'archive_header_style'                    => 'style1',
				'archive_header_background_container'     => 'boxed',
				'archive_header_default_background_image' => '',
				'archive_header_filters'                  => true,
				'archive_header_enable_badge'             => true,
			);

			/**** "Archive->Featured Area" section */
			$archive_featured_area = array(
				'archive_featured_area_type'                   => 'type-1-1',
				'archive_featured_area_disable_gap'            => false,
				'archive_featured_area_hide_elements'          => array(),
				'archive_featured_area_conditions'             => 'recent',
				'archive_featured_area_time_range'             => 'all',
				'archive_featured_area_exclude_from_main_loop' => true,
			);

			/**** "Archive->Posts Strip" section */
			$archive_strip = array(
				'archive_strip_configuration'  => 'inherit',
				'archive_strip_type'           => 'slider',
				'archive_strip_width'          => 'boxed',
				'archive_strip_size'           => 'big',
				'archive_strip_title_position' => 'inside',
				'archive_strip_disable_gap'    => false,
				'archive_strip_conditions'     => 'recent',
				'archive_strip_time_range'     => 'all',
				'archive_strip_items_count'    => 18,
				'archive_strip_category'       => '',
				'archive_strip_tags'           => '',
			);

			/**** "Single Post->General" section */
			$single_post_general = array(
				'single_post_general_sidebar_type'                       => '1-sidebar-1_3',
				'single_post_general_sidebar_orientation'                => 'right',
				'single_post_general_featured_media'                     => false,
				'single_post_general_layout'                             => 'style1',
				'single_post_general_hide_elements'                      => array(),
				'single_post_general_top_sharebar'                       => true,
				'single_post_general_bottom_sharebar'                    => true,
				'single_post_general_share_box_elements'                 => array( 'share_count', 'points' ),
				'single_post_general_sections'                           => array(
					'reactions',
					'author_info',
					'comments',
					'navigation',
					'related_posts',
					'more_from_posts',
					'dont_miss_posts',
				),
				'single_post_general_enable_full_post_button_conditions' => array( 'image_ratio' ),
				'single_post_general_post_button_label'                  => esc_html__( 'View Full Post', 'boombox' ),
				'single_post_general_pagination_layout'                  => 'page_xy',
				'single_post_general_navigation_direction'               => 'to-oldest',
				'single_post_general_next_prev_buttons'                  => true,
				'single_post_general_floating_navbar'                    => 'post_title',
				'single_post_general_floating_navbar_navigation'         => true,
				'single_post_general_side_navigation'                    => true,
			);

			/**** "Single Post->Related Posts" section */
			$single_post_related = array(
				'single_post_related_posts_related_entries_per_page'    => 6,
				'single_post_related_posts_related_entries_heading'     => esc_html__( 'You may also like', 'boombox' ),
				'single_post_related_posts_more_entries_per_page'       => 6,
				'single_post_related_posts_more_entries_heading'        => esc_html__( 'More From:', 'boombox' ),
				'single_post_related_posts_dont_miss_entries_per_page'  => 6,
				'single_post_related_posts_dont_miss_entries_heading'   => esc_html__( 'DON\'T MISS', 'boombox' ),
				'single_post_related_posts_grid_sections_hide_elements' => array(),
			);

			/**** "Single Post->Posts Strip" section */
			$single_post_strip = array(
				'single_post_strip_configuration'  => 'inherit',
				'single_post_strip_size'           => 'big',
				'single_post_strip_title_position' => 'inside',
				'single_post_strip_width'          => 'boxed',
				'single_post_strip_type'           => 'slider',
				'single_post_strip_disable_gap'    => false,
				'single_post_strip_conditions'     => 'recent',
				'single_post_strip_time_range'     => 'all',
				'single_post_strip_category'       => '',
				'single_post_strip_tags'           => '',
				'single_post_strip_items_count'    => 18,
			);

			/**** "Single Post->Sponsored Articles" section */
			$single_post_sponsored_articles = array(
				'single_post_sponsored_articles_label'    => esc_html__( 'presented by', 'boombox' ),
				'single_post_sponsored_articles_position' => 'top'
			);

			/**** "Mobile->Global" section */
			$mobile_global = array(
				'mobile_global_enable_strip'         => false,
				'mobile_global_enable_footer_strip'  => false,
				'mobile_global_enable_featured_area' => false,
				'mobile_global_enable_sidebar'       => false,
			);

			/**** "Mobile->Header" section */
			$mobile_header = array(
				'mobile_header_composition'           => 'brand-l',
				'mobile_header_components'            => array(
					'left'  => array(),
					'right' => array(
						'search',
						'authentication',
					),
				),
				'mobile_header_sticky'                => 'classic',
				'mobile_header_logo'                  => '',
				'mobile_header_logo_width'            => '',
				'mobile_header_logo_height'           => '',
				'mobile_header_logo_hdpi'             => '',
				'mobile_header_logo_margin_top'       => 5,
				'mobile_header_logo_margin_bottom'    => 5,
				'mobile_header_bg_color'              => '#ffe400',
				'mobile_header_gradient_color'        => '',
				'mobile_header_text_color'            => '#1f1f1f',
				'mobile_header_address_bar_color'     => '',
				'mobile_header_include_in_color_mode' => false,
			);

			/***** "Extras->Authentication" section */
			$extras_authentication = array(
				'extra_authentication_enable_site_auth'            => true,
				'extra_authentication_login_popup_title'           => esc_html__( 'log in', 'boombox' ),
				'extra_authentication_login_popup_text'            => '',
				'extra_authentication_enable_remember_me'          => true,
				'extra_authentication_registration_custom_url'     => '',
				'extra_authentication_registration_popup_title'    => esc_html__( 'sign up', 'boombox' ),
				'extra_authentication_registration_popup_text'     => '',
				'extra_authentication_forgot_password_popup_title' => esc_html__( 'forgot password', 'boombox' ),
				'extra_authentication_forgot_password_popup_text'  => '',
				'extra_authentication_reset_password_popup_title' => esc_html__( 'reset password', 'boombox' ),
				'extra_authentication_reset_password_popup_text'  => '',
				'extra_authentication_terms_of_use_page'           => 0,
				'extra_authentication_privacy_policy_page'         => 0,
				'extra_authentication_enable_login_captcha'        => true,
				'extra_authentication_enable_registration_captcha' => true,
				'extra_authentication_captcha_type'                => 'image',
				'extra_authentication_google_recaptcha_site_key'   => '',
				'extra_authentication_google_recaptcha_secret_key' => '',
				'extra_authentication_google_v3_recaptcha_site_key' => '',
				'extra_authentication_google_v3_recaptcha_secret_key'  => '',
				'extra_authentication_enable_social_auth'          => false,
				'extra_authentication_facebook_app_id'             => '',
				'extra_authentication_google_oauth_id'             => '',
				'extra_authentication_google_api_key'              => '',
			);

			/***** "Extras->GDPR" section */
			$extras_gdpr = array(
				'extras_gdpr_visibility'            => array(),
				'extras_gdpr_cookie_consent_script' => ''
			);

			/***** "Extras->Video Control" section */
			$extras_video_control = array(
				'extras_video_control_enable_mp4_video_on_post_listings'   => true,
				'extras_video_control_enable_embed_video_on_post_listings' => true,
				'extras_video_control_mp4_video_player_controls'           => 'mute',
				'extras_video_control_mp4_video_autoplay'                  => 'scroll',
				'extras_video_control_mp4_video_sound'                     => 'muted',
				'extras_video_control_mp4_video_click_event_handler'       => 'mute_unmute',
				'extras_video_control_enable_mp4_video_loop'               => true,
			);

			/***** "Extras->Gif Control" section */
			$extras_gif_control = array(
				'extras_gif_control_enable_sharing'           => true,
				'extras_gif_control_animation_event'          => 'click',
				'extras_gif_control_cloudconvert_app_key'     => '',
				'extras_gif_control_storage'                  => 'local',
				'extras_gif_control_aws_s3_access_key_id'     => '',
				'extras_gif_control_aws_s3_secret_access_key' => '',
				'extras_gif_control_aws_s3_bucket_name'       => '',
			);

			/**** "Extras->Post Ranking System" section */
			$extras_post_ranking_system = array(
				'extras_post_ranking_system_enable_view_track'      => true,
				'extras_post_ranking_system_points_login_require'   => false,
				'extras_post_ranking_system_numeration_badges'      => true,
				'extras_post_ranking_system_trending_conditions'    => 'most_viewed',
				'extras_post_ranking_system_trending_enable'        => true,
				'extras_post_ranking_system_trending_page'          => 0,
				'extras_post_ranking_system_trending_posts_count'   => 25,
				'extras_post_ranking_system_trending_minimal_score' => 5,
				'extras_post_ranking_system_trending_badge'         => true,
				'extras_post_ranking_system_hot_enable'             => true,
				'extras_post_ranking_system_hot_page'               => 0,
				'extras_post_ranking_system_hot_posts_count'        => 25,
				'extras_post_ranking_system_hot_minimal_score'      => 5,
				'extras_post_ranking_system_hot_badge'              => true,
				'extras_post_ranking_system_popular_enable'         => true,
				'extras_post_ranking_system_popular_page'           => 0,
				'extras_post_ranking_system_popular_posts_count'    => 25,
				'extras_post_ranking_system_popular_minimal_score'  => 5,
				'extras_post_ranking_system_popular_badge'          => true,
				'extras_post_ranking_system_fake_views_count'       => 0,
				'extras_post_ranking_system_fake_points_count'      => 0,
				'extras_post_ranking_system_views_count_scale'      => 1,
				'extras_post_ranking_system_views_count_style'      => 'rounded',
			);

			/**** "Extras->Post Reaction System" section */
			$extras_post_reaction_system = array(
				'extras_post_reaction_system_enable'                   => true,
				'extras_post_reaction_system_login_require'            => false,
				'extras_post_reaction_system_award_minimal_score'      => 3,
				'extras_post_reaction_system_maximal_count_per_vote'   => 3,
				'extras_post_reaction_system_fake_reaction_count_base' => 0,
			);

			/**** "Extras->Badges" section */
			$extras_badges = array(
				'extras_badges_position_on_thumbnails'          => 'outside-left',
				'extras_badges_reactions'                       => true,
				'extras_badges_reactions_background_color'      => '#ffe400',
				'extras_badges_reactions_text_color'            => '#1f1f1f',
				'extras_badges_reactions_type'                  => 'face-text',
				'extras_badges_trending'                        => true,
				'extras_badges_trending_icon'                   => 'trending5',
				'extras_badges_trending_background_color'       => '#f43547',
				'extras_badges_trending_icon_color'             => '#ffffff',
				'extras_badges_trending_text_color'             => '#1f1f1f',
				'extras_badges_category'                        => true,
				'extras_badges_show_for_categories'             => array( 'quiz', 'poll' ),
				'extras_badges_show_for_post_tags'              => array( 'quiz', 'poll' ),
				'extras_badges_category_background_color'       => '#6759eb',
				'extras_badges_category_icon_color'             => '#ffffff',
				'extras_badges_category_text_color'             => '#1f1f1f',
				'extras_badges_post_type_badges'                => false,
				'extras_badges_post_type_badges_on_strip'       => false,
				'extras_badges_categories_for_post_type_badges' => array(),
				'extras_badges_post_tags_for_post_type_badges'  => array(),
			);

			/**** "Extras->NSFW" section */
			$extras_nsfw = array(
				'extras_nsfw_categories'   => array( 'nsfw' ),
				'extras_nsfw_require_auth' => true,
			);

			/**** "Extras->Breadcrumb" section */
			$extras_breadcrumb = array(
				'extras_breadcrumb_visibility'   => array( 'archive', 'page', 'post' ),
			);

			/**** "Extras->Reading Time" section */
			$extras_reading_time = array(
				'extras_reading_time_words_per_minute' => 300,
				'extras_reading_time_include_images'   => true,
				'extras_reading_time_visibility'       => array()
			);

			/**** "Extras->Image Sizes" section */
			$extras_image_sizes = array(
				'extras_image_sizes_active_sizes' => array(
					'boombox_image200x150',
					'boombox_image360',
					'boombox_image360x180',
					'boombox_image360x270',
					'boombox_image545',
					'boombox_image768x450',
					'boombox_image768',
					'boombox_image1600'
				)
			);

			/**** "Extras->Additional" section */
			$extras_additional = array(
				'extra_additional_enable_back_to_top_btn' => true,
				'extra_additional_affiliate_button_text' => esc_html__( 'Check It Out', 'boombox' ),
			);

			$this->default_values = array_merge(
				$site_identity,
				$design,
				$header_layout,
				$header_design,
				$header_feaured_labels,
				$header_typography,
				$header_strip,
				$footer_general,
				$footer_strip,
				$footer_design,
				$home_main_posts,
				$home_featured_area,
				$home_visual_composer_content,
				$archive_main_posts,
				$archive_header,
				$archive_featured_area,
				$archive_strip,
				$single_post_general,
				$single_post_related,
				$single_post_strip,
				$single_post_sponsored_articles,
				$mobile_global,
				$mobile_header,
				$extras_authentication,
				$extras_gdpr,
				$extras_video_control,
				$extras_gif_control,
				$extras_post_ranking_system,
				$extras_post_reaction_system,
				$extras_badges,
				$extras_nsfw,
				$extras_breadcrumb,
				$extras_reading_time,
				$extras_image_sizes,
				$extras_additional
			);
		}

		/**
		 * Get default values
		 *
		 * @return array
		 */
		public function get_default_values() {
			return apply_filters( 'boombox/customizer_default_values', $this->default_values );
		}

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return Boombox_Customizer|null
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * @var string Relative path to sections folder
		 */
		private $sections_path = 'sections';

		/**
		 * @var string Relative path to field types folder folder
		 */
		private $field_types_path = 'custom-field-types';

		/**
		 * Boombox_Customizer constructor.
		 */
		private function __construct() {
			if ( ! class_exists( 'Kirki' ) ) {
				return;
			}

			$this->disable_css_output();
			$this->set_default_values();
			$this->includes();
			$this->setup_hooks();
		}

		/**
		 * A dummy magic method to prevent Boombox_Customizer from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Include files
		 */
		private function includes() {
			// "Site Identity" section
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'site-identity.php' );

			// "Design" section
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'design.php' );

			// "Header" panel
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'header' . DIRECTORY_SEPARATOR . 'layout.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'header' . DIRECTORY_SEPARATOR . 'design.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'header' . DIRECTORY_SEPARATOR . 'typography.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'header' . DIRECTORY_SEPARATOR . 'featured-labels.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'header' . DIRECTORY_SEPARATOR . 'strip.php' );

			// "Footer" panel
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'footer' . DIRECTORY_SEPARATOR . 'general.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'footer' . DIRECTORY_SEPARATOR . 'strip.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'footer' . DIRECTORY_SEPARATOR . 'design.php' );

			// "Home" panel
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'main-posts.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'featured-area.php' );
			/*
			 * Temporary commented
			 * @since v2.0.3
			 * require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'visual-composer.php' );
			 */

			// "Archive" panel
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . 'main-posts.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . 'title-area.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . 'featured-area.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . 'strip.php' );

			// "Single Post" panel
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'single-post' . DIRECTORY_SEPARATOR . 'general.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'single-post' . DIRECTORY_SEPARATOR . 'related-posts.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'single-post' . DIRECTORY_SEPARATOR . 'strip.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'single-post' . DIRECTORY_SEPARATOR . 'sponsored-articles.php' );

			// "Mobile" panel
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'mobile' . DIRECTORY_SEPARATOR . 'global.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'mobile' . DIRECTORY_SEPARATOR . 'header.php' );

			// "Extras" panel
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'authentication.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'gdpr.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'video-control.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'gif-control.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'posts-ranking-system.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'posts-reaction-system.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'badges.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'nsfw.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'breadcrumb.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'reading-time.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'image-sizes.php' );
			require_once( $this->sections_path . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'additional.php' );

			/***** Allow others to include files */
			do_action( 'boombox/customizer/include_files', $this );
		}

		/**
		 * Setup hooks
		 */
		private function setup_hooks() {
			add_action( 'customize_register', array( $this, 'register_custom_field_types' ), 10, 1 );
			add_action( 'customize_register', array( $this, 'move_homepage_settings_to_required_panel' ), 10, 1 );
			add_action( 'init', array( $this, 'init' ) );
			add_filter( 'kirki/config', array( $this, 'edit_customizer_styles' ) );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_control_css' ) );
		}

		/**
		 * Register custom field types
		 *
		 * @param $customizer WP_Customize_Manager Customizer instance
		 */
		public function register_custom_field_types( $customizer ) {
			// "Sortable Composition" field
			require_once( $this->field_types_path . DIRECTORY_SEPARATOR . 'sortable-composition' . DIRECTORY_SEPARATOR . 'master.php' );
			require_once( $this->field_types_path . DIRECTORY_SEPARATOR . 'sortable-composition' . DIRECTORY_SEPARATOR . 'slave.php' );
		}

		/**
		 * @param $customizer WP_Customize_Manager Customizer instance
		 */
		public function move_homepage_settings_to_required_panel( $customizer ) {
			$static_front_page_section = $customizer->get_section( 'static_front_page' );
			if ( $static_front_page_section && is_a( $static_front_page_section, 'WP_Customize_Section' ) ) {
				$static_front_page_section->panel = 'boombox_home';
				$static_front_page_section->priority = 50;
			}
		}

		/**
		 * Customizer init
		 */
		public function init() {
			$this->register_config();
			$this->add_panels();
			$this->add_sections();
			$this->add_fields();
		}

		/**
		 * Edit customizer style
		 *
		 * @param array $config Current configuration
		 *
		 * @return array
		 */
		public function edit_customizer_styles( $config ) {
			return wp_parse_args( array(
				'logo_image'  => 'https://boombox.px-lab.com/wp-content/uploads/2017/03/original-logo.png',
				'description' => esc_attr__( 'Boombox is most powerful and flexible viral and buzz style WordPress theme. Flexible and fully customizable viral magazine theme combined with most powerful Viral content plugin with a ton of snacks and exclusive features and all that packed with dozens of powerful and popular plugins and with top-notch design.', 'boombox' ),
			), $config );
		}

		/**
		 * Enqueue additional styles
		 */
		public function customize_control_css() {
			$min = boombox_get_minified_asset_suffix();

			wp_enqueue_style(
				'boombox-customize-controls',
				BOOMBOX_INCLUDES_URL . 'customizer/assets/css/style' . $min . '.css',
				array(),
				boombox_get_assets_version()
			);
		}

		/**
		 * Get panels configuration
		 * @return array
		 */
		private function get_panels() {

			/***** Theme core panels */
			$panels = array(
				array(
					'panel_id'    => 'boombox_header',
					'priority'    => 26,
					'title'       => __( 'Header', 'boombox' ),
					'description' => __( 'Boombox Header Settings', 'boombox' ),
				),
				array(
					'panel_id'    => 'boombox_footer',
					'priority'    => 27,
					'title'       => __( 'Footer', 'boombox' ),
					'description' => __( 'Boombox Footer Settings', 'boombox' ),
				),
				array(
					'panel_id'    => 'boombox_home',
					'priority'    => 28,
					'title'       => __( 'Home', 'boombox' ),
					'description' => __( 'Boombox "Home Page" Settings', 'boombox' ),
				),
				array(
					'panel_id'    => 'boombox_single_post',
					'priority'    => 29,
					'title'       => __( 'Single Post', 'boombox' ),
					'description' => __( 'Boombox "Single Post" Settings', 'boombox' ),
				),
				array(
					'panel_id'    => 'boombox_archive',
					'priority'    => 30,
					'title'       => __( 'Archive', 'boombox' ),
					'description' => __( 'Boombox "Archive Template" Settings', 'boombox' ),
				),
				array(
					'panel_id'    => 'boombox_mobile',
					'priority'    => 31,
					'title'       => __( 'Mobile', 'boombox' ),
					'description' => __( 'Boombox "Mobile" Settings', 'boombox' ),
				),
				array(
					'panel_id'    => 'boombox_extras',
					'priority'    => 32,
					'title'       => __( 'Extras', 'boombox' ),
					'description' => __( 'Boombox "Extra" Settings', 'boombox' ),
				),
			);

			// Let others to add panels
			return apply_filters( 'boombox/customizer/register/panels', $panels );
		}

		/**
		 * Get sections
		 * @return array()
		 */
		private function get_sections() {
			// Let others to add sections
			return apply_filters( 'boombox/customizer/register/sections', array() );
		}

		/**
		 * Get fields
		 * @return array
		 */
		private function get_fields() {
			// Let other to add fields
			return apply_filters( 'boombox/customizer/register/fields',
				array(),
				$this->get_default_values(),
				$this
			);
		}

		/**
		 * Register config
		 */
		private function register_config() {

			Kirki::add_config( self::CONFIG_ID, array(
				'capability'  => 'edit_theme_options',
				'option_type' => 'option',
				'option_name' => self::OPTION_NAME,
			) );

		}

		/**
		 * Register panels
		 */
		private function add_panels() {
			foreach ( $this->get_panels() as $panel ) {
				Kirki::add_panel( $panel[ 'panel_id' ], array(
					'priority'    => $panel[ 'priority' ],
					'title'       => $panel[ 'title' ],
					'description' => $panel[ 'description' ],
				) );
			}
		}

		/**
		 * Register sections
		 */
		private function add_sections() {
			foreach ( $this->get_sections() as $section ) {
				Kirki::add_section( $section[ 'id' ], $section[ 'args' ] );
			}
		}

		/**
		 * Register fields
		 */
		private function add_fields() {
			foreach ( $this->get_fields() as $field ) {
				Kirki::add_field( self::CONFIG_ID, $field );
			}
		}

		/**
		 * Get customizer option
		 *
		 * @param string $option_name   Option name
		 * @param bool   $force_default Force returning default values
		 *
		 * @return mixed
		 */
		public function get_option( $option_name, $force_default = false ) {

			$values = get_option( self::OPTION_NAME, array() );

			if ( ! isset ( $values[ $option_name ] ) || $force_default ) {
				$defaults = $this->get_default_values();
				$value = isset( $defaults[ $option_name ] ) ? $defaults[ $option_name ] : null;
			} else {
				$value = $values[ $option_name ];
			}

			return $value;
		}

		/**
		 * Get values of customizer options set
		 *
		 * @param array $options Options
		 *
		 * @return array
		 */
		public function get_options_set( $options = array() ) {
			$values = get_option( self::OPTION_NAME, array() );

			$options = array_flip( $options );
			$values = array_intersect_key( $values, $options );

			// fill absentees with default values
			if ( count( $values ) < count( $options ) ) {
				$absentees = array_intersect_key(
					$this->get_default_values(),
					array_flip( array_keys( array_diff_key( $options, $values ) ) )
				);

				$values = array_merge( $values, $absentees );

				// if we still have difference, let's fill that unknown fields values with nulls
				if ( count( $values ) < count( $options ) ) {
					$nullable = array_fill_keys( array_keys( array_diff_key( $options, $values ) ), null );
					$values = array_merge( $values, $nullable );
				}
			}

			return $values;
		}

		/**
		 * Sanitize number field
		 *
		 * @param $value
		 *
		 * @return int|null|WP_Error
		 */
		public function sanitize_number( $value ) {
			$can_validate = method_exists( 'WP_Customize_Setting', 'validate' );
			if ( ! is_numeric( $value ) ) {
				return $can_validate ? new WP_Error( 'nan', __( 'Not a number', 'boombox' ) ) : null;
			}

			return intval( $value );
		}

		/**
		 * Completely disable CSS output for avoid duplicate fonts, we are already using remote fonts from google
		 */
		private function disable_css_output() {
			if ( ! defined( 'KIRKI_NO_OUTPUT' ) ) {
				define( 'KIRKI_NO_OUTPUT', true );
			}
		}

	}

	Boombox_Customizer::get_instance();

}