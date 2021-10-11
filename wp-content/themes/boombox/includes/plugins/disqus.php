<?php
/**
 * Disqus plugin functions
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( !boombox_plugin_management_service()->is_plugin_active( 'disqus-comment-system/disqus.php' ) ) {
	return;
}

if ( !class_exists( 'Boombox_Disqus' ) ) {

	final class Boombox_Disqus {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Disqus|null
		 */
		public static function get_instance () {

			if ( NULL == static::$_instance ) {
				static::$_instance = new Boombox_Disqus();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_Disqus constructor.
		 */
		private function __construct () {
			$this->hooks();

			do_action( 'boombox/disqus/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_Disqus from being cloned.
		 *
		 */
		public function __clone () {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup Hooks
		 */
		public function hooks () {
			add_filter( 'get_comments_link', array( $this, 'edit_comment_link' ), 10, 2 );
		}

		/**
		 * Edit comments link
		 *
		 * @param string      $comments_link Post comments permalink with '#comments' appended.
		 * @param int|WP_Post $post_id       Post ID or WP_Post object.
		 *
		 * @return string
		 */
		public function edit_comment_link ( $comments_link, $post_id ) {

			$hash = '#boombox_comments';
			$comments_link = get_permalink( $post_id ) . $hash;

			return $comments_link;
		}

	}

	Boombox_Disqus::get_instance();

}