<?php
/**
 * Boombox Template Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Template' ) ) {

	final class Boombox_Template {

		/**
		 * Holds template data
		 * @var array
		 */
		private static $data = array();

		/**
		 * Set template data
		 * @param string $key Data key
		 * @param mixed $value Data value
		 */
		public static function set( $key, $value ) {
			static::$data[ $key ] = $value;
		}

		/**
		 * Get template data
		 * @param string $key Data Key
		 * @param string $default Value to return if data key does not exists
		 *
		 * @return mixed|null
		 */
		public static function get( $key, $default = null ) {
			return isset( static::$data[ $key ] ) ? static::$data[ $key ] : $default;
		}

		/**
		 * Reset template data
		 * @param string $key Data key
		 */
		public static function reset( $key ) {
			if( isset( static::$data[ $key ] ) ) {
				unset( static::$data[ $key ] );
			}
		}

		/**
		 * Get & clean data
		 * @param string $key Data Key
		 * @param null $default Value to return if data key does not exists
		 *
		 * @return mixed
		 */
		public static function get_clean( $key, $default = null ) {
			$value = static::get( $key, $default );
			static::reset( $key );

			return $value;
		}

		/**
		 * @var array Map to template helpers
		 */
		private static $map = array(
			'header'          => array(
				'class' => 'Boombox_Header_Template_Helper',
				'path'  => 'template-helpers/class-header-template-helper.php',
			),
			'archive'         => array(
				'class' => 'Boombox_Archive_Template_Helper',
				'path'  => 'template-helpers/class-archive-template-helper.php',
			),
			'author'          => array(
				'class' => 'Boombox_Author_Template_Helper',
				'path'  => 'template-helpers/class-author-template-helper.php',
			),
			'featured-area'   => array(
				'class' => 'Boombox_Featured_Area_Template_Helper',
				'path'  => 'template-helpers/class-featured-area-template-helper.php',
			),
			'featured-strip'  => array(
				'class' => 'Boombox_Featured_Strip_Template_Helper',
				'path'  => 'template-helpers/class-featured-strip-template-helper.php',
			),
			'footer'          => array(
				'class' => 'Boombox_Footer_Template_Helper',
				'path'  => 'template-helpers/class-footer-template-helper.php',
			),
			'collection-item' => array(
				'class' => 'Boombox_Collection_Item_Template_Helper',
				'path'  => 'template-helpers/class-collection-item-template-helper.php',
			),
			'index'           => array(
				'class' => 'Boombox_Index_Template_Helper',
				'path'  => 'template-helpers/class-index-template-helper.php',
			),
			'page'            => array(
				'class' => 'Boombox_Page_Template_Helper',
				'path'  => 'template-helpers/class-page-template-helper.php',
			),
			'search'          => array(
				'class' => 'Boombox_Search_Template_Helper',
				'path'  => 'template-helpers/class-search-template-helper.php',
			),
			'post'            => array(
				'class' => 'Boombox_Single_Post_Template_Helper',
				'path'  => 'template-helpers/class-single-post-template-helper.php',
			),
			'title'           => array(
				'class' => 'Boombox_Title_Template_Helper',
				'path'  => 'template-helpers/class-title-template-helper.php',
			),
			'authentication'  => array(
				'class' => 'Boombox_Authentication_Template_Helper',
				'path'  => 'template-helpers/class-authentication-template-helper.php',
			),
			'featured-labels' => array(
				'class' => 'Boombox_Featured_Labels_Template_Helper',
				'path'  => 'template-helpers/class-featured-labels-template-helper.php',
			),
			'breadcrumb' => array(
				'class' => 'Boombox_Breadcrumb_Template_Helper',
				'path'  => 'template-helpers/class-breadcrumb-template-helper.php',
			)
		);

		/**
		 * Get template helpers map
		 * @return array
		 */
		private static function get_map() {
			return apply_filters( 'boombox/template_helpers_map', static::$map );
		}

		/**
		 * Init template helper
		 *
		 * @param $key
		 *
		 * @return mixed
		 * @throws Exception
		 */
		public static function init( $key ) {
			$map = self::get_map();

			if ( ! isset( $map[ $key ] ) ) {
				throw new Exception( "Invalid template map key: {$key}" );
			}

			$class = isset( $map[ $key ][ 'class' ] ) ? $map[ $key ][ 'class' ] : null;
			$path = isset( $map[ $key ][ 'path' ] ) ? strtr( $map[ $key ][ 'path' ], array( '/' => DIRECTORY_SEPARATOR ) ) : null;

			if ( ! $class ) {
				throw new Exception( "Please specify template class name. Current: {$class}" );
			}

			if ( ! class_exists( $class ) ) {
				require_once( $path );
			}

			return $class::get_instance();
		}

	}

}