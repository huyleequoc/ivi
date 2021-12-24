<?php
/**
 * WP Customizer panel section to handle "Header->Typography" section
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
 * Get "Header->Typography" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_header_typography_section_id() {
	return 'boombox_header_typography';
}

/**
 * Register "Header->Typography" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_header_typography_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_header_typography_section_id(),
		'args' => array(
			'title'      => __( 'Typography', 'boombox' ),
			'panel'      => 'boombox_header',
			'priority'   => 40,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_header_typography_section', 10, 1 );

/**
 * Register fields for "Header->Typography" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_header_typography_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_header_typography_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();
	$custom_fields = array(
		/***** Top Menu Font */
		array(
			'settings' => 'header_typography_top_menu_configuration',
			'label'    => __( 'Top Menu Font', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'multiple' => 1,
			'priority' => 20,
			'default'  => $defaults[ 'header_typography_top_menu_configuration' ],
			'choices'  => $choices_helper->get_header_typography_configuration_choices(),
		),
		/***** Top Menu Font */
		array(
			'settings'        => 'header_typography_top_menu',
			'section'         => $section,
			'type'            => 'typography',
			'priority'        => 20,
			'default'         => $defaults[ 'header_typography_top_menu' ],
			'active_callback' => array(
				array(
					'setting'  => 'header_typography_top_menu_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** Bottom Menu Font */
		array(
			'settings' => 'header_typography_bottom_menu_configuration',
			'label'    => __( 'Bottom Menu Font', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'multiple' => 1,
			'priority' => 30,
			'default'  => $defaults[ 'header_typography_bottom_menu_configuration' ],
			'choices'  => $choices_helper->get_header_typography_configuration_choices(),
		),
		/***** Bottom Menu Font */
		array(
			'settings'        => 'header_typography_bottom_menu',
			'section'         => $section,
			'type'            => 'typography',
			'priority'        => 30,
			'default'         => $defaults[ 'header_typography_bottom_menu' ],
			'active_callback' => array(
				array(
					'setting'  => 'header_typography_bottom_menu_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** Sub Menu Font */
		array(
			'settings' => 'header_typography_sub_menu_configuration',
			'label'    => __( 'Sub Menu Font', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'multiple' => 1,
			'priority' => 40,
			'default'  => $defaults[ 'header_typography_sub_menu_configuration' ],
			'choices'  => $choices_helper->get_header_typography_configuration_choices(),
		),
		/***** Sub Menu Font */
		array(
			'settings'        => 'header_typography_sub_menu',
			'section'         => $section,
			'type'            => 'typography',
			'priority'        => 40,
			'default'         => $defaults[ 'header_typography_sub_menu' ],
			'active_callback' => array(
				array(
					'setting'  => 'header_typography_sub_menu_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/header_typography', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_header_typography_fields', 10, 2 );