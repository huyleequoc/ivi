<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Rate_Job' ) ) {
	/**
	 * Class Boombox_Rate_Job
	 */
	class Boombox_Rate_Job
	{
		/**
		 * Max number of posts that will be processed
		 * @var int
		 */
		const MAX_LIMIT = 200;

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @param $name string
		 *
		 * @throws UnexpectedValueException
		 */
		protected function set_name($name)
		{
			if (true !== ($error_message = Boombox_Exception_Helper::check_null_or_empty_string($name, '$name'))) {
				throw new UnexpectedValueException($error_message);
			}
			$this->name = $name;
		}

		/**
		 * @return string
		 */
		public function get_name()
		{
			return $this->name;
		}

		/**
		 * @var bool
		 */
		protected $is_live = false;

		/**
		 * @param string $is_live
		 */
		public function set_live($is_live){
			$this->is_live = !!$is_live;
		}

		/**
		 * @return bool
		 */
		public function is_live(){
			return $this->is_live;
		}

		/**
		 * @var Boombox_Rate_Criteria
		 */
		protected $criteria = null;

		/**
		 * @param $criteria Boombox_Rate_Criteria
		 */
		protected function set_criteria(Boombox_Rate_Criteria $criteria)
		{
			$this->criteria = $criteria;
		}

		/**
		 * @return Boombox_Rate_Criteria
		 */
		public function get_criteria()
		{
			return $this->criteria;
		}

		/**
		 * @var Boombox_Rate_Time_Range
		 */
		protected $time_range = null;

		/**
		 * @param $timeRange Boombox_Rate_Time_Range
		 */
		protected function set_time_range(Boombox_Rate_Time_Range $timeRange)
		{
			$this->time_range = $timeRange;
		}

		/**
		 * @return Boombox_Rate_Time_Range
		 */
		public function get_time_range()
		{
			return $this->time_range;
		}

		/**
		 * Holds number of posts that will be processed
		 * @var int
		 */
		protected $limit = -1;

		/**
		 * @param int $rate_limit
		 *
		 * @throws UnexpectedValueException
		 */
		protected function set_limit($rate_limit)
		{
			if (true !== ($error_message = Boombox_Exception_Helper::check_positive_or_minus_one_number($rate_limit, '$rate_limit'))) {
				throw new UnexpectedValueException($error_message);
			}
			$this->limit = (-1 === $rate_limit || static::MAX_LIMIT < $rate_limit) ? static::MAX_LIMIT : $rate_limit;
		}

		/**
		 * @return int
		 */
		public function get_limit()
		{
			return $this->limit;
		}

		/**
		 * @var array of strings
		 */
		protected $post_types = array();

		/**
		 * @param string $post_type
		 *
		 * @return bool
		 */
		protected function add_post_type($post_type)
		{
			$post_type = strtolower($post_type);
			if (post_type_exists($post_type) && !in_array($post_type, $this->post_types)) {
				$this->post_types[] = $post_type;

				return true;
			}

			return false;
		}

		/**
		 * @param array $post_types of strings
		 * @throws UnexpectedValueException
		 */
		protected function set_post_types(array $post_types)
		{
			foreach ($post_types as $post_type) {
				$this->add_post_type($post_type);
			}
			if (0 < count($this->post_types)) {
				sort($this->post_types);
			} else {
				throw new UnexpectedValueException('Argument $post_types must contain at least one valid registered post type.');
			}
		}

		/**
		 * @return array of strings
		 */
		public function get_post_types()
		{
			return $this->post_types;
		}

		/**
		 * @var array of strings
		 */
		protected $post_statuses = array();

		/**
		 * @param string $post_status
		 *
		 * @return bool
		 */
		protected function add_post_status($post_status)
		{
			$post_status = strtolower($post_status);
			if (post_status_exists($post_status) && !in_array($post_status, $this->post_statuses)) {
				$this->post_statuses[] = $post_status;

				return true;
			}

			return false;
		}

		/**
		 * @param array $post_statuses of strings
		 *
		 * @throws UnexpectedValueException
		 */
		protected function set_post_statuses(array $post_statuses)
		{
			foreach ($post_statuses as $post_status) {
				$this->add_post_status($post_status);
			}
			if (0 < count($this->post_statuses)) {
				sort($this->post_statuses);
			} else {
				throw new UnexpectedValueException('Argument $post_statuses must contain at least one valid registered post status.');
			}
		}

		/**
		 * @return array of strings
		 */
		public function get_post_statuses()
		{
			return $this->post_statuses;
		}

		/**
		 * Min count of rating that post must have to be included in rating list
		 * @var int
		 */
		protected $min_count = 1;

		/**
		 * @param int $min_count
		 *
		 * @throws UnexpectedValueException
		 */
		protected function set_min_count($min_count)
		{
			if (true !== ($error_message = Boombox_Exception_Helper::check_positive_or_zero_number($min_count, '$min_count'))) {
				throw new UnexpectedValueException($error_message);
			}
			$this->min_count = $min_count;
		}

		/**
		 * @return int
		 */
		public function get_min_count()
		{
			return $this->min_count;
		}

		/**
		 * @param string $name
		 * @param array|string $post_types
		 * @param array|string $post_statuses
		 * @param string $rate_criteria_name
		 * @param string $rate_time_range_name
		 * @param int $rate_limit
		 * @param int $min_count
		 * @param bool $is_live
		 *
		 * @throws UnexpectedValueException
		 */
		function __construct($name, $post_types, $post_statuses, $rate_criteria_name, $rate_time_range_name, $rate_limit = -1, $min_count = 0, $is_live = false)
		{
			if (!is_array($post_types)) {
				$post_types = array($post_types);
			}
			if (!is_array($post_statuses)) {
				$post_statuses = array($post_statuses);
			}
			try {
				$this->set_name($name);
				$this->set_post_types($post_types);
				$this->set_post_statuses($post_statuses);
				$this->set_limit($rate_limit);
				$this->set_min_count($min_count);
				$this->set_criteria(Boombox_Rate_Criteria::get_criteria_by_name($rate_criteria_name));
				$this->set_time_range(Boombox_Rate_Time_Range::get_time_range_by_name($rate_time_range_name));
				$this->set_live($is_live);
			} catch (UnexpectedValueException $ex) {
				throw $ex;
			}
		}

		/**
		 * Holds static initiation status
		 * @var bool
		 */
		protected static $static_initiated = false;
		/**
		 * @var array of Boombox_Rate_Job
		 */
		protected static $jobs = array();

		/**
		 * Initiates class statics
		 * @throws Exception
		 */
		public static function init_static_once()
		{
			if (false === static::$static_initiated) {
				static::$static_initiated = true;
				$jobs = apply_filters('boombox_rate_jobs', array());
				foreach ($jobs as $job_name => $job) {
					if (!is_a($job, 'Boombox_Rate_Job')) {
						throw new Exception("Push variables of type Boombox_Rate_Job while using 'boombox_rate_jobs' filter. Assoc array format is 'job_name' => " . '$job');
					}
					static::$jobs[$job_name] = $job;
				}
				do_action( 'boombox_rate_jobs_register', static::$jobs );
			}
		}

		/**
		 * @param string $job_name
		 *
		 * @return Boombox_Rate_Job | null
		 */
		public static function get_job_by_name($job_name)
		{
			foreach (static::$jobs as $key => $job) {
				if ($job_name === $key) {
					return $job;
				}
			}
			return null;
		}
	}
}
add_action('init', array('Boombox_Rate_Job', 'init_static_once'));