<?php
/**
 * BuddyPress Zombify Submissions Component template functions.
 *
 *
 * @package Zombify
 * @subpackage Buddypress Submissions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Create static variables for templates
 *
 * @return array
 */
function zf_submissions_get_template_static_variables() {
	return array(
		'zombify_post_types'    => zombify()->get_post_types(),
		'zombify_post_statuses' => array(
			'publish'   => __( 'Published', 'zombify' ),
			'pending'   => __( 'Pending', 'zombify' ),
			'draft'     => __( 'Draft', 'zombify' ),
		)
	);
}

/**
 * Get submission item path
 * @return string
 */
function zf_submissions_get_loop_item_template_path() {
	return zombify()->locate_template('public/buddypress/parts/submission-item.php');
}

/**
 * All submissions
 *
 * Handle request
 */
function zf_submissions_render_all_submissions() {
	$bp = buddypress();
	$bp->zf_submissions->current_page_slug = zf_submissions_get_subpage_slug( 'all' );
	$bp->zf_submissions->current_page_url = zf_submissions_get_page_url( $bp->zf_submissions->current_page_slug );
	
	do_action( 'zf_submissions_render_all_submissions', 'all' );
	
	add_action( 'bp_template_content', 'zf_submissions_render_all_submissions_tab_content' );
	bp_core_load_template( apply_filters('bp_core_template_plugin', 'members/single/plugins') );
}

/**
 * All submissions render tab content
 */
function zf_submissions_render_all_submissions_tab_content() {
	
	$filter = ( isset( $_REQUEST['filter'] ) && isset( $_REQUEST['filter'] ) ) ? sanitize_text_field( $_REQUEST['filter'] ) : '';
	if( $filter ) {
		
		switch( $filter ) {
			case 'simple-post':
				$meta_query = array(
					array(
						'key'       => 'zombify_data_type',
						'compare'   => 'NOT EXISTS'
					)
				);
				
				break;
			default:
				if( strpos( $filter, 'subtype_' ) !== false ) {
					$meta_key = 'zombify_data_subtype';
					$meta_value = substr( $filter, 8 );
				} else {
					$meta_key = 'zombify_data_type';
					$meta_value = $filter;
				}
				$meta_query = array(
					array(
						'key'       => $meta_key,
						'value'     => $meta_value
					)
				);
		}
		
	} else {
		$meta_query = array(
			array(
				'key'       => 'zombify_virtual_post',
				'compare'   => 'NOT EXISTS'
			)
		);
	}
	
	$query_args = array(
		'post_type'         => 'post',
		'author'            => bp_displayed_user_id(),
		'post_status'       => array( 'publish', 'pending', 'draft' ),
		'posts_per_page'    => apply_filters( 'zombify_bp_all_submissions_per_page', zf_submissions_get_posts_per_page() ),
		'paged'             => zf_submissions_get_paged(),
		'order'             => 'DESC',
		'orderby'           => 'date',
		'meta_query'        => $meta_query
	);
	
	global $wp_query;
	$tmp_query = $wp_query;
	
	$wp_query = new WP_Query( $query_args );
	$template_variables = apply_filters( 'zombify_bp_template_vars', zf_submissions_get_template_static_variables(), 'all_submissions' );
	zf_submissions_render_view( 'all_submissions', $template_variables );
	
	$wp_query = $tmp_query;
}

/**
 * Published submissions
 *
 * Handle request
 */
function zf_submissions_render_published_submissions() {
	$bp = buddypress();
	$bp->zf_submissions->current_page_slug = zf_submissions_get_subpage_slug( 'published' );
	$bp->zf_submissions->current_page_url = zf_submissions_get_page_url( $bp->zf_submissions->current_page_slug );
	
	do_action( 'zf_submissions_render_published_submissions' );
	
	add_action('bp_template_content', 'zf_submissions_render_published_submissions_tab_content' );
	bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
}

/**
 * Published submissions render tab content
 */
