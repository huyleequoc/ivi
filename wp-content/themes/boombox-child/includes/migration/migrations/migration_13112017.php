<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class migration_13112017 {

	/**
	 * Get font family migration value
	 * @param string $current_value Old value
	 *
	 * @return array
	 */
	private static function get_font_family_migration_value( $current_value, $subsets = array() ) {

		$serifs = array(
			'Georgia, serif',
			'"Palatino Linotype", "Book Antiqua", Palatino, serif',
			'"Times New Roman", Times, serif'
		);

		$sans_serifs = array(
			'Arial, Helvetica',
			'"Arial Black", Gadget',
			'"Comic Sans MS", cursive',
			'Impact, Charcoal',
			'"Lucida Sans Unicode", "Lucida Grande"',
			'Tahoma, Geneva',
			'"Trebuchet MS", Helvetica',
			'Verdana, Geneva'
		);

		if( in_array( $current_value, $serifs ) ) {
			$value = array(
				'font-family' => 'Georgia,Times,"Times New Roman",serif',
				'font-style'  => 'normal',
				'font-weight' => 400,
				'variant'     => 'regular',
				'subsets'     => NULL,
			);
		} else if( in_array( $current_value, $sans_serifs ) ) {
			$value = array(
				'font-family' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
				'font-style'  => 'normal',
				'font-weight' => 400,
				'variant'     => 'regular',
				'subsets'     => NULL,
			);
		} else {
			$value = array(
				'font-family' => $current_value,
				'font-style'  => 'normal',
				'font-weight' => 400,
				'variant'     => 'regular',
				'subsets'     => $subsets,
			);
		}

		return $value;
	}

	/**
	 * Migrate pages metadata to single meta
	 * @return bool
	 */
	private static function pages_metadata_migrate_up() {
		global $wpdb;

		require_once( BOOMBOX_ADMIN_PATH . 'metaboxes' . DIRECTORY_SEPARATOR . 'class-boombox-page-metaboxes.php' );
		$pages = get_posts( array( 'post_type' => 'page', 'posts_per_page' => -1 ) );

		$default_values = array(
			'boombox_sidebar_type'        => '1-sidebar-1_3',
			'boombox_sidebar_orientation' => 'right',
			'boombox_primary_sidebar'     => 'default-sidebar',
			'boombox_secondary_sidebar'   => 'page-secondary',

			'boombox_featured_area_type'                   => 'type-1-1',
			'boombox_featured_disable_gap'                 => 0,
			'boombox_featured_hide_elements'               => array(),
			'boombox_featured_area_conditions'             => 'featured',
			'boombox_featured_area_time_range'             => 'all',
			'boombox_featured_area_category'               => array( '' ),
			'boombox_featured_area_tags'                   => '',
			'boombox_featured_area_exclude_from_main_loop' => 1,

			'boombox_listing_type'          => 'list',
			'boombox_listing_hide_elements' => array(),
			'boombox_listing_condition'     => 'recent',
			'boombox_listing_time_range'    => 'all',
			'boombox_listing_categories'    => array( '' ),
			'boombox_listing_tags'          => '',
			'boombox_pagination_type'       => 'load_more',
			'boombox_posts_per_page'        => get_option( 'posts_per_page' ),

			'boombox_hide_title_area'                          => 0,
			'boombox_title_area_style'                         => 'style1',
			'boombox_title_area_background_container'          => 'boxed',
			'boombox_title_area_text_color'                    => '',
			'boombox_title_area_background_color'              => '',
			'boombox_title_area_background_gradient_color'     => '',
			'boombox_title_area_background_gradient_direction' => 'top',
			'boombox_title_area_background_image'              => '',
			'boombox_title_area_background_image_size'         => 'cover',
			'boombox_title_area_background_image_position'     => 'center',
			'boombox_title_area_background_image_repeat'       => 'repeat-no',
			'boombox_title_area_hide_filter'                   => 1,

			'boombox_strip_configuration'  => 'inherit',
			'boombox_strip_type'           => 'slider',
			'boombox_strip_width'          => 'boxed',
			'boombox_strip_size'           => 'big',
			'boombox_strip_title_position' => 'inside',
			'boombox_strip_disable_gap'    => 1,
			'boombox_strip_conditions'     => 'recent',
			'boombox_strip_time_range'     => 'all',
			'boombox_strip_items_count'    => 18,
			'boombox_strip_category'       => array( '' ),
			'boombox_strip_tags'           => '',
		);

		/***** new meta data */
		$new_metas = array(
			'boombox_featured_area_conditions'             => boombox_get_theme_option( 'layout_page_featured_conditions' ),
			'boombox_featured_area_time_range'             => boombox_get_theme_option( 'layout_page_featured_time_range' ),
			'boombox_featured_area_category'               => boombox_get_theme_option( 'layout_page_featured_category' ),
			'boombox_featured_area_tags'                   => boombox_get_theme_option( 'layout_page_featured_tags' ),
			'boombox_listing_hide_elements'                => boombox_get_theme_option( 'layout_page_hide_elements' ),
			'boombox_strip_size'                           => boombox_get_theme_option( 'layout_page_strip_size' ),
			'boombox_strip_title_position'                 => boombox_get_theme_option( 'layout_page_strip_title_position' ),
			'boombox_strip_conditions'                     => boombox_get_theme_option( 'layout_page_strip_conditions' ),
			'boombox_strip_time_range'                     => boombox_get_theme_option( 'layout_page_strip_time_range' ),
			'boombox_strip_category'                       => boombox_get_theme_option( 'layout_page_strip_category' ),
			'boombox_strip_tags'                           => boombox_get_theme_option( 'layout_page_strip_tags' ),
			'boombox_strip_items_count'                    => boombox_get_theme_option( 'layout_page_strip_items_count' ),
			'boombox_show_featured_area'                   => 'migrate',
			'boombox_featured_area_type'                   => 'migrate',
			'boombox_sidebar_type'                         => 'migrate',
			'boombox_primary_sidebar'                      => 'migrate',
			'boombox_secondary_sidebar'                    => 'migrate',
			'boombox_strip_disable'                        => 'migrate',
			'boombox_hide_page_title'                      => 'migrate',
		);

		/***** existing meta data */
		$existing_metas = array(
			'boombox_listing_type',
			'boombox_trending_listing_type',
			'boombox_three_column_sidebar_position',
			'boombox_listing_condition',
			'boombox_listing_time_range',
			'boombox_listing_categories',
			'boombox_listing_tags',
			'boombox_pagination_type',
			'boombox_posts_per_page',
			'boombox_page_ad',
			'boombox_inject_ad_instead_post',
			'boombox_page_newsletter',
			'boombox_inject_newsletter_instead_post',
			'boombox_page_products_inject',
			'boombox_page_injected_products_count',
			'boombox_page_injected_products_position',
		);

		$delete_query_metas = array();
		$update = array();

		/***** process update */
		foreach ( $pages as $page ) {
			if ( boombox_get_post_meta( $page->ID, 'boombox_page_version' ) ) {
				continue;
			}

			foreach ( $new_metas as $meta_key => $meta_value ) {
				if ( $meta_value == 'migrate' ) {
					switch ( $meta_key ) {

						case 'boombox_show_featured_area':

							if ( ! boombox_get_post_meta( $page->ID, $meta_key ) ) {
								$update[ 'boombox_featured_area_type' ] = 'disable';
							}

							break;

						case 'boombox_featured_area_type':
							if ( ! boombox_get_post_meta( $page->ID, 'boombox_show_featured_area' ) ) {
								$value = 'disable';
							} else {
								$current_value = boombox_get_theme_option( 'layout_page_featured_type' );
								if ( $current_value == '1' ) {
									$value = 'type-1-with-newsletter';
								} else if ( $current_value == '2' ) {
									$value = 'type-1-1';
								} else if ( $current_value == '3' ) {
									$value = 'type-1-2';
								} else if ( $current_value == '4' ) {
									$value = 'type-1-1-1';
								} else {
									$value = 'type-1-1';
								}
							}

							$update[ 'boombox_featured_area_type' ] = $value;
							break;

						case 'boombox_sidebar_type':
							$template = boombox_get_post_meta( $page->ID, '_wp_page_template' );

							$update_template_to_default = false;
							$sidebar_type = '1-sidebar-1_3';
							$sidebar_orientation = 'right';

							if ( $template == 'default' ) {
								$sidebar_type = '1-sidebar-1_3';
								$sidebar_orientation = 'right';
							} else if ( $template == 'page-with-left-sidebar.php' ) {
								$sidebar_type = '1-sidebar-1_3';
								$sidebar_orientation = 'left';
								$update_template_to_default = true;
							} else if ( $template == 'page-no-sidebar.php' ) {
								$sidebar_type = 'no-sidebar';
								$sidebar_orientation = 'right';
								$update_template_to_default = true;
							} elseif( $template == 'page-trending-result.php' ) {
								$sidebar_type = '1-sidebar-1_3';
								$sidebar_orientation = 'right';
								$update_template_to_default = true;
							}

							$update[ 'boombox_sidebar_type' ] = $sidebar_type;
							$update[ 'boombox_sidebar_orientation' ] = $sidebar_orientation;

							/***** Update page template to default */
							if ( $update_template_to_default ) {
								update_post_meta( $page->ID, '_wp_page_template', 'default' );
							}

							break;

						case 'boombox_primary_sidebar':
							$update[ $meta_key ] = boombox_get_post_meta( $page->ID, 'boombox_sidebar_template' );
							break;

						case 'boombox_secondary_sidebar':
							$update[ $meta_key ] = 'page-secondary';
							break;

						case 'boombox_strip_disable':
							$value = 'none';
							if( boombox_get_post_meta( $page->ID, 'boombox_show_strip' ) ) {
								$value = 'inherit';
							}
							$update[ 'boombox_strip_configuration' ] = $value;
							break;

						case 'boombox_hide_page_title':
							$update[ 'boombox_hide_title_area' ] = boombox_get_post_meta( $page->ID, $meta_key );
							break;
					}
				} else {
					$update[ $meta_key ] = $meta_value;
				}
			}

			foreach ( $existing_metas as $meta_key ) {
				$update[ $meta_key ] = boombox_get_post_meta( $page->ID, $meta_key );
				$delete_query_metas[] = $wpdb->prepare( '%s', $meta_key );
			}

			// we need to merge populated array with default values, to handle any missed data
			$update = array_merge( $default_values, $update );

			$updated = update_post_meta( $page->ID, AIOM_Config::get_post_meta_key(), $update );
			if ( $updated ) {
				update_post_meta( $page->ID, 'boombox_page_version', Boombox_Page_Metabox::PAGE_VERSION );

				$delete_query = $wpdb->prepare(
					"DELETE FROM `{$wpdb->postmeta}` WHERE `post_id`=%d AND `meta_key` IN (" . implode( ', ', $delete_query_metas ) . ")",
					$page->ID
				);

				$wpdb->query( $delete_query );
			}
		}

		return true;
	}

	/**
	 * Get customizer migration keymap
	 * @return array
	 */
	private static function get_customizer_migration_keymap() {

		/***** Special migrations */
		$special_migrations = array(
			'design_footer_hide_pattern'                                  => '',
			'layout_post_hide_elements'                                   => '',
			'design_global_custom_css'                                    => '',
			'layout_archive_template'                                     => '',
			'layout_archive_disable_featured_area'                        => array(
				'archive_featured_area_disable',
				'home_featured_area_disable',
			),
			'layout_archive_disable_strip'                                => 'header_strip_visibility',
			'layout_post_disable_strip'                                   => 'header_strip_visibility',
			'mobile_layout_global_disable_strip'                          => 'mobile_global_enable_strip',
			'mobile_layout_global_disable_footer_strip'                   => 'mobile_global_enable_footer_strip',
			'mobile_layout_global_disable_featured_area'                  => 'mobile_global_enable_featured_area',
			'mobile_layout_global_disable_sidebar'                        => 'mobile_global_enable_sidebar',
			'disable_site_auth'                                           => 'extra_authentication_enable_site_auth',
			'disable_social_auth'                                         => 'extra_authentication_enable_social_auth',
			'settings_video_control_disable_mp4_video_on_post_listings'   => 'extras_video_control_enable_mp4_video_on_post_listings',
			'settings_video_control_disable_embed_video_on_post_listings' => 'extras_video_control_enable_embed_video_on_post_listings',
			'settings_video_control_disable_mp4_video_loop'               => 'extras_video_control_enable_mp4_video_loop',
			'settings_gif_control_disable_sharing'                        => 'extras_gif_control_enable_sharing',
			'layout_post_disable_view_track'                              => 'extras_post_ranking_system_enable_view_track',
			'settings_trending_disable'                                   => 'extras_post_ranking_system_trending_enable',
			'settings_trending_hide_badge'                                => 'extras_post_ranking_system_trending_badge',
			'settings_hot_disable'                                        => 'extras_post_ranking_system_hot_enable',
			'settings_hot_hide_badge'                                     => 'extras_post_ranking_system_hot_badge',
			'settings_popular_disable'                                    => 'extras_post_ranking_system_popular_enable',
			'settings_popular_hide_badge'                                 => 'extras_post_ranking_system_popular_badge',
			'settings_reactions_disable'                                  => 'extras_post_reaction_system_enable',
			'design_badges_hide_reactions'                                => 'extras_badges_reactions',
			'design_badges_hide_trending'                                 => 'extras_badges_trending',
			'design_badges_hide_category'                                 => 'extras_badges_category',
			'design_badges_hide_post_type_badges'                         => 'extras_badges_post_type_badges',
			'disable_buddypress_account_activation'                       => 'buddypress_account_activation',
			'design_footer_hide_footer_top'                               => 'footer_general_footer_top',
			'design_footer_hide_footer_bottom'                            => 'footer_general_footer_bottom',
			'design_footer_hide_social_icons'                             => 'footer_general_social_icons',
			'design_footer_disable_strip'                                 => 'footer_strip_enable',
			'design_disable_top_header'                                   => 'header_layout_top_header',
			'design_disable_bottom_header'                                => 'header_layout_bottom_header',
			'design_auth_position'                                        => '',
			'design_search_position'                                      => '',
			'design_social_position'                                      => '',
			'design_wpml_language_switcher_position'                      => '',
			'design_top_menu_alignment'                                   => 'header_layout_top_layer_composition',
			'design_bottom_menu_alignment'                                => 'header_layout_bottom_layer_composition',
			'design_top_header_height'                                    => 'header_layout_top_header_height',
			'design_bottom_header_height'                                 => 'header_layout_bottom_header_height',
			'design_badges_position'                                      => 'header_layout_badges_position',
			'design_global_logo_font_family'                              => 'design_logo_font_family',
			'design_global_primary_font_family'                           => 'design_primary_font_family',
			'design_global_secondary_font_family'                         => 'design_secondary_font_family',
			'design_global_post_titles_font_family'                       => 'design_post_titles_font_family'
		);

		/***** "Site Identity" section */
		$site_identity = array(
			'branding_show_tagline' => 'branding_show_tagline',
			'branding_logo'         => 'branding_logo',
			'branding_logo_width'   => 'branding_logo_width',
			'branding_logo_height'  => 'branding_logo_height',
			'branding_logo_hdpi'    => 'branding_logo_hdpi',
			'branding_logo_small'   => 'branding_logo_small',
			'footer_text'           => 'footer_general_text',
			'branding_404_image'    => 'branding_404_image',
		);

		/***** "Design" section */
		$design = array(
			'design_global_general_text_font_size'                => 'design_general_text_font_size',
			'design_global_single_post_heading_font_size'         => 'design_single_post_heading_font_size',
			'design_global_widget_heading_font_size'              => 'design_widget_heading_font_size',
			'design_global_page_wrapper_width_type'               => 'design_background_style',
			'design_global_body_background_color'                 => 'design_body_background_color',
			'design_global_body_background_image'                 => 'design_body_background_image',
			'design_badges_body_background_image_type'            => 'design_body_background_image_type',
			'design_global_body_background_link'                  => 'design_body_background_link',
			'design_global_content_background_color'              => 'design_content_background_color',
			'design_global_primary_color'                         => 'design_primary_color',
			'design_global_primary_text_color'                    => 'design_primary_text_color',
			'design_global_base_text_color'                       => 'design_base_text_color',
			'design_global_secondary_text_color'                  => 'design_secondary_text_color',
			'design_global_heading_text_color'                    => 'design_heading_text_color',
			'design_global_link_text_color'                       => 'design_link_text_color',
			'design_global_secondary_components_background_color' => 'design_secondary_components_background_color',
			'design_global_secondary_components_text_color'       => 'design_secondary_components_text_color',
			'design_global_border_color'                          => 'design_border_color',
			'design_global_border_radius'                         => 'design_border_radius',
			'design_global_inputs_buttons_border_radius'          => 'design_inputs_buttons_border_radius',
			'design_global_social_icons_border_radius'            => 'design_social_icons_border_radius',
		);

		/***** "Header->Layout" section */
		$header_layout = array(
			'design_logo_position'                   => 'header_layout_logo_position',
			'design_shadow_position'                 => 'header_layout_shadow_position',
			'design_burger_navigation_position'      => 'header_layout_more_menu_position',
			'design_header_community_text'           => 'header_layout_community_text',
			'design_sticky_header'                   => 'header_layout_sticky_header',
			'design_top_header_width'                => 'header_layout_top_header_width',
			'design_bottom_header_width'             => 'header_layout_bottom_header_width',
			'design_header_layout_button'            => 'header_layout_button',
			'design_header_button_text'              => 'header_layout_button_text',
			'design_header_button_link'              => 'header_layout_button_link',
			'design_header_button_enable_plus_icon'  => 'header_layout_button_plus_icon',
		);

		/***** "Header->Strip" section */
		$header_strip = array(
			'layout_page_strip_size'           => 'header_strip_size',
			'layout_page_strip_title_position' => 'header_strip_title_position',
			'layout_page_strip_width'          => 'header_strip_width',
			'layout_page_strip_type'           => 'header_strip_type',
			'layout_page_strip_conditions'     => 'header_strip_conditions',
			'layout_page_strip_time_range'     => 'header_strip_time_range',
			'layout_page_strip_category'       => 'header_strip_category',
			'layout_page_strip_tags'           => 'header_strip_tags',
			'layout_page_strip_items_count'    => 'header_strip_items_count',
		);

		/***** "Header->Colour & Style" section */
		$header_design = array(
			'design_header_site_title_color'             => 'header_design_site_title_color',
			'design_header_top_background_color'         => 'header_design_top_background_color',
			'design_header_top_text_color'               => 'header_design_top_text_color',
			'design_header_top_text_hover_color'         => 'header_design_top_text_hover_color',
			'design_header_top_include_in_color_mode'    => 'header_design_top_include_in_color_mode',
			'design_header_bottom_background_color'      => 'header_design_bottom_background_color',
			'design_header_bottom_text_color'            => 'header_design_bottom_text_color',
			'design_header_bottom_text_hover_color'      => 'header_design_bottom_text_hover_color',
			'design_header_bottom_include_in_color_mode' => 'header_design_bottom_include_in_color_mode',
			'design_header_button_background_color'      => 'header_design_button_background_color',
			'design_header_button_text_color'            => 'header_design_button_text_color',
			'design_pattern_position'                    => 'header_design_pattern_position',
			'design_pattern_type'                        => 'header_design_pattern_type',
		);

		/***** "Footer->General" section */
		$footer_general = array();

		/***** "Footer->Posts Strip" section */
		$footer_strip = array(
			'design_footer_strip_conditions'  => 'footer_strip_conditions',
			'design_footer_strip_time_range'  => 'footer_strip_time_range',
			'design_footer_strip_category'    => 'footer_strip_category',
			'design_footer_strip_tags'        => 'footer_strip_tags',
			'design_footer_strip_items_count' => 'footer_strip_items_count',
		);

		/***** "Footer->Design" section */
		$footer_design = array(
			'design_footer_top_background_color'    => 'footer_design_top_background_color',
			'design_footer_top_primary_color'       => 'footer_design_top_primary_color',
			'design_footer_top_primary_text_color'  => 'footer_design_top_primary_text_color',
			'design_footer_top_heading_color'       => 'footer_design_top_heading_color',
			'design_footer_top_text_color'          => 'footer_design_top_text_color',
			'design_footer_top_link_color'          => 'footer_design_top_link_color',
			'design_footer_bottom_background_color' => 'footer_design_bottom_background_color',
			'design_footer_bottom_text_color'       => 'footer_design_bottom_text_color',
			'design_footer_bottom_text_hover_color' => 'footer_design_bottom_text_hover_color',
			'design_footer_pattern_position'        => 'footer_design_pattern_position',
			'design_footer_pattern_type'            => 'footer_design_pattern_type',
		);

		/***** "Archive->Main Posts" section */
		$archive_main_posts = array(
			'layout_archive_listing_type'                   => array(
				'archive_main_posts_listing_type',
				'home_main_posts_listing_type',
			),
			'layout_archive_hide_elements'                  => array(
				'archive_main_posts_hide_elements',
				'home_main_posts_hide_elements',
			),
			'layout_archive_pagination_type'                => array(
				'archive_main_posts_pagination_type',
				'home_main_posts_pagination_type',
			),
			'layout_archive_posts_per_page'                 => array(
				'archive_main_posts_posts_per_page',
				'home_main_posts_posts_per_page',
			),
			'layout_archive_ad'                             => array(
				'archive_main_posts_inject_ad',
				'home_main_posts_inject_ad',
			),
			'layout_archive_inject_ad_instead_post'         => array(
				'archive_main_posts_injected_ad_position',
				'home_main_posts_injected_ad_position',
			),
			'layout_archive_newsletter'                     => array(
				'archive_main_posts_inject_newsletter',
				'home_main_posts_inject_newsletter',
			),
			'layout_archive_inject_newsletter_instead_post' => array(
				'archive_main_posts_injected_newsletter_position',
				'home_main_posts_injected_newsletter_position',
			),
			'layout_archive_products'                       => array(
				'archive_main_posts_inject_products',
				'home_main_posts_inject_products',
			),
			'layout_archive_products_count'                 => array(
				'archive_main_posts_injected_products_count',
				'home_main_posts_injected_products_count',
			),
			'layout_archive_products_position'              => array(
				'archive_main_posts_injected_products_position',
				'home_main_posts_injected_products_position',
			),
		);

		/***** "Archive->Featured Area" section */
		$archive_featured_area = array(
			'layout_archive_featured_type'       => array(
				'archive_featured_area_type',
				'home_featured_area_type',
			),
			'layout_archive_featured_conditions' => array(
				'archive_featured_area_conditions',
				'home_featured_area_conditions',
			),
			'layout_archive_featured_time_range' => array(
				'archive_featured_area_time_range',
				'home_featured_area_time_range',
			),
		);

		/***** "Archive->Posts Strip" section */
		$archive_strip = array(
			'layout_archive_strip_size'           => 'archive_strip_size',
			'layout_archive_strip_title_position' => 'archive_strip_title_position',
			'layout_archive_strip_width'          => 'archive_strip_width',
			'layout_archive_strip_type'           => 'archive_strip_type',
			'layout_archive_strip_conditions'     => 'archive_strip_conditions',
			'layout_archive_strip_time_range'     => 'archive_strip_time_range',
			'layout_archive_strip_category'       => 'archive_strip_category',
			'layout_archive_strip_tags'           => 'archive_strip_tags',
			'layout_archive_strip_items_count'    => 'archive_strip_items_count',
		);

		/***** "Single Post->General" section */
		$single_general = array(
			'layout_post_navigation_direction'               => 'single_post_general_navigation_direction',
			'layout_post_share_box_elements'                 => 'single_post_general_share_box_elements',
			'layout_post_enable_full_post_button_conditions' => 'single_post_general_enable_full_post_button_conditions',
			'layout_post_full_post_button_label'             => 'single_post_general_post_button_label',
		);

		/***** "Single Post->Related Posts" section */
		$single_related_posts = array(
			'layout_post_related_entries_per_page'    => 'single_post_related_posts_related_entries_per_page',
			'layout_post_related_entries_heading'     => 'single_post_related_posts_related_entries_heading',
			'layout_post_more_entries_per_page'       => 'single_post_related_posts_more_entries_per_page',
			'layout_post_more_entries_heading'        => 'single_post_related_posts_more_entries_heading',
			'layout_post_dont_miss_entries_per_page'  => 'single_post_related_posts_dont_miss_entries_per_page',
			'layout_post_dont_miss_entries_heading'   => 'single_post_related_posts_dont_miss_entries_heading',
			'layout_post_grid_sections_hide_elements' => 'single_post_related_posts_grid_sections_hide_elements',
		);

		/***** "Single Post->Posts Strip" section */
		$single_strip = array(
			'layout_post_strip_size'           => 'single_post_strip_size',
			'layout_post_strip_title_position' => 'single_post_strip_title_position',
			'layout_post_strip_width'          => 'single_post_strip_width',
			'layout_post_strip_type'           => 'single_post_strip_type',
			'layout_post_strip_conditions'     => 'single_post_strip_conditions',
			'layout_post_strip_time_range'     => 'single_post_strip_time_range',
			'layout_post_strip_category'       => 'single_post_strip_category',
			'layout_post_strip_tags'           => 'single_post_strip_tags',
			'layout_post_strip_items_count'    => 'single_post_strip_items_count',
		);

		/***** "Mobile->Global" section */
		$mobile_global = array();

		/***** "Extra->Authentication" section */
		$extra_authentication = array(
			'auth_login_popup_heading'          => 'extra_authentication_login_popup_title',
			'auth_login_popup_text'             => 'extra_authentication_login_popup_text',
			'auth_registration_custom_url'      => 'extra_authentication_registration_custom_url',
			'auth_registration_popup_heading'   => 'extra_authentication_registration_popup_title',
			'auth_registration_popup_text'      => 'extra_authentication_registration_popup_text',
			'auth_reset_password_popup_heading' => 'extra_authentication_reset_password_popup_title',
			'auth_reset_password_popup_text'    => 'extra_authentication_reset_password_popup_text',
			'auth_terms_of_use_page'            => 'extra_authentication_terms_of_use_page',
			'auth_privacy_policy_page'          => 'extra_authentication_privacy_policy_page',
			'auth_enable_login_captcha'         => 'extra_authentication_enable_login_captcha',
			'auth_enable_registration_captcha'  => 'extra_authentication_enable_registration_captcha',
			'auth_captcha_type'                 => 'extra_authentication_captcha_type',
			'auth_google_recaptcha_site_key'    => 'extra_authentication_google_recaptcha_site_key',
			'auth_google_recaptcha_secret_key'  => 'extra_authentication_google_recaptcha_secret_key',
			'facebook_app_id'                   => 'extra_authentication_facebook_app_id',
			'google_oauth_id'                   => 'extra_authentication_google_oauth_id',
			'google_api_key'                    => 'extra_authentication_google_api_key',
		);

		/***** "Extra->Video Control" section */
		$extra_video_control = array(
			'settings_video_control_mp4_video_player_controls'     => 'extras_video_control_mp4_video_player_controls',
			'settings_video_control_mp4_video_autoplay'            => 'extras_video_control_mp4_video_autoplay',
			'settings_video_control_mp4_video_sound'               => 'extras_video_control_mp4_video_sound',
			'settings_video_control_mp4_video_click_event_handler' => 'extras_video_control_mp4_video_click_event_handler',
		);

		/***** "Extra->Gif Control" section */
		$extra_gif_control = array(
			'settings_gif_control_animation_event'          => 'extras_gif_control_animation_event',
			'settings_gif_control_cloudconvert_app_key'     => 'extras_gif_control_cloudconvert_app_key',
			'settings_gif_control_storage'                  => 'extras_gif_control_storage',
			'settings_gif_control_aws_s3_access_key_id'     => 'extras_gif_control_aws_s3_access_key_id',
			'settings_gif_control_aws_s3_secret_access_key' => 'extras_gif_control_aws_s3_secret_access_key',
			'settings_gif_control_aws_s3_bucket_name'       => 'extras_gif_control_aws_s3_bucket_name',
		);

		/***** "Extra->Post Ranking System" section */
		$extra_post_ranking_system = array(
			'settings_rating_points_login_require' => 'extras_post_ranking_system_points_login_require',
			'settings_trending_conditions'         => 'extras_post_ranking_system_trending_conditions',
			'settings_trending_page'               => 'extras_post_ranking_system_trending_page',
			'settings_trending_posts_count'        => 'extras_post_ranking_system_trending_posts_count',
			'settings_trending_minimal_score'      => 'extras_post_ranking_system_trending_minimal_score',
			'settings_hot_page'                    => 'extras_post_ranking_system_hot_page',
			'settings_hot_posts_count'             => 'extras_post_ranking_system_hot_posts_count',
			'settings_hot_minimal_score'           => 'extras_post_ranking_system_hot_minimal_score',
			'settings_popular_page'                => 'extras_post_ranking_system_popular_page',
			'settings_popular_posts_count'         => 'extras_post_ranking_system_popular_posts_count',
			'settings_popular_minimal_score'       => 'extras_post_ranking_system_popular_minimal_score',
			'settings_rating_essb_fake_share_count' => 'extras_post_ranking_system_essb_fake_share_count',
			'settings_rating_fake_views_count'     => 'extras_post_ranking_system_fake_views_count',
			'settings_rating_fake_points_count'    => 'extras_post_ranking_system_fake_points_count',
			'settings_rating_views_count_scale'    => 'extras_post_ranking_system_views_count_scale',
		);

		/***** "Extra->Post Reaction System" section */
		$extra_post_reaction_system = array(
			'settings_rating_reactions_login_require'   => 'extras_post_reaction_system_login_require',
			'settings_reaction_award_minimal_score'     => 'extras_post_reaction_system_award_minimal_score',
			'settings_reactions_maximal_count_per_vote' => 'extras_post_reaction_system_maximal_count_per_vote',
		);

		/***** "Extra->Badges" section */
		$extra_badges_system = array(
			'design_badges_position_on_thumbnails'          => 'extras_badges_position_on_thumbnails',
			'design_badges_reactions_background_color'      => 'extras_badges_reactions_background_color',
			'design_badges_reactions_text_color'            => 'extras_badges_reactions_text_color',
			'design_badges_reactions_type'                  => 'extras_badges_reactions_type',
			'design_badges_trending_icon'                   => 'extras_badges_trending_icon',
			'design_badges_trending_background_color'       => 'extras_badges_trending_background_color',
			'design_badges_trending_icon_color'             => 'extras_badges_trending_icon_color',
			'design_badges_trending_text_color'             => 'extras_badges_trending_text_color',
			'design_badges_show_for_categories'             => 'extras_badges_show_for_categories',
			'design_badges_show_for_post_tags'              => 'extras_badges_show_for_post_tags',
			'design_badges_category_background_color'       => 'extras_badges_category_background_color',
			'design_badges_category_icon_color'             => 'extras_badges_category_icon_color',
			'design_badges_category_text_color'             => 'extras_badges_category_text_color',
			'design_badges_categories_for_post_type_badges' => 'extras_badges_categories_for_post_type_badges',
			'design_badges_post_tags_for_post_type_badges'  => 'extras_badges_post_tags_for_post_type_badges',
		);

		return array_merge(
			$special_migrations,
			$site_identity,
			$design,
			$header_layout,
			$header_strip,
			$header_design,
			$footer_general,
			$footer_strip,
			$footer_design,
			$archive_main_posts,
			$archive_featured_area,
			$archive_strip,
			$single_general,
			$single_related_posts,
			$single_strip,
			$mobile_global,
			$extra_authentication,
			$extra_video_control,
			$extra_gif_control,
			$extra_post_ranking_system,
			$extra_post_reaction_system,
			$extra_badges_system
		);
	}

	/**
	 * Customizer data migrate up
	 */
	private static function customizer_migrate_up() {
		if ( ! class_exists( 'Boombox_Customizer' ) ) {
			require_once( BOOMBOX_INCLUDES_PATH . 'customizer' . DIRECTORY_SEPARATOR . 'class-boombox-customizer.php' );
		}

		$new_config = array();
		$old_config = get_option( Boombox_Customizer::OPTION_NAME, array() );

		if ( ! empty( $old_config ) ) {

			$keymap = static::get_customizer_migration_keymap();

			foreach ( $keymap as $old => $new ) {

				if ( ! isset( $old_config[ $old ] ) ) {
					continue;
				}

				/***** Special cases */
				switch ( $old ) {

					case 'design_global_custom_css':
						$custom_css_post = wp_get_custom_css_post();
						$css = $custom_css_post ? $custom_css_post->post_content : '';
						$css .= $old_config[ $old ];

						wp_update_custom_css_post( $css );

						break;

					case 'design_footer_hide_pattern':
						if ( $old_config[ $old ] ) {
							$new_config[ 'footer_design_pattern_position' ] = 'none';
						}
						break;

					case 'layout_post_hide_elements':
						$hide_elements = array_flip( $old_config[ $old ] );
						$new_config[ 'single_post_general_hide_elements' ] = array();
						$new_config[ 'single_post_general_sections' ] = array();

						/***** migrate fields that should be stay in hide elements */
						$fields_to_keep = array(
							'subtitle',
							'author',
							'date',
							'categories',
							'comments_count',
							'views',
							'badges',
							'tags',
						);
						foreach ( $fields_to_keep as $field ) {
							if ( isset( $hide_elements[ $field ] ) ) {
								$new_config[ 'single_post_general_hide_elements' ][] = $field;
							}
						}
						/***** end */

						/***** migrate fields that should be separate option */
						$fields_to_separate_option = array(
							'media'               => 'single_post_general_featured_media',
							'top_sharebar'        => 'single_post_general_top_sharebar',
							'bottom_sharebar'     => 'single_post_general_bottom_sharebar',
							'next_prev_buttons'   => 'single_post_general_next_prev_buttons',
							'floating_navbar'     => 'single_post_general_floating_navbar',
							'side_navigation'     => 'single_post_general_side_navigation',
						);
						foreach ( $fields_to_separate_option as $old_key => $new_key ) {
							$value = 1;
							if ( isset( $hide_elements[ $old_key ] ) ) {
								$value = 0;
							}
							$new_config[ $new_key ] = $value;
						}
						/***** end */

						/***** migrate fields that should be sortable option */
						$fields_to_sortable = array(
							'reactions',
							'author_info',
							'comments',
							'navigation',
							'subscribe_form',
						);
						foreach ( $fields_to_sortable as $field ) {
							if ( ! ( isset( $hide_elements[ $field ] ) && $hide_elements[ $field ] ) ) {
								$new_config[ 'single_post_general_sections' ][] = $field;
							}
						}
						if ( ! ( isset( $old_config[ 'layout_post_disable_related_block' ] ) && $old_config[ 'layout_post_disable_related_block' ] ) ) {
							$new_config[ 'single_post_general_sections' ][] = 'related_posts';
						}
						if ( ! ( isset( $old_config[ 'layout_post_disable_more_block' ] ) && $old_config[ 'layout_post_disable_more_block' ] ) ) {
							$new_config[ 'single_post_general_sections' ][] = 'more_from_posts';
						}
						if ( ! ( isset( $old_config[ 'layout_post_disable_dont_miss_block' ] ) && $old_config[ 'layout_post_disable_dont_miss_block' ] ) ) {
							$new_config[ 'single_post_general_sections' ][] = 'dont_miss_posts';
						}

						break;

					case 'layout_archive_template':

						if ( $old_config[ $old ] == 'right-sidebar' ) {
							$sidebar_type = '1-sidebar-1_3';
							$sidebar_orientation = 'right';
						} else if ( $old_config[ $old ] == 'left-sidebar' ) {
							$sidebar_type = '1-sidebar-1_3';
							$sidebar_orientation = 'left';
						} else if ( $old_config[ $old ] == 'no-sidebar' ) {
							$sidebar_type = 'no-sidebar';
							$sidebar_orientation = 'right';
						} else {
							$sidebar_type = '1-sidebar-1_3';
							$sidebar_orientation = 'right';
						}

						$new_config[ 'home_main_posts_sidebar_type' ] = $sidebar_type;
						$new_config[ 'home_main_posts_sidebar_orientation' ] = $sidebar_orientation;

						$new_config[ 'archive_main_posts_sidebar_type' ] = $sidebar_type;
						$new_config[ 'archive_main_posts_sidebar_orientation' ] = $sidebar_orientation;

						break;

					case 'layout_archive_featured_type':
						if ( $old_config[ $old ] == '1' ) {
							$value = 'type-1-with-newsletter';
						} else if ( $old_config[ $old ] == '2' ) {
							$value = 'type-1-1';
						} else if ( $old_config[ $old ] == '3' ) {
							$value = 'type-1-2';
						} else if ( $old_config[ $old ] == '4' ) {
							$value = 'type-1-1-1';
						} else {
							$value = 'type-1-1';
						}

						$new_config[ 'archive_featured_area_type' ] = $value;
						$new_config[ 'home_featured_area_type' ] = $value;

						break;

					case 'layout_archive_disable_strip':
						if ( ! isset( $new_config[ $new ] ) ) {
							$new_config[ $new ] = array( 'page' );
						}

						if ( ! $old_config[ $old ] ) {
							$new_config[ $new ][] = 'archive';
							$new_config[ $new ][] = 'home';
							$new_config[ $new ] = array_unique( $new_config[ $new ] );
						}

						break;

					case 'layout_post_disable_strip':
						if ( ! isset( $new_config[ $new ] ) ) {
							$new_config[ $new ] = array( 'page' );
						}

						if ( ! $old_config[ $old ] ) {
							$new_config[ $new ][] = 'post';
							$new_config[ $new ] = array_unique( $new_config[ $new ] );
						}

						break;

					case 'layout_archive_disable_featured_area':
					case 'mobile_layout_global_disable_strip':
					case 'mobile_layout_global_disable_footer_strip':
					case 'mobile_layout_global_disable_featured_area':
					case 'mobile_layout_global_disable_sidebar':
					case 'disable_site_auth':
					case 'disable_social_auth':
					case 'settings_video_control_disable_mp4_video_on_post_listings':
					case 'settings_video_control_disable_embed_video_on_post_listings':
					case 'settings_video_control_disable_mp4_video_loop':
					case 'settings_gif_control_disable_sharing':
					case 'layout_post_disable_view_track':
					case 'settings_trending_disable':
					case 'settings_trending_hide_badge':
					case 'settings_hot_disable':
					case 'settings_hot_hide_badge':
					case 'settings_popular_disable':
					case 'settings_popular_hide_badge':
					case 'settings_reactions_disable':
					case 'design_badges_hide_reactions':
					case 'design_badges_hide_trending':
					case 'design_badges_hide_category':
					case 'design_badges_hide_post_type_badges':
					case 'disable_buddypress_account_activation':
					case 'design_footer_hide_footer_top':
					case 'design_footer_hide_footer_bottom':
					case 'design_footer_hide_social_icons':
					case 'design_footer_disable_strip':
					case 'design_disable_top_header':
					case 'design_disable_bottom_header':
						if ( is_array( $new ) ) {
							$new_config = array_merge(
								$new_config,
								array_fill_keys( $new, ! $old_config[ $old ] )
							);
						} else {
							$new_config[ $new ] = ! $old_config[ $old ];
						}

						break;

					case 'design_auth_position':
						if ( ! isset( $new_config[ 'header_layout_top_components' ] ) ) {
							$new_config[ 'header_layout_top_components' ] = array(
								'left'  => array(),
								'right' => array(),
							);
							$new_config[ 'header_layout_bottom_components' ] = array(
								'left'  => array(),
								'right' => array(),
							);
						}
						if ( $old_config[ $old ] == 'top' ) {
							$new_config[ 'header_layout_top_components' ][ 'right' ][200] = 'woocommerce-cart';
							$new_config[ 'header_layout_top_components' ][ 'right' ][300] = 'authentication';
							$new_config[ 'header_layout_top_components' ][ 'right' ][400] = 'button-compose';
						}
						else if ( $old_config[ $old ] == 'bottom' ) {
							$new_config[ 'header_layout_bottom_components' ][ 'right' ][200] = 'woocommerce-cart';
							$new_config[ 'header_layout_bottom_components' ][ 'right' ][300] = 'authentication';
							$new_config[ 'header_layout_bottom_components' ][ 'right' ][400] = 'button-compose';
						}

						break;
					case 'design_search_position':
						if ( ! isset( $new_config[ 'header_layout_top_components' ] ) ) {
							$new_config[ 'header_layout_top_components' ] = array(
								'left'  => array(),
								'right' => array(),
							);
							$new_config[ 'header_layout_bottom_components' ] = array(
								'left'  => array(),
								'right' => array(),
							);
						}
						if ( $old_config[ $old ] == 'top' ) {
							$new_config[ 'header_layout_top_components' ][ 'right' ][ 180 ] = 'search';
						}
						else if ( $old_config[ $old ] == 'bottom' ) {
							$new_config[ 'header_layout_bottom_components' ][ 'right' ][ 180 ] = 'search';
						}

						break;
					case 'design_social_position':
						if ( ! isset( $new_config[ 'header_layout_top_components' ] ) ) {
							$new_config[ 'header_layout_top_components' ] = array(
								'left'  => array(),
								'right' => array(),
							);
							$new_config[ 'header_layout_bottom_components' ] = array(
								'left'  => array(),
								'right' => array(),
							);
						}
						if ( $old_config[ $old ] == 'top' ) {
							$new_config[ 'header_layout_top_components' ][ 'right' ][ 170 ] = 'social';
						}
						else if ( $old_config[ $old ] == 'bottom' ) {
							$new_config[ 'header_layout_bottom_components' ][ 'right' ][ 170 ] = 'social';
						}

						break;
					case 'design_wpml_language_switcher_position':
						if ( ! isset( $new_config[ 'header_layout_top_components' ] ) ) {
							$new_config[ 'header_layout_top_components' ] = array(
								'left'  => array(),
								'right' => array(),
							);
							$new_config[ 'header_layout_bottom_components' ] = array(
								'left'  => array(),
								'right' => array(),
							);
						}
						if ( $old_config[ $old ] == 'top' ) {
							$new_config[ 'header_layout_top_components' ][ 'right' ][ 160 ] = 'wpml-switcher';
						}
						else if ( $old_config[ $old ] == 'bottom' ) {
							$new_config[ 'header_layout_bottom_components' ][ 'right' ][ 160 ] = 'wpml-switcher';
						}

						break;
					case 'design_top_menu_alignment':
						if ( $old_config[ $old ] == 'left' ) {
							$new_config[ $new ]= 'brand-l_menu-l';
						} else if ( $old_config[ $old ] == 'middle' ) {
							$new_config[ $new ]= 'brand-l_menu-c';
						} elseif( $old_config[ $old ] == 'right' ) {
							$new_config[ $new ]= 'brand-l_menu-r';
						}
						break;
					case 'design_bottom_menu_alignment':
						if ( $old_config[ $old ] == 'left' ) {
							$new_config[ $new ]= 'brand-l_menu-l';
						} else if ( $old_config[ $old ] == 'middle' ) {
							$new_config[ $new ]= 'brand-l_menu-c';
						} elseif( $old_config[ $old ] == 'right' ) {
							$new_config[ $new ]= 'brand-l_menu-r';
						}
						break;
						break;

					case 'design_top_header_height':
					case 'design_bottom_header_height':
						$value = $old_config[ $old ];
						if( $value == 'narrow' ) {
							$value = 'medium';
						}
						$new_config[ $new ] = $value;
						break;

					case 'design_badges_position':
						if ( ! isset( $new_config[ 'header_layout_top_components' ] ) ) {
							$new_config[ 'header_layout_top_components' ] = array(
								'left'  => array(),
								'right' => array(),
							);
							$new_config[ 'header_layout_bottom_components' ] = array(
								'left'  => array(),
								'right' => array(),
							);
						}

						if( $old_config[ $old ] == 'top' ) {
							$value = 'inside';
							$new_config[ 'header_layout_top_components' ]['right'][ 150 ] = 'badges';
						} elseif( $old_config[ $old ] == 'bottom' ) {
							$value = 'inside';
							$new_config[ 'header_layout_bottom_components' ]['right'][ 150 ] = 'badges';
						} else {
							$value = $old_config[ $old ];
						}

						$new_config[ $new ] = $value;
						break;

					case 'design_global_logo_font_family':
					case 'design_global_primary_font_family':
					case 'design_global_secondary_font_family':
					case 'design_global_post_titles_font_family':
						$subsets = array();

						if( isset( $old_config['design_global_google_font_subset'] ) && ! empty( $old_config['design_global_google_font_subset'] ) ) {
							$subsets = explode( ',', $old_config['design_global_google_font_subset'] );
						}

						$new_config[ $new ] = static::get_font_family_migration_value( $old_config[ $old ], $subsets );

						break;

					case 'design_global_page_wrapper_width_type':
						$value = $old_config[ $old ];
						if( $value == 'full_width' ) {
							$value = 'stretched';
						}

						$new_config[ $new ] = $value;
						break;

					/***** end */

					default:
						if ( is_array( $new ) ) {
							$new_config = array_merge(
								$new_config,
								array_fill_keys( $new, $old_config[ $old ] )
							);
						} else {
							$new_config[ $new ] = $old_config[ $old ];
						}
				}

			}

			// new fields with migrations
			$new_fields_with_migrations = array(
				'mobile_header_bg_color',
				'mobile_header_text_color'
			);
			foreach( $new_fields_with_migrations as $new ) {
				switch( $new ){
					case 'mobile_header_bg_color':
						$logo_position = isset( $old_config[ 'design_logo_position' ] ) ? $old_config[ 'design_logo_position' ] : 'top';
						$value = false;
						if( $logo_position == 'top' ) {
							$value = $old_config[ 'design_header_top_background_color' ];
						}
						elseif( $logo_position == 'bottom' ) {
							$value = $old_config[ 'design_header_bottom_background_color' ];
						}

						if( $value ) {
							$new_config[ $new ] = $value;
						}

						break;
					case 'mobile_header_text_color':
						$logo_position = isset( $old_config[ 'design_logo_position' ] ) ? $old_config[ 'design_logo_position' ] : 'top';
						$value = false;
						if( ( $logo_position == 'top' ) && isset( $old_config[ 'design_header_top_text_color' ] ) ) {
							$value = $old_config[ 'design_header_top_text_color' ];
						}
						elseif( ( $logo_position == 'bottom' ) && isset( $old_config[ 'design_header_bottom_text_color' ] ) ) {
							$value = $old_config[ 'design_header_bottom_text_color' ];
						}

						if( $value ) {
							$new_config[ $new ] = $value;
						}

						break;
				}
			}

			// header top layer components
			if( isset( $new_config[ 'header_layout_top_components' ] ) ) {
				ksort( $new_config[ 'header_layout_top_components' ]['left'] );
				$new_config[ 'header_layout_top_components' ]['left'] = array_values( $new_config[ 'header_layout_top_components' ]['left'] );
				ksort( $new_config[ 'header_layout_top_components' ]['right'] );
				$new_config[ 'header_layout_top_components' ]['right'] = array_values( $new_config[ 'header_layout_top_components' ]['right'] );
			}
			// header bottom layer components
			if( isset( $new_config[ 'header_layout_bottom_components' ] ) ) {
				ksort( $new_config[ 'header_layout_bottom_components' ]['left'] );
				$new_config[ 'header_layout_bottom_components' ]['left'] = array_values( $new_config[ 'header_layout_bottom_components' ]['left'] );
				ksort( $new_config[ 'header_layout_bottom_components' ]['right'] );
				$new_config[ 'header_layout_bottom_components' ]['right'] = array_values( $new_config[ 'header_layout_bottom_components' ]['right'] );
			}

			update_option( Boombox_Customizer::OPTION_NAME . '_old', $old_config );
			update_option( Boombox_Customizer::OPTION_NAME, $new_config );

		}

		return true;
	}

	/**
	 * Organize migration tasks
	 * @return false|int
	 */
	public static function up() {
		return (
			self::pages_metadata_migrate_up()
			&& self::customizer_migrate_up()
		);
	}

}