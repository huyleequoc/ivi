<?php
/**
 * WP Customizer panel section to handle "Extras->NSFW" section
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
 * Get "Extras->NSFW" section id
 * @return string
 *
 * @since   2.1.3
 * @version 2.1.3
 */
function boombox_customizer_get_extras_nsfw_section_id() {
	return 'boombox_extras_nsfw';
}

/**
 * Register "Extras->NSFW" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.1.3
 * @version 2.1.3
 */
function boombox_customizer_register_extras_nsfw_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_extras_nsfw_section_id(),
		'args' => array(
			'title'      => __( 'NSFW', 'boombox' ),
			'panel'      => 'boombox_extras',
			'priority'   => 90,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_extras_nsfw_section', 10, 1 );

/**
 * Register fields for "Extras->NSFW" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.1.3
 * @version 2.1.3
 */
function boombox_customizer_register_extras_nsfw_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_extras_nsfw_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();
	$custom_fields = array(
		/***** "NSFW" categories */
		array(
			'settings'        => 'extras_nsfw_categories',
			'label'           => __( '"NSFW" categories', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 20,
			'default'         => $defaults[ 'extras_nsfw_categories' ],
			'multiple'        => 999999999,
			'choices'         => $choices_helper->get_categories(),
		),
		/***** Users must to be logged in to view posts */
		array(
			'settings' => 'extras_nsfw_require_auth',
			'label'    => __( 'Users must to be logged in to view posts', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 30,
			'default'  => $defaults[ 'extras_nsfw_require_auth' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/extras_nsfw', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_extras_nsfw_fields', 10, 2 );