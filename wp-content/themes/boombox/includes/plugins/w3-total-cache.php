<?php
/**
 * W3 Total Cache plugin functions
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! boombox_plugin_management_service()->is_plugin_active( 'w3-total-cache/w3-total-cache.php' ) ) {
	return;
}

if ( ! class_exists( 'Boombox_W3_Total_Cache' ) ) {

	final class Boombox_W3_Total_Cache {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Hold plugin config
		 * @var array|mixed|W3_Config
		 */
		public $config = array();

		/**
		 * Get instance
		 * @return Boombox_W3_Total_Cache|null
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_W3_Total_Cache constructor.
		 */
		private function __construct() {

			if ( ! function_exists( 'w3tc_config' ) ) {
				return;
			}

			if ( ! defined( 'W3TC_DYNAMIC_SECURITY' ) ) {
				define( 'W3TC_DYNAMIC_SECURITY', md5( $_SERVER[ 'HTTP_HOST' ] ) );
			}

			$this->config = w3tc_config();
			$this->hooks();

			do_action( 'boombox/w3tc/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_W3_Total_Cache from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup Hooks
		 */
		private function hooks() {

			add_action( 'boombox_after_user_login_success', array( $this, 'bw3tc_after_user_login_success' ), 10, 2 );

			if ( $this->config->get_boolean( 'pgcache.enabled' ) ) {

				//add_filter( 'boombox/frgcache.enabled', function(){ return true; } );
				add_filter( 'boombox/pgcache.enabled', '__return_true' );

				add_action( 'boombox/post_reacted', array( $this, 'flush_post_cache_by_id' ), 10, 1 );
				add_action( 'boombox/post_pointed', array( $this, 'flush_post_cache_by_id' ), 10, 1 );

				add_filter( 'boombox_ajax_login_check_referer', '__return_false' );
				add_filter( 'boombox_ajax_register_check_referer', '__return_false' );

				add_filter( 'mycred_badge_user_value', array( $this, 'award_badge_to_user' ), 999, 3 );
				add_action( 'mycred_user_got_demoted', array( $this, 'rank_changed' ), 999, 2 );
				add_action( 'mycred_user_got_promoted', array( $this, 'rank_changed' ), 999, 2 );

			}
		}

		/**
		 * Clear post cache
		 *
		 * @param int $post_id Post ID
		 */
		public function flush_post_cache_by_id( $post_id ) {
			w3tc_flush_post( $post_id );
		}

		/**
		 * Clear target URL page cache on login success
		 *
		 * @param WP_User $user         Logged in user instance
		 * @param string  $redirect_url Target URL
		 */
		public function bw3tc_after_user_login_success( $user, $redirect_url ) {
			if ( $redirect_url ) {
				$post_id = url_to_postid( $redirect_url );
				if ( $post_id ) {
					w3tc_flush_post( $post_id );
				}
			}
		}

		/**
		 * Callback function on awarding badge to user
		 *
		 * @param int $level    Badge level
		 * @param int $user_id  User ID
		 * @param int $badge_id Badge ID
		 *
		 * @return int
		 */
		public function award_badge_to_user( $level, $user_id, $badge_id ) {
			if ( boombox_plugin_management_service()->is_plugin_active( 'buddypress/bp-loader.php' ) ) {

				$author_url = esc_url( get_author_posts_url( $user_id ) );

				w3tc_flush_url( $author_url );
			}

			return $level;
		}

		/**
		 * Rank motion callback
		 *
		 * @param int $user_id User ID
		 * @param int $rank_id User rank ID
		 */
		public function rank_changed( $user_id, $rank_id ) {
			if ( boombox_plugin_management_service()->is_plugin_active( 'buddypress/bp-loader.php' ) ) {
				$author_url = esc_url( get_author_posts_url( $user_id ) );

				w3tc_flush_url( $author_url );
			}
		}

	}

	Boombox_W3_Total_Cache::get_instance();
}