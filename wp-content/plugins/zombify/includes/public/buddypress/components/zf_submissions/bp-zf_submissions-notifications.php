<?php
/**
 * BuddyPress Zombify Submissions Component notifications.
 *
 *
 * @package Zombify
 * @subpackage Buddypress Submissions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Format notifications related to zf_submissions.
 *
 * @since 1.5.0
 *
 * @param string $action            The type of zf_notification item. Just 'post_published' for now.
 * @param int    $item_id           Associated article ID.
 * @param int    $secondary_item_id User ID to notify.
 * @param int    $total_items       The total number of notifications to format.
 * @param string $format            'string' to get a BuddyBar-compatible notification, 'array' otherwise.
 * @param int    $id                Optional. The notification ID.
 * @return string $return Formatted @mention notification.
 */
function bp_zf_submissions_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string', $id = 0 ) {

    $action_filter = $action;
    $return        = false;
    $user_id       = $secondary_item_id;
    $user_fullname = bp_core_get_user_displayname( $user_id );

    switch ( $action ) {
        case 'post_published':

            $action_filter = 'post_published';
            $title         = sprintf( __( '@%s approved articles', 'zombify' ), $user_fullname );
            $amount        = 'single';

            if ( (int) $total_items > 1 ) {

                $text = sprintf( __( '%1$d articles are published', 'zombify' ), (int) $total_items );
                $link = trailingslashit( bp_loggedin_user_domain() . zf_submissions_get_slug() ) . 'publish';
                $amount = 'multiple';
            } else {
                $link = get_permalink( $item_id );
                $text = sprintf( __( '%1$s published your post', 'zombify' ), $user_fullname );
            }
            break;
    }

    if ( 'string' == $format ) {

        /**
         * Filters the zf_submission notification for the string format.
         *
         * This is a variable filter that is dependent on how many items
         * need notified about. The two possible hooks are bp_zf_submission_single_post_published_notification
         * or bp_zf_submission_multiple_post_published_notification.
         *
         * @param string $string          HTML anchor tag for the interaction.
         * @param string $link            The permalink for the interaction.
         * @param int    $total_items     How many items being notified about.
         * @param int    $item_id         ID of the article being formatted.
         * @param int    $user_id         ID of the user who inited the interaction.
         */
        $return = apply_filters( 'bp_zf_submission_' . $amount . '_' . $action_filter . '_notification', '<a href="' . esc_url( $link ) . '" title="' . esc_attr( $title ) . '">' . esc_html( $text ) . '</a>', $link, (int) $total_items, $item_id, $user_id );
    } else {

        /**
         * Filters the zf_submission notification for any non-string format.
         *
         * This is a variable filter that is dependent on how many items need notified about.
         * The two possible hooks are bp_zf_submission_single_post_published_notification
         * or bp_zf_submission_multiple_post_published_notification.
         *
         * @param array  $array           Array holding the content and permalink for the interaction notification.
         * @param string $link            The permalink for the interaction.
         * @param int    $total_items     How many items being notified about.
         * @param int    $item_id         ID of the article being formatted.
         * @param int    $user_id         ID of the user who inited the interaction.
         */
        $return = apply_filters( 'bp_zf_submission_' . $amount . '_' . $action_filter . '_notification', array(
            'text' => $text,
            'link' => $link
        ), $link, (int) $total_items, $item_id, $user_id );
    }

    /**
     * Fires right before returning the formatted zf_submissions notifications.
     *
     * @since 1.2.0
     *
     * @param string $action            The type of zf_submission item.
     * @param int    $item_id           The article ID.
     * @param int    $secondary_item_id The user ID who inited the interaction.
     * @param int    $total_items       Total amount of items to format.
     */
    do_action( 'zf_submissions_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

    return $return;
}

/**
 * Add a buddypress notification for post author on post publish
 */
