<?php
/**
 * WP Customizer panel section to handle "Footer->Posts Strip" section
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
 * Get "Footer->Posts Strip" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_footer_strip_section_id() {
	return 'boombox_footer_strip';
}

/**
 * Register "Footer->Posts Strip" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_footer_strip_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_footer_strip_section_id(),
		'args' => array(
			'title'      => __( 'Posts Strip', 'boombox' ),
			'panel'      => 'boombox_footer',
			'priority'   => 30,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_footer_strip_section', 10, 1 );

/**
 * Register fields for "Footer->Posts Strip" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_footer_strip_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_footer_strip_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();
	$custom_fields = array(
		/***** Strip */
		array(
			'settings' => 'footer_strip_enable',
			'label'    => __( 'Strip', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 20,
			'default'  => $defaults[ 'footer_strip_enable' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** "Posts Loop" heading */
		array(
			'settings' => 'footer_strip_posts_loop_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 30,
			'default'  => sprintf( '<div class="bb-sub-section"><h3>%s</h3><hr></div>', __( 'Posts Loop', 'boombox' ) ),
			'active_callback' => array(
				array(
					'setting'  => 'footer_strip_enable',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Order Criteria */
		array(
			'settings'        => 'footer_strip_conditions',
			'label'           => __( 'Order Criteria', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 30,
			'default'         => $defaults[ 'footer_strip_conditions' ],
			'multiple'        => 1,
			'choices'         => $choices_helper->get_conditions(),
			'active_callback' => array(
				array(
					'setting'  => 'footer_strip_enable',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Time Range */
		array(
			'settings'        => 'footer_strip_time_range',
			'label'           => __( 'Time Range', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 40,
			'default'         => $defaults[ 'footer_strip_time_range' ],
			'multiple'        => 1,
			'choices'         => $choices_helper->get_time_ranges(),
			'active_callback' => array(
				array(
					'setting'  => 'footer_strip_enable',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Items Count */
		array(
			'settings'        => 'footer_strip_items_count',
			'label'           => __( 'Items Count', 'boombox' ),
			'description'     => __( 'Minimum count: 6. To show all items, please enter -1.', 'boombox' ),
			'section'         => $section,
			'type'            => 'number',
			'priority'        => 50,
			'default'         => $defaults[ 'footer_strip_items_count' ],
			'choices'     => array(
				'min'  => -1,
				'step' => 1,
			),
			'active_callback' => array(
				array(
					'setting'  => 'footer_strip_enable',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Categories Filter */
		array(
			'settings'        => 'footer_strip_category',
			'label'           => __( 'Categories Filter', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 60,
			'default'         => $defaults[ 'footer_strip_category' ],
			'multiple'        => 999999999,
			'choices'         => $choices_helper->get_categories(),
			'active_callback' => array(
				array(
					'setting'  => 'footer_strip_enable',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Tags Filter */
		array(
			'settings'        => 'footer_strip_tags',
			'label'           => __( 'Tags Filter', 'boombox' ),
			'description'     => __( 'Comma separated list of tags slugs', 'boombox' ),
			'section'         => $section,
			'type'            => 'textarea',
			'priority'        => 70,
			'default'         => $defaults[ 'footer_strip_tags' ],
			'active_callback' => array(
				array(
					'setting'  => 'footer_strip_enable',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/footer_strip', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_footer_strip_fields', 10, 2 );