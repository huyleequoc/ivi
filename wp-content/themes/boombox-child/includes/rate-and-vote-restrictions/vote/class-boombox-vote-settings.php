<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Vote_Settings' ) ) {
	/**
	 * Class Boombox_Vote_Settings
	 */
	class Boombox_Vote_Settings {
		const MAX_VOTE_COUNT = 2147483647;

		const USER_TOTAL = 0b00001;
		const USER_DAILY = 0b00010;
		const IP_TOTAL = 0b00100;
		const IP_DAILY = 0b01000;
		const SESSION_TOTAL = 0b10000;

		/**
		 * Holds combination of flags including USER_TOTAL | USER_DAILY | IP_TOTAL | IP_DAILY | SESSION_TOTAL
		 * @var int
		 */
		protected $flags;

		/**
		 * @param int $flags Combination of flags including USER_TOTAL | USER_DAILY | IP_TOTAL | IP_DAILY | SESSION_TOTAL
		 */
		protected function set_flags( $flags ) {
			$this->flags = $flags;
		}

		/**
		 * @param int $flag Combination of flags including USER_TOTAL | USER_DAILY | IP_TOTAL | IP_DAILY | SESSION_TOTAL
		 *
		 * @return bool
		 */
		protected function check_flag( $flag ) {
			return ( bool ) ( $flag & $this->flags );
		}

		/**
		 * Checks for USER_TOTAL flag
		 * @return bool
		 */
		public function need_to_check_user_total() {
			return $this->check_flag( Boombox_Vote_Settings::USER_TOTAL );
		}

		/**
		 * Checks for USER_DAILY flag
		 * @return bool
		 */
		public function need_to_check_user_daily() {
			return $this->check_flag( Boombox_Vote_Settings::USER_DAILY );
		}

		/**
		 * Checks for IP_TOTAL flag
		 * @return bool
		 */
		public function need_to_check_ip_total() {
			return $this->check_flag( Boombox_Vote_Settings::IP_TOTAL );
		}

		/**
		 * Checks for IP_DAILY flag
		 * @return bool
		 */
		public function need_to_check_ip_daily() {
			return $this->check_flag( Boombox_Vote_Settings::IP_DAILY );
		}

		/**
		 * Checks for SESSION_TOTAL flag
		 * @return bool
		 */
		public function need_to_check_session_total() {
			return $this->check_flag( Boombox_Vote_Settings::SESSION_TOTAL );
		}

		/**
		 * Holds total number of user interactions
		 * @var int
		 */
		protected $user_total;

		/**
		 * @param int $user_total Holds total number of user interactions
		 *
		 * @throws UnexpectedValueException
		 */
		protected function set_user_total( $user_total ) {
			if ( true !== ( $error_message = Boombox_Exception_Helper::check_positive_number( $user_total, '$user_total' ) ) ) {
				throw new UnexpectedValueException( $error_message );
			}
			$this->user_total = $user_total;
		}

		/**
		 * @return int
		 */
		public function get_user_total() {
			return $this->user_total;
		}

		/**
		 * Holds total number of user daily interactions
		 * @var int
		 */
		protected $user_daily;

		/**
		 * @param int $user_daily Holds total number of user daily interactions
		 *
		 * @throws UnexpectedValueException
		 */
		protected function set_user_daily( $user_daily ) {
			if ( true !== ( $error_message = Boombox_Exception_Helper::check_positive_number( $user_daily, '$user_daily' ) ) ) {
				throw new UnexpectedValueException( $error_message );
			}
			$this->user_daily = $user_daily;
		}

		/**
		 * @return int
		 */
		public function get_user_daily() {
			return $this->user_daily;
		}

		/**
		 * Holds total number of session interactions
		 * @var int
		 */
		protected $session_total;

		/**
		 * @param int $session_total Holds total number of session interactions
		 *
		 * @throws UnexpectedValueException
		 */
		protected function set_session_total( $session_total ) {
			if ( true !== ( $error_message = Boombox_Exception_Helper::check_positive_number( $session_total, '$session_total' ) ) ) {
				throw new UnexpectedValueException( $error_message );
			}
			$this->session_total = $session_total;
		}

		/**
		 * @return int
		 */
		public function get_session_total() {
			return $this->session_total;
		}

		/**
		 * Holds total number of ip interactions
		 * @var int
		 */
		protected $ip_total;

		/**
		 * @param int $ip_total Holds total number of ip interactions
		 *
		 * @throws UnexpectedValueException
		 */
		protected function set_ip_total( $ip_total ) {
			if ( true !== ( $error_message = Boombox_Exception_Helper::check_positive_number( $ip_total, '$ip_total' ) ) ) {
				throw new UnexpectedValueException( $error_message );
			}
			$this->ip_total = $ip_total;
		}

		/**
		 * @return int
		 */
		public function get_ip_total() {
			return $this->ip_total;
		}

		/**
		 * Holds total number of ip daily interactions
		 * @var int
		 */
		protected $ip_daily;

		/**
		 * @param int $ip_daily Holds total number of ip daily interactions
		 *
		 * @throws UnexpectedValueException
		 */
		protected function set_ip_daily( $ip_daily ) {
			if ( true !== ( $error_message = Boombox_Exception_Helper::check_positive_number( $ip_daily, '$ip_daily' ) ) ) {
				throw new UnexpectedValueException( $error_message );
			}
			$this->ip_daily = $ip_daily;
		}

		/**
		 * @return int
		 */
		public function get_ip_daily() {
			return $this->ip_daily;
		}

		/**
		 * @param int $flags Holds combination of flags including USER_TOTAL | USER_DAILY | IP_TOTAL | IP_DAILY | SESSION_TOTAL
		 * @param int $user_total Holds total number of user interactions
		 * @param int $user_daily Holds total number of user daily interactions
		 * @param int $session_total Holds total number of session interactions
		 * @param int $ip_total Holds total number of ip interactions
		 * @param int $ip_daily Holds total number of ip daily interactions
		 *
		 * @throws UnexpectedValueException
		 */
		function __construct(
			$flags,
			$user_total = 1,
			$user_daily = 1,
			$session_total = 1,
			$ip_total = 1,
			$ip_daily = 1
		) {
			$this->set_flags( $flags );
			try {
				$this->set_user_total( $user_total );
				$this->set_user_daily( $user_daily );
				$this->set_session_total( $session_total );
				$this->set_ip_total( $ip_total );
				$this->set_ip_daily( $ip_daily );
			} catch ( UnexpectedValueException $ex ) {
				throw $ex;
			}
		}
	}
}