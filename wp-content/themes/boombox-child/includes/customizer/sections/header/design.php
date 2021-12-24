<?php
/**
 * WP Customizer panel section to handle "Header->Color & Style" section
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
 * Get "Header->Color & Style" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_header_design_section_id() {
	return 'boombox_header_design';
}

/**
 * Register "Header->Color & Style" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_header_design_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_header_design_section_id(),
		'args' => array(
			'title'      => __( 'Color & Style', 'boombox' ),
			'panel'      => 'boombox_header',
			'priority'   => 30,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_header_design_section', 10, 1 );

/**
 * Register fields for "Header->Color & Style" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_header_design_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_header_design_section_id();
	$custom_fields = array(
		/***** Site Title Color */
		array(
			'settings'    => 'header_design_site_title_color',
			'label'       => __( 'Site Title Color', 'boombox' ),
			'section'     => $section,
			'type'        => 'color',
			'priority'    => 20,
			'default'     => $defaults[ 'header_design_site_title_color' ],
			'choices'     => array(
				'alpha' => false,
			),
		),
		/***** Top Background Color */
		array(
			'settings'    => 'header_design_top_background_color',
			'label'       => __( 'Top Background Color', 'boombox' ),
			'section'     => $section,
			'type'        => 'color',
			'priority'    => 30,
			'default'     => $defaults[ 'header_design_top_background_color' ],
			'choices'     => array(
				'alpha' => false,
			),
		),
		/***** Top Gradient Color */
		array(
			'settings'    => 'header_design_top_gradient_color',
			'label'       => __( 'Top Gradient Color', 'boombox' ),
			'section'     => $section,
			'type'        => 'color',
			'priority'    => 40,
			'default'     => $defaults[ 'header_design_top_gradient_color' ],
			'choices'     => array(
				'alpha' => false,
			),
		),
		/***** Top Text Color */
		array(
			'settings'    => 'header_design_top_text_color',
			'label'       => __( 'Top Text Color', 'boombox' ),
			'section'     => $section,
			'type'        => 'color',
			'priority'    => 50,
			'default'     => $defaults[ 'header_design_top_text_color' ],
			'choices'     => array(
				'alpha' => false,
			),
		),
		/***** Top Text Hover Color */
		array(
			'settings'    => 'header_design_top_text_hover_color',
			'label'       => __( 'Top Text Hover Color', 'boombox' ),
			'section'     => $section,
			'type'        => 'color',
			'priority'    => 60,
			'default'     => $defaults[ 'header_design_top_text_hover_color' ],
			'choices'     => array(
				'alpha' => false,
			),
		),
		/***** Top Layer in dark/light mode */
		array(
			'settings'    => 'header_design_top_include_in_color_mode',
			'label'       => __( 'Include Top Layer in dark/light mode', 'boombox' ),
			'section'     => $section,
			'type'        => 'switch',
			'priority'    => 70,
			'default'     => $defaults[ 'header_design_top_include_in_color_mode' ],
			'choices'     => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Bottom Background Color */
		array(
			'settings'    => 'header_design_bottom_background_color',
			'label'       => __( 'Bottom Background Color', 'boombox' ),
			'section'     => $section,
			'type'        => 'color',
			'priority'    => 80,
			'default'     => $defaults[ 'header_design_bottom_background_color' ],
			'choices'     => array(
				'alpha' => false,
			),
		),
		/***** Bottom Gradient Color */
		array(
			'settings'    => 'header_design_bottom_gradient_color',
			'label'       => __( 'Bottom Gradient Color', 'boombox' ),
			'section'     => $section,
			'type'        => 'color',
			'priority'    => 90,
			'default'     => $defaults[ 'header_design_bottom_gradient_color' ],
			'choices'     => array(
				'alpha' => false,
			),
		),
		/***** Bottom Text Color */
		array(
			'settings'    => 'header_design_bottom_text_color',
			'label'       => __( 'Bottom Text Color', 'boombox' ),
			'section'     => $section,
			'type'        => 'color',
			'priority'    => 100,
			'default'     => $defaults[ 'header_design_bottom_text_color' ],
			'choices'     => array(
				'alpha' => false,
			),
		),
		/***** Bottom Text Hover Color */
		array(
			'settings'    => 'header_design_bottom_text_hover_color',
			'label'       => __( 'Bottom Text Hover Color', 'boombox' ),
			'section'     => $section,
			'type'        => 'color',
			'priority'    => 110,
			'default'     => $defaults[ 'header_design_bottom_text_hover_color' ],
			'choices'     => array(
				'alpha' => false,
			),
		),
		/***** Bottom Layer in dark/light mode */
		array(
			'settings'    => 'header_design_bottom_include_in_color_mode',
			'label'       => __( 'Include Bottom Layer in dark/light mode', 'boombox' ),
			'section'     => $section,
			'type'        => 'switch',
			'priority'    => 120,
			'default'     => $defaults[ 'header_design_bottom_include_in_color_mode' ],
			'choices'     => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Button Background Color */
		array(
			'settings'    => 'header_design_button_background_color',
			'label'       => __( 'Button Background Color', 'boombox' ),
			'section'     => $section,
			'type'        => 'color',
			'priority'    => 130,
			'default'     => $defaults[ 'header_design_button_background_color' ],
			'choices'     => array(
				'alpha' => false,
			),
		),
		/***** Button Text Color */
		array(
			'settings'    => 'header_design_button_text_color',
			'label'       => __( 'Button Text Color', 'boombox' ),
			'section'     => $section,
			'type'        => 'color',
			'priority'    => 140,
			'default'     => $defaults[ 'header_design_button_text_color' ],
			'choices'     => array(
				'alpha' => false,
			),
		),
		/***** Shadow Position */
		array(
			'settings'    => 'header_layout_shadow_position',
			'label'       => __( 'Shadow Position', 'boombox' ),
			'section'     => $section,
			'type'        => 'select',
			'priority'    => 150,
			'default'     => $defaults[ 'header_layout_shadow_position' ],
			'multiple'    => 1,
			'choices'     => array(
				'top'    => __( 'Top Layer', 'boombox' ),
				'bottom' => __( 'Bottom Layer', 'boombox' ),
				'none'   => __( 'None', 'boombox' ),
			),
		),
		/***** Pattern Position */
		array(
			'settings'    => 'header_design_pattern_position',
			'label'       => __( 'Pattern Position', 'boombox' ),
			'section'     => $section,
			'type'        => 'select',
			'priority'    => 160,
			'default'     => $defaults[ 'header_design_pattern_position' ],
			'multiple'    => 1,
			'choices'     => array(
				'top'    => __( 'Top Layer', 'boombox' ),
				'bottom' => __( 'Bottom Layer', 'boombox' ),
				'none'   => __( 'None', 'boombox' ),
			),
		),
		/***** Pattern Type */
		array(
			'settings'    => 'header_design_pattern_type',
			'label'       => __( 'Pattern Type', 'boombox' ),
			'section'     => $section,
			'type'        => 'select',
			'priority'    => 170,
			'default'     => $defaults[ 'header_design_pattern_type' ],
			'multiple'    => 1,
			'choices'     => array(
				'rags-header.svg'   => __( 'Rags', 'boombox' ),
				'clouds-header.svg' => __( 'Clouds', 'boombox' ),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/header_design', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_header_design_fields', 10, 2 );