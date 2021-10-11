<?php
/**
 * WP Customizer panel section to handle "Extras->Authentication" section
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
 * Get "Extras->Authentication" section id
 * @return string
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_get_extras_authentication_section_id() {
	return 'boombox_extras_authentication';
}

/**
 * Register "Extras->Authentication" section
 *
 * @param array $sections Current sections
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_extras_authentication_section( $sections ) {

	$sections[] = array(
		'id'   => boombox_customizer_get_extras_authentication_section_id(),
		'args' => array(
			'title'      => __( 'Authentication', 'boombox' ),
			'panel'      => 'boombox_extras',
			'priority'   => 20,
			'capability' => 'edit_theme_options',
		),
	);

	return $sections;
}

add_filter( 'boombox/customizer/register/sections', 'boombox_customizer_register_extras_authentication_section', 10, 1 );

/**
 * Register fields for "Extras->Authentication" section
 *
 * @param array $fields   Current fields configuration
 * @param array $defaults Array containing default values
 *
 * @return array
 *
 * @since   2.0.0
 * @version 2.0.0
 */
function boombox_customizer_register_extras_authentication_fields( $fields, $defaults ) {

	$section = boombox_customizer_get_extras_authentication_section_id();
	$choices_helper = Boombox_Choices_Helper::get_instance();
	$published_pages = $choices_helper->get_published_pages();

	$custom_fields = array(
		/***** Site Authentication */
		array(
			'settings' => 'extra_authentication_enable_site_auth',
			'label'    => __( 'Site Authentication', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 20,
			'default'  => $defaults[ 'extra_authentication_enable_site_auth' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Login Popup Heading */
		array(
			'settings'        => 'extra_authentication_login_popup_title',
			'label'           => __( 'Login Popup Heading', 'boombox' ),
			'section'         => $section,
			'type'            => 'text',
			'priority'        => 30,
			'default'         => $defaults[ 'extra_authentication_login_popup_title' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Login Popup Text */
		array(
			'settings'        => 'extra_authentication_login_popup_text',
			'label'           => __( 'Login Popup Text', 'boombox' ),
			'section'         => $section,
			'type'            => 'textarea',
			'priority'        => 40,
			'default'         => $defaults[ 'extra_authentication_login_popup_text' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** "Remember Me" */
		array(
			'settings'        => 'extra_authentication_enable_remember_me',
			'label'           => __( '"Remember Me" On Login', 'boombox' ),
			'section'         => $section,
			'type'            => 'switch',
			'priority'        => 50,
			'default'         => $defaults[ 'extra_authentication_enable_remember_me' ],
			'choices'         => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Custom Registration URL */
		array(
			'settings'        => 'extra_authentication_registration_custom_url',
			'label'           => __( '"Sign Up" Button URL', 'boombox' ),
			'description'     => __( 'Leave empty to use native registration.', 'boombox' ),
			'section'         => $section,
			'type'            => 'text',
			'priority'        => 60,
			'default'         => $defaults[ 'extra_authentication_registration_custom_url' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Registration Popup Heading */
		array(
			'settings'        => 'extra_authentication_registration_popup_title',
			'label'           => __( 'Registration Popup Heading', 'boombox' ),
			'section'         => $section,
			'type'            => 'text',
			'priority'        => 70,
			'default'         => $defaults[ 'extra_authentication_registration_popup_title' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Registration Popup Text */
		array(
			'settings'        => 'extra_authentication_registration_popup_text',
			'label'           => __( 'Registration Popup Text', 'boombox' ),
			'section'         => $section,
			'type'            => 'textarea',
			'priority'        => 80,
			'default'         => $defaults[ 'extra_authentication_registration_popup_text' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Forgot Password Popup Heading */
		array(
			'settings'        => 'extra_authentication_forgot_password_popup_title',
			'label'           => __( 'Forgot Password Popup Heading', 'boombox' ),
			'section'         => $section,
			'type'            => 'text',
			'priority'        => 90,
			'default'         => $defaults[ 'extra_authentication_forgot_password_popup_title' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Forgot Popup Text */
		array(
			'settings'        => 'extra_authentication_forgot_password_popup_text',
			'label'           => __( 'Forgot Password Popup Text', 'boombox' ),
			'section'         => $section,
			'type'            => 'textarea',
			'priority'        => 100,
			'default'         => $defaults[ 'extra_authentication_forgot_password_popup_text' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Reset Password Popup Heading */
		array(
			'settings'        => 'extra_authentication_reset_password_popup_title',
			'label'           => __( 'Reset Password Popup Heading', 'boombox' ),
			'section'         => $section,
			'type'            => 'text',
			'priority'        => 90,
			'default'         => $defaults[ 'extra_authentication_reset_password_popup_title' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Reset Popup Text */
		array(
			'settings'        => 'extra_authentication_reset_password_popup_text',
			'label'           => __( 'Reset Password Popup Text', 'boombox' ),
			'section'         => $section,
			'type'            => 'textarea',
			'priority'        => 100,
			'default'         => $defaults[ 'extra_authentication_reset_password_popup_text' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** "Terms Of Use" Page */
		array(
			'settings' => 'extra_authentication_terms_of_use_page',
			'label'    => __( '"Terms Of Use" Page', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 130,
			'default'  => $defaults[ 'extra_authentication_terms_of_use_page' ],
			'multiple' => 1,
			'choices'  => $published_pages,
		),
		/***** "Privacy Policy" Page */
		array(
			'settings' => 'extra_authentication_privacy_policy_page',
			'label'    => __( '"Privacy Policy" Page', 'boombox' ),
			'section'  => $section,
			'type'     => 'select',
			'priority' => 140,
			'default'  => $defaults[ 'extra_authentication_privacy_policy_page' ],
			'multiple' => 1,
			'choices'  => $published_pages,
		),
		/***** Captcha On Login */
		array(
			'settings'        => 'extra_authentication_enable_login_captcha',
			'label'           => __( 'Captcha On Login', 'boombox' ),
			'section'         => $section,
			'type'            => 'switch',
			'priority'        => 150,
			'default'         => $defaults[ 'extra_authentication_enable_login_captcha' ],
			'choices'         => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Captcha On Registration */
		array(
			'settings'        => 'extra_authentication_enable_registration_captcha',
			'label'           => __( 'Captcha On Registration', 'boombox' ),
			'section'         => $section,
			'type'            => 'switch',
			'priority'        => 160,
			'default'         => $defaults[ 'extra_authentication_enable_registration_captcha' ],
			'choices'         => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Captcha Type */
		array(
			'settings'        => 'extra_authentication_captcha_type',
			'label'           => __( 'Captcha Type', 'boombox' ),
			'section'         => $section,
			'type'            => 'select',
			'priority'        => 170,
			'default'         => $defaults[ 'extra_authentication_captcha_type' ],
			'multiple'        => 1,
			'choices'         => $choices_helper->get_captcha_type_choices(),
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Google Recaptcha Site Key */
		array(
			'settings'        => 'extra_authentication_google_recaptcha_site_key',
			'label'           => __( 'Google Recaptcha Site Key', 'boombox' ),
			'section'         => $section,
			'type'            => 'text',
			'priority'        => 180,
			'default'         => $defaults[ 'extra_authentication_google_recaptcha_site_key' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
				array(
					'setting'  => 'extra_authentication_captcha_type',
					'value'    => 'google',
					'operator' => '==',
				),
			),
		),
		/***** Google Recaptcha Secret Key */
		array(
			'settings'        => 'extra_authentication_google_recaptcha_secret_key',
			'label'           => __( 'Google Recaptcha Secret Key', 'boombox' ),
			'section'         => $section,
			'type'            => 'text',
			'priority'        => 190,
			'default'         => $defaults[ 'extra_authentication_google_recaptcha_secret_key' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
				array(
					'setting'  => 'extra_authentication_captcha_type',
					'value'    => 'google',
					'operator' => '==',
				),
			),
		),

		/***** Google Recaptcha V3 Site Key */
		array(
			'settings'        => 'extra_authentication_google_v3_recaptcha_site_key',
			'label'           => __( 'Google Recaptcha V3 Site Key', 'boombox' ),
			'section'         => $section,
			'type'            => 'text',
			'priority'        => 200,
			'default'         => $defaults[ 'extra_authentication_google_v3_recaptcha_site_key' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
				array(
					'setting'  => 'extra_authentication_captcha_type',
					'value'    => 'google_v3',
					'operator' => '==',
				),
			),
		),


		/***** Google Recaptcha V3 Secret Key */
		array(
			'settings'        => 'extra_authentication_google_v3_recaptcha_secret_key',
			'label'           => __( 'Google Recaptcha V3 Secret Key', 'boombox' ),
			'section'         => $section,
			'type'            => 'text',
			'priority'        => 210,
			'default'         => $defaults[ 'extra_authentication_google_v3_recaptcha_secret_key' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_site_auth',
					'value'    => 1,
					'operator' => '==',
				),
				array(
					'setting'  => 'extra_authentication_captcha_type',
					'value'    => 'google_v3',
					'operator' => '==',
				),
			),
		),

		/***** Social Authentication Heading */
		array(
			'settings' => 'extra_authentication_social_heading',
			'section'  => $section,
			'type'     => 'custom',
			'priority' => 220,
			'default'  => sprintf( '<h2>%s</h2><hr/>', __( 'Social Authentication', 'boombox' ) ),
		),
		/***** Social Authentication */
		array(
			'settings' => 'extra_authentication_enable_social_auth',
			'label'    => __( 'Social Authentication', 'boombox' ),
			'section'  => $section,
			'type'     => 'switch',
			'priority' => 220,
			'default'  => $defaults[ 'extra_authentication_enable_social_auth' ],
			'choices'  => array(
				'on'  => esc_attr__( 'On', 'boombox' ),
				'off' => esc_attr__( 'Off', 'boombox' ),
			),
		),
		/***** Facebook App ID */
		array(
			'settings'        => 'extra_authentication_facebook_app_id',
			'label'           => __( 'Facebook App ID', 'boombox' ),
			'section'         => $section,
			'type'            => 'text',
			'priority'        => 230,
			'default'         => $defaults[ 'extra_authentication_facebook_app_id' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_social_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Google oauth ID */
		array(
			'settings'        => 'extra_authentication_google_oauth_id',
			'label'           => __( 'Google oauth Client ID', 'boombox' ),
			'section'         => $section,
			'type'            => 'text',
			'priority'        => 240,
			'default'         => $defaults[ 'extra_authentication_google_oauth_id' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_social_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Google API Key */
		array(
			'settings'        => 'extra_authentication_google_api_key',
			'label'           => __( 'Google API Key', 'boombox' ),
			'section'         => $section,
			'type'            => 'text',
			'priority'        => 250,
			'default'         => $defaults[ 'extra_authentication_google_api_key' ],
			'active_callback' => array(
				array(
					'setting'  => 'extra_authentication_enable_social_auth',
					'value'    => 1,
					'operator' => '==',
				),
			),
		),
		/***** Other fields need to go here */
	);

	/***** Let others to add fields to this section */
	$custom_fields = apply_filters( 'boombox/customizer/fields/extras_authentication', $custom_fields, $section, $defaults );

	return array_merge( $fields, $custom_fields );
}

add_filter( 'boombox/customizer/register/fields', 'boombox_customizer_register_extras_authentication_fields', 10, 2 );