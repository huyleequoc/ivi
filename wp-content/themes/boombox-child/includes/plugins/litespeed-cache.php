<?php

/**
 * "LiteSpeed Cache" plugin functions
 *
 * @package BoomBox_Theme
 * @since   2.5.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! boombox_plugin_management_service()->is_plugin_active( 'litespeed-cache/litespeed-cache.php' ) ) {
	return;
}

if ( ! class_exists( 'Boombox_LiteSpeed_Cache' ) ) {

	final class Boombox_LiteSpeed_Cache {

		/**
		 * Holds class single instance
		 *
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 *
		 * @return Boombox_LiteSpeed_Cache|null
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_LiteSpeed_Cache constructor.
		 */
		private function __construct() {
			$this->hooks();

			do_action( 'boombox/lscache/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_LiteSpeed_Cache from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup Hooks
		 */
		public function hooks() {
			add_filter( 'litespeed_ajax_vary', '__return_true' );
			add_filter( 'boombox_ajax_login_check_referer', '__return_false' );
			add_filter( 'boombox_ajax_register_check_referer', '__return_false' );
			add_action( 'boombox/post_reacted', array( $this, 'flush_post_by_id' ), 10, 1 );
			add_action( 'boombox/post_pointed', array( $this, 'flush_post_by_id' ), 10, 1 );
			add_action( 'zf_flush_post_by_id', array( $this, 'flush_post_by_id' ), 10, 1 );
		}

		/**
		 * Purge single post cache
		 *
		 * @param string|int $post_id The post ID
		 */
		public function flush_post_by_id( $post_id ) {
			do_action( 'litespeed_purge_post', $post_id );
		}

	}

	Boombox_LiteSpeed_Cache::get_instance();
}