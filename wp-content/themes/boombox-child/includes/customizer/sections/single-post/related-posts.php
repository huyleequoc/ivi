<?php
/**
 * WP Customizer panel section to handle "Single Post->Related Posts" section
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
 * Get "Single Post->Related Posts" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_single_post_related_posts_section_id() {
	return 'boombox_single_post_related_posts';
}

/**
 * Register "Single Post->Related Posts" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_single_post_related_posts_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_single_post_related_posts_section_id(),
		'args' => array(
			'title'      => __( 'Related Posts', 'boombox' ),
			'panel'      => 'boombox_single_post',
			'priority'   => 20,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_single_post_related_posts_section', 10, 1 );

/**
 * Register fields for "Single Post->Related Posts" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_single_post_related_posts_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_single_post_related_posts_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();
	$custom_fields = array(
		/***** Related Entries Per Page */
		array(
			'settings'    => 'single_post_related_posts_related_entries_per_page',
			'label'       => __( '"Related Posts" Per Page', 'boombox' ),
			'description' => __( 'To show all items, please enter -1.', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 20,
			'default'     => $defaults[ 'single_post_related_posts_related_entries_per_page' ],
			'choices' => array(
				'min'  => -1,
				'step' => 1,
			),
		),
		/***** Related Entries Heading */
		array(
			'settings' => 'single_post_related_posts_related_entries_heading',
			'label'    => __( '"Related Posts" Block Heading', 'boombox' ),
			'section'  => $section,
			'type'     => 'text',
			'priority' => 30,
			'default'  => $defaults[ 'single_post_related_posts_related_entries_heading' ],
		),
		/***** "More From" Posts Per Page */
		array(
			'settings'    => 'single_post_related_posts_more_entries_per_page',
			'label'       => __( '"More From" Posts Per Page', 'boombox' ),
			'description' => __( 'To show all items, please enter -1.', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 40,
			'default'     => $defaults[ 'single_post_related_posts_more_entries_per_page' ],
			'choices' => array(
				'min'  => -1,
				'step' => 1,
			),
		),
		/***** "More From" Block Heading */
		array(
			'settings' => 'single_post_related_posts_more_entries_heading',
			'label'    => __( '"More From" Block Heading', 'boombox' ),
			'section'  => $section,
			'type'     => 'text',
			'priority' => 50,
			'default'  => $defaults[ 'single_post_related_posts_more_entries_heading' ],
		),
		/***** Don't Miss Entries Per Page */
		array(
			'settings'    => 'single_post_related_posts_dont_miss_entries_per_page',
			'label'       => __( '"Don\'t Miss" Entries Per Page', 'boombox' ),
			'description' => __( 'To show all items, please enter -1.', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 60,
			'default'     => $defaults[ 'single_post_related_posts_dont_miss_entries_per_page' ],
			'choices' => array(
				'min'  => -1,
				'step' => 1,
			),
		),
		/***** Don't Miss Entries Heading */
		array(
			'settings' => 'single_post_related_posts_dont_miss_entries_heading',
			'label'    => __( '"Don\'t Miss" Block Heading', 'boombox' ),
			'section'  => $section,
			'type'     => 'text',
			'priority' => 70,
			'default'  => $defaults[ 'single_post_related_posts_dont_miss_entries_heading' ],
		),
		/***** Hide Elements ( for related posts ) */
		array(
			'settings' => 'single_post_related_posts_grid_sections_hide_elements',
			'label'    => __( 'Hide Elements', 'boombox' ),
			'section'  => $section,
			'type'     => 'multicheck',
			'priority' => 80,
			'default'  => $defaults[ 'single_post_related_posts_grid_sections_hide_elements' ],
			'choices'  => $choices_helper->get_grid_hide_elements(),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/single_post_related_posts', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_single_post_related_posts_fields', 10, 2 );