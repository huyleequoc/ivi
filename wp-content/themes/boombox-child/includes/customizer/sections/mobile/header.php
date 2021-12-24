<?php
/**
 * WP Customizer panel section to handle "Mobile->Header" section
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
 * Get "Mobile->Header" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_mobile_header_section_id() {
	return 'boombox_mobile_header';
}

/**
 * Register "Mobile->Header" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_mobile_header_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_mobile_header_section_id(),
		'args' => array(
			'title'      => __( 'Header', 'boombox' ),
			'panel'      => 'boombox_mobile',
			'priority'   => 30,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_mobile_header_section', 10, 1 );

/**
 * Register fields for "Mobile->Header" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_mobile_header_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_mobile_header_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();

	$header_components_choices = $choices_helper->get_mobile_header_composition_component_choices();
	$components = boombox_get_theme_option( 'mobile_header_components' );

	$left = isset( $components[ 'left' ] ) ? array_flip( (array)$components[ 'left' ] ) : array();
	$header_components_choices = array_diff_key( $header_components_choices, $left );

	$right = isset( $components[ 'right' ] ) ? array_flip( (array)$components[ 'right' ] ) : array();
	$header_components_choices = array_diff_key( $header_components_choices, $right );

	$custom_fields = array(
		/***** Composition */
		array(
			'settings' => 'mobile_header_composition',
			'label'    => __( 'Composition', 'boombox' ),
			'section'  => $section,
			'type'     => 'radio-image',
			'priority' => 20,
			'default'  => $defaults[ 'mobile_header_composition' ],
			'choices'  => $choices_helper->get_mobile_header_composition_choices(),
		),
		/***** Components */
		array(
			'settings' => 'mobile_header_components',
			'label'    => __( 'Components', 'boombox' ),
			'section'  => $section,
			'type'     => 'bb-composition-sortable-slave',
			'priority' => 30,
			'default'  => $defaults[ 'mobile_header_components' ],
		),
		/***** Unused Components */
		array(
			'settings' => 'mobile_header_composition_sortable_master',
			'label'    => __( 'Unused Components', 'boombox' ),
			'section'  => $section,
			'type'     => 'bb-composition-sortable-master',
			'priority' => 30,
			'choices'  => $header_components_choices,
		),
		/***** Sticky */
		array(
			'settings' => 'mobile_header_sticky',
			'label'    => __( 'Sticky', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 40,
			'default'  => $defaults[ 'mobile_header_sticky' ],
			'choices'  => array(
				'classic' => __( 'Classic', 'boombox' ),
				'smart'   => __( 'Smart', 'boombox' ),
				'none'    => __( 'None', 'boombox' ),
			),
		),
		/***** Mobile Logo */
		array(
			'settings' => 'mobile_header_logo',
			'label'    => __( 'Mobile Logo', 'boombox' ),
			'section'  => $section,
			'type'     => 'image',
			'priority' => 50,
			'default'  => $defaults[ 'mobile_header_logo' ],
		),
		/***** Mobile Logo Width */
		array(
			'settings'    => 'mobile_header_logo_width',
			'label'       => __( 'Mobile Logo Width', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'choices' => array(
				'min'  => 0,
				'step' => 1,
			),
			'priority'    => 60,
			'default'     => $defaults[ 'mobile_header_logo_width' ],
		),
		/***** Mobile Logo Height */
		array(
			'settings'    => 'mobile_header_logo_height',
			'label'       => __( 'Mobile Logo Height', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'choices' => array(
				'min'  => 0,
				'step' => 1,
			),
			'priority'    => 70,
			'default'     => $defaults[ 'mobile_header_logo_height' ],
		),
		/***** Mobile Logo Margin Top */
		array(
			'settings'    => 'mobile_header_logo_margin_top',
			'label'       => __( 'Mobile Logo Margin Top (px)', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 80,
			'default'     => $defaults[ 'mobile_header_logo_margin_top' ],
			'choices' => array(
				'min'  => 0,
				'step' => 1,
			),
		),
		/***** Mobile Logo Margin Bottom */
		array(
			'settings'    => 'mobile_header_logo_margin_bottom',
			'label'       => __( 'Mobile Logo Margin Bottom (px)', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 90,
			'default'     => $defaults[ 'mobile_header_logo_margin_bottom' ],
			'choices' => array(
				'min'  => 0,
				'step' => 1,
			),
		),
		/***** Mobile Logo HD */
		array(
			'settings' => 'mobile_header_logo_hdpi',
			'label'    => __( 'Mobile Logo HD', 'boombox' ),
			'section'  => $section,
			'type'     => 'image',
			'priority' => 100,
			'default'  => $defaults[ 'mobile_header_logo_hdpi' ],
		),
		/***** Background Color */
		array(
			'settings' => 'mobile_header_bg_color',
			'label'    => __( 'Background Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 110,
			'choices'  => array(
				'alpha' => false,
			),
			'default'  => $defaults[ 'mobile_header_bg_color' ],
		),
		/***** Gradient Color */
		array(
			'settings' => 'mobile_header_gradient_color',
			'label'    => __( 'Gradient Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 120,
			'choices'  => array(
				'alpha' => false,
			),
			'default'  => $defaults[ 'mobile_header_gradient_color' ],
		),
		/***** Text Color */
		array(
			'settings' => 'mobile_header_text_color',
			'label'    => __( 'Text Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 130,
			'choices'  => array(
				'alpha' => false,
			),
			'default'  => $defaults[ 'mobile_header_text_color' ],
		),
		/***** Address Bar Color */
		array(
			'settings' => 'mobile_header_address_bar_color',
			'label'    => __( 'Browser Address Bar Color', 'boombox' ),
			'section'  => $section,
			'type'     => 'color',
			'priority' => 140,
			'choices'  => array(
				'alpha' => false,
			),
			'default'  => $defaults[ 'mobile_header_address_bar_color' ],
		),
		/***** Mobile Header in dark/light mode */
		array(
			'settings'    => 'mobile_header_include_in_color_mode',
			'label'       => __( 'Include Mobile Header in dark/light mode', 'boombox' ),
			'section'     => $section,
			'type'        => 'switch',
			'priority'    => 150,
			'default'     => $defaults[ 'mobile_header_include_in_color_mode' ],
			'choices'     => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/mobile_header', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_mobile_header_fields', 10, 2 );