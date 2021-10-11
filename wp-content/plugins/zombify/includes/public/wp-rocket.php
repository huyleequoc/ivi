<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! is_plugin_active( 'w3-total-cache/w3-total-cache.php' ) ) {
	return;
}

if( class_exists( 'ZF_WP_Rocket' ) ) {
	return;
}

/**
 * Class ZF_WP_Rocket
 */
class ZF_WP_Rocket {

	/**
	 * Holds unique instance
	 * @var ZF_WP_Rocket
	 */
	private static $_instance;

	/**
	 * Get unique instance
	 * @return ZF_WP_Rocket
	 */
	public static function get_instance() {
		if( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * ZF_WP_Rocket constructor.
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
		rocket_clean_post( $post_id );
	}

	/**
	 * Callback to flush post cache by post URLs
	 * @param string $url The URL to flush
	 */
	public function flush_post_by_url( $url ) {
		add_filter( 'rocket_post_purge_urls', function( $urls ) use ( $url ){
			$urls[] = $url;

			return $urls;
		} );
	}

}

ZF_WP_Rocket::get_instance();