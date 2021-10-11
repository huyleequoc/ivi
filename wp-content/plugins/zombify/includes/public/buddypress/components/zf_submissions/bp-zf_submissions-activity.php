<?php
/**
 * BuddyPress Zombify Submissions Component activity.
 *
 *
 * @package Zombify
 * @subpackage Buddypress Submissions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Log an activity
 * @param int $ID The post ID
 * @param WP_Post $post The post object
 * @since 1.4.7
 * @version 1.4.7
 */
function zf_submissions_activity_publish_post( $ID, $post ) {

	// add possibility to disable
	if( ! apply_filters( 'zf_submission_allow_activity_log', true, $ID, $post ) ) {
		return;
	}

	// do nothing if is post is not a zf post
	$zombify_data_type = get_post_meta( $ID, 'zombify_data_type', true );
	if ( ! $zombify_data_type ) {
		return;
	}

	$buddypress = buddypress();

	$existing = bp_activity_get( array(
		'max' => 1,
		'filter' => array(
			'user_id'      => $post->post_author,               // User ID to filter on.
			'object'       => $buddypress->zf_submissions->id,  // Object to filter on e.g. groups, profile, status, friends.
			'action'       => 'zf_submission_publish',          // Action to filter on e.g. activity_update, profile_updated.
			'secondary_id' => $post->ID,                        // Secondary object ID to filter on e.g. a post_id.
		),
	) );
	if( ! empty( $existing[ 'activities' ] ) ) {
		return;
	}

	$zf_post_types = zombify()->get_active_post_types();
	bp_activity_add( array(
		'component'         => $buddypress->zf_submissions->id, // The name/ID of the component e.g. groups, profile, mycomponent.
		'type'              => 'zf_submission_publish',         // The activity type e.g. activity_update, profile_updated.
		'user_id'           => $post->post_author,              // Optional: The user to record the activity for, can be false if this activity is not for a user.
		'secondary_item_id' => $post->ID,                       // Optional: A second ID used to further filter e.g. a comment_id.
		'action'            => sprintf( __( '%s published "%s"', 'zombify' ), '<a href="' . bp_core_get_user_domain( $post->post_author ) . '">' . bp_core_get_user_displayname( $post->post_author ) . '</a>', $zf_post_types[ $zombify_data_type ]['name'] ),
		'content'           => '<a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>',
	) );

}
add_action( 'publish_post', 'zf_submissions_activity_publish_post', 10, 2 );