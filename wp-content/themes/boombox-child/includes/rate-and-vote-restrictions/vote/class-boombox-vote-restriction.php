<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Vote_Restriction' ) ) {
	/**
	 * Class Boombox_Vote_Restriction
	 */
	class Boombox_Vote_Restriction {
		/**
		 * @param Boombox_Vote_Restriction $object
		 *
		 * @return Boombox_Vote_Restriction
		 */
		public static function cast( $object ) {
			if ( is_a( $object, 'Boombox_Vote_Restriction' ) ) {
				return $object;
			}

			return null;
		}

		/**
		 * @var string Holds restriction name
		 */
		protected $name;

		/**
		 * @param string $name Holds restriction name
		 *
		 * @throws UnexpectedValueException
		 */
		protected function set_name( $name ) {
			if ( true !== ( $error_message = Boombox_Exception_Helper::check_null_or_empty_string( $name, '$name' ) ) ) {
				throw new UnexpectedValueException( $error_message );
			}
			$this->name = $name;
		}

		/**
		 * @return string the restriction name
		 */
		public function get_name() {
			return $this->name;
		}

		/**
		 * @var Boombox_Vote_Settings
		 */
		protected $settings;

		/**
		 * @param Boombox_Vote_Settings $settings
		 */
		protected function set_settings( Boombox_Vote_Settings $settings ) {
			$this->settings = $settings;
		}

		/**
		 * @return Boombox_Vote_Settings
		 */
		public function get_settings() {
			return $this->settings;
		}

		/**
		 * @var Boombox_Vote_Db_Settings
		 */
		protected $db_settings;

		/**
		 * @param Boombox_Vote_Db_Settings $db_settings
		 */
		protected function set_db_settings( Boombox_Vote_Db_Settings $db_settings ) {
			$this->db_settings = $db_settings;
		}

		/**
		 * @return Boombox_Vote_Db_Settings
		 */
		public function get_db_settings() {
			return $this->db_settings;
		}

		/**
		 * @param string                   $name the restriction name
		 * @param Boombox_Vote_Settings    $settings
		 * @param Boombox_Vote_Db_Settings $dbSettings
		 *
		 * @throws UnexpectedValueException
		 */
		public function __construct( $name, Boombox_Vote_Settings $settings, Boombox_Vote_Db_Settings $dbSettings ) {
			$name_exists = true;
			try {
				static::get_restriction_by_name( $name );
			} catch ( Exception $ex ) {
				$name_exists = false;
			}
			if ( $name_exists ) {
				throw new UnexpectedValueException( 'Restriction with given name already exists. $name: ' . $name );
			}
			try {
				$this->set_name( $name );
			} catch ( UnexpectedValueException $ex ) {
				throw $ex;
			}
			$this->set_settings( $settings );
			$this->set_db_settings( $dbSettings );
		}

		/**
		 * @var bool holds static initiation status
		 */
		protected static $static_initiated = false;

		/**
		 * @throws Exception
		 */
		public static function init_static_once() {
			if ( false === static::$static_initiated ) {
				static::$static_initiated = true;
				static::init_user_id();
				static::init_session_id();
				static::init_ip();
				$restrictions = apply_filters( 'boombox_vote_restrictions', array() );
				foreach ( $restrictions as $restriction ) {
					if ( ! is_a( $restriction, 'Boombox_Vote_Restriction' ) ) {
						throw new Exception( "Push variables of type Boombox_Vote_Restriction while using 'boombox_vote_restrictions' filter." );
					}
					static::$restrictions[] = $restriction;
				}
				add_filter( 'boombox_vote_restriction_where', array(
					'Boombox_Vote_Restriction',
					'filter_where'
				), 10, 3 );

				add_filter( 'boombox_vote_restriction_where_user', array(
					'Boombox_Vote_Restriction',
					'filter_where_user'
				), 10, 3 );
				add_filter( 'boombox_vote_restriction_where_user_daily', array(
					'Boombox_Vote_Restriction',
					'filter_where_daily'
				), 10, 3 );

				add_filter( 'boombox_vote_restriction_where_session', array(
					'Boombox_Vote_Restriction',
					'filter_where_session'
				), 10, 3 );

				add_filter( 'boombox_vote_restriction_where_ip', array(
					'Boombox_Vote_Restriction',
					'filter_where_ip'
				), 10, 3 );
				add_filter( 'boombox_vote_restriction_where_ip_daily', array(
					'Boombox_Vote_Restriction',
					'filter_where_daily'
				), 10, 3 );
			}
		}

		/**
		 * @var int
		 */
		protected static $user_id;

		/**
		 * Initiates current user id
		 */
		protected static function init_user_id() {
			static::$user_id = get_current_user_id();
		}

		/**
		 * @return int
		 */
		public static function get_user_id() {
			return static::$user_id;
		}

		/**
		 * @var string
		 */
		protected static $session_id;

		/**
		 * Initiates session id
		 */
		protected static function init_session_id() {
			if ( session_id() == '' || session_status() == PHP_SESSION_NONE ) {
				session_start();
			}
			static::$session_id = session_id();
			session_write_close();
		}

		/**
		 * @return string
		 */
		public static function get_session_id() {
			return static::$session_id;
		}

		/**
		 * @var string
		 */
		protected static $ip;

		/**
		 * Initiates ip address
		 */
		protected static function init_ip() {
			$pattern = '#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s';

			if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && preg_match( $pattern, $_SERVER['HTTP_X_FORWARDED_FOR'], $matches ) ) {
				$ip = $matches[0];
			} elseif ( isset( $_SERVER['HTTP_CLIENT_IP'] ) && preg_match( $pattern, $_SERVER['HTTP_CLIENT_IP'], $matches ) ) {
				$ip = $matches[0];
			} elseif ( isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) && preg_match( $pattern, $_SERVER['HTTP_CF_CONNECTING_IP'], $matches ) ) {
				$ip = $matches[0];
			} elseif ( isset( $_SERVER['HTTP_X_REAL_IP'] ) && preg_match( $pattern, $_SERVER['HTTP_X_REAL_IP'], $matches ) ) {
				$ip = $matches[0];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			static::$ip = apply_filters( 'boombox/vote-restriction/ip_address', $ip );
		}

		/**
		 * @return string
		 */
		public static function get_ip() {
			return static::$ip;
		}

		/**
		 * @var array holds Boombox_Vote_Restriction  objects
		 */
		protected static $restrictions = array();

		/**
		 * @return array of Boombox_Vote_Restriction objects
		 */
		public static function get_restrictions() {
			return static::$restrictions;
		}

		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public static function restriction_exists( $name ) {
			foreach ( static::$restrictions as $restriction ) {
				if ( 'Boombox_Vote_Restriction' === get_class( $restriction ) && $name === static::cast( $restriction )->get_name() ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * @param string $name
		 *
		 * @return Boombox_Vote_Restriction
		 * @throws UnexpectedValueException
		 */
		public static function get_restriction_by_name( $name ) {
			foreach ( static::$restrictions as $restriction ) {
				if ( $name === static::cast( $restriction )->get_name() ) {
					return $restriction;
				}
			}
			throw new UnexpectedValueException( 'Invalid vote restriction name. Value $name: ' . $name );
		}

		/**
		 * @param string                   $where
		 * @param Boombox_Vote_Restriction $restriction
		 * @param array                    $values
		 *
		 * @return string
		 */
		public static function filter_where( $where, Boombox_Vote_Restriction $restriction, array $values ) {
			global $wpdb;
			$key_column_names = $restriction->get_db_settings()->get_key_column_names();
			foreach ( $key_column_names as $key_column_name ) {
				$where .= $wpdb->prepare( " AND `" . $key_column_name . "` = %s ", $values[ $key_column_name ] );
			}

			return $where;
		}

		/**
		 * @param string                   $where
		 * @param Boombox_Vote_Restriction $restriction
		 *
		 * @return string
		 */
		public static function filter_where_daily( $where, Boombox_Vote_Restriction $restriction ) {
			$where .= " AND `" . $restriction->get_db_settings()->get_date_column_name() . "` > DATE_ADD( CURDATE( ), INTERVAL -1 DAY ) ";

			return $where;
		}

		/**
		 * @param string                   $where
		 * @param Boombox_Vote_Restriction $restriction
		 *
		 * @return string
		 */
		public static function filter_where_user( $where, Boombox_Vote_Restriction $restriction ) {
			global $wpdb;
			$where .= $wpdb->prepare( " AND `" . $restriction->get_db_settings()->get_user_id_column_name() . "` = %d ", static::get_user_id() );

			return $where;
		}

		/**
		 * @param string                   $where
		 * @param Boombox_Vote_Restriction $restriction
		 *
		 * @return string
		 */
		public static function filter_where_session( $where, Boombox_Vote_Restriction $restriction ) {
			global $wpdb;
			$where .= $wpdb->prepare( " AND `" . $restriction->get_db_settings()->get_session_column_name() . "` = %s ", static::get_session_id() );

			return $where;
		}

		/**
		 * @param string                   $where
		 * @param Boombox_Vote_Restriction $restriction
		 *
		 * @return string
		 */
		public static function filter_where_ip( $where, Boombox_Vote_Restriction $restriction ) {
			global $wpdb;
			$where .= $wpdb->prepare( " AND `" . $restriction->get_db_settings()->get_ip_column_name() . "` = %s ", static::get_ip() );

			return $where;
		}

		/**
		 * @param array        $keys
		 * @param string|array $values
		 *
		 * @return array|bool
		 */
		public static function prepare_values( $keys, $values ) {
			if ( 1 === count( $keys ) && ! is_array( $values ) ) {
				return array( $keys[0] => $values );
			}
			foreach ( $keys as $key ) {
				if ( ! isset( $values[ $key ] ) ) {
					return false;
				}
			}

			return $values;
		}

		/**
		 * @param string       $restriction_name
		 * @param string|array $values
		 *
		 * @return bool
		 */
		public static function check( $restriction_name, $values ) {
			global $wpdb;
			$status = true;
			while ( true ) {
				$restriction = static::get_restriction_by_name( $restriction_name );
				$settings    = $restriction->get_settings();
				$db_settings = $restriction->get_db_settings();
				$values      = static::prepare_values( $db_settings->get_key_column_names(), $values );
				if ( ! $values ) {
					$status = "Invalid values.";
					break;
				}

				$where = apply_filters( 'boombox_vote_restriction_where', ' 1=1 ', $restriction, $values );
				$where = apply_filters( 'boombox_vote_restriction_where_' . $restriction->get_name(), $where, $restriction, $values );

				if ( $db_settings->has_count_column() ) {
					$aggregation = " SUM(`" . $db_settings->get_count_column_name() . "`) ";
				} else {
					$aggregation = ' COUNT(*) ';
				}

				if ( $settings->need_to_check_user_total() || $settings->need_to_check_user_daily() ) {
					if ( ! static::get_user_id() ) {
						$status = "User not signed in.";
						break;
					}
					$where_user = apply_filters( 'boombox_vote_restriction_where_user', $where, $restriction, $values );
					$where_user = apply_filters( 'boombox_vote_restriction_where_user_' . $restriction->get_name(), $where_user, $restriction, $values );
					if ( $settings->need_to_check_user_total() ) {
						$where_user_total = apply_filters( 'boombox_vote_restriction_where_user_total', $where_user, $restriction, $values );
						$where_user_total = apply_filters( 'boombox_vote_restriction_where_user_total_' . $restriction->get_name(), $where_user_total, $restriction, $values );

						$query = "
                            SELECT " . $aggregation . "
                                FROM `" . $db_settings->get_table_name() . "`
                                WHERE " . $where_user_total . "
						         GROUP BY `" . $db_settings->get_table_name() . "`.`" . $db_settings->get_user_id_column_name() . "`";
						$stat  = $wpdb->get_var( $query );
						$stat  = intval( $stat );
						if ( $stat >= $settings->get_user_total() ) {
							$status = "Total limit for user exceeded.";
							break;
						}
					}
					if ( $settings->need_to_check_user_daily() ) {
						$where_user_daily = apply_filters( 'boombox_vote_restriction_where_user_daily', $where_user, $restriction, $values );
						$where_user_daily = apply_filters( 'boombox_vote_restriction_where_user_daily_' . $restriction->get_name(), $where_user_daily, $restriction, $values );

						$query = "
                            SELECT " . $aggregation . "
                                FROM `" . $db_settings->get_table_name() . "`
                                WHERE " . $where_user_daily . "
                                GROUP BY `" . $db_settings->get_table_name() . "`.`" . $db_settings->get_user_id_column_name() . "`";

						$stat = $wpdb->get_var( $query );
						$stat = intval( $stat );
						if ( $stat >= $settings->get_user_daily() ) {
							$status = "Daily limit for user exceeded.";
							break;
						}
					}
				}
				if ( $settings->need_to_check_ip_total() || $settings->need_to_check_ip_daily() ) {
					$where_ip = apply_filters( 'boombox_vote_restriction_where_ip', $where, $restriction, $values );
					$where_ip = apply_filters( 'boombox_vote_restriction_where_ip_' . $restriction->get_name(), $where_ip, $restriction, $values );
					if ( $settings->need_to_check_ip_total() ) {
						$where_ip_total = apply_filters( 'boombox_vote_restriction_where_ip_total', $where_ip, $restriction, $values );
						$where_ip_total = apply_filters( 'boombox_vote_restriction_where_ip_total_' . $restriction->get_name(), $where_ip_total, $restriction, $values );

						$query = "
                            SELECT " . $aggregation . "
                                FROM `" . $db_settings->get_table_name() . "`
                                WHERE " . $where_ip_total . "
						         GROUP BY `" . $db_settings->get_table_name() . "`.`" . $db_settings->get_ip_column_name() . "`";

						$stat = $wpdb->get_var( $query );
						$stat = intval( $stat );
						if ( $stat >= $settings->get_ip_total() ) {
							$status = "Total limit for ip exceeded.";
							break;
						}
					}
					if ( $settings->need_to_check_ip_daily() ) {
						$where_ip_daily = apply_filters( 'boombox_vote_restriction_where_ip_daily', $where_ip, $restriction, $values );
						$where_ip_daily = apply_filters( 'boombox_vote_restriction_where_ip_daily_' . $restriction->get_name(), $where_ip_daily, $restriction, $values );

						$query = "
                            SELECT " . $aggregation . "
                                FROM `" . $db_settings->get_table_name() . "`
                                WHERE " . $where_ip_daily . "
						         GROUP BY `" . $db_settings->get_table_name() . "`.`" . $db_settings->get_ip_column_name() . "`";

						$stat = $wpdb->get_var( $query );
						$stat = intval( $stat );
						if ( $stat >= $settings->get_ip_daily() ) {
							$status = "Daily limit for ip exceeded.";
							break;
						}
					}
				}
				if ( $settings->need_to_check_session_total() ) {
					$where_session = apply_filters( 'boombox_vote_restriction_where_session', $where, $restriction, $values );
					$where_session = apply_filters( 'boombox_vote_restriction_where_session_' . $restriction->get_name(), $where_session, $restriction, $values );
					$query         = "
                        SELECT " . $aggregation . "
                            FROM `" . $db_settings->get_table_name() . "`
                            WHERE " . $where_session . "
					                 GROUP BY `" . $db_settings->get_table_name() . "`.`" . $db_settings->get_session_column_name() . "`";

					$stat = $wpdb->get_var( $query );
					$stat = intval( $stat );
					if ( $stat >= $settings->get_session_total() ) {
						$status = "Daily limit for session exceeded.";
						break;
					}
				}
				break;
			}

			return $status === true ? true : false;
		}

		/**
		 * @param string       $restriction_name
		 * @param string|array $values
		 *
		 * @return bool
		 */
		public static function discard( $restriction_name, $values ) {
			global $wpdb;
			$status = false;

			while ( true ) {
				$restriction = static::get_restriction_by_name( $restriction_name );
				$settings    = $restriction->get_settings();
				$db_settings = $restriction->get_db_settings();
				$values      = static::prepare_values( $db_settings->get_key_column_names(), $values );
				if ( ! $values ) {
					$status = "Invalid values.";
					break;
				}

				$where = apply_filters( 'boombox_vote_restriction_where', ' 1=1 ', $restriction, $values );
				$where = apply_filters( 'boombox_vote_restriction_where_' . $restriction->get_name(), $where, $restriction, $values );

				if ( $settings->need_to_check_user_total() || $settings->need_to_check_user_daily() ) {
					if ( ! static::get_user_id() ) {
						$status = "User not signed in.";
						break;
					}
					$where_user = apply_filters( 'boombox_vote_restriction_where_user', $where, $restriction, $values );
					$where_user = apply_filters( 'boombox_vote_restriction_where_user_' . $restriction->get_name(), $where_user, $restriction, $values );
					$query      = "
						DELETE FROM `" . $db_settings->get_table_name() . "`
                            WHERE " . $where_user;

					$status = ( bool ) $wpdb->query( $query );
				} elseif ( $settings->need_to_check_session_total() ) {
					$where_session = apply_filters( 'boombox_vote_restriction_where_session', $where, $restriction, $values );
					$where_session = apply_filters( 'boombox_vote_restriction_where_session_' . $restriction->get_name(), $where_session, $restriction, $values );
					$query         = "
                        DELETE FROM `" . $db_settings->get_table_name() . "`
                            WHERE " . $where_session;

					$status = ( bool ) $wpdb->query( $query );
				} elseif ( $settings->need_to_check_ip_total() || $settings->need_to_check_ip_daily() ) {
					$where_ip = apply_filters( 'boombox_vote_restriction_where_ip', $where, $restriction, $values );
					$where_ip = apply_filters( 'boombox_vote_restriction_where_ip_' . $restriction->get_name(), $where_ip, $restriction, $values );
					$query    = "
                        DELETE FROM `" . $db_settings->get_table_name() . "`
                            WHERE " . $where_ip;
					$status   = ( bool ) $wpdb->query( $query );
				}
				break;
			}

			// echo $status;
			return ( $status === false || is_string( $status ) ) ? false : true;
		}
	}
}
add_action( 'init', array( 'Boombox_Vote_Restriction', 'init_static_once' ) );