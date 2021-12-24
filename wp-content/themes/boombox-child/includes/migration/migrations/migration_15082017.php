<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class migration_15082017 {

	/**
	 * Update 'Auto Load Next Post' plugin container selector
	 * @return false|int
	 */
	private static function alnp_container_migrate_up() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if( array_key_exists( 'auto-load-next-post/auto-load-next-post.php', get_plugins() ) ) {
			update_option( 'auto_load_next_post_navigation_container', 'nav.next-prev-pagination:last' );
		}
		
		return true;
	}
	
	/**
	 * Downgrade 'Auto Load Next Post' plugin container selector
	 * @return false|int
	 */
	private static function alnp_container_migrate_down() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if( array_key_exists( 'auto-load-next-post/auto-load-next-post.php', get_plugins() ) ) {
			update_option( 'auto_load_next_post_navigation_container', 'div.next-prev-pagination' );
		}
		
		return true;
	}
	
	/**
	 * Organize migration tasks
	 * @return false|int
	 */
	public static function up() {
		return self::alnp_container_migrate_up();
	}
	
	/**
	 * Revert migrations back
	 * @return false|int
	 */
	public static function down() {
		return self::alnp_container_migrate_down();
	}
	
}