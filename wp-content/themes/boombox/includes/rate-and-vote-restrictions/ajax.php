<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}


/**
 * Hooks
 */
add_action( 'wp_ajax_boombox_ajax_point', 'boombox_ajax_point' );
add_action( 'wp_ajax_nopriv_boombox_ajax_point', 'boombox_ajax_point' );

add_action( 'wp_ajax_boombox_ajax_point_discard', 'boombox_ajax_point_discard' );
add_action( 'wp_ajax_nopriv_boombox_ajax_point_discard', 'boombox_ajax_point_discard' );

add_action( 'wp_ajax_boombox_ajax_reaction_add', 'boombox_ajax_reaction_add' );
add_action( 'wp_ajax_nopriv_boombox_ajax_reaction_add', 'boombox_ajax_reaction_add' );

add_action( 'wp_ajax_boombox_ajax_track_view', 'boombox_ajax_track_view' );
add_action( 'wp_ajax_nopriv_boombox_ajax_track_view', 'boombox_ajax_track_view' );

/**
 * Up and Down Points
 */
function boombox_ajax_point() {
	$sub_action  = $_POST['sub_action'];
	$id          = $_POST['id'];
	$point_count = 0;
	
	$status = false;
	if ( 'up' === $sub_action ) {
		$status = Boombox_Point_Count_Helper::point_up( $id );
	} else if ( 'down' === $sub_action ) {
		$status = Boombox_Point_Count_Helper::point_down( $id );
	}

	if ( $status ) {
		$point_count = Boombox_Point_Count_Helper::get_post_points( $id );
		
		do_action( 'boombox/post_pointed', $id, $point_count, $sub_action );
	}

	echo json_encode( array( 'status' => $status, 'point_count' => $point_count ) );
	wp_die();
}

/**
 * Discard Points
 */
function boombox_ajax_point_discard() {
	$sub_action  = $_POST['sub_action'];
	$id          = $_POST['id'];
	$point_count = 0;
	
	$status = false;
	if ( 'up' === $sub_action ) {
		$status = Boombox_Point_Count_Helper::discard_point_up( $id );
	} else if ( 'down' === $sub_action ) {
		$status = Boombox_Point_Count_Helper::discard_point_down( $id );
	}

	if ( $status ) {
		$point_count = Boombox_Point_Count_Helper::get_post_points( $id );
		
		do_action( 'boombox/post_pointed', $id, $point_count, $sub_action );
	}

	echo json_encode( array( 'status' => $status, 'point_count' => $point_count ) );
	wp_die();
}

/**
 * Add reaction to post
 */
function boombox_ajax_reaction_add(){
	$reaction_id  = $_POST['reaction_id'];
	$post_id           = $_POST['post_id'];

	$status = Boombox_Reaction_Helper::add_reaction( $post_id, $reaction_id );
	$reaction_total = Boombox_Reaction_Helper::get_reaction_total( $post_id );
	$reaction_restrictions = Boombox_Reaction_Helper::get_post_reaction_restrictions( $post_id );
	if( $status ) {
		do_action( 'boombox/post_reacted', $post_id, $reaction_id );
	}

	echo json_encode( array(
		'reaction_restrictions' => $reaction_restrictions,
		'reaction_total'        => $reaction_total,
		'status'                => $status
	) );
	wp_die();
}

/**
 * Track page view
 */
function boombox_ajax_track_view() {
	
	$is_amp = ( isset( $_REQUEST['is_amp'] ) && $_REQUEST['is_amp'] );
	if( $is_amp ) {
		$post_id = ( isset( $_REQUEST['post_id'] ) && $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : false;
		$token = ( isset( $_REQUEST['token'] ) && $_REQUEST['token'] ) ? $_REQUEST['token'] : '';
		$is_token_valid = wp_verify_nonce( $token, 'boombox_ajax_track_view_security_token' );
	} else {
		$post_id = ( isset( $_POST['post_id'] ) && $_POST['post_id'] ) ? $_POST['post_id'] : false;
		$is_token_valid = true;
	}
	
	$status = false;
	if( $is_token_valid && $post_id ) {
		$status = Boombox_View_Count_Helper::add_view( $post_id );
	}

	echo json_encode( array(
		'status' => $status
	) );
	wp_die();
}