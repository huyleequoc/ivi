<?php

/**
 * "WP Rocket" plugin functions
 *
 * @package BoomBox_Theme
 * @since 2.1.2
 * @version 2.1.2
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! boombox_plugin_management_service()->is_plugin_active( 'wp-rocket/wp-rocket.php' ) ) {
	return;
}

if ( ! class_exists( 'Boombox_WP_Rocket' ) ) {

	final class Boombox_WP_Rocket {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return Boombox_WP_Rocket|null
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Holds array of users to clean
		 * @var array
		 */
		private $clean_urls = array();

		/**
		 * Get URLs array to clean
		 * @return array
		 */
		public function get_clean_urls() {
			return $this->clean_urls;
		}

		/**
		 * Boombox_WP_Rocket constructor.
		 */
		private function __construct() {
			$this->hooks();

			do_action( 'boombox/wp_rocket/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_WP_Rocket from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup Hooks
		 */
		public function hooks() {
			add_action( 'boombox_after_user_login_success', array( $this, 'clean_user_cache' ), 10, 2 );
			add_filter( 'boombox_ajax_login_check_referer', '__return_false' );
			add_filter( 'boombox_ajax_register_check_referer', '__return_false' );
			add_action( 'boombox/post_reacted', array( $this, 'flush_post_cache_by_id' ), 10, 1 );
			add_action( 'boombox/post_pointed', array( $this, 'flush_post_cache_by_id' ), 10, 1 );
		}

		/**
		 * Clear target URL cache on login success
		 *
		 * @param WP_User $user         Logged in user instance
		 * @param string  $redirect_url Target URL
		 */
		public function clean_user_cache( $user, $redirect_url ) {
			$home_url = home_url( '/' );
			if ( $redirect_url ) {
				$this->clean_urls[] = esc_url( $redirect_url );
			} else {
				$this->clean_urls[] = esc_url( $home_url );
			}

			if( wp_unslash( $redirect_url ) == wp_unslash( $home_url ) ) {
				rocket_clean_home();
			} else {
				rocket_clean_post( url_to_postid( $redirect_url ) );
			}

			add_filter( 'rocket_clean_domain_urls', array( $this, 'edit_clean_urls_on_user_login' ), 10, 1 );
			rocket_clean_user( $user->ID );
			remove_filter( 'rocket_clean_domain_urls', array( $this, 'edit_clean_urls_on_user_login' ), 10 );
		}

		/**
		 * Edit clean urls on user login
		 * @param array $urls Current URLs
		 *
		 * @return array
		 */
		public function edit_clean_urls_on_user_login( $urls ) {
			$urls = array_merge( $urls, (array)$this->clean_urls );

			return $urls;
		}

		/**
		 * Clean up post cache
		 * @param int  $post_id  Post ID
		 */
		public function flush_post_cache_by_id( $post_id ) {
			rocket_clean_files( get_permalink( $post_id ) );
		}

	}

	Boombox_WP_Rocket::get_instance();

}