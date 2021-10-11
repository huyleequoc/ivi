<?php
/*
Plugin Name:    Gamify
Description:    Enhance myCRED functionality with advanced hooks, buddypress components and many other features.
Author:         Px-Lab
Author URI:     https://px-lab.com
Version:        1.3.1
Text Domain:    gamify
Domain Path:    /languages/
License:        GNU General Public License v2.0
License URI:    http://www.gnu.org/licenses/gpl-2.0.html
*/

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class GFY {
	
	/**
	 * Holds current instance
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get instance
	 * @return GFY|null
	 */
	public static function get_instance() {

		if( null == static::$_instance ) {
			static::$_instance = new GFY();
		}

		return static::$_instance;

	}
	
	/**
	 * GFY constructor.
	 */
	private function __construct() {
		$this->define_constants();
		$this->load_core_files();
		$this->setup_actions();
	}
	
	/**
	 * A dummy magic method to prevent GFY from being cloned.
	 *
	 */
	public function __clone() {
	    throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
	}
	
	/**
	 * Define constants
	 */
	private function define_constants() {
		
	    if( ! defined( 'GFY_MAIN_FILE' ) ) {
	        define( 'GFY_MAIN_FILE', __FILE__ );
        }
	 
		if( ! defined( 'GFY_DIR' ) ) {
			define( 'GFY_DIR', plugin_dir_path( GFY_MAIN_FILE ) );
		}
		if( ! defined( 'GFY_URL' ) ) {
			define( 'GFY_URL', plugin_dir_url( GFY_MAIN_FILE ) );
		}
		
		
		if( ! defined( 'GFY_CORE_DIR' ) ) {
			define( 'GFY_CORE_DIR', trailingslashit( GFY_DIR . 'core' ) );
		}
		if( ! defined( 'GFY_CORE_URL' ) ) {
			define( 'GFY_CORE_URL', trailingslashit( GFY_URL . 'core' ) );
		}
		
		
		if( ! defined( 'GFY_MODULES_DIR' ) ) {
			define( 'GFY_MODULES_DIR', trailingslashit( GFY_DIR . 'modules' ) );
		}
		if( ! defined( 'GFY_MODULES_URL' ) ) {
			define( 'GFY_MODULES_URL', trailingslashit( GFY_URL . 'modules' ) );
		}


		if( ! defined( 'GFY_ADMIN_DIR' ) ) {
			define( 'GFY_ADMIN_DIR', trailingslashit( GFY_CORE_DIR . 'admin' ) );
		}
		if( ! defined( 'GFY_ADMIN_URL' ) ) {
			define( 'GFY_ADMIN_URL', trailingslashit( GFY_CORE_URL . 'admin' ) );
		}
	
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'gamify', false, basename( GFY_DIR ) . '/languages' );
	}
	
	/**
	 * Setup actions
	 */
	private function setup_actions() {
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'mycred_activation', array( $this, 'activate' ) );
		add_action( 'plugins_loaded', array( $this, 'start' ), 5 );
	}
	
	/**
	 * Includes
	 */
	private function includes() {
		$this->load_modules();
	}

	/**
	 * Load core files
	 */
	private function load_core_files() {
		require_once GFY_CORE_DIR . 'bootstrap.php';
	}
	
	/**
	 * Load modules
	 */
	private function load_modules() {

		$modules = array(
			GFY_MODULES_DIR . 'buddypress-achievements' . DIRECTORY_SEPARATOR . 'achievements.php',
			GFY_MODULES_DIR . 'buddypress-ranks' . DIRECTORY_SEPARATOR . 'ranks.php',
			GFY_MODULES_DIR . 'buddypress-history' . DIRECTORY_SEPARATOR . 'history.php',
			GFY_MODULES_DIR . 'buddypress-leaderboard' . DIRECTORY_SEPARATOR . 'leaderboard.php',
			GFY_MODULES_DIR . 'zombify' . DIRECTORY_SEPARATOR . 'zombify.php',
			GFY_MODULES_DIR . 'post-ranking-system' . DIRECTORY_SEPARATOR . 'post-ranking-system.php'

		);

		foreach( $modules as $path ) {
			if( $path && is_file( $path ) ) {
				require_once $path;
			}
		}
		
	}
	
	/**
	 * Plugin activation
	 */
	public function activate() {
		$this->includes();
		
		do_action( 'gfy/activate' );
	}
	
	/**
	 * Plugin starts
	 */
	public function start() {
		$plugin_management_service = GFY_Plugin_Management_Service::get_instance();
		if( $plugin_management_service->is_plugin_active( 'mycred/mycred.php' ) ) {
			$this->includes();
		} elseif( is_admin() ) {
			$gamify_plugin_data = $plugin_management_service->get_plugin_data( GFY_MAIN_FILE );
		    $notification = sprintf( '<b>' . __( '%s requires <a href="https://wordpress.org/plugins/mycred/" target="_blank" rel="noopener">myCRED</a> to be activated', 'gamify' ) . '</b>', $gamify_plugin_data['Name'] );
			GFY_Admin_Notifier::get_instance()->add_notification( $notification, false, GFY_Admin_Notifier::ERROR );
		}
	}
	
}

GFY::get_instance();