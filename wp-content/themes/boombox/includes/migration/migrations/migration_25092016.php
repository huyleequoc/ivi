<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class migration_25092016 {

	/**
	 * Create database tables
	 * @return bool
	 */
	private static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$point_total_table_name    = $wpdb->prefix . 'point_total';
		$points_table_name         = $wpdb->prefix . 'points';
		$reaction_total_table_name = $wpdb->prefix . 'reaction_total';
		$reactions_table_name      = $wpdb->prefix . 'reactions';
		$view_total_table_name     = $wpdb->prefix . 'view_total';
		$views_table_name          = $wpdb->prefix . 'views';
		$rate_schedule_table_name  = $wpdb->prefix . 'rate_schedule';

		/*Table structure for table `point_total` */
		$sql = "CREATE TABLE IF NOT EXISTS `{$point_total_table_name}`  (
			`post_id` bigint(20) NOT NULL,
			`total` bigint(20) NOT NULL,
		    PRIMARY KEY (`post_id`)
		) {$charset_collate}; ";
		$point_total_table_status = $wpdb->query( $sql );


		/*Table structure for table `points` */
		$sql = "CREATE TABLE IF NOT EXISTS `{$points_table_name}` (
				`post_id` bigint(20) NOT NULL,
				`user_id` bigint(20) DEFAULT NULL,
				`ip_address` varchar(127) NOT NULL,
				`point` tinyint(1) NOT NULL,
				`created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`session_id` varchar(255) NOT NULL
			) {$charset_collate}; ";
		$points_table_status = $wpdb->query( $sql );

		/*Table structure for table `reaction_total` */
		$sql = "CREATE TABLE IF NOT EXISTS `{$reaction_total_table_name}` (
				`post_id` bigint(20) NOT NULL,
				`reaction_id` bigint(20) NOT NULL,
				`total` bigint(20) DEFAULT NULL,
				PRIMARY KEY (`post_id`,`reaction_id`)
			) {$charset_collate}; ";
		$reaction_total_table_status = $wpdb->query( $sql );

		/*Table structure for table `reactions` */
		$sql = "CREATE TABLE IF NOT EXISTS `{$reactions_table_name}` (
			`post_id` bigint(20) NOT NULL,
			`user_id` bigint(20) DEFAULT NULL,
			`ip_address` varchar(127) NOT NULL,
			`created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`reaction_id` bigint(20) NOT NULL,
			`session_id` varchar(255) NOT NULL
		) {$charset_collate}; ";
		$reactions_table_status = $wpdb->query( $sql );

		/*Table structure for table `view_total` */
		$sql = "CREATE TABLE IF NOT EXISTS `{$view_total_table_name}` (
			`post_id` bigint(20) unsigned NOT NULL,
			`total` bigint(20) unsigned NOT NULL,
			PRIMARY KEY (`post_id`)
		) {$charset_collate}; ";
		$view_total_table_status = $wpdb->query( $sql );

		/*Table structure for table `views` */
		$sql = "CREATE TABLE IF NOT EXISTS `{$views_table_name}` (
			`post_id` bigint(20) unsigned NOT NULL,
			`user_id` bigint(20) unsigned DEFAULT NULL,
			`ip_address` varchar(127) NOT NULL,
			`created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`session_id` varchar(255) NOT NULL,
			KEY `fk_posts_id` (`post_id`),
			KEY `fk_users_id` (`user_id`),
			KEY `k_ip` (`ip_address`)
		) {$charset_collate}; ";
		$views_table_status = $wpdb->query( $sql );

		/* Table structure for table `rate_schedule` */
		$sql = "CREATE TABLE IF NOT EXISTS `{$rate_schedule_table_name}` (
			`hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		    `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		    `limit` int(11) DEFAULT NULL,
		    PRIMARY KEY (`hash`)
		) {$charset_collate}; ";
		$rate_schedule_table_status = $wpdb->query( $sql );

		return (
			( false !== $point_total_table_status )
			&& ( false !== $points_table_status )
			&& ( false !== $reaction_total_table_status )
			&& ( false !== $reactions_table_status )
			&& ( false !== $view_total_table_status )
			&& ( false !== $views_table_status )
			&& ( false !== $rate_schedule_table_status )
		);
	}

	/**
	 * Drop database tables
	 * @return bool
	 */
	public static function drop_tables() {
		global $wpdb;
		$sql = '';

		$point_total_table_name    = $wpdb->prefix . 'point_total';
		$points_table_name         = $wpdb->prefix . 'points';
		$reaction_total_table_name = $wpdb->prefix . 'reaction_total';
		$reactions_table_name      = $wpdb->prefix . 'reactions';
		$view_total_table_name     = $wpdb->prefix . 'view_total';
		$views_table_name          = $wpdb->prefix . 'views';
		$rate_schedule_table_name  = $wpdb->prefix . 'rate_schedule';

		/* Drop `point_total` table */
		$sql = "DROP TABLE `{$point_total_table_name}`";
		$point_total_table_status = $wpdb->query( $sql );

		/* Drop `points` table */
		$sql = "DROP TABLE `{$points_table_name}`";
		$points_table_status = $wpdb->query( $sql );

		/* Drop `reaction_total` table */
		$sql = "DROP TABLE `{$reaction_total_table_name}`";
		$reaction_total_table_status = $wpdb->query( $sql );

		/* Drop `reactions` table */
		$sql = "DROP TABLE `{$reactions_table_name}`";
		$reactions_table_status = $wpdb->query( $sql );

		/* Drop `view_total` table */
		$sql = "DROP TABLE `{$view_total_table_name}`";
		$view_total_table_status = $wpdb->query( $sql );

		/* Drop `views` table */
		$sql = "DROP TABLE `{$views_table_name}`";
		$views_table_status = $wpdb->query( $sql );

		/* Drop `rate_schedule` table */
		$sql = "DROP TABLE `{$rate_schedule_table_name}`";
		$rate_schedule_table_status = $wpdb->query( $sql );

		return (
			( false !== $point_total_table_status )
			&& ( false !== $points_table_status )
			&& ( false !== $reaction_total_table_status )
			&& ( false !== $reactions_table_status )
			&& ( false !== $view_total_table_status )
			&& ( false !== $views_table_status )
			&& ( false !== $rate_schedule_table_status )
		);
	}

	/**
	 * Organize migration tasks
	 * @return bool
	 */
	public static function up() {
		return self::create_tables();
	}

	/**
	 * Revert migrations back
	 * @return bool
	 */
	public static function down() {
		return self::drop_tables();
	}

}