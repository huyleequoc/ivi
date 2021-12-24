<?php
/**
 * Boombox deprecated functions and methods
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Check whether the plugin is active
 *
 * @since      1.0.0
 * @deprecated 2.0.0
 *
 * @param string $plugin Path to the main plugin file from plugins directory.
 *
 * @return bool
 */
function boombox_is_plugin_active( $plugin ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'boombox_plugin_management_service()' );

	return boombox_plugin_management_service()->is_plugin_active( $plugin );
}

/**
 * Get single page settings
 *
 * @param string $featured_image_size Featured image size
 *
 * @return array
 */
function boombox_get_single_post_settings( $featured_image_size = 'boombox_image768' ) {
	_deprecated_function( __FUNCTION__, '2.0.0', "Boombox_Template::init( 'post' )->get_options()" );

	return Boombox_Template::init( 'post' )->get_options( $featured_image_size );
}

/**
 * Get list type classes
 *
 * @param       $list_type
 * @param array $add_grid_class
 * @param array $additional_classes
 */
function boombox_list_type_classes( $list_type, $add_grid_class = array(), $additional_classes = array() ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'boombox_get_list_type_classes' );

	echo boombox_get_list_type_classes( $list_type, $add_grid_class, $additional_classes );
}

/**
 * Get single post layout
 *
 * @return mixed
 */
function boombox_get_single_post_template() {
	_deprecated_function( __FUNCTION__, '2.0.0', "Boombox_Template::init( 'post' )->get_layout()" );

	return Boombox_Template::init( 'post' )->get_layout();
}

/**
 * Get page settings
 *
 * @param int $paged Current page number
 *
 * @return array
 *
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Template::init( 'page' )->get_options()
 * @see        Boombox_Page_Template_Helper::get_options()
 */
function boombox_get_page_settings( $paged ) {
	_deprecated_function( __FUNCTION__, '2.0.0', "Boombox_Template::init( 'page' )->get_options()" );

	return Boombox_Template::init( 'page' )->get_options( $paged );
}

/**
 * Render listing container classes by listing type
 *
 * @param string $list_type           Listing type
 * @param string $additional_position Additional position
 *
 * @since      1.0.0
 * @deprecated 2.0.0
 * @see        boombox_get_container_classes_by_type()
 */
function boombox_container_classes_by_type( $list_type, $additional_position = '' ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'boombox_get_container_classes_by_type' );

	echo boombox_get_container_classes_by_type( $list_type, $additional_position );
}

/**
 * Get author template settings
 * @return array
 *
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Author_Template_Helper::get_options()
 * @see        Boombox_Author_Template_Helper::get_options()
 */
function boombox_get_author_template_settings() {
	_deprecated_function( __FUNCTION__, '2.0.0', "Boombox_Template::init( 'author' )->get_options()" );

	return Boombox_Template::init( 'author' )->get_options();
}

/**
 * Get footer settings
 * @return array <string, bool|string>
 *
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Template::init( 'footer' )->get_options()
 * @see        Boombox_Footer_Template_Helper::get_options()
 */
function boombox_get_footer_settings() {
	_deprecated_function( __FUNCTION__, '2.0.0', "Boombox_Template::init( 'footer' )->get_options()" );

	return Boombox_Template::init( 'footer' )->get_options();
}

/**
 * Get Featured Strip Template Settings
 * @return mixed
 *
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Template::init( 'featured-strip' )->get_options()
 * @see        Boombox_Featured_Strip_Template_Helper::get_options()
 */
function boombox_get_featured_strip_settings() {
	_deprecated_function( __FUNCTION__, '2.0.0', "Boombox_Template::init( 'featured-strip' )->get_options()" );

	return Boombox_Template::init( 'featured-strip' )->get_options();
}

/**
 * Get featured area query
 *
 * @return WP_Query
 *
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Template::init( 'featured-area' )->get_query()
 * @see        Boombox_Featured_Area_Template_Helper::get_query()
 */
