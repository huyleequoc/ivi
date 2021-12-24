<?php
/**
 * WP Customizer panel section to handle "Home->Featured Area" section
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
 * Get "Home->Featured Area" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_home_featured_area_section_id() {
	return 'boombox_home_featured_area';
}

/**
 * Register "Home->Featured Area" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_home_featured_area_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_home_featured_area_section_id(),
		'args' => array(
			'title'      => __( 'Featured Area', 'boombox' ),
			'panel'      => 'boombox_home',
			'priority'   => 30,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_home_featured_area_section', 10, 1 );

/**
 * Register fields for "Home->Featured Area" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_home_featured_area_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_home_featured_area_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();
	$custom_fields = array(
		/****** "Layout" heading */
		array(
			'settings' => 'home_featured_area_layout_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 20,
			'default'  => sprintf( '<h2>%s</h2><hr/>', __( 'Layout', 'boombox' ) ),
		),
		/****** Area Type */
		array(
			'settings' => 'home_featured_area_type',
			'label'    => __( 'Area Type', 'boombox' ),
			'section'  => $section,
			'type'     => 'radio-image',
			'priority' => 20,
			'default'  => $defaults[ 'home_featured_area_type' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_featured_area_types(),
		),
		/****** Disable Gap Between Thumbnails */
		array(
			'settings'        => 'home_featured_area_disable_gap',
			'label'           => __( 'Disable Gap Between Thumbnails', 'boombox' ),
			'section'         => $section,
			'type'            => 'checkbox',
			'priority'        => 30,
			'default'         => $defaults[ 'home_featured_area_disable_gap' ],
			'active_callback' => array(
				array(
					array(
						'setting'  => 'home_featured_area_type',
						'value'    => 'disable',
						'operator' => '!=',
					),
					array(
						'setting'  => 'home_featured_area_type',
						'value'    => 'type-1long',
						'operator' => '!=',
					),
				)
			),
		),
		/****** Hide Elements */
		array(
			'settings'        => 'home_featured_area_hide_elements',
			'label'           => __( 'Hide Elements', 'boombox' ),
			'section'         => $section,
			'type'            => 'multicheck',
			'priority'        => 40,
			'default'         => $defaults[ 'home_featured_area_hide_elements' ],
			'multiple'        => 1,
			'choices'         => $choices_helper->get_featured_area_hide_elements(),
			'active_callback' => array(
				array(
					'setting'  => 'home_featured_area_type',
					'value'    => 'disable',
					'operator' => '!=',
				),
			),
		),
		/****** "Posts Loop" heading */
		array(
			'settings'        => 'home_featured_area_posts_loop_heading',
			'section'         => $section,
			'type'            => 'custom',
			'priority'        => 50,
			'default'         => sprintf( '<h2>%s</h2><hr/>', __( 'Posts loop', 'boombox' ) ),
			'active_callback' => array(
				array(
					'setting'  => 'home_featured_area_type',
					'value'    => 'disable',
					'operator' => '!=',
				),
			),
		),
		/****** Order Criteria */
		array(
			'settings'        => 'home_featured_area_conditions',
			'label'           => __( 'Order Criteria', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 50,
			'default'         => $defaults[ 'home_featured_area_conditions' ],
			'multiple'        => 1,
			'choices'         => $choices_helper->get_conditions(),
			'active_callback' => array(
				array(
					'setting'  => 'home_featured_area_type',
					'value'    => 'disable',
					'operator' => '!=',
				),
			),
		),
		/****** Time Range */
		array(
			'settings'        => 'home_featured_area_time_range',
			'label'           => __( 'Time Range', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 60,
			'default'         => $defaults[ 'home_featured_area_time_range' ],
			'multiple'        => 1,
			'choices'         => $choices_helper->get_time_ranges(),
			'active_callback' => array(
				array(
					'setting'  => 'home_featured_area_type',
					'value'    => 'disable',
					'operator' => '!=',
				),
			),
		),
		/****** Categories Filter */
		array(
			'settings'        => 'home_featured_area_category',
			'label'           => __( 'Categories Filter', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 70,
			'default'         => $defaults[ 'home_featured_area_category' ],
			'multiple'        => 999999999,
			'choices'         => $choices_helper->get_categories(),
			'active_callback' => array(
				array(
					'setting'  => 'home_featured_area_type',
					'value'    => 'disable',
					'operator' => '!=',
				),
			),
		),
		/****** Tags Filter */
		array(
			'settings'        => 'home_featured_area_tags',
			'label'           => __( 'Tags Filter', 'boombox' ),
			'description'     => __( 'Comma separated list of tags slugs', 'boombox' ),
			'section'         => $section,
			'type'            => 'textarea',
			'priority'        => 80,
			'default'         => $defaults[ 'home_featured_area_tags' ],
			'active_callback' => array(
				array(
					'setting'  => 'home_featured_area_type',
					'value'    => 'disable',
					'operator' => '!=',
				),
			),
		),
		/****** Exclude Featured Entries From Main Posts Loop */
		array(
			'settings'        => 'home_featured_area_exclude_from_main_loop',
			'label'           => __( 'Exclude Featured Entries From Main Posts Loop', 'boombox' ),
			'section'         => $section,
			'type'            => 'checkbox',
			'priority'        => 90,
			'default'         => $defaults[ 'home_featured_area_exclude_from_main_loop' ],
			'active_callback' => array(
				array(
					'setting'  => 'home_featured_area_type',
					'value'    => 'disable',
					'operator' => '!=',
				),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/home_featured_area', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_home_featured_area_fields', 10, 2 );