<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class Boombox_Hook_Management_Service {

	/**
	 * Holds class single instance
	 * @var null
	 */
	private static $_instance = NULL;

	/**
	 * Get instance
	 * @return Boombox_Hook_Management_Service|null
	 */
	public static function get_instance() {

		if ( NULL == static::$_instance ) {
			static::$_instance = new self();
		}

		return static::$_instance;

	}

	/**
	 * Boombox_Hook_Management_Service constructor.
	 */
	private function __construct() {}

	/**
	 * A dummy magic method to prevent Boombox_Hook_Management_Service from being cloned.
	 */
	public function __clone() {
		throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
	}
	
	/**
	 * Remove action by callback name
	 *
	 * @param $action string Name of the action to remove callback from
	 * @param $callback array|string Function to process with
	 * @param null $priority, default null if you don't know what's the current
	 *
	 * @return bool
	 */
	public function remove_action_by_callback_name( $action, $callback, $priority = null ) {
		$result = false;
		
		$callback = $this->extract_callback( $callback );
		$callback_data = $this->get_hook_callback( $action, $callback['func'], $callback['type'], $priority );
		if( $callback_data ) {
			$result = remove_action( $action, $callback_data['unique_id'], $callback_data['priority'] );
		}
		
		return $result;
	}
	
	/**
	 * Reassign callback to another action
	 *
	 * @param $callback array|string Function to process with
	 * @param $old_action string Name of the action to remove callback from
	 * @param $new_action string Name of the action to add callback to
	 * @param null $old_priority, default null if you don't know what's the current
	 * @param int $new_priority New priority for the callback
	 *
	 * @return bool
	 */
	public function reassign_callback_action( $callback, $old_action, $new_action, $old_priority = null, $new_priority = 10 ) {
		$result = false;
		
		$callback = $this->extract_callback( $callback );
		$callback_data = $this->get_hook_callback( $old_action, $callback['func'], $callback['type'], $old_priority );
		if( $callback_data ) {
			$result = remove_action( $old_action, $callback_data['unique_id'], $callback_data['priority'] );
			
			add_action( $new_action, $callback_data['callable'], $new_priority );
		}
		
		return $result;
	}
	
	/**
	 * Change hook priority
	 *
	 * @param string $action Hook name to search
	 * @param array|string The name of the function to search
	 * @param int $new_priority
	 * @param int $old_priority, default null if you don't know what's the current
	 *
	 * @return bool
	 */
	public function change_priority( $action, $callback, $new_priority = 10, $old_priority = null ){
		return $this->reassign_callback_action( $callback, $action, $action, $old_priority, $new_priority );
	}
	
	/**
	 * Get callable data from callback
	 * @param $callback
	 *
	 * @return array
	 */
	private function extract_callback( $callback ) {
		$return = array(
			'type'  => null,
			'func'  => ''
		);
		if( is_array( $callback ) ) {
			$return[ 'type' ] = $callback[0];
			$return[ 'func' ] = $callback[1];
		} else {
			$return[ 'func' ] = $callback;
		}
		
		return $return;
	}
	
	/**
	 * Get hook callback
	 * @param string $action Hook name to search
	 * @param string $func name
	 * @param string $type class name default null
	 * @param int $current_priority, default null to look everywhere
	 *
	 * @return array|bool
	 */
	private function get_hook_callback( $action, $func, $type = null, $current_priority = null ){

		$return = false;
		global $wp_filter;

		if( isset( $wp_filter[ $action ] ) ) {

			$callback_instance = $wp_filter[ $action ];

			$callbacks = $callback_instance->callbacks;
			if ( $current_priority ) {
				if ( isset( $callbacks[ $current_priority ] ) ) {
					foreach ( $callbacks[ $current_priority ] as $unique_id => $data ) {

						if ( is_array( $data[ 'function' ] ) ) {
							$type_check_result = $type ? ( is_a( $data[ 'function' ][ 0 ], ( is_object( $type ) ? get_class( $type ) : $type ) ) || ( $data[ 'function' ][ 0 ] == $type ) ) : true;
							$status = ( $type_check_result && ( $data[ 'function' ][ 1 ] == $func ) );
						} else {
							$status = ( $data[ 'function' ] == $func );
						}

						if ( $status ) {
							$return = array(
								'priority'      => $current_priority,
								'unique_id'     => $unique_id,
								'callable'      => $data[ 'function' ],
								'accepted_args' => $data[ 'accepted_args' ]
							);

							break;
						}
					}
				}

			} else {
				foreach ( $callbacks as $priority => $callback_data ) {
					foreach ( $callback_data as $unique_id => $data ) {
						if ( is_array( $data[ 'function' ] ) ) {
							$type_check_result = $type ? ( is_a( $data[ 'function' ][ 0 ], ( is_object( $type ) ? get_class( $type ) : $type ) ) || ( $data[ 'function' ][ 0 ] == $type ) ) : true;
							$status = ( $type_check_result && ( $data[ 'function' ][ 1 ] == $func ) );
						} else {
							$status = ( $data[ 'function' ] == $func );
						}

						if ( $status ) {
							$return = array(
								'priority'      => $priority,
								'unique_id'     => $unique_id,
								'callable'      => $data[ 'function' ],
								'accepted_args' => $data[ 'accepted_args' ]
							);

							break 2;
						}
					}
				}
			}

		}
		
		return $return;
	}
	
}