<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class Boombox_Woocommerce_Customizer {

	/**
	 * Holds unique instance
	 *
	 * @var Boombox_Woocommerce_Customizer
	 */
	private static $_instance;

	/**
	 * Get unique instance
	 *
	 * @return Boombox_Woocommerce_Customizer
	 */
	public static function get_instance() {
		if ( null === static::$_instance ) {
			static::$_instance = new self();
		}

		return static::$_instance;
	}

	/**
	 * Boombox_Woocommerce_Customizer constructor.
	 */
	private function __construct() {
		$this->hooks();
	}

	/**
	 * Prevent cloning
	 */
	private function __clone() {
	}

	/**
	 * Add required actions
	 */
	private function hooks() {
		add_action( 'customize_register', array( $this, 'register_customizer' ), 20 );
	}

	/**
	 * Callback for customizer register
	 *
	 * @hooked in $wp_customize "customize_register" action
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer instance
	 */
	public function register_customizer( $wp_customize ) {
		$this->add_product_section( $wp_customize );
	}

	/**
	 * Add single product section
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer instance
	 */
	public function add_product_section( $wp_customize ) {

		$choices_helper = Boombox_Choices_Helper::get_instance();

		// region Single Product section
		$wp_customize->add_section(
			'boombox_woocommerce_single_product',
			array(
				'title'    => __( 'Single Product', 'boombox' ),
				'priority' => 50,
				'panel'    => 'woocommerce',
			)
		);
		// endregion

		// region Sidebar Type
		$wp_customize->add_setting(
			'boombox_woocommerce_sidebar_type',
			array(
				'default'           => '1-sidebar-1_3',
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => array( $this, 'boombox_woocommerce_sanitize_select' ),
			)
		);

		$wp_customize->add_control(
			'boombox_woocommerce_sidebar_type',
			array(
				'label'       => __( 'Sidebar Type', 'boombox' ),
				'description' => __( 'Choose sidebar type for single product.', 'boombox' ),
				'section'     => 'boombox_woocommerce_single_product',
				'settings'    => 'boombox_woocommerce_sidebar_type',
				'type'        => 'select',
				'choices'     => array(
					'1-sidebar-1_3'        => __( '1 Sidebar - 1/3', 'boombox' ),
					'1-sidebar-1_4'        => __( '1 Sidebar - 1/4', 'boombox' ),
					'2-sidebars-1_4-1_4'   => __( '2 Sidebars - 1/4 - 1/4', 'boombox' ),
					'2-sidebars-small-big' => __( '2 Sidebars - Small/Big', 'boombox' ),
					'no-sidebar'           => __( 'No Sidebar', 'boombox' ),
				),
			)
		);
		// endregion

		// region Sidebar Orientation
		$wp_customize->add_setting(
			'boombox_woocommerce_sidebar_orientation',
			array(
				'default'           => 'right',
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => array( $this, 'boombox_woocommerce_sanitize_select' ),
			)
		);

		$wp_customize->add_control(
			'boombox_woocommerce_sidebar_orientation',
			array(
				'label'       => __( 'Sidebar Orientation', 'boombox' ),
				'description' => __( 'Choose sidebar orientation for single product.', 'boombox' ),
				'section'     => 'boombox_woocommerce_single_product',
				'settings'    => 'boombox_woocommerce_sidebar_orientation',
				'type'        => 'select',
				'choices'     => array(
					'right' => __( 'Right', 'boombox' ),
					'left'  => __( 'Left', 'boombox' ),
				),
			)
		);
		// endregion

		// region Primary Sidebar
		$wp_customize->add_setting(
			'boombox_woocommerce_primary_sidebar',
			array(
				'default'           => Boombox_Woocommerce::get_instance()->get_single_product_primary_sidebar( '' ),
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => array( $this, 'boombox_woocommerce_sanitize_select' ),
			)
		);

		$wp_customize->add_control(
			'boombox_woocommerce_primary_sidebar',
			array(
				'label'       => __( 'Primary Sidebar', 'boombox' ),
				'description' => __( 'Choose sidebar orientation for single product.', 'boombox' ),
				'section'     => 'boombox_woocommerce_single_product',
				'settings'    => 'boombox_woocommerce_primary_sidebar',
				'type'        => 'select',
				'choices'     => $choices_helper->get_primary_sidebars(),
			)
		);
		// endregion

		// region Secondary Sidebar
		$wp_customize->add_setting(
			'boombox_woocommerce_secondary_sidebar',
			array(
				'default'           => Boombox_Woocommerce::get_instance()->get_single_product_secondary_sidebar( '' ),
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => array( $this, 'boombox_woocommerce_sanitize_select' ),
			)
		);

		$wp_customize->add_control(
			'boombox_woocommerce_secondary_sidebar',
			array(
				'label'       => __( 'Sidebar Orientation', 'boombox' ),
				'description' => __( 'Choose sidebar orientation for single product.', 'boombox' ),
				'section'     => 'boombox_woocommerce_single_product',
				'settings'    => 'boombox_woocommerce_secondary_sidebar',
				'type'        => 'select',
				'choices'     => $choices_helper->get_secondary_sidebars(),
			)
		);
		// endregion

	}

	/**
	 * Callback for sanitize select options
	 *
	 * @param $input
	 * @param $setting
	 *
	 * @return string
	 *
	 */
	public function boombox_woocommerce_sanitize_select( $input, $setting ) {
		//input must be a slug: lowercase alphanumeric characters, dashes and underscores are allowed only
		$input = sanitize_key( $input );

		//get the list of possible select options
		$choices = $setting->manager->get_control( $setting->id )->choices;

		//return input if valid or return default option
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
	}

}

Boombox_Woocommerce_Customizer::get_instance();