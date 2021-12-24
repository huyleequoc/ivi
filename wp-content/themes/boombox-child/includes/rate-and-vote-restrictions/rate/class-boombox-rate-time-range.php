<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Rate_Time_Range' ) ) {
	/**
	 * Class Boombox_Rate_Time_Range
	 */
	class Boombox_Rate_Time_Range
	{
		/**
		 * @param Boombox_Rate_Time_Range $object
		 *
		 * @return Boombox_Rate_Time_Range
		 */
		public static function cast($object)
		{
			if (is_a($object, 'Boombox_Rate_Time_Range')) {
				return $object;
			}
			return null;
		}

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @param string $name
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
		 * @var string
		 */
		protected $display_name;

		/**
		 * @param string $display_name
		 *
		 * @throws UnexpectedValueException
		 */
		protected function set_display_name($display_name)
		{
			if (true !== ($error_message = Boombox_Exception_Helper::check_null_or_empty_string($display_name, '$display_name'))) {
				throw new UnexpectedValueException($error_message);
			}
			$this->display_name = $display_name;
		}

		/**
		 * @return string
		 */
		public function get_display_name()
		{
			return $this->display_name;
		}

		/**
		 * @var int
		 */
		protected $day_count = 1;

		/**
		 * @param int $day_count
		 *
		 * @throws UnexpectedValueException
		 */
		protected function set_day_count($day_count)
		{
			if (true !== ($error_message = Boombox_Exception_Helper::check_positive_or_minus_one_number($day_count, '$day_count'))) {
				throw new UnexpectedValueException($error_message);
			}
			$this->day_count = $day_count;
		}

		/**
		 * @return int
		 */
		public function get_day_count()
		{
			return $this->day_count;
		}

		/**
		 * @param string $name
		 * @param string $display_name
		 * @param int $day_count
		 *
		 * @throws UnexpectedValueException
		 */
		public function __construct($name, $display_name, $day_count = 1)
		{
			try {
				$this->set_name($name);
				$this->set_display_name($display_name);
				$this->set_day_count($day_count);
			} catch (UnexpectedValueException $ex) {
				throw $ex;
			}
		}

		/**
		 * Holds static initiation status
		 * @var bool
		 */
		protected static $staticInitiated = false;
		/**
		 * Holds array of Boombox_Rate_Time_Range objects
		 * @var array
		 */
		protected static $time_ranges = array();

		/**
		 * Initiates class statics
		 *
		 * @throws Exception
		 */
		public static function init_static_once()
		{
			if (false === static::$staticInitiated) {
				static::$staticInitiated = true;
				$time_ranges = apply_filters('boombox_rate_time_range', array());
				foreach ($time_ranges as $time_range) {
					if (!is_a($time_range, 'Boombox_Rate_Time_Range')) {
						throw new Exception("Push variables of type Boombox_Rate_Time_Range while using 'boombox_rate_time_range' filter.");
					}
					static::$time_ranges[] = $time_range;
				}

			}
		}

		/**
		 * @return array of Boombox_Rate_Time_Range objects
		 */
		static function get_time_ranges()
		{
			return static::$time_ranges;
		}

		/**
		 * @return array
		 */
		static function get_time_range_names()
		{
			$time_range_names = array();
			foreach (static::$time_ranges as $time_range) {
				$time_range = static::cast($time_range);
				$time_range_names[$time_range->get_name()] = $time_range->get_display_name();
			}

			return $time_range_names;
		}

		/**
		 * @param string $time_range_name
		 *
		 * @return Boombox_Rate_Time_Range
		 *
		 * @throws UnexpectedValueException
		 */
		static function get_time_range_by_name($time_range_name)
		{
			foreach (static::$time_ranges as $time_range) {
				$time_range = static::cast($time_range);
				if ($time_range_name === $time_range->get_name()) {
					return $time_range;
				}
			}
			throw new UnexpectedValueException('Invalid rate time range name. Value $time_range_name: ' . $time_range_name);
		}
	}
}
add_action('init', array('Boombox_Rate_Time_Range', 'init_static_once'), 5);