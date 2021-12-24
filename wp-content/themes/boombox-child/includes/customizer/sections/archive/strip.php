<?php
/**
 * WP Customizer panel section to handle "Archive->Posts Strip" section
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
 * Get "Archive->Posts Strip" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_archive_strip_section_id() {
	return 'boombox_archive_strip';
}

/**
 * Register "Archive->Posts Strip" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_archive_strip_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_archive_strip_section_id(),
		'args' => array(
			'title'      => __( 'Posts Strip', 'boombox' ),
			'panel'      => 'boombox_archive',
			'priority'   => 50,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_archive_strip_section', 10, 1 );

/**
 * Register fields for "Archive->Posts Strip" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_archive_strip_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_archive_strip_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();
	$custom_fields = array(
		/***** Configuration */
		array(
			'settings' => 'archive_strip_configuration',
			'label'    => __( 'Configuration', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 20,
			'default'  => $defaults[ 'archive_strip_configuration' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_strip_configurations(),
		),
		/***** "Layout" heading */
		array(
			'settings'        => 'archive_strip_layout_heading',
			'section'         => $section,
			'type'            => 'custom',
			'priority'        => 30,
			'default'         => sprintf( '<div class="bb-sub-section"><h3>%s</h3><hr></div>', __( 'Layout', 'boombox' ) ),
			'active_callback' => array(
				array(
					'setting'  => 'archive_strip_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** Type */
		array(
			'settings'        => 'archive_strip_type',
			'label'           => __( 'Type', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 30,
			'default'         => $defaults[ 'archive_strip_type' ],
			'multiple'        => 1,
			'choices'         => $choices_helper->get_strip_types(),
			'active_callback' => array(
				array(
					'setting'  => 'archive_strip_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** Width */
		array(
			'settings'        => 'archive_strip_width',
			'label'           => __( 'Width', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 40,
			'default'         => $defaults[ 'archive_strip_width' ],
			'multiple'        => 1,
			'choices'         => $choices_helper->get_strip_dimensions(),
			'active_callback' => array(
				array(
					'setting'  => 'archive_strip_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** Size */
		array(
			'settings'        => 'archive_strip_size',
			'label'           => __( 'Size', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 50,
			'default'         => $defaults[ 'archive_strip_size' ],
			'multiple'        => 1,
			'choices'         => $choices_helper->get_strip_sizes(),
			'active_callback' => array(
				array(
					'setting'  => 'archive_strip_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** Titles Position */
		array(
			'settings'        => 'archive_strip_title_position',
			'label'           => __( 'Titles Position', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 60,
			'default'         => $defaults[ 'archive_strip_title_position' ],
			'multiple'        => 1,
			'choices'         => $choices_helper->get_strip_title_positions(),
			'active_callback' => array(
				array(
					'setting'  => 'archive_strip_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** Gap Between Thumbnails */
		array(
			'settings'        => 'archive_strip_disable_gap',
			'label'           => __( 'Disable Gap Between Thumbnails', 'boombox' ),
			'section'         => $section,
			'type'            => 'checkbox',
			'priority'        => 70,
			'default'         => $defaults[ 'archive_strip_disable_gap' ],
			'active_callback' => array(
				array(
					'setting'  => 'archive_strip_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** "Posts loop" heading */
		array(
			'settings'        => 'archive_strip_posts_loop_heading',
			'section'         => $section,
			'type'            => 'custom',
			'priority'        => 80,
			'default'         => sprintf( '<div class="bb-sub-section"><h3>%s</h3><hr></div>', __( 'Posts loop', 'boombox' ) ),
			'active_callback' => array(
				array(
					'setting'  => 'archive_strip_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** Order Criteria */
		array(
			'settings'        => 'archive_strip_conditions',
			'label'           => __( 'Order Criteria', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 80,
			'default'         => $defaults[ 'archive_strip_conditions' ],
			'multiple'        => 1,
			'choices'         => $choices_helper->get_conditions(),
			'active_callback' => array(
				array(
					'setting'  => 'archive_strip_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** Time Range */
		array(
			'settings'        => 'archive_strip_time_range',
			'label'           => __( 'Time Range', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 90,
			'default'         => $defaults[ 'archive_strip_time_range' ],
			'multiple'        => 1,
			'choices'         => $choices_helper->get_time_ranges(),
			'active_callback' => array(
				array(
					'setting'  => 'archive_strip_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** Items Count */
		array(
			'settings'        => 'archive_strip_items_count',
			'label'           => __( 'Items Count', 'boombox' ),
			'description'     => __( 'Minimum count: 6. To show all items, please enter -1.', 'boombox' ),
			'section'         => $section,
			'type'            => 'number',
			'priority'        => 100,
			'default'         => $defaults[ 'archive_strip_items_count' ],
			'choices'     => array(
				'min'  => -1,
				'step' => 1,
			),
			'active_callback' => array(
				array(
					'setting'  => 'archive_strip_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** Categories Filter */
		array(
			'settings'        => 'archive_strip_category',
			'label'           => __( 'Categories Filter', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 110,
			'default'         => $defaults[ 'archive_strip_category' ],
			'multiple'        => 999999999,
			'choices'         => $choices_helper->get_categories(),
			'active_callback' => array(
				array(
					'setting'  => 'archive_strip_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** Tags filter */
		array(
			'settings'        => 'archive_strip_tags',
			'label'           => __( 'Tags Filter', 'boombox' ),
			'description'     => __( 'Comma separated list of tags slugs', 'boombox' ),
			'section'         => $section,
			'type'            => 'textarea',
			'priority'        => 120,
			'default'         => $defaults[ 'archive_strip_tags' ],
			'active_callback' => array(
				array(
					'setting'  => 'archive_strip_configuration',
					'value'    => 'custom',
					'operator' => '==',
				),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/archive_strip', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_archive_strip_fields', 10, 2 );