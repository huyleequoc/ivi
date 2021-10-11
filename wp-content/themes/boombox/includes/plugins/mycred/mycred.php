<?php
/**
 * MyCRED plugin functions
 *
 * @package BoomBox_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! boombox_plugin_management_service()->is_plugin_active( 'mycred/mycred.php' ) ) {
	return;
}

if ( ! class_exists( 'Boombox_myCRED' ) ) {

	final class Boombox_myCRED {

		/**
		 * Holds current instance
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Hold path to current file
		 * @var
		 */
		private $dir;

		/**
		 * Holds HTML templates
		 * @var
		 */
		private $html_templates;

		/**
		 * Holds active addons
		 * @var
		 */
		private $modules;

		/**
		 * Singleton.
		 */
		static function get_instance() {
			if ( static::$instance == null ) {
				static::$instance = new self();
			}

			return static::$instance;
		}

		/**
		 * Constructor
		 */
		private function __construct() {
			$this->setup_actions();

			do_action( 'boombox/mycred/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_myCRED from being cloned.
		 */
		private function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'boombox' ), '2.0.0' );
		}

		/**
		 * Setup actions
		 */
		private function setup_actions() {
			add_filter( 'boombox/buddypress/member_header_additional_data', array( $this, 'bp_header_meta_render_points_count' ), 10, 1 );
		}

		/**
		 * Render visible points total in buddypress header meta
		 * @param $content
		 *
		 * @return string
		 */
		public function bp_header_meta_render_points_count( $content ) {

			$user_id = bp_displayed_user_id();

			$types = apply_filters( 'boombox/mycred/bp_member_header_display_point_types', mycred_get_types() );
			$treshold = apply_filters( 'boombox/buddypress/member_header_points_treshold', -1 );
			foreach( $types as $type => $label ) {

				$total_balance = mycred_get_users_balance( $user_id, $type );
				if( $total_balance > $treshold ) {
					$total_label = sprintf( __( 'Total %s', 'boombox' ), $label );

					$content .= sprintf( '<div class="row">
                        <span class="col total-label text-right">%s:</span>
                        <span class="col total-count text-left">%s</span>
                    </div>', $total_label, number_format( floatval( $total_balance ) ) );
				}

			}

			return $content;
		}

	}

}

/**
 * Get plugin integration instance
 * @return Boombox_myCRED|null
 */
function boombox_mycred() {
	return Boombox_myCRED::get_instance();
}

boombox_mycred();