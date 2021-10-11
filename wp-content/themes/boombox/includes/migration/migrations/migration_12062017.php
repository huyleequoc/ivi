<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class migration_12062017 {
	
	/**
	 * Alter database tables
	 * @return false|int
	 */
	private static function alter_tables_migrate_up() {
		global $wpdb;
		
		$views_table_name = $wpdb->prefix . 'views';
		
		// Check if column exists
		$sql = "SELECT COLUMN_NAME
					FROM information_schema.COLUMNS
				WHERE
				TABLE_SCHEMA = '{$wpdb->dbname}'
				AND TABLE_NAME = '{$views_table_name}'
				AND COLUMN_NAME = 'point';";
		
		$column_exists = (bool)$wpdb->get_var( $sql );
		if( ! $column_exists ) {
			// Alter views table
			$sql = "ALTER TABLE `{$views_table_name}`
					ADD COLUMN `point` INT(11) UNSIGNED NOT NULL DEFAULT 1;";

			$status = $wpdb->query( $sql );
			return ( false !== $status );
		}
		
		return true;
	}
	
	/**
	 * Add 'total_views' meta key
	 * @return false|int
	 */
	private static function insert_total_view_post_meta() {
		global $wpdb;
		
		$views_table_name = $wpdb->prefix . 'view_total';
		
		$sql = "INSERT INTO `{$wpdb->postmeta}` ( `post_id`, `meta_key`, `meta_value` )
			SELECT `post_id`, 'total_views', `total` FROM `{$views_table_name}`; ";

		$status = $wpdb->query( $sql );

		return ( false !== $status );
	}

	/**
	 * Revert database tables migration
	 * @return false|int
	 */
	private static function alter_tables_migrate_down() {
		global $wpdb;
		
		$views_table_name = $wpdb->prefix . 'views';
		$sql = "ALTER TABLE `{$views_table_name}`
					DROP COLUMN `point`; ";
		
		$status = $wpdb->query( $sql );

		return ( false !== $status );
	}
	
	/**
	 * Remove 'total_views' meta key
	 * @return false|int
	 */
	private static function remove_total_view_post_meta() {
		global $wpdb;
		$sql = "DELETE FROM `{$wpdb->postmeta}` WHERE `meta_key` = 'total_views'";
		
		$status = $wpdb->query( $sql );

		return ( false !== $status );
	}
	
	/**
	 * Organize migration tasks
	 * @return false|int
	 */
	public static function up() {
		return (
			self::alter_tables_migrate_up()
			&& self::insert_total_view_post_meta()
		);
	}
	
	/**
	 * Revert migrations back
	 * @return false|int
	 */
	public static function down() {
		return (
			self::alter_tables_migrate_down()
			&& self::remove_total_view_post_meta()
		);
	}
	
}