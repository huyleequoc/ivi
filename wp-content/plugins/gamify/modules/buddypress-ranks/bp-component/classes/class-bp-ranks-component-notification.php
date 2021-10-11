<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if(
	bp_is_active( 'notifications' )
	&& ! class_exists( 'GFY_BP_Ranks_Component_Notification' )
) {

	class GFY_BP_Ranks_Component_Notification {

		const RANK_DEMOTED = 'rank_demoted';
		const RANK_PROMOTED = 'rank_promoted';

		/**
		 * Holds current instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return GFY_BP_Ranks_Component_Notification|null
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
				static::$_instance = new GFY_BP_Ranks_Component_Notification();
			}

			return static::$_instance;

		}

		/**
		 * GFY_BP_Ranks_Component_Notification constructor.
		 */
		private function __construct() {
			$this->setup_actions();
		}

		/**
		 * A dummy magic method to prevent GFY_BP_Ranks_Component_Notification from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup actions
		 */
		public function setup_actions() {
			add_action( 'mycred_user_got_demoted', array( $this, 'rank_demotion' ), 10, 2 );
			add_action( 'mycred_user_got_promoted', array( $this, 'rank_promotion' ), 10, 2 );
			add_action( 'bp_before_member_header_meta', array( $this, 'mark_notifications_as_read' ) );
		}

		/**
		 * Format notifications related to badge assignment.
		 *
		 * @param string $action            The type of zf_notification item. Just 'badge_assign' for now.
		 * @param int    $item_id           Item ID.
		 * @param int    $secondary_item_id Secondary item number.
		 * @param int    $total_items       The total number of notifications to format.
		 * @param string $format            'string' to get a BuddyBar-compatible notification, 'array' otherwise.
		 * @param int    $id                Optional. The notification ID.
		 * @return string $return Formatted @mention notification.
		 */
		public function format_notification( $action, $item_id, $secondary_item_id, $total_items, $format = 'string', $id = 0 ) {

			$is_valid = true;
			switch ( $action ) {
				case self::RANK_DEMOTED:

					$title = sprintf(
						_n( 'Your were demoted by %d rank', 'Your were demoted by %d ranks',  (int)$total_items, 'gamify' ),
						(int)$total_items
					);

					if ( (int)$total_items > 1 ) {
						$text   = sprintf( __( 'You were demoted by %d ranks.', 'gamify' ), (int)$total_items );
						$link   = bp_loggedin_user_domain();
						$amount = 'multiple';
					} else {
						$rank       = mycred_get_rank( $item_id );

						if( $rank ) {
							$link = bp_loggedin_user_domain();
							$text = sprintf( __( 'You were demoted to new rank - %s', 'gamify' ), $rank->title );
							$amount = 'single';
						} else {
							$is_valid = false;
						}
					}
					break;
				case self::RANK_PROMOTED:

					$title = sprintf(
						_n( 'Your were promoted by %d rank', 'Your were promoted by %d ranks',  (int)$total_items, 'gamify' ),
						(int)$total_items
					);

					if ( (int)$total_items > 1 ) {
						$text   = sprintf( __( 'You were promoted by %d ranks.', 'gamify' ), (int)$total_items );
						$link   = bp_loggedin_user_domain();
						$amount = 'multiple';
					} else {
						$rank       = mycred_get_rank( $item_id );

						if( $rank ) {
							$link   = bp_loggedin_user_domain();
							$text   = sprintf( __( 'You were promoted to new rank - %s', 'gamify' ), $rank->title );
							$amount = 'single';
						} else {
							$is_valid = false;
						}
					}
					break;
				default:
					$is_valid = false;
					$title = '';
					$text = '';
					$link = '';
					$amount = '';
			}

			$return = '';
			if( $is_valid ) {

				if ( 'string' == $format ) {

					/**
					 * Filters the zf_submission notification for the string format.
					 *
					 * This is a variable filter that is dependent on how many items
					 * need notified about. The two possible hooks are bp_zf_submission_single_post_published_notification
					 * or bp_zf_submission_multiple_post_published_notification.
					 *
					 * @param string $string            HTML anchor tag for the interaction.
					 * @param string $link              The permalink for the interaction.
					 * @param int    $total_items       How many items being notified about.
					 * @param int    $item_id           ID of the article being formatted.
					 * @param int    $secondary_item_id ID of the user who inited the interaction.
					 */
					$return = apply_filters( 'gfy/bp_ranks/' . $amount . '/' . $action . '/notification',
						'<a href="' . esc_url( $link ) . '" title="' . esc_attr( $title ) . '">' . esc_html( $text ) . '</a>',
						$link,
						(int)$total_items,
						$item_id,
						$secondary_item_id
					);
				} else {

					/**
					 * Filters the zf_submission notification for any non-string format.
					 *
					 * This is a variable filter that is dependent on how many items need notified about.
					 * The two possible hooks are bp_zf_submission_single_post_published_notification
					 * or bp_zf_submission_multiple_post_published_notification.
					 *
					 * @param array  $array       Array holding the content and permalink for the interaction notification.
					 * @param string $link        The permalink for the interaction.
					 * @param int    $total_items How many items being notified about.
					 * @param int    $item_id     ID of the article being formatted.
					 * @param int    $user_id     ID of the user who inited the interaction.
					 */
					$return = apply_filters( 'gfy/bp_ranks/' . $amount . '/' . $action . '/notification', array(
						'text' => $text,
						'link' => $link
					), $link, (int)$total_items, $item_id, $secondary_item_id );
				}

				/**
				 * Fires right before returning the formatted notifications.
				 *
				 * @param string $action            The type of notification item.
				 * @param int    $item_id           Earned badge ID.
				 * @param int    $secondary_item_id The user ID who inited the interaction.
				 * @param int    $total_items       Total amount of items to format.
				 */
				do_action( 'gfy/bp_ranks/format_notification', $action, $item_id, $secondary_item_id, $total_items );

			}

			return $return;
		}

		/**
		 * Rank demotion callback
		 * @param int $user_id User ID
		 * @param int $rank_id User rank ID
		 */
		public function rank_demotion( $user_id, $rank_id ) {
			$this->setup_notification( $user_id, $rank_id, self::RANK_DEMOTED );
		}

		/**
		 * Rank promotion callback
		 * @param int $user_id User ID
		 * @param int $rank_id User rank ID
		 */
		public function rank_promotion( $user_id, $rank_id ) {
			$this->setup_notification( $user_id, $rank_id, self::RANK_PROMOTED );
		}

		/**
		 * Setup notification for user when user rank is changed
		 *
		 * @param int $user_id  User ID
		 * @param int $rank_id  User rank ID
		 * @param int $action   Current rank action
		 */
		private function setup_notification( $user_id, $rank_id, $action ) {

			if( GFY_BP_Ranks_BP_Component_Helper::is_notification_disabled() ) {
				return;
			}

			$component_id = GFY_BP_Ranks_BP_Component_Helper::get_id();

			bp_notifications_add_notification( array(
				'user_id'           => $user_id,
				'item_id'           => $rank_id,
				'component_name'    => buddypress()->{$component_id}->id,
				'component_action'  => $action,
				'date_notified'     => bp_core_current_time(),
				'is_new'            => 1,
			) );
		}

		/**
		 * Mark notifications as read
		 */
		public function mark_notifications_as_read() {

			if( GFY_BP_Ranks_BP_Component_Helper::is_notification_disabled() ) {
				return;
			}

			$user_id = bp_displayed_user_id();
			$current_user_id = get_current_user_id();

			if( $user_id != $current_user_id ) {
				return;
			}

			bp_notifications_mark_notifications_by_type( $current_user_id, '', self::RANK_DEMOTED, false );
			bp_notifications_mark_notifications_by_type( $current_user_id, '', self::RANK_PROMOTED, false );

		}

	}

}