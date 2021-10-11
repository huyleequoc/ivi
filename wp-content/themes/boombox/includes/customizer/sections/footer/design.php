<?php
/**
 * WP Customizer panel section to handle "Footer->Design" section
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
 * Get "Footer->Design" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_footer_design_section_id() {
	return 'boombox_footer_design';
}

/**
 * Register "Footer->Design" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_footer_design_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_footer_design_section_id(),
		'args' => array(
			'title'      => __( 'Color & Style', 'boombox' ),
			'panel'      => 'boombox_footer',
			'priority'   => 40,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_footer_design_section', 10, 1 );

/**
 * Register fields for "Footer->Design" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_footer_design_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_footer_design_section_id();
	$custom_fields = array(
		/***** Top Background Color */
		array(
			'settings' => 'footer_design_top_background_color',
			'label'    => __( 'Top Background Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 20,
			'default'  => $defaults[ 'footer_design_top_background_color' ],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Top Primary Color */
		array(
			'settings' => 'footer_design_top_primary_color',
			'label'    => __( 'Top Primary Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 30,
			'default'  => $defaults[ 'footer_design_top_primary_color' ],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Top Primary Text Color */
		array(
			'settings' => 'footer_design_top_primary_text_color',
			'label'    => __( 'Top Primary Text Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 40,
			'default'  => $defaults[ 'footer_design_top_primary_text_color' ],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Top Heading Color */
		array(
			'settings' => 'footer_design_top_heading_color',
			'label'    => __( 'Top Heading Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 50,
			'default'  => $defaults[ 'footer_design_top_heading_color' ],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Top Text Color */
		array(
			'settings' => 'footer_design_top_text_color',
			'label'    => __( 'Top Text Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 60,
			'default'  => $defaults[ 'footer_design_top_text_color' ],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Top Link Color */
		array(
			'settings' => 'footer_design_top_link_color',
			'label'    => __( 'Top Link Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 70,
			'default'  => $defaults[ 'footer_design_top_link_color' ],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Bottom Background Color */
		array(
			'settings' => 'footer_design_bottom_background_color',
			'label'    => __( 'Bottom Background Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 80,
			'default'  => $defaults[ 'footer_design_bottom_background_color' ],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Bottom Text Color */
		array(
			'settings' => 'footer_design_bottom_text_color',
			'label'    => __( 'Bottom Text Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 90,
			'default'  => $defaults[ 'footer_design_bottom_text_color' ],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Bottom Text Hover Color */
		array(
			'settings' => 'footer_design_bottom_text_hover_color',
			'label'    => __( 'Bottom Text Hover Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 100,
			'default'  => $defaults[ 'footer_design_bottom_text_hover_color' ],
			'choices'  => array(
				'alpha' => false,
			),
		),
		/***** Pattern Position */
		array(
			'settings' => 'footer_design_pattern_position',
			'label'    => __( 'Pattern Position', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 110,
			'default'  => $defaults[ 'footer_design_pattern_position' ],
			'multiple' => 1,
			'choices'  => array(
				'top'    => __( 'Top Footer', 'boombox' ),
				'bottom' => __( 'Bottom Footer', 'boombox' ),
				'none'   => __( 'None', 'boombox' ),
			),
		),
		/***** Pattern Type */
		array(
			'settings' => 'footer_design_pattern_type',
			'label'    => __( 'Pattern Type', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 120,
			'default'  => $defaults[ 'footer_design_pattern_type' ],
			'multiple' => 1,
			'choices'  => array(
				'rags-footer.svg' => __( 'Rags', 'boombox' ),
//				'clouds-footer.svg'    	=> __( 'Clouds', 'boombox' ),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/footer_design', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_footer_design_fields', 10, 2 );