<?php
/**
 * WP Customizer panel section to handle "Extras->Additional" section
 *
 * @package BoomBox_Theme
 * @since   2.6.1
 * @version 2.6.1
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get "Extras->Additional" section id
 *
 * @return string
 *
 * @since   2.6.1
 * @version 2.6.1
 */
function boombox_customizer_get_extras_additional_section_id() {
	return 'boombox_extras_additional';
}

/**
 * Register "Extras->Additional" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.6.1
 * @version 2.6.1
 */
function boombox_customizer_register_extras_additional_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_extras_additional_section_id(),
		'args' => array(
			'title'      => __( 'Additional', 'boombox' ),
			'panel'      => 'boombox_extras',
			'priority'   => 140,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_extras_additional_section', 10, 1 );

/**
 * Register fields for "Extras->Additional" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.6.1
 * @version 2.6.1
 */
function boombox_customizer_register_extras_additional_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_extras_additional_section_id();

	$custom_fields = array(
		/***** "Back To Top" */
		array(
			'settings' => 'extra_additional_enable_back_to_top_btn',
			'label'    => __( '"Back To Top" Button', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 20,
			'default'  => $defaults['extra_additional_enable_back_to_top_btn'],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),

		/***** Affiliate Button Text */
		array(
			'settings' => 'extra_additional_affiliate_button_text',
			'label'    => __( '"Affiliate Button Text"', 'boombox' ),
			'section'  => $section,
			'type'     => 'text',
			'priority' => 30,
			'default'  => $defaults['extra_additional_affiliate_button_text'],
		),

		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/extras_additional', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_extras_additional_fields', 10, 2 );