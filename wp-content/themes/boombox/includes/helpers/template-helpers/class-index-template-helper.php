<?php
/**
 * Boombox Index Template Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Index_Template_Helper' ) ) {

	final class Boombox_Index_Template_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Index_Template_Helper|null
		 */
		public static function get_instance() {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Holds additional data
		 * @var array
		 */
		private $data = array();

		/**
		 * Setter
		 *
		 * @param string $name  Variable key
		 * @param mixed  $value Variable value
		 */
		public function __set( $name, $value ) {
			$this->data[ $name ] = $value;
		}

		/**
		 * Getter
		 *
		 * @param string $name Variable key
		 *
		 * @return mixed Variable value if it exists or null otherwise
		 */
		public function __get( $name ) {
			if ( array_key_exists( $name, $this->data ) ) {
				return $this->data[ $name ];
			}

			return null;
		}

		/**
		 * Boombox_Index_Template_Helper constructor.
		 */
		private function __construct() {
		}

		/**
		 * A dummy magic method to prevent Boombox_Index_Template_Helper from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Get elements
		 * @return array
		 */
		public function get_options() {

			if ( boombox_is_fragment_cache_enabled() ) {
				?>
				<!-- mfunc <?php echo W3TC_DYNAMIC_SECURITY; ?>

					$featured_strip = in_array( 'home', boombox_get_theme_option( 'header_strip_visibility' ) );
					$featured_area = ( boombox_get_theme_option( 'home_featured_area_type' ) != 'disable' );

					if( wp_is_mobile() ) {
						$featured_strip = ( $featured_strip && boombox_get_theme_option( 'mobile_global_enable_strip') );
						$featured_area = ( $featured_area &&
											boombox_get_theme_option( 'mobile_global_enable_featured_area' ) );
					}
				-->
				<?php
				$featured_strip = in_array( 'home', boombox_get_theme_option( 'header_strip_visibility' ) );
				$featured_area = ( boombox_get_theme_option( 'home_featured_area_type' ) != 'disable' );

				if( wp_is_mobile() ) {
					$featured_strip = ( $featured_strip && boombox_get_theme_option( 'mobile_global_enable_strip') );
					$featured_area = ( $featured_area &&
						boombox_get_theme_option( 'mobile_global_enable_featured_area' ) );
				}
				?>
				<!-- /mfunc <?php echo W3TC_DYNAMIC_SECURITY; ?> -->
				<?php
			} else if ( boombox_is_page_cache_enabled() ) {
				$featured_strip = in_array( 'home', boombox_get_theme_option( 'header_strip_visibility' ) );
				$featured_area = ( boombox_get_theme_option( 'home_featured_area_type' ) != 'disable' );
			} else {
				$featured_strip = in_array( 'home', boombox_get_theme_option( 'header_strip_visibility' ) );
				$featured_area = ( boombox_get_theme_option( 'home_featured_area_type' ) != 'disable' );

				if( wp_is_mobile() ) {
					$featured_strip = ( $featured_strip && boombox_get_theme_option( 'mobile_global_enable_strip') );
					$featured_area = ( $featured_area &&
						boombox_get_theme_option( 'mobile_global_enable_featured_area' ) );
				}
			}

			$sidebar_type = boombox_get_theme_option( 'home_main_posts_sidebar_type' );
			$options = array(
				'featured_area'            => $featured_area,
				'featured_strip'           => $featured_strip,
				'listing_type'             => boombox_get_theme_option( 'home_main_posts_listing_type' ),
				'pagination_type'          => boombox_get_theme_option( 'home_main_posts_pagination_type' ),
				'filters'                  => boombox_get_theme_option( 'archive_header_filters' ),
				'enable_primary_sidebar'   => boombox_is_primary_sidebar_enabled( $sidebar_type ),
				'enable_secondary_sidebar' => boombox_is_secondary_sidebar_enabled( $sidebar_type ),
				'hidden_seo_title'         => true,
			);

			return apply_filters( 'boombox/index_template_settings', $options );
		}

	}

}