<?php
/**
 * WP Customizer panel section to handle "Archive->Main Posts" section
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.6.8
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Get "Archive->Main Posts" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_archive_main_posts_section_id() {
	return 'boombox_archive_main_posts';
}

/**
 * Register "Archive->Main Posts" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_archive_main_posts_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_archive_main_posts_section_id(),
		'args' => array(
			'title'      => __( 'Main', 'boombox' ),
			'panel'      => 'boombox_archive',
			'priority'   => 20,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_archive_main_posts_section', 10, 1 );

/**
 * Register fields for "Archive->Main Posts" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_archive_main_posts_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_archive_main_posts_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();
	$custom_fields = array(
		/***** "Layout" heading */
		array(
			'settings' => 'archive_main_posts_layout_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 20,
			'default'  => sprintf( '<h2>%s</h2><hr/>', __( 'Layout', 'boombox' ) ),
		),
		/***** Sidebar Type */
		array(
			'settings' => 'archive_main_posts_sidebar_type',
			'label'    => __( 'Sidebar Type', 'boombox' ),
			'section'  => $section,
			'type'     => 'radio-image',
			'priority' => 20,
			'default'  => $defaults[ 'archive_main_posts_sidebar_type' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_sidebar_types(),
		),
		/***** Sidebar Orientation */
		array(
			'settings' => 'archive_main_posts_sidebar_orientation',
			'label'    => __( 'Sidebar Orientation', 'boombox' ),
			'section'  => $section,
			'type'     => 'radio',
			'priority' => 30,
			'default'  => $defaults[ 'archive_main_posts_sidebar_orientation' ],
			'multiple' => 1,
			'choices'  => array(
				'right' => __( 'Right', 'boombox' ),
				'left'  => __( 'Left', 'boombox' ),
			),
		),
		/***** Listing Type */
		array(
			'settings' => 'archive_main_posts_listing_type',
			'label'    => __( 'Listing Type', 'boombox' ),
			'section'  => $section,
			'type'     => 'radio-image',
			'priority' => 40,
			'default'  => $defaults[ 'archive_main_posts_listing_type' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_listing_types( 'value=>image' ),
		),
		/***** Hide Elements */
		array(
			'settings' => 'archive_main_posts_hide_elements',
			'label'    => __( 'Hide Elements', 'boombox' ),
			'section'  => $section,
			'type'     => 'multicheck',
			'priority' => 50,
			'default'  => $defaults[ 'archive_main_posts_hide_elements' ],
			'choices'  => $choices_helper->get_grid_hide_elements(),
		),
		/***** Share Bar Elements */
		array(
			'settings' => 'archive_main_posts_share_bar_elements',
			'label'    => __( 'Share Bar Elements', 'boombox' ),
			'section'  => $section,
			'type'     => 'multicheck',
			'priority' => 60,
			'default'  => $defaults[ 'archive_main_posts_share_bar_elements' ],
			'choices'  => $choices_helper->get_share_bar_elements(),
//todo always show Share Bar Elements till the Kirki offers alternative solution for "not contains" operator
//			'active_callback' => array(
//				array(
//					'setting'  => 'archive_main_posts_hide_elements',
//					'operator' => 'not contains',
//					'value'    => 'share_bar',
//				),
//			),
		),
		/***** "Posts Loop" heading */
		array(
			'settings' => 'archive_main_posts_posts_loop_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 70,
			'default'  => sprintf( '<h2>%s</h2><hr>', __( 'Posts Loop', 'boombox' ) ),
		),
		/***** Default Order */
		array(
			'settings' => 'archive_main_posts_default_order',
			'label'    => __( 'Default Order', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 70,
			'default'  => $defaults[ 'archive_main_posts_default_order' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_conditions(),
		),
		/***** Pagination Type */
		array(
			'settings' => 'archive_main_posts_pagination_type',
			'label'    => __( 'Pagination Type', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 80,
			'default'  => $defaults[ 'archive_main_posts_pagination_type' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_pagination_types(),
		),
		/***** Posts Per Page */
		array(
			'settings'    => 'archive_main_posts_posts_per_page',
			'label'       => __( 'Posts Per Page', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 90,
			'default'     => $defaults[ 'archive_main_posts_posts_per_page' ],
			'choices' => array(
				'min'  => -1,
				'step' => 1,
			),
		),
		/***** "Injects" heading */
		array(
			'settings' => 'archive_main_posts_posts_injects_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 100,
			'default'  => sprintf( '<div class="bb-sub-section"><h3>%s</h3><hr></div>', __( 'Injects', 'boombox' ) ),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/archive_main_posts', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_archive_main_posts_fields', 10, 2 );