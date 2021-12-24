<?php
/**
 * WP Customizer panel section to handle "Extras->Video Control" section
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
 * Get "Extras->Video Control" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_extras_video_control_section_id() {
	return 'boombox_extras_video_control';
}

/**
 * Register "Extras->Video Control" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_extras_video_control_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_extras_video_control_section_id(),
		'args' => array(
			'title'      => __( 'Video Control', 'boombox' ),
			'panel'      => 'boombox_extras',
			'priority'   => 40,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_extras_video_control_section', 10, 1 );

/**
 * Register fields for "Extras->Video Control" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_extras_video_control_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_extras_video_control_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();
	$custom_fields = array(
		/***** Video/MP4/WebM On Post Listings */
		array(
			'settings' => 'extras_video_control_enable_mp4_video_on_post_listings',
			'label'    => __( 'Video/MP4/WebM On Post Listings', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 20,
			'default'  => $defaults[ 'extras_video_control_enable_mp4_video_on_post_listings' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Embed Videos On Post Listings */
		array(
			'settings' => 'extras_video_control_enable_embed_video_on_post_listings',
			'label'    => __( 'Embed Videos On Post Listings', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 30,
			'default'  => $defaults[ 'extras_video_control_enable_embed_video_on_post_listings' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** "Video/MP4/WebM" Heading */
		array(
			'settings' => 'extras_video_control_mp4_video_player_options_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 40,
			'default'  => sprintf( '<h2>%s</h2><hr/>', __( 'Video/MP4/WebM', 'boombox' ) ),
		),
		/***** Player Controls */
		array(
			'settings' => 'extras_video_control_mp4_video_player_controls',
			'label'    => __( 'Player Controls', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 40,
			'default'  => $defaults[ 'extras_video_control_mp4_video_player_controls' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_mp4_video_player_controls(),
		),
		/***** Autoplay */
		array(
			'settings' => 'extras_video_control_mp4_video_autoplay',
			'label'    => __( 'Autoplay', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 50,
			'default'  => $defaults[ 'extras_video_control_mp4_video_autoplay' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_mp4_video_player_auto_plays(),
		),
		/***** Sound Default State */
		array(
			'settings' => 'extras_video_control_mp4_video_sound',
			'label'    => __( 'Sound Default State', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 60,
			'default'  => $defaults[ 'extras_video_control_mp4_video_sound' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_mp4_video_player_sound_options(),
		),
		/***** Click Event Handler (after play) */
		array(
			'settings' => 'extras_video_control_mp4_video_click_event_handler',
			'label'    => __( 'Click Event Handler (after play)', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 70,
			'default'  => $defaults[ 'extras_video_control_mp4_video_click_event_handler' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_mp4_video_player_click_event_handlers(),
		),
		/***** Playback Loop */
		array(
			'settings' => 'extras_video_control_enable_mp4_video_loop',
			'label'    => __( 'Playback Loop', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 80,
			'default'  => $defaults[ 'extras_video_control_enable_mp4_video_loop' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/extras_video_control', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_extras_video_control_fields', 10, 2 );