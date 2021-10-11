<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if(
	bp_is_active( 'notifications' )
	&& ! class_exists( 'GFY_BP_Achievements_Component_Notification' )
) {

	class GFY_BP_Achievements_Component_Notification {

		const BADGE_ASSIGN = 'badge_assign';

		/**
		 * Holds current instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return GFY_BP_Achievements_Component_Template|null
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
				static::$_instance = new GFY_BP_Achievements_Component_Notification();
			}

			return static::$_instance;

		}

		/**
		 * GFY_BP_Achievements_Component_Template constructor.
		 */
		private function __construct() {
			$this->setup_actions();
		}

		/**
		 * A dummy magic method to prevent GFY_BP_Achievements_Component_Template from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup actions
		 */
		public function setup_actions() {

			$component_id = GFY_BP_Achievements_BP_Component_Helper::get_id();

			$slug_all = GFY_BP_Achievements_BP_Component_Helper::get_subpage_slug( 'all' );
			$slug_earned = GFY_BP_Achievements_BP_Component_Helper::get_subpage_slug( 'earned' );

			add_action( 'gfy/' . $component_id .'/render_' . $slug_all, array( $this, 'mark_notifications_as_read' ) );
			add_action( 'gfy/' . $component_id .'/render_' . $slug_earned, array( $this, 'mark_notifications_as_read' ) );

			add_filter( 'mycred_badge_user_value', array( $this, 'badge_assigned' ), 10, 3 );
			add_action( 'gfy/bp_achievements/badge_assigned', array( $this, 'setup_notification' ), 10, 3 );
		}

		/**
		 * Trigger an action on badge assigning
		 *
		 * @param int $level Current level
		 * @param int $user_id User ID
		 * @param int $badge_id Badge post ID
		 * @return int
		 */
		public function badge_assigned( $level, $user_id, $badge_id ) {

			if( GFY_BP_Achievements_BP_Component_Helper::is_notification_disabled() ) {
				return;
			}

			do_action( 'gfy/bp_achievements/badge_assigned', $level, $user_id, $badge_id );

			return $level;
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
				case self::BADGE_ASSIGN:

					$title = sprintf( _n( '%d earned badge', '%d earned badges',  (int)$total_items, 'gamify' ), (int)$total_items );

					if ( (int)$total_items > 1 ) {
						$text   = sprintf( __( 'You have earned %d badges.', 'gamify' ), (int)$total_items );
						$link   = GFY_BP_Achievements_BP_Component_Helper::get_page_url( 'earned', true );
						$amount = 'multiple';
					} else {
						$badge = mycred_get_badge( $item_id, $secondary_item_id );
						if( $badge ) {
							if ( $badge->level_label ) {
								$badge_title = $badge->level_label;
							} else {
								$badge_title = sprintf( __( '%s: Level %d', 'gamify' ), $badge->title, ( $secondary_item_id + 1 ) );
							}

							$link = GFY_BP_Achievements_BP_Component_Helper::get_page_url( 'earned', true );
							$text = sprintf( __( 'You have earned a new badge - %s', 'gamify' ), $badge_title );
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
					$return = apply_filters( 'gfy/bp_achievements/' . $amount . '/' . $action . '/notification',
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
					$return = apply_filters( 'gfy/bp_achievements/' . $amount . '/' . $action . '/notification', array(
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
				do_action( 'gfy/bp_achievements/format_notification', $action, $item_id, $secondary_item_id, $total_items );

			}

			return $return;
		}

		/**
		 * Setup notification for user when new bagde is assigned
		 *
		 * @param int $level    Current level
		 * @param int $user_id  User ID
		 * @param int $badge_id Badge post ID
		 */
		public function setup_notification( $level, $user_id, $badge_id ) {

			if( GFY_BP_Achievements_BP_Component_Helper::is_notification_disabled() ) {
				return;
			}

			$component_id = GFY_BP_Achievements_BP_Component_Helper::get_id();

			bp_notifications_add_notification( array(
				'user_id'           => $user_id,
				'item_id'           => $badge_id,
				'secondary_item_id' => $level,
				'component_name'    => buddypress()->{$component_id}->id,
				'component_action'  => self::BADGE_ASSIGN,
				'date_notified'     => bp_core_current_time(),
				'is_new'            => 1,
			) );

		}

		/**
		 * Mark notifications as read
		 */
		public function mark_notifications_as_read() {

			if( GFY_BP_Achievements_BP_Component_Helper::is_notification_disabled() ) {
				return;
			}

			$user_id = bp_displayed_user_id();
			$current_user_id = get_current_user_id();
			$component_id = GFY_BP_Achievements_BP_Component_Helper::get_id();

			if( $user_id != $current_user_id ) {
				return;
			}

			bp_notifications_mark_notifications_by_type( $current_user_id, buddypress()->{$component_id}->id, self::BADGE_ASSIGN, false );

		}

	}

}