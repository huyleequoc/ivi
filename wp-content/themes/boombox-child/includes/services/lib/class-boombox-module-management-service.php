<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class Boombox_Module_Management_Service {

	/**
	 * Holds class single instance
	 * @var null
	 */
	private static $_instance = NULL;

	/**
	 * Get instance
	 * @return Boombox_Module_Management_Service|null
	 */
	public static function get_instance() {

		if ( NULL == static::$_instance ) {
			static::$_instance = new self();
		}

		return static::$_instance;

	}

	/**
	 * Boombox_Module_Management_Service constructor.
	 */
	private function __construct() {
	}

	/**
	 * A dummy magic method to prevent Boombox_Module_Management_Service from being cloned.
	 */
	public function __clone() {
		throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
	}

	/**
	 * Check whether a module is active.
	 * @param string $module Module ID
	 *
	 * @return bool
	 */
	public function is_module_active( $module ) {
		return (bool)apply_filters( 'boombox/module_management_service/is_active_' . $module, false );
	}

}