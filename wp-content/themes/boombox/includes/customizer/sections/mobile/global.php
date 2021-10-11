<?php
/**
 * WP Customizer panel section to handle "Mobile->Global" section
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
 * Get "Mobile->Global" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_mobile_global_section_id() {
	return 'boombox_mobile_global';
}

/**
 * Register "Mobile->Global" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_mobile_global_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_mobile_global_section_id(),
		'args' => array(
			'title'      => __( 'Global', 'boombox' ),
			'panel'      => 'boombox_mobile',
			'priority'   => 20,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_mobile_global_section', 10, 1 );

/**
 * Register fields for "Mobile->Global" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_mobile_global_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_mobile_global_section_id();
	$custom_fields = array(
		/***** Strip */
		array(
			'settings' => 'mobile_global_enable_strip',
			'label'    => __( 'Strip', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 20,
			'default'  => $defaults[ 'mobile_global_enable_strip' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Footer Strip */
		array(
			'settings' => 'mobile_global_enable_footer_strip',
			'label'    => __( 'Footer Strip', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 30,
			'default'  => $defaults[ 'mobile_global_enable_footer_strip' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Featured Area */
		array(
			'settings' => 'mobile_global_enable_featured_area',
			'label'    => __( 'Featured Area', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 40,
			'default'  => $defaults[ 'mobile_global_enable_featured_area' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Sidebar */
		array(
			'settings' => 'mobile_global_enable_sidebar',
			'label'    => __( 'Sidebar', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 50,
			'default'  => $defaults[ 'mobile_global_enable_sidebar' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/mobile_global', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_mobile_global_fields', 10, 2 );