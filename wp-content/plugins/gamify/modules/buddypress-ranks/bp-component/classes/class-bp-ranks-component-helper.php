<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class GFY_BP_Ranks_BP_Component_Helper {
	
	/**
	 * Hold component ID
	 * @var string
	 */
	private static $id = 'gfy_bp_ranks';
	
	/**
	 * Get component ID
	 *
	 * @return string
	 */
	public static function get_id() {
		return static::$id;
	}

	/**
	 * Get component title
	 *
	 * @return string
	 */
	public static function get_title() {
		return apply_filters( 'gfy/' . static::get_id() . '/component_title', __( 'Ranks', 'gamify' ) );
	}

	/**
	 * Get component description
	 *
	 * @return string
	 */
	public static function get_description() {
		return apply_filters( 'gfy/' . static::get_id() . '/component_description', __( 'Extends ranks functionality ( myCRED ).', 'gamify' ) );
	}

	/**
	 * Get notifications status
	 * @return bool
	 */
	public static function is_notification_disabled() {
		return (bool)apply_filters( 'gfy/' . static::get_id() . '/disable_notifications', false );
	}

	/**
	 * Register BP component
	 * @param array $components
	 *
	 * @return array
	 */
	public static function register_component( $components ) {

		$components[] = array(
			'id'            => static::get_id(),
			'class'         => 'GFY_BP_Ranks_BP_Component',
			'path'          => trailingslashit( __DIR__ ) . 'class-bp-ranks-component.php',
			'hidden'        => true
		);

		return $components;
	}

}