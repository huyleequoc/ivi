<?php

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( !class_exists( 'GFY_BP_Achievements_Module' ) && class_exists( 'myCRED_Module' ) ) {

	/**
	 * Class GFY_BP_Achievements_Module
	 */
	final class GFY_BP_Achievements_Module extends myCRED_Module {

		const MODULE_ID = 'GFY_BP_Achievements_Module';
		const MODULE_NAME = 'gfy_bp_achievements';

		/**
		 * GFY_BP_Achievements_Module constructor.
		 */
		public function __construct () {
			parent::__construct( self::MODULE_ID, array(
				'module_name' => self::MODULE_NAME,
				'register'    => false,
				'add_to_core' => true,
			) );

			add_action( 'mycred_init', array( $this, 'edit_buddypress_actions' ), 9999 );
		}

		/**
		 * Module init
		 */
		public function module_init () {
			add_filter( 'mycred_the_badge', array( $this, 'wrap_badge_image' ), 10, 4 );
			add_filter( 'mycred_badge_image', array( $this, 'edit_mycred_badge_image' ), 10, 3 );
		}

		/**
		 * Admin init
		 */
		public function module_admin_init () {
			add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_badge_metadata' ), 10, 3 );
		}

		/**
		 * Modify buddypress integration actions
		 */
		public function edit_buddypress_actions () {

			/**
			 * @var myCRED_Badge_Module $addon
			 */
			$badges_module = mycred_get_module( 'badges' );

			if ( $badges_module ) {

				/********** BP member header */
				if ( $badges_module->badges[ 'buddypress' ] == 'header' || $badges_module->badges[ 'buddypress' ] == 'both' ) {
					$action_priority = 40;

					/********** Change achievements rendering location */
					remove_action( 'bp_before_member_header_meta', array( $badges_module, 'insert_into_buddypress' ) );
					add_action( 'bp_before_member_header_meta', array( $this, 'bp_member_header_render_achievements_title' ), $action_priority );
				}

				/********** BP user profile */
				if ( $badges_module->badges[ 'buddypress' ] == 'profile' || $badges_module->badges[ 'buddypress' ] == 'both' ) {
					remove_action( 'bp_after_profile_loop_content', array( $badges_module, 'insert_into_buddypress' ) );
					add_action( 'bp_after_profile_loop_content', array( $this, 'bp_profile_render_achievements' ), 9 );
				}
			}
		}

		/**
		 * Render achievements title in buddypress header meta
		 */
		public function bp_member_header_render_achievements_title () {
			$template_name = 'modules/buddypress-achievements/templates/bp_member_header.php';
			$template_data = array(
				'title'           => apply_filters( 'gfy/buddypress-achievements/bp_member_header/title', __( 'Achievements', 'gamify' ) ),
				'tooltip_content' => apply_filters( 'gfy/buddypress-achievements/bp_member_header/tooltip_content',
					__( 'Here\'s where all your unlocked badges are displayed for everyone to see!', 'gamify' ) ),
				'module'          => mycred_get_module( 'badges' ),
			);

			gfy_get_template_part( $template_name, $template_data );
		}

		/**
		 * Render achievements in buddypress profile layout
		 */
		public function bp_profile_render_achievements () {
			$template_name = 'modules/buddypress-achievements/templates/bp_member_profile.php';
			$template_data = array(
				'title' => apply_filters( 'gfy/buddypress-achievements/bp_member_profile/title', __( 'Achievements', 'gamify' ) ),
				'addon' => mycred_get_module( 'badges' ),
			);

			gfy_get_template_part( $template_name, $template_data );
		}

		/**
		 * Wrap badge image
		 *
		 * @param $badge_image  string          Badge image HTML
		 * @param $badge_id     int             Badge ID
		 * @param $badge        myCRED_Badge    Badge instance
		 * @param $user_id      int             User ID
		 *
		 * @return string
		 */
		public function wrap_badge_image ( $badge_image, $badge_id, $badge, $user_id ) {
			if( $badge_image ) {
				$badge_image = sprintf( '<div class="the-badge">%s</div>', $badge_image );
			}
			
			return $badge_image;
		}

		/**
		 * Register meta boxes
		 */
		public function register_meta_boxes () {
			add_meta_box(
				'gfy-badges-meta-boxes',
				__( 'Gamify', 'gamify' ),
				array( $this, 'render_meta_box' ),
				'mycred_badge',
				'normal',
				'high'
			);
		}

		/**
		 * Render meta boxes
		 */
		public function render_meta_box () {
			global $post;
			$badge_description = get_post_meta( $post->ID, 'gfy_badge_description', true );
			?>
			<label for="gfy_badge_description"><b><?php _e( 'Badge Description', 'gamify' ); ?></b></label>
			<?php
			wp_editor( $badge_description, 'gfy_badge_description', array(
				'media_buttons' => false,
			) );
		}

		/**
		 * Save post metadata
		 *
		 * @param $post_id  int         Post ID
		 * @param $post     WP_Post     Post object
		 * @param $update   bool        Whether this is an existing post being updated or not.
		 */
		public function save_badge_metadata ( $post_id, $post, $update ) {

			if ( 'mycred_badge' != $post->post_type ) {
				return;
			}

			$badge_description = '';
			if ( isset( $_POST[ 'gfy_badge_description' ] ) ) {
				$badge_description = sanitize_text_field( $_POST[ 'gfy_badge_description' ] );
			}
			update_post_meta( $post_id, 'gfy_badge_description', $badge_description );
		}

		/**
		 * Edit myCRED badge image html
		 *
		 * @param string          $html     Current HTML
		 * @param null|string|int $level    Badge level
		 * @param myCRED_Badge    $instance Badge instance
		 *
		 * @return string
		 */
		public function edit_mycred_badge_image ( $html, $level, $instance ) {

			$level_label = esc_attr( $instance->level_label );
			$badge_title = esc_attr( $instance->title );

			if ( $level_label && ( $level_label != $badge_title ) ) {
				preg_match_all( '/(title|alt)="([^"]*)"/i', $html, $attr_matches );
				$atts = $attr_matches[ 0 ];
				$map = $attr_matches[ 1 ];

				$replace_pairs = array();
				foreach ( $atts as $index => $search ) {
					$attr = $map[ $index ];
					$replace_pairs[ $search ] = sprintf( '%s="%s"', $attr, $level_label );
				}

				$html = strtr( $html, $replace_pairs );
			}

			return $html;
		}

		/**
		 * Get user unearned badges data
		 *
		 * @param $user_id      int User ID
		 * @param $paged        int Current page number
		 * @param $per_page     int|string -1 for all badges, integer for paginated part
		 *
		 * @return array
		 */
		public function get_user_unearned_badges ( $user_id, $paged = 1, $per_page = '-1' ) {

			global $wpdb;
			$badges = array();

			foreach ( mycred_get_badge_ids() as $badge_id ) {

				if ( !$badge_id ) {
					continue;
				}

				$user_excluded = false;
				$levels = mycred_get_badge_levels( $badge_id );
				$base_requirements = $levels[ 0 ][ 'requires' ];
				$compare = $levels[ 0 ][ 'compare' ];
				$user_results = array();


				// Based on the base requirements, we first get the users log entry results
				if ( !empty( $base_requirements ) ) {
					foreach ( $base_requirements as $requirement_id => $requirement ) {

						if ( $requirement[ 'type' ] == '' ) {
							$requirement[ 'type' ] = MYCRED_DEFAULT_TYPE_KEY;
						}

						$mycred = mycred( $requirement[ 'type' ] );
						if ( $mycred->exclude_user( $user_id ) ) {
							$user_excluded = true;
							break;
						};

						$having = 'COUNT(*)';
						if ( $requirement[ 'by' ] != 'count' ) {
							$having = 'SUM(creds)';
						}

						$query = $wpdb->prepare( "SELECT {$having} FROM {$mycred->log_table} WHERE ctype = %s AND ref = %s AND user_id = %d;", $requirement[ 'type' ], $requirement[ 'reference' ], $user_id );

						$user_results[ $requirement[ 'reference' ] ] = (int)$wpdb->get_var( $query );

					}
				}

				if ( $user_excluded ) {
					continue;
				}

				$level_id = mycred_badge_level_reached( $user_id, $badge_id );
				if ( $level_id === false ) {
					$level_id = 0;
					$prev_level = array_fill_keys( array_keys( $user_results ), 0 );
					$next_level = wp_list_pluck( $levels[ $level_id ][ 'requires' ], 'amount', 'reference' );
				} else {
					$prev_level = isset( $levels[ $level_id ] ) ? wp_list_pluck( $levels[ $level_id ][ 'requires' ], 'amount', 'reference' ) : array_fill_keys( array_keys( $user_results ), 0 );

					++$level_id;
					$next_level = isset( $levels[ $level_id ] ) ? wp_list_pluck( $levels[ $level_id ][ 'requires' ], 'amount', 'reference' ) : false;
				}

				if ( !(bool)$next_level ) {
					continue;
				}

				$total_progresses = array();
				foreach ( $user_results as $reference => $user_reference_value ) {

					if ( !(int)$next_level[ $reference ] ) {
						continue;
					}

					if ( $next_level[ $reference ] == 999999999 ) {
						continue;
					}

					$progress = $user_reference_value * 100 / (int)$next_level[ $reference ];
					$total_progresses[ $reference ] = array(
						'progress'             => min( $progress, 100 ),
						'user_reference_value' => $user_reference_value,
						'prev_level_value'     => $prev_level[ $reference ],
						'next_level_value'     => $next_level[ $reference ],
					);
				}

				$progress = 0;
				$tmp_progress = wp_list_pluck( $total_progresses, 'progress' );
				if ( $compare == 'AND' ) {

					if ( count( $base_requirements ) > 1 ) {
						// calculate average
						$percentage = min( array_sum( $tmp_progress ) / count( $tmp_progress ), 100 );
						$progress = array(
							'type'       => 'percentage',
							'percentage' => $percentage,
						);
					} else {

						$total_progresses = array_values( $total_progresses );
						$progress = array(
							'type'             => 'number',
							'percentage'       => min( $total_progresses[ 0 ][ 'progress' ], 100 ),
							'user_value'       => $total_progresses[ 0 ][ 'user_reference_value' ],
							'prev_level_value' => $total_progresses[ 0 ][ 'prev_level_value' ],
							'next_level_value' => $total_progresses[ 0 ][ 'next_level_value' ],
						);
					}
				} else if ( $compare == 'OR' ) {
					$highest = max( $tmp_progress );
					$maxs = array_keys( $tmp_progress, $highest );

					$progress = array(
						'type'             => 'number',
						'percentage'       => min( $highest, 100 ),
						'user_value'       => $total_progresses[ $maxs[ 0 ] ][ 'user_reference_value' ],
						'prev_level_value' => $total_progresses[ $maxs[ 0 ] ][ 'prev_level_value' ],
						'next_level_value' => $total_progresses[ $maxs[ 0 ] ][ 'next_level_value' ],
					);
				}

				$badges[] = array(
					'badge_id' => $badge_id,
					'level'    => $level_id,
					'progress' => $progress,
					'type'     => 'unearned',
				);
			}

			$total = count( $badges );
			if ( is_numeric( $per_page ) && $per_page > 0 ) {
				$offset = ( $paged - 1 ) * $per_page;
				$badges = array_slice( $badges, $offset, $per_page );
			}

			return array(
				'badges' => $badges,
				'total'  => $total,
			);
		}

		/**
		 * Get user earned badges data
		 *
		 * @param $user_id      int User ID
		 * @param $paged        int Current page number
		 * @param $per_page     int|string -1 for all badges, integer for paginated part
		 *
		 * @return array
		 */
		public function get_user_earned_badges ( $user_id, $paged = 1, $per_page = '-1' ) {
			$badges = mycred_get_users_badges( $user_id );
			if ( !empty( $badges ) ) {
				$progress = 100;
				$earned_badges = array();
				foreach ( $badges as $badge_id => $level ) {
					if ( !$badge_id ) {
						continue;
					}
					$earned_badges[] = array(
						'badge_id' => $badge_id,
						'level'    => $level,
						'progress' => $progress,
						'type'     => 'earned',
					);
				}
				$badges = $earned_badges;
			}

			$total = count( $badges );
			if ( is_numeric( $per_page ) && $per_page > 0 ) {
				$offset = ( $paged - 1 ) * $per_page;
				$badges = array_slice( $badges, $offset, $per_page );
			}

			return array(
				'badges' => $badges,
				'total'  => $total,
			);
		}

		/**
		 * Get user all badges data
		 *
		 * @param $user_id      int User ID
		 * @param $paged        int Current page number
		 * @param $per_page     int|string -1 for all badges, integer for paginated part
		 *
		 * @return array
		 */
		public function get_user_all_badges ( $user_id, $paged = 1, $per_page = '-1' ) {
			$earned_badges_data = $this->get_user_earned_badges( $user_id );
			$unearned_badges_data = $this->get_user_unearned_badges( $user_id );

			$badges = array_merge( $earned_badges_data[ 'badges' ], $unearned_badges_data[ 'badges' ] );
			$total = $earned_badges_data[ 'total' ] + $unearned_badges_data[ 'total' ];

			if ( is_numeric( $per_page ) && $per_page > 0 ) {
				$offset = ( $paged - 1 ) * $per_page;
				$badges = array_slice( $badges, $offset, $per_page );
			}

			return array(
				'badges' => $badges,
				'total'  => $total,
			);
		}

	}

	if ( GFY_Plugin_Management_Service::get_instance()->is_plugin_active( 'buddypress/bp-loader.php' ) ) {
		GFY_myCRED_Module_Loader::get_instance()->register(
			GFY_BP_Achievements_Module::MODULE_NAME,
			'GFY_BP_Achievements_Module',
			array(
				'depended' => 'badges',
				'includes' => array(
					__DIR__ . DIRECTORY_SEPARATOR . 'bp-component' . DIRECTORY_SEPARATOR . 'bootstrap.php',
				),
			)
		);
	}

}