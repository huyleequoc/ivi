<?php
/**
 * WP Customizer panel section to handle "Extras->Breadcrumb" section
 *
 * @package BoomBox_Theme
 * @since   2.1.3
 * @version 2.1.3
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get "Extras->Breadcrumb" section id
 * @return string
 *
 * @since   2.1.3
 * @version 2.1.3
 */
function boombox_customizer_get_extras_breadcrumb_section_id() {
	return 'boombox_extras_breadcrumb';
}

/**
 * Register "Extras->Breadcrumb" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.1.3
 * @version 2.1.3
 */
function boombox_customizer_register_extras_breadcrumb_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_extras_breadcrumb_section_id(),
		'args' => array(
			'title'      => __( 'Breadcrumb', 'boombox' ),
			'panel'      => 'boombox_extras',
			'priority'   => 100,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_extras_breadcrumb_section', 10, 1 );

/**
 * Register fields for "Extras->Gif Control" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.1.3
 * @version 2.1.3
 */
function boombox_customizer_register_extras_breadcrumb_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_extras_breadcrumb_section_id();
	$custom_fields = array(
		/***** "Breadcrumb" visibility */
		array(
			'settings'        => 'extras_breadcrumb_visibility',
			'label'           => __( 'Visibility', 'boombox' ),
			'section'         => $section,
			'type'            => 'multicheck',
			'priority'        => 20,
			'default'         => $defaults[ 'extras_breadcrumb_visibility' ],
			'multiple'        => 999999999,
			'choices'         => array(
				'archive' => __( 'Archive', 'boombox' ),
				'page'    => __( 'Page', 'boombox' ),
				'post'    => __( 'Single Post', 'boombox' ),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/extras_breadcrumb', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_extras_breadcrumb_fields', 10, 2 );