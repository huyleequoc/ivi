<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Vote_Db_Settings' ) ) {
	/**
	 * Class Boombox_Vote_Db_Settings
	 */
	class Boombox_Vote_Db_Settings {
		/**
		 * @var string Holds DB table name prefix included
		 */
		protected $table_name = null;

		/**
		 * @param string $table_name the DB table name prefix included
		 *
		 * @throws UnexpectedValueException
		 */
		public function set_table_name( $table_name ) {
			if ( true !== ( $error_message = Boombox_Exception_Helper::check_null_or_empty_string( $table_name, '$table_name' ) ) ) {
				throw new UnexpectedValueException( $error_message );
			}
			$this->table_name = $table_name;
		}

		/**
		 * @return string the DB table name prefix included
		 */
		public function get_table_name() {
			return $this->table_name;
		}

		/**
		 * @var string
		 */
		protected $user_id_column_name = 'user_id';

		/**
		 * @param string $user_id_column_name
		 *
		 * @throws UnexpectedValueException
		 */
		public function set_user_id_column_name( $user_id_column_name ) {
			if ( true !== ( $error_message = Boombox_Exception_Helper::check_null_or_empty_string( $user_id_column_name, '$user_id_column_name' ) ) ) {
				throw new UnexpectedValueException( $error_message );
			}
			$this->user_id_column_name = $user_id_column_name;
		}

		/**
		 * @return string
		 */
		public function get_user_id_column_name() {
			return $this->user_id_column_name;
		}

		/**
		 * @var string
		 */
		protected $ip_column_name = 'ip_address';

		/**
		 * @param string $ip_column_name
		 *
		 * @throws UnexpectedValueException
		 */
		public function set_ip_column_name( $ip_column_name ) {
			if ( true !== ( $error_message = Boombox_Exception_Helper::check_null_or_empty_string( $ip_column_name, '$ip_column_name' ) ) ) {
				throw new UnexpectedValueException( $error_message );
			}
			$this->ip_column_name = $ip_column_name;
		}

		/**
		 * @return string
		 */
		public function get_ip_column_name() {
			return $this->ip_column_name;
		}

		/**
		 * @var string
		 */
		protected $session_column_name = 'session_id';

		/**
		 * @param string $session_column_name
		 *
		 * @throws UnexpectedValueException
		 */
		public function set_session_column_name( $session_column_name ) {
			if ( true !== ( $error_message = Boombox_Exception_Helper::check_null_or_empty_string( $session_column_name, '$session_column_name' ) ) ) {
				throw new UnexpectedValueException( $error_message );
			}
			$this->session_column_name = $session_column_name;
		}

		/**
		 * @return string
		 */
		public function get_session_column_name() {
			return $this->session_column_name;
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
		public function set_date_column_name( $date_column_name ) {
			if ( true !== ( $error_message = Boombox_Exception_Helper::check_null_or_empty_string( $date_column_name, '$date_column_name' ) ) ) {
				throw new UnexpectedValueException( $error_message );
			}
			$this->date_column_name = $date_column_name;
		}

		/**
		 * @return string
		 */
		public function get_date_column_name() {
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
		public function set_count_column_name( $count_column_name ) {
			if ( true !== ( $error_message = Boombox_Exception_Helper::check_empty_string( $count_column_name, '$count_column_name' ) ) ) {
				throw new UnexpectedValueException( $error_message );
			}
			$this->count_column_name = $count_column_name;
		}

		/**
		 * @return string
		 */
		public function get_count_column_name() {
			return $this->count_column_name;
		}

		/**
		 * @return bool
		 */
		public function has_count_column() {
			return ! is_null( $this->count_column_name );
		}

		/**
		 * @var array
		 */
		protected $key_column_names = array( 'post_id' );

		/**
		 * @param string $key_column_name
		 *
		 * @throws UnexpectedValueException
		 */
		protected function add_key_column_name( $key_column_name ) {
			if ( true !== ( $error_message = Boombox_Exception_Helper::check_null_or_empty_string( $key_column_name, '$key_column_name' ) ) ) {
				throw new UnexpectedValueException( $error_message );
			}
			$this->key_column_names[] = $key_column_name;
		}

		/**
		 * @param string|array $key_column_names
		 *
		 * @throws UnexpectedValueException
		 */
		public function set_key_column_names( $key_column_names ) {
			$this->key_column_names = array();
			if ( ! is_array( $key_column_names ) ) {
				$key_column_names = array( $key_column_names );
			}
			try {
				foreach ( $key_column_names as $key_column_name ) {
					$this->add_key_column_name( $key_column_name );
				}
			} catch ( UnexpectedValueException $ex ) {
				throw $ex;
			}
		}

		/**
		 * @return array
		 */
		public function get_key_column_names() {
			return $this->key_column_names;
		}
	}
}