<?php
/**
 * WP Customizer panel section to handle "Extras->GDPR" section
 *
 * @package BoomBox_Theme
 * @since   2.5.5
 * @version 2.5.5
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get "Extras->GDPR" section id
 * @return string
 *
 * @since   2.5.5
 * @version 2.5.5
 */
function boombox_customizer_get_extras_gdpr_section_id() {
	return 'boombox_extras_gdpr';
}

/**
 * Register "Extras->GDPR" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.5.5
 * @version 2.5.5
 */
function boombox_customizer_register_extras_gdpr_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_extras_gdpr_section_id(),
		'args' => array(
			'title'      => __( 'GDPR', 'boombox' ),
			'panel'      => 'boombox_extras',
			'priority'   => 30,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}
add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_extras_gdpr_section', 10, 1 );

/**
 * Register fields for "Extras->GDPR" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.5.5
 * @version 2.5.5
 */
function boombox_customizer_register_extras_gdpr_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_extras_gdpr_section_id();
	$visibility_choices = Boombox_Choices_Helper::get_instance()->get_gdpr_checkox_visibility_choices();

	$custom_fields = array(
		/**** Visibility */
		array(
			'settings' => 'extras_gdpr_visibility',
			'label'    => __( 'Visibility', 'boombox' ),
			'section'  => $section,
			'type'     => 'multicheck',
			'priority' => 20,
			'default'  => $defaults['extras_gdpr_visibility'],
			'choices'  => $visibility_choices,
		),
		/***** Cookie Consent Script */
		array(
			'settings'        => 'extras_gdpr_cookie_consent_script',
			'label'           => __( 'Cookie Consent Script', 'boombox' ),
			'section'         => $section,
			'type'            => 'code',
			'choices'     => array(
				'language' => 'html',
			),
			'description'     => sprintf( __( 'You can generate consent script <a href="%s" target="_blank">here</a>', 'boombox' ), 'https://silktide.com/tools/cookie-consent/download/' ),
			'priority'        => 40,
			'default'         => $defaults[ 'extras_gdpr_cookie_consent_script' ]
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/extras_gdpr', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_extras_gdpr_fields', 10, 2 );