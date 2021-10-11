<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class migration_13092017 {
	
	/**
	 * Update 'Auto Load Next Post' plugin container selector
	 * @return false|int
	 */
	private static function add_total_posts_view_count_meta() {
		global $wpdb;
		
		/* Delete old data */
		$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => 'total_posts_view_count' ), array( '%s' ) );
		
		$views_table_name = $wpdb->prefix . 'views';
		/* Update user data */
		$sql = "INSERT INTO `{$wpdb->usermeta}` (`user_id`, `meta_key`, `meta_value`)
                  SELECT `posts`.`post_author`, 'total_posts_view_count', SUM(`views`.`point`)
                    FROM `{$views_table_name}` as `views`
                  JOIN `{$wpdb->posts}` AS `posts` ON `posts`.ID = `views`.`post_id`
                  GROUP BY `posts`.`post_author`";
		
		$status = $wpdb->query( $sql );
		
		return ( false !== $status );
	}
	/**
	 * Downgrade 'Auto Load Next Post' plugin container selector
	 * @return false|int
	 */
	private static function remove_total_posts_view_count_meta() {
		global $wpdb;
		
		$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => 'total_posts_view_count' ), array( '%s' ) );
		
		return true;
	}
	
	/**
	 * Organize migration tasks
	 * @return false|int
	 */
	public static function up() {
		return self::add_total_posts_view_count_meta();
	}
	
	/**
	 * Revert migrations back
	 * @return false|int
	 */
	public static function down() {
		return self::remove_total_posts_view_count_meta();
	}
	
}