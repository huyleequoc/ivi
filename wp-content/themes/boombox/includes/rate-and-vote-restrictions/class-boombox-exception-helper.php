<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Exception_Helper' ) ) {
	/**
	 * Class Boombox_Exception_Helper
	 */
	class Boombox_Exception_Helper
	{

		/**
		 * @param string $val
		 *
		 * @return bool
		 */
		public static function is_null_or_empty_string($val)
		{
			return (is_null($val) || '' === trim($val));
		}

		/**
		 * @param string $var_value
		 * @param string $var_name
		 *
		 * @return bool|string
		 */
		public static function check_null_or_empty_string($var_value, $var_name)
		{
			if (static::is_null_or_empty_string($var_value)) {
				return "Argument '{$var_name}' can not be null or empty. Value: '{$var_value}'";
			}

			return true;
		}

		/**
		 * @param string $val
		 *
		 * @return bool
		 */
		public static function is_empty_string($val)
		{
			return (!is_null($val) && '' === trim($val));
		}

		/**
		 * @param string $var_value
		 * @param string $var_name
		 *
		 * @return bool|string
		 */
		public static function check_empty_string($var_value, $var_name)
		{
			if (static::is_empty_string($var_value)) {
				return "Argument '{$var_name}' can be null but not empty. Value: '{$var_value}'";
			}

			return true;
		}

		/**
		 * @param string $var_value
		 * @param string $var_name
		 * @param int $max_length
		 *
		 * @return bool|string
		 */
		public static function check_string_max_length($var_value, $var_name, $max_length)
		{
			if ($max_length < strlen($var_value)) {
				return "Argument '{$var_name}' length must be shorter than {$max_length}. Value: '{$var_value}'";
			}

			return true;
		}

		/**
		 * @param int $val
		 *
		 * @return bool
		 */
		public static function is_positive_number($val)
		{
			return (is_int($val) && 0 < $val);
		}

		/**
		 * @param int $val
		 *
		 * @return bool
		 */
		public static function is_positive_or_zero($val)
		{
			return (is_int($val) && 0 <= $val);
		}

		/**
		 * @param int $var_value
		 * @param string $var_name
		 *
		 * @return bool|string
		 */
		public static function check_positive_number($var_value, $var_name)
		{
			if (!static::is_positive_number($var_value)) {
				return "Argument '{$var_name}' must me positive integer. Value: '{$var_value}'";
			}

			return true;
		}

		/**
		 * @param int $var_value
		 * @param string $var_name
		 *
		 * @return bool|string
		 */
		public static function check_positive_or_zero_number($var_value, $var_name)
		{
			if (!static::is_positive_or_zero($var_value)) {
				return "Argument '{$var_name}' must me positive integer or zero. Value: '{$var_value}'";
			}

			return true;
		}

		/**
		 * @param int $var_value
		 * @param string $var_name
		 *
		 * @return bool|string
		 */
		public static function check_positive_or_minus_one_number($var_value, $var_name)
		{
			if (!static::is_positive_number($var_value) && -1 !== $var_value) {
				return "Argument '{$var_name}' must me positive integer or -1. Value: '{$var_value}'";
			}

			return true;
		}
	}
}