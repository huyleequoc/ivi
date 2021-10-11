<?php
/**
 * WP Customizer panel section to handle "Extras->Image Sizes" section
 *
 * @package BoomBox_Theme
 * @since   2.5.8
 * @version 2.5.8
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get "Extras->Image Sizes" section id
 * @return string
 *
 * @since   2.5.8
 * @version 2.5.8
 */
function boombox_customizer_get_extras_image_sizes_section_id() {
	return 'boombox_extras_image_sizes';
}

/**
 * Register "Extras->Image Sizes" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.5.8
 * @version 2.5.8
 */
function boombox_customizer_register_extras_image_sizes_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_extras_image_sizes_section_id(),
		'args' => array(
			'title'      => __( 'Image Sizes', 'boombox' ),
			'panel'      => 'boombox_extras',
			'priority'   => 130,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}
add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_extras_image_sizes_section', 10, 1 );


/**
 * Register fields for "Extras->Image Sizes" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.5.8
 * @version 2.5.8
 */
function boombox_customizer_register_extras_image_sizes_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_extras_image_sizes_section_id();

	$image_sizes_choices = array();
	foreach( Boombox_Choices_Helper::get_instance()->get_image_sizes_choices() as $size_configuration ) {
		$image_sizes_choices[ $size_configuration[ 'name' ] ] = $size_configuration[ 'label' ];
		if( $size_configuration[ 'has_2x' ] ) {
			$image_sizes_choices[ $size_configuration[ 'name' ] . '-2x' ] = $size_configuration[ 'label' ] . ' - 2x';
		}
	}
	$custom_fields = array(
		/***** "Image Sizes" visibility */
		array(
			'settings'        => 'extras_image_sizes_active_sizes',
			'label'           => __( 'Image sizes', 'boombox' ),
			'section'         => $section,
			'type'            => 'multicheck',
			'priority'        => 20,
			'default'         => $defaults[ 'extras_image_sizes_active_sizes' ],
			'multiple'        => 999999999,
			'choices'         => $image_sizes_choices,
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/extras_image_sizes', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_extras_image_sizes_fields', 10, 2 );