function zf_submissions_render_published_submissions_tab_content() {
	
	$filter = ( isset( $_REQUEST['filter'] ) && isset( $_REQUEST['filter'] ) ) ? sanitize_text_field( $_REQUEST['filter'] ) : '';
	if( $filter ) {
		
		switch( $filter ) {
			case 'simple-post':
				$meta_query = array(
					array(
						'key'       => 'zombify_data_type',
						'compare'   => 'NOT EXISTS'
					)
				);
				
				break;
			default:
				if( strpos( $filter, 'subtype_' ) !== false ) {
					$meta_key = 'zombify_data_subtype';
					$meta_value = substr( $filter, 8 );
				} else {
					$meta_key = 'zombify_data_type';
					$meta_value = $filter;
				}
				$meta_query = array(
					array(
						'key'       => $meta_key,
						'value'     => $meta_value
					)
				);
		}
		
	} else {
		$meta_query = array(
			array(
				'key'       => 'zombify_virtual_post',
				'compare'   => 'NOT EXISTS'
			)
		);
	}
	
	$query_args = array(
		'post_type'         => 'post',
		'author'            => bp_displayed_user_id(),
		'post_status'       => 'publish',
		'posts_per_page'    => apply_filters( 'zombify_bp_published_submissions_per_page', zf_submissions_get_posts_per_page() ),
		'paged'             => zf_submissions_get_paged(),
		'order'             => 'DESC',
		'orderby'           => 'date',
		'meta_query'        => $meta_query
	);
	
	global $wp_query;
	$tmp_query = $wp_query;
	
	$wp_query = new WP_Query( $query_args );
	$template_variables = apply_filters( 'zombify_bp_template_vars', zf_submissions_get_template_static_variables(), 'published_submissions' );
	zf_submissions_render_view( 'published_submissions', $template_variables );
	
	$wp_query = $tmp_query;
}

/**
 * Pending submissions
 *
 * Handle request
 */
function zf_submissions_render_pending_submissions() {
	$bp = buddypress();
	$bp->zf_submissions->current_page_slug = zf_submissions_get_subpage_slug( 'pending' );
	$bp->zf_submissions->current_page_url = zf_submissions_get_page_url( $bp->zf_submissions->current_page_slug );
	
	do_action( 'zf_submissions_render_pending_submissions', 'pending' );
	
	add_action( 'bp_template_content', 'zf_submissions_render_pending_submissions_tab_content' );
	bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
}

/**
 * Pending submissions render tab content
 */
function zf_submissions_render_pending_submissions_tab_content() {

	$filter = ( isset( $_REQUEST['filter'] ) && isset( $_REQUEST['filter'] ) ) ? sanitize_text_field( $_REQUEST['filter'] ) : '';
	if( $filter ) {
		
		switch( $filter ) {
			case 'simple-post':
				$meta_query = array(
					array(
						'key'       => 'zombify_data_type',
						'compare'   => 'NOT EXISTS'
					)
				);
				
				break;
			default:
				if( strpos( $filter, 'subtype_' ) !== false ) {
					$meta_key = 'zombify_data_subtype';
					$meta_value = substr( $filter, 8 );
				} else {
					$meta_key = 'zombify_data_type';
					$meta_value = $filter;
				}
				$meta_query = array(
					array(
						'key'       => $meta_key,
						'value'     => $meta_value
					)
				);
		}
		
	} else {
		$meta_query = array(
			array(
				'key'       => 'zombify_virtual_post',
				'compare'   => 'NOT EXISTS'
			)
		);
	}
	
	$query_args = array(
		'post_type'         => 'post',
		'author'            => bp_displayed_user_id(),
		'post_status'       => 'pending',
		'posts_per_page'    => apply_filters( 'zombify_bp_pending_submissions_per_page', zf_submissions_get_posts_per_page() ),
		'paged'             => zf_submissions_get_paged(),
		'order'             => 'DESC',
		'orderby'           => 'date',
		'meta_query'        => $meta_query
	);
	
	global $wp_query;
	$tmp_query = $wp_query;
	
	$wp_query = new WP_Query( $query_args );
	$template_variables = apply_filters( 'zombify_bp_template_vars', zf_submissions_get_template_static_variables(), 'pending_submissions' );
	zf_submissions_render_view( 'pending_submissions', $template_variables );
	
	$wp_query = $tmp_query;
}

