<?php
/**
 * WP Customizer panel section to handle "Header->Posts Strip" section
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
 * Get "Header->Posts Strip" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_header_strip_section_id() {
	return 'boombox_header_strip';
}

/**
 * Register "Header->Posts Strip" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_header_strip_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_header_strip_section_id(),
		'args' => array(
			'title'      => __( 'Posts Strip', 'boombox' ),
			'panel'      => 'boombox_header',
			'priority'   => 60,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_header_strip_section', 10, 1 );

/**
 * Register fields for "Header->Posts Strip" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_header_strip_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_header_strip_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();
	$conditions_choices = $choices_helper->get_conditions();
	$time_range_choices = $choices_helper->get_time_ranges();
	$category_choices = $choices_helper->get_categories();
	$custom_fields = array(
		/***** Visibility */
		array(
			'settings' => 'header_strip_visibility',
			'label'    => __( 'Visibility', 'boombox' ),
			'section'  => $section,
			'type'     => 'multicheck',
			'priority' => 20,
			'default'  => $defaults[ 'header_strip_visibility' ],
			'choices'  => $choices_helper->get_strip_visibilities(),
		),
		/***** "Layout" heading */
		array(
			'settings' => 'header_strip_layout_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 30,
			'default'  => sprintf( '<div class="bb-sub-section"><h3>%s</h3><hr></div>', __( 'Layout', 'boombox' ) ),
		),
		/***** Type */
		array(
			'settings' => 'header_strip_type',
			'label'    => __( 'Type', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 30,
			'default'  => $defaults[ 'header_strip_type' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_strip_types(),
		),
		/***** Width */
		array(
			'settings' => 'header_strip_width',
			'label'    => __( 'Width', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 40,
			'default'  => $defaults[ 'header_strip_width' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_strip_dimensions(),
		),
		/***** Size */
		array(
			'settings' => 'header_strip_size',
			'label'    => __( 'Size', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 50,
			'default'  => $defaults[ 'header_strip_size' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_strip_sizes(),
		),
		/***** Titles Position */
		array(
			'settings' => 'header_strip_title_position',
			'label'    => __( 'Titles Position', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 60,
			'default'  => $defaults[ 'header_strip_title_position' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_strip_title_positions(),
		),
		/***** Gap Between Thumbnails */
		array(
			'settings' => 'header_strip_disable_gap',
			'label'    => __( 'Disable Gap Between Thumbnails', 'boombox' ),
			'section'  => $section,
			'type'     => 'checkbox',
			'priority' => 70,
			'default'  => $defaults[ 'header_strip_disable_gap' ],
		),
		/***** "Posts Loop" heading */
		array(
			'settings' => 'header_strip_posts_loop_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 80,
			'default'  => sprintf( '<div class="bb-sub-section"><h3>%s</h3><hr></div>', __( 'Posts Loop', 'boombox' ) ),
		),
		/***** Order Criteria */
		array(
			'settings' => 'header_strip_conditions',
			'label'    => __( 'Order Criteria', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 80,
			'default'  => $defaults[ 'header_strip_conditions' ],
			'multiple' => 1,
			'choices'  => $conditions_choices,
		),
		/***** Time Range */
		array(
			'settings' => 'header_strip_time_range',
			'label'    => __( 'Time Range', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 90,
			'default'  => $defaults[ 'header_strip_time_range' ],
			'multiple' => 1,
			'choices'  => $time_range_choices,
		),
		/***** Items Count */
		array(
			'settings'    => 'header_strip_items_count',
			'label'       => __( 'Items Count', 'boombox' ),
			'description' => __( 'Minimum count: 6. To show all items, please enter -1.', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 100,
			'default'     => $defaults[ 'header_strip_items_count' ],
			'choices' => array(
				'min'  => -1,
				'step' => 1,
			),
		),
		/***** Categories Filter */
		array(
			'settings' => 'header_strip_category',
			'label'    => __( 'Categories Filter', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 110,
			'default'  => $defaults[ 'header_strip_category' ],
			'multiple' => 999999999,
			'choices'  => $category_choices,
		),
		/***** Tags Filter */
		array(
			'settings'    => 'header_strip_tags',
			'label'       => __( 'Tags Filter', 'boombox' ),
			'description' => __( 'Comma separated list of tags slugs', 'boombox' ),
			'section'     => $section,
			'type'        => 'textarea',
			'priority'    => 120,
			'default'     => $defaults[ 'header_strip_tags' ],
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/header_strip', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_header_strip_fields', 10, 2 );