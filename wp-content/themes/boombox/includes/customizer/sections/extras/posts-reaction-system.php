<?php
/**
 * WP Customizer panel section to handle "Extras->Posts Reaction System" section
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
 * Get "Extras->Posts Reaction System" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_extras_posts_reaction_system_section_id() {
	return 'boombox_extras_posts_reaction_system';
}

/**
 * Register "Extras->Posts Reaction System" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_extras_posts_reaction_system_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_extras_posts_reaction_system_section_id(),
		'args' => array(
			'title'      => __( 'Posts Reaction System', 'boombox' ),
			'panel'      => 'boombox_extras',
			'priority'   => 70,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_extras_posts_reaction_system_section', 10, 1 );

/**
 * Register fields for "Extras->Posts Reaction System" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_extras_posts_reaction_system_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_extras_posts_reaction_system_section_id();
	$custom_fields = array(
		/***** Reactions */
		array(
			'settings' => 'extras_post_reaction_system_enable',
			'label'    => __( 'Reactions', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 20,
			'default'  => $defaults[ 'extras_post_reaction_system_enable' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Login Required For Reactions Voting */
		array(
			'settings'        => 'extras_post_reaction_system_login_require',
			'label'           => __( 'Login Required For Reactions Voting', 'boombox' ),
			'section'         => $section,
			'type'            => 'switch',
			'priority'        => 30,
			'default'         => $defaults[ 'extras_post_reaction_system_login_require' ],
			'choices'         => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'extras_post_reaction_system_enable',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Reaction Award Minimal Score */
		array(
			'settings'        => 'extras_post_reaction_system_award_minimal_score',
			'label'           => __( 'Reaction Award Minimal Score', 'boombox' ),
			'section'         => $section,
			'type'            => 'number',
			'priority'        => 40,
			'default'         => $defaults[ 'extras_post_reaction_system_award_minimal_score' ],
			'choices'     => array(
				'min'  => 1,
				'step' => 1,
			),
			'active_callback' => array(
				array(
					'setting'  => 'extras_post_reaction_system_enable',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Reactions Maximal Count Per Vote */
		array(
			'settings'        => 'extras_post_reaction_system_maximal_count_per_vote',
			'label'           => __( 'Reactions Maximal Count Per Vote', 'boombox' ),
			'section'         => $section,
			'type'            => 'number',
			'priority'        => 50,
			'default'         => $defaults[ 'extras_post_reaction_system_maximal_count_per_vote' ],
			'choices'     => array(
				'min'  => 1,
				'step' => 1,
			),
			'active_callback' => array(
				array(
					'setting'  => 'extras_post_reaction_system_enable',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Fake reaction count base */
		array(
			'settings'        => 'extras_post_reaction_system_fake_reaction_count_base',
			'label'           => __( 'Fake reaction count base', 'boombox' ),
			'description'     => __( 'Use 0 to not use the "Fake reactions" feature.', 'boombox' ),
			'section'         => $section,
			'type'            => 'number',
			'priority'        => 60,
			'default'         => $defaults[ 'extras_post_reaction_system_fake_reaction_count_base' ],
			'choices'     => array(
				'min'  => 0,
				'step' => 1,
			),
			'active_callback' => array(
				array(
					'setting'  => 'extras_post_reaction_system_enable',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/extras_posts_reaction_system', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_extras_posts_reaction_system_fields', 10, 2 );