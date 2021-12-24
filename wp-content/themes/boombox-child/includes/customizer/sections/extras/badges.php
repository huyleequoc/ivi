<?php
/**
 * WP Customizer panel section to handle "Extras->Badges" section
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get "Extras->Badges" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_extras_badges_system_section_id() {
	return 'boombox_extras_badges';
}

/**
 * Register "Extras->Badges" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_extras_badges_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_extras_badges_system_section_id(),
		'args' => array(
			'title'      => __( 'Badges', 'boombox' ),
			'panel'      => 'boombox_extras',
			'priority'   => 80,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_extras_badges_section', 10, 1 );

/**
 * Register fields for "Extras->Badges" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_extras_badges_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_extras_badges_system_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();
	$category_choices = $choices_helper->get_categories();
	$custom_fields = array(
		/***** Badges Positions On Post Thumbnail */
		array(
			'settings' => 'extras_badges_position_on_thumbnails',
			'label'    => __( 'Badges Positions On Post Thumbnail', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 20,
			'default'  => $defaults[ 'extras_badges_position_on_thumbnails' ],
			'multiple' => 1,
			'choices'  => array(
				'outside-left'  => __( 'Outside Left', 'boombox' ),
				'outside-right' => __( 'Outside Right', 'boombox' ),
				'inside-left'   => __( 'Inside Left', 'boombox' ),
				'inside-right'  => __( 'Inside Right', 'boombox' ),
			),
		),
		/***** "Reactions Badges" Heading */
		array(
			'settings' => 'extras_badges_reactions_badges_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 30,
			'default'  => sprintf( '<h2>%s</h2><hr/>', __( 'Reactions Badges', 'boombox' ) ),
		),
		/***** Reactions Badges */
		array(
			'settings' => 'extras_badges_reactions',
			'label'    => __( 'Reactions Badges', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 30,
			'default'  => $defaults[ 'extras_badges_reactions' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Reactions Background Color */
		array(
			'settings'        => 'extras_badges_reactions_background_color',
			'label'           => __( 'Reactions Background Color', 'boombox' ),
			'section'         => $section,
			'type'            => 'color',
			'priority'        => 40,
			'default'         => $defaults[ 'extras_badges_reactions_background_color' ],
			'choices'         => array(
				'alpha' => false,
			),
			'active_callback' => array(
				array(
					'setting'  => 'extras_badges_reactions',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Reactions Text Color */
		array(
			'settings'        => 'extras_badges_reactions_text_color',
			'label'           => __( 'Reactions Text Color', 'boombox' ),
			'section'         => $section,
			'type'            => 'color',
			'priority'        => 50,
			'default'         => $defaults[ 'extras_badges_reactions_text_color' ],
			'choices'         => array(
				'alpha' => false,
			),
			'active_callback' => array(
				array(
					'setting'  => 'extras_badges_reactions',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Reactions Type */
		array(
			'settings'        => 'extras_badges_reactions_type',
			'label'           => __( 'Reactions Type', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 60,
			'default'         => $defaults[ 'extras_badges_reactions_type' ],
			'multiple'        => 1,
			'choices'         => array(
				'face'       => __( 'Face', 'boombox' ),
				'text'       => __( 'Text', 'boombox' ),
				'face-text'  => __( 'Face-Text', 'boombox' ),
				'text-angle' => __( 'Text Angle', 'boombox' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'extras_badges_reactions',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** "Post Ranking System" Badges Heading */
		array(
			'settings' => 'extras_badges_trending_badges_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 70,
			'default'  => sprintf( '<h2>%s</h2><hr/>', __( 'Post Ranking System Badges', 'boombox' ) ),
		),
		/***** Post Ranking System Badges */
		array(
			'settings' => 'extras_badges_trending',
			'label'    => __( 'Post Ranking System Badges', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 70,
			'default'  => $defaults[ 'extras_badges_trending' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Post Ranking System Background Color */
		array(
			'settings'        => 'extras_badges_trending_background_color',
			'label'           => __( 'Post Ranking System Background Color', 'boombox' ),
			'section'         => $section,
			'type'            => 'color',
			'priority'        => 80,
			'default'         => $defaults[ 'extras_badges_trending_background_color' ],
			'multiple'        => 1,
			'choices'         => array(
				'alpha' => false,
			),
			'active_callback' => array(
				array(
					'setting'  => 'extras_badges_trending',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Post Ranking System Icon Color */
		array(
			'settings'        => 'extras_badges_trending_icon_color',
			'label'           => __( 'Post Ranking System Icon Color', 'boombox' ),
			'section'         => $section,
			'type'            => 'color',
			'priority'        => 90,
			'default'         => $defaults[ 'extras_badges_trending_icon_color' ],
			'multiple'        => 1,
			'choices'         => array(
				'alpha' => false,
			),
			'active_callback' => array(
				array(
					'setting'  => 'extras_badges_trending',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Post Ranking System Text Color */
		array(
			'settings'        => 'extras_badges_trending_text_color',
			'label'           => __( 'Post Ranking System Text Color', 'boombox' ),
			'section'         => $section,
			'type'            => 'color',
			'priority'        => 100,
			'default'         => $defaults[ 'extras_badges_trending_text_color' ],
			'multiple'        => 1,
			'choices'         => array(
				'alpha' => false,
			),
			'active_callback' => array(
				array(
					'setting'  => 'extras_badges_trending',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Trending Icon */
		array(
			'settings'        => 'extras_badges_trending_icon',
			'label'           => __( 'Trending Icon', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 110,
			'default'         => $defaults[ 'extras_badges_trending_icon' ],
			'multiple'        => 1,
			'choices'         => array(
				'trending'  => __( 'Trending 1', 'boombox' ),
				'trending2' => __( 'Trending 2', 'boombox' ),
				'trending3' => __( 'Trending 3', 'boombox' ),
				'trending5' => __( 'Trending V2', 'boombox' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'extras_badges_trending',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Category & Tag Badges Heading */
		array(
			'settings' => 'extras_badges_category_badges_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 120,
			'default'  => sprintf( '<h2>%s</h2><hr/>', __( 'Category & Tag Badges', 'boombox' ) ),
		),
		/***** Category & Tag Badges */
		array(
			'settings' => 'extras_badges_category',
			'label'    => __( 'Category & Tag Badges', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 120,
			'default'  => $defaults[ 'extras_badges_category' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Show Badges For Categories */
		array(
			'settings'        => 'extras_badges_show_for_categories',
			'label'           => __( 'Show Badges For Categories', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 130,
			'default'         => $defaults[ 'extras_badges_show_for_categories' ],
			'multiple'        => 999999999,
			'choices'         => $category_choices,
			'active_callback' => array(
				array(
					'setting'  => 'extras_badges_category',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Show Badges For Tags */
		array(
			'settings'        => 'extras_badges_show_for_post_tags',
			'label'           => __( 'Show Badges For Tags', 'boombox' ),
			'description'     => __( 'Comma separated list of tags slugs', 'boombox' ),
			'section'         => $section,
			'type'            => 'textarea',
			'priority'        => 140,
			'default'         => $defaults[ 'extras_badges_show_for_post_tags' ],
			'active_callback' => array(
				array(
					'setting'  => 'extras_badges_category',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Category & Tag Background Color */
		array(
			'settings'        => 'extras_badges_category_background_color',
			'label'           => __( 'Category & Tag Badges Background Color', 'boombox' ),
			'section'         => $section,
			'type'            => 'color',
			'priority'        => 150,
			'default'         => $defaults[ 'extras_badges_category_background_color' ],
			'choices'         => array(
				'alpha' => false,
			),
			'active_callback' => array(
				array(
					'setting'  => 'extras_badges_category',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Category & Tag Icon Color */
		array(
			'settings'        => 'extras_badges_category_icon_color',
			'label'           => __( 'Category & Tag Icon Color', 'boombox' ),
			'section'         => $section,
			'type'            => 'color',
			'priority'        => 160,
			'default'         => $defaults[ 'extras_badges_category_icon_color' ],
			'choices'         => array(
				'alpha' => false,
			),
			'active_callback' => array(
				array(
					'setting'  => 'extras_badges_category',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Category & Tag Text Color */
		array(
			'settings'        => 'extras_badges_category_text_color',
			'label'           => __( 'Category & Tag Text Color', 'boombox' ),
			'section'         => $section,
			'type'            => 'color',
			'priority'        => 170,
			'default'         => $defaults[ 'extras_badges_category_text_color' ],
			'choices'         => array(
				'alpha' => false,
			),
			'active_callback' => array(
				array(
					'setting'  => 'extras_badges_category',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** "Post Formats Badges" Heading */
		array(
			'settings' => 'extras_badges_post_type_format_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 180,
			'default'  => sprintf( '<h2>%s</h2><hr/>', __( 'Post Formats', 'boombox' ) ),
		),
		/***** Post Formats Badges */
		array(
			'settings' => 'extras_badges_post_type_badges',
			'label'    => __( 'Post Format Icons', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 180,
			'default'  => $defaults[ 'extras_badges_post_type_badges' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		array(
			'settings' => 'extras_badges_post_type_badges_on_strip',
			'label'    => __( 'Post Format Icons On Strip', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 190,
			'default'  => $defaults[ 'extras_badges_post_type_badges_on_strip' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Show Post Formats Icons For Categories */
		array(
			'settings'        => 'extras_badges_categories_for_post_type_badges',
			'label'           => __( 'Show Icons For Categories', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 200,
			'default'         => $defaults[ 'extras_badges_categories_for_post_type_badges' ],
			'multiple'        => 999999999,
			'choices'         => $category_choices,
			'active_callback' => array(
				array(
					'setting'  => 'extras_badges_post_type_badges',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Show Post Formats Icons For Tags */
		array(
			'settings'        => 'extras_badges_post_tags_for_post_type_badges',
			'label'           => __( 'Show Icons For Tags', 'boombox' ),
			'description'     => __( 'Comma separated list of tags slugs', 'boombox' ),
			'section'         => $section,
			'type'            => 'textarea',
			'priority'        => 210,
			'default'         => $defaults[ 'extras_badges_post_tags_for_post_type_badges' ],
			'active_callback' => array(
				array(
					'setting'  => 'extras_badges_post_type_badges',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/extras_badges', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_extras_badges_fields', 10, 2 );