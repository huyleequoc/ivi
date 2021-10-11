<?php
/**
 * WP Customizer panel section to handle "Header->Layout" section
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
 * Get "Header->Layout" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_header_layout_section_id() {
	return 'boombox_header_layout';
}

/**
 * Register "Header->Layout" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_header_layout_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_header_layout_section_id(),
		'args' => array(
			'title'      => __( 'Layout', 'boombox' ),
			'panel'      => 'boombox_header',
			'priority'   => 20,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_header_layout_section', 10, 1 );

/**
 * Register fields for "Header->Layout" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_header_layout_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_header_layout_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();

	$header_components_choices = $choices_helper->get_header_composition_component_choices();
	$top_components = boombox_get_theme_option( 'header_layout_top_components' );

	$t_left = isset( $top_components[ 'left' ] ) ? array_flip( (array)$top_components[ 'left' ] ) : array();
	$header_components_choices = array_diff_key( $header_components_choices, $t_left );

	$t_right = isset( $top_components[ 'right' ] ) ? array_flip( (array)$top_components[ 'right' ] ) : array();
	$header_components_choices = array_diff_key( $header_components_choices, $t_right );


	$bottom_components = boombox_get_theme_option( 'header_layout_bottom_components' );
	$b_left = isset( $bottom_components[ 'left' ] ) ? array_flip( (array)$bottom_components[ 'left' ] ) : array();
	$header_components_choices = array_diff_key( $header_components_choices, $b_left );

	$b_right = isset( $bottom_components[ 'right' ] ) ? array_flip( (array)$bottom_components[ 'right' ] ) : array();
	$header_components_choices = array_diff_key( $header_components_choices, $b_right );

	$custom_fields = array(
		/***** Logo Position */
		array(
			'settings' => 'header_layout_logo_position',
			'label'    => __( 'Logo Position', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 20,
			'default'  => $defaults[ 'header_layout_logo_position' ],
			'multiple' => 1,
			'choices'  => array(
				'top'    => __( 'Top Layer', 'boombox' ),
				'bottom' => __( 'Bottom Layer', 'boombox' ),
			),
		),
		/***** "Top Layer" heading */
		array(
			'settings' => 'header_layout_top_layer_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 30,
			'default'  => sprintf( '<div class="bb-sub-section"><h3>%s</h3><hr></div>', __( 'Top Layer', 'boombox' ) ),
		),
		/***** Enable Top Layer */
		array(
			'settings' => 'header_layout_top_header',
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 30,
			'default'  => $defaults[ 'header_layout_top_header' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Top Layer Composition */
		array(
			'settings'        => 'header_layout_top_layer_composition',
			'label'           => __( 'Composition', 'boombox' ),
			'section'         => $section,
			'type'            => 'radio-image',
			'priority'        => 40,
			'default'         => $defaults[ 'header_layout_top_layer_composition' ],
			'multiple'        => 1,
			'choices'         => $choices_helper->get_header_compositions(),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout_top_header',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Top Layer Width */
		array(
			'settings'        => 'header_layout_top_header_width',
			'label'           => __( 'Width', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 50,
			'default'         => $defaults[ 'header_layout_top_header_width' ],
			'multiple'        => 1,
			'choices'         => array(
				'full-width' => __( 'Full width', 'boombox' ),
				'boxed'      => __( 'Boxed', 'boombox' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout_top_header',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Top Layer Height */
		array(
			'settings'        => 'header_layout_top_header_height',
			'label'           => __( 'Height', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 60,
			'default'         => $defaults[ 'header_layout_top_header_height' ],
			'multiple'        => 1,
			'choices'         => array(
				'small'  => __( 'Small', 'boombox' ),
				'medium' => __( 'Medium', 'boombox' ),
				'large'  => __( 'Large', 'boombox' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout_top_header',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** "Bottom Layer" heading */
		array(
			'settings' => 'header_layout_bottom_layer_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 70,
			'default'  => sprintf( '<div class="bb-sub-section"><h3>%s</h3><hr></div>', __( 'Bottom Layer', 'boombox' ) ),
		),
		/***** Enable Bottom Layer */
		array(
			'settings' => 'header_layout_bottom_header',
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 70,
			'default'  => $defaults[ 'header_layout_bottom_header' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Bottom Layer Composition */
		array(
			'settings'        => 'header_layout_bottom_layer_composition',
			'label'           => __( 'Composition', 'boombox' ),
			'section'         => $section,
			'type'            => 'radio-image',
			'priority'        => 80,
			'default'         => $defaults[ 'header_layout_bottom_layer_composition' ],
			'multiple'        => 1,
			'choices'         => $choices_helper->get_header_compositions(),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout_bottom_header',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Bottom Layer Width */
		array(
			'settings'        => 'header_layout_bottom_header_width',
			'label'           => __( 'Width', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 90,
			'default'         => $defaults[ 'header_layout_bottom_header_width' ],
			'multiple'        => 1,
			'choices'         => array(
				'full-width' => __( 'Full width', 'boombox' ),
				'boxed'      => __( 'Boxed', 'boombox' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout_bottom_header',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Bottom Layer Height */
		array(
			'settings'        => 'header_layout_bottom_header_height',
			'label'           => __( 'Height', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 100,
			'default'         => $defaults[ 'header_layout_bottom_header_height' ],
			'multiple'        => 1,
			'choices'         => array(
				'small'  => __( 'Small', 'boombox' ),
				'medium' => __( 'Medium', 'boombox' ),
				'large'  => __( 'Large', 'boombox' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout_bottom_header',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** "Components" heading */
		array(
			'settings' => 'header_layout_components_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 110,
			'default'  => sprintf( '<div class="bb-sub-section"><h3>%s</h3><hr></div>', __( 'Components Positions', 'boombox' ) ),
		),
		/***** Top Layer Components */
		array(
			'settings'        => 'header_layout_top_components',
			'label'           => __( 'Top Layer Components', 'boombox' ),
			'section'         => $section,
			'type'            => 'bb-composition-sortable-slave',
			'priority'        => 110,
			'default'         => $defaults[ 'header_layout_top_components' ],
			'active_callback' => array(
				array(
					'setting'  => 'header_layout_top_header',
					'value'    => 1,
					'operator' => '==',
				),
			),
			'components_dependencies' => array(
				array(
					'component' => 'badges',
					'setting'   => 'header_layout_badges_position',
					'value'     => 'inside',
					'operator'  => '!=',
				),
			),
		),
		/***** Components Choices */
		array(
			'settings'                => 'header_layout_header_composition_sortable_master',
			'label'                   => __( 'Unused Components', 'boombox' ),
			'section'                 => $section,
			'type'                    => 'bb-composition-sortable-master',
			'priority'                => 110,
			'choices'                 => $header_components_choices,
			'active_callback'         => array(
				array(
					array(
						'setting'  => 'header_layout_top_header',
						'value'    => 1,
						'operator' => '==',
					),
					array(
						'setting'  => 'header_layout_bottom_header',
						'value'    => 1,
						'operator' => '==',
					),
				),
			),
			'components_dependencies' => array(
				array(
					'component' => 'badges',
					'setting'   => 'header_layout_badges_position',
					'value'     => 'inside',
					'operator'  => '!=',
				),
			),
		),
		/***** Bottom Layer Components */
		array(
			'settings'        => 'header_layout_bottom_components',
			'label'           => __( 'Bottom Layer Components', 'boombox' ),
			'section'         => $section,
			'type'            => 'bb-composition-sortable-slave',
			'priority'        => 110,
			'default'         => $defaults[ 'header_layout_bottom_components' ],
			'active_callback' => array(
				array(
					'setting'  => 'header_layout_bottom_header',
					'value'    => 1,
					'operator' => '==',
				),
			),
			'components_dependencies' => array(
				array(
					'component' => 'badges',
					'setting'   => 'header_layout_badges_position',
					'value'     => 'inside',
					'operator'  => '!=',
				),
			),
		),
		/***** "Other Options" heading */
		array(
			'settings' => 'header_layout_other_settings_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 120,
			'default'  => '<hr>',
		),
		/***** Badges Menu Location */
		array(
			'settings' => 'header_layout_badges_position',
			'label'    => __( 'Badges Menu Location', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 120,
			'default'  => $defaults[ 'header_layout_badges_position' ],
			'multiple' => 1,
			'choices'  => array(
				'inside'  => __( 'Inside Header', 'boombox' ),
				'outside' => __( 'Outside Header', 'boombox' ),
				'none'    => __( 'None', 'boombox' ),
			),
		),
		/***** "More" Button */
		array(
			'settings' => 'header_layout_more_menu_position',
			'label'    => __( '"More" Button', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 130,
			'default'  => $defaults[ 'header_layout_more_menu_position' ],
			'multiple' => 1,
			'choices'  => array(
				'top'    => __( 'After Top Menu', 'boombox' ),
				'bottom' => __( 'After Bottom Menu', 'boombox' ),
				'none'   => __( 'None', 'boombox' ),
			),
		),
		/***** Community Text */
		array(
			'settings' => 'header_layout_community_text',
			'label'    => __( 'Community Text', 'boombox' ),
			'section'  => $section,
			'type'     => 'text',
			'priority' => 140,
			'default'  => $defaults[ 'header_layout_community_text' ],
		),
		/***** Sticky */
		array(
			'settings' => 'header_layout_sticky_header',
			'label'    => __( 'Sticky', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 150,
			'default'  => $defaults[ 'header_layout_sticky_header' ],
			'multiple' => 1,
			'choices'  => array(
				'top'    => __( 'Top Layer', 'boombox' ),
				'bottom' => __( 'Bottom Layer', 'boombox' ),
				'both'   => __( 'Both', 'boombox' ),
				'none'   => __( 'None', 'boombox' ),
			),
		),
		/***** Sticky Type */
		array(
			'settings' => 'header_layout_sticky_type',
			'label'    => __( 'Sticky Type', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 160,
			'default'  => $defaults[ 'header_layout_sticky_type' ],
			'multiple' => 1,
			'choices'  => array(
				'classic'    => __( 'Classic', 'boombox' ),
				'smart' => __( 'Smart', 'boombox' )
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout_sticky_header',
					'value'    => 'none',
					'operator' => '!=',
				),
			),
		),
		/***** Logo Margin Top */
		array(
			'settings'    => 'header_layout_logo_margin_top',
			'label'       => __( 'Logo Margin Top (px)', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 170,
			'default'     => $defaults[ 'header_layout_logo_margin_top' ],
			'choices' => array(
				'min'  => 0,
				'step' => 1,
			),
		),
		/***** Logo Margin Bottom */
		array(
			'settings'    => 'header_layout_logo_margin_bottom',
			'label'       => __( 'Logo Margin Bottom (px)', 'boombox' ),
			'section'     => $section,
			'type'        => 'number',
			'priority'    => 180,
			'default'     => $defaults[ 'header_layout_logo_margin_bottom' ],
			'choices' => array(
				'min'  => 0,
				'step' => 1,
			),
		),

		/***** "Compose Button" heading */
		array(
			'settings' => 'header_layout_compose_btn_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 190,
			'default'  => sprintf( '<div class="bb-sub-section"><h3>%s</h3><hr></div>', __( 'Compose Button', 'boombox' ) ),
		),
		/***** Enable Compose Button */
		array(
			'settings' => 'header_layout_button',
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 200,
			'default'  => $defaults[ 'header_layout_button' ],
			'choices'  => array(
				'on'  => esc_attr__( 'Show', 'boombox' ),
				'off' => esc_attr__( 'Hide', 'boombox' ),
			),
		),
		/***** Compose Button Text */
		array(
			'settings' => 'header_layout_button_text',
			'label'    => __( '"Compose" Button Text', 'boombox' ),
			'section'  => $section,
			'type'     => 'text',
			'priority' => 210,
			'default'  => $defaults[ 'header_layout_button_text' ],
			'active_callback' => array(
				array(
					'setting'  => 'header_layout_button',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Compose Button Link */
		array(
			'settings' => 'header_layout_button_link',
			'label'    => __( '"Compose" Button Link', 'boombox' ),
			'section'  => $section,
			'type'     => 'text',
			'priority' => 220,
			'default'  => $defaults[ 'header_layout_button_link' ],
			'active_callback' => array(
				array(
					'setting'  => 'header_layout_button',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Plus Icon On Button */
		array(
			'settings' => 'header_layout_button_plus_icon',
			'label'    => __( 'Plus Icon On Button', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 230,
			'default'  => $defaults[ 'header_layout_button_plus_icon' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout_button',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/header_layout', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_header_layout_fields', 10, 2 );