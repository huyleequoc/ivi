<?php
/**
 * Gamify plugin functions
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

if( ! boombox_plugin_management_service()->is_plugin_active( 'gamify/gamify.php' ) ) {
	return;
}

if ( ! class_exists( 'Boombox_Gamify' ) ) {

	final class Boombox_Gamify {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return Boombox_Gamify|null
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_Gamify constructor.
		 */
		private function __construct() {
			$this->hooks();

			do_action( 'boombox/gamify/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_Gamify from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup Hooks
		 */
		public function hooks() {
			add_filter( 'boombox/author/name_row', array( $this, 'add_ranks_to_author_extended_profile' ), 10, 2 );
			add_filter( 'gfy/fb_app_id', array( $this, 'edit_fb_application_id' ) );
			add_filter( 'gfy/widget/featured_author/total_data', array( $this, 'edit_featured_author_widget_total_data' ), 10, 3 );
			add_filter( 'gfy/widget/featured_author/avatar_size', array( $this, 'edit_featured_author_widget_avatar_size' ) );
		}

		/**
		 * Render user rank in single post author extended profile
		 * @param string $html Current HRML
		 * @param int $user_id User ID
		 *
		 * @return string
		 */
		public function add_ranks_to_author_extended_profile( $html, $user_id ) {
			$ranks = mycred_get_module( 'ranks' );
			if( $ranks ) {
				$ranks_html = '';
				$mycred_types = mycred_get_usable_types( $user_id );
				foreach ( $mycred_types as $type_id ) {
					$users_rank = mycred_get_users_rank_id( $user_id, $type_id );
					if ( $users_rank === false ) {
						continue;
					}

					$rank_title = get_the_title( $users_rank );
					$rank_logo = mycred_get_rank_logo( $users_rank, 'full', array(
						'title' => $rank_title,
						'alt' => $rank_title,
						'class' => 'mycred-badge-image'
					) );
					if( $rank_logo ) {
						$ranks_html .= sprintf( '<span class="gfy-badge">%s</span>', $rank_logo );
					}
				}

				if( $ranks_html ) {
					$html .= '<span class="gfy-bp-component gfy-badge-list">' . $ranks_html . '</span>';
				}
			}

			return $html;
		}

		/**
		 * Edit facebook application ID
		 * @param string|int $app_id Current application ID
		 *
		 * @return string|int
		 */
		public function edit_fb_application_id( $app_id ) {
			$theme_app_id = boombox_get_theme_option( 'extra_authentication_facebook_app_id' );
			if( $theme_app_id ) {
				$app_id = $theme_app_id;
			}

			return $app_id;
		}

		/**
		 * Edit "featured author" widget total data
		 * @param array $data Current data
		 * @param int $user_id Selected user ID
		 *
		 * @return array
		 */
		public function edit_featured_author_widget_total_data( $data, $user_id, $instance ) {

			$disable_total_points = isset( $instance[ 'disable_total_reads' ] ) ? !!$instance[ 'disable_total_reads' ] : false;
			if( ! $disable_total_points ) {
				$data[ 'total_views' ] = array(
					'label'    => __( 'Total Reads', 'boombox' ),
					'value'    => get_user_meta( $user_id, 'total_posts_view_count', true ),
					'priority' => 40
				);
			}

			return $data;
		}

		/**
		 * Edit "featured author" widget avatar size
		 *
		 * @return int
		 */
		public function edit_featured_author_widget_avatar_size() {
			return 130;
		}

	}

	Boombox_Gamify::get_instance();

}