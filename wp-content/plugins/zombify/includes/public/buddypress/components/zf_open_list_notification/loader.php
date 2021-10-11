<?php
/**
 * BuddyPress Zombify Open List Notification Loader.
 *
 *
 * @package Zombify
 * @subpackage Buddypress Submissions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class ZF_Open_List_Notification {

	/**
	 * @var string Holds component unique ID
	 * @since 1.4.7
	 * @version 1.4.7
	 */
	private $_id = 'zf_open_list_notifier';

	/**
	 * Get component ID
	 * @return string
	 * @since 1.4.7
	 * @version 1.4.7
	 */
	public function get_id() {
		return $this->_id;
	}

	/**
	 * @var string Holds component unique slug
	 * @since 1.4.7
	 * @version 1.4.7
	 */
	private $_slug = 'zf_open_list_notifier';

	/**
	 * Get component slug
	 * @return string
	 * @since 1.4.7
	 * @version 1.4.7
	 */
	public function get_slug() {
		return $this->_slug;
	}

	/**
	 * Get mark as read associations with request query param
	 * @return array
	 * @since 1.4.7
	 * @version 1.4.7
	 */
	private function get_mark_as_read_associations() {
		return array(
			1 => 'list_item_published',
			2 => 'list_item_approved_for_list_author',
			3 => 'list_item_approved_for_item_author'
		);
	}

	/**
	 * Holds unique instance
	 * @var ZF_Open_List_Notification
	 * @since 1.4.7
	 * @version 1.4.7
	 */
	private static $_instance = null;

	/**
	 * Get unique instance
	 * @return ZF_Open_List_Notification
	 * @since 1.4.7
	 * @version 1.4.7
	 */
	public static function get_instance() {
		if( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * ZF_Open_List_Notification constructor.
	 * @since 1.4.7
	 * @version 1.4.7
	 */
	private function __construct() {
		$this->setup_actions();
	}

	/**
	 * Setup components actions
	 * @since 1.4.7
	 * @version 1.4.7
	 */
	private function setup_actions() {
		add_action( 'bp_setup_globals', array( $this, 'bp_setup_globals' ) );
		add_action( 'zf_save_open_list_item', array( $this, 'insert_direct_publish_notification' ), 10, 2 );
		add_action( 'publish_list_item', array( $this, 'insert_moderation_publish_notification' ), 10, 2 );
		add_action( 'template_redirect', array( $this, 'may_be_clean_up_post_notification' ) );
	}

	/**
	 * Callback to setup component
	 * @hooked in "bp_setup_globals"
	 * @since 1.4.7
	 * @vers 1.4.7
	 */
	public function bp_setup_globals() {

		if( ! bp_is_active( 'notifications' ) ) {
			return;
		}

		$component_id = $this->get_id();
		$component_slug = $this->get_slug();

		$notifier = new stdClass();
		$notifier->id = $component_id;
		$notifier->slug = $component_slug;
		$notifier->notification_callback = array( $this, 'format_notifications' );

		$bp = buddypress();
		$bp->$component_id = $notifier;
		$bp->active_components[ $component_id ] = $component_id;
	}

	/**
	 * Insert notification for list author on list item direct publishing
	 * @param string|int $post_id The new item ID
	 * @param string|int $list_id The list ID
	 * @since 1.4.7
	 * @version 1.4.7
	 */
	public function insert_direct_publish_notification( $post_id, $list_id ) {

		if( ! bp_is_active( 'notifications' ) ) {
			return;
		}

		// setup notification if the list item does not need to be approved
		$post = get_post( $post_id );
		$list = get_post( $list_id );
		if( ( 'publish' == $post->post_status ) && ( $post->post_author != $list->post_author ) ) {
			$id = bp_notifications_add_notification( array(
				'user_id'           => $list->post_author,
				'item_id'           => $list_id,
				'secondary_item_id' => $post_id,
				'component_name'    => $this->get_id(),
				'component_action'  => 'list_item_published',
				'date_notified'     => bp_core_current_time(),
				'is_new'            => 1,
			) );

			if( $id ) {
				bp_notifications_add_meta( $id, 'author', $post->post_author );
			}
		}
	}

	/**
	 * Insert notification for list author on list item publishing after moderation
	 * @param int $ID The post ID
	 * @param $post WP_Post The post object
	 */
	public function insert_moderation_publish_notification( $ID, $post ) {

		if( ! bp_is_active( 'notifications' ) ) {
			return;
		}

		// do not work with post types rather than "list_item"
		if( 'list_item' != $post->post_type ) {
			return;
		}

		$list = get_post( $post->post_parent );
		if( ! $list ) {
			return;
		}

		// region Notification for list author
		$id = bp_notifications_add_notification( array(
			'user_id'           => $list->post_author,
			'item_id'           => $list->ID,
			'secondary_item_id' => $post->ID,
			'component_name'    => $this->get_id(),
			'component_action'  => 'list_item_approved_for_list_author',
			'date_notified'     => bp_core_current_time(),
			'is_new'            => 1,
		) );
		if( $id ) {
			bp_notifications_add_meta( $id, 'author', $post->post_author );
		}
		// endregion

		// region Notification for list_item author
		$id = bp_notifications_add_notification( array(
			'user_id'           => $post->post_author,
			'item_id'           => $list->ID,
			'secondary_item_id' => $post->ID,
			'component_name'    => $this->get_id(),
			'component_action'  => 'list_item_approved_for_item_author',
			'date_notified'     => bp_core_current_time(),
			'is_new'            => 1,
		) );
		if( $id ) {
			bp_notifications_add_meta( $id, 'author', get_current_user_id() );
		}
		// endregion
	}

	/**
	 * Format notifications.
	 *
	 * @since 1.4.7
	 * @version 1.4.7
	 *
	 * @param string $action            The type of zf_notification item. Just 'list_item_submit' for now.
	 * @param int    $item_id           The list ID.
	 * @param int    $secondary_item_id The list_item ID.
	 * @param int    $total_items       The total number of notifications to format.
	 * @param string $format            'string' to get a BuddyBar-compatible notification, 'array' otherwise.
	 * @param int    $id                Optional. The notification ID.
	 * @return string $return Formatted @mention notification.
	 */
	function format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string', $id = 0 ) {

		$list = get_post( $item_id );
		$user_id = false;
		$associations = array_flip( $this->get_mark_as_read_associations() );


		switch ( $action ) {
			case 'list_item_published':

				$list = get_post( $item_id );
				$list_item = get_post( $secondary_item_id );
				$user_id = bp_notifications_get_meta( $id, 'author' );
				$user_fullname = bp_core_get_user_displayname( $user_id );
				$link = add_query_arg( array( 'mark' => $associations[ $action ] ), get_permalink( $list ) );

				if( (int) $total_items > 1 ) {
					$amount = 'multiple';
					$text = sprintf( __( '@%s published items at "%s"', 'zombify' ), $user_fullname, get_the_title( $list ) );
				} else {
					$amount = 'single';
					$text = sprintf( __( '@%s published "%s" item at "%s"', 'zombify' ), $user_fullname, get_the_title( $list_item ), get_the_title( $list ) );
				}

				break;

			// region Notification for list author
			case 'list_item_approved_for_list_author':

				$list = get_post( $item_id );
				$list_item = get_post( $secondary_item_id );
				$user_id = bp_notifications_get_meta( $id, 'author' );
				$user_fullname = bp_core_get_user_displayname( $user_id );
				$link = add_query_arg( array( 'mark' => $associations[ $action ] ), get_permalink( $list ) );

				if( (int) $total_items > 1 ) {
					$amount = 'multiple';
					$text = sprintf( __( '@%s published items at "%s"', 'zombify' ), $user_fullname, get_the_title( $list ) );
				} else {
					$amount = 'single';
					$text = sprintf( __( '@%s published "%s" item at "%s"', 'zombify' ), $user_fullname, get_the_title( $list_item ), get_the_title( $list ) );
				}

				break;
			// endregion

			// region Notification for list_item author
			case 'list_item_approved_for_item_author':

				$list = get_post( $item_id );
				$list_item = get_post( $secondary_item_id );
				$user_id = bp_notifications_get_meta( $id, 'author' );
				$user_fullname = bp_core_get_user_displayname( $user_id );
				$link = add_query_arg( array( 'mark' => $associations[ $action ] ), get_permalink( $list ) );

				if( (int) $total_items > 1 ) {
					$amount = 'multiple';
					$text = sprintf( __( '@%s approved your items submitted to "%s"', 'zombify' ), $user_fullname, get_the_title( $list ) );
				} else {
					$amount = 'single';
					$text = sprintf( __( '@%s approved your "%s" item submitted to "%s"', 'zombify' ), $user_fullname, get_the_title( $list_item ), get_the_title( $list ) );
				}

				break;
			// endregion
		}

		if ( 'string' == $format ) {

			/**
			 * Filters the zf_submission notification for the string format.
			 *
			 * This is a variable filter that is dependent on how many items
			 * need notified about. The two possible hooks are zf_open_list_item_single_list_item_submit_notification
			 * or zf_open_list_item_multiple_list_item_submit_notification.
			 *
			 * @param string $string          HTML anchor tag for the interaction.
			 * @param string $link            The permalink for the interaction.
			 * @param int    $total_items     How many items being notified about.
			 * @param int    $item_id         ID of the article being formatted.
			 * @param int    $user_id         ID of the user who inited the interaction.
			 */
			$return = apply_filters( 'zf_open_list_item_' . $amount . '_' . $action . '_notification', '<a href="' . esc_url( $link ) . '" title="' . esc_attr( $text ) . '">' . esc_html( $text ) . '</a>', $link, (int) $total_items, $item_id, $user_id );
		} else {

			/**
			 * Filters the zf_submission notification for any non-string format.
			 *
			 * This is a variable filter that is dependent on how many items need notified about.
			 * The two possible hooks are zf_open_list_item_single_post_published_notification
			 * or zf_open_list_item_multiple_post_published_notification.
			 *
			 * @param array  $array           Array holding the content and permalink for the interaction notification.
			 * @param string $link            The permalink for the interaction.
			 * @param int    $total_items     How many items being notified about.
			 * @param int    $item_id         ID of the article being formatted.
			 * @param int    $user_id         ID of the user who inited the interaction.
			 */
			$return = apply_filters( 'zf_open_list_item_' . $amount . '_' . $action . '_notification', array(
				'text' => $text,
				'link' => $link
			), $link, (int) $total_items, $item_id, $user_id );
		}

		/**
		 * Fires right before returning the formatted zf_open_list_item notifications.
		 *
		 * @since 1.2.0
		 *
		 * @param string $action            The type of zf_submission item.
		 * @param int    $item_id           The article ID.
		 * @param int    $secondary_item_id The user ID who inited the interaction.
		 * @param int    $total_items       Total amount of items to format.
		 */
		do_action( 'zf_open_list_item_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

		return $return;
	}

	/**
	 * Clean up notifications
	 * @sinc 1.4.7
	 * @version 1.4.7
	 */
	public function may_be_clean_up_post_notification() {

		// do nothing if this is not a single template
		if( ! is_single() ) {
			return;
		}

		$article = get_queried_object();

		// do nothing if article is not a submission
		$zombify_data_type = get_post_meta( $article->ID, 'zombify_data_type', true );
		if ( ! $zombify_data_type ) {
			return;
		}

		$user_id = get_current_user_id();
		if( isset( $_GET[ 'mark' ] ) ) {
			$associations = $this->get_mark_as_read_associations();
			if( ! isset( $associations[ $_GET[ 'mark' ] ] ) ) {
				return;
			}
			bp_notifications_mark_notifications_by_item_id( $user_id, $article->ID, $this->get_id(), $associations[ $_GET[ 'mark' ] ] );
		}
	}

}

ZF_Open_List_Notification::get_instance();