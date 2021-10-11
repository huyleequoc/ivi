<?php
/**
 * WP Customizer panel section to handle "Archive->Header" section
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get "Archive->Header" section id
 * @return string
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_archive_header_section_id() {
	return 'boombox_archive_header';
}

/**
 * Register "Archive->Header" section
 *
 * @param array $sections Current sections
 *
 * @return array
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_archive_header_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_archive_header_section_id(),
		'args' => array(
			'title'      => __( 'Title Area', 'boombox' ),
			'panel'      => 'boombox_archive',
			'priority'   => 30,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_archive_header_section', 10, 1 );

/**
 * Register fields for "Archive->Header" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_archive_header_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_archive_header_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();
	$custom_fields = array(
		/***** Disable Title Area */
		array(
			'settings' => 'archive_header_disable',
			'label'    => __( 'Disable Title Area', 'boombox' ),
			'section'  => $section,
			'type'     => 'checkbox',
			'priority' => 20,
			'default'  => $defaults[ 'archive_header_disable' ],
		),
		/***** Style */
		array(
			'settings' => 'archive_header_style',
			'label'    => __( 'Style', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 30,
			'default'  => $defaults[ 'archive_header_style' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_template_header_style_choices(),
		),
		/***** Container Type */
		array(
			'settings' => 'archive_header_background_container',
			'label'    => __( 'Container Type', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 40,
			'multiple' => 1,
			'default'  => $defaults[ 'archive_header_background_container' ],
			'choices'  => $choices_helper->get_template_header_background_container_choices(),
		),
		/***** Container Type */
		array(
			'settings' => 'archive_header_default_background_image',
			'label'    => __( 'Default Background Image', 'boombox' ),
			'section'  => $section,
			'type'     => 'image',
			'priority' => 50,
			'default'  => $defaults[ 'archive_header_default_background_image' ],
			'choices'     => array(
				'save_as' => 'id',
			)
		),
		/***** Hide Filters */
		array(
			'settings' => 'archive_header_filters',
			'label'    => __( 'Filters', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 60,
			'default'  => $defaults[ 'archive_header_filters' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Hide Badge */
		array(
			'settings' => 'archive_header_enable_badge',
			'label'    => __( 'Badge', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 70,
			'default'  => $defaults[ 'archive_header_enable_badge' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/archive_header', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_archive_header_fields', 10, 2 );