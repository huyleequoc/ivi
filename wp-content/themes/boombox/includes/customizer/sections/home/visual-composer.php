<?php
/**
 * WP Customizer panel section to handle "Home->Visual Composer Content" section
 *
 * @package BoomBox_Theme
 * @since 2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get "Home->Visual Composer Content" section id
 * @return string
 *
 * @since 2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_home_visual_composer_section_id() {
	return 'boombox_home_visual_composer';
}

/**
 * Register "Home->Visual Composer Content" section
 *
 * @param array $sections Current sections
 * @return array
 *
 * @since 2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_home_visual_composer_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_home_visual_composer_section_id(),
		'args' => array(
			'title'         => __( 'Visual Composer Content', 'boombox' ),
			'panel'         => 'boombox_home',
			'priority'      => 40,
			'capability'    => 'edit_theme_options',
		)
	);

	return $sections;
}
add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_home_visual_composer_section', 10, 1 );

/**
 * Register fields for "Home->Visual Composer Content" section
 *
 * @param array $fields     Current fields configuration
 * @param array $defaults   Array containing default values
 * @return array
 *
 * @since 2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_home_visual_composer_fields( $fields, $defaults ) {

	$section            = boombox_customizer_get_home_visual_composer_section_id();
	$custom_fields = array(
		// This Section Placeholder
		array(
			'settings'      => 'this_section_placeholder',
			'label'         => __( 'This Section Placeholder', 'boombox' ),
			'section'       => $section,
			'type'          => 'text',
			'priority'      => 20,
			'default'       => 'this_section_placeholder',
		),
		// Other fields need to go here
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/home_visual_composer', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}
add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_home_visual_composer_fields', 10, 2 );