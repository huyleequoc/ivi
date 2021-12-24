<?php
/**
 * Boombox "Woocommerce" Shop Page Template Helper
 *
 * @package BoomBox_Theme
 * @since 2.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( !class_exists( 'Boombox_Woocommerce_Template_Helper' ) ) {

	final class Boombox_Woocommerce_Template_Helper {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Woocommerce_Template_Helper|null
		 */
		public static function get_instance() {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_Woocommerce_Template_Helper constructor.
		 */
		private function __construct() {
		}

		/**
		 * A dummy magic method to prevent Boombox_Woocommerce_Template_Helper from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Get options
		 *
		 * @return array
		 */
		public function get_options() {

			if ( is_product() ) {
				$sidebar_type = Boombox_Woocommerce::get_instance()->get_single_product_sidebar_type();
				$enable_primary_sidebar = boombox_is_primary_sidebar_enabled( $sidebar_type );
				$enable_secondary_sidebar = boombox_is_secondary_sidebar_enabled( $sidebar_type );
			} else if ( is_shop() || is_product_taxonomy() ) {
				$page_id = wc_get_page_id( 'shop' );

				$sidebar_type = boombox_get_post_meta( $page_id, 'boombox_sidebar_type' );
				if( ! $sidebar_type ) {
					$sidebar_type = '1-sidebar-1_3';
				}
				$enable_primary_sidebar = boombox_is_primary_sidebar_enabled( $sidebar_type );
				$enable_secondary_sidebar = boombox_is_secondary_sidebar_enabled( $sidebar_type );
			}

			$template_settings = array(
				'enable_primary_sidebar'   => $enable_primary_sidebar,
				'enable_secondary_sidebar' => $enable_secondary_sidebar,
			);

			return apply_filters( 'boombox/woocommence_shop_template_settings', $template_settings );
		}

	}

}