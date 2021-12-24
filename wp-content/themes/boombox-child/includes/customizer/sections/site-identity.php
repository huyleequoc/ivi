<?php
/**
 * WP Customizer panel section to handle general side options (like logo, footer text)
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Register fields for "Site Identity" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_site_identity_fields( $fields, $defaults ) {

	$section = 'title_tagline';
	$custom_fields = array(
		/***** Show Tagline */
		array(
			'settings' => 'branding_show_tagline',
			'label'    => __( 'Show Tagline', 'boombox' ),
			'section'  => $section,
			'type'     => 'checkbox',
			'priority' => 55,
			'default'  => $defaults[ 'branding_show_tagline' ],
		),
		/***** Logo */
		array(
			'settings' => 'branding_logo',
			'label'    => __( 'Logo', 'boombox' ),
			'section'  => $section,
			'type'     => 'image',
			'priority' => 70,
			'default'  => $defaults[ 'branding_logo' ],
		),
		/***** Logo Width */
		array(
			'settings'    => 'branding_logo_width',
			'label'       => __( 'Logo Width', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'choices' => array(
				'min'  => 0,
				'step' => 1,
			),
			'priority'    => 71,
			'default'     => $defaults[ 'branding_logo_width' ],
		),
		/***** Logo Height */
		array(
			'settings'    => 'branding_logo_height',
			'label'       => __( 'Logo Height', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'choices' => array(
				'min'  => 0,
				'step' => 1,
			),
			'priority'    => 72,
			'default'     => $defaults[ 'branding_logo_height' ],
		),
		/***** Logo HDPI */
		array(
			'settings'    => 'branding_logo_hdpi',
			'label'       => __( 'Logo HDPI', 'boombox' ),
			'description' => __( 'An image for High DPI screen (like Retina) should be twice as big.', 'boombox' ),
			'section'     => $section,
			'type'        => 'image',
			'priority'    => 80,
			'default'     => $defaults[ 'branding_logo_hdpi' ],
		),
		/***** Small Logo */
		array(
			'settings' => 'branding_logo_small',
			'label'    => __( 'Small Logo', 'boombox' ),
			'section'  => $section,
			'type'     => 'image',
			'priority' => 90,
			'default'  => $defaults[ 'branding_logo_small' ],
		),
		/***** 404 Page Image */
		array(
			'settings' => 'branding_404_image',
			'label'    => __( '404 Page Image', 'boombox' ),
			'section'  => $section,
			'type'     => 'image',
			'priority' => 100,
			'default'  => $defaults[ 'branding_404_image' ],
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/site_identity', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_site_identity_fields', 10, 2 );