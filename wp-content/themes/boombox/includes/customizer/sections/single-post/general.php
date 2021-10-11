<?php
/**
 * WP Customizer panel section to handle "Single Post->General" section
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
 * Get "Single Post->General" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_single_post_general_section_id() {
	return 'boombox_single_post_general';
}

/**
 * Register "Single Post->General" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_single_post_general_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_single_post_general_section_id(),
		'args' => array(
			'title'      => __( 'Main', 'boombox' ),
			'panel'      => 'boombox_single_post',
			'priority'   => 20,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_single_post_general_section', 10, 1 );

/**
 * Register fields for "Single Post->General" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_single_post_general_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_single_post_general_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();
	$custom_fields = array(
		/***** "Layout" Heading */
		array(
			'settings' => 'single_post_layout_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 20,
			'default'  => sprintf( '<h2>%s</h2><hr/>', __( 'Layout', 'boombox' ) ),
		),
		/***** Sidebar Type */
		array(
			'settings' => 'single_post_general_sidebar_type',
			'label'    => __( 'Sidebar Type', 'boombox' ),
			'section'  => $section,
			'type'     => 'radio-image',
			'priority' => 20,
			'default'  => $defaults[ 'single_post_general_sidebar_type' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_sidebar_types(),
		),
		/***** Sidebar Orientation */
		array(
			'settings'        => 'single_post_general_sidebar_orientation',
			'label'           => __( 'Sidebar Orientation', 'boombox' ),
			'section'         => $section,
			'type'            => 'radio',
			'priority'        => 30,
			'default'         => $defaults[ 'single_post_general_sidebar_orientation' ],
			'multiple'        => 1,
			'choices'         => array(
				'right' => __( 'Right', 'boombox' ),
				'left'  => __( 'Left', 'boombox' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'single_post_general_sidebar_type',
					'value'    => 'no-sidebar',
					'operator' => '!=',
				),
			),
		),
		/***** Featured Media */
		array(
			'settings' => 'single_post_general_featured_media',
			'label'    => __( 'Featured Media', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 40,
			'default'  => $defaults[ 'single_post_general_featured_media' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Template */
		array(
			'settings' => 'single_post_general_layout',
			'label'    => __( 'Template', 'boombox' ),
			'section'  => $section,
			'type'     => 'radio-image',
			'priority' => 50,
			'default'  => $defaults[ 'single_post_general_layout' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_single_templates(),
		),
		/***** Hide Elements */
		array(
			'settings' => 'single_post_general_hide_elements',
			'label'    => __( 'Hide Elements', 'boombox' ),
			'section'  => $section,
			'type'     => 'multicheck',
			'priority' => 60,
			'default'  => $defaults[ 'single_post_general_hide_elements' ],
			'choices'  => $choices_helper->get_post_hide_elements(),
		),
		/***** Top Share Bar */
		array(
			'settings' => 'single_post_general_top_sharebar',
			'label'    => __( 'Top Share Bar', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 70,
			'default'  => $defaults[ 'single_post_general_top_sharebar' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Bottom Share Bar */
		array(
			'settings' => 'single_post_general_bottom_sharebar',
			'label'    => __( 'Bottom Share Bar', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 80,
			'default'  => $defaults[ 'single_post_general_bottom_sharebar' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Share Bar Elements */
		array(
			'settings' => 'single_post_general_share_box_elements',
			'label'    => __( 'Share Bar Elements', 'boombox' ),
			'section'  => $section,
			'type'     => 'multicheck',
			'priority' => 90,
			'default'  => $defaults[ 'single_post_general_share_box_elements' ],
			'choices'  => array(
				'share_count' => __( 'Share Count', 'boombox' ),
				'comments'    => __( 'Comments', 'boombox' ),
				'points'      => __( 'Points', 'boombox' ),
			),
		),
		/***** Sections */
		array(
			'settings' => 'single_post_general_sections',
			'label'    => __( 'Sections', 'boombox' ),
			'section'  => $section,
			'type'     => 'sortable',
			'priority' => 100,
			'default'  => $defaults[ 'single_post_general_sections' ],
			'choices'  => $choices_helper->get_post_sortable_sections(),
		),
		/***** "Miscellaneous" Heading */
		array(
			'settings' => 'single_post_general_miscellaneous_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 110,
			'default'  => sprintf( '<h2>%s</h2><hr/>', __( 'Miscellaneous', 'boombox' ) ),
		),
		/***** Show "View Full Post" Button If */
		array(
			'settings' => 'single_post_general_enable_full_post_button_conditions',
			'label'    => __( 'Show "View Full Post" Button If', 'boombox' ),
			'section'  => $section,
			'type'     => 'multicheck',
			'priority' => 120,
			'default'  => $defaults[ 'single_post_general_enable_full_post_button_conditions' ],
			'choices'  => $choices_helper->get_view_full_post_button_appearance_conditions(),
		),
		/***** "View Full Post" Button Label */
		array(
			'settings' => 'single_post_general_post_button_label',
			'label'    => __( 'Button Text', 'boombox' ),
			'section'  => $section,
			'type'     => 'text',
			'priority' => 130,
			'default'  => $defaults[ 'single_post_general_post_button_label' ],
		),
		/***** "View Full Post" Button End */
		array(
			'settings' => 'single_post_general_view_full_post_button_end',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 140,
			'default'  => '<hr/>',
		),
		/***** Pagination Style */
		array(
			'settings' => 'single_post_general_pagination_layout',
			'label'    => __( 'Pagination Style', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 150,
			'default'  => $defaults[ 'single_post_general_pagination_layout' ],
			'multiple' => 1,
			'choices'  => array(
				'page_xy' => __( 'X / Y', 'boombox' ),
				'numeric' => __( 'Numeric', 'boombox' ),
				'next_xy' => __( 'Single Next Button', 'boombox' )
			),
		),
		/***** Posts Navigation Direction */
		array(
			'settings' => 'single_post_general_navigation_direction',
			'label'    => __( 'Posts Navigation Direction', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 150,
			'default'  => $defaults[ 'single_post_general_navigation_direction' ],
			'multiple' => 1,
			'choices'  => array(
				'to-oldest' => __( 'From Newest To Oldest', 'boombox' ),
				'to-newest' => __( 'From Oldest To Newest', 'boombox' ),
			),
		),
		/***** Disable Next / Prev Buttons */
		array(
			'settings' => 'single_post_general_next_prev_buttons',
			'label'    => __( 'Next / Prev Post Buttons', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 160,
			'default'  => $defaults[ 'single_post_general_next_prev_buttons' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Floating Navbar */
		array(
			'settings' => 'single_post_general_floating_navbar',
			'label'    => __( 'Floating Navbar', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 170,
			'default'  => $defaults[ 'single_post_general_floating_navbar' ],
			'multiple' => 1,
			'choices'  => array(
				'post_title' => __( 'With Post Title', 'boombox' ),
				'share_bar'  => __( 'With Share Bar', 'boombox' ),
				'none'       => __( 'None', 'boombox' )
			),
		),
		/***** Side Navigation */
		array(
			'settings' => 'single_post_general_floating_navbar_navigation',
			'label'    => __( 'Floating Navbar Next Button', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 180,
			'default'  => $defaults[ 'single_post_general_floating_navbar_navigation' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Side Navigation */
		array(
			'settings' => 'single_post_general_side_navigation',
			'label'    => __( 'Side Navigation', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 190,
			'default'  => $defaults[ 'single_post_general_side_navigation' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		)
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/single_post_general', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_single_post_general_fields', 10, 2 );