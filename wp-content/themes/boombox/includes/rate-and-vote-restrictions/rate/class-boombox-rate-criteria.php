<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Rate_Criteria' ) ) {
	/**
	 * Class Boombox_Rate_Criteria
	 */
	class Boombox_Rate_Criteria
	{
		/**
		 * @param Boombox_Rate_Criteria $object
		 *
		 * @return Boombox_Rate_Criteria
		 */
		public static function cast($object)
		{
			if (is_a($object, 'Boombox_Rate_Criteria')) {
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
		 * @var string DB table name prefix included
		 */
		protected $table_name;

		/**
		 * @param string $table_name DB table name prefix included
		 *
		 * @throws UnexpectedValueException
		 */
		protected function setTableName($table_name)
		{
			if (true !== ($error_message = Boombox_Exception_Helper::check_null_or_empty_string($table_name, '$table_name'))) {
				throw new UnexpectedValueException($error_message);
			}
			$this->table_name = $table_name;
		}

		/**
		 * @return string DB table name prefix included
		 */
		public function get_table_name()
		{
			return $this->table_name;
		}

		/**
		 * @var string
		 */
		protected $post_id_column_name = 'post_id';

		/**
		 * @param string $post_id_column_name
		 *
		 * @throws UnexpectedValueException
		 */
		public function set_post_id_column_name($post_id_column_name)
		{
			if (true !== ($error_message = Boombox_Exception_Helper::check_null_or_empty_string($post_id_column_name, '$post_id_column_name'))) {
				throw new UnexpectedValueException($error_message);
			}
			$this->post_id_column_name = $post_id_column_name;
		}

		/**
		 * @return string
		 */
		public function get_post_id_column_name()
		{
			return $this->post_id_column_name;
		}

		/**
		 * @var string
		 */
		protected $date_column_name = 'created_date';

		/**
		 * @param string $date_column_name
		 *
		 * @throws UnexpectedValueException
		 */
		public function set_date_column_name($date_column_name)
		{
			if (true !== ($error_message = Boombox_Exception_Helper::check_null_or_empty_string($date_column_name, '$date_column_name'))) {
				throw new UnexpectedValueException($error_message);
			}
			$this->date_column_name = $date_column_name;
		}

		/**
		 * @return string
		 */
		public function get_date_column_name()
		{
			return $this->date_column_name;
		}

		/**
		 * @var string
		 */
		protected $count_column_name = null;

		/**
		 * @param string $count_column_name
		 *
		 * @throws UnexpectedValueException
		 */
		public function set_count_column_name($count_column_name)
		{
			if (true !== ($error_message = Boombox_Exception_Helper::check_empty_string($count_column_name, '$count_column_name'))) {
				throw new UnexpectedValueException($error_message);
			}
			$this->count_column_name = $count_column_name;
		}

		/**
		 * @return string
		 */
		public function get_count_column_name()
		{
			return $this->count_column_name;
		}

		/**
		 * @return bool
		 */
		public function has_count_column()
		{
			return !is_null($this->count_column_name);
		}

		/**
		 * @param string $name
		 * @param string $display_name
		 * @param string $table_name
		 *
		 * @throws UnexpectedValueException
		 */
		function __construct($name, $display_name, $table_name)
		{
			try {
				$this->set_name($name);
				$this->set_display_name($display_name);
				$this->setTableName($table_name);
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
		 * @var array of Boombox_Rate_Criteria objects
		 */
		protected static $criterias = array();

		/**
		 * Initiates class statics
		 * @throws Exception
		 */
		public static function init_static_once()
		{
			if (false === static::$static_initiated) {
				static::$static_initiated = true;
				$criterias = apply_filters('boombox_rate_criterias', array());
				foreach ($criterias as $criteria) {
					if (!is_a($criteria, 'Boombox_Rate_Criteria')) {
						throw new Exception("Push variables of type Boombox_Rate_Criteria while using 'boombox_rate_criterias' filter.");
					}
					static::$criterias[] = $criteria;
				}
			}
		}

		/**
		 * @return array of Boombox_Rate_Criteria objects
		 */
		public static function get_criterias()
		{
			return static::$criterias;
		}

		/**
		 * @return array of strings
		 */
		public static function get_criteria_names()
		{
			$criteria_names = array();
			foreach (static::$criterias as $criteria) {
				$criteria = static::cast($criteria);
				$criteria_names[$criteria->get_name()] = $criteria->get_display_name();
			}

			return $criteria_names;
		}

		/**
		 * @param string $criteria_name
		 *
		 * @return Boombox_Rate_Criteria | null
		 */
		public static function get_criteria_by_name($criteria_name)
		{
			foreach (static::$criterias as $criteria) {
				$criteria = static::cast($criteria);
				if ($criteria_name === $criteria->get_name()) {
					return $criteria;
				}
			}
			return null;
		}
	}
}
add_action('init', array('Boombox_Rate_Criteria', 'init_static_once'), 5);