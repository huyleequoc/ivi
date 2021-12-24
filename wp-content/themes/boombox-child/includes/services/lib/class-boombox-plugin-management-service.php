<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class Boombox_Plugin_Management_Service {

	/**
	 * Holds class single instance
	 * @var null
	 */
	private static $_instance = NULL;

	/**
	 * Get instance
	 * @return Boombox_Plugin_Management_Service|null
	 */
	public static function get_instance() {

		if ( NULL == static::$_instance ) {
			static::$_instance = new self();
		}

		return static::$_instance;

	}

	/**
	 * Boombox_Plugin_Management_Service constructor.
	 */
	private function __construct() {
	}

	/**
	 * A dummy magic method to prevent Boombox_Plugin_Management_Service from being cloned.
	 */
	public function __clone() {
		throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
	}

	/**
	 * Check whether a plugin is active.
	 *
	 * @param string $plugin Path to the main plugin file from plugins directory.
	 *
	 * @see is_plugin_active() for additional information
	 * @return bool True, if in the active plugins list. False, not in the list.
	 */
	public function is_plugin_active( $plugin ) {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		return is_plugin_active( $plugin );
	}

	/**
	 * Check whether the plugin is active for the entire network.
	 *
	 * @param string $plugin Path to the main plugin file from plugins directory.
	 *
	 * @see is_plugin_active_for_network() for additional information
	 * @return bool True, if active for the network, otherwise false.
	 */
	public function is_plugin_active_for_network( $plugin ) {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		return is_plugin_active_for_network( $plugin );
	}

	/**
	 * Parses the plugin contents to retrieve plugin's metadata.
	 *
	 * @param string $plugin_file Path to the main plugin file.
	 * @param bool   $markup      Optional. If the returned data should have HTML markup applied.
	 *                            Default true.
	 * @param bool   $translate   Optional. If the returned data should be translated. Default true.
	 *
	 * @see get_plugin_data() for additional information
	 *
	 * @return array
	 */
	public function get_plugin_data( $plugin_file, $markup = true, $translate = true ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		return @get_plugin_data( $plugin_file, $markup, $translate );
	}

}