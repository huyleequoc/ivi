<?php
/*
Plugin Name:    BoomBox Theme Extensions
Description:    Add custom functionality to BoomBox theme ( social icons, social widget, shortcodes, custom post types, etc. )
Author:         Px-Lab
Author URI:     https://px-lab.com
Version:        1.6.8
Text Domain:    boombox-theme-extensions
Domain Path:    /languages/
License:        GNU General Public License v2.0
License URI:    http://www.gnu.org/licenses/gpl-2.0.html
*/

// Prevent direct script access
if ( ! defined( 'ABSPATH' ) ) {
	die ( 'No direct script access allowed' );
}
if ( ! class_exists( 'Boombox_Theme_Extensions' ) ) {

	class Boombox_Theme_Extensions {
		/**
		 * @var string
		 */
		private static $option_name = 'boombox_theme_extensions';

		/**
		 * @var null The single instance of the class
		 */
		protected static $_instance = null;
		/**
		 * @return Boombox_Theme_Extensions $_instance
		 */
		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
		/**
		 * Constructor.
		 */
		private function __construct() {
			$this->define_constants();
			$this->includes();
			$this->init();
			$this->load_translations();
		}
		/**
		 * Define Constants
		 */
		public function define_constants() {
			$this->define( 'BBTE_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			$this->define( 'BBTE_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
			$this->define( 'BBTE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		}
		/**
		 * Define constant if not already set
		 *
		 * @param  string $name
		 * @param  string|bool $value
		 */
		public function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {
			if( defined( 'BOOMBOX_THEME_PATH' ) ) {
				include_once( BBTE_PLUGIN_PATH . '/boombox-social/functions.php' );
				include_once( BBTE_PLUGIN_PATH . '/boombox-shortcodes/functions.php' );
				include_once( BBTE_PLUGIN_PATH . '/boombox-gif-to-video/functions.php' );
			}
			include_once( BBTE_PLUGIN_PATH . '/boombox-reactions/functions.php' );
			include_once( BBTE_PLUGIN_PATH . '/boombox-brands/functions.php' );
		}
		/**
		 * Init
		 */
		public function init() {
			if ( class_exists( 'Boombox_Social' ) ) {
				Boombox_Social::init();
			}
		}
		/**
		 * Load the plugin translations
		 */
		private function load_translations() {
			load_plugin_textdomain( 'boombox-theme-extensions', false, dirname( BBTE_PLUGIN_BASENAME ). '/languages' );
			load_plugin_textdomain( 'boombox-theme-extensions' );
		}

	}
}

add_action( 'after_setup_theme', 'bbte_theme_setup' );
function bbte_theme_setup() {
	Boombox_Theme_Extensions::instance();
}