function boombox_get_featured_area_items() {
	_deprecated_function( __FUNCTION__, '2.0.0', "Boombox_Template::init( 'featured-area' )->get_query()" );

	return Boombox_Template::init( 'featured-area' )->get_query();
}

/**
 * Get listing type choices
 *
 * @param string $type Return type
 *
 * @return array
 *
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_listing_types()
 * @see        Boombox_Choices_Helper::get_listing_types()
 */
function boombox_get_listing_types_choices( $type = 'value=>label' ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_listing_types()' );

	return Boombox_Choices_Helper::get_instance()->get_listing_types( $type );
}

/**
 * Return list of categories
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_categories()
 * @see        Boombox_Choices_Helper::get_categories()
 */
function boombox_get_category_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_categories()' );

	return Boombox_Choices_Helper::get_instance()->get_categories();
}

/**
 * Get list of tags
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_tags()
 * @see        Boombox_Choices_Helper::get_tags()
 */
function boombox_get_tag_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_tags()' );

	return Boombox_Choices_Helper::get_instance()->get_tags();
}

/**
 * Get grid hide elements choices for mobile
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_mobile_grid_hide_elements()
 * @see        Boombox_Choices_Helper::get_mobile_grid_hide_elements()
 */
function boombox_mobile_get_grid_hide_elements_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_mobile_grid_hide_elements()' );

	return Boombox_Choices_Helper::get_instance()->get_mobile_grid_hide_elements();
}

/**
 * Get post hide elements choices for mobile
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_mobile_post_hide_elements()
 * @see        Boombox_Choices_Helper::get_mobile_post_hide_elements()
 */
function boombox_mobile_get_post_hide_elements_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_mobile_post_hide_elements()' );

	return Boombox_Choices_Helper::get_instance()->get_mobile_post_hide_elements();
}

/**
 * Get secondary content position choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_sidebar_orientation()
 * @see        Boombox_Choices_Helper::get_sidebar_orientation()
 */
function boombox_get_secondary_sidebar_position_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_sidebar_orientation()' );

	return Boombox_Choices_Helper::get_instance()->get_sidebar_orientation();
}

/**
 * Get pagination types
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_pagination_types()
 * @see        Boombox_Choices_Helper::get_pagination_types()
 */
function boombox_get_pagination_types_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_pagination_types()' );

	return Boombox_Choices_Helper::get_instance()->get_pagination_types();
}

/**
 * Get Conditions
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_conditions()
 * @see        Boombox_Choices_Helper::get_conditions()
 */
function boombox_get_conditions_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_conditions()' );

	return Boombox_Choices_Helper::get_instance()->get_conditions();
}

/**
 * Get Trending Conditions
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_trending_conditions()
 * @see        Boombox_Choices_Helper::get_trending_conditions()
 */
function boombox_get_trending_conditions_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_trending_conditions()' );

	return Boombox_Choices_Helper::get_instance()->get_trending_conditions();
}

/**
 * Get time range choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_time_ranges()
 * @see        Boombox_Choices_Helper::get_time_ranges()
 */
function boombox_get_time_range_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_time_ranges()' );

	return Boombox_Choices_Helper::get_instance()->get_time_ranges();
}

/**
 * Get font choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_all_fonts()
 * @see        Boombox_Choices_Helper::get_all_fonts()
 */
function boombox_get_font_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_all_fonts()' );

	return Boombox_Choices_Helper::get_instance()->get_all_fonts();
}

/**
 * Get default fonts
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_default_fonts()
 * @see        Boombox_Choices_Helper::get_default_fonts()
 */
function boombox_get_default_fonts() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_default_fonts()' );

	return Boombox_Choices_Helper::get_instance()->get_default_fonts();
}

/**
 * Get Google fonts
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0
 */
function boombox_get_google_fonts() {
	_deprecated_function( __FUNCTION__, '2.0.0' );
}

/**
 * Get Google fonts choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0
 */
function boombox_get_google_fonts_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0' );
}

/**
 * Get Google fonts subset choices
 *
 * @return array
 * @since      1.0.0
 */
function boombox_get_google_fonts_subset_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0' );
}

