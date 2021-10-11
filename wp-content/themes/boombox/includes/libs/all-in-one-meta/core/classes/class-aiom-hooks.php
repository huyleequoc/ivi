<?php
/**
 * Library hook helper
 *
 * @package "All In One Meta" library
 * @since   1.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! class_exists( 'AIOM_Hooks' ) ) {

	/**
	 * Class AIOM_Hooks
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Hooks {

		/**
		 * Setup Hooks
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function attach() {
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
			add_action( 'save_post', array( __CLASS__, 'save_post' ), 1, 2 );

			add_action( 'created_term', array( __CLASS__, 'save_term' ), 1, 3 );
			add_action( 'edit_term', array( __CLASS__, 'save_term' ), 1, 3 );

			add_action( 'user_register', array( __CLASS__, 'save_user' ), 1, 1 );
			add_action( 'personal_options_update', array( __CLASS__, 'save_user' ), 1, 1 );
			add_action( 'edit_user_profile_update', array( __CLASS__, 'save_user' ), 1, 1 );
		}

		/**
		 * Register assets
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function register_assets() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_register_style(
				'aiom-style',
				AIOM_URL . 'core/assets/css/aiom-admin' . $suffix . '.css',
				array(),
				'1.0.0'
			);
			wp_register_script(
				'aiom-script',
				AIOM_URL . 'core/assets/js/aiom-admin' . $suffix . '.js',
				array( 'jquery', 'media-models' ),
				'1.0.0',
				true
			);
		}

		/**
		 * Check whether it's a post preview
		 * @return bool
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private static function is_wp_preview() {
			return ( isset( $_POST[ 'wp-preview' ] ) && ( 'dopreview' == $_POST[ 'wp-preview' ] ) );
		}

		/**
		 * Trigger prefixed 'aiom_save_post' action for internal usage on wordpress's core 'save_post' action
		 * @param int $post_id Post ID
		 * @param WP_Post $post Current post object
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function save_post( $post_id, $post ) {
			if(
				isset( $_POST[ 'has_aiom_data' ] )
				&& $_POST[ 'has_aiom_data' ]
				&& ! self::is_wp_preview()
				&& apply_filters( 'aiom_allow_save_post', true, $post )
			) {
				do_action( 'aiom_save_post', $post_id, $post );
			}
		}

		/**
		 * Trigger prefixed 'aiom_save_term' action for internal usage on
		 * wordpress's core 'edit_term' or 'created_term' action
		 *
		 * @param int    $term_id  Term ID.
		 * @param int    $tt_id    Term taxonomy ID.
		 * @param string $taxonomy Taxonomy slug.
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function save_term( $term_id, $tt_id, $taxonomy ) {
			if(
				isset( $_POST[ 'has_aiom_data' ] )
				&& $_POST[ 'has_aiom_data' ]
				&& apply_filters( 'aiom_allow_save_term', true, $term_id, $taxonomy )
			) {
				do_action( 'aiom_save_term', $term_id, $taxonomy );
			}
		}

		/**
		 * Trigger prefixed 'aiom_save_user' action for internal usage on
		 * wordpress's core 'edit_term' or 'created_term' action
		 *
		 * @param int    $user_id  User ID.
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function save_user( $user_id ) {
			if( isset( $_POST[ 'has_aiom_data' ] )
			    && $_POST[ 'has_aiom_data' ]
			    && apply_filters( 'aiom_allow_save_user', true, $user_id )
			) {
				do_action( 'aiom_save_user', $user_id );
			}
		}

	}

	AIOM_Hooks::attach();

}