/**
 * Draft submissions
 *
 * Handle request
 */
function zf_submissions_render_draft_submissions() {

	$bp = buddypress();
	$bp->zf_submissions->current_page_slug = zf_submissions_get_subpage_slug( 'draft' );
	$bp->zf_submissions->current_page_url = zf_submissions_get_page_url( $bp->zf_submissions->current_page_slug );
	
	do_action( 'zf_submissions_render_draft_submissions', 'draft' );
	
	add_action( 'bp_template_content', 'zf_submissions_render_draft_submissions_tab_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

/**
 * Draft submissions render tab content
 *
 * Handle request
 */
function zf_submissions_render_draft_submissions_tab_content() {
	
	$filter = ( isset( $_REQUEST['filter'] ) && isset( $_REQUEST['filter'] ) ) ? sanitize_text_field( $_REQUEST['filter'] ) : '';
	if( $filter ) {
		
		switch( $filter ) {
			case 'simple-post':
				$meta_query = array(
					array(
						'key'       => 'zombify_data_type',
						'compare'   => 'NOT EXISTS'
					)
				);
				
				break;
			default:
				if( strpos( $filter, 'subtype_' ) !== false ) {
					$meta_key = 'zombify_data_subtype';
					$meta_value = substr( $filter, 8 );
				} else {
					$meta_key = 'zombify_data_type';
					$meta_value = $filter;
				}
				$meta_query = array(
					array(
						'key'       => $meta_key,
						'value'     => $meta_value
					)
				);
		}
		
	} else {
		$meta_query = array(
			array(
				'key'       => 'zombify_virtual_post',
				'compare'   => 'NOT EXISTS'
			)
		);
	}
	
	$query_args = array(
		'post_type'         => 'post',
		'author'            => bp_displayed_user_id(),
		'post_status'       => 'draft',
		'posts_per_page'    => apply_filters( 'zombify_bp_draft_submissions_per_page', zf_submissions_get_posts_per_page() ),
		'paged'             => zf_submissions_get_paged(),
		'order'             => 'DESC',
		'orderby'           => 'date',
		'meta_query'        => $meta_query
	);
	
	global $wp_query;
	$tmp_query = $wp_query;
	
	$wp_query = new WP_Query( $query_args );
	$template_variables = apply_filters( 'zombify_bp_template_vars', zf_submissions_get_template_static_variables(), 'draft_submissions' );
	zf_submissions_render_view( 'draft_submissions', $template_variables );
	
	$wp_query = $tmp_query;
}

/**
 * Get submission item deletable statuses
 * @return array
 */
function zf_get_deletable_post_statuses() {
	return apply_filters( 'zf_submission_item_get_deletable_post_statuses', array( 'draft', 'pending' ) );
}

/**
 * Callback to delete zombify post
 */
function zf_submissions_delete_post( $sub_page_slug ) {
	// draft post removal
	if ( isset( $_REQUEST['action'] ) && ( 'delete' == $_REQUEST['action'] ) ) {

		$post_id = ( isset( $_REQUEST['post_id'] ) && $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : 0;
		$key     = isset( $_REQUEST['key'] ) ? $_REQUEST['key'] : '';

		if ( wp_verify_nonce( $key, 'zf-delete-post' ) ) {
			$post = get_post( $post_id );

			// only post author has access to delete his own posts with status draft or pending
			if ( $post && ( $post->post_author == get_current_user_id() ) && in_array( $post->post_status, zf_get_deletable_post_statuses() ) ) {
				wp_delete_post( $post->ID, apply_filters( 'zf_submission_force_delete', true ) );
			}

			$redirect_url = zf_submissions_get_page_url( zf_submissions_get_subpage_slug( $sub_page_slug ) );
			wp_redirect( $redirect_url );
			die;
		}
	}
}
add_action( 'zf_submissions_render_all_submissions', 'zf_submissions_delete_post', 10, 1 );
add_action( 'zf_submissions_render_pending_submissions', 'zf_submissions_delete_post', 10, 1 );
add_action( 'zf_submissions_render_draft_submissions', 'zf_submissions_delete_post', 10, 1 );