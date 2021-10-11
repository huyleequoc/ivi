<?php
/**
 * WP Customizer panel section to handle "Extras->Reading Time" section
 *
 * @package BoomBox_Theme
 * @since   2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get "Extras->Reading Time" section id
 * @return string
 *
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_customizer_get_extras_reading_time_section_id() {
	return 'boombox_extras_reading_time';
}

/**
 * Register "Extras->Reading Time" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_customizer_register_extras_reading_time_section( $sections ) {
	$sections[] = array(
		'id'   => boombox_customizer_get_extras_reading_time_section_id(),
		'args' => array(
			'title'      => __( 'Reading Time', 'boombox' ),
			'panel'      => 'boombox_extras',
			'priority'   => 110,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_extras_reading_time_section', 10, 1 );

/**
 * Register fields for "Extras->Reading Time" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.5.0
 * @version 2.5.0
 */
function boombox_customizer_register_extras_reading_time_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_extras_reading_time_section_id();
	$custom_fields = array(
		/***** Words Per Minute */
		array(
			'settings'        => 'extras_reading_time_words_per_minute',
			'label'           => __( 'Words Per Minute', 'boombox' ),
			'section'         => $section,
			'type'            => 'number',
			'priority'        => 20,
			'default'         => $defaults[ 'extras_reading_time_words_per_minute' ],
			'choices' => array(
				'min'  => 1,
				'step' => 1,
			),
		),
		/***** Include Images On Calculation */
		array(
			'settings' => 'extras_reading_time_include_images',
			'label'    => __( 'Include Images On Calculation', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 30,
			'default'  => $defaults[ 'extras_reading_time_include_images' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** visibility */
		array(
			'settings' => 'extras_reading_time_visibility',
			'label'    => __( 'Visibility', 'boombox' ),
			'section'  => $section,
			'type'     => 'multicheck',
			'priority' => 50,
			'default'  => $defaults['extras_reading_time_visibility'],
			'choices'  => array(
				'home'    => __( 'Home', 'boombox' ),
				'archive' => __( 'Archive', 'boombox' ),
				'page'    => __( 'Page', 'boombox' ),
				'post'    => __( 'Single Post', 'boombox' )
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/extras_reading_time', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_extras_reading_time_fields', 10, 2 );