<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_View_Count_Helper' ) && trait_exists( 'Boombox_Vote_Restriction_Trait' ) ) {
	/**
	 * Class Boombox_View_Count_Helper
	 */
	class Boombox_View_Count_Helper
	{
		use Boombox_Vote_Restriction_Trait;

		/**
		 * @return string DB table name prefix included
		 */
		public static function get_view_total_table_name()
		{
			global $wpdb;

			return $wpdb->prefix . 'view_total';
		}

		/**
		 * Scales up the view point
		 * @var int
		 */
		protected static $view_point_scale;

		/**
		 * Checks the restriction, if its turned off then returns true
		 * @param int $post_id
		 * @return bool
		 */
		public static function check($post_id)
		{
			if (static::is_restriction_enabled()) {
				return Boombox_Vote_Restriction::check(static::get_restriction_name(), $post_id);
			}

			return true;
		}

		/**
		 * Adds new record about the post being viewed by  user, session, ip
		 * @param int $post_id
		 */
		protected static function insert_view($post_id)
		{
			global $wpdb;
			$data = array(
				'post_id' => $post_id,
				'ip_address' => Boombox_Vote_Restriction::get_ip(),
				'session_id' => Boombox_Vote_Restriction::get_session_id(),
				'point' => static::$view_point_scale
			);
			$format = array('%d', '%s', '%s', '%d');
			$user_id = Boombox_Vote_Restriction::get_user_id();
			if ($user_id) {
				$data['user_id'] = $user_id;
				$format[] = '%d';
			}
			$wpdb->insert(static::get_table_name(), $data, $format);
		}

		/**
		 * Increments the post's total view count
		 * @param int $post_id
		 * @return bool
		 */
		protected static function update_view_total($post_id)
		{
			global $wpdb;
			$result = $wpdb->query( $wpdb->prepare("
				INSERT INTO `" . static::get_view_total_table_name() . "` (`post_id`, `total`)
				VALUES ( %d, %d )
				ON DUPLICATE KEY UPDATE `total` = `total` + %d
			", $post_id, static::$view_point_scale, static::$view_point_scale ) );

			if( !!$result ) {
				do_action( 'boombox/view_total_updated', static::$view_point_scale, $post_id );
			}

			return (!$result) ? false : true;
		}

		/**
		 * Locks tables and calls insert_view and update_view_total functions
		 * @param int $post_id
		 * @return bool
		 */
		public static function add_view($post_id)
		{
			
			$allow_view_count_up = apply_filters( 'boombox/allow_view_count_up', true, $post_id );
			if( ! $allow_view_count_up ) {
				return false;
			}
			
			global $wpdb;
			$status = true;
			if (static::is_restriction_enabled()) {
				$wpdb->query('LOCK TABLES ' . static::get_table_name() . ' WRITE');
				if (static::check($post_id)) {
					static::insert_view($post_id);
				} else {
					$status = false;
				}
				$wpdb->query('UNLOCK TABLES');
			} else {
				static::insert_view($post_id);
			}
			
			if ($status) {
				static::update_view_total($post_id);
			}
			return $status;
		}

		/**
		 * @param int $post_id
		 * @return int total count of views for the post
		 */
		public static function get_post_views($post_id)
		{
			global $wpdb;
			$result = $wpdb->get_var( $wpdb->prepare("
	            SELECT `total`
	            FROM `" . static::get_view_total_table_name() . "`
	            WHERE `post_id` = %d
	        ", $post_id ) );

			return apply_filters( 'post_views_count', ( (!$result) ? 0 : intval($result) ), $post_id );
		}

		/**
		 * Initiates class statics
		 */
		public static function init()
		{
			static::$restriction_name = 'view_count';
			static::$table_name = 'views';

			$view_point_scale = boombox_get_theme_option( 'extras_post_ranking_system_views_count_scale' );
			if( 0 >= absint( $view_point_scale ) ){
				$view_point_scale = 1;
			}
			static::$view_point_scale = $view_point_scale;
		}
	}
}
Boombox_View_Count_Helper::init();