<?php
/**
 * WP Customizer panel section to handle "Single Post->Sponsored Articles" section
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get "Single Post->Sponsored Articles" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_single_post_sporsored_articles_section_id() {
	return 'boombox_single_sponsored_articles';
}

/**
 * Register "Single Post->Sponsored Articles" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_single_post_sponsored_articles_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_single_post_sporsored_articles_section_id(),
		'args' => array(
			'title'      => __( 'Sponsored Articles', 'boombox' ),
			'panel'      => 'boombox_single_post',
			'priority'   => 50,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}
add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_single_post_sponsored_articles_section', 10, 1 );

/**
 * Register fields for "Single Post->Sponsored Articles" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_single_post_sponsored_articles_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_single_post_sporsored_articles_section_id();
	$custom_fields = array(
		/***** Label */
		array(
			'settings' => 'single_post_sponsored_articles_label',
			'label'    => __( 'Label', 'boombox' ),
			'section'  => $section,
			'type'     => 'text',
			'priority' => 20,
			'default'  => $defaults[ 'single_post_sponsored_articles_label' ],
		),
		/***** Position */
		array(
			'settings' => 'single_post_sponsored_articles_position',
			'label'    => __( 'Position', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 30,
			'default'  => $defaults[ 'single_post_sponsored_articles_position' ],
			'multiple' => 1,
			'choices'  => array(
				'top'    => esc_html__( 'Top', 'boombox' ),
				'bottom' => esc_html__( 'Bottom', 'boombox' ),
				'both'   => esc_html__( 'Both', 'boombox' ),
				'none'   => esc_html__( 'None', 'boombox' ),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/single_post_sponsored_articles', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_single_post_sponsored_articles_fields', 10, 2 );