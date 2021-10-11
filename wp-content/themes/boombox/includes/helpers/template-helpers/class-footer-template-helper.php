<?php
/**
 * Boombox Footer Template Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.1.3
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Footer_Template_Helper' ) ) {

	final class Boombox_Footer_Template_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Footer_Template_Helper|null
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
		 * Boombox_Footer_Template_Helper constructor.
		 */
		private function __construct() {
		}

		/**
		 * A dummy magic method to prevent Boombox_Footer_Template_Helper from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Get template options
		 * @return array
		 */
		public function get_options() {
			$key = 'footer_settings';

			$footer_settings = boombox_cache_get( $key );

			if ( ! $footer_settings ) {
				if ( boombox_is_fragment_cache_enabled() ) {
					?>
					<!-- mfunc <?php echo W3TC_DYNAMIC_SECURITY; ?>
						$featured_strip = wp_is_mobile() ? ( boombox_get_theme_option(
						'mobile_global_enable_footer_strip' ) && boombox_get_theme_option( 'footer_strip_enable'
						 ) ) : boombox_get_theme_option( 'footer_strip_enable' );
					-->
					<?php $featured_strip = wp_is_mobile() ? ( boombox_get_theme_option( 'mobile_global_enable_footer_strip' ) && boombox_get_theme_option( 'footer_strip_enable' ) ) : boombox_get_theme_option( 'footer_strip_enable' ); ?>
					<!-- /mfunc <?php echo W3TC_DYNAMIC_SECURITY; ?> -->
					<?php
				} else if ( boombox_is_page_cache_enabled() ) {
					$featured_strip = boombox_get_theme_option( 'footer_strip_enable' );
				} else {
					$featured_strip = wp_is_mobile() ? ( boombox_get_theme_option( 'mobile_global_enable_footer_strip' ) && boombox_get_theme_option( 'footer_strip_enable' ) ) : boombox_get_theme_option( 'footer_strip_enable' );
				}

				$pattern_position = boombox_get_theme_option( 'footer_design_pattern_position' );

				$footer_settings = array(
					'classes'          => ( $pattern_position == 'none' ) ? '' : sprintf( '%s-bg', $pattern_position ),
					'footer_top'       => boombox_get_theme_option( 'footer_general_footer_top' ),
					'footer_bottom'    => boombox_get_theme_option( 'footer_general_footer_bottom' ),
					'pattern_position' => $pattern_position,
					'featured_strip'   => $featured_strip,
					'social_icons'     => boombox_get_theme_option( 'footer_general_social_icons' ),
					'footer_text'      => wp_kses_post( esc_html__( boombox_get_theme_option( 'footer_general_text' ), 'boombox' ) ),
				);

				boombox_cache_set( $key, $footer_settings );
			}

			return apply_filters( 'boombox/footer_template_settings', $footer_settings );
		}

	}

}