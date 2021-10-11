<?php
/**
 * JetPack plugin functions
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( !boombox_plugin_management_service()->is_plugin_active( 'jetpack/jetpack.php' ) ) {
	return;
}

if ( !class_exists( 'Boombox_JetPack' ) ) {

	final class Boombox_JetPack {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_JetPack|null
		 */
		public static function get_instance () {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_JetPack constructor.
		 */
		private function __construct () {
			$this->hooks();

			do_action( 'boombox/jetpack/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_JetPack from being cloned.
		 *
		 */
		public function __clone () {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup Hooks
		 */
		private function hooks () {
			add_filter( 'video_embed_html', 'boombox_wrap_embed_within_responsive_container', 10, 1 );
		}

	}

	Boombox_JetPack::get_instance();

}