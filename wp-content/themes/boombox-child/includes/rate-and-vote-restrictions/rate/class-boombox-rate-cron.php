<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Rate_Cron' ) ) {
	/**
	 * Class Boombox_Rate_Cron
	 */
	class Boombox_Rate_Cron
	{
		/**
		 * @var int
		 */
		protected static $schedule_time = 86400; // 60 * 60 * 24 one day

		/**
		 * @param int $schedule_time
		 *
		 * @throws UnexpectedValueException
		 */
		protected static function set_schedule_time($schedule_time)
		{
			if (true !== ($error_message = Boombox_Exception_Helper::check_positive_number($schedule_time, '$schedule_time'))) {
				throw new UnexpectedValueException($error_message);
			}
			static::$schedule_time = $schedule_time;
		}

		/**
		 * @return int
		 */
		public static function get_schedule_time()
		{
			return static::$schedule_time;
		}

		/**
		 * @var array of Boombox_Rate_Job
		 */
		protected static $jobs = array();

		/**
		 * @param Boombox_Rate_Job $job
		 * @return string
		 */
		public static function get_meta_key(Boombox_Rate_Job $job)
		{
			// return 'boombox_rate_' . $job->get_name() . '_' . static::get_hash($job);
			return 'boombox_rate_' . static::get_hash($job);
		}

		/**
		 * @param string $hash
		 * @param int $limit
		 * @return bool
		 */
		protected static function job_exists($hash, $limit)
		{
			if (!isset(static::$jobs[$hash]) || $limit > static::$jobs[$hash]) {
				static::$jobs[$hash] = $limit;

				return false;
			}

			return true;
		}

		/**
		 * @param Boombox_Rate_Job $job
		 * @return string
		 */
		static public function get_hash(Boombox_Rate_Job $job)
		{
			$criteria = $job->get_criteria();
			$time_range = $job->get_time_range();

			return 'boombox_hash_' . md5(
				$criteria->get_name() . '#'
				. $time_range->get_name() . '#'
				. $job->get_min_count() . '#'
				. implode('', $job->get_post_types()) . '#'
				. implode('', $job->get_post_statuses())
			);
		}

		/**
		 * @param Boombox_Rate_Job $job
		 */
		static public function register_job(Boombox_Rate_Job $job)
		{
			$hash = static::get_hash($job);
			if(false === $job->is_live()){
				Boombox_Rate_Schedule::add_schedule( $hash, $job->get_limit() );
			}
			static::do_job($job, $hash);
		}

		/**
		 * @param Boombox_Rate_Job $job
		 * @param string $hash
		 */
		static protected function do_job(Boombox_Rate_Job $job, $hash)
		{
			do_action( 'boombox_rate_before_do_job', $job, $hash );
			if(true === $job->is_live() || Boombox_Rate_Schedule::need_to_execute($hash)){
				static::update_ratings($job);
			}
		}

		/**
		 * @param Boombox_Rate_Job $job
		 */
		static protected function update_ratings(Boombox_Rate_Job $job)
		{
			global $wpdb;
			$meta_key = static::get_meta_key($job);
			$criteria = $job->get_criteria();
			$post_statuses = '';
			foreach ($job->get_post_statuses() AS $post_status) {
				$post_statuses .= $wpdb->prepare('"%s",', $post_status);
			}
			$post_statuses = rtrim($post_statuses, ",");
			$post_types = '';
			foreach ($job->get_post_types() AS $post_type) {
				$post_types .= $wpdb->prepare('"%s",', $post_type);
			}
			$post_types = rtrim($post_types, ",");
			$where = apply_filters('boombox_rate_where', ' 1=1 ', $job);
			$where = apply_filters('boombox_rate_where_' . $job->get_name(), $where, $job);
			if ($criteria->has_count_column()) {
				$aggregation = " SUM(`" . $criteria->get_count_column_name() . "`) ";
			} else {
				$aggregation = ' COUNT(*) ';
			}

			$query = $wpdb->prepare("
				SELECT `rate_table`.`" . $criteria->get_post_id_column_name() . "`, '%s' AS `rate_name`, " . $aggregation . " AS `rate_count`
				FROM `" . $criteria->get_table_name() . "` AS `rate_table`
					JOIN `" . $wpdb->posts . "` AS `rate_posts`
					    ON `rate_posts`.`ID` = `rate_table`.`" . $criteria->get_post_id_column_name() . "`
					    AND `rate_posts`.`post_type` In( " . $post_types . " )
					    AND `rate_posts`.`post_status` In( " . $post_statuses . " )
					WHERE " . $where . "
                    GROUP BY `rate_table`.`" . $criteria->get_post_id_column_name() . "`
					HAVING `rate_count` >= %d
					ORDER BY `rate_count` DESC
					LIMIT %d
		        ",
				$meta_key,
				$job->get_min_count(),
				$job->get_limit()
			);

			$rated_items = $wpdb->get_results($query, ARRAY_A);
			if( !is_array($rated_items) ){
				$rated_items = array();
			}

			$filtered_rated_items = array();
			foreach ( $rated_items as $rated_item ){
				$filtered_rated_items[] = array(
					'post_id' => $rated_item[ $criteria->get_post_id_column_name() ],
					'rating' => $rated_item[ 'rate_count' ]
				);
			}
			$filtered_rated_items = apply_filters( 'boombox_rated_items_' . static::get_hash( $job ), $filtered_rated_items );

			$wpdb->delete($wpdb->postmeta, array('meta_key' => $meta_key), array('%s'));

			/***** new functionality: This way we prevent multiple queries execution as we're using foreach loop */
			$values = array();
			foreach( $filtered_rated_items as $rated_item ){
				$values[] = $wpdb->prepare( "( %d, %s, %d )", $rated_item['post_id'], $meta_key, $rated_item['rating'] );
			}
			if( ! empty( $values ) ) {
				$values = implode( ', ', $values );
				$query = "
					INSERT INTO `" . $wpdb->postmeta . "` 
						( `post_id`, `meta_key`, `meta_value` ) 
					VALUES " . $values . ";";

				$wpdb->query( $query );
			}
		}

		/**
		 * Holds static initiation status
		 * @var bool
		 */
		protected static $staticInitiated = false;

		/**
		 * Initiates class statics
		 */
		public static function init_static_once()
		{
			if (false === static::$staticInitiated) {
				static::$staticInitiated = true;

				$config = apply_filters('boombox_rate_cron_config', array(
					'schedule_time' => static::$schedule_time
				));
				static::set_schedule_time($config['schedule_time']);
			}
		}

		/**
		 * @param string $where
		 * @param Boombox_Rate_Job $job
		 * @return string
		 */
		public static function filter_rate_where($where, Boombox_Rate_Job $job)
		{
			global $wpdb;
			$criteria = $job->get_criteria();
			$time_range = $job->get_time_range();
			if (0 < $time_range->get_day_count()) {
				$where_fragment = $wpdb->prepare(
					" AND `" . $criteria->get_date_column_name() . "` > DATE_ADD( CURDATE( ), INTERVAL -%d DAY ) ",
					$time_range->get_day_count()
				);
				if ($where_fragment) {
					$where .= $where_fragment;
				}
			}

			return $where;
		}

		/**
		 * @param Boombox_Rate_Job $job
		 * @param int $post_id
		 * @return bool
		 */
		public static function is_post_rated(Boombox_Rate_Job $job, $post_id)
		{
			return !!boombox_get_post_meta( $post_id, static::get_meta_key( $job ) );
		}
	}
}
add_filter('boombox_rate_where', array('Boombox_Rate_Cron', 'filter_rate_where'), 10, 2);
add_action('init', array('Boombox_Rate_Cron', 'init_static_once'));