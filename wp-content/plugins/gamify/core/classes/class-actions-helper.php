<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class GFY_Actions_Helper {

	/**
	 * Holds class single instance
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get instance
	 * @return GFY_Actions_Helper|null
	 */
	public static function get_instance() {

		if( null == static::$_instance ) {
			static::$_instance = new self();
		}

		return static::$_instance;

	}

	/**
	 * A dummy magic method to prevent GFY_Actions_Helper from being cloned.
	 *
	 */
	public function __clone() {
		throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
	}

	/**
	 * GFY_Actions_Helper constructor.
	 */
	private function __construct() {
		$this->setup_actions();
	}

	/**
	 * Setup actions
	 */
	private function setup_actions() {
		add_filter( 'mycred_badge_user_value', array( $this, 'award_badge_to_user' ), 10, 3 );
		add_action( 'mycred_user_got_demoted', array( $this, 'rank_changed' ), 10, 2 );
		add_action( 'mycred_user_got_promoted', array( $this, 'rank_changed' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_notification_assets' ), 10, 1 );
		add_action( 'wp_footer', array( $this, 'render_notifications_popup' ) );
	}

	/**
	 * Callback to setup notifications for the user once the badge is awarded.
	 * @param int $level Level ID
	 * @param int $user_id User ID
	 * @param int $badge_id Badge ID
	 *
	 * @return int
	 */
	public function award_badge_to_user( $level, $user_id, $badge_id ) {

		/***** Firstly, let's try to get existing notifications, if any */
		$notifications = get_user_meta( $user_id, 'gfy_popup_notifications', true );

		/***** In case user does not have any, we need to set a basic configuration */
		if( ! $notifications ) {
			$notifications = array();
		}

		/***** Configure notification */
		$notifications[] = array(
			'type' => 'badge_awarded',
			'args' => array(
				'badge_id'  => $badge_id,
				'level'     => $level
			)
		);

		/***** Finally, update user notifications */
		update_user_meta( $user_id, 'gfy_popup_notifications', $notifications );

		return $level;
	}

	/**
	 * Callback to setup notifications for the user once the rank is promoted.
	 *
	 * @param int $user_id User ID
	 * @param int $rank_id User rank ID
	 */
	public function rank_changed( $user_id, $rank_id ) {

		/***** Firstly, let's try to get existing notifications, if any */
		$notifications = get_user_meta( $user_id, 'gfy_popup_notifications', true );

		/***** In case user does not have any, we need to set a basic configuration */
		if( ! $notifications ) {
			$notifications = array();
		}

		/***** Configure notification */
		$notifications[] = array(
			'type' => 'rank_promoted',
			'args' => array(
				'rank_id'  => $rank_id
			)
		);

		/***** Finally, update user notifications */
		update_user_meta( $user_id, 'gfy_popup_notifications', $notifications );

	}

	/**
	 * Callback to render notifications popup if needed
	 */
	public function render_notifications_popup() {
		if( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();
		$user_notifications = get_user_meta( $user_id, 'gfy_popup_notifications', true );
		if( ! (bool)$user_notifications ) {
			return;
		}

		$execute = false;
		$notification = array_splice( $user_notifications, 0, 1 );
		update_user_meta( $user_id, 'gfy_popup_notifications', $user_notifications );
		$notification = $notification[0];

		$notification_data = array(
			'greetings'      => '',
			'description'    => '',
			'trophy_title'   => '',
			'trophy_image'   => '',
			'share_message' => ''
		);

		/***** Badge awarded */
		if( $notification[ 'type' ] == 'badge_awarded' ) {
			/**
			 * @var myCRED_Badge $badge Badge instance
			 */
			$badge = mycred_get_badge( $notification['args']['badge_id'], $notification['args']['level'] );
			if( $badge ) {
				$level_label = $badge->level_label ? $badge->level_label : ( $notification['args']['level'] + 1 );
				$notification_data = array(
					'greetings'     => __( 'Congratulations!', 'gamify' ),
					'description'   => __( 'You\'ve earned a badge!', 'gamify' ),
					'share_message' => __( 'I\'ve earned a "badge"!', 'gamify' ) . ' ' . $badge->title . ' - ' . $level_label,
					'trophy_title'  => $badge->title,
					'trophy_image'  => ( $badge->level_image !== false ) ? $badge->get_image( $notification['args']['level'] ) : $badge->get_image( 'main' )
				);
				$execute = true;
			}
		}
		/***** Rank promoted */
		elseif( $notification[ 'type' ] == 'rank_promoted' ) {
			/**
			 * @var myCRED_Rank $rank Rank instance
			 */
			$rank = mycred_get_rank( $notification['args']['rank_id'] );
			if( $rank ) {
				$notification_data = array(
					'greetings'     => __( 'Congratulations!', 'gamify' ),
					'description'   => __( 'You\'ve promoted a rank!', 'gamify' ),
					'share_message' => __( "I've promoted a rank!", 'gamify' ) . ' ' . $rank->title,
					'trophy_title'  => $rank->title,
					'trophy_image'  => $rank->get_image()
				);
				$execute = true;
			}
		}

		if( ! $execute ) {
			return;
		}

		preg_match('/src="([^"]*)"/i', $notification_data[ 'trophy_image' ], $matches );
		$notification_data[ 'trophy_image_url' ] = empty( $matches ) ? '' : $matches[1];

		$template_name = 'core/templates/notifications-popup.php';
		$template_data = wp_parse_args(
			apply_filters( 'gfy/notification_popup_content', $notification_data, $notification[ 'type' ] ),
			array(
				'greetings'        => '',
				'description'      => '',
				'share_message'    => '',
				'trophy_title'     => '',
				'trophy_image'     => '',
				'trophy_image_url' => '',
			)
		);

		gfy_get_template_part( $template_name, $template_data );

	}

	/**
	 * Enqueue assets
	 */
	public function enqueue_notification_assets() {
		if( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();
		$user_notifications = get_user_meta( $user_id, 'gfy_popup_notifications', true );
		if( ! (bool)$user_notifications ) {
			return;
		}

		$plugin_data = GFY_Plugin_Management_Service::get_instance()->get_plugin_data( GFY_MAIN_FILE );
		$assets_url = trailingslashit( GFY_CORE_URL . 'assets' );
		$version = $plugin_data[ 'Version' ] && ! empty( $plugin_data[ 'Version' ] ) ? $plugin_data[ 'Version' ] : false;
		$fb_app_id = gfy_get_fb_app_id();

		wp_enqueue_script( 'gfy-notifications-scripts', $assets_url . 'notification-popup.js', array( 'jquery' ), $version, true );
		wp_localize_script( 'gfy-notifications-scripts', 'gfy', array( 'fb_app_id' => $fb_app_id ) );

		if( $fb_app_id ) {
			wp_enqueue_script(
				'facebook-jssdk',
				'https://connect.facebook.net/' . get_locale() . '/sdk.js',
				array(),
				$version,
				true
			);
		}
	}

}