add_action( 'publish_post', 'zf_submissions_publish_post', 10, 2 );
function zf_submissions_publish_post ( $ID, $post ) {
	
	// do nothing if is post is not a zf post
	$zombify_data_type = get_post_meta( $ID, 'zombify_data_type', true );
	if( ! $zombify_data_type ) {
		return;
	}
	
	$publisher = $post->post_author;
	$moderator = get_current_user_id();
	
	// do not setup self notification
	if( $publisher == $moderator ) {
		return;
	}
	
	// safe to add notification
	do_action( 'bp_zf_submissions_publish_post', $publisher, $ID, $moderator );
	
}

/**
 * Add a buddypress notification for post author on post publish
 *
 * @param $publisher_id Post author
 * @param $post_id      Post ID
 * @param $moderator_id Moderator id
 */
add_action( 'bp_zf_submissions_publish_post', 'zf_submissions_post_published_notification', 10, 3 );
function zf_submissions_post_published_notification( $publisher_id, $post_id, $moderator_id ) {
    bp_notifications_add_notification( array(
        'user_id'           => $publisher_id,
        'item_id'           => $post_id,
        'secondary_item_id' => $moderator_id,
        'component_name'    => buddypress()->zf_submissions->id,
        'component_action'  => 'post_published',
        'date_notified'     => bp_core_current_time(),
        'is_new'            => 1,
    ) );
}

/**
 * Send email notification to post author on post approval
 */
add_action( 'bp_zf_submissions_publish_post', 'zf_submissions_post_published_email_notification', 10, 3 );
function zf_submissions_post_published_email_notification( $publisher_id, $post_id, $moderator_id ) {
	
	$post_published = bp_get_user_meta( $publisher_id, 'notification_zf_submission_post_published', true );
	if( ! $post_published ) {
		$post_published = 'yes';
	}
	
	$post_published = apply_filters( 'bp_zf_submission_post_published_allow_send_email', $post_published, $publisher_id, $post_id, $moderator_id );
	
	if ( 'no' == $post_published ) {
		return;
	}
	
	$unsubscribe_args = array(
		'user_id'           => $publisher_id,
		'notification_type' => 'zf_submission_post_published',
	);
	
	$email_variables = array(
		'moderator.name'            => bp_core_get_username( $moderator_id ),
		'post.title'                => get_post_field( 'post_title', $post_id ),
		'post.url'                  => esc_url( get_permalink( $post_id ) ),
	);
	$email_variables = apply_filters( 'bp_zf_submission_post_published_email_args', $email_variables, $publisher_id, $moderator_id, $post_id );
	$email_variables[ 'unsubscribe' ] = esc_url( bp_email_get_unsubscribe_link( $unsubscribe_args ) );
	
	$args = array( 'tokens' => $email_variables );
	
	bp_send_email( 'zf_submission_post_published', (int)$publisher_id, $args );
}

/**
 * Clear notifications on visiting 'all submissions' or 'publised submissions' page
 */
add_action( 'zf_submissions_render_all_submissions', 'zf_submissions_clear_notifications' );
add_action( 'zf_submissions_render_published_submissions', 'zf_submissions_clear_notifications' );
function zf_submissions_clear_notifications() {

    $displayed_user_id = bp_displayed_user_id();
    $current_user_id = get_current_user_id();

    // do nothing if looking at other user profile submissions
    if( $displayed_user_id != $current_user_id ) {
        return;
    }

    $bp = buddypress();
    bp_notifications_mark_notifications_by_type( $current_user_id, $bp->zf_submissions->id, 'post_published' );
}

add_action( 'template_redirect', 'zf_submissions_template_redirect' );
function zf_submissions_template_redirect() {
    // do nothing if this is not a single template
    if( ! is_single() ) {
        return;
    }

    $article = get_queried_object();
    $current_id = get_current_user_id();

    // do nothing if current user is not the article author
    if( $article->post_author != $current_id ) {
        return;
    }

    // do nothing if article is not a submission
    $zombify_data_type = get_post_meta( $article->ID, 'zombify_data_type', true );
    if( ! $zombify_data_type ) {
        return;
    }

    // safe to clear the notification
    $bp = buddypress();
    bp_notifications_mark_notifications_by_item_id( $current_id, $article->ID, $bp->zf_submissions->id, 'post_published' );
}