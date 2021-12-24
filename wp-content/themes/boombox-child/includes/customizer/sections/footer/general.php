<?php
/**
 * WP Customizer panel section to handle "Footer->General" section
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
 * Get "Footer->General" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_footer_general_section_id() {
	return 'boombox_footer_general';
}

/**
 * Register "Footer->General" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_footer_general_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_footer_general_section_id(),
		'args' => array(
			'title'      => __( 'General', 'boombox' ),
			'panel'      => 'boombox_footer',
			'priority'   => 20,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_footer_general_section', 10, 1 );

/**
 * Register fields for "Footer->General" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_footer_general_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_footer_general_section_id();
	$custom_fields = array(
		/***** Top Part */
		array(
			'settings' => 'footer_general_footer_top',
			'label'    => __( 'Top Part', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 20,
			'default'  => $defaults[ 'footer_general_footer_top' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Bottom Part */
		array(
			'settings' => 'footer_general_footer_bottom',
			'label'    => __( 'Bottom Part', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 30,
			'default'  => $defaults[ 'footer_general_footer_bottom' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Social Icons */
		array(
			'settings' => 'footer_general_social_icons',
			'label'    => __( 'Social Icons', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 40,
			'default'  => $defaults[ 'footer_general_social_icons' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Footer Text */
		array(
			'settings' => 'footer_general_text',
			'label'    => __( 'Footer Text', 'boombox' ),
			'section'  => $section,
			'type'     => 'text',
			'priority' => 50,
			'default'  => $defaults[ 'footer_general_text' ],
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/footer_general', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_footer_general_fields', 10, 2 );