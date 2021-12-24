<?php
/**
 * WP Customizer panel section to handle "Extras->Posts Ranking System" section
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
 * Get "Extras->Posts Ranking System" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_extras_posts_ranking_system_section_id() {
	return 'boombox_extras_posts_ranking_system';
}

/**
 * Register "Extras->Posts Ranking System" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_extras_posts_ranking_system_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_extras_posts_ranking_system_section_id(),
		'args' => array(
			'title'      => __( 'Posts Ranking System', 'boombox' ),
			'panel'      => 'boombox_extras',
			'priority'   => 60,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_extras_posts_ranking_system_section', 10, 1 );

/**
 * Register fields for "Extras->Posts Ranking System" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_extras_posts_ranking_system_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_extras_posts_ranking_system_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();
	$published_pages = $choices_helper->get_published_pages();

	$custom_fields = array(
		/***** View Track */
		array(
			'settings' => 'extras_post_ranking_system_enable_view_track',
			'label'    => __( 'View Track', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 20,
			'default'  => $defaults[ 'extras_post_ranking_system_enable_view_track' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Login Required For Points Voting */
		array(
			'settings' => 'extras_post_ranking_system_points_login_require',
			'label'    => __( 'Login Required For Points Voting', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 30,
			'default'  => $defaults[ 'extras_post_ranking_system_points_login_require' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Numeration badges */
		array(
			'settings' => 'extras_post_ranking_system_numeration_badges',
			'label'    => __( 'Numeration badges', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 40,
			'default'  => $defaults[ 'extras_post_ranking_system_numeration_badges' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Trending Conditions */
		array(
			'settings' => 'extras_post_ranking_system_trending_conditions',
			'label'    => __( 'Trending Conditions', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 50,
			'default'  => $defaults[ 'extras_post_ranking_system_trending_conditions' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_trending_conditions(),
		),
		/***** "Trending" Heading */
		array(
			'settings'    => 'extras_post_ranking_system_trending_heading',
			'label'       => sprintf( '<h2>%s</h2>', __( 'Trending', 'boombox' ) ),
			'section'     => $section,
			'type'        => 'custom',
			'priority'    => 60,
			'description' => __( 'Time Range: Last 24 hours', 'boombox' ) . '<hr />',
		),
		/***** Trending */
		array(
			'settings' => 'extras_post_ranking_system_trending_enable',
			'label'    => __( 'Trending', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 60,
			'default'  => $defaults[ 'extras_post_ranking_system_trending_enable' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Trending Page */
		array(
			'settings' => 'extras_post_ranking_system_trending_page',
			'label'    => __( 'Trending page', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 70,
			'default'  => $defaults[ 'extras_post_ranking_system_trending_page' ],
			'multiple' => 1,
			'choices'  => $published_pages,
		),
		/***** Trending Posts Count */
		array(
			'settings'    => 'extras_post_ranking_system_trending_posts_count',
			'label'       => __( 'Posts Count', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 80,
			'default'     => $defaults[ 'extras_post_ranking_system_trending_posts_count' ],
			'choices' => array(
				'min'  => -1,
				'step' => 1,
			),
		),
		/***** Minimal Trending Score */
		array(
			'settings'    => 'extras_post_ranking_system_trending_minimal_score',
			'label'       => __( 'Minimal Trending Score', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 90,
			'default'     => $defaults[ 'extras_post_ranking_system_trending_minimal_score' ],
			'choices' => array(
				'min'  => 1,
				'step' => 1,
			),
		),
		/***** Trending Badge */
		array(
			'settings' => 'extras_post_ranking_system_trending_badge',
			'label'    => __( 'Trending Badge', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 100,
			'default'  => $defaults[ 'extras_post_ranking_system_trending_badge' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** "Hot" Heading */
		array(
			'settings'    => 'extras_post_ranking_system_hot_heading',
			'label'       => sprintf( '<h2>%s</h2>', __( 'Hot', 'boombox' ) ),
			'section'     => $section,
			'type'        => 'custom',
			'priority'    => 110,
			'description' => __( 'Time Range: Last 7 days', 'boombox' ) . '<hr />',
		),
		/***** Disable Hot */
		array(
			'settings' => 'extras_post_ranking_system_hot_enable',
			'label'    => __( 'Hot', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 110,
			'default'  => $defaults[ 'extras_post_ranking_system_hot_enable' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Hot Page */
		array(
			'settings' => 'extras_post_ranking_system_hot_page',
			'label'    => __( 'Hot page', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 120,
			'default'  => $defaults[ 'extras_post_ranking_system_hot_page' ],
			'multiple' => 1,
			'choices'  => $published_pages,
		),
		/***** Hot Posts Count */
		array(
			'settings'    => 'extras_post_ranking_system_hot_posts_count',
			'label'       => __( 'Posts Count', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 130,
			'default'     => $defaults[ 'extras_post_ranking_system_hot_posts_count' ],
			'choices' => array(
				'min'  => -1,
				'step' => 1,
			),
		),
		/***** Minimal Hot Score */
		array(
			'settings'    => 'extras_post_ranking_system_hot_minimal_score',
			'label'       => __( 'Minimal Hot Score', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 140,
			'default'     => $defaults[ 'extras_post_ranking_system_hot_minimal_score' ],
			'choices' => array(
				'min'  => 1,
				'step' => 1,
			),
		),
		/***** Hot Badge */
		array(
			'settings' => 'extras_post_ranking_system_hot_badge',
			'label'    => __( 'Hot Badge', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 150,
			'default'  => $defaults[ 'extras_post_ranking_system_hot_badge' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** "Popular" Heading */
		array(
			'settings'    => 'extras_post_ranking_system_popular_heading',
			'label'       => sprintf( '<h2>%s</h2>', __( 'Popular', 'boombox' ) ),
			'section'     => $section,
			'type'        => 'custom',
			'priority'    => 160,
			'description' => __( 'Time Range: Last 30 days', 'boombox' ) . '<hr />',
		),
		/***** Disable Popular */
		array(
			'settings' => 'extras_post_ranking_system_popular_enable',
			'label'    => __( 'Popular', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 160,
			'default'  => $defaults[ 'extras_post_ranking_system_popular_enable' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Popular Page */
		array(
			'settings' => 'extras_post_ranking_system_popular_page',
			'label'    => __( 'Popular page', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 170,
			'default'  => $defaults[ 'extras_post_ranking_system_popular_page' ],
			'multiple' => 1,
			'choices'  => $published_pages,
		),
		/***** Popular Posts Count */
		array(
			'settings'    => 'extras_post_ranking_system_popular_posts_count',
			'label'       => __( 'Posts Count', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 180,
			'default'     => $defaults[ 'extras_post_ranking_system_popular_posts_count' ],
			'choices' => array(
				'min'  => -1,
				'step' => 1,
			),
		),
		/***** Minimal Popular Score */
		array(
			'settings'    => 'extras_post_ranking_system_popular_minimal_score',
			'label'       => __( 'Minimal Popular Score', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 190,
			'default'     => $defaults[ 'extras_post_ranking_system_popular_minimal_score' ],
			'choices' => array(
				'min'  => 1,
				'step' => 1,
			),
		),
		/***** Popular Badge */
		array(
			'settings' => 'extras_post_ranking_system_popular_badge',
			'label'    => __( 'Popular Badge', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 200,
			'default'  => $defaults[ 'extras_post_ranking_system_popular_badge' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Fake Views Count */
		array(
			'settings'    => 'extras_post_ranking_system_fake_views_count',
			'label'       => __( 'Fake Views Count', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 210,
			'default'     => $defaults[ 'extras_post_ranking_system_fake_views_count' ],
			'choices' => array(
				'min'  => 0,
				'step' => 1,
			),
		),
		/***** Fake Points Count */
		array(
			'settings'    => 'extras_post_ranking_system_fake_points_count',
			'label'       => __( 'Fake Points Count', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 220,
			'default'     => $defaults[ 'extras_post_ranking_system_fake_points_count' ],
			'choices' => array(
				'min'  => 0,
				'step' => 1,
			),
		),
		/***** View Count Scale */
		array(
			'settings'    => 'extras_post_ranking_system_views_count_scale',
			'label'       => __( 'Views Count Scale', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 230,
			'default'     => $defaults[ 'extras_post_ranking_system_views_count_scale' ],
			'choices' => array(
				'min'  => 1,
				'step' => 1,
			),
		),
		/***** View Count Style */
		array(
			'settings' => 'extras_post_ranking_system_views_count_style',
			'label'    => __( 'Views Count Style', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 240,
			'default'  => $defaults[ 'extras_post_ranking_system_views_count_style' ],
			'multiple' => 1,
			'choices'  => $choices_helper->get_view_count_style_choices(),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/extras_posts_ranking_system', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_extras_posts_ranking_system_fields', 10, 2 );