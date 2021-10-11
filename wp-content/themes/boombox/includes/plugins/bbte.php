<?php
/**
 * Boombox Theme Extensions plugin functions
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! boombox_plugin_management_service()->is_plugin_active( 'boombox-theme-extensions/boombox-theme-extensions.php' ) ) {
	return;
}

if ( ! class_exists( 'Boombox_BBTE' ) ) {

	final class Boombox_BBTE {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_BBTE|null
		 */
		public static function get_instance() {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_BBTE constructor.
		 */
		private function __construct() {
			$this->hooks();

			do_action( 'boombox/bbte/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_BBTE from being cloned.
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup Hooks
		 */
		public function hooks() {
			add_filter( 'bbte/cloudconvert_allow_processing', array( $this, 'cloudconvert_allow_processing' ), 10, 6 );
			add_filter( 'boombox/prs/fake_reactions_base', array( $this, 'edit_fake_reaction_base' ) );
		}

		/**
		 * Prevent cloudconvert converting
		 *
		 * @param bool    $allow         Current state
		 * @param WP_Post $attachment    Attachment post
		 * @param string  $action        Current action ( new or edit )
		 * @param string  $input_format  Input format
		 * @param string  $output_format Output format
		 * @param string  $unique_id     Unique identifier
		 *
		 * @return bool
		 */
		public function cloudconvert_allow_processing( $allow, $attachment, $action, $input_format, $output_format, $unique_id ) {
			//do not process converting if it is already done.
			if ( 'edit' == $action && boombox_get_post_meta( $attachment->ID, 'mp4_id' ) ) {
				$allow = false;
			}

			return $allow;
		}

		/**
		 * Edit fake reaction count base
		 * @return int
		 */
		public function edit_fake_reaction_base() {
			return boombox_get_theme_option( 'extras_post_reaction_system_fake_reaction_count_base' );
		}

	}

	Boombox_BBTE::get_instance();

}