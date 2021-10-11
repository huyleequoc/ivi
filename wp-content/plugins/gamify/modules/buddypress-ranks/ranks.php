<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'GFY_BP_Ranks_Module' ) && class_exists( 'myCRED_Module' ) ) {

	/**
	 * Class GFY_BP_Ranks_Module
	 */
	final class GFY_BP_Ranks_Module extends myCRED_Module {

		const MODULE_ID = 'GFY_BP_Ranks_Module';
		const MODULE_NAME = 'gfy_bp_ranks';

		/**
		 * GFY_BP_Ranks_Module constructor.
		 */
		public function __construct() {
			parent::__construct( self::MODULE_ID, array(
				'module_name' => self::MODULE_NAME,
				'register'    => false,
				'add_to_core' => true,
			) );
		}

		/**
		 * Admin init
		 */
		public function module_admin_init() {
			add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_rank_metadata' ), 10, 2 );
		}

		/**
		 * Module init
		 */
		public function module_init() {
			add_filter( 'mycred_bp_header_ranks_row', array( $this, 'edit_bp_header_ranks_row' ), 10, 4 );
			add_filter( 'mycred_bp_rank_in_header', array( $this, 'wrap_bp_ranks_in_header' ), 10, 2 );
		}

		/**
		 * Register meta boxes
		 */
		public function register_meta_boxes() {
			add_meta_box(
				'gfy-rank-meta-boxes',
				__( 'Gamify', 'gamify' ),
				array( $this, 'render_meta_box' ),
				'mycred_rank',
				'normal',
				'high'
			);
		}

		/**
		 * Render meta boxes
		 */
		public function render_meta_box() {
			global $post;
			$rank_description = get_post_meta( $post->ID, 'gfy_rank_description', true );

			printf( '<label for="gfy_rank_description"><b>%s</b></label>', __( 'Rank Description', 'gamify' ) );
			wp_editor( $rank_description, 'gfy_rank_description', array(
				'media_buttons' => false,
			) );
		}

		/**
		 * Edit "Buddypress" header rank row
		 *
		 * @param $template string              Selected template
		 * @param $user_id  int                 User ID
		 * @param $rank_id  int                 User rank ID
		 * @param $mycred   myCRED_Settings     Settings
		 *
		 * @return string
		 */
		public function edit_bp_header_ranks_row( $template, $user_id, $rank_id, $mycred ) {

			$template = '';
			if ( $rank_id ) {

				$rank = mycred_get_rank( $rank_id );
				if ( $rank ) {
					$total_balance = mycred_get_users_total_balance( $user_id, $mycred->cred_id );

					$template_name = 'modules/buddypress-ranks/templates/bp_member_header.php';
					$template_data = array(
						'rank'         => $rank,
						'user_balance' => $total_balance,
					);

					ob_start();
					gfy_get_template_part( $template_name, $template_data );
					$template = ob_get_clean();
				}

			}

			return $template;

		}

		/**
		 * Wrap BP header ranks
		 *
		 * @param string $output  Current HTML
		 * @param int    $user_id Displayed user ID
		 *
		 * @return string
		 */
		public function wrap_bp_ranks_in_header( $output, $user_id ) {
			return sprintf( '<div class="gfy-bp-component gfy-bp-my-ranks">%s</div>', $output );
		}

		/**
		 * Save post metadata
		 *
		 * @param $post_id  int         Post ID
		 * @param $post     WP_Post     Post object
		 */
		public function save_rank_metadata( $post_id, $post ) {

			if ( 'mycred_rank' != $post->post_type ) {
				return;
			}

			if ( isset( $_POST[ 'gfy_rank_description' ] ) ) {
				$rank_description = sanitize_text_field( $_POST[ 'gfy_rank_description' ] );
				update_post_meta( $post_id, 'gfy_rank_description', $rank_description );
			}
		}

	}

	if ( GFY_Plugin_Management_Service::get_instance()->is_plugin_active( 'buddypress/bp-loader.php' ) ) {
		GFY_myCRED_Module_Loader::get_instance()->register(
			GFY_BP_Ranks_Module::MODULE_NAME,
			'GFY_BP_Ranks_Module',
			array(
				'depended' => 'ranks',
				'includes' => array(
					__DIR__ . DIRECTORY_SEPARATOR . 'bp-component' . DIRECTORY_SEPARATOR . 'bootstrap.php',
				),
			)
		);
	}

}