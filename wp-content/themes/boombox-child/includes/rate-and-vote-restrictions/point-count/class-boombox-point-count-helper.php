<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Point_Count_Helper' ) && trait_exists( 'Boombox_Vote_Restriction_Trait' ) ) {
	/**
	 * Class Boombox_Point_Count_Helper
	 */
	class Boombox_Point_Count_Helper
	{
		use Boombox_Vote_Restriction_Trait;
		
		/**
		 * Scales up the view point, can not be changed
		 * @var int
		 */
		protected static $point_scale;
		
		const ACTION_UP = 1;
		const ACTION_DOWN = 2;
		
		/**
		 * @param $type
		 *
		 * @return int
		 */
		protected static function get_point_scale( $type ) {
			$scale = abs( static::$point_scale );
			
			return self::ACTION_UP == $type ? $scale : ( 0 - $scale );
		}

		/**
		 * @return string the DB table name prefix included
		 */
		public static function get_point_total_table_name()
		{
			global $wpdb;

			return $wpdb->prefix . 'point_total';
		}

		/**
		 * @param int $post_id
		 *
		 * @return bool
		 */
		public static function pointed_up($post_id)
		{
			return static::voted(array(
				'post_id' => $post_id,
				'point' => static::get_point_scale( self::ACTION_UP )
			));
		}

		/**
		 * @param int $post_id
		 *
		 * @return bool
		 */
		public static function pointed_down($post_id)
		{
			return static::voted(array(
				'post_id' => $post_id,
				'point' => static::get_point_scale( self::ACTION_DOWN )
			));
		}

		/**
		 * @param mixed|array $values
		 *
		 * @return bool
		 */
		protected static function voted($values)
		{
			global $wpdb;
			$restriction = Boombox_Vote_Restriction::get_restriction_by_name(static::get_restriction_name());
			$settings = $restriction->get_settings();
			$db_settings = $restriction->get_db_settings();
			$where = ' 1 = 1 ';

			$values = Boombox_Vote_Restriction::prepare_values($db_settings->get_key_column_names(), $values);
			if (!$values) {
				return false;
			}
			foreach ($values as $key => $value) {
				$where .= $wpdb->prepare(" AND `" . $key . "` = %s ", $value);
			}

			if ($settings->need_to_check_user_daily() || $settings->need_to_check_user_total()) {
				$user_id = Boombox_Vote_Restriction::get_user_id();
				if (!$user_id) {
					return false;
				}
				$where .= $wpdb->prepare(" AND `" . $db_settings->get_user_id_column_name() . "` = %d ", $user_id );
			}
			if ($settings->need_to_check_ip_daily() || $settings->need_to_check_ip_total()) {
				$where .= $wpdb->prepare(" AND `" . $db_settings->get_ip_column_name() . "` = %s ", Boombox_Vote_Restriction::get_ip() );
			}
			if ($settings->need_to_check_session_total()) {
				$where .= $wpdb->prepare(" AND `" . $db_settings->get_session_column_name() . "` = %s ", Boombox_Vote_Restriction::get_session_id() );
			}

			$db_settings->get_key_column_names();
			$voted = $wpdb->get_var(" SELECT COUNT(*) FROM `" . static::get_table_name() . "` WHERE " . $where );
			return !$voted ? false : true;
		}

		/**
		 * @param int $post_id
		 *
		 * @return bool
		 */
		public static function can_point_up($post_id)
		{
			if (static::is_restriction_enabled()) {
				return Boombox_Vote_Restriction::check(static::get_restriction_name(), array('post_id' => $post_id, 'point' => static::get_point_scale( self::ACTION_UP )));
			}

			return true;
		}

		/**
		 * @param int $post_id
		 *
		 * @return bool
		 */
		public static function can_point_down($post_id)
		{
			if (static::is_restriction_enabled()) {
				return Boombox_Vote_Restriction::check(static::get_restriction_name(), array( 'post_id' => $post_id, 'point' => static::get_point_scale( self::ACTION_DOWN ) ));
			}

			return true;
		}

		/**
		 * @param int $post_id
		 * @param int $point
		 */
		protected static function insert_vote($post_id, $point)
		{
			global $wpdb;
			$data = array(
				'post_id' => $post_id,
				'ip_address' => Boombox_Vote_Restriction::get_ip(),
				'point' => $point,
				'session_id' => Boombox_Vote_Restriction::get_session_id()
			);
			$format = array('%d', '%s', '%d', '%s');
			$user_id = Boombox_Vote_Restriction::get_user_id();
			if ($user_id) {
				$data['user_id'] = $user_id;
				$format[] = '%d';
			}
			$wpdb->insert(static::get_table_name(), $data, $format);
		}

		/**
		 * @param int $post_id
		 *
		 * @return bool
		 */
		protected static function update_point_total( $post_id, $scale, $is_discard = false )
		{
			global $wpdb;
			
			$result = $wpdb->query( $wpdb->prepare("
                INSERT INTO `" . static::get_point_total_table_name() . "` (`post_id`, `total`)
                VALUES ( %d, %d )
                ON DUPLICATE KEY UPDATE `total` = `total` + %d", $post_id, $scale, $scale
			) );
			
			if( !!$result ) {
				do_action( 'boombox/point_total_updated', $post_id, $scale, $is_discard );
			}
			
			return (!$result) ? false : true;
		}

		/**
		 * @param int $post_id
		 *
		 * @return bool
		 */
		public static function point_up($post_id)
		{
			global $wpdb;
			$status = true;
			if (static::is_restriction_enabled()) {
				$wpdb->query('LOCK TABLES ' . static::get_table_name() . ' WRITE');
				if (static::can_point_up($post_id)) {
					static::insert_vote($post_id, static::get_point_scale( self::ACTION_UP ));
				} else {
					$status = false;
				}
				$wpdb->query('UNLOCK TABLES');
			} else {
				static::insert_vote($post_id, static::get_point_scale( self::ACTION_UP ));
			}
			static::discard_point_down($post_id);
			static::update_point_total($post_id, static::get_point_scale( self::ACTION_UP ));

			return $status;
		}

		/**
		 * @param int $post_id
		 *
		 * @return bool
		 */
		public static function point_down($post_id)
		{
			global $wpdb;
			$status = true;
			if (static::is_restriction_enabled()) {
				$wpdb->query('LOCK TABLES ' . static::get_table_name() . ' WRITE');
				if (static::can_point_down($post_id)) {
					static::insert_vote($post_id,  static::get_point_scale( self::ACTION_DOWN ) );
				} else {
					$status = false;
				}
				$wpdb->query('UNLOCK TABLES');
			} else {
				static::insert_vote($post_id, static::get_point_scale( self::ACTION_DOWN ) );
			}
			static::discard_point_up($post_id);
			static::update_point_total($post_id, static::get_point_scale( self::ACTION_DOWN ));

			return $status;
		}

		/**
		 * @param int $post_id
		 *
		 * @return bool
		 */
		public static function discard_point_up($post_id)
		{
			$status = Boombox_Vote_Restriction::discard(static::get_restriction_name(), array('post_id' => $post_id, 'point' => static::get_point_scale( self::ACTION_UP )));
			if( $status ) {
				static::update_point_total($post_id, static::get_point_scale( self::ACTION_DOWN ), true);
			}
			return $status;
		}

		/**
		 * @param int $post_id
		 *
		 * @return bool
		 */
		public static function discard_point_down($post_id)
		{
			$status = Boombox_Vote_Restriction::discard( static::get_restriction_name(), array('post_id' => $post_id, 'point' => static::get_point_scale( self::ACTION_DOWN ) ));
			if( $status ) {
				static::update_point_total($post_id, static::get_point_scale( self::ACTION_UP ), true);
			}
			return $status;
		}

		/**
		 * @param int $post_id
		 *
		 * @return int
		 */
		public static function get_post_points( $post_id )
		{
			global $wpdb;
			$result = $wpdb->get_var( $wpdb->prepare("
            	SELECT `total`
            	FROM `" . static::get_point_total_table_name() . "`
            	WHERE `post_id` = %d", $post_id
			) );

			return apply_filters( 'post_points_count', ( (!$result) ? 0 : intval($result) ), $post_id );
		}

		/**
		 * Evaluates trait static fields
		 */
		public static function init()
		{
			static::$restriction_name = 'point_count';
			static::$table_name = 'points';
			static::$point_scale = 1;
		}
	}
}
Boombox_Point_Count_Helper::init();