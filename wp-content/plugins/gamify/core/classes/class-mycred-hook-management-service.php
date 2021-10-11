<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

final class GFY_myCRED_Hook_Management_Service {

	/**
	 * Holds class single instance
	 * @var null
	 */
	private static $_instance = null;

	/**
	 *
	 * @return GFY_myCRED_Hook_Management_Service|null
	 */
	public static function get_instance() {

		if (null == static::$_instance) {
			static::$_instance = new static();
		}

		return static::$_instance;

	}

	/**
	 * Holds registered hooks data
	 * @var array
	 */
	private $hooks = array();

	/**
	 * Register hook
	 *
	 * @param string $id            Hook ID
	 * @param string $title         Hook title
	 * @param string $description   Hook description
	 * @param array  $callback      Hook callback class
	 */
	public function register_hook( $id, $title, $description, $callback ) {

		$hook = new stdClass();
		$hook->id = $id;
		$hook->title = $title;
		$hook->description = $description;
		$hook->callback = $callback;

		$this->hooks[] = $hook;
	}

	/**
	 * GFY_myCRED_Hook_Management_Service constructor.
	 */
	private function __construct() {
		$this->setup_actions();
	}

	/**
	 * A dummy magic method to prevent GFY_myCRED_Hook_Management_Service from being cloned.
	 *
	 */
	public function __clone() {
		throw new Exception('Cloning ' . __CLASS__ . ' is forbidden');
	}

	/**
	 * Setup actions
	 */
	private function setup_actions() {
		add_filter( 'mycred_setup_hooks', array( $this, 'setup_hooks' ), 10, 2 );
	}

	/**
	 * Register hooks
	 *
	 * @param $installed    array<string,array> Installed hooks
	 * @param $cred_type    string              myCRED Type used
	 *
	 * @return array<string,array>
	 */
	public function setup_hooks( $installed, $cred_type ) {

		foreach( $this->hooks as $hook ) {
			$installed[ $hook->id ] = array(
				'title'        => $hook->title,
				'description'  => $hook->description,
				'callback'     => $hook->callback
			);
		}

		return $installed;
	}

}