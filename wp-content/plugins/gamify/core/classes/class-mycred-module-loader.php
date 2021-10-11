<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class GFY_myCRED_Module_Loader {

	/**
	 * Holds class single instance
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get instance
	 * @return GFY_myCRED_Module_Loader|null
	 */
	public static function get_instance() {

		if( null == static::$_instance ) {
			static::$_instance = new self();
		}

		return static::$_instance;

	}

	/**
	 * Holds myCRED addons
	 * @var array
	 */
	private $modules = array();

	/**
	 * GFY_myCRED_Module_Loader constructor.
	 */
	private function __construct() {
		$this->setup_actions();
	}

	/**
	 * A dummy magic method to prevent GFY_myCRED_Module_Loader from being cloned.
	 *
	 */
	public function __clone() {
		throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
	}

	/**
	 * Setup actions
	 */
	private function setup_actions() {
		add_action( 'mycred_load_modules', array( $this, 'load_modules' ), 9999, 2 );
	}

	/**
	 * Include module dependencies
	 * @param array $files Files paths array
	 */
	private function include_module_dependencies( array $files ) {
		foreach( $files as $file ) {
			if( is_file( $file ) ) {
				require_once $file;
			}
		}
	}

	/**
	 * Register module
	 * @param string    $id             Module unique ID
	 * @param string    $class_name     Module class name
	 * @param array     $args           Module args
	 * @return bool
	 */
	public function register( $id, $class_name, $args = array() ) {

		$r = wp_parse_args( $args, array(
			'depended' => false,
			'includes' => array()
		) );

		$load = true;
		if( $r[ 'depended' ] && class_exists( 'myCRED_Addons_Module' ) ) {
			$addons_manager = new myCRED_Addons_Module();
			if( ! $addons_manager->is_active( $r[ 'depended' ] ) ) {
				$load = false;
			}
		}

		$registered = false;
		if( $load ) {
			$instance = new stdClass();

			$instance->id = $id;
			$instance->class_name = $class_name;
			$this->include_module_dependencies( (array)$r[ 'includes' ] );

			$this->modules[] = $instance;
			$registered = true;
		}

		return $registered;
	}

	/**
	 * Load registerd modules
	 * @param array $modules Registered modules
	 * @param array $point_types Registered point types
	 * @return array
	 */
	public function load_modules( $modules, $point_types ) {

		foreach( $this->modules as $module ) {

			if( ! class_exists( $module->class_name ) ) {
				continue;
			}

			$rc = new ReflectionClass( $module->class_name );

			$modules['solo'][ $module->id ] = $rc->newInstance();
			$modules['solo'][ $module->id ]->load();
		}

		return $modules;
	}



}