/**
 * Get Page Ad choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_injects()
 * @see        Boombox_Choices_Helper::get_injects()
 */
function boombox_get_page_ad_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_injects()' );

	return Boombox_Choices_Helper::get_instance()->get_injects();
}

/**
 * Get Page Newsletter choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_injects()
 * @see        Boombox_Choices_Helper::get_injects()
 */
function boombox_get_page_newsletter_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_injects()' );

	return Boombox_Choices_Helper::get_instance()->get_injects();
}

/**
 * Get post featured image choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_post_featured_image_appearance()
 * @see        Boombox_Choices_Helper::get_post_featured_image_appearance()
 */
function boombox_single_post_featured_image_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_post_featured_image_appearance()' );

	return Boombox_Choices_Helper::get_instance()->get_post_featured_image_appearance();
}

/**
 * Get strip size choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_strip_sizes()
 * @see        Boombox_Choices_Helper::get_strip_sizes()
 */
function boombox_get_strip_size_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_strip_sizes()' );

	return Boombox_Choices_Helper::get_instance()->get_strip_sizes();
}

/**
 * Get strip title position choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_strip_title_positions()
 * @see        Boombox_Choices_Helper::get_strip_title_positions()
 */
function boombox_get_strip_title_position_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_strip_title_positions()' );

	return Boombox_Choices_Helper::get_instance()->get_strip_title_positions();
}

/**
 * Get strip width choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_strip_dimensions()
 * @see        Boombox_Choices_Helper::get_strip_dimensions()
 */
function boombox_get_strip_width_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_strip_dimensions()' );

	return Boombox_Choices_Helper::get_instance()->get_strip_dimensions();
}

/**
 * Get strip type choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_strip_types()
 * @see        Boombox_Choices_Helper::get_strip_types()
 */
function boombox_get_strip_type_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_strip_types()' );

	return Boombox_Choices_Helper::get_instance()->get_strip_types();
}

/**
 * Get featured area type choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_featured_area_types()
 * @see        Boombox_Choices_Helper::get_featured_area_types()
 */
function boombox_get_featured_type_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_featured_area_types()' );

	return Boombox_Choices_Helper::get_instance()->get_featured_area_types();
}

/**
 * Get "view full post" button choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_view_full_post_button_appearance_conditions()
 * @see        Boombox_Choices_Helper::get_view_full_post_button_appearance_conditions()
 */
function boombox_get_disable_view_full_post_button_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_view_full_post_button_appearance_conditions()' );

	return Boombox_Choices_Helper::get_instance()->get_view_full_post_button_appearance_conditions();
}

/**
 * Get MP4/video player control choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_mp4_video_player_controls()
 * @see        Boombox_Choices_Helper::get_mp4_video_player_controls()
 */
function boombox_get_mp4_video_player_control_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_mp4_video_player_controls()' );

	return Boombox_Choices_Helper::get_instance()->get_mp4_video_player_controls();
}

/**
 * Get MP4/video player auto play choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_mp4_video_player_auto_plays()
 * @see        Boombox_Choices_Helper::get_mp4_video_player_auto_plays()
 */
function boombox_get_mp4_video_player_autoplay_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_mp4_video_player_auto_plays()' );

	return Boombox_Choices_Helper::get_instance()->get_mp4_video_player_auto_plays();
}

/**
 * Get MP4/video player sound choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_mp4_video_player_sound_options()
 * @see        Boombox_Choices_Helper::get_mp4_video_player_sound_options()
 */
function boombox_get_mp4_video_player_sound_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_mp4_video_player_sound_options()' );

	return Boombox_Choices_Helper::get_instance()->get_mp4_video_player_sound_options();
}

/**
 * Get MP4/video player click event handler choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_mp4_video_player_click_event_handlers()
 * @see        Boombox_Choices_Helper::get_mp4_video_player_click_event_handlers()
 */
function boombox_get_mp4_video_player_click_event_handler_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_mp4_video_player_click_event_handlers()' );

	return Boombox_Choices_Helper::get_instance()->get_mp4_video_player_click_event_handlers();
}

