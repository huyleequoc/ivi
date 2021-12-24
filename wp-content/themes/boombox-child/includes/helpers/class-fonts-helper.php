<?php
/**
 * Boombox Choices Helper
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Fonts_Helper' ) ) {

	/**
	 * Class Boombox_Fonts_Helper
	 */
	final class Boombox_Fonts_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Fonts_Helper|null
		 */
		public static function get_instance() {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Holds google fonts
		 * @var null
		 */
		private $_google_fonts = null;

		/**
		 * Boombox_Fonts_Helper constructor.
		 */
		private function __construct() {
			$this->_google_fonts = array_keys( Kirki_Fonts::get_google_fonts() );
		}

		/**
		 * Check whether it's a google font
		 * @param string $family The font family
		 *
		 * @return bool
		 */
		private function is_google_font( $family ) {
			return in_array( $family, $this->_google_fonts );
		}

		/**
		 * A dummy magic method to prevent Boombox_Fonts_Helper from being cloned.
		 * @throws Exception
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Generate font family for inline CSS
		 * @param string $family The font family
		 * @param string $suffix The suffix: serif, sans-serif, monospace
		 *
		 * @return string
		 */
		public function generate_inline_css_font_family( $family, $suffix = '' ) {
			$css = '';
			if( $family ) {
				if( $this->is_google_font( $family ) ) {
					$css = sprintf( 'font-family: %s', htmlspecialchars_decode( $family ) );
					if( in_array( $suffix, array( 'serif', 'sans-serif', 'monospace' ) ) ) {
						$css .= sprintf( ',%s', $suffix );
					}
					$css .= ';';
				} else {
					$css = sprintf( 'font-family: %s;', htmlspecialchars_decode( $family ) );
				}
			}

			return $css;
		}

		/**
		 * Generate google webfont request URL
		 * @return string
		 */
		public function get_google_url() {

			$fonts = array();
			$subsets = array();
			$set = boombox_get_theme_options_set( array(
				'design_logo_font_family',
				'design_primary_font_family',
				'design_secondary_font_family',
				'design_post_titles_font_family'
			) );

			/***** Logo Font Family */
			if (
				isset( $set['design_logo_font_family']['font-family'] )
				&& $set['design_logo_font_family']['font-family']
				&& $this->is_google_font( $set['design_logo_font_family']['font-family'] )
			) {
				$tmp_fonts = array(
					$set['design_logo_font_family']['font-family']
				);

				if( isset( $set['design_logo_font_family']['variant'] ) && $set['design_logo_font_family']['variant'] ) {
					$tmp_fonts[] = '300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i';
				}
				$fonts[] = implode( ':', $tmp_fonts );

				if( isset( $set['design_logo_font_family']['subsets'] ) && ! empty( $set['design_logo_font_family']['subsets'] ) ) {
					$subsets = array_merge( $subsets, (array)$set['design_logo_font_family']['subsets'] );
				}
			}

			/***** Primary Font Family */
			if(
				isset( $set['design_primary_font_family']['font-family'] )
				&& $set['design_primary_font_family']['font-family']
				&& $this->is_google_font( $set['design_primary_font_family']['font-family'] )
			) {
				$tmp_fonts = array(
					$set['design_primary_font_family']['font-family']
				);

				if( isset( $set['design_primary_font_family']['variant'] ) && $set['design_primary_font_family']['variant'] ) {
					$tmp_fonts[] = '300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i';
				}
				$fonts[] = implode( ':', $tmp_fonts );

				if( isset( $set['design_primary_font_family']['subsets'] ) && ! empty( $set['design_primary_font_family']['subsets'] ) ) {
					$subsets = array_merge( $subsets, (array)$set['design_primary_font_family']['subsets'] );
				}
			}

			/***** Secondary Font Family */
			if (
				isset( $set['design_secondary_font_family']['font-family'] )
				&& $set['design_secondary_font_family']['font-family']
				&& $this->is_google_font( $set['design_secondary_font_family']['font-family'] )
			) {
				$tmp_fonts = array(
					$set['design_secondary_font_family']['font-family']
				);

				if( isset( $set['design_secondary_font_family']['variant'] ) && $set['design_secondary_font_family']['variant'] ) {
					$tmp_fonts[] = '300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i';
				}
				$fonts[] = implode( ':', $tmp_fonts );

				if( isset( $set['design_secondary_font_family']['subsets'] ) && ! empty( $set['design_secondary_font_family']['subsets'] ) ) {
					$subsets = array_merge( $subsets, (array)$set['design_secondary_font_family']['subsets'] );
				}
			}

			/***** Post Titles Font Family */
			if (
				isset( $set['design_post_titles_font_family']['font-family'] )
				&& $set['design_post_titles_font_family']['font-family']
				&& $this->is_google_font( $set['design_post_titles_font_family']['font-family'] )
			) {
				$tmp_fonts = array(
					$set['design_post_titles_font_family']['font-family']
				);

				if( isset( $set['design_post_titles_font_family']['variant'] ) && $set['design_post_titles_font_family']['variant'] ) {
					$tmp_fonts[] = '300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i';
				}
				$fonts[] = implode( ':', $tmp_fonts );

				if( isset( $set['design_post_titles_font_family']['subsets'] ) && ! empty( $set['design_post_titles_font_family']['subsets'] ) ) {
					$subsets = array_merge( $subsets, (array)$set['design_post_titles_font_family']['subsets'] );
				}
			}

			$fonts_url = '';
			if ( ! empty( $fonts ) ) {

				if ( ! empty( $subsets ) ) {
					$subsets = array( 'latin', 'latin-ext' );
				}

				$fonts = implode( '|', array_unique( $fonts ) );
				$subsets = implode( ',', array_filter( array_unique( $subsets ) ) );

				$fonts_url = add_query_arg( array(
					'family' => urlencode( $fonts ),
					'subset' => urlencode( $subsets ),
				), 'https://fonts.googleapis.com/css' );

			}

			return $fonts_url;
		}

	}

}