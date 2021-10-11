<?php
/**
 * WP Customizer panel section to handle "Design" section
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
 * Get "Design" section id
 *
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_design_section_id() {
	return 'boombox_design';
}

/**
 * Register "Design" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_design_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_design_section_id(),
		'args' => array(
			'title'      => __( 'Design', 'boombox' ),
			'priority'   => 25,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_design_section', 10, 1 );

/**
 * Register fields for "Design" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_design_fields( $fields, $defaults ) {

	$section       = boombox_customizer_get_design_section_id();
	$custom_fields = array(
		/***** Logo Font Family */
		array(
			'settings' => 'design_logo_font_family',
			'label'    => __( 'Logo Font Family', 'boombox' ),
			'section'  => $section,
			'type'     => 'typography',
			'priority' => 20,
			'default'  => $defaults['design_logo_font_family'],
		),
		/***** Primary Font Family */
		array(
			'settings' => 'design_primary_font_family',
			'label'    => __( 'Primary Font Family', 'boombox' ),
			'section'  => $section,
			'type'     => 'typography',
			'priority' => 30,
			'default'  => $defaults['design_primary_font_family'],
		),
		/***** Secondary Font Family */
		array(
			'settings' => 'design_secondary_font_family',
			'label'    => __( 'Secondary Font Family', 'boombox' ),
			'section'  => $section,
			'type'     => 'typography',
			'priority' => 40,
			'default'  => $defaults['design_secondary_font_family'],
		),
		/***** Post Titles Font Family */
		array(
			'settings' => 'design_post_titles_font_family',
			'label'    => __( 'Post Titles Font Family', 'boombox' ),
			'section'  => $section,
			'type'     => 'typography',
			'priority' => 50,
			'default'  => $defaults['design_post_titles_font_family'],
		),
		/***** Texts Font Size */
		array(
			'settings' => 'design_general_text_font_size',
			'label'    => __( 'General Text Font Size (px)', 'boombox' ),
			'section'  => $section,
			'type'     => 'number',
			'priority' => 60,
			'default'  => $defaults['design_general_text_font_size'],
			'choices'  => array(
				'min'  => 0,
				'step' => 1,
			),
		),
		/***** Single Posts Headings Font Size */
		array(
			'settings' => 'design_single_post_heading_font_size',
			'label'    => __( 'Single Post Heading Font Size (px)', 'boombox' ),
			'section'  => $section,
			'type'     => 'number',
			'priority' => 70,
			'default'  => $defaults['design_single_post_heading_font_size'],
			'choices'  => array(
				'min'  => 0,
				'step' => 1,
			),
		),
		/***** Widgets Titles Font Size */
		array(
			'settings' => 'design_widget_heading_font_size',
			'label'    => __( 'Widget Heading Font Size (px)', 'boombox' ),
			'section'  => $section,
			'type'     => 'number',
			'priority' => 80,
			'default'  => $defaults['design_widget_heading_font_size'],
			'choices'  => array(
				'min'  => 0,
				'step' => 1,
			),
		),
		/***** Color Mode */
		array(
			'settings'    => 'design_color_mode',
			'label'       => __( 'Color Mode', 'boombox' ),
			'description' => __( 'Is your site in light colors or in dark?', 'boombox' ),
			'section'     => $section,
			'type'        => 'radio-buttonset',
			'priority'    => 90,
			'default'     => $defaults['design_color_mode'],
			'choices'     => array(
				'light' => __( 'Light', 'boombox' ),
				'dark'  => __( 'Dark', 'boombox' ),
			),
		),
		/***** Background Style */
		array(
			'settings' => 'design_background_style',
			'label'    => __( 'Background Style', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 100,
			'multiple' => 1,
			'default'  => $defaults['design_background_style'],
			'choices'  => array(
				'stretched'      => __( 'Stretched', 'boombox' ),
				'boxed'          => __( 'Boxed', 'boombox' ),
				'cards'          => __( 'Flat Cards', 'boombox' ),
				'material_cards' => __( 'Material Cards', 'boombox' )
			),
		),
		/***** Body Background Color */
		array(
			'settings' => 'design_body_background_color',
			'label'    => __( 'Body Background Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 110,
			'default'  => $defaults['design_body_background_color'],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Body Background Image */
		array(
			'settings' => 'design_body_background_image',
			'label'    => __( 'Body Background Image', 'boombox' ),
			'section'  => $section,
			'type'     => 'image',
			'priority' => 120,
			'default'  => $defaults['design_body_background_image'],
		),
		/***** Body Background Image Type */
		array(
			'settings' => 'design_body_background_image_type',
			'label'    => __( 'Body Background Image Type', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 130,
			'multiple' => 1,
			'default'  => $defaults['design_body_background_image_type'],
			'choices'  => array(
				'cover'  => __( 'Cover', 'boombox' ),
				'repeat' => __( 'Repeat', 'boombox' ),
			),
		),
		/***** Body Background Link */
		array(
			'settings' => 'design_body_background_link',
			'label'    => __( 'Body Background Link', 'boombox' ),
			'section'  => $section,
			'type'     => 'text',
			'priority' => 140,
			'default'  => $defaults['design_body_background_link'],
		),
		/***** Content Background Color */
		array(
			'settings' => 'design_content_background_color',
			'label'    => __( 'Content Background Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 150,
			'default'  => $defaults['design_content_background_color'],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Primary Color */
		array(
			'settings' => 'design_primary_color',
			'label'    => __( 'Primary Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 160,
			'default'  => $defaults['design_primary_color'],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Primary Text Color */
		array(
			'settings' => 'design_primary_text_color',
			'label'    => __( 'Primary Text Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 170,
			'default'  => $defaults['design_primary_text_color'],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Base Text Color */
		array(
			'settings' => 'design_base_text_color',
			'label'    => __( 'Base Text Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 180,
			'default'  => $defaults['design_base_text_color'],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Secondary Text Color */
		array(
			'settings' => 'design_secondary_text_color',
			'label'    => __( 'Secondary Text Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 190,
			'default'  => $defaults['design_secondary_text_color'],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Heading Text Color */
		array(
			'settings' => 'design_heading_text_color',
			'label'    => __( 'Heading Text Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 200,
			'default'  => $defaults['design_heading_text_color'],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Link Text Color */
		array(
			'settings' => 'design_link_text_color',
			'label'    => __( 'Link Text Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 210,
			'default'  => $defaults['design_link_text_color'],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Secondary Components Background Color */
		array(
			'settings' => 'design_secondary_components_background_color',
			'label'    => __( 'Secondary Elements Background Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 220,
			'default'  => $defaults['design_secondary_components_background_color'],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Secondary Components Text Color */
		array(
			'settings' => 'design_secondary_components_text_color',
			'label'    => __( 'Secondary Elements Text Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 230,
			'default'  => $defaults['design_secondary_components_text_color'],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Global Border Color */
		array(
			'settings' => 'design_border_color',
			'label'    => __( 'Global Border Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 240,
			'default'  => $defaults['design_border_color'],
			'choices'  => array(
				'alpha' => true,
			),
		),
		/***** Global Border Radius */
		array(
			'settings'    => 'design_border_radius',
			'label'       => __( 'Global Border Radius (px)', 'boombox' ),
			'description' => __( 'A number between 0 and 100', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 250,
			'default'     => $defaults['design_border_radius'],
			'choices'     => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
		),
		/***** Inputs/Buttons Border Radius */
		array(
			'settings'    => 'design_inputs_buttons_border_radius',
			'label'       => __( 'Inputs/Buttons Border Radius (px)', 'boombox' ),
			'description' => __( 'A number between 0 and 100', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 260,
			'default'     => $defaults['design_inputs_buttons_border_radius'],
			'choices'     => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
		),
		/***** Social Icons Border Radius */
		array(
			'settings'    => 'design_social_icons_border_radius',
			'label'       => __( 'Social Icons Border Radius (px)', 'boombox' ),
			'description' => __( 'A number between 0 and 100', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 270,
			'default'     => $defaults['design_social_icons_border_radius'],
			'choices'     => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/design', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_design_fields', 10, 2 );