/**
 * Get single post sortable section choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_post_sortable_sections()
 * @see        Boombox_Choices_Helper::get_post_sortable_sections()
 */
function boombox_single_post_sortable_section_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_post_sortable_sections()' );

	return Boombox_Choices_Helper::get_instance()->get_post_sortable_sections();
}

/**
 * Get Post template choices
 *
 * @return array
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Choices_Helper::get_post_templates()
 * @see        Boombox_Choices_Helper::get_post_templates()
 */
function boombox_get_post_template_choices() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'Boombox_Choices_Helper::get_instance()->get_post_templates()' );

	return Boombox_Choices_Helper::get_instance()->get_post_templates();
}

/**
 * Check against sidebar enabled feature
 * @return bool
 *
 * @since      1.0.0
 * @deprecated 2.0.0 boombox_is_primary_sidebar_enabled()
 * @see        boombox_is_primary_sidebar_enabled();
 */
function boombox_is_sidebar_enabled() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'boombox_is_primary_sidebar_enabled' );

	return boombox_is_primary_sidebar_enabled();
}

/**
 * Returns site authentication status
 * @return bool
 * @since      1.0.0
 * @deprecated 2.0.0 boombox_is_auth_allowed()
 * @see        boombox_is_auth_allowed();
 */
function boombox_disabled_site_auth() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'boombox_is_auth_allowed' );

	return ! boombox_is_auth_allowed();
}

/**
 * Return Get Featured Strip Items
 *
 * @return WP_Query
 *
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Template::init( 'featured-strip' )->get_footer_query()
 * @see        Boombox_Featured_Strip_Template_Helper::get_footer_query();
 */
function boombox_get_footer_featured_strip_items() {
	_deprecated_function( __FUNCTION__, '2.0.0', "Boombox_Template::init( 'featured-strip' )->get_footer_query()" );

	return Boombox_Template::init( 'featured-strip' )->get_footer_query();
}

/**
 * Get predefined
 *
 * @param string $template
 *
 * @return int
 *
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Template::init( 'featured-area' )->get_items_count_by_template()
 * @see        Boombox_Featured_Area_Template_Helper::get_items_count_by_template();
 */
function boombox_get_featured_area_items_predefined_counts( $template = '' ) {
	_deprecated_function( __FUNCTION__, '2.0.0', "Boombox_Template::init( 'featured-area' )->get_items_count_by_template()" );

	return Boombox_Template::init( 'featured-area' )->get_items_count_by_template( $template );
}

/**
 * Render title badge
 *
 * @since      1.0.0
 * @deprecated 2.0.0 Boombox_Template::init( 'title' )->get_title_badge()
 * @see        Boombox_Title_Template_Helper::get_title_badge();
 */
function boombox_the_title_badge() {
	_deprecated_function( __FUNCTION__, '2.0.0', "Boombox_Template::init( 'title' )->get_title_badge()" );

	echo Boombox_Template::init( 'title' )->get_title_badge();
}

/**
 * Render HTML subtitle for current post.
 *
 * @since      1.0.0
 * @version    2.0.0
 * @deprecated 2.0.0 boombox_get_post_subtitle()
 * @see        boombox_get_post_subtitle();
 */
function boombox_the_post_subtitle() {
	_deprecated_function( __FUNCTION__, '2.0.0', "boombox_get_post_subtitle" );

	echo boombox_get_post_subtitle();
}

/**
 * Render create post button in header auth box
 *
 * @param $boombox_header_settings
 *
 * @since   1.0.0
 * @removed 2.0.0
 */
function boombox_auth_box_create_post_button( $boombox_header_settings ) {
	_deprecated_function( __FUNCTION__, '2.0.0' );
}

/**
 * Return profile button
 *
 * @param array $classes
 *
 * @return string
 * @version    1.0.0
 * @deprecated 2.0.0
 */
function boombox_get_profile_button( $classes = array() ) {
	_deprecated_function( __FUNCTION__, '2.0.0' );

	return '';
}

