<?php
/**
 * WP Customizer panel section to handle "Header->Featured Labels" section
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
 * Get "Header->Featured Labels" section id
 * Get "Header->Featured Labels" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_header_featured_labels_section_id() {
	return 'boombox_header_featured_labels';
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
function boombox_customizer_register_header_featured_labels_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_header_featured_labels_section_id(),
		'args' => array(
			'title'      => __( 'Featured Labels', 'boombox' ),
			'panel'      => 'boombox_header',
			'priority'   => 40,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_header_featured_labels_section', 10, 1 );

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
function boombox_customizer_register_header_featured_labels_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_header_featured_labels_section_id();
	$custom_fields = array(
		/***** Visibility */
		array(
			'settings' => 'header_featured_labels_visibility',
			'label'    => __( 'Visibility', 'boombox' ),
			'section'  => $section,
			'type'     => 'multicheck',
			'priority' => 20,
			'default'  => $defaults[ 'header_featured_labels_visibility' ],
			'choices'  => array(
				'home'        => __( 'Home', 'boombox' ),
				'archive'     => __( 'Archive', 'boombox' ),
				'single_post' => __( 'Single Post', 'boombox' ),
				'page'        => __( 'Page', 'boombox' ),
			),
		),
		/***** Background Color */
		array(
			'settings' => 'header_featured_labels_background_color',
			'label'    => __( 'Background Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 30,
			'default'  => $defaults[ 'header_featured_labels_background_color' ],
			'choices'  => array(
				'alpha' => false
			),
		),
		/***** Text Color */
		array(
			'settings' => 'header_featured_labels_text_color',
			'label'    => __( 'Text Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 40,
			'default'  => $defaults[ 'header_featured_labels_text_color' ],
			'choices'  => array(
				'alpha' => false
			),
		),
		/***** Border Radius */
		array(
			'settings'    => 'header_featured_labels_border_radius',
			'label'       => __( 'Border Radius (px)', 'boombox' ),
			'description' => __( 'A number between 0 and 100', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 50,
			'default'     => $defaults[ 'header_featured_labels_border_radius' ],
			'choices'     => array(
				'min' => 0,
				'max' => 100,
				'step' => 1
			),
		),
		/***** Hide Separator Line */
		array(
			'settings'    => 'header_featured_labels_disable_separator',
			'label'       => __( 'Hide Separator Line', 'boombox' ),
			'section'     => $section,
			'type'        => 'checkbox',
			'priority'    => 60,
			'default'     => $defaults[ 'header_featured_labels_disable_separator' ],
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/featured_labels', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_header_featured_labels_fields', 10, 2 );