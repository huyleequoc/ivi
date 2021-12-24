<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Rate_Schedule' ) ) {
	/**
	 * Class Boombox_Rate_Schedule
	 */
	class Boombox_Rate_Schedule {
		/**
		 * @return string the DB table name prefix included
		 */
		protected static function get_table_name(){
			global $wpdb;
			return $wpdb->prefix . 'rate_schedule';
		}

		public static function delete_obsoletes(){
			global $wpdb;
			$wpdb->query('
				DELETE FROM ' . static::get_table_name() . '
				WHERE `created_date` <= DATE_ADD( CURDATE(), INTERVAL -3 DAY )
			');
		}

		/**
		 * @param string $hash
		 *
		 * @return bool
		 */
		public static function need_to_execute( $hash ){
			// todo -backend: need to be scheduled
			Boombox_Rate_Schedule::delete_obsoletes();
			global $wpdb;
			$query = $wpdb->prepare("
				SELECT count(*)
				FROM " . static::get_table_name() . "
				WHERE `hash` = %s AND `created_date` <= DATE_ADD( CURDATE(), INTERVAL -1 DAY )
			", $hash );
			$result = $wpdb->get_var($query);
			if(!!$result){
				$query = $wpdb->prepare("
					UPDATE " . static::get_table_name() . "
					SET `created_date` = CURDATE( )
					WHERE `hash` = '%s'
				", $hash);
				$wpdb->query($query);
				return true;
			}
			return false;
		}

		/**
		 * @param string $hash
		 * @param int $limit
		 */
		public static function add_schedule( $hash, $limit ){
			global $wpdb;
			$query = $wpdb->prepare("
				INSERT INTO " . static::get_table_name() . " (`hash`, `limit`, `created_date`)
				VALUES ( %s, %d, DATE_ADD( CURDATE( ), INTERVAL -1 DAY ) )
				ON DUPLICATE KEY UPDATE `limit` = GREATEST( `limit`, %d )
			", $hash, $limit, $limit);
			
			$wpdb->query($query);
		}
	}
}