/**
 * Render user profile icon in header auth box
 *
 * @param $boombox_header_settings
 *
 * @version    1.0.0
 * @deprecated 2.0.0
 */
function boombox_auth_box_render_profile_icon( $boombox_header_settings ) {
	_deprecated_function( __FUNCTION__, '2.0.0' );
}

/**
 * Get header settings
 * @return array
 *
 * @version    1.0.0
 * @deprecated 2.0.0
 */
function boombox_get_header_settings() {
	_deprecated_function( __FUNCTION__, '2.0.0' );

	return array();
}

/**
 * Theme Name
 *
 * @return string
 * @version    1.0.0
 * @deprecated 2.0.0 Boombox_Customizer::OPTION_NAME;
 */
function boombox_get_theme_name() {
	_deprecated_function( __FUNCTION__, '2.0.0' );

	return Boombox_Customizer::OPTION_NAME;
}

/**
 * Get default theme option values
 *
 * @return array
 * @version    1.0.0
 * @deprecated 2.0.0
 */
function boombox_get_theme_defaults() {
	_deprecated_function( __FUNCTION__, '2.0.0' );

	return array();
}

/**
 * Get customizer default values
 *
 * @return array
 * @version    1.0.0
 * @deprecated 2.0.0 Boombox_Customizer::get_default_values()
 * @see        Boombox_Customizer::get_default_values()
 */
function boombox_get_theme_customizer_default_values() {
	_deprecated_function( __FUNCTION__, '2.0.0', "Boombox_Customizer::get_instance()->get_default_values()" );

	return Boombox_Customizer::get_instance()->get_default_values();
}

/**
 * Sanitize multiple checkbox field
 *
 * @param array $values Current values
 *
 * @return array
 * @version    1.0.0
 * @deprecated 2.0.0
 */
function boombox_sanitize_multiple_checkbox_field( $values ) {
	_deprecated_function( __FUNCTION__, '2.0.0' );

	return array();
}

/**
 * Sanitize multiple select field
 *
 * @param array $values Current values
 *
 * @return array
 * @version    1.0.0
 * @deprecated 2.0.0
 */
function boombox_sanitize_multiple_select_field( $values ) {
	_deprecated_function( __FUNCTION__, '2.0.0' );

	return array();
}

/**
 * Validate positive number
 *
 * @param bool  $validity Current validity
 * @param mixed $value    Current Value
 *
 * @return bool
 * @version    1.0.0
 * @deprecated 2.0.0
 */
function check_positive_number( $validity, $value ) {
	_deprecated_function( __FUNCTION__, '2.0.0' );

	return true;
}

/**
 * Get captcha type
 * @param string $default
 *
 * @return string
 * @version    1.0.0
 * @deprecated 2.0.0
 */
function boombox_auth_captcha_type( $default = 'image' ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'boombox_get_auth_captcha_type' );

	return boombox_get_auth_captcha_type();
}

/**
 * Register Google fonts for Boombox.
 *
 * @return string Google fonts URL
 * @since 1.0.0
 * @deprecated 2.0.0 Boombox_Fonts_Helper::get_google_url()
 * @see        Boombox_Fonts_Helper::get_google_url()
 */
function boombox_fonts_url() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'boombox_get_auth_captcha_type' );

	return Boombox_Fonts_Helper::get_instance()->get_google_url();
}

/**
 * The template for displaying the trending page
 *
 * @package BoomBox_Theme
 * @since   1.0
 * @deprecated 2.0.0
 */
function boombox_get_trending_page_settings( $boombox_paged = 1 ) {
	_deprecated_function( __FUNCTION__, '2.0.0' );
}

/**
 * The template for displaying the header navigation badges
 *
 * @package BoomBox_Theme
 * @since   1.0
 * @deprecated 2.0.0
 */
function boombox_render_header_navigation_badges() {
	_deprecated_function( __FUNCTION__, '2.0.0' );
}

/**
 * Render header auth box
 *
 * @package BoomBox_Theme
 * @since   1.0
 * @deprecated 2.0.0
 */
