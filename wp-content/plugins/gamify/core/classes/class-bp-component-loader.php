<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class GFY_BP_Component_Loader {
	
	/**
	 * Holds class single instance
	 * @var null
	 */
	private static $_instance = null;
	
	/**
	 * Get instance
	 * @return GFY_BP_Component_Loader|null
	 */
	public static function get_instance() {
		
		if( null == static::$_instance ) {
			static::$_instance = new GFY_BP_Component_Loader();
		}
		
		return static::$_instance;
		
	}
	
	/**
	 * GFY_BP_Component_Loader constructor.
	 */
	private function __construct() {
		$this->setup_actions();
	}
	
	/**
	 * A dummy magic method to prevent GFY_BP_Component_Loader from being cloned.
	 *
	 */
	public function __clone() {
		throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
	}
	
	/**
	 * Setup actions
	 */
	private function setup_actions() {
		add_action( 'mycred_activation', array( $this, 'activate_components' ), 40 );
		add_action( 'bp_setup_components', array( $this, 'setup_components' ), 8 );
		add_filter( 'bp_new_install_default_components', array( $this, 'new_install_default_components' ), 10, 1 );
		add_filter( 'bp_core_get_components', array( $this, 'add_to_bp_components_list' ), 10, 2 );
		add_filter( 'bp_active_components', array( $this, 'load_force_active_components' ) );
	}
	
	/**
	 * Get components list
	 * @return array
	 */
	private function get_bp_components() {
		$components = (array)apply_filters( 'gfy/register_bp_components', array() );

		$valid_components = array();
		foreach( $components as $index => $component ) {
			$component = wp_parse_args( $component, array(
				'id'            => '',
				'class'         => '',
				'path'          => '',
				'title'         => '',
				'description'   => '',
				'hidden'        => false,
			) );

			if( ! $component['id'] || ! $component['class'] || ! $component['path'] ) {
				continue;
			}

			$valid_components[] = $component;
		}

		return $valid_components;
	}
	
	/**
	 * Activate components on plugin activation
	 */
	public function activate_components() {
		if( ! GFY_Plugin_Management_Service::get_instance()->is_plugin_active( 'buddypress/bp-loader.php' ) ) {
			return;
		}
		
		$components  = $this->get_bp_components();
		if( empty( $components ) ) {
			return;
		}
		
		$active_components = bp_get_option( 'bp-active-components' );
		
		foreach( wp_list_pluck( $components, 'id' ) as $component_id ) {
			if ( ! array_key_exists( $component_id, $active_components ) ) {
				$active_components[ $component_id ] = 1;
			}
		}
		
		bp_update_option( 'bp-active-components', $active_components );
	}
	
	/**
	 * Set up components
	 */
	public function setup_components() {

		$components  = $this->get_bp_components();
		
		if( empty( $components ) ) {
			return;
		}
		
		$bp = buddypress();
		foreach( $components as $component ) {
			
			$component_id = $component['id'];
			$component_class = $component['class'];
			$component_path = $component['path'];

			if ( bp_is_active( $component_id ) ) {

				if ( ! class_exists( $component_class ) && @is_file( $component_path ) ) {
					require_once $component_path;
				}

				$rc = new ReflectionClass( $component_class );
				$bp->{$component_id} = $rc->newInstance();
			}
		}

	}
	
	/**
	 * Activate components on new install
	 * @param array $components
	 *
	 * @return array
	 */
	public function new_install_default_components( $components ) {
		
		$extra_components = wp_list_pluck( $this->get_bp_components(), 'id' );
		if( ! empty( $extra_components ) ) {
			foreach( $extra_components as $component_id ) {
				$components[ $component_id ] = 1;
			}
		}
		
		return $components;
	}
	
	/**
	 * Add components to components list
	 * @param array     $components Current components
	 * @param string    $type       Current filter
	 *
	 * @return array
	 */
	public function add_to_bp_components_list( $components, $type ){
		
		$extra_components = $this->get_bp_components();
		if( ! empty( $extra_components ) && ! in_array( $type, array( 'required', 'retired' ) ) ) {
			foreach( $extra_components as $component ) {

				if( $component['hidden'] ) {
					continue;
				}

				$components[ $component['id'] ] = array(
					'title'         => $component['title'],
					'description'   => $component['description']
				);
			}
		}

		return $components;
	}

	/**
	 * Load forced components
	 * @param array $components Active components
	 * @return array
	 */
	public function load_force_active_components( $components ) {
		$load = true;
		if( is_admin() ) {

			if( ! function_exists( 'get_current_screen' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/screen.php' );
			}
			if( get_current_screen() && get_current_screen()->id == 'settings_page_bp-components' ) {
				$load = false;
			}
		}

		if( $load ) {
			$extra_components = wp_list_filter( $this->get_bp_components(), array( 'hidden' => true ) );

			foreach ( $extra_components as $component ) {
				$components[ $component[ 'id' ] ] = 1;
			}
		}

		return $components;
	}

}