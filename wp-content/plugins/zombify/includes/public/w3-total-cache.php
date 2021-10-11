<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! is_plugin_active( 'w3-total-cache/w3-total-cache.php' ) ) {
	return;
}

if( class_exists( 'ZF_W3TC' ) ) {
	return;
}

/**
 * Class ZF_W3TC
 */
class ZF_W3TC {

	/**
	 * Holds unique instance
	 * @var ZF_W3TC
	 */
	private static $_instance;

	/**
	 * Get unique instance
	 * @return ZF_W3TC
	 */
	public static function get_instance() {
		if( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * ZF_W3TC constructor.
	 */
	private function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Setup required actions
	 */
	public function setup_hooks() {
		add_action( 'zf_flush_post_by_id', array( $this, 'flush_post_by_id' ), 100, 1 );
		add_action( 'zf_flush_post_by_url', array( $this, 'flush_post_by_url' ), 100, 1 );
	}

	/**
	 * Callback to flush post cache by post ID
	 * @param int|string $post_id The post ID
	 */
	public function flush_post_by_id( $post_id ) {
		do_action( 'w3tc_flush_url', get_permalink( $post_id ) );
	}

	/**
	 * Callback to flush post cache by URL
	 * @param string $url The post permalink
	 */
	public function flush_post_by_url( $url ) {
		do_action( 'w3tc_flush_url', $url );
	}

}

ZF_W3TC::get_instance();