function boombox_render_header_auth_box() {
	_deprecated_function( __FUNCTION__, '2.0.0' );
}

/**
 * Render header search box
 *
 * @package BoomBox_Theme
 * @since   1.0
 * @deprecated 2.0.0
 */
function boombox_render_header_search_box() {
	_deprecated_function( __FUNCTION__, '2.0.0' );
}

/**
 * Render post categories list
 *
 * @package BoomBox_Theme
 * @since   1.0
 * @deprecated 2.0.5
 */
function boombox_categories_list() {
	_deprecated_function( __FUNCTION__, '2.0.5', 'boombox_terms_list' );

	boombox_terms_list( array( 'category' => true ) );
}

/**
 * Render post tags list
 *
 * @package BoomBox_Theme
 * @since   1.0
 * @deprecated 2.0.5
 */
function boombox_tags_list() {
	_deprecated_function( __FUNCTION__, '2.0.5', 'boombox_terms_list' );

	boombox_terms_list( array( 'post_tag' => true, 'class' => 'bb-tags' ) );
}

/**
 * Get "NSFW" category name
 * @return string
 * @deprecated 2.1.3
 */
function boombox_get_nsfw_category_name() {
	_deprecated_function( __FUNCTION__, '2.1.3' );

	return '';
}

/**
 * Get "NSFW" category slug
 * @return string
 * @deprecated 2.1.3
 */
function boombox_get_nsfw_category_slug() {
	_deprecated_function( __FUNCTION__, '2.1.3' );

	return '';
}

/**
 * Render HTML with date information for current post.
 *
 * @param array $args
 *
 * @return string
 * @deprecated 2.5.0
 *
 * @since   1.0.0
 * @version 2.5.0
 */
function boombox_post_date( $args = array() ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'boombox_render_post_date' );
	
	$args = wp_parse_args( $args, array(
		'display' => true,
		'echo'    => true
	) );

	if( $args[ 'echo' ] ) {
		boombox_render_post_date( $args[ 'show' ] );
	} else {
		return boombox_get_post_date( $args[ 'show' ] );
	}
}

/**
 * Render views HTML
 * @param array $args Arguments
 * @since 2.5.0
 * @version 2.5.0
 * @deprecated 2.5.0
 */
function boombox_show_post_views() {
	_deprecated_function( __FUNCTION__, '2.5.0', 'boombox_get_post_views_count_html' );
	
	echo boombox_get_post_views_count_html();
}

/**
 * Render post comments HTML
 * @return string|null
 *
 * @since   1.0.0
 * @version 2.0.0
 * @deprecated 2.5.0
 */
function boombox_post_comments() {
	_deprecated_function( __FUNCTION__, '2.5.0', 'boombox_get_post_comments_count_html' );
	
	echo boombox_get_post_comments_count_html();
}

/**
 * Get|Render share count HTML for current post.
 *
 * @param bool   $html     As HTML
 * @param bool   $render   Render or return
 * @param string $location Location
 *
 * @return string
 *
 * @since   1.0.0
 * @version 2.0.0
 * @deprecated 2.5.0
 */
function boombox_post_share_count( $html = true, $render = true, $location = '' ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'boombox_get_post_share_count' );
	
	if( $render ) {
		echo boombox_get_post_share_count( array( 'html' => $html, 'location' => $location ) );
	} else {
		return boombox_get_post_share_count( array( 'html' => $html, 'location' => $location ) );
	}
}

/**
 * Render post points HTML
 *
 * @since      1.0.0
 * @version    2.0.0
 * @deprecated 2.5.0
 */
function boombox_post_points() {
	_deprecated_function( __FUNCTION__, '2.5.0', 'boombox_get_post_points_html' );
	echo boombox_get_post_points_html();
}

/**
 * Render post view / vote count HTML
 * @param int $post_id Optional. Post ID
 * @param array $args Configuration arguments
 * @since 1.0.0
 * @version 2.0.0
 * @deprecated 2.5.0
 */
function boombox_post_view_vote_count( $post_id, $args = array() ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'boombox_get_post_view_vote_count_html' );
	
	$args[ 'post_id' ] = $post_id;
	echo boombox_get_post_view_vote_count_html( $args );
}

/**
 * Render author expanded information block
 *
 * @since   1.0.0
 * @version 2.0.0
 * @deprecated 2.5.0
 */
function boombox_post_author_expanded_info( $args = array() ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'boombox_get_post_author_card' );
	
	echo boombox_get_post_author_card( $args );
}

/**
 * Render share buttons
 *
 * @see     Mashshare Plugin
 *
 * @since   1.0.0
 * @version 2.0.0
 * @deprecated 2.5.0
 */
function boombox_post_share_buttons() {
	_deprecated_function( __FUNCTION__, '2.5.0', 'boombox_get_post_share_buttons_html' );
	
	echo boombox_get_post_share_buttons_html();
}

/**
 * Render mobile share buttons
 *
 * @param bool $show_comments Include comments count
 * @param bool $show_share    Inlude share count
 * @param bool $show_points   Include points count
 *
 * @since   1.0.0
 * @version 2.0.0
 * @deprecated 2.5.0
 */
function boombox_post_share_mobile_buttons( $show_comments, $show_share, $show_points ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'boombox_get_post_share_mobile_buttons_html' );
	
	echo boombox_get_post_share_mobile_buttons_html( array(
		'comments' => (bool) $show_comments,
		'shares'   => (bool) $show_share,
		'points'   => (bool) $show_points,
	) );
}

/**
 * Render post categories list
 *
 * @since   2.0.5
 * @version 2.0.5
 * @deprecated 2.5.0
 *
 * @param array $args Arguments {
 *
 * @type bool category Show categories
 * @type bool post_tag Show post tags
 * @type string before Content before list
 * @type string after Content after list
 * @type string class Css classes for wrapper
 * }
 */
function boombox_terms_list( $args = array() ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'boombox_terms_list_html' );
	
	echo boombox_terms_list_html( $args );
}

/**
 * Check if thumbnail should be shown for multipage post
 * @return bool
 *
 * @since   1.0.0
 * @version 2.0.0
 * @deprecated 2.5.0
 */
function boombox_show_thumbnail() {
	_deprecated_function( __FUNCTION__, '2.5.0', 'boombox_show_multipage_thumbnail' );
	
	return boombox_show_multipage_thumbnail();
}

/**
 * Newsletter Form HTML
 *
 * @see     Mashshare Plugin
 *
 * @param array $args Parsing arguments
 *
 * @return string
 *
 * @since   1.0.0
 * @version 2.0.0
 * @deprecated 2.5.0
 */
function boombox_mailchimp_form( $args = array() ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'boombox_show_multipage_thumbnail' );
	
	$echo = ( isset( $args[ 'echo' ] ) && $args[ 'echo' ] );
	if( $echo ) {
		echo boombox_get_mailchimp_form_html( $args );
	} else {
		return boombox_get_mailchimp_form_html( $args );
	}
}

/**
 * Create a meta box for the post author with author and date data
 * @param array $args
 *
 * @since   1.0.0
 * @version 2.0.0
 * @deprecated 2.5.0
 */
function boombox_post_author_meta( $args = array() ) {
	_deprecated_function( __FUNCTION__, '2.5.0' );
}

/**
 * Get / Render  HTML with author information for current post.
 * @param array $args
 *
 * @since   1.0.0
 * @version 2.0.0
 * @deprecated 2.5.0
 */
function boombox_post_author( $args = array() ) {
	_deprecated_function( __FUNCTION__, '2.5.0' );
}

/**
 * Get "iconmoon" icons pack
 *
 * @return array
 * @since   1.0.0
 * @version 2.0.0
 * @deprecated 2.5.0
 */
function get_icomoon_icons_array() {
	_deprecated_function( __FUNCTION__, '2.5.1', 'boombox_get_icomoon_icons_array' );

	return boombox_get_icomoon_icons_array();
}