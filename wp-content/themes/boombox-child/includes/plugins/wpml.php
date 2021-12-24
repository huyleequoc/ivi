<?php
/**
 * WPML plugin functions
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! boombox_plugin_management_service()->is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
	return;
}

if ( ! class_exists( 'Boombox_WPML' ) ) {

	final class Boombox_WPML {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return Boombox_WPML|null
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_WPML constructor.
		 */
		private function __construct() {
			$this->hooks();

			do_action( 'boombox/wpml/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_WPML from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup hooks
		 */
		private function hooks() {
			add_action( 'boombox/header/init', array( $this, 'setup_language_switcher' ), 10, 1 );
			add_filter( 'boombox/customizer_default_values', array( $this, 'edit_customizer_default_values' ), 10, 1 );
			add_filter( 'wp_nav_menu_objects', array( $this, 'remove_wpml_menu_item' ), 100, 2 );
			add_filter( 'boombox/header_composition_component_choices', array( $this, 'edit_header_composition_component_choices' ), 10, 1 );
			add_action( 'boombox/header/render_composition_item/wpml-switcher', array( $this, 'render_header_composition_item' ) );
			add_action( 'customize_save_after', array( $this, 'register_dynamic_strings' ) );
		}

		/**
		 * Remove language switcher from menu items from unsupported places
		 *
		 * @param array    $sorted_menu_items The menu items, sorted by each menu item's menu order.
		 * @param stdClass $args              An object containing wp_nav_menu() arguments.
		 *
		 * @return array
		 */
		public function remove_wpml_menu_item( $sorted_menu_items, $args ) {

			$is_depricated = false;
			$depricated_locations = array(
				'badges_nav',               // because menu walker has a specific HTML layout that matches badges only
				'burger_badges_nav',        // because menu walker has a specific HTML layout that matches badges only
				'burger_top_nav',           // because location is preconfigured to have a max 'depth' = 1.
				'burger_bottom_nav',        // because location is preconfigured to have a max 'depth' = 1.
				'profile_nav',              // because location is preconfigured to have a max 'depth' = 1.
				'footer_nav'                // because location is preconfigured to have a max 'depth' = 1.
			);

			/****** restriction by menu theme location */
			if ( in_array( $args->theme_location, $depricated_locations ) ) {
				$is_depricated = true;
			}

			if ( $is_depricated ) {
				$sorted_menu_items = array_filter(
					$sorted_menu_items,
					function ( $menu_item ) {
						return ! ( $menu_item instanceof WPML_LS_Menu_Item );
					}
				);
			}

			return $sorted_menu_items;
		}

		/**
		 * Set default values for additional fields set to customizer
		 *
		 * @param array $values Current values
		 *
		 * @return array
		 */
		public function edit_customizer_default_values( $values ) {
			$values[ 'header_layout_language_switcher_position' ] = 'top';

			return $values;
		}

		/**
		 * Edit header composition components choices
		 *
		 * @param array $choices Current choices
		 *
		 * @return array
		 * @since   2.0.0
		 * @version 2.0.0
		 */
		public function edit_header_composition_component_choices( $choices ) {
			$choices[ 'wpml-switcher' ] = __( 'WPML switcher', 'boombox' );

			return $choices;
		}

		/**
		 * Render language switcher as header composition item
		 */
		public function render_header_composition_item() {
			?>
			<div class="boombox-wpml-language-switcher header-item"><?php do_action( 'wpml_add_language_selector' ); ?></div>
			<?php
		}

		/**
		 * Register dynamic strings
		 * @since 2.1.3
		 * @version 2.1.3
		 */
		public function register_dynamic_strings() {

			/***** do nothing if "String translation" addon is unavailable */
			if( ! boombox_plugin_management_service()->is_plugin_active( 'wpml-string-translation/plugin.php' ) ) {
				return;
			}

			/***** Options set */
			$set = boombox_get_theme_options_set( array(
				'extra_authentication_login_popup_title',
				'extra_authentication_login_popup_text',
				'extra_authentication_registration_popup_title',
				'extra_authentication_registration_popup_text',
				'extra_authentication_forgot_password_popup_title',
				'extra_authentication_forgot_password_popup_text',
				'header_layout_community_text',
				'header_layout_button_text',
				'footer_general_text',
				'single_post_general_post_button_label',
				'single_post_related_posts_related_entries_heading',
				'single_post_related_posts_more_entries_heading',
				'single_post_related_posts_dont_miss_entries_heading',
				'single_post_sponsored_articles_label'
			) );

			/***** Options set values */
			$associations = array(
				'extra_authentication_login_popup_title'              => 'Login Popup Heading',
				'extra_authentication_login_popup_text'               => 'Login Popup Text',
				'extra_authentication_registration_popup_title'       => 'Registration Popup Heading',
				'extra_authentication_registration_popup_text'        => 'Registration Popup Text',
				'extra_authentication_forgot_password_popup_title'    => 'Forgot Password Popup Heading',
				'extra_authentication_forgot_password_popup_text'      => 'Forgot Password Popup Text',
				'header_layout_community_text'                        => 'Community Text',
				'header_layout_button_text'                           => '"Compose" Button Text',
				'footer_general_text'                                 => 'Footer Text',
				'single_post_general_post_button_label'               => 'Button Text',
				'single_post_related_posts_related_entries_heading'   => '"Related Posts" Block Heading',
				'single_post_related_posts_more_entries_heading'      => '"More From" Block Heading',
				'single_post_related_posts_dont_miss_entries_heading' => '"Don\'t Miss" Block Heading',
				'single_post_sponsored_articles_label'                => 'Sponsored Articles Label'
			);

			foreach ( $associations as $key => $name ) {
				do_action( 'wpml_register_single_string', 'boombox', $name, $set[ $key ] );
			}
		}

	}

	Boombox_WPML